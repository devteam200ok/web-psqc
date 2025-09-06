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
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'c-crawl';
    }

    /**
     * 테스트 설정 반환
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
     * 크롤링 테스트 실행
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
            'domain' => parse_url($this->url, PHP_URL_HOST) ?? $this->url,
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
     * 테스트 정보 탭 내용
     */
    public function getTestInformation(): array
    {
        return [
            'title' => '사이트 크롤링 검사',
            'subtitle' => 'robots.txt/sitemap.xml 기반 SEO 기술검사 + 페이지 품질 분석',
            'description' => '
                웹사이트의 robots.txt와 sitemap.xml을 분석하여 SEO 준수 여부를 검증하고,
                sitemap에 등록된 페이지들의 접근성과 품질을 종합적으로 평가합니다.
                <br><br>
                <strong>검사 프로세스:</strong><br>
                1. robots.txt 파일 존재 여부 및 규칙 확인<br>
                2. sitemap.xml 파일 검색 및 URL 수집<br>
                3. robots.txt 규칙에 따른 크롤링 허용 URL 필터링<br>
                4. 최대 50개 페이지 샘플링 및 순차 검사<br>
                5. 각 페이지의 HTTP 상태, 메타데이터, 품질 점수 측정<br>
                6. 중복 콘텐츠(title/description) 비율 분석<br><br>
                
                <strong>품질 점수 산정 기준:</strong><br>
                • Title 태그 길이 (5자 미만: -15점)<br>
                • Description 메타 태그 (20자 미만: -10점)<br>
                • Canonical URL 누락 (-5점)<br>
                • H1 태그 부재 (-10점) / 과다 사용 (-5점)<br>
                • 콘텐츠 부족 (1000자 미만: -10점)<br><br>
                
                이 검사는 약 <strong>30초~2분</strong> 정도 소요됩니다.
            ',
            'grades' => [
                'A+' => [
                    'criteria' => [
                        'robots.txt 정상 적용',
                        'sitemap.xml 존재 및 누락/404 없음',
                        '검사 대상 페이지 전부 2xx',
                        '전체 페이지 품질 평균 ≥ 85점',
                        '중복 콘텐츠 ≤ 30%'
                    ]
                ],
                'A' => [
                    'criteria' => [
                        'robots.txt 정상 적용',
                        'sitemap.xml 존재 및 정합성 확보',
                        '검사 대상 페이지 전부 2xx',
                        '전체 페이지 품질 평균 ≥ 85점'
                    ]
                ],
                'B' => [
                    'criteria' => [
                        'robots.txt 및 sitemap.xml 존재',
                        '검사 대상 페이지 전부 2xx',
                        '전체 페이지 품질 평균 무관'
                    ]
                ],
                'C' => [
                    'criteria' => [
                        'robots.txt 및 sitemap.xml 존재',
                        '검사 리스트 일부 4xx/5xx 오류 포함'
                    ]
                ],
                'D' => [
                    'criteria' => [
                        'robots.txt 및 sitemap.xml 존재',
                        '검사 대상 URL 생성 가능',
                        '단, 정상 접근률 낮거나 품질 점검 불가'
                    ]
                ],
                'F' => [
                    'criteria' => [
                        'robots.txt 부재 또는 sitemap.xml 부재',
                        '검사 리스트 자체 생성 불가'
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