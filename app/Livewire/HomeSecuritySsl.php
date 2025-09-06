<?php

namespace App\Livewire;

use App\Jobs\RunSslTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeSecuritySsl extends Component
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
        return 's-ssl';
    }

    /**
     * 테스트 설정 반환
     */
    protected function getTestConfig(): array
    {
        return [
            'tool' => 'testssl.sh',
            'test_mode' => 'ssl_basic',
            'timeout' => 600,
            'options' => ['--warnings off', '--openssl-timeout 10', '--fast']
        ];
    }

    /**
     * SSL 기본 테스트 실행
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
            $this->addError('url', '동일한 URL에 대한 테스트가 최근 5분 내에 실행되었습니다.');
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
        RunSslTest::dispatch($this->url, $test->id)->onQueue('ssl');
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
     * 인증서 발급 가능 여부 체크
     */
    public function canIssueCertificate(): bool
    {
        return $this->currentTest && 
               $this->currentTest->status === 'completed' &&
               in_array($this->currentTest->overall_grade, ['A+', 'A', 'B']);
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-security-ssl', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'canIssueCertificate' => $this->canIssueCertificate()
        ]);
    }
}