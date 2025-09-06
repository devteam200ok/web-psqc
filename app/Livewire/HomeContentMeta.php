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
     * 테스트 타입 반환
     */
    protected function getTestType(): string
    {
        return 'c-meta';
    }

    /**
     * 테스트 설정 반환
     */
    protected function getTestConfig(): array
    {
        return [
            'test_mode' => 'metadata_analysis',
            'timeout' => 15000
        ];
    }

    /**
     * 메타데이터 테스트 실행
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
     * 메타데이터 테스트만의 고유 메서드 - 개선 제안 생성
     */
    public function getImprovementSuggestions(): array
    {
        if (!$this->currentTest || !$this->currentTest->metrics) {
            return [];
        }

        $suggestions = [];
        $metrics = $this->currentTest->metrics;

        // Title 개선 제안
        if (!$metrics['title']['is_optimal']) {
            if ($metrics['title']['length'] < 50) {
                $suggestions[] = '제목을 50-60자 사이로 늘려 검색 결과에서 더 많은 정보를 제공하세요.';
            } elseif ($metrics['title']['length'] > 60) {
                $suggestions[] = '제목을 60자 이하로 줄여 검색 결과에서 잘리지 않도록 하세요.';
            }
        }

        // Description 개선 제안
        if (!$metrics['description']['is_optimal']) {
            if ($metrics['description']['length'] < 120) {
                $suggestions[] = '설명을 120-160자 사이로 작성하여 검색 결과에서 충분한 정보를 제공하세요.';
            } elseif ($metrics['description']['length'] > 160) {
                $suggestions[] = '설명을 160자 이하로 줄여 검색 결과에서 완전히 표시되도록 하세요.';
            }
        }

        // Open Graph 개선 제안
        if (!$metrics['open_graph']['is_perfect']) {
            if (!$metrics['open_graph']['has_basic']) {
                $suggestions[] = 'Open Graph 기본 태그(title, description, image, url)를 추가하세요.';
            } else {
                $suggestions[] = 'Open Graph type 태그를 추가하여 소셜 미디어 공유를 최적화하세요.';
            }
        }

        // Canonical 개선 제안
        if (!$metrics['canonical']['exists']) {
            $suggestions[] = 'Canonical URL을 설정하여 중복 콘텐츠 문제를 방지하세요.';
        }

        // Twitter Cards 개선 제안
        if (!$metrics['twitter_cards']['has_basic']) {
            $suggestions[] = 'Twitter Cards 태그를 추가하여 트위터에서의 공유를 개선하세요.';
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