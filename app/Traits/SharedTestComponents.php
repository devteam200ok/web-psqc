<?php

// app/Traits/SharedTestComponents.php

namespace App\Traits;

use App\Models\WebTest;
use App\Models\Domain;
use App\Models\ScheduledTest;
use App\Models\Certificate;
use App\Models\UserPlan;
use App\Validators\UrlSecurityValidator;
use App\Services\DomainVerificationService;
use Illuminate\Support\Facades\Auth;
use App\Traits\ManagesUserPlanUsage;

trait SharedTestComponents
{
    use ManagesUserPlanUsage;
    
    // Common properties
    public $url = '';
    public $currentTest = null;
    public $isLoading = false;

    public $testHistory = [];
    public $selectedHistoryTest = null;

    public $newDomainUrl = '';
    public $userDomains = [];

    public $sideTabActive = 'history';
    public $mainTabActive = 'information';

    public $showVerificationModal = false;
    public $currentVerificationDomain = null;
    public $verificationMessage = '';
    public $verificationMessageType = 'info';

    // Schedule related properties
    public $showScheduleForm = false;
    public $showRecurringForm = false;
    public $scheduleDate = '';
    public $scheduleHour = '';
    public $scheduleMinute = '';
    public $recurringStartDate = '';
    public $recurringEndDate = '';
    public $recurringHour = '';
    public $recurringMinute = '';

    public $scheduledTests = [];
    public $userPlanUsage = null;

    protected $rules = [
        'url' => 'required|url|max:2048',
    ];

    /**
     * Common initialization - called from each component
     */
    protected function initializeSharedComponents()
    {
        $this->getUrl();
        $this->loadTestHistory();
        $this->loadUserDomains();
        $this->loadScheduledTests();
        $this->refreshUsageInfo();
    }

    /**
     * Refresh usage information
     */
    protected function getUrl()
    {
        if(isset($_GET['url'])) {
            $this->url = $_GET['url'];
        }
    }

    /**
     * Refresh usage information
     */
    protected function refreshUsageInfo()
    {
        if (Auth::check()) {
            $this->userPlanUsage = $this->calculateAvailableUsage();
        } else {
            $this->userPlanUsage = null;
        }
    }

    /**
     * Check if user has Pro/Agency plan
     */
    protected function hasProOrAgencyPlan(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return UserPlan::where('user_id', Auth::id())
            ->whereIn('plan_type', ['pro', 'agency'])
            ->subscription()
            ->active()
            ->where('end_date', '>', now())
            ->exists();
    }

    // === Schedule related methods ===
    
    protected function getScheduleRules()
    {
        return [
            'url' => 'required|url|max:2048',
            'scheduleDate' => 'required|date|after_or_equal:today',
            'scheduleHour' => 'required|numeric|between:0,23',
            'scheduleMinute' => 'required|numeric|between:0,59'
        ];
    }

    protected function getRecurringRules()
    {
        return [
            'url' => 'required|url|max:2048',
            'recurringStartDate' => 'required|date|after_or_equal:today',
            'recurringEndDate' => 'required|date|after_or_equal:recurringStartDate',
            'recurringHour' => 'required|numeric|between:0,23',
            'recurringMinute' => 'required|numeric|between:0,59'
        ];
    }

    public function toggleScheduleForm()
    {
        $this->showScheduleForm = !$this->showScheduleForm;
        $this->showRecurringForm = false;
        $this->resetScheduleForm();
    }

    public function toggleRecurringForm()
    {
        $this->showRecurringForm = !$this->showRecurringForm;
        $this->showScheduleForm = false;
        $this->resetRecurringForm();
    }

    public function resetScheduleForm()
    {
        $this->scheduleDate = '';
        $this->scheduleHour = '';
        $this->scheduleMinute = '';
        $this->resetValidation(['scheduleDate', 'scheduleHour', 'scheduleMinute']);
    }

    public function resetRecurringForm()
    {
        $this->recurringStartDate = '';
        $this->recurringEndDate = '';
        $this->recurringHour = '';
        $this->recurringMinute = '';
        $this->resetValidation(['recurringStartDate', 'recurringEndDate', 'recurringHour', 'recurringMinute']);
    }

