<?php

namespace App\Livewire;

use App\Jobs\RunMetaTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeContentMeta extends Component
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
        return 'c-meta';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'metadata_analysis',
            'timeout' => 15000
        ];
    }

    /**
     * Run metadata test
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
                session()->flash('error', 'You have exceeded your available usage limit.');
                return;
            }
        } else {
            if (!$this->hasUsageRemaining()) {
                session()->flash('error', 'Usage limit exceeded. Please log in to continue.');
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
        RunMetaTest::dispatch($this->url, $test->id)->onQueue('meta');
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
     * Metadata-test-specific method — generate improvement suggestions
     */
    public function getImprovementSuggestions(): array
    {
        if (!$this->currentTest || !$this->currentTest->metrics) {
            return [];
        }

        $suggestions = [];
        $metrics = $this->currentTest->metrics;

        // Title suggestions
        if (!$metrics['title']['is_optimal']) {
            if ($metrics['title']['length'] < 50) {
                $suggestions[] = 'Increase the title to 50–60 characters to provide more context in search results.';
            } elseif ($metrics['title']['length'] > 60) {
                $suggestions[] = 'Shorten the title to 60 characters or fewer to avoid truncation in search results.';
            }
        }

        // Description suggestions
        if (!$metrics['description']['is_optimal']) {
            if ($metrics['description']['length'] < 120) {
                $suggestions[] = 'Write the meta description in the 120–160 character range to provide sufficient information.';
            } elseif ($metrics['description']['length'] > 160) {
                $suggestions[] = 'Trim the meta description to 160 characters or fewer so it displays fully.';
            }
        }

        // Open Graph suggestions
        if (!$metrics['open_graph']['is_perfect']) {
            if (!$metrics['open_graph']['has_basic']) {
                $suggestions[] = 'Add the core Open Graph tags (title, description, image, url).';
            } else {
                $suggestions[] = 'Add the Open Graph "type" tag to optimize social sharing.';
            }
        }

        // Canonical suggestions
        if (!$metrics['canonical']['exists']) {
            $suggestions[] = 'Set a canonical URL to prevent duplicate content issues.';
        }

        // Twitter Cards suggestions
        if (!$metrics['twitter_cards']['has_basic']) {
            $suggestions[] = 'Add Twitter Cards tags to improve sharing on Twitter.';
        }

        return $suggestions;
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-content-meta', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'improvementSuggestions' => $this->getImprovementSuggestions()
        ]);
    }
}