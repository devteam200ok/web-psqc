<?php

namespace App\Livewire;

use App\Jobs\RunLinksTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeContentLinks extends Component
{
    use ManagesIpUsage, ManagesUserPlanUsage, SharedTestComponents;

    public function mount()
    {
        $this->initializeSharedComponents();
    }

    /**
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'c-links';
    }

    /**
     * 테스트 설정 반환
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'links_validation',
            'allow_oauth' => true,
            'timeout' => 240,
        ];
    }

    /**
     * 링크 검증 테스트 실행
     */
    public function runTest()
    {
        $this->validate([
            'url' => 'required|url|max:2048'
        ]);

        $securityErrors = UrlSecurityValidator::validate($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }
        
        if ($this->isDuplicateRecentTest($this->url)) {
            $this->addError('url', '동일한 URL에 대한 테스트가 최근 1분 내에 실행되었습니다.');
            return;
        }

        // 사용량 체크
        if (Auth::check()) {
            if (!$this->canUseService()) {
                session()->flash('error', '사용 가능한 횟수를 초과했습니다.');
                return;
            }
        } else {
            if (!$this->hasUsageRemaining()) {
                session()->flash('error', '사용량이 초과되었습니다. 로그인 후 이용해주세요.');
                return;
            }
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
        if (Auth::check()) {
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $this->consumeService($domain, $this->getTestType());
            $this->refreshUsageInfo();
        } else {
            $this->consumeUsage();
        }

        $this->currentTest = $test;
        RunLinksTest::dispatch($this->url, $test->id)->onQueue('links');
        $this->dispatch('start-polling');
        $this->loadTestHistory();
    }

    private function isDuplicateRecentTest(string $url): bool
    {
        $recentTest = WebTest::where('url', $url)
            ->where('test_type', $this->getTestType())
            ->where('created_at', '>=', now()->subMinutes(1))
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
     * 링크 검증 결과의 등급별 색상 클래스 반환
     */
    public function getGradeBadgeClass($grade): string
    {
        return match($grade) {
            'A+' => 'badge bg-green-lt text-green-lt-fg',
            'A' => 'badge bg-lime-lt text-lime-lt-fg',
            'B' => 'badge bg-blue-lt text-blue-lt-fg',
            'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
            'D' => 'badge bg-orange-lt text-orange-lt-fg',
            'F' => 'badge bg-red-lt text-red-lt-fg',
            default => 'badge bg-secondary',
        };
    }

    /**
     * HTTP 상태별 색상 클래스 반환
     */
    public function getStatusBadgeClass($status): string
    {
        if (!$status || $status === 0) {
            return 'badge bg-secondary';
        }
        
        if ($status >= 200 && $status < 300) {
            return 'badge bg-green-lt text-green-lt-fg';
        } elseif ($status >= 300 && $status < 400) {
            return 'badge bg-blue-lt text-blue-lt-fg';
        } elseif ($status >= 400 && $status < 500) {
            return 'badge bg-orange-lt text-orange-lt-fg';
        } else {
            return 'badge bg-red-lt text-red-lt-fg';
        }
    }

    /**
     * 오류율별 색상 클래스 반환
     */
    public function getErrorRateBadgeClass($errorRate): string
    {
        if ($errorRate == 0) {
            return 'text-green';
        } elseif ($errorRate <= 1) {
            return 'text-lime';
        } elseif ($errorRate <= 3) {
            return 'text-yellow';
        } elseif ($errorRate <= 5) {
            return 'text-orange';
        } else {
            return 'text-red';
        }
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-content-links', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan()
        ]);
    }
}