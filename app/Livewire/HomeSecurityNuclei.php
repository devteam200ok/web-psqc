<?php

namespace App\Livewire;

use App\Jobs\RunNucleiTest;
use App\Models\WebTest;
use App\Models\Domain;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeSecurityNuclei extends Component
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
        return 's-nuclei';
    }

    /**
     * 테스트 설정 반환
     */
    protected function getTestConfig(): array
    {
        return [
            'severity' => 'critical,high',
            'templates' => '2024-2025',
            'scan_type' => 'targeted',
            'timeout' => 150
        ];
    }

    /**
     * Nuclei 보안 테스트 실행
     */
    public function runTest()
    {
        // 로그인 체크
        if (!Auth::check()) {
            session()->flash('error', '보안 스캔은 로그인이 필요합니다. 로그인 후 도메인 등록 및 소유권 인증을 완료해주세요.');
            return;
        }

        $this->validate([
            'url' => 'required|url|max:2048'
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
            $this->addError('url', '동일한 URL에 대한 테스트가 최근 1분 내에 실행되었습니다.');
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
        RunNucleiTest::dispatch($this->url, $test->id)->onQueue('nuclei');
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

    public function render()
    {
        return $this->renderSharedView('livewire.home-security-nuclei', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan()
        ]);
    }
}