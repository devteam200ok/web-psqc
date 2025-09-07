<?php

namespace App\Livewire;

use App\Jobs\RunCrawlTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeContentCrawl extends Component
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
        return 'c-crawl';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'max_pages' => 50,
            'timeout' => 15000,
            'test_mode' => 'sitemap_crawl'
        ];
    }

    /**
     * Run crawling test
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
            'domain' => parse_url($this->url, PHP_URL_HOST) ?? $this->url,
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
        RunCrawlTest::dispatch($this->url, $test->id)->onQueue('crawl');
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
     * Test information tab content
     */
    public function getTestInformation(): array
    {
        return [
            'title' => 'Site Crawling Audit',
            'subtitle' => 'robots.txt/sitemap.xml–based SEO technical checks + page quality analysis',
            'description' => '
                Analyzes your website’s robots.txt and sitemap.xml to verify SEO compliance,
                then evaluates the accessibility and quality of pages listed in the sitemap.
                <br><br>
                <strong>Audit process:</strong><br>
                1. Check for robots.txt and validate rules<br>
                2. Locate sitemap.xml and collect URLs<br>
                3. Filter allowed URLs per robots.txt rules<br>
                4. Sample up to 50 pages and scan sequentially<br>
                5. Measure each page’s HTTP status, metadata, and quality score<br>
                6. Analyze duplicate content ratio (title/description)<br><br>
                
                <strong>Quality score deductions:</strong><br>
                • Title tag too short (&lt; 5 chars): -15 points<br>
                • Description meta too short (&lt; 20 chars): -10 points<br>
                • Missing canonical URL: -5 points<br>
                • Missing H1: -10 points / Excessive H1s: -5 points<br>
                • Thin content (&lt; 1,000 characters): -10 points<br><br>
                
                This audit typically takes <strong>30 seconds to 2 minutes</strong>.
            ',
            'grades' => [
                'A+' => [
                    'criteria' => [
                        'robots.txt correctly applied',
                        'sitemap.xml present with no missing/404 entries',
                        'All sampled pages return 2xx',
                        'Average page quality ≥ 85',
                        'Duplicate content ≤ 30%'
                    ]
                ],
                'A' => [
                    'criteria' => [
                        'robots.txt correctly applied',
                        'sitemap.xml present and consistent',
                        'All sampled pages return 2xx',
                        'Average page quality ≥ 85'
                    ]
                ],
                'B' => [
                    'criteria' => [
                        'robots.txt and sitemap.xml present',
                        'All sampled pages return 2xx',
                        'Average page quality not required'
                    ]
                ],
                'C' => [
                    'criteria' => [
                        'robots.txt and sitemap.xml present',
                        'Some 4xx/5xx errors among sampled pages'
                    ]
                ],
                'D' => [
                    'criteria' => [
                        'robots.txt and sitemap.xml present',
                        'Crawl targets can be generated',
                        'But low success rate or quality not measurable'
                    ]
                ],
                'F' => [
                    'criteria' => [
                        'robots.txt missing or sitemap.xml missing',
                        'Unable to generate crawl list'
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-content-crawl', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'testInformation' => $this->getTestInformation()
        ]);
    }
}