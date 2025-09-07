<?php

namespace App\Livewire;

use App\Jobs\RunMobileTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomePerformanceMobile extends Component
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
        return 'p-mobile';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'devices' => ['iPhone SE', 'iPhone 11', 'iPhone 15 Pro', 'Galaxy S9+', 'Galaxy S20 Ultra', 'Pixel 5'],
            'test_mode' => 'mobile_performance',
            'cpu_throttling' => 4,
            'runs_per_device' => 4,
            'warmup_runs' => 1
        ];
    }

    /**
     * Execute mobile performance test
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

        // Check usage
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

        // Deduct usage
        if (Auth::check()) {
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $this->consumeService($domain, $this->getTestType());
            $this->refreshUsageInfo();
        } else {
            $this->consumeUsage();
        }

        $this->currentTest = $test;
        RunMobileTest::dispatch($this->url, $test->id)->onQueue('mobile');
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
     * Check if certificate can be issued
     */
    public function canIssueCertificate(): bool
    {
        return $this->currentTest && 
               $this->currentTest->status === 'completed' &&
               in_array($this->currentTest->overall_grade, ['A+', 'A', 'B']);
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-performance-mobile', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'canIssueCertificate' => $this->canIssueCertificate()
        ]);
    }
}