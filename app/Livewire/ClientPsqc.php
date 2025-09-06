<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain;
use App\Models\WebTest;
use App\Models\PsqcCertification;
use App\Traits\SharedTestComponents;

class ClientPsqc extends Component
{
    use SharedTestComponents;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $dateFrom = '';
    public $dateTo = '';
    public $status = 'all'; // all|valid|expired
    public $type = 'all';   // all or test_type key
    public $perPage = 12;

    public $page = 'history';
    public array $psqcCards = [];

    public function mount()
    {
        $count = PsqcCertification::where('user_id', Auth::id())
            ->where('payment_status', '=', 'paid')
            ->where('is_valid', true)
            ->orderByDesc('issued_at')
            ->count();

        if($count == 0) {
            $this->page = 'issue';
        }

        $this->initializeSharedComponents();
        $this->sideTabActive = 'domain';
        $this->buildPsqcCards();
    }

    public function updated($name)
    {
        // Any filter change resets pagination
        if (in_array($name, ['dateFrom', 'dateTo', 'status', 'type'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->status = 'all';
        $this->type = 'all';
        $this->resetPage();
    }

    public function buildPsqcCards(): void
    {
        if (!Auth::check()) {
            $this->psqcCards = [];
            return;
        }

        $testTypes = WebTest::getTestTypes();
        $verifiedDomains = Domain::forUser(Auth::id())->verified()->get();

        $cards = [];
        foreach ($verifiedDomains as $domain) {
            $tests = [];
            $completedCount = 0;
            $since = now()->subDays(3);

            foreach ($testTypes as $key => $label) {
                // psqc_certification_id가 null인 테스트만 가져오기 (이미 사용되지 않은 것)
                $test = WebTest::light()
                    ->where('user_id', Auth::id())
                    ->where('url', $domain->url)
                    ->byTestType($key)
                    ->completed()
                    ->where('finished_at', '>=', $since)
                    ->whereNull('psqc_certification_id') // 이미 인증서에 사용되지 않은 것만
                    ->orderByDesc('overall_score')
                    ->first();

                $tests[$key] = $test; // may be null
                if ($test) { $completedCount++; }
            }

            // Calculate weighted PSQC score if all tests present
            $finalScore = null;
            $finalGrade = null;
            if ($completedCount === count($testTypes)) {
                [$finalScore, $finalGrade] = $this->calculatePsqcScore($tests);
            }

            $cards[] = [
                'domain_id' => $domain->id,
                'domain' => $domain->domain_only,
                'display' => $domain->display_name,
                'url' => $domain->url,
                'tests' => $tests,
                'completed' => $completedCount,
                'total' => count($testTypes),
                'final_score' => $finalScore,
                'final_grade' => $finalGrade,
            ];
        }

        // Sort by most completed tests
        usort($cards, function ($a, $b) {
            return $b['completed'] <=> $a['completed'];
        });

        $this->psqcCards = $cards;
    }

    private function calculatePsqcScore(array $tests): array
    {
        // Expect keys per WebTest::getTestTypes()
        $scoreOf = function ($key) use ($tests) {
            return isset($tests[$key]) && $tests[$key]?->overall_score ? (float) $tests[$key]->overall_score : 0.0;
        };

        // Performance (300)
        $perf = $scoreOf('p-speed') * 1.0
              + $scoreOf('p-load') * 1.0
              + $scoreOf('p-mobile') * 1.0; // 300 max

        // Security (300)
        $sec = $scoreOf('s-ssl') * 0.8
             + $scoreOf('s-sslyze') * 0.6
             + $scoreOf('s-header') * 0.6
             + $scoreOf('s-scan') * 0.6
             + $scoreOf('s-nuclei') * 0.4; // 300 max

        // Quality (250)
        $qual = $scoreOf('q-lighthouse') * 1.2
              + $scoreOf('q-accessibility') * 0.7
              + $scoreOf('q-compatibility') * 0.3
              + $scoreOf('q-visual') * 0.3; // 250 max

        // Content (150)
        $cont = $scoreOf('c-links') * 0.5
              + $scoreOf('c-structure') * 0.4
              + $scoreOf('c-crawl') * 0.4
              + $scoreOf('c-meta') * 0.2; // 150 max

        $total = round($perf + $sec + $qual + $cont, 1); // 1000 max

        $grade = match (true) {
            $total >= 900 => 'A+',
            $total >= 800 => 'A',
            $total >= 700 => 'B',
            $total >= 600 => 'C',
            $total >= 500 => 'D',
            default => 'F',
        };

        return [$total, $grade];
    }

    // Stubs required by SharedTestComponents
    protected function getTestType(): string { return 'psqc'; }
    protected function getTestConfig(): array { return []; }

    // === 인증서 발급 ===
    public function issueCertificate($domainId)
    {
        if (!Auth::check()) {
            session()->flash('error', '인증서 발급은 로그인이 필요합니다.');
            return;
        }

        try {
            // 도메인 ID로 카드 찾기
            $card = collect($this->psqcCards)->firstWhere('domain_id', $domainId);
            
            if (!$card) {
                throw new \Exception('도메인 정보를 찾을 수 없습니다.');
            }

            if ($card['completed'] !== $card['total']) {
                throw new \Exception('모든 테스트가 완료되지 않았습니다.');
            }

            // 인증서 생성
            $psqc = \App\Models\PsqcCertification::createCertification(
                Auth::id(),
                $card['url'],
                $card['domain'],
                $card['final_grade'],
                $card['final_score'],
                $card['tests']
            );

            // 결제 페이지로 리다이렉트
            return redirect()->route('psqc.checkout', ['certificate' => $psqc->id]);
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function generatePsqcCertificatePdf($code)
    {
        \Illuminate\Support\Facades\Artisan::call('cert:make-psqc-pdf', [
            'code'    => $code,
            '--force' => true,
        ]);

        $rel = "psqc-certification/{$code}.pdf";
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($rel)) {
            session()->flash('success', '인증서 PDF가 생성되었습니다.');
        } else {
            session()->flash('error', 'PDF 생성에 실패했습니다.');
        }
    }

    public function render()
    {
        // Always rebuild to reflect latest tests/domains quickly
        $this->buildPsqcCards();

        if($this->page == 'history') {
            $query = PsqcCertification::query()
                ->where('user_id', auth()->id())
                ->where('payment_status', 'paid'); // 발급 완료된 인증서만

            if (!empty($this->dateFrom)) {
                $query->whereDate('issued_at', '>=', $this->dateFrom);
            }
            if (!empty($this->dateTo)) {
                $query->whereDate('issued_at', '<=', $this->dateTo);
            }

            if ($this->status === 'valid') {
                $query->where('is_valid', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            } elseif ($this->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('is_valid', false)
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('expires_at')->where('expires_at', '<=', now());
                    });
                });
            }

            $certifications = $query->orderByDesc('issued_at')
                                      ->orderByDesc('id')
                                      ->paginate($this->perPage);
        } else {
            $certifications = [];
        }

        return view('livewire.client-psqc', [
            'hasProOrAgencyPlan' => $this->hasProOrAgencyPlan(),
            'certifications' => $certifications,
        ])->layout('layouts.app');
    }
}