    public function scheduleTest()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Login is required for scheduling feature.');
            return;
        }

        $this->validate($this->getScheduleRules());

        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }

        // Usage check
        if (!$this->canUseService(1)) {
            session()->flash('error', 'Insufficient usage remaining. Scheduling requires 1 usage count.');
            return;
        }

        try {
            $scheduledAt = \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i',
                $this->scheduleDate . ' ' . $this->scheduleHour . ':' . $this->scheduleMinute
            );

            if ($scheduledAt <= now()) {
                $this->addError('scheduleDate', 'Scheduled time must be after current time.');
                return;
            }

            // Deduct usage
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $testName = $this->getTestType() . '_scheduled';
            
            if (!$this->consumeService($domain, $testName, 1)) {
                session()->flash('error', 'Failed to deduct usage.');
                return;
            }

            // Create schedule (including deducted usage information)
            $scheduledTest = ScheduledTest::create([
                'user_id' => Auth::id(),
                'test_type' => $this->getTestType(),
                'url' => $this->url,
                'scheduled_at' => $scheduledAt,
                'test_config' => $this->getTestConfig(),
                'usage_deducted' => true, // Indicates usage has been deducted
            ]);

            session()->flash('success', "Test has been scheduled for {$scheduledAt->format('Y-m-d H:i')}.");
            $this->resetScheduleForm();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            $this->showScheduleForm = false;

        } catch (\Exception $e) {
            session()->flash('error', 'Error occurred during scheduling: ' . $e->getMessage());
        }
    }

    public function createRecurringSchedule()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Login is required for schedule registration.');
            return;
        }

        $this->validate($this->getRecurringRules());

        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }

        try {
            $startDate = \Carbon\Carbon::parse($this->recurringStartDate);
            $endDate = \Carbon\Carbon::parse($this->recurringEndDate);
            
            // Calculate total required usage
            $requiredCount = 0;
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $scheduledAt = $date->copy()->setTime($this->recurringHour, $this->recurringMinute);
                if ($scheduledAt > now()) {
                    $requiredCount++;
                }
            }

            if ($requiredCount === 0) {
                session()->flash('error', 'No schedulable dates available. All schedules are in the past.');
                return;
            }

            // Usage check
            if (!$this->canUseService($requiredCount)) {
                session()->flash('error', "Insufficient usage remaining. {$requiredCount} usage counts required.");
                return;
            }

            // Deduct usage
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $testName = $this->getTestType() . '_recurring';
            
            if (!$this->consumeService($domain, $testName, $requiredCount)) {
                session()->flash('error', 'Failed to deduct usage.');
                return;
            }

            // Create schedules
            $created = 0;
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $scheduledAt = $date->copy()->setTime($this->recurringHour, $this->recurringMinute);

                if ($scheduledAt <= now()) {
                    continue;
                }

                ScheduledTest::create([
                    'user_id' => Auth::id(),
                    'test_type' => $this->getTestType(),
                    'url' => $this->url,
                    'scheduled_at' => $scheduledAt,
                    'test_config' => array_merge($this->getTestConfig(), ['recurring' => true]),
                    'usage_deducted' => true, // Indicates usage has been deducted
                ]);

                $created++;
            }

            session()->flash('success', "{$created} schedules have been registered.");
            $this->resetRecurringForm();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            $this->showRecurringForm = false;

        } catch (\Exception $e) {
            session()->flash('error', 'Error occurred during schedule registration: ' . $e->getMessage());
        }
    }

    public function loadScheduledTests()
    {
        if (Auth::check()) {
            $this->scheduledTests = ScheduledTest::where('user_id', Auth::id())
                ->whereIn('status', ['pending'])
                ->orderBy('scheduled_at', 'asc')
                ->get()
                ->map(function ($scheduled) {
                    return [
                        'id' => $scheduled->id,
                        'url' => $scheduled->url,
                        'short_domain' => $scheduled->short_domain,
                        'test_type_name' => $scheduled->test_type_name,
                        'scheduled_at' => $scheduled->scheduled_at,
                        'scheduled_at_formatted' => $scheduled->scheduled_at->format('m/d H:i'),
                        'status' => $scheduled->status,
                        'status_text' => ScheduledTest::getStatuses()[$scheduled->status] ?? $scheduled->status,
                        'status_badge_class' => $scheduled->status_badge_class,
                        'can_be_cancelled' => $scheduled->canBeCancelled(),
                        'is_overdue' => $scheduled->is_overdue,
                        'time_until_execution' => $scheduled->time_until_execution,
                        'usage_deducted' => $scheduled->usage_deducted ?? false,
                    ];
                })
                ->toArray();
        } else {
            $this->scheduledTests = [];
        }
    }

    public function cancelScheduledTest($scheduledId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $scheduled = ScheduledTest::where('id', $scheduledId)
                                ->where('user_id', Auth::id())
                                ->first();
        
        if ($scheduled && $scheduled->canBeCancelled()) {
            // Restore usage if it was deducted
            if ($scheduled->usage_deducted) {
                $this->restoreUsageForCancelledTest($scheduled);
            }
            
            $scheduled->cancel();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            session()->flash('success', 'Scheduled test has been cancelled. Usage has been restored.');
        } else {
            session()->flash('error', 'Cannot cancel this test.');
        }
    }

    /**
     * Restore usage for cancelled test
     */
    protected function restoreUsageForCancelledTest(ScheduledTest $scheduled)
    {
        $userId = $scheduled->user_id;
        $planUsage = $this->getUserPlanUsage();
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return;
        }

        // If no plan - restore to user->usage
        if (!$planUsage['subscription'] && $planUsage['coupons']->isEmpty()) {
            $user->increment('usage', 1);
            return;
        }

        // Restoration priority: Coupon (reverse order of expiry) -> Subscription
        // Restore in reverse order of deduction
        
        // 1. Restore to coupon first (from furthest expiry date)
        if (!$planUsage['coupons']->isEmpty()) {
            $sortedCoupons = $planUsage['coupons']->sortByDesc('end_date');
            
            foreach ($sortedCoupons as $coupon) {
                // If coupon has usage history and can be restored within limit
                if ($coupon->used_count > 0) {
                    $coupon->decrement('used_count', 1);
                    
                    // Also restore daily usage (only if same day)
                    if ($coupon->daily_used_count > 0 && 
                        $coupon->updated_at->isToday()) {
                        $coupon->decrement('daily_used_count', 1);
                    }
                    return;
                }
            }
        }

        // 2. Restore to subscription plan
        if ($planUsage['subscription']) {
            $sub = $planUsage['subscription'];
            
            // Restore monthly usage
            if ($sub->used_count > 0) {
                $sub->decrement('used_count', 1);
            }
            
            // Restore daily usage (only if same day)
            if ($sub->daily_used_count > 0 && 
                $sub->updated_at->isToday()) {
                $sub->decrement('daily_used_count', 1);
            }
        }
    }

    // === History related methods ===
    
    public function loadTestHistory()
    {
        if (Auth::check()) {
            $this->testHistory = WebTest::light()
                ->where('user_id', Auth::id())
                ->where('test_type', $this->getTestType())
                ->recentFirst()
                ->limit(100)
                ->get();
        } else {
            $this->testHistory = collect();
        }
    }

    public function selectHistoryTest($testId)
    {
        $test = WebTest::find($testId);
        
        if ($test && ($test->user_id === Auth::id() || !Auth::check())) {
            $this->selectedHistoryTest = $test;
            $this->currentTest = $test;
            $this->mainTabActive = 'results';
        }
    }

    public function deleteTestHistory($testId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $test = WebTest::where('id', $testId)
                    ->where('user_id', Auth::id())
                    ->first();
        
        if ($test) {
            $test->delete();
            $this->loadTestHistory();
            
            if ($this->selectedHistoryTest && $this->selectedHistoryTest->id === $testId) {
                $this->selectedHistoryTest = null;
                $this->currentTest = null;
            }
            
            session()->flash('success', 'Test history has been deleted.');
        }
    }

    // === Domain related methods ===
    
    public function loadUserDomains()
    {
        if (Auth::check()) {
            $this->userDomains = Domain::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($domain) {
                    return [
                        'id' => $domain->id,
                        'url' => $domain->url,
                        'display_name' => $domain->display_name,
                        'domain_only' => $domain->domain_only,
                        'verification_status' => $domain->verification_status,
                        'verification_status_class' => $this->getVerificationBadgeClass($domain),
                        'txt_record_value' => $domain->txt_record_value,
                        'verification_file_name' => $domain->verification_file_name,
                        'verification_file_content' => $domain->verification_file_content,
                    ];
                })
                ->toArray();
        } else {
            $this->userDomains = [];
        }
    }

    private function getVerificationBadgeClass($domain): string
    {
        if ($domain->is_verified) {
            return 'badge bg-green-lt text-green-lt-fg';
        }
        
        return $domain->verification_token ? 'badge bg-orange-lt text-orange-lt-fg' : 'badge bg-red-lt text-red-lt-fg';
    }

    protected function getDomainRules()
    {
        return [
            'newDomainUrl' => 'required|url|max:2048'
        ];
    }
    
    public function addDomain()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Login is required.');
            return;
        }
        
        $this->validate($this->getDomainRules());
        
        $securityErrors = UrlSecurityValidator::validate($this->newDomainUrl);
        if (!empty($securityErrors)) {
            $this->addError('newDomainUrl', implode(' ', $securityErrors));
            return;
        }
        
        try {
            $domain = Domain::create([
                'user_id' => Auth::id(),
                'url' => $this->newDomainUrl
            ]);
            
            // Check domain status again after created event execution
            $domain->refresh();
            
            $this->newDomainUrl = '';
            
            // Wait briefly before reloading domain list (ensure event processing completion)
            usleep(100000); // 0.1 second wait
            $this->loadUserDomains();
            
            // Check if auto-verified
            if ($domain->is_verified && $domain->verification_method === 'auto_hostname') {
                $hostname = parse_url($domain->url, PHP_URL_HOST);
                session()->flash('success', "Domain has been added and automatically verified as it shares the same hostname as the already verified {$hostname}.");
            } else {
                session()->flash('success', 'Domain has been added.');
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $this->addError('newDomainUrl', 'This URL is already registered.');
            } else {
                $this->addError('newDomainUrl', 'Failed to add domain.');
            }
        }
    }

    public function deleteDomain($domainId)
    {
        if (!Auth::check()) {
            return;
        }
        
        $domain = Domain::where('id', $domainId)
                    ->where('user_id', Auth::id())
                    ->first();
        
        if ($domain) {
            $domain->delete();
            $this->loadUserDomains();
            session()->flash('success', 'Domain has been deleted.');
        }
    }

    public function selectDomain($domainUrl)
    {
        $this->url = $domainUrl;
        session()->flash('success', 'URL has been automatically entered.');
    }

    // === Domain verification related ===
    
    public function openVerificationModal($domainId)
    {
        $domain = Domain::where('id', $domainId)
                    ->where('user_id', Auth::id())
                    ->first();
        
        if ($domain) {
            $this->currentVerificationDomain = [
                'id' => $domain->id,
                'url' => $domain->url,
                'domain_only' => $domain->domain_only,
                'verification_status' => $domain->verification_status,
                'verification_status_class' => $this->getVerificationBadgeClass($domain),
                'txt_record_value' => $domain->txt_record_value,
                'verification_file_name' => $domain->verification_file_name,
                'verification_file_content' => $domain->verification_file_content,
            ];
            
            $this->showVerificationModal = true;
            $this->verificationMessage = '';
        }
    }

    public function closeVerificationModal()
    {
        $this->showVerificationModal = false;
        $this->currentVerificationDomain = null;
        $this->verificationMessage = '';
    }

    public function verifyDomainByTxt()
    {
        if (!$this->currentVerificationDomain) {
            return;
        }
        
        $domain = Domain::find($this->currentVerificationDomain['id']);
        if (!$domain) {
            return;
        }
        
        $verified = DomainVerificationService::verifyByTxtRecord($domain);
        
        if ($verified) {
            // Check auto-verified domain count
            $hostname = parse_url($domain->url, PHP_URL_HOST);
            $autoVerifiedCount = Domain::where('user_id', Auth::id())
                ->where('id', '!=', $domain->id)
                ->where('verification_method', 'auto_hostname')
                ->where('verified_at', '>=', now()->subSeconds(5))
                ->get()
                ->filter(function ($d) use ($hostname) {
                    return parse_url($d->url, PHP_URL_HOST) === $hostname;
                })
                ->count();
            
            $this->verificationMessage = 'TXT record verification completed!';
            if ($autoVerifiedCount > 0) {
                $this->verificationMessage .= " ({$autoVerifiedCount} related domains were also auto-verified)";
            }
            
            $this->verificationMessageType = 'success';
            $this->loadUserDomains();
            $this->currentVerificationDomain['verification_status'] = 'Verified';
            $this->currentVerificationDomain['verification_status_class'] = 'badge bg-green-lt text-green-lt-fg';
        } else {
            $this->verificationMessage = 'TXT record not found. Please check your DNS settings.';
            $this->verificationMessageType = 'danger';
        }
    }
        
    public function verifyDomainByFile()
    {
        if (!$this->currentVerificationDomain) {
            return;
        }
        
        $domain = Domain::find($this->currentVerificationDomain['id']);
        if (!$domain) {
            return;
        }
        
        $verified = DomainVerificationService::verifyByFileUpload($domain);
        
        if ($verified) {
            // Check auto-verified domain count
            $hostname = parse_url($domain->url, PHP_URL_HOST);
            $autoVerifiedCount = Domain::where('user_id', Auth::id())
                ->where('id', '!=', $domain->id)
                ->where('verification_method', 'auto_hostname')
                ->where('verified_at', '>=', now()->subSeconds(5))
                ->get()
                ->filter(function ($d) use ($hostname) {
                    return parse_url($d->url, PHP_URL_HOST) === $hostname;
                })
                ->count();
            
            $this->verificationMessage = 'File upload verification completed!';
            if ($autoVerifiedCount > 0) {
                $this->verificationMessage .= " ({$autoVerifiedCount} related domains were also auto-verified)";
            }
            
            $this->verificationMessageType = 'success';
            $this->loadUserDomains();
            $this->currentVerificationDomain['verification_status'] = 'Verified';
            $this->currentVerificationDomain['verification_status_class'] = 'badge bg-green-lt text-green-lt-fg';
        } else {
            $this->verificationMessage = 'Verification file not found. Please verify the file was uploaded to the correct location.';
            $this->verificationMessageType = 'danger';
        }
    }

    public function refreshVerificationToken()
    {
        if (!$this->currentVerificationDomain) {
            return;
        }
        
        $domain = Domain::find($this->currentVerificationDomain['id']);
        if (!$domain) {
            return;
        }
        
        $domain->generateNewVerificationToken();
        $this->loadUserDomains();
        
        $this->currentVerificationDomain = [
            'id' => $domain->id,
            'url' => $domain->url,
            'domain_only' => $domain->domain_only,
            'verification_status' => $domain->verification_status,
            'verification_status_class' => $this->getVerificationBadgeClass($domain),
            'txt_record_value' => $domain->txt_record_value,
            'verification_file_name' => $domain->verification_file_name,
            'verification_file_content' => $domain->verification_file_content,
        ];
        
        $this->verificationMessage = 'New verification token has been generated.';
        $this->verificationMessageType = 'info';
    }

    // === Certificate issuance ===
    
    public function issueCertificate()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Login is required for certificate issuance.');
            return;
        }

        if (!$this->currentTest) {
            session()->flash('error', 'No test results available.');
            return;
        }

        if (!in_array($this->currentTest->overall_grade, ['A+', 'A', 'B'])) {
            session()->flash('error', 'Certificate issuance is available from grade B and above.');
            return;
        }

        if ($this->currentTest->status !== 'completed') {
            session()->flash('error', 'Only completed tests are eligible for certificate issuance.');
            return;
        }

        if (!$this->currentTest->finished_at || $this->currentTest->finished_at->diffInDays(now()) > 3) {
            session()->flash('error', 'Certificates can only be issued within 3 days of test completion.');
            return;
        }

        try {
            $certificate = Certificate::createFromWebTest($this->currentTest);
            return redirect()->route('certificate.checkout', ['certificate' => $certificate->id]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // === Abstract methods to be implemented by each component ===
    
    /**
     * Return test type (e.g., 'p-speed', 'p-lighthouse', etc.)
     */
    abstract protected function getTestType(): string;

    /**
     * Return test configuration
     */
    abstract protected function getTestConfig(): array;

    /**
     * Common render method
     */
    protected function renderSharedView($viewName, $additionalData = [])
    {
        $this->refreshUsageInfo();
        
        return view($viewName, array_merge([
            'userPlanUsage' => $this->userPlanUsage,
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan()
        ], $additionalData))->layout('layouts.app');
    }
}