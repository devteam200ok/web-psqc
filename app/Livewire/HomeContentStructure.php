<?php

namespace App\Livewire;

use App\Jobs\RunStructureTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeContentStructure extends Component
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
        return 'c-structure';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'structure_data',
            'check_json_ld' => true,
            'check_microdata' => true,
            'check_rdfa' => true,
            'check_rich_results' => true
        ];
    }

    /**
     * Run structured data test
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
        RunStructureTest::dispatch($this->url, $test->id)->onQueue('structure');
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
     * Return test information text
     */
    public function getTestInformation(): array
    {
        return [
            'title' => 'JSON-LD / Schema.org Structured Data Validation',
            'description' => 'Validates structured data that helps search engines better understand your pages and display Rich Results (rich snippets).',
            'details' => [
                'Parse and validate JSON-LD format',
                'Verify required fields by Schema.org type',
                'Detect Google Rich Results–supported types',
                'Check presence of Microdata and RDFa',
                'Provide errors, warnings, and improvement suggestions',
                'Include example JSON-LD snippets'
            ],
            'test_items' => [
                'Core schemas such as Organization, WebSite, BreadcrumbList',
                'Content-specific schemas such as Article, Product, FAQPage',
                'Specialized schemas such as Event, JobPosting, LocalBusiness',
                'Rating-related schemas such as AggregateRating, Review',
                'Media schemas such as VideoObject, Recipe, Course'
            ],
            'benefits' => [
                'Eligibility for Rich Snippets in search results',
                'Improved click-through rate (CTR)',
                'Better search engine understanding of content',
                'Optimization for Voice Search and AI Search',
                'Higher likelihood of Knowledge Graph inclusion'
            ]
        ];
    }

    /**
     * Return grade criteria information
     */
    public function getGradeCriteria(): array
    {
        return [
            'A+' => [
                'label' => 'A+',
                'score' => '90-100',
                'criteria' => [
                    'Perfect JSON-LD implementation',
                    '100% Rich Results recognition',
                    '0 errors, 0 warnings',
                    'All required fields present',
                    'Appropriate schema types applied'
                ]
            ],
            'A' => [
                'label' => 'A',
                'score' => '80-89',
                'criteria' => [
                    'Key schemas valid',
                    'Implemented with JSON-LD',
                    'Most Rich Snippets recognized',
                    '0 errors, ≤2 warnings'
                ]
            ],
            'B' => [
                'label' => 'B',
                'score' => '70-79',
                'criteria' => [
                    'Some core schemas missing',
                    'Limited Rich Snippet recognition',
                    '≤1 error, ≤5 warnings'
                ]
            ],
            'C' => [
                'label' => 'C',
                'score' => '60-69',
                'criteria' => [
                    'Structured data incomplete',
                    'Unstable Rich Snippets',
                    '≤3 errors, many warnings',
                    'C is the ceiling when JSON-LD is not used'
                ]
            ],
            'D' => [
                'label' => 'D',
                'score' => '50-59',
                'criteria' => [
                    'Structured data inconsistent/duplicated',
                    'Rich Snippets not recognized',
                    '≥4 errors (up to 10)',
                    'Incorrect schema types applied'
                ]
            ],
            'F' => [
                'label' => 'F',
                'score' => '0-49',
                'criteria' => [
                    'No structured data implemented',
                    'No JSON-LD/Microdata present',
                    'Widespread errors',
                    'Rich Snippets not possible'
                ]
            ]
        ];
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-content-structure', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'testInformation' => $this->getTestInformation(),
            'gradeCriteria' => $this->getGradeCriteria()
        ]);
    }
}