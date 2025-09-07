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

    // K6 test specific properties
    public $vus = 50;
    public $duration_seconds = 45;
    public $think_time_min = 3;
    public $think_time_max = 10;

    public function mount()
    {
        $this->initializeSharedComponents();
    }

    /**
     * Return test type
     */
    protected function getTestType(): string
    {
        return 'p-load';
    }

    /**
     * Return test configuration
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
     * Execute K6 load test
     */
    public function runTest()
    {
        // Check login status
        if (!Auth::check()) {
            session()->flash('error', 'Load testing requires login. Please sign in and complete domain registration and ownership verification.');
            return;
        }

        // Define and execute K6 test specific validation rules
        $this->validate([
            'url' => 'required|url|max:2048',
            'vus' => 'required|integer|min:10|max:100',
            'duration_seconds' => 'required|integer|min:30|max:100',
            'think_time_min' => 'required|integer|min:1|max:30',
            'think_time_max' => 'required|integer|min:1|max:60',
        ], [
            'url.required' => 'Please enter a URL.',
            'url.url' => 'Invalid URL format.',
            'vus.required' => 'Please enter the number of Virtual Users.',
            'vus.min' => 'Virtual Users must be at least 10.',
            'vus.max' => 'Virtual Users can be up to 100 maximum.',
            'duration_seconds.required' => 'Please enter the test duration.',
            'duration_seconds.min' => 'Test duration must be at least 30 seconds.',
            'duration_seconds.max' => 'Test duration can be up to 100 seconds maximum.',
        ]);

        // Domain ownership verification
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
            $this->addError('url', 'Domain ownership verification required. Please register your domain in the "Domains" tab in the sidebar and complete verification.');
            return;
        }

        $securityErrors = UrlSecurityValidator::validate($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }
        
        if ($this->isDuplicateRecentTest($this->url)) {
            $this->addError('url', 'A test for the same URL was executed within the last 5 minutes.');
            return;
        }

        // Check usage
        if (!$this->canUseService()) {
            session()->flash('error', 'You have exceeded your usage limit.');
            return;
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
     * Return maximum grade based on VU count and Duration
     */
    public function getMaxGradeForSettings(): string
    {
        // Calculate highest grade satisfying both VU and Duration conditions
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
     * Return maximum score based on settings
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
     * Check if certificate can be issued
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