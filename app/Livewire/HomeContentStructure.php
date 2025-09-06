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
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'c-structure';
    }

    /**
     * 테스트 설정 반환
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
     * 구조화 데이터 테스트 실행
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
     * 테스트 정보 텍스트 반환
     */
    public function getTestInformation(): array
    {
        return [
            'title' => 'JSON-LD / Schema.org 구조화 데이터 검증',
            'description' => '검색엔진이 웹페이지의 콘텐츠를 더 잘 이해하고 Rich Results(리치 스니펫)로 표시할 수 있도록 하는 구조화 데이터를 검증합니다.',
            'details' => [
                'JSON-LD 형식 파싱 및 유효성 검사',
                'Schema.org 타입별 필수 필드 검증',
                'Google Rich Results 지원 타입 감지',
                'Microdata 및 RDFa 존재 여부 확인',
                '오류, 경고 및 개선 권장사항 제공',
                '예시 JSON-LD 스니펫 제공'
            ],
            'test_items' => [
                'Organization, WebSite, BreadcrumbList 등 기본 스키마',
                'Article, Product, FAQPage 등 콘텐츠별 스키마',
                'Event, JobPosting, LocalBusiness 등 특수 스키마',
                'AggregateRating, Review 등 평가 관련 스키마',
                'VideoObject, Recipe, Course 등 미디어 스키마'
            ],
            'benefits' => [
                '검색 결과에서 Rich Snippets 노출 가능',
                '클릭률(CTR) 향상',
                '검색엔진 콘텐츠 이해도 개선',
                'Voice Search 및 AI 검색 최적화',
                '지식 그래프(Knowledge Graph) 등록 가능성 증가'
            ]
        ];
    }

    /**
     * 등급 기준 정보 반환
     */
    public function getGradeCriteria(): array
    {
        return [
            'A+' => [
                'label' => 'A+',
                'score' => '90-100',
                'criteria' => [
                    'JSON-LD 완벽 구현',
                    'Rich Results 100% 인식',
                    '오류 0개, 경고 0개',
                    '모든 필수 필드 완비',
                    '적절한 스키마 타입 적용'
                ]
            ],
            'A' => [
                'label' => 'A',
                'score' => '80-89',
                'criteria' => [
                    '주요 스키마 정상',
                    'JSON-LD 기반 구현',
                    'Rich Snippets 대부분 인식',
                    '오류 0개, 경고 ≤2개'
                ]
            ],
            'B' => [
                'label' => 'B',
                'score' => '70-79',
                'criteria' => [
                    '핵심 스키마 일부 누락',
                    'Rich Snippets 제한적 인식',
                    '오류 ≤1개, 경고 ≤5개'
                ]
            ],
            'C' => [
                'label' => 'C',
                'score' => '60-69',
                'criteria' => [
                    '구조화 데이터 불완전',
                    'Rich Snippets 불안정',
                    '오류 ≤3개, 경고 다수',
                    'JSON-LD 미사용 시 상한 C'
                ]
            ],
            'D' => [
                'label' => 'D',
                'score' => '50-59',
                'criteria' => [
                    '구조화 데이터 불일치/중복',
                    'Rich Snippets 미인식',
                    '오류 ≥4개 (≤10개)',
                    '잘못된 타입 적용'
                ]
            ],
            'F' => [
                'label' => 'F',
                'score' => '0-49',
                'criteria' => [
                    '구조화 데이터 미구현',
                    'JSON-LD/마이크로데이터 전무',
                    '오류 전면적 발생',
                    'Rich Snippets 불가'
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