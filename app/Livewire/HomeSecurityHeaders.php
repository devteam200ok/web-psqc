<?php

namespace App\Livewire;

use App\Jobs\RunHeadersTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeSecurityHeaders extends Component
{
    use ManagesIpUsage, ManagesUserPlanUsage, SharedTestComponents;

    public function mount()
    {
        $this->initializeSharedComponents();
    }

    /**
     * Return test type
     */
    protected function getTestType(): string
    {
        return 's-header';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'security_headers',
            'headers_checked' => [
                'content-security-policy',
                'x-frame-options',
                'x-content-type-options',
                'referrer-policy',
                'permissions-policy',
                'strict-transport-security'
            ]
        ];
    }

    /**
     * Execute security headers test
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
            $this->addError('url', 'A test for the same URL was executed within the last minute.');
            return;
        }

        // Usage check
        if (Auth::check()) {
            if (!$this->canUseService()) {
                session()->flash('error', 'You have exceeded your usage limit.');
                return;
            }
        } else {
            if (!$this->hasUsageRemaining()) {
                session()->flash('error', 'Usage limit exceeded. Please sign in to continue.');
                return;
            }
        }

        $this->isLoading = true;

        // Create WebTest
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
        RunHeadersTest::dispatch($this->url, $test->id)->onQueue('headers');
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
        return $this->renderSharedView('livewire.home-security-headers', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan()
        ]);
    }
}
