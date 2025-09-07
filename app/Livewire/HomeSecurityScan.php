<?php

namespace App\Livewire;

use App\Jobs\RunScanTest;
use App\Models\WebTest;
use App\Models\Domain;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeSecurityScan extends Component
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
        return 's-scan';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'scan_type' => 'passive',
            'scan_depth' => 0,
            'max_duration' => 60
        ];
    }

    /**
     * Run security scan test
     */
    public function runTest()
    {
        // Check login
        if (!Auth::check()) {
            session()->flash('error', 'Security scans require login. Please log in and complete domain registration and ownership verification.');
            return;
        }

        $this->validate([
            'url' => 'required|url|max:2048'
        ]);

        // Verify domain ownership
        $domain = parse_url($this->url, PHP_URL_HOST);
        if (!$domain) {
            $this->addError('url', 'Invalid URL format.');
            return;
        }

        $verifiedDomain = Domain::where('user_id', Auth::id())
            ->where('is_verified', true)
            ->whereRaw('? LIKE CONCAT("%", SUBSTRING_INDEX(SUBSTRING_INDEX(url, "://", -1), "/", 1), "%")', [$domain])
            ->first();

        if (!$verifiedDomain) {
            $this->addError('url', 'Domain ownership verification is required. Please register and verify the domain in the "Domains" tab on the sidebar.');
            return;
        }

        $securityErrors = UrlSecurityValidator::validate($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }
        
        if ($this->isDuplicateRecentTest($this->url)) {
            $this->addError('url', 'A test for this URL was already executed within the last minute.');
            return;
        }

        // Check usage limit
        if (!$this->canUseService()) {
            session()->flash('error', 'You have exceeded your available usage limit.');
            return;
        }

        $this->isLoading = true;

        // Create WebTest record
        $test = WebTest::create([
            'user_id' => Auth::id(),
            'test_type' => $this->getTestType(),
            'url' => $this->url,
            'status' => 'pending',
            'started_at' => now(),
            'test_config' => $this->getTestConfig()
        ]);

        // Deduct usage
        $this->consumeService($domain, $this->getTestType());
        $this->refreshUsageInfo();

        $this->currentTest = $test;
        RunScanTest::dispatch($this->url, $test->id)->onQueue('scan');
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
        return $this->renderSharedView('livewire.home-security-scan', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan()
        ]);
    }
}