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
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'q-accessibility';
    }

    /**
     * 테스트 설정 반환
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
     * 접근성 테스트 실행
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
            $this->addError('url', '동일한 URL에 대한 테스트가 최근 1분 내에 실행되었습니다.');
            return;
        }

        // 사용량 체크
        if (Auth::check()) {
            if (!$this->canUseService()) {
                session()->flash('error', '사용 가능한 횟수를 초과했습니다.');
                return;
            }
        } else {
            if (!$this->hasUsageRemaining()) {
                session()->flash('error', '사용량이 초과되었습니다. 로그인 후 이용해주세요.');
                return;
            }
        }

        $this->isLoading = true;

        // WebTest 생성
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
     * 접근성 테스트 정보 텍스트
     */
    public function getTestInformation(): array
    {
        return [
            'title' => '웹 접근성 심화 테스트 (axe-core 기반)',
            'description' => 'WCAG 2.1 규칙 기반으로 웹사이트의 접근성을 자동으로 검사합니다.',
            'details' => [
                '• axe-core CLI를 사용한 국제 표준 준수 검사',
                '• WCAG 2.1 Level A, AA 규칙 및 모범 사례 검증',
                '• Critical, Serious, Moderate, Minor 4단계 중요도 분류',
                '• 키보드 탐색, 스크린 리더 호환성, ARIA 속성 검증',
                '• 색상 대비, 대체 텍스트, 레이블링 점검',
                '• 랜드마크, 헤딩 구조, 포커스 관리 분석'
            ],
            'test_duration' => '약 30초 ~ 2분',
            'test_method' => 'Puppeteer 기반 헤드리스 브라우저로 페이지를 렌더링한 후, axe-core 엔진으로 접근성 규칙을 검사합니다.'
        ];
    }

    /**
     * 등급 기준 정보
     */
    public function getGradeCriteria(): array
    {
        return [
            'A+' => [
                'score' => '90~100',
                'criteria' => [
                    'Critical: 0건',
                    'Serious: 0건',
                    '전체 위반: 3건 이하',
                    '키보드/ARIA/대체텍스트/대비 모두 양호'
                ]
            ],
            'A' => [
                'score' => '80~89',
                'criteria' => [
                    'Critical: 0건',
                    'Serious: 3건 이하',
                    '전체 위반: 8건 이하',
                    '주요 Landmark/Label 대체로 양호'
                ]
            ],
            'B' => [
                'score' => '70~79',
                'criteria' => [
                    'Critical: 1건 이하',
                    'Serious: 6건 이하',
                    '전체 위반: 15건 이하',
                    '일부 contrast/label 개선 필요'
                ]
            ],
            'C' => [
                'score' => '60~69',
                'criteria' => [
                    'Critical: 3건 이하',
                    'Serious: 10건 이하',
                    '전체 위반: 25건 이하',
                    '포커스/ARIA 구조 보완 필요'
                ]
            ],
            'D' => [
                'score' => '50~59',
                'criteria' => [
                    'Critical: 6건 이하 또는 Serious: 18건 이하',
                    '전체 위반: 40건 이하',
                    '키보드 트랩/레이블 누락 다수'
                ]
            ],
            'F' => [
                'score' => '0~49',
                'criteria' => [
                    '위 기준 초과',
                    'Critical/Serious 다수',
                    '스크린리더/키보드 이용 곤란 수준'
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