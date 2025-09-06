<?php

namespace App\Livewire;

use App\Jobs\RunLoadTest;
use App\Models\WebTest;
use App\Models\Domain;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomePerformanceLoad extends Component
{
    use ManagesIpUsage, ManagesUserPlanUsage, SharedTestComponents;

    // K6 테스트 특화 프로퍼티
    public $vus = 50;
    public $duration_seconds = 45;
    public $think_time_min = 3;
    public $think_time_max = 10;

    public function mount()
    {
        $this->initializeSharedComponents();
    }

    /**
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'p-load';
    }

    /**
     * 테스트 설정 반환
     */
    protected function getTestConfig(): array
    {
        return [
            'vus' => $this->vus,
            'duration_seconds' => $this->duration_seconds,
            'think_time_min' => $this->think_time_min,
            'think_time_max' => $this->think_time_max,
            'test_mode' => 'load_test',
            'region' => 'seoul'
        ];
    }

    /**
     * K6 부하 테스트 실행
     */
    public function runTest()
    {
        // 로그인 체크
        if (!Auth::check()) {
            session()->flash('error', '부하 테스트는 로그인이 필요합니다. 로그인 후 도메인 등록 및 소유권 인증을 완료해주세요.');
            return;
        }

        // K6 테스트에 특화된 검증 규칙 정의 및 실행
        $this->validate([
            'url' => 'required|url|max:2048',
            'vus' => 'required|integer|min:10|max:100',
            'duration_seconds' => 'required|integer|min:30|max:100',
            'think_time_min' => 'required|integer|min:1|max:30',
            'think_time_max' => 'required|integer|min:1|max:60',
        ], [
            'url.required' => 'URL을 입력해주세요.',
            'url.url' => '올바른 URL 형식이 아닙니다.',
            'vus.required' => 'Virtual Users 수를 입력해주세요.',
            'vus.min' => 'Virtual Users는 최소 10명 이상이어야 합니다.',
            'vus.max' => 'Virtual Users는 최대 100명까지 가능합니다.',
            'duration_seconds.required' => '테스트 시간을 입력해주세요.',
            'duration_seconds.min' => '테스트 시간은 최소 30초 이상이어야 합니다.',
            'duration_seconds.max' => '테스트 시간은 최대 100초까지 가능합니다.',
        ]);

        // 도메인 소유권 검증
        $domain = parse_url($this->url, PHP_URL_HOST);
        if (!$domain) {
            $this->addError('url', '올바른 URL 형식이 아닙니다.');
            return;
        }

        $verifiedDomain = Domain::where('user_id', Auth::id())
            ->where('is_verified', true)
            ->whereRaw('? LIKE CONCAT("%", SUBSTRING_INDEX(SUBSTRING_INDEX(url, "://", -1), "/", 1), "%")', [$domain])
            ->first();

        if (!$verifiedDomain) {
            $this->addError('url', '해당 도메인에 대한 소유권 인증이 필요합니다. 사이드바의 "도메인" 탭에서 도메인을 등록하고 인증을 완료해주세요.');
            return;
        }

        $securityErrors = UrlSecurityValidator::validate($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }
        
        if ($this->isDuplicateRecentTest($this->url)) {
            $this->addError('url', '동일한 URL에 대한 테스트가 최근 5분 내에 실행되었습니다.');
            return;
        }

        // 사용량 체크
        if (!$this->canUseService()) {
            session()->flash('error', '사용 가능한 횟수를 초과했습니다.');
            return;
        }

        $this->isLoading = true;

        // WebTest 생성
        $test = WebTest::create([
            'user_id' => Auth::id(),
            'test_type' => $this->getTestType(),
            'url' => $this->url,
            'status' => 'pending',
            'started_at' => now(),
            'test_config' => $this->getTestConfig()
        ]);

        // 사용량 차감
        $this->consumeService($domain, $this->getTestType());
        $this->refreshUsageInfo();

        $this->currentTest = $test;
        RunLoadTest::dispatch($this->url, $test->id)->onQueue('load');
        $this->dispatch('start-polling');
        $this->loadTestHistory();
    }

    private function isDuplicateRecentTest(string $url): bool
    {
        $recentTest = WebTest::where('url', $url)
            ->where('test_type', $this->getTestType())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();
            
        return $recentTest !== null;
    }

    #[On('check-status')]
    public function checkStatus()
    {
        if ($this->currentTest) {
            $this->currentTest->refresh();
            
            if (in_array($this->currentTest->status, ['completed', 'failed'])) {
                $this->isLoading = false;
                $this->mainTabActive = 'results';
                $this->dispatch('stop-polling');
            }
        }
    }

    /**
     * VU 수와 Duration에 따른 최대 등급 반환
     */
    public function getMaxGradeForSettings(): string
    {
        // VU와 Duration 조건을 모두 만족하는 최고 등급 계산
        if ($this->vus >= 100 && $this->duration_seconds >= 60) {
            return 'A+';
        } elseif ($this->vus >= 50 && $this->duration_seconds >= 45) {
            return 'A';
        } elseif ($this->vus >= 30 && $this->duration_seconds >= 30) {
            return 'B';
        } elseif ($this->vus >= 20 && $this->duration_seconds >= 30) {
            return 'C';
        } elseif ($this->vus >= 10 && $this->duration_seconds >= 30) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * 설정에 따른 최대 점수 반환
     */
    public function getMaxScoreForSettings(): int
    {
        $maxGrade = $this->getMaxGradeForSettings();
        
        return match($maxGrade) {
            'A+' => 100,
            'A' => 90,
            'B' => 80,
            'C' => 70,
            default => 50
        };
    }

    /**
     * 인증서 발급 가능 여부 체크
     */
    public function canIssueCertificate(): bool
    {
        return $this->currentTest && 
               $this->currentTest->status === 'completed' &&
               $this->vus >= 30 && 
               $this->duration_seconds >= 30 &&
               in_array($this->currentTest->overall_grade, ['A+', 'A', 'B']);
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-performance-load', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'maxGrade' => $this->getMaxGradeForSettings(),
            'maxScore' => $this->getMaxScoreForSettings(),
            'canIssueCertificate' => $this->canIssueCertificate()
        ]);
    }
}