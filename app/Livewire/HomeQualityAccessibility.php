<?php

namespace App\Livewire;

use App\Jobs\RunAccessibilityTest;
use App\Models\WebTest;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\ManagesIpUsage;
use App\Traits\ManagesUserPlanUsage;
use App\Traits\SharedTestComponents;
use Illuminate\Support\Facades\Auth;
use App\Validators\UrlSecurityValidator;

class HomeQualityAccessibility extends Component
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
        return 'q-accessibility';
    }

    /**
     * Return test configuration
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'axe_core',
            'tags' => 'wcag2a,wcag2aa,best-practice',
            'timeout' => 120
        ];
    }

    /**
     * Run accessibility test
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
            $this->addError('url', 'A test for this URL was already executed within the last minute.');
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
        if (Auth::check()) {
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $this->consumeService($domain, $this->getTestType());
            $this->refreshUsageInfo();
        } else {
            $this->consumeUsage();
        }

        $this->currentTest = $test;
        RunAccessibilityTest::dispatch($this->url, $test->id)->onQueue('accessibility');
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
     * Accessibility test information text
     */
    public function getTestInformation(): array
    {
        return [
            'title' => 'Web Accessibility Advanced Test (axe-core based)',
            'description' => 'Automatically checks website accessibility based on WCAG 2.1 standards.',
            'details' => [
                '• International standard compliance check using axe-core CLI',
                '• Verification against WCAG 2.1 Level A and AA rules and best practices',
                '• Four severity levels: Critical, Serious, Moderate, Minor',
                '• Keyboard navigation, screen reader compatibility, ARIA attribute validation',
                '• Color contrast, alternative text, labeling verification',
                '• Landmark, heading structure, and focus management analysis'
            ],
            'test_duration' => 'Approx. 30 seconds to 2 minutes',
            'test_method' => 'The page is rendered with a Puppeteer-based headless browser, then accessibility rules are validated using the axe-core engine.'
        ];
    }

    /**
     * Grade criteria information
     */
    public function getGradeCriteria(): array
    {
        return [
            'A+' => [
                'score' => '90~100',
                'criteria' => [
                    'Critical: 0',
                    'Serious: 0',
                    'Total violations: 3 or fewer',
                    'Keyboard/ARIA/Alt-text/Contrast all valid'
                ]
            ],
            'A' => [
                'score' => '80~89',
                'criteria' => [
                    'Critical: 0',
                    'Serious: 3 or fewer',
                    'Total violations: 8 or fewer',
                    'Majority of Landmarks/Labels valid'
                ]
            ],
            'B' => [
                'score' => '70~79',
                'criteria' => [
                    'Critical: up to 1',
                    'Serious: up to 6',
                    'Total violations: 15 or fewer',
                    'Some contrast/label improvements needed'
                ]
            ],
            'C' => [
                'score' => '60~69',
                'criteria' => [
                    'Critical: up to 3',
                    'Serious: up to 10',
                    'Total violations: 25 or fewer',
                    'Focus/ARIA structure improvements needed'
                ]
            ],
            'D' => [
                'score' => '50~59',
                'criteria' => [
                    'Critical: up to 6 OR Serious: up to 18',
                    'Total violations: 40 or fewer',
                    'Multiple keyboard traps/label omissions'
                ]
            ],
            'F' => [
                'score' => '0~49',
                'criteria' => [
                    'Exceeds above criteria',
                    'Multiple Critical/Serious issues',
                    'Severe accessibility barriers for screen reader/keyboard use'
                ]
            ]
        ];
    }

    public function render()
    {
        return $this->renderSharedView('livewire.home-quality-accessibility', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'testInformation' => $this->getTestInformation(),
            'gradeCriteria' => $this->getGradeCriteria()
        ]);
    }
}