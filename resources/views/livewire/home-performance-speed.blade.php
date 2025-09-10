@section('title')
@section('title')
    <title>⚡ Global Speed Test - Loading Speed Measurement Across 8 Regions - Web-PSQC</title>
    <meta name="description"
        content="Simultaneously measure website loading speeds from 8 global regions (Seoul, Tokyo, Singapore, Sydney, Virginia, Oregon, Frankfurt, London) and receive performance ratings. Get TTFB, load time analysis and global user experience optimization insights.">
    <meta name="keywords"
        content="global speed test, website performance measurement, TTFB test, load time analysis, multi-region performance test, web speed optimization, global CDN test, website performance rating, regional speed measurement, Web-PSQC">
    <meta name="author" content="Web-PSQC">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="⚡ Global Speed Test - Loading Speed Measurement Across 8 Regions - Web-PSQC" />
    <meta property="og:description"
        content="Simultaneously measure website performance across 8 global regions to analyze global user experience and receive performance certificates up to A+ grade." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property='og:image' content='{{ url('/') }}/storage/{{ $setting->og_image }}' />
        <meta property='og:image:alt' content='Web-PSQC – Global Speed Test' />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="⚡ Global Speed Test - Loading Speed Measurement Across 8 Regions - Web-PSQC" />
    <meta name="twitter:description"
        content="Simultaneous TTFB/LoadTime measurement across 8 regions, result grading and certificate issuance support." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage + BreadcrumbList --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Global Speed Test',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Test page that evaluates global user experience by simultaneously measuring TTFB and Load Time across 8 global regions.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
@endsection
@section('css')
@include('components.test-shared.css')
<style>
    #loading-progress {
        border-left: 4px solid #0066cc;
        animation: pulse-border 2s infinite;
    }

    @keyframes pulse-border {
        0% {
            border-left-color: #0066cc;
        }

        50% {
            border-left-color: #0099ff;
        }

        100% {
            border-left-color: #0066cc;
        }
    }

    .progress-bar {
        transition: width 0.3s ease;
        background: linear-gradient(90deg, #0066cc, #0099ff);
    }

    #current-region {
        font-family: 'Courier New', monospace;
        color: #495057;
        min-height: 1.2em;
    }
</style>
@endsection

<div class="page-wrapper">
{{-- 헤더 (공통 컴포넌트) --}}
<x-test-shared.header title="Global Speed" subtitle="Measure load speed across 8 regions" :user-plan-usage="$userPlanUsage"
    :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

