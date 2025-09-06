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
    
    // 공통 프로퍼티들
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

    // 예약 관련 프로퍼티
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
     * 공통 초기화 - 각 컴포넌트에서 호출
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
     * 사용량 정보 새로고침
     */
    protected function getUrl()
    {
        if(isset($_GET['url'])) {
            $this->url = $_GET['url'];
        }
    }

    /**
     * 사용량 정보 새로고침
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
     * Pro/Agency 플랜 보유 여부 체크
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

    // === 예약 관련 메서드들 ===
    
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
            session()->flash('error', '예약 기능은 로그인이 필요합니다.');
            return;
        }

        $this->validate($this->getScheduleRules());

        $securityErrors = UrlSecurityValidator::validateWithDnsCheck($this->url);
        if (!empty($securityErrors)) {
            $this->addError('url', implode(' ', $securityErrors));
            return;
        }

        // 사용량 체크
        if (!$this->canUseService(1)) {
            session()->flash('error', '사용 가능한 횟수가 부족합니다. 예약에는 1회 사용량이 필요합니다.');
            return;
        }

        try {
            $scheduledAt = \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i',
                $this->scheduleDate . ' ' . $this->scheduleHour . ':' . $this->scheduleMinute
            );

            if ($scheduledAt <= now()) {
                $this->addError('scheduleDate', '예약 시간은 현재 시간보다 이후여야 합니다.');
                return;
            }

            // 사용량 차감
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $testName = $this->getTestType() . '_scheduled';
            
            if (!$this->consumeService($domain, $testName, 1)) {
                session()->flash('error', '사용량 차감에 실패했습니다.');
                return;
            }

            // 예약 생성 (차감된 사용량 정보 포함)
            $scheduledTest = ScheduledTest::create([
                'user_id' => Auth::id(),
                'test_type' => $this->getTestType(),
                'url' => $this->url,
                'scheduled_at' => $scheduledAt,
                'test_config' => $this->getTestConfig(),
                'usage_deducted' => true, // 사용량이 차감되었음을 표시
            ]);

            session()->flash('success', "검사가 {$scheduledAt->format('Y년 m월 d일 H시 i분')}에 예약되었습니다.");
            $this->resetScheduleForm();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            $this->showScheduleForm = false;

        } catch (\Exception $e) {
            session()->flash('error', '예약 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    public function createRecurringSchedule()
    {
        if (!Auth::check()) {
            session()->flash('error', '스케쥴 등록은 로그인이 필요합니다.');
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
            
            // 필요한 총 사용량 계산
            $requiredCount = 0;
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $scheduledAt = $date->copy()->setTime($this->recurringHour, $this->recurringMinute);
                if ($scheduledAt > now()) {
                    $requiredCount++;
                }
            }

            if ($requiredCount === 0) {
                session()->flash('error', '예약 가능한 일정이 없습니다. 모든 일정이 과거 시간입니다.');
                return;
            }

            // 사용량 체크
            if (!$this->canUseService($requiredCount)) {
                session()->flash('error', "사용 가능한 횟수가 부족합니다. {$requiredCount}회 사용량이 필요합니다.");
                return;
            }

            // 사용량 차감
            $domain = parse_url($this->url, PHP_URL_HOST) ?? $this->url;
            $testName = $this->getTestType() . '_recurring';
            
            if (!$this->consumeService($domain, $testName, $requiredCount)) {
                session()->flash('error', '사용량 차감에 실패했습니다.');
                return;
            }

            // 스케쥴 생성
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
                    'usage_deducted' => true, // 사용량이 차감되었음을 표시
                ]);

                $created++;
            }

            session()->flash('success', "{$created}개의 스케쥴이 등록되었습니다.");
            $this->resetRecurringForm();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            $this->showRecurringForm = false;

        } catch (\Exception $e) {
            session()->flash('error', '스케쥴 등록 중 오류가 발생했습니다: ' . $e->getMessage());
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
            // 사용량이 차감되어 있었다면 복원
            if ($scheduled->usage_deducted) {
                $this->restoreUsageForCancelledTest($scheduled);
            }
            
            $scheduled->cancel();
            $this->loadScheduledTests();
            $this->refreshUsageInfo();
            session()->flash('success', '예약된 검사가 취소되었습니다. 사용량이 복원되었습니다.');
        } else {
            session()->flash('error', '취소할 수 없는 검사입니다.');
        }
    }

    /**
     * 취소된 테스트의 사용량 복원
     */
    protected function restoreUsageForCancelledTest(ScheduledTest $scheduled)
    {
        $userId = $scheduled->user_id;
        $planUsage = $this->getUserPlanUsage();
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return;
        }

        // 플랜이 없는 경우 - user->usage에 복원
        if (!$planUsage['subscription'] && $planUsage['coupons']->isEmpty()) {
            $user->increment('usage', 1);
            return;
        }

        // 복원 우선순위: 쿠폰(만료일 먼 순서) -> 구독
        // 차감과 반대 순서로 복원
        
        // 1. 쿠폰에 먼저 복원 (만료일이 먼 것부터)
        if (!$planUsage['coupons']->isEmpty()) {
            $sortedCoupons = $planUsage['coupons']->sortByDesc('end_date');
            
            foreach ($sortedCoupons as $coupon) {
                // 쿠폰에 사용된 내역이 있고, 한도 내에서 복원 가능한 경우
                if ($coupon->used_count > 0) {
                    $coupon->decrement('used_count', 1);
                    
                    // 일간 사용량도 복원 (당일인 경우만)
                    if ($coupon->daily_used_count > 0 && 
                        $coupon->updated_at->isToday()) {
                        $coupon->decrement('daily_used_count', 1);
                    }
                    return;
                }
            }
        }

        // 2. 구독 플랜에 복원
        if ($planUsage['subscription']) {
            $sub = $planUsage['subscription'];
            
            // 월간 사용량 복원
            if ($sub->used_count > 0) {
                $sub->decrement('used_count', 1);
            }
            
            // 일간 사용량 복원 (당일인 경우만)
            if ($sub->daily_used_count > 0 && 
                $sub->updated_at->isToday()) {
                $sub->decrement('daily_used_count', 1);
            }
        }
    }

    // === 히스토리 관련 메서드들 ===
    
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
            
            session()->flash('success', '검사 내역이 삭제되었습니다.');
        }
    }

    // === 도메인 관련 메서드들 ===
    
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
            session()->flash('error', '로그인이 필요합니다.');
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
            
            // created 이벤트가 실행된 후 도메인 상태 다시 확인
            $domain->refresh();
            
            $this->newDomainUrl = '';
            
            // 도메인 목록을 다시 로드하기 전에 잠시 대기 (이벤트 처리 완료 보장)
            usleep(100000); // 0.1초 대기
            $this->loadUserDomains();
            
            // 자동 인증되었는지 확인
            if ($domain->is_verified && $domain->verification_method === 'auto_hostname') {
                $hostname = parse_url($domain->url, PHP_URL_HOST);
                session()->flash('success', "도메인이 추가되었으며, 이미 인증된 {$hostname}과 같은 호스트네임이므로 자동으로 인증되었습니다.");
            } else {
                session()->flash('success', '도메인이 추가되었습니다.');
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $this->addError('newDomainUrl', '이미 등록된 URL입니다.');
            } else {
                $this->addError('newDomainUrl', '도메인 추가에 실패했습니다.');
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
            session()->flash('success', '도메인이 삭제되었습니다.');
        }
    }

    public function selectDomain($domainUrl)
    {
        $this->url = $domainUrl;
        session()->flash('success', 'URL이 자동으로 입력되었습니다.');
    }

    // === 도메인 인증 관련 ===
    
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
            // 자동 인증된 도메인 수 확인
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
            
            $this->verificationMessage = 'TXT 레코드 인증이 완료되었습니다!';
            if ($autoVerifiedCount > 0) {
                $this->verificationMessage .= " ({$autoVerifiedCount}개의 관련 도메인도 자동 인증되었습니다)";
            }
            
            $this->verificationMessageType = 'success';
            $this->loadUserDomains();
            $this->currentVerificationDomain['verification_status'] = '인증완료';
            $this->currentVerificationDomain['verification_status_class'] = 'badge bg-green-lt text-green-lt-fg';
        } else {
            $this->verificationMessage = 'TXT 레코드를 찾을 수 없습니다. DNS 설정을 확인해주세요.';
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
            // 자동 인증된 도메인 수 확인
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
            
            $this->verificationMessage = '파일 업로드 인증이 완료되었습니다!';
            if ($autoVerifiedCount > 0) {
                $this->verificationMessage .= " ({$autoVerifiedCount}개의 관련 도메인도 자동 인증되었습니다)";
            }
            
            $this->verificationMessageType = 'success';
            $this->loadUserDomains();
            $this->currentVerificationDomain['verification_status'] = '인증완료';
            $this->currentVerificationDomain['verification_status_class'] = 'badge bg-green-lt text-green-lt-fg';
        } else {
            $this->verificationMessage = '인증 파일을 찾을 수 없습니다. 파일이 올바른 위치에 업로드되었는지 확인해주세요.';
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
        
        $this->verificationMessage = '새로운 인증 토큰이 생성되었습니다.';
        $this->verificationMessageType = 'info';
    }

    // === 인증서 발급 ===
    
    public function issueCertificate()
    {
        if (!Auth::check()) {
            session()->flash('error', '인증서 발급은 로그인이 필요합니다.');
            return;
        }

        if (!$this->currentTest) {
            session()->flash('error', '테스트 결과가 없습니다.');
            return;
        }

        if (!in_array($this->currentTest->overall_grade, ['A+', 'A', 'B'])) {
            session()->flash('error', 'B등급 이상부터 인증서 발급이 가능합니다.');
            return;
        }

        if ($this->currentTest->status !== 'completed') {
            session()->flash('error', '완료된 테스트만 인증서 발급이 가능합니다.');
            return;
        }

        if (!$this->currentTest->finished_at || $this->currentTest->finished_at->diffInDays(now()) > 3) {
            session()->flash('error', '인증서는 테스트 완료 후 3일 이내에만 발급 가능합니다.');
            return;
        }

        try {
            $certificate = Certificate::createFromWebTest($this->currentTest);
            return redirect()->route('certificate.checkout', ['certificate' => $certificate->id]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // === 각 컴포넌트에서 구현해야 할 추상 메서드들 ===
    
    /**
     * 테스트 타입 반환 (예: 'p-speed', 'p-lighthouse' 등)
     */
    abstract protected function getTestType(): string;

    /**
     * 테스트 설정 반환
     */
    abstract protected function getTestConfig(): array;

    /**
     * 공통 렌더 메서드
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