<div class="page-body">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-8 d-block mb-2">
                {{-- URL 폼 (개별 컴포넌트) --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- URL 입력 폼 -->
                        <div class="row mb-4">
                            <div class="col-xl-12">
                                <label class="form-label">Website URL</label>
                                <div class="input-group">
                                    <input type="url" wire:model="url" wire:keydown.enter="runTest"
                                        class="form-control @error('url') is-invalid @enderror"
                                        placeholder="https://www.example.com"
                                        @if ($isLoading) disabled @endif>
                                    <button wire:click="runTest" class="btn btn-primary"
                                        @if ($isLoading) disabled @endif>
                                        @if ($isLoading)
                                            <span class="spinner-border spinner-border-sm me-2"
                                                role="status"></span>
                                            Testing...
                                        @else
                                            Test
                                        @endif
                                    </button>
                                </div>
                                @error('url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($hasProOrAgencyPlan)
                                    <div class="mt-2">
                                        <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                            class="text-primary me-3">Schedule Test</a>
                                        <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                            class="text-primary">Recurring Schedule</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- URL 입력 폼 아래에 추가 -->
                        @if ($isLoading)
                            <div class="mt-3 p-3 bg-light rounded" id="loading-progress" wire:ignore>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <span class="fw-bold">Testing in progress...</span>
                                </div>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"
                                        id="progress-bar"></div>
                                </div>
                                <div class="small text-muted">
                                    Estimated time: 30-60 seconds | Testing across 8 global regions
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($hasProOrAgencyPlan)
                    {{-- 검사 예약 폼 (공통 컴포넌트) --}}
                    <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                        :schedule-minute="$scheduleMinute" />

                    {{-- 스케쥴 등록 폼 (공통 컴포넌트) --}}
                    <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                        :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                @endif

                {{-- 테스트 상태 (공통 컴포넌트) --}}
                <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                {{-- 개별 테스트만의 고유 내용 --}}
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Test Info</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Results</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>8 Regions: Seoul, Tokyo, Sydney, Singapore, Frankfurt, Virginia, Oregon, London
                                </h3>
                                <div class="text-muted small mt-1">
                                    Simulate real global user access speeds through 8 regions distributed across
                                    major internet hubs worldwide (Asia, North America, Europe, Oceania).
                                    <br><br>
                                    • Asia (Seoul, Tokyo, Singapore) → Covers East & Southeast Asia<br>
                                    • Oceania (Sydney) → Australia and Pacific region<br>
                                    • North America (Virginia, Oregon) → East & West coast hubs<br>
                                    • Europe (Frankfurt, London) → Western & Central European major hubs
                                    <br><br>
                                    These 8 regions are core hubs commonly operated by global infrastructure
                                    providers like Cloudflare, AWS, and GCP, representing the majority of worldwide
                                    internet traffic.
                                    <br><br>
                                    <strong>Web-PSQC</strong> sends API requests to self-built testing servers in
                                    each region, aggregates all results, and generates reports.<br>
                                    This process takes approximately <strong>30 seconds to 2 minutes</strong>.
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive mt-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Score</th>
                                                <th>Criteria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Origin: TTFB ≤ 200ms, Load ≤ 1.5s<br>Global Average: TTFB ≤
                                                    800ms, Load
                                                    ≤ 2.5s<br>All Regions: TTFB ≤ 1.5s, Load ≤ 3s<br>Repeat Visit
                                                    Improvement: 80%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>Origin: TTFB ≤ 400ms, Load ≤ 2.5s<br>Global Average: TTFB ≤
                                                    1.2s, Load ≤
                                                    3.5s<br>All Regions: TTFB ≤ 2s, Load ≤ 4s<br>Repeat Visit
                                                    Improvement: 60%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Origin: TTFB ≤ 800ms, Load ≤ 3.5s<br>Global Average: TTFB ≤
                                                    1.6s, Load ≤
                                                    4.5s<br>All Regions: TTFB ≤ 2.5s, Load ≤ 5.5s<br>Repeat Visit
                                                    Improvement: 50%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Origin: TTFB ≤ 1.2s, Load ≤ 4.5s<br>Global Average: TTFB ≤ 2.0s,
                                                    Load ≤
                                                    5.5s<br>All Regions: TTFB ≤ 3.0s, Load ≤ 6.5s<br>Repeat Visit
                                                    Improvement: 37.5%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Origin: TTFB ≤ 1.6s, Load ≤ 6.0s<br>Global Average: TTFB ≤ 2.5s,
                                                    Load ≤
                                                    7.0s<br>All Regions: TTFB ≤ 3.5s, Load ≤ 8.5s<br>Repeat Visit
                                                    Improvement: 25%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Below standards</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">
                                @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                    @php
                                        $results = $currentTest->results['results'] ?? [];
                                        $probeErrors = $currentTest->results['errors'] ?? [];

                                        // 기존 계산 로직은 그대로 유지...
                                        $regionLabels = [
                                            'seoul' => 'Seoul',
                                            'tokyo' => 'Tokyo',
                                            'singapore' => 'Singapore',
                                            'virginia' => 'Virginia',
                                            'oregon' => 'Oregon',
                                            'frankfurt' => 'Frankfurt',
                                            'london' => 'London',
                                            'sydney' => 'Sydney',
                                        ];

                                        $firstTTFB = [];
                                        $firstLoad = [];
                                        $repeatTTFB = [];
                                        $repeatLoad = [];

                                        foreach ($regionLabels as $region => $label) {
                                            $m = $currentTest->getRegionMetrics($region);
                                            if (!$m) {
                                                continue;
                                            }

                                            $ft = data_get($m, 'first.ttfb');
                                            $fl = data_get($m, 'first.load');
                                            $rt = data_get($m, 'repeat.ttfb');
                                            $rl = data_get($m, 'repeat.load');

                                            if (is_numeric($ft)) {
                                                $firstTTFB[$region] = (float) $ft;
                                            }
                                            if (is_numeric($fl)) {
                                                $firstLoad[$region] = (float) $fl;
                                            }
                                            if (is_numeric($rt)) {
                                                $repeatTTFB[$region] = (float) $rt;
                                            }
                                            if (is_numeric($rl)) {
                                                $repeatLoad[$region] = (float) $rl;
                                            }
                                        }

                                        // Origin = TTFB가 가장 빠른 리전
                                        $originRegion = null;
                                        $originTTFB = null;
                                        $originLoad = null;
                                        if (!empty($firstTTFB)) {
                                            $tmp = $firstTTFB;
                                            asort($tmp);
                                            $originRegion = array_key_first($tmp);
                                            $originTTFB = $tmp[$originRegion] ?? null;
                                            $originLoad =
                                                $firstLoad[$originRegion] ??
                                                (count($firstLoad) ? min($firstLoad) : null);
                                        }

                                        $avgTTFB = count($firstTTFB) ? array_sum($firstTTFB) / count($firstTTFB) : null;
                                        $avgLoad = count($firstLoad) ? array_sum($firstLoad) / count($firstLoad) : null;
                                        $worstTTFB = count($firstTTFB) ? max($firstTTFB) : null;
                                        $worstLoad = count($firstLoad) ? max($firstLoad) : null;

                                        // 재방문 성능향상 계산
                                        $improvedRegions = 0;
                                        $eligibleRegions = 0;
                                        foreach ($firstLoad as $r => $fl) {
                                            $rl = $repeatLoad[$r] ?? null;
                                            if (is_numeric($fl) && is_numeric($rl) && $fl > 0) {
                                                $eligibleRegions++;
                                                if ($rl < $fl) {
                                                    $improvedRegions++;
                                                }
                                            }
                                        }
                                        $repeatImprovePct = $eligibleRegions
                                            ? ($improvedRegions / $eligibleRegions) * 100.0
                                            : null;

                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $gradeClass = match ($grade) {
                                            'A+' => 'badge bg-green-lt text-green-lt-fg',
                                            'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                            'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                            'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                            'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                            'F' => 'badge bg-red-lt text-red-lt-fg',
                                            default => 'badge bg-secondary',
                                        };

                                        $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                        $fmt = fn($v, $unit = 'ms') => is_numeric($v)
                                            ? number_format($v, 1) . $unit
                                            : 'No data';
                                        $fmtPct = fn($v) => is_numeric($v) ? number_format($v, 1) . '%' : 'No data';
                                    @endphp

                                    <x-test-shared.certificate :current-test="$currentTest" />

                                    <!-- 성능 지표 요약 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Performance Summary</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>TTFB</th>
                                                            <th>Load Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Origin
                                                                    ({{ $originRegion ? ucfirst($originRegion) : 'N/A' }})</strong>
                                                            </td>
                                                            <td>{{ $fmt($originTTFB) }}</td>
                                                            <td>{{ $fmt($originLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Global Average</strong></td>
                                                            <td>{{ $fmt($avgTTFB) }}</td>
                                                            <td>{{ $fmt($avgLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>All Regions (Maximum)</strong></td>
                                                            <td>{{ $fmt($worstTTFB) }}</td>
                                                            <td>{{ $fmt($worstLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Repeat Visit Improvement</strong></td>
                                                            <td colspan="2">
                                                                {{ $fmtPct($repeatImprovePct) }}
                                                                @if ($eligibleRegions)
                                                                    <span
                                                                        class="text-muted">({{ $improvedRegions }}
                                                                        / {{ $eligibleRegions }} regions
                                                                        improved)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 지역별 상세 결과 -->
                                    @if ($currentTest->metrics)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Detailed Results by Region</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter table-nowrap">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Region</th>
                                                                <th>TTFB</th>
                                                                <th>Load Time</th>
                                                                <th>Transfer Size</th>
                                                                <th>Resources</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $formatMetric = function (
                                                                    $first,
                                                                    $repeat,
                                                                    $unit = 'ms',
                                                                ) {
                                                                    if ($first === null) {
                                                                        return '<span class="text-muted">No Data</span>';
                                                                    }
                                                                    $firstFormatted = is_numeric($first)
                                                                        ? number_format($first, 1)
                                                                        : $first;
                                                                    $output = "<strong>{$firstFormatted}{$unit}</strong>";
                                                                    if ($repeat !== null) {
                                                                        $repeatFormatted = is_numeric($repeat)
                                                                            ? number_format($repeat, 1)
                                                                            : $repeat;
                                                                        $delta = $repeat - $first;
                                                                        $deltaFormatted =
                                                                            ($delta >= 0 ? '+' : '') .
                                                                            number_format($delta, 1);
                                                                        $deltaClass =
                                                                            $delta < 0
                                                                                ? 'text-success'
                                                                                : ($delta > 0
                                                                                    ? 'text-danger'
                                                                                    : 'text-muted');
                                                                        $output .= "<br><small>{$repeatFormatted}{$unit} <span class='{$deltaClass}'>({$deltaFormatted})</span></small>";
                                                                    }
                                                                    return $output;
                                                                };
                                                                $regionLabels = [
                                                                    'seoul' => 'Seoul',
                                                                    'tokyo' => 'Tokyo',
                                                                    'singapore' => 'Singapore',
                                                                    'virginia' => 'Virginia',
                                                                    'oregon' => 'Oregon',
                                                                    'frankfurt' => 'Frankfurt',
                                                                    'london' => 'London',
                                                                    'sydney' => 'Sydney',
                                                                ];
                                                            @endphp

                                                            @foreach ($regionLabels as $region => $label)
                                                                @php
                                                                    $metrics = $currentTest->getRegionMetrics($region);
                                                                    $hasData = $metrics !== null;
                                                                    $rowClass = $hasData ? '' : 'table-secondary';
                                                                @endphp
                                                                <tr class="{{ $rowClass }}">
                                                                    <td><strong>{{ $label }}</strong></td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.ttfb'), data_get($metrics, 'repeat.ttfb'), 'ms') !!}</td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.load'), data_get($metrics, 'repeat.load'), 'ms') !!}</td>
                                                                    <td>{!! $formatMetric(
                                                                        data_get($metrics, 'first.bytes') ? data_get($metrics, 'first.bytes') / 1024 : null,
                                                                        data_get($metrics, 'repeat.bytes') ? data_get($metrics, 'repeat.bytes') / 1024 : null,
                                                                        'KB',
                                                                    ) !!}</td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.resources'), data_get($metrics, 'repeat.resources'), '') !!}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>Display Format:</strong> <span class="fw-bold">First Visit</span>
                                        value → <span class="fw-bold">Repeat Visit</span> value (Δ difference)<br>
                                        <span class="text-success">Green = Improved (faster repeat visit)</span> |
                                        <span class="text-danger">Red = Degraded (slower repeat visit)</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <h6>Performance Metrics Explained</h6>
                                        <p class="mb-2"><strong>TTFB (Time To First Byte):</strong> Time from
                                            when the user sends a request until receiving the first response byte
                                            from the server</p>
                                        <p class="mb-2"><strong>Load Time:</strong> Time for all resources (HTML,
                                            CSS, JS, images) to be loaded and the page to be fully displayed</p>
                                        <p class="mb-0"><strong>Repeat Visit Performance:</strong> Faster loading
                                            speeds on repeat visits due to browser cache, Keep-Alive connections,
                                            and CDN caching</p>
                                    </div>

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why are repeat visits faster?</strong><br>
                                        - Browser Cache: Static resources like images, JS, and CSS are cached,
                                        eliminating the need to re-download.<br>
                                        - Keep-Alive & Session Reuse: Server connections are maintained, skipping
                                        handshake/SSL authentication processes.<br>
                                        - CDN Caching Effect: Resources are fetched from globally distributed CDN
                                        caches, reducing latency.<br>
                                        As a result, <span class="fw-bold">Repeat Visit Performance</span>
                                        typically shows much shorter loading times than first visits.
                                    </div>

                                    <div class="alert alert-secondary d-block">
                                        <strong>📌 Difference between TTFB and Load Time</strong><br>
                                        - <strong>TTFB (Time To First Byte)</strong>: Time from when a user sends a
                                        request until receiving the first response byte from the server.<br>
                                        - <strong>Load Time</strong>: Time for all resources (HTML, CSS, JS, images)
                                        to be loaded and the page to be fully displayed.<br><br>

                                        <strong>🌍 Network Round-Trip (RTT) Structure</strong><br>
                                        • TCP handshake + TLS handshake + actual data request/response → Minimum 3
                                        round trips required.<br>
                                        • Therefore, <u>regions physically farther from the origin server</u>
                                        accumulate more latency.<br><br>

                                        <strong>📊 Minimum Regional Latency</strong><br>
                                        - Same continent (e.g., Seoul→Tokyo/Singapore): TTFB of tens to ~200ms.<br>
                                        - Inter-continental (Seoul→US/Europe): Fiber optic round-trip alone adds
                                        150-250ms+.<br>
                                        - Including TLS/data requests: <u>minimum TTFB of 400-600ms+</u> can
                                        occur.<br>
                                        - Load Time can extend to several seconds depending on resource size and
                                        count, especially with many images/JS files <u>5+ seconds</u> is
                                        common.<br><br>

                                        In other words, <span class="fw-bold">regions physically farthest from
                                            origin (e.g., Korean server → US East/Europe)</span> will inevitably
                                        have <u>minimum TTFB of hundreds of ms</u> and <u>Load Time of 2-5+
                                            seconds</u> regardless of optimization.
                                        To reduce this, CDN, caching, and Edge server deployment are essential.
                                    </div>
                                @else
                                    <div class="alert alert-info d-block">
                                        <h5>No Results Yet</h5>
                                        <p class="mb-0">Run a test to see global performance results by region.
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Raw JSON Data</h5>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="copyJsonToClipboard()" title="Copy JSON data">
                                            Copy
                                        </button>
                                    </div>
                                    <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <div class="alert alert-info d-block">
                                        <h5>No Results Yet</h5>
                                        <p class="mb-0">Run a test to view Raw JSON data.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-2">
                {{-- 사이드바 (공통 컴포넌트) --}}
                <x-test-shared.sidebar :side-tab-active="$sideTabActive" :test-history="$testHistory" :selected-history-test="$selectedHistoryTest" :user-domains="$userDomains"
                    :scheduled-tests="$scheduledTests" :has-pro-or-agency-plan="$hasProOrAgencyPlan" />

                {{-- 도메인 인증 모달 (공통 컴포넌트) --}}
                <x-test-shared.domain-verification-modal :show-verification-modal="$showVerificationModal" :current-verification-domain="$currentVerificationDomain" :verification-message="$verificationMessage"
                    :verification-message-type="$verificationMessageType" />
            </div>
        </div>
    </div>
</div>
</div>

@section('js')
@include('components.test-shared.js')
<script>
    let progressInterval;
    let pollingInterval;

    function startProgressSimulation() {
        let progress = 0;

        progressInterval = setInterval(() => {
            if (progress < 95) {
                // 1-8% 사이 랜덤 증가
                const increment = Math.random() * 8 + 1;
                progress = Math.min(95, progress + increment);
                document.getElementById('progress-bar').style.width = progress + '%';
            }
        }, getRandomInterval());
    }

    function getRandomInterval() {
        return Math.random() * (1500 - 200) + 200;
    }

    function stopProgressSimulation() {
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }

        document.getElementById('progress-bar').style.width = '100%';
    }

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
            Livewire.dispatch('check-status');
        }, 2000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('auto-start-test', () => {
            setTimeout(() => {
                startProgressSimulation();
                startPolling();
                @this.call('runTest');
            }, 500);
        });

        Livewire.on('start-polling', () => {
            startPolling();
        });

        Livewire.on('stop-polling', () => {
            stopProgressSimulation();
            stopPolling();
        });
    });
</script>
@endsection
