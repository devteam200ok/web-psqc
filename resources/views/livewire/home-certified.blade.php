@section('title')
    @include('inc.component.seo')
@endsection
@section('css')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+KR:wght@400;500;700&family=Allura&display=swap"
        rel="stylesheet">
    @include('components.test-shared.css')

    <style>
        /* 서명: 테두리/배경 완전 제거 + 폰트 교체 */
        .signature-line {
            margin-top: 8px;
            padding-bottom: 10px;
        }

        .signature-line .label {
            font-weight: 600;
            margin-right: 10px;
        }

        .signature {
            font-family: 'Allura', cursive;
            font-size: 30px;
            line-height: 1;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block;
            vertical-align: baseline;
        }

        .sig-meta {
            font-size: 10.5px;
            color: #6b7280;
        }
    </style>
@endsection
@php
    // test_type에서 앞글자가 p 이면 performance, s이면 security, q이면 quality, c이면 content로 url_first
    $url_first = '';
    // 첫 글자 확인
    if (isset($test_type[0])) {
        if ($test_type[0] === 'p') {
            $url_first = 'performance';
        } elseif ($test_type[0] === 's') {
            $url_first = 'security';
        } elseif ($test_type[0] === 'q') {
            $url_first = 'quality';
        } elseif ($test_type[0] === 'c') {
            $url_first = 'content';
        }
    }

    // 3번째 글자부터는 끝까지는 url_second
    $url_second = '';
    if (isset($test_type[2])) {
        $url_second = substr($test_type, 2);
    }
@endphp
<div class="page page-center">
    <div class="container container-narrow py-4">
        @include('inc.component.message')
        <div class="row mt-3 my-3"> 
            <div class="col d-flex align-items-center">
                @php
                    $pdfRel = "certification/{$certificate->code}.pdf";
                @endphp
                @if (Storage::disk('local')->exists($pdfRel))
                    <a href="{{ route('cert.pdf.download', ['code' => $certificate->code]) }}"
                        class="btn btn-sm px-2 py-2 btn-secondary" target="_blank" rel="noopener">
                        Download Certificate
                    </a>
                @else
                    <button class="btn btn-sm px-2 py-2 btn-primary" wire:click="generateCertificatePdf">
                        Generate Certificate
                    </button>
                @endif
                <a href="{{ url('/') }}/{{ $url_first }}/{{ $url_second }}?url={{ $currentTest->url }}"
                    class="btn btn-sm px-2 py-2 btn-dark ms-auto" target="_blank" rel="noopener">
                    Verify Test Results
                </a>
            </div>
        </div>
        <div class="text-center mb-4">

            @if ($test_type == 'p-speed')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
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
                                            : 'No Data';
                                        $fmtPct = fn($v) => is_numeric($v) ? number_format($v, 1) . '%' : 'No Data';
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Performance Certificate
                                            </h1>
                                            <h2>(Global Speed Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Metric</th>
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
                                                                    <span class="text-muted">({{ $improvedRegions }}
                                                                        / {{ $eligibleRegions }} regions improved)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on web performance test results conducted through <strong>8 global regions measurement network</strong>.<br>
                                            All data was collected by <u>simulating real user environments</u>, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade based on measurements across major global regions,
                                                demonstrating <u>top 10% web quality performance</u>.<br>
                                                This shows that it is an excellent website with <strong>fast response times</strong> and <strong>global user-friendliness</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Regional Access Speed -->
                                    @if ($currentTest->metrics)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Regional Access Speed</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter table-nowrap">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Region</th>
                                                                <th>TTFB</th>
                                                                <th>Load Time</th>
                                                                <th>Transfer Size</th>
                                                                <th>Resource Count</th>
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

                                    <!-- Additional Information -->
                                    <div class="alert alert-info d-block">
                                        <strong>Display Format:</strong> <span class="fw-bold">First Visit</span> Value → <span
                                            class="fw-bold">Repeat Visit</span> Value (Δ Difference)<br>
                                        <span class="text-success">Green = Improvement (faster repeat visit)</span> | <span
                                            class="text-danger">Red
                                            = Degradation (slower repeat visit)</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>TTFB (Time To First Byte):</strong> Time taken from when the user sends a request 
                                            until receiving the first response byte from the server</p>
                                        <p class="mb-2"><strong>Load Time:</strong> Time taken for all resources (HTML, CSS, JS, images, etc.) 
                                            to load in the browser until the page is completely displayed</p>
                                        <p class="mb-0"><strong>Repeat Visit Performance:</strong> Faster loading speeds on repeat visits 
                                            due to browser cache, Keep-Alive connections, CDN caching, and other optimization effects</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Verification Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>8 Global Regions: Seoul, Tokyo, Sydney, Singapore, Frankfurt, Virginia, Oregon, London</h3>
                                <div class="text-muted small mt-1">
                                    Simulates actual global user access speeds through 8 regions distributed across 
                                    major internet hubs worldwide (Asia, North America, Europe, Oceania).
                                    <br><br>
                                    • Asia (Seoul, Tokyo, Singapore) → Covers East Asia & Southeast Asia<br>
                                    • Oceania (Sydney) → Australia and Pacific region<br>
                                    • North America (Virginia, Oregon) → East and West coast major hubs<br>
                                    • Europe (Frankfurt, London) → Western and Central Europe key hubs
                                    <br><br>
                                    These 8 regions are core hubs commonly operated by global infrastructure providers 
                                    like Cloudflare, AWS, and GCP, representing the majority of worldwide internet traffic.
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>Origin: TTFB ≤ 200ms, Load ≤ 1.5s<br>Global Average: TTFB ≤ 800ms, Load
                                                    ≤ 2.5s<br>All Regions: TTFB ≤ 1.5s, Load ≤ 3s<br>Repeat Visit Improvement: 80%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>Origin: TTFB ≤ 400ms, Load ≤ 2.5s<br>Global Average: TTFB ≤ 1.2s, Load ≤
                                                    3.5s<br>All Regions: TTFB ≤ 2s, Load ≤ 4s<br>Repeat Visit Improvement: 60%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Origin: TTFB ≤ 800ms, Load ≤ 3.5s<br>Global Average: TTFB ≤ 1.6s, Load ≤
                                                    4.5s<br>All Regions: TTFB ≤ 2.5s, Load ≤ 5.5s<br>Repeat Visit Improvement: 50%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Origin: TTFB ≤ 1.2s, Load ≤ 4.5s<br>Global Average: TTFB ≤ 2.0s, Load ≤
                                                    5.5s<br>All Regions: TTFB ≤ 3.0s, Load ≤ 6.5s<br>Repeat Visit Improvement: 37.5%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Origin: TTFB ≤ 1.6s, Load ≤ 6.0s<br>Global Average: TTFB ≤ 2.5s, Load ≤
                                                    7.0s<br>All Regions: TTFB ≤ 3.5s, Load ≤ 8.5s<br>Repeat Visit Improvement: 25%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Below the above criteria</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Difference between TTFB and Load Time</strong><br>
                                    - <strong>TTFB (Time To First Byte)</strong>: Time from when the user sends a request 
                                    until receiving the first response byte from the server.<br>
                                    - <strong>Load Time</strong>: Time for all resources (HTML, CSS, JS, images, etc.) 
                                    to load in the browser until the page is completely displayed.<br><br>

                                    <strong>🌍 Network Round-trip (RTT) Structure</strong><br>
                                    • TCP handshake 1x + TLS handshake 1x + actual data request/response 1x → minimum 3 round trips required.<br>
                                    • Therefore, <u>regions physically farther from the origin server</u> accumulate more latency.<br><br>

                                    <strong>📊 Minimum Latency by Region</strong><br>
                                    - Same continent (e.g., Seoul→Tokyo/Singapore): TTFB typically tens of ms ~ 200ms.<br>
                                    - Inter-continental (Seoul→US/Europe): Fiber optic round-trip delay alone is 150~250ms+.<br>
                                    - Including TLS/data requests, <u>minimum TTFB of 400~600ms+</u> can occur.<br>
                                    - Load Time can extend to several seconds depending on resource size and count, 
                                    especially with many images/JS files, <u>5+ seconds</u> is common.<br><br>

                                    Therefore, <span class="fw-bold">regions physically farthest from origin (e.g., Korean server → US East/Europe)</span>
                                    will inevitably have <u>minimum TTFB of hundreds of ms+</u> and <u>Load Time of 2-5+ seconds</u> 
                                    regardless of optimization. CDN, caching, and Edge server deployment are essential to reduce this.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'p-load')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
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

                                        $metrics = $currentTest->metrics ?? [];
                                        $config = $currentTest->test_config ?? [];

                                        $totalRequests = $metrics['http_reqs'] ?? 0;
                                        $failureRate = ($metrics['http_req_failed'] ?? 0) * 100;
                                        $p95Response = $metrics['http_req_duration_p95'] ?? 0;
                                        $avgResponse = $metrics['http_req_duration_avg'] ?? 0;
                                        $requestsPerSec = $metrics['http_reqs_rate'] ?? 0;
                                        $vus = $config['vus'] ?? 'N/A';
                                        $duration = $config['duration_seconds'] ?? 'N/A';

                                        $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Performance Certificate
                                            </h1>
                                            <h2>(K6 Load Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Metric</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Virtual Users × Duration</strong></td>
                                                            <td>{{ $vus }} VUs × {{ $duration }} seconds</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Total Requests</strong></td>
                                                            <td>{{ number_format($totalRequests) }}
                                                                ({{ number_format($requestsPerSec, 1) }} req/s)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>P95 Response Time</strong></td>
                                                            <td>{{ number_format($p95Response) }}ms</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Error Rate</strong></td>
                                                            <td
                                                                class="{{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
                                                                {{ number_format($failureRate, 2) }}%
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Load Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on web performance test results conducted through <strong>K6 Load Testing</strong>.<br>
                                            The test simulated real usage patterns with <strong>{{ $vus }} concurrent users</strong> for
                                            <strong>{{ $duration }} seconds</strong>, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on server environment and optimization status.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in load testing,
                                                demonstrating <u>high concurrent user handling capability</u>.<br>
                                                This shows that it is a website with <strong>stable service delivery</strong> and <strong>excellent server performance</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Performance Metrics -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Performance Metrics</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Response Time Analysis</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Average</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_avg'] ?? 0, 2) }}ms
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Median</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_med'] ?? 0, 2) }}ms
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>P90</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_p90'] ?? 0, 2) }}ms
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>P95</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_p95'] ?? 0, 2) }}ms
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Max</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_max'] ?? 0, 2) }}ms
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Data Transfer & Checks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Data Received</td>
                                                                    <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }}
                                                                        MB</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Data Sent</td>
                                                                    <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }}
                                                                        MB</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Iterations</td>
                                                                    <td>{{ $metrics['iterations'] ?? 0 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Checks Passed</td>
                                                                    <td>{{ $metrics['checks_passes'] ?? 0 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Checks Failed</td>
                                                                    <td>{{ $metrics['checks_fails'] ?? 0 }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-block">
                                        <strong>Display Format:</strong> Think Time
                                        {{ $config['think_time_min'] ?? 3 }}-{{ $config['think_time_max'] ?? 10 }} seconds
                                        applied<br>
                                        <span class="text-success">Error Rate < 1% = Excellent</span> | <span
                                            class="text-danger">Error Rate > 5% = Needs Improvement</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Virtual Users (VUs):</strong> Number of concurrent virtual users 
                                            that simulate actual traffic load</p>
                                        <p class="mb-2"><strong>P95 Response Time:</strong> Time within which 95% of all requests received responses 
                                            (key indicator of user experience)</p>
                                        <p class="mb-0"><strong>Think Time:</strong> Wait time that mimics real user navigation patterns between pages
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's K6 Load Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>K6 Load Testing Verification Environment</h3>
                                <div class="text-muted small mt-1">
                                    K6 is a modern load testing tool developed by Grafana that uses JavaScript to create test scenarios
                                    for verifying website or API performance and stability.
                                    <br><br>
                                    • <strong>Virtual Users (VUs)</strong>: Number of concurrent virtual users<br>
                                    • <strong>Duration</strong>: Test execution time<br>
                                    • <strong>Think Time</strong>: Wait time between requests (simulates real user behavior patterns)<br>
                                    • <strong>P95 Response Time</strong>: Time within which 95% of all requests received responses
                                    <br><br>
                                    Average response time can be skewed by some very fast requests, so P95 more accurately reflects actual user experience.
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>VU/Duration Requirements</th>
                                                <th>Performance Criteria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>100+ VUs + 60+ seconds</td>
                                                <td>P95 < 1000ms<br>Error Rate < 0.1%<br>Stability: P90 ≤ 200% of Average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>50+ VUs + 45+ seconds</td>
                                                <td>P95 < 1200ms<br>Error Rate < 0.5%<br>Stability: P90 ≤ 240% of Average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>30+ VUs + 30+ seconds</td>
                                                <td>P95 < 1500ms<br>Error Rate < 1.0%<br>Stability: P90 ≤ 280% of Average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>20+ VUs + 30+ seconds</td>
                                                <td>P95 < 2000ms<br>Error Rate < 2.0%<br>Stability: P90 ≤ 320% of Average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>10+ VUs + 30+ seconds</td>
                                                <td>P95 < 3000ms<br>Error Rate < 5.0%<br>Stability: P90 ≤ 400% of Average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>-</td>
                                                <td>Below the above criteria</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 K6 Load Testing Features</strong><br>
                                    - <strong>Real User Pattern Simulation</strong>: Natural user behavior reproduction through Think Time<br>
                                    - <strong>Concurrent Connection Load Testing</strong>: Real traffic situation simulation through VUs<br>
                                    - <strong>Comprehensive Performance Metrics Analysis</strong>: Multi-angle measurement of response time, error rate, throughput, etc.<br><br>

                                    <strong>🌍 Test Execution Environment</strong><br>
                                    • Test Region: {{ ucfirst($config['region'] ?? 'seoul') }}<br>
                                    • Virtual Users: {{ $vus }} VUs<br>
                                    • Duration: {{ $duration }} seconds<br>
                                    • Think Time:
                                    {{ $config['think_time_min'] ?? 3 }}-{{ $config['think_time_max'] ?? 10 }} seconds<br><br>

                                    <strong>📊 Performance Criteria Interpretation</strong><br>
                                    - P95 < 1s: Excellent user experience<br>
                                    - P95 < 2s: Good user experience<br>
                                    - P95 > 3s: Needs improvement<br>
                                    - Error Rate < 1%: Stable service<br>
                                    - Error Rate > 5%: Immediate improvement required
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'p-mobile')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $report = $currentTest->results;
                                        $overall = $report['overall'] ?? [];
                                        $results = $report['results'] ?? [];

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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Performance Certificate
                                            </h1>
                                            <h2>(Mobile Performance Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Metric</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Average Median Response Time</strong></td>
                                                            <td>{{ $overall['medianAvgMs'] ?? 0 }}ms</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Average Long Tasks</strong></td>
                                                            <td>{{ $overall['longTasksAvgMs'] ?? 0 }}ms</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>JS Runtime Errors (First-party/Third-party)</strong></td>
                                                            <td>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }} /
                                                                {{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Render Width Overflow</strong></td>
                                                            <td>{{ !empty($overall['bodyOverflowsViewport']) ? 'Present' : 'None' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Mobile Performance Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on mobile web performance test results conducted through <strong>Playwright headless browser</strong>.<br>
                                            Testing was performed on <strong>6 representative mobile devices</strong> (3 iOS, 3 Android) with CPU ×4 throttling environment
                                            to simulate actual mobile conditions, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on website optimization and device-specific adaptations.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in mobile performance testing,
                                                demonstrating <u>excellent mobile optimization level</u>.<br>
                                                This shows that it is a website with <strong>fast mobile rendering</strong> and <strong>stable runtime environment</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Results by Device -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Measurement Results by Device</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter table-nowrap">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Device</th>
                                                            <th>Median(ms)</th>
                                                            <th>TBT(ms)</th>
                                                            <th>JS Errors(First-party)</th>
                                                            <th>JS Errors(Third-party)</th>
                                                            <th>Render Width</th>
                                                            <th>Viewport</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($results as $result)
                                                            <tr>
                                                                <td><strong>{{ $result['device'] ?? 'Unknown' }}</strong>
                                                                </td>
                                                                <td>{{ $result['medianMs'] ?? 0 }}</td>
                                                                <td>{{ $result['longTasksTotalMs'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsFirstPartyCount'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsThirdPartyCount'] ?? 0 }}</td>
                                                                <td>{{ !empty($result['bodyOverflowsViewport']) ? 'Overflow' : 'Normal' }}
                                                                </td>
                                                                <td>
                                                                    @if (!empty($result['viewport']))
                                                                        {{ $result['viewport']['w'] ?? '?' }}×{{ $result['viewport']['h'] ?? '?' }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-block">
                                        <strong>Test Environment:</strong> 4 runs per device (excluding 1 warmup run, using median of 3 runs)<br>
                                        <span class="text-success">No JS errors = Excellent</span> | <span class="text-danger">Render
                                            width overflow = Responsive design improvement needed</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Median Response Time:</strong> Median time taken for page loading on repeat visits (cache utilized state)</p>
                                        <p class="mb-2"><strong>TBT (Total Blocking Time):</strong> Total main thread blocking time 
                                            due to JavaScript execution (excess over 50ms)</p>
                                        <p class="mb-0"><strong>Render Width Overflow:</strong> Whether the body element exceeds viewport width causing horizontal scrolling</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Mobile Performance Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Mobile Performance Test Verification Environment</h3>
                                <div class="text-muted small mt-1">
                                    Using Playwright to simulate actual mobile device environments and precisely measure
                                    website mobile performance and stability.
                                    <br><br>
                                    • <strong>Test Devices</strong>: 3 iOS devices (iPhone SE, 11, 15 Pro), 3 Android devices (Galaxy S9+,
                                    S20 Ultra, Pixel 5)<br>
                                    • <strong>Measurement Method</strong>: 4 runs per device, excluding 1 warmup run, using median of 3 runs<br>
                                    • <strong>CPU Throttling</strong>: ×4 applied to simulate actual mobile performance constraints<br>
                                    • <strong>Key Metrics</strong>: Repeat visit load time, Long Tasks(TBT), JS runtime errors, render width overflow
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Score</th>
                                                <th>Performance Criteria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Median Response Time: ≤ 800ms<br>JS Runtime Errors: 0<br>Render Width Overflow: None</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>Median Response Time: ≤ 1200ms<br>JS Runtime Errors: ≤ 1<br>Render Width Overflow: None</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Median Response Time: ≤ 2000ms<br>JS Runtime Errors: ≤ 2<br>Render Width Overflow: Allowed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Median Response Time: ≤ 3000ms<br>JS Runtime Errors: ≤ 3<br>Render Width Overflow: Frequent</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Median Response Time: ≤ 4000ms<br>JS Runtime Errors: ≤ 5<br>Render Width Overflow: Severe</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Below the above criteria</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Playwright Testing Features</strong><br>
                                    - <strong>Microsoft Development</strong>: Modern web automation tool enabling accurate performance measurement<br>
                                    - <strong>Headless Execution</strong>: Stable operation in background without UI<br>
                                    - <strong>CPU Throttling</strong>: Precisely simulates actual mobile environment performance constraints<br><br>

                                    <strong>🌍 Measurement Metrics Interpretation</strong><br>
                                    • <strong>Older devices performing faster</strong>: Lighter assets may be served for smaller viewports<br>
                                    • <strong>Uniform CPU throttling</strong>: ×4 applied to all devices, so resource weight directly affects speed<br>
                                    • <strong>JS error categorization</strong>: Separate counting of first-party (test domain) and third-party errors<br><br>

                                    <strong>📊 Why This Test Matters</strong><br>
                                    - Accurately measures mobile perceived rendering performance<br>
                                    - Identifies runtime stability and error responsibility<br>
                                    - Automatically verifies responsive design compatibility<br>
                                    - Optimizes regression comparison and target management between releases
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 's-ssl')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results;
                                        $grade = $currentTest->overall_grade ?? 'N/A';
                                        $gradeClass = match ($grade) {
                                            'A+' => 'badge bg-green-lt text-green-lt-fg',
                                            'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                            'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                            'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                            'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                            'F' => 'badge bg-red-lt text-red-lt-fg',
                                            default => 'badge bg-secondary',
                                        };

                                        $metrics = $currentTest->metrics ?? [];
                                        $tlsVersion = $metrics['tls_version'] ?? 'N/A';
                                        $forwardSecrecy = $metrics['forward_secrecy'] ?? false;
                                        $hstsEnabled = $metrics['hsts_enabled'] ?? false;

                                        $vulnerableCount = 0;
                                        if (isset($results['vulnerabilities'])) {
                                            foreach ($results['vulnerabilities'] as $status) {
                                                if ($status['vulnerable'] ?? false) {
                                                    $vulnerableCount++;
                                                }
                                            }
                                        }

                                        $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Security Certificate
                                            </h1>
                                            <h2>(SSL/TLS Security Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Highest TLS Version</strong></td>
                                                            <td>{{ $tlsVersion }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Perfect Forward Secrecy (PFS)</strong></td>
                                                            <td
                                                                class="{{ $forwardSecrecy ? 'text-success' : 'text-danger' }}">
                                                                {{ $forwardSecrecy ? 'Supported' : 'Not Supported' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ $hstsEnabled ? 'text-success' : 'text-warning' }}">
                                                                {{ $hstsEnabled ? 'Enabled' : 'Disabled' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Vulnerabilities</strong></td>
                                                            <td
                                                                class="{{ $vulnerableCount > 0 ? 'text-danger' : 'text-success' }}">
                                                                {{ $vulnerableCount > 0 ? $vulnerableCount . ' Found' : 'None' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS Security Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on SSL/TLS security test results conducted through <strong>testssl.sh</strong>.<br>
                                            Comprehensive testing of server SSL/TLS configuration, supported protocols, cipher suites, and known vulnerabilities
                                            was performed, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration changes and security updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in SSL/TLS security testing,
                                                demonstrating <u>highest level security configuration</u>.<br>
                                                This shows that it is a website with <strong>secure encrypted communication</strong> and <strong>compliance with latest security standards</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Security Information -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Security Information</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Certificate Information</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Issuer</td>
                                                                    <td>{{ $results['certificate']['issuer'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Validity Period</td>
                                                                    <td>{{ $results['cert_expiry'] ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Signature Algorithm</td>
                                                                    <td>{{ $results['certificate']['signature_algorithm'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Key Size</td>
                                                                    <td>{{ $results['certificate']['key_size'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Protocol Support</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (isset($results['supported_protocols']) && count($results['supported_protocols']) > 0)
                                                                    <tr>
                                                                        <td>Supported Protocols</td>
                                                                        <td>{{ implode(', ', $results['supported_protocols']) }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if (isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0)
                                                                    <tr>
                                                                        <td>Vulnerable Protocols</td>
                                                                        <td class="text-danger">
                                                                            {{ implode(', ', $results['vulnerable_protocols']) }}
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td>Vulnerable Protocols</td>
                                                                        <td class="text-success">None</td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <td>IP Address</td>
                                                                    <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Port</td>
                                                                    <td>{{ $results['port'] ?? '443' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vulnerability Summary -->
                                    @if ($vulnerableCount > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Identified Vulnerabilities</h4>
                                                <div class="alert alert-warning">
                                                    @php
                                                        $vulnList = [];
                                                        foreach ($results['vulnerabilities'] as $vuln => $status) {
                                                            if ($status['vulnerable'] ?? false) {
                                                                $vulnList[] = strtoupper(
                                                                    str_replace(['_', '-'], ' ', $vuln),
                                                                );
                                                            }
                                                        }
                                                    @endphp
                                                    <strong>{{ $vulnerableCount }} vulnerabilities found:</strong>
                                                    {{ implode(', ', $vulnList) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Highest level security configuration (compliant with all latest standards)
                                        @elseif ($grade === 'A')
                                            Excellent security configuration (compliant with most standards)
                                        @elseif ($grade === 'B')
                                            Good security configuration (some improvements needed)
                                        @else
                                            Security configuration improvements needed
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>testssl.sh:</strong> Open source SSL/TLS tester with 10,000+ GitHub stars, industry standard tool</p>
                                        <p class="mb-2"><strong>Perfect Forward Secrecy (PFS):</strong> Security feature that prevents past communications from being decrypted in the future</p>
                                        <p class="mb-0"><strong>HSTS:</strong> HTTP Strict Transport Security header that enforces HTTPS connections</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's SSL/TLS Security Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>SSL/TLS Security Test Verification Environment</h3>
                                <div class="text-muted small mt-1">
                                    testssl.sh is an open source tool that comprehensively tests SSL/TLS configuration,
                                    precisely analyzing website HTTPS security settings.
                                    <br><br>
                                    • <strong>Testing Tool</strong>: testssl.sh (GitHub 10,000+ stars open source project)<br>
                                    • <strong>Test Coverage</strong>: SSL/TLS protocols, cipher suites, certificates, known vulnerabilities<br>
                                    • <strong>Vulnerability Testing</strong>: Major vulnerabilities including Heartbleed, POODLE, BEAST, CRIME, FREAK<br>
                                    • <strong>Security Features</strong>: Support status for latest security features like PFS, HSTS, OCSP Stapling
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Score</th>
                                                <th>Security Criteria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Only latest TLS used, no vulnerabilities<br>Strong cipher suites applied<br>Certificate and chain completely normal<br>Excellent security settings including HSTS</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.2/1.3 support, legacy versions blocked<br>No major vulnerabilities<br>Some weak ciphers or configuration gaps possible<br>Generally safe level</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Mainly secure protocols<br>Some weak cipher suites present<br>Multiple warnings (WEAK)<br>Improvements needed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Some legacy TLS versions active<br>High usage of vulnerable encryption<br>Certificate expiry approaching/simple DV<br>Few vulnerabilities found</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>SSLv3/TLS 1.0 allowed<br>Many weak ciphers active<br>Certificate chain errors/expiry approaching<br>Multiple vulnerabilities present</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Fundamental SSL/TLS configuration flaws<br>Vulnerable protocols fully allowed<br>Certificate expired/self-signed<br>Multiple FAIL/VULNERABLE findings</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Key Testing Areas</strong><br>
                                    - <strong>SSL/TLS Protocols</strong>: Test support for SSL 2.0/3.0, TLS 1.0~1.3<br>
                                    - <strong>Cipher Suites</strong>: Supported algorithms, PFS, weak encryption detection<br>
                                    - <strong>SSL Certificates</strong>: Validity, expiration, chain integrity, OCSP Stapling<br>
                                    - <strong>Security Vulnerabilities</strong>: Heartbleed, POODLE, BEAST, CRIME, FREAK, etc.<br><br>

                                    <strong>🌍 Why SSL/TLS Testing Matters</strong><br>
                                    • <strong>Data Protection</strong>: Ensures encryption quality of all data transmitted between users and servers<br>
                                    • <strong>Trust Building</strong>: Provides secure HTTPS connections without browser warnings<br>
                                    • <strong>Compliance</strong>: Meets security regulation requirements like GDPR, PCI-DSS<br>
                                    • <strong>SEO Enhancement</strong>: Search engines favor HTTPS sites<br><br>

                                    <strong>📊 Security Improvement Recommendations</strong><br>
                                    - Complete deactivation of legacy protocols (SSL 2.0/3.0, TLS 1.0/1.1)<br>
                                    - Use strong cipher suites (AES-GCM, ChaCha20-Poly1305)<br>
                                    - Enable security features like HSTS, OCSP Stapling<br>
                                    - Regular security updates and certificate management
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw testssl.sh Output</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="bg-dark text-light p-3 rounded json-dump" id="json-data"
                                    style="max-height: 600px; overflow-y: auto; font-size: 11px; line-height: 1.2;">{{ $currentTest->results['raw_output'] ?? 'No Data' }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 's-sslyze')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results;
                                        $analysis = $results['analysis'] ?? [];
                                        $issues = $results['issues'] ?? [];
                                        $recommendations = $results['recommendations'] ?? [];

                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;

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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Security Certificate
                                            </h1>
                                            <h2>(SSL/TLS Deep Analysis)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>TLS Version</strong></td>
                                                            <td>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)
                                                                    TLS 1.3 Supported
                                                                @elseif ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                                                    TLS 1.2 (1.3 Not Supported)
                                                                @else
                                                                    Legacy Versions Only
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>PFS Ratio</strong></td>
                                                            <td>{{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}%
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>OCSP Stapling</strong></td>
                                                            <td
                                                                class="{{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'text-success' : 'text-danger' }}">
                                                                {{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'Enabled' : 'Disabled' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ !empty($analysis['http_headers']['hsts']) ? 'text-success' : 'text-danger' }}">
                                                                {{ !empty($analysis['http_headers']['hsts']) ? 'Configured' : 'Not Configured' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS Deep Analysis Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on SSL/TLS deep security analysis results conducted through <strong>SSLyze v5.x</strong>.<br>
                                            Comprehensive testing of TLS protocol versions, cipher suite strength, certificate chains, OCSP Stapling, HTTP security headers, and more
                                            was performed, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration changes and security updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in SSL/TLS deep analysis,
                                                demonstrating <u>highest level encryption security</u>.<br>
                                                This shows that it is a website with <strong>latest TLS protocols</strong> and <strong>strong cipher suite configuration</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Analysis Results -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Analysis Results</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Cipher Suite Analysis</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['cipher_suites']['tls_1_2']))
                                                                    <tr>
                                                                        <td>TLS 1.2 Cipher Suites</td>
                                                                        <td>{{ $analysis['cipher_suites']['tls_1_2']['total'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Strong Ciphers</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['strong'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Weak Ciphers</td>
                                                                        <td
                                                                            class="{{ ($analysis['cipher_suites']['tls_1_2']['weak'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['weak'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if (!empty($analysis['cipher_suites']['tls_1_3']))
                                                                    <tr>
                                                                        <td>TLS 1.3 Cipher Suites</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_3']['total'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Certificate Information</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['certificate']['details']))
                                                                    <tr>
                                                                        <td>Key Algorithm</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_algorithm'] ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Key Size</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_size'] ?? 'N/A' }} bits
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Signature Algorithm</td>
                                                                        <td>{{ $analysis['certificate']['details']['signature_algorithm'] ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Days to Expiry</td>
                                                                        <td
                                                                            class="{{ ($analysis['certificate']['details']['days_to_expiry'] ?? 31) <= 30 ? 'text-warning' : '' }}">
                                                                            {{ $analysis['certificate']['details']['days_to_expiry'] ?? 'N/A' }} days
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Identified Issues -->
                                    @if (!empty($issues))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Identified Security Issues</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($issues) }} issues found:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach (array_slice($issues, 0, 5) as $issue)
                                                            <li>{{ $issue }}</li>
                                                        @endforeach
                                                        @if (count($issues) > 5)
                                                            <li>Plus {{ count($issues) - 5 }} more...</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Highest level SSL/TLS security configuration (TLS 1.3, strong cipher suites, perfect security headers)
                                        @elseif ($grade === 'A')
                                            Excellent SSL/TLS security configuration (TLS 1.2+, mostly strong cipher suites)
                                        @elseif ($grade === 'B')
                                            Good SSL/TLS security configuration (some improvements needed)
                                        @else
                                            SSL/TLS security configuration improvements needed
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>SSLyze:</strong> Open source SSL/TLS scanner recommended by Mozilla, Qualys, and IETF</p>
                                        <p class="mb-2"><strong>PFS:</strong> Perfect Forward Secrecy - prevents future decryption of past communications</p>
                                        <p class="mb-0"><strong>OCSP Stapling:</strong> Mechanism to efficiently verify certificate revocation status</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's SSLyze Deep Analysis.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>SSLyze SSL/TLS Deep Analysis Verification Environment</h3>
                                <div class="text-muted small mt-1">
                                    SSLyze v5.x is an open source SSL/TLS scanner recommended by Mozilla, Qualys, IETF, and others,
                                    providing comprehensive diagnosis of website SSL/TLS configuration.
                                    <br><br>
                                    • <strong>Testing Tool</strong>: SSLyze v5.x - Industry standard SSL/TLS analysis tool<br>
                                    • <strong>TLS Protocols</strong>: SSL 2.0/3.0, TLS 1.0/1.1/1.2/1.3 support verification<br>
                                    • <strong>Cipher Suite Analysis</strong>: Strength, PFS support, weak cipher detection<br>
                                    • <strong>Certificate Chain</strong>: Validity, expiration, signature algorithm, key size<br>
                                    • <strong>Security Features</strong>: OCSP Stapling, HSTS, elliptic curve cryptography
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>Only TLS 1.3/1.2 allowed, no weak ciphers (all PFS)<br>
                                                    Certificate ECDSA or RSA≥3072, complete chain, 60+ days to expiry<br>
                                                    OCSP Stapling working (Must-Staple if available)<br>
                                                    HSTS enabled, max-age ≥ 1 year, includeSubDomains, preload</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.3/1.2, strong ciphers prioritized (mostly PFS)<br>
                                                    Certificate RSA≥2048, SHA-256+, normal chain, 30+ days to expiry<br>
                                                    OCSP Stapling enabled (occasional failures allowed)<br>
                                                    HSTS enabled, max-age ≥ 6 months</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>65~79</td>
                                                <td>TLS 1.2 required, 1.3 optional/unsupported, some CBC present<br>
                                                    Certificate RSA≥2048, normal chain (14+ days to expiry)<br>
                                                    OCSP Stapling disabled (but OCSP response available)<br>
                                                    HSTS configured but some deficiencies</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>50~64</td>
                                                <td>TLS 1.0/1.1 enabled or many weak ciphers (low PFS)<br>
                                                    Chain missing/weak signatures (SHA-1) or expiry approaching (≤14 days)<br>
                                                    No stapling, unclear revocation verification<br>
                                                    HSTS not configured</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>35~49</td>
                                                <td>Legacy protocols/ciphers (SSLv3/EXPORT/RC4 etc.) allowed<br>
                                                    Certificate mismatch/chain errors frequent<br>
                                                    Stapling failed, revocation verification impossible<br>
                                                    Security headers generally inadequate</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~34</td>
                                                <td>Handshake failure level defects<br>
                                                    Expired/self-signed/hostname mismatch<br>
                                                    Widespread weak protocol/cipher allowance<br>
                                                    Overall TLS configuration breakdown</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 SSLyze Deep Analysis Features</strong><br>
                                    - <strong>Comprehensive Testing</strong>: Complete analysis of TLS protocols, cipher suites, certificates, security headers<br>
                                    - <strong>Precise Diagnosis</strong>: Individual assessment of each cipher suite's strength and PFS support<br>
                                    - <strong>Real-time Verification</strong>: Live verification of OCSP Stapling and certificate chain<br>
                                    - <strong>Elliptic Curve Analysis</strong>: Evaluation of supported elliptic curves list and strength<br><br>

                                    <strong>🌍 Why SSLyze Deep Analysis Matters</strong><br>
                                    • <strong>Detailed Security Diagnosis</strong>: Identifies specific vulnerabilities beyond simple grading<br>
                                    • <strong>Latest Standards Compliance</strong>: Verifies latest security requirements including TLS 1.3 support<br>
                                    • <strong>Performance Optimization</strong>: Improves handshake performance by removing unnecessary weak ciphers<br>
                                    • <strong>Compliance Verification</strong>: Confirms meeting regulatory requirements like PCI-DSS, HIPAA<br><br>

                                    <strong>📊 Security Improvement Recommendations</strong><br>
                                    - Enable TLS 1.3 and completely disable TLS 1.0/1.1<br>
                                    - Use only PFS-supporting ECDHE/DHE cipher suites<br>
                                    - Use RSA minimum 3072-bit or ECDSA 256-bit certificates<br>
                                    - Mandatory configuration of OCSP Stapling and HSTS headers
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results['raw_json'] ?? $currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 's-header')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $report = $currentTest->results;
                                        $metrics = $currentTest->metrics;
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;

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

                                        // 헤더 상태 분석
                                        $csp = $metrics['headers']['csp'] ?? [];
                                        $hsts = $metrics['headers']['hsts'] ?? [];

                                        $presentHeaders = 0;
                                        foreach ($metrics['breakdown'] ?? [] as $header) {
                                            if (!empty($header['value'])) {
                                                $presentHeaders++;
                                            }
                                        }
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Security Certificate
                                            </h1>
                                            <h2>(Security Headers Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Applied Headers</strong></td>
                                                            <td>{{ $presentHeaders }}/6 headers</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>CSP</strong></td>
                                                            <td
                                                                class="{{ $csp['present'] ?? false ? ($csp['strong'] ?? false ? 'text-success' : 'text-warning') : 'text-danger' }}">
                                                                @if ($csp['present'] ?? false)
                                                                    {{ $csp['strong'] ?? false ? 'Strong' : 'Weak' }}
                                                                @else
                                                                    None
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ $hsts['present'] ?? false ? 'text-success' : 'text-danger' }}">
                                                                @if ($hsts['present'] ?? false)
                                                                    Configured
                                                                    ({{ number_format(($hsts['max_age'] ?? 0) / 86400) }} days)
                                                                @else
                                                                    None
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>X-Frame-Options</strong></td>
                                                            <td>
                                                                @php
                                                                    $xfo = '';
                                                                    foreach ($metrics['breakdown'] ?? [] as $header) {
                                                                        if ($header['key'] === 'X-Frame-Options') {
                                                                            $xfo = $header['value'] ?? 'None';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ $xfo ?: 'None' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Security Headers Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on web security test results conducted through <strong>6 Core Security Headers</strong> comprehensive testing.<br>
                                            Major HTTP security headers including CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy,
                                            Permissions-Policy, and HSTS were tested and measured, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration changes.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in security headers testing,
                                                demonstrating <u>excellent web security configuration</u>.<br>
                                                This shows that it is a website with <strong>strong defense systems</strong> against major web vulnerabilities including <strong>XSS, clickjacking, and MIME sniffing</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Header Score Details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Header Score Analysis</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Header</th>
                                                            <th>Value</th>
                                                            <th>Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($metrics['breakdown'] ?? [] as $item)
                                                            <tr>
                                                                <td><strong>{{ $item['key'] }}</strong></td>
                                                                <td class="text-truncate" style="max-width: 400px;"
                                                                    title="{{ $item['value'] ?? '(Not set)' }}">
                                                                    {{ $item['value'] ?? '(Not set)' }}
                                                                </td>
                                                                <td>{{ round((($item['score'] ?? 0) * 100) / 60, 1) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grade Reasoning -->
                                    @if (!empty($report['reasons']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <strong>Grade Assessment Reasoning:</strong><br>
                                                    {{ implode(' · ', $report['reasons']) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Highest level security headers configuration (all headers applied including strong CSP)
                                        @elseif ($grade === 'A')
                                            Excellent security headers configuration (most headers applied)
                                        @elseif ($grade === 'B')
                                            Good security headers configuration (core headers applied)
                                        @else
                                            Security headers configuration improvements needed
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>CSP:</strong> The most powerful security mechanism defending against XSS attacks and data injection attacks</p>
                                        <p class="mb-2"><strong>X-Frame-Options:</strong> Blocks iframe embedding to prevent clickjacking attacks</p>
                                        <p class="mb-0"><strong>HSTS:</strong> Enforces HTTPS connections to prevent man-in-the-middle attacks and protocol downgrade</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Security Headers Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>6 Core Security Headers Comprehensive Testing</h3>
                                <div class="text-muted small mt-1">
                                    Activates browser security features through HTTP response headers to protect web applications from various attacks.
                                    <br><br>
                                    • <strong>Content-Security-Policy (CSP)</strong>: Restricts resource loading sources, prevents XSS and third-party script exploitation<br>
                                    • <strong>X-Frame-Options</strong>: Blocks iframe embedding, prevents clickjacking and phishing overlays<br>
                                    • <strong>X-Content-Type-Options</strong>: Blocks MIME sniffing, defends against incorrect execution vulnerabilities<br>
                                    • <strong>Referrer-Policy</strong>: Minimizes URL information when sending to external sites, prevents personal info and internal path exposure<br>
                                    • <strong>Permissions-Policy</strong>: Restricts browser features like location, microphone, camera, protects privacy<br>
                                    • <strong>Strict-Transport-Security (HSTS)</strong>: Enforces HTTPS, prevents man-in-the-middle attacks and downgrade
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>95-100</td>
                                                <td>Strong CSP (nonce/hash/strict-dynamic, no unsafe-*)<br>
                                                    XFO: DENY/SAMEORIGIN or frame-ancestors restriction<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: strict-origin-when-cross-origin or better<br>
                                                    Permissions-Policy: blocks unnecessary features<br>
                                                    HSTS: 6+ months + subdomains</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>85-94</td>
                                                <td>CSP present (weak allowed) or excellent non-CSP 5 items<br>
                                                    XFO applied (or frame-ancestors restriction)<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: recommended values used<br>
                                                    Permissions-Policy: basic restrictions applied<br>
                                                    HSTS: 6+ months</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70-84</td>
                                                <td>No/weak CSP<br>
                                                    XFO properly applied<br>
                                                    X-Content-Type: present<br>
                                                    Referrer-Policy: good/moderate<br>
                                                    Permissions-Policy: some restrictions<br>
                                                    HSTS: short-term or no subdomains</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>55-69</td>
                                                <td>Only some headers present<br>
                                                    No/weak CSP<br>
                                                    Weak Referrer-Policy<br>
                                                    Missing X-Content-Type<br>
                                                    No or very short HSTS</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>40-54</td>
                                                <td>Only 1-2 core headers<br>
                                                    No CSP<br>
                                                    Weak/no Referrer<br>
                                                    Multiple missing headers</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0-39</td>
                                                <td>Nearly no security headers<br>
                                                    No CSP/XFO/X-Content<br>
                                                    No Referrer-Policy<br>
                                                    No HSTS</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Importance of Security Headers</strong><br>
                                    - <strong>XSS Defense</strong>: CSP blocks script injection attacks at the source<br>
                                    - <strong>Clickjacking Prevention</strong>: X-Frame-Options blocks malicious iframe embedding<br>
                                    - <strong>MIME Sniffing Defense</strong>: X-Content-Type-Options prevents file type spoofing<br>
                                    - <strong>Information Leak Prevention</strong>: Referrer-Policy protects sensitive URL information<br><br>

                                    <strong>🌍 Configuration Locations</strong><br>
                                    • <strong>CDN Level</strong>: Configure in Cloudflare, CloudFront, etc.<br>
                                    • <strong>Web Server Level</strong>: Nginx, Apache configuration files<br>
                                    • <strong>Application Level</strong>: Laravel, Express.js middleware<br><br>

                                    <strong>📊 Grading Policy</strong><br>
                                    - A+ grade requires strong CSP<br>
                                    - A grade possible without CSP if other 5 headers are excellent<br>
                                    - Strongest security effect when all headers applied together
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 's-scan')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                                        $technologies = $currentTest->results['technologies'] ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;

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

                                        $totalVulns =
                                            ($vulnerabilities['critical'] ?? 0) +
                                            ($vulnerabilities['high'] ?? 0) +
                                            ($vulnerabilities['medium'] ?? 0) +
                                            ($vulnerabilities['low'] ?? 0) +
                                            ($vulnerabilities['informational'] ?? 0);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Security Certificate
                                            </h1>
                                            <h2>(Security Vulnerability Scan)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Severity</th>
                                                            <th>Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['critical'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['high'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $vulnerabilities['medium'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($vulnerabilities['low'] ?? 0) + ($vulnerabilities['informational'] ?? 0) }} issues
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Security Vulnerability Scan Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on web security vulnerability analysis results conducted through <strong>OWASP ZAP</strong> passive scanning.<br>
                                            Non-intrusive testing of security headers, sensitive information exposure, session management, and potential vulnerabilities
                                            was performed through HTTP response analysis, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on website updates and security patches.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in security vulnerability scanning,
                                                demonstrating <u>excellent security level</u>.<br>
                                                This shows that it is a website with <strong>no major security vulnerabilities</strong> and <strong>secure configuration</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vulnerability Summary -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Vulnerability Analysis Results</h4>
                                            <div class="row g-2">
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-danger">
                                                                {{ $vulnerabilities['critical'] ?? 0 }}</div>
                                                            <div class="text-muted">Critical</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-warning">
                                                                {{ $vulnerabilities['high'] ?? 0 }}</div>
                                                            <div class="text-muted">High</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-info">
                                                                {{ $vulnerabilities['medium'] ?? 0 }}</div>
                                                            <div class="text-muted">Medium</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold">
                                                                {{ $vulnerabilities['low'] ?? 0 }}</div>
                                                            <div class="text-muted">Low</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-muted">
                                                                {{ $vulnerabilities['informational'] ?? 0 }}</div>
                                                            <div class="text-muted">Info</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Key Findings -->
                                    @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Key Findings</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($vulnerabilities['details']) }} security issues
                                                        identified.</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach (array_slice($vulnerabilities['details'], 0, 5) as $vuln)
                                                            <li>
                                                                <strong>{{ $vuln['name'] }}</strong>
                                                                <span
                                                                    class="badge {{ match ($vuln['risk']) {
                                                                        'critical' => 'bg-red-lt text-red-lt-fg',
                                                                        'high' => 'bg-orange-lt text-orange-lt-fg',
                                                                        'medium' => 'bg-yellow-lt text-yellow-lt-fg',
                                                                        'low' => 'bg-blue-lt text-blue-lt-fg',
                                                                        default => 'bg-azure-lt text-azure-lt-fg',
                                                                    } }}">{{ ucfirst($vuln['risk']) }}</span>
                                                            </li>
                                                        @endforeach
                                                        @if (count($vulnerabilities['details']) > 5)
                                                            <li>Plus {{ count($vulnerabilities['details']) - 5 }} more...
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Detected Technologies -->
                                    @if (isset($technologies) && count($technologies) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Detected Technology Stack</h4>
                                                <div>
                                                    @foreach (array_slice($technologies, 0, 10) as $tech)
                                                        <span
                                                            class="badge bg-azure-lt text-azure-lt-fg me-1 mb-1">{{ $tech['name'] }}</span>
                                                    @endforeach
                                                    @if (count($technologies) > 10)
                                                        <span
                                                            class="badge bg-secondary me-1 mb-1">+{{ count($technologies) - 10 }} more</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Highest security level (no Critical/High vulnerabilities, complete security headers)
                                        @elseif ($grade === 'A')
                                            Excellent security (no Critical, minimal High, good security configuration)
                                        @elseif ($grade === 'B')
                                            Good security (some improvements needed)
                                        @else
                                            Security improvements needed
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>OWASP ZAP:</strong> The world's most widely used open source web security testing tool</p>
                                        <p class="mb-2"><strong>Passive Scan:</strong> Non-intrusive testing that analyzes only HTTP responses without actual attacks</p>
                                        <p class="mb-0"><strong>Scan Coverage:</strong> Security headers, sensitive information exposure, session management, technology stack detection</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's OWASP ZAP Security Scan.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>OWASP ZAP Passive Scan - Non-intrusive Security Vulnerability Analysis</h3>
                                <div class="text-muted small mt-1">
                                    OWASP ZAP (Zed Attack Proxy) is the world's most widely used open source web application security testing tool.
                                    <br><br>
                                    • <strong>Testing Tool</strong>: OWASP ZAP - Industry standard web security testing tool<br>
                                    • <strong>Test Method</strong>: Passive scan (analyzes only HTTP responses without actual attacks)<br>
                                    • <strong>Scan Items</strong>: Security headers, sensitive information exposure, session management, potential injection points<br>
                                    • <strong>Technology Stack Detection</strong>: Identification of servers, frameworks, and libraries in use<br>
                                    • <strong>Duration</strong>: Approximately 10-20 seconds
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>0 High/Medium vulnerabilities<br>Complete security headers (HTTPS, HSTS, X-Frame-Options, etc.)<br>No sensitive information exposure<br>Minimized server/framework version information</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>0 High, ≤1 Medium<br>Most security headers implemented<br>No sensitive information exposure<br>Minor information disclosure present</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>≤1 High, ≤2 Medium<br>Some security headers missing<br>Session cookie Secure/HttpOnly missing<br>Minor internal identifier exposure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>≥2 High or ≥3 Medium<br>Major security headers absent<br>Sensitive parameters/tokens exposed<br>Vulnerable session management</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>≥1 Critical or ≥3 High<br>Serious authentication/session attributes missing<br>Debug/development information exposure<br>Public admin console/config file exposure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Widespread High vulnerabilities<br>HTTPS not implemented or completely compromised<br>Sensitive data transmitted/exposed in plain text<br>General absence of security headers and session controls</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 OWASP ZAP Passive Scan Features</strong><br>
                                    - <strong>Non-intrusive Testing</strong>: Analyzes only HTTP responses without actual attacks<br>
                                    - <strong>Fast Scanning</strong>: Identifies major vulnerabilities within 10-20 seconds<br>
                                    - <strong>Safe Testing</strong>: Assesses security level without service impact<br>
                                    - <strong>Comprehensive Analysis</strong>: Multi-angle testing including security headers, sessions, information exposure<br><br>

                                    <strong>🌍 Vulnerability Risk Classification</strong><br>
                                    • <strong>Critical</strong>: Immediate action required (SQL Injection, XSS, RCE)<br>
                                    • <strong>High</strong>: Fast remediation needed (Session management vulnerabilities, CSRF)<br>
                                    • <strong>Medium</strong>: Improvement recommended (Missing security headers)<br>
                                    • <strong>Low</strong>: Low risk (Information disclosure, configuration issues)<br>
                                    • <strong>Info</strong>: Reference information<br><br>

                                    <strong>📊 Security Improvement Recommendations</strong><br>
                                    - Configure security headers (HSTS, X-Frame-Options, X-Content-Type-Options)<br>
                                    - Set Secure, HttpOnly, SameSite attributes on cookies<br>
                                    - Block server version, debug message exposure<br>
                                    - Run regular security scans at least monthly
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 's-nuclei')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Results Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                                        $templateDetails = $currentTest->results['template_details'] ?? [];
                                        $metrics = $currentTest->metrics ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;

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

                                        $totalVulns =
                                            ($metrics['vulnerability_counts']['critical'] ?? 0) +
                                            ($metrics['vulnerability_counts']['high'] ?? 0) +
                                            ($metrics['vulnerability_counts']['medium'] ?? 0) +
                                            ($metrics['vulnerability_counts']['low'] ?? 0) +
                                            ($metrics['vulnerability_counts']['info'] ?? 0);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Security Certificate
                                            </h1>
                                            <h2>(Latest CVE Vulnerability Scan)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} Points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Severity</th>
                                                            <th>Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['critical'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['high'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['medium'] ?? 0 }} issues
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($metrics['vulnerability_counts']['low'] ?? 0) + ($metrics['vulnerability_counts']['info'] ?? 0) }} issues
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Latest CVE Vulnerability Scan Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on latest CVE vulnerability analysis results conducted through <strong>Nuclei by ProjectDiscovery</strong>.<br>
                                            Newly released CVEs from 2024-2025, zero-day vulnerabilities, configuration errors, and sensitive information exposure
                                            were precisely tested through template-based scanning, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurement results at a specific point in time and may vary depending on security patches and updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in latest CVE vulnerability scanning,
                                                demonstrating <u>excellent response to latest security threats</u>.<br>
                                                This shows that it is a website with <strong>2024-2025 CVE patches</strong> and <strong>secure configuration management</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vulnerability Summary -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Vulnerability Analysis Results</h4>
                                            <div class="row g-2">
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-danger">
                                                                {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}
                                                            </div>
                                                            <div class="text-muted">Critical</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-warning">
                                                                {{ $metrics['vulnerability_counts']['high'] ?? 0 }}
                                                            </div>
                                                            <div class="text-muted">High</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-info">
                                                                {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}
                                                            </div>
                                                            <div class="text-muted">Medium</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold">
                                                                {{ $metrics['vulnerability_counts']['low'] ?? 0 }}
                                                            </div>
                                                            <div class="text-muted">Low</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center">
                                                            <div class="h3 fw-bold text-muted">
                                                                {{ $metrics['vulnerability_counts']['info'] ?? 0 }}
                                                            </div>
                                                            <div class="text-muted">Info</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (isset($metrics['scan_duration']) && $metrics['scan_duration'] > 0)
                                                <div class="text-muted small mt-2 text-center">
                                                    Scan Duration: {{ $metrics['scan_duration'] }} seconds |
                                                    Matched Templates: {{ $metrics['templates_matched'] ?? 0 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Critical/High Vulnerabilities -->
                                    @php
                                        $criticalHighCount = 0;
                                        foreach (['critical', 'high'] as $severity) {
                                            $criticalHighCount += count($vulnerabilities[$severity] ?? []);
                                        }
                                    @endphp

                                    @if ($criticalHighCount > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Critical/High Vulnerabilities</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ $criticalHighCount }} high-risk vulnerabilities identified.</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach (['critical', 'high'] as $severity)
                                                            @foreach (array_slice($vulnerabilities[$severity] ?? [], 0, 3) as $vuln)
                                                                <li>
                                                                    <strong>{{ $vuln['name'] ?? 'Unknown' }}</strong>
                                                                    <span
                                                                        class="badge {{ $severity === 'critical' ? 'bg-red-lt text-red-lt-fg' : 'bg-orange-lt text-orange-lt-fg' }}">
                                                                        {{ ucfirst($severity) }}
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        @endforeach
                                                        @if ($criticalHighCount > 6)
                                                            <li>Plus {{ $criticalHighCount - 6 }} more...</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Highest security level (0 Critical/High, no 2024-2025 CVE detected)
                                        @elseif ($grade === 'A')
                                            Excellent security (no direct exposure to latest CVEs, good patch management)
                                        @elseif ($grade === 'B')
                                            Good security (some configuration improvements needed)
                                        @else
                                            Security improvements needed
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Nuclei:</strong> Industry standard vulnerability scanner by ProjectDiscovery, template-based fast scanning</p>
                                        <p class="mb-2"><strong>CVE Coverage:</strong> 2024-2025 new CVEs, Log4Shell, Spring4Shell and other major vulnerabilities</p>
                                        <p class="mb-0"><strong>Scan Coverage:</strong> WordPress/Joomla/Drupal plugins, Git/ENV exposure, API endpoints</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Nuclei CVE Scan.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Nuclei-based Latest CVE Vulnerability Automated Detection</h3>
                                <div class="text-muted small mt-1">
                                    Nuclei by ProjectDiscovery is the industry standard vulnerability scanner providing template-based fast scanning.
                                    <br><br>
                                    • <strong>Testing Tool</strong>: Nuclei - Template-based vulnerability scanner<br>
                                    • <strong>Test Coverage</strong>: Newly released CVE vulnerabilities from 2024-2025<br>
                                    • <strong>Scan Items</strong>: Zero-day, configuration errors, sensitive information exposure, backup files<br>
                                    • <strong>Major Vulnerabilities</strong>: Major RCEs like Log4Shell, Spring4Shell<br>
                                    • <strong>Duration</strong>: Approximately 30 seconds-3 minutes (varies by template count)
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>0 Critical/High, 0 Medium<br>No 2024-2025 CVE detected<br>No public directory/debug/sensitive file exposure<br>Good security headers/banner exposure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>≤1 High, ≤1 Medium<br>No direct exposure to recent CVEs<br>Minor configuration warnings level<br>Good patch/configuration management</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>≤2 High or ≤3 Medium<br>Some configuration/banner exposure present<br>Protected admin endpoints exist<br>Patch delay tendency</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>≥3 High or numerous Medium<br>Sensitive file/backup/indexing exposure found<br>Legacy component versions detectable<br>Systematic patch/configuration management improvement needed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>≥1 Critical or low-difficulty High exploitation<br>Recent (2024-2025) CVE direct impact estimated<br>Risky endpoints accessible without authentication<br>Build/log/environment sensitive information exposure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Multiple Critical/High simultaneously present<br>Latest CVE mass unpatched/widespread exposure<br>Lack of basic security configuration<br>Complete absence of security guardrails</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Nuclei Scan Features</strong><br>
                                    - <strong>Template-based</strong>: Accurate vulnerability identification with YAML templates<br>
                                    - <strong>Non-intrusive</strong>: Signature verification only without actual attacks<br>
                                    - <strong>Fast Scanning</strong>: Completion within 30 seconds-3 minutes with optimized templates<br>
                                    - <strong>Latest CVEs</strong>: Immediate reflection of 2024-2025 new vulnerabilities<br><br>

                                    <strong>🌍 Latest Vulnerability Coverage</strong><br>
                                    • <strong>Major RCEs</strong>: Log4Shell, Spring4Shell, etc.<br>
                                    • <strong>CMS Plugins</strong>: WordPress, Joomla, Drupal<br>
                                    • <strong>Web Server Configuration</strong>: Apache, Nginx, IIS<br>
                                    • <strong>Exposure Detection</strong>: Git, SVN, ENV files<br>
                                    • <strong>API Vulnerabilities</strong>: GraphQL, REST API<br>
                                    • <strong>Cloud</strong>: AWS, Azure, GCP configuration errors<br><br>

                                    <strong>📊 Security Improvement Recommendations</strong><br>
                                    - Immediately patch Critical/High vulnerabilities<br>
                                    - Keep CMS, plugins, frameworks updated to latest versions<br>
                                    - Disable unnecessary services, remove debug mode<br>
                                    - Run regular vulnerability scans at least monthly
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'q-lighthouse')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results ?? [];
                                        $metrics = $currentTest->metrics ?? [];
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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Test Certificate
                                            </h1>
                                            <h2>(Google Lighthouse Quality Test)</h2>
                                            <h3>Certificate Code: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-1">{{ $metrics['performance_score'] ?? 'N/A' }}</h3>
                                                            <small>Performance</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-1">{{ $metrics['accessibility_score'] ?? 'N/A' }}</h3>
                                                            <small>Accessibility</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-1">{{ $metrics['best_practices_score'] ?? 'N/A' }}</h3>
                                                            <small>Best Practices</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-1">{{ $metrics['seo_score'] ?? 'N/A' }}</h3>
                                                            <small>SEO</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on the results of the <strong>Google Lighthouse engine</strong>.<br>
                                            All data was collected by <u>simulating a real browser environment</u>, and anyone can validate authenticity via our QR system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects results at a specific point in time and may vary with ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> in Google Lighthouse,
                                                demonstrating <u>top 10% web quality</u>.<br>
                                                It indicates <strong>excellent performance</strong> along with <strong>high accessibility and SEO optimization</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Core Web Vitals -->
                                    @if(isset($results['audits']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Core Web Vitals Results</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Metric</th>
                                                                <th>Value</th>
                                                                <th>Recommended</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($results['audits']['first-contentful-paint']))
                                                                <tr>
                                                                    <td><strong>First Contentful Paint (FCP)</strong></td>
                                                                    <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 1.8s</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['largest-contentful-paint']))
                                                                <tr>
                                                                    <td><strong>Largest Contentful Paint (LCP)</strong></td>
                                                                    <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 2.5s</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['cumulative-layout-shift']))
                                                                <tr>
                                                                    <td><strong>Cumulative Layout Shift (CLS)</strong></td>
                                                                    <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 0.1</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['speed-index']))
                                                                <tr>
                                                                    <td><strong>Speed Index</strong></td>
                                                                    <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 3.4s</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['total-blocking-time']))
                                                                <tr>
                                                                    <td><strong>Total Blocking Time (TBT)</strong></td>
                                                                    <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 200ms</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['interactive']))
                                                                <tr>
                                                                    <td><strong>Time to Interactive (TTI)</strong></td>
                                                                    <td>{{ $results['audits']['interactive']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">≤ 3.8s</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Opportunities -->
                                        @php
                                            $opportunities = collect($results['audits'])->filter(function($audit) {
                                                return isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 0;
                                            })->sortByDesc('details.overallSavingsMs');
                                        @endphp
                                        @if($opportunities->count() > 0)
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h4 class="mb-3">Opportunities for Improvement</h4>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Estimated Savings</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($opportunities->take(5) as $key => $opportunity)
                                                                    <tr>
                                                                        <td>{{ $opportunity['title'] ?? $key }}</td>
                                                                        <td>{{ round($opportunity['details']['overallSavingsMs'] ?? 0) }}ms potential reduction</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Four Evaluation Areas:</strong> Performance, Accessibility, Best Practices, SEO<br>
                                        <span class="text-muted">Each area is scored out of 100; the overall score is a weighted average of the four.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>FCP:</strong> Time from page load start until first content is painted</p>
                                        <p class="mb-2"><strong>LCP:</strong> When the largest content element becomes visible</p>
                                        <p class="mb-2"><strong>CLS:</strong> Cumulative score of unexpected layout shifts during load</p>
                                        <p class="mb-0"><strong>TBT:</strong> Total time the main thread is blocked and can’t respond to input</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Verified via DevTeam-Test Lighthouse Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test provides web quality assessments using the Google Lighthouse engine.
                                            Certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Google Lighthouse — Comprehensive Website Quality Tool</h3>
                                <div class="text-muted small mt-1">
                                    Google Lighthouse is an open-source quality auditing tool by Google, built into Chrome DevTools,
                                    that analyzes performance, accessibility, SEO, and adherence to best practices.
                                    <br><br>
                                    <strong>Tools & Environment</strong><br>
                                    • Latest Lighthouse (Chrome engine based)<br>
                                    • Headless Chrome simulating real browser conditions<br>
                                    • Mobile 3G/4G network profiles and mid-tier device settings<br>
                                    • Core Web Vitals measured to reflect real user experience
                                    <br><br>
                                    <strong>Four Evaluation Areas</strong><br>
                                    1. <strong>Performance</strong>: Load speed, Core Web Vitals, resource optimization<br>
                                    2. <strong>Accessibility</strong>: ARIA labels, color contrast, keyboard navigation<br>
                                    3. <strong>Best Practices</strong>: HTTPS usage, console errors, image ratios<br>
                                    4. <strong>SEO</strong>: Meta tags, structured data, mobile friendliness
                                </div>
                                {{-- Grade Criteria --}}
                                <div class="table-responsive my-3">
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
                                                <td>95–100</td>
                                                <td>Performance ≥ 90<br>Accessibility ≥ 90<br>Best Practices ≥ 90<br>SEO ≥ 90<br>Overall average ≥ 95</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>90–94</td>
                                                <td>Performance ≥ 85<br>Accessibility ≥ 85<br>Best Practices ≥ 85<br>SEO ≥ 85<br>Overall average ≥ 90</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>80–89</td>
                                                <td>Performance ≥ 75<br>Accessibility ≥ 75<br>Best Practices ≥ 75<br>SEO ≥ 75<br>Overall average ≥ 80</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>70–79</td>
                                                <td>Performance ≥ 65<br>Accessibility ≥ 65<br>Best Practices ≥ 65<br>SEO ≥ 65<br>Overall average ≥ 70</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>60–69</td>
                                                <td>Performance ≥ 55<br>Accessibility ≥ 55<br>Best Practices ≥ 55<br>SEO ≥ 55<br>Overall average ≥ 60</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–59</td>
                                                <td>Below the thresholds above</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Core Web Vitals Explained</strong><br>
                                    - <strong>FCP (First Contentful Paint)</strong>: Time until the first content is painted<br>
                                    - <strong>LCP (Largest Contentful Paint)</strong>: Time until the largest element renders in the viewport (≤ 2.5s recommended)<br>
                                    - <strong>CLS (Cumulative Layout Shift)</strong>: Cumulative score of unexpected layout shifts (≤ 0.1 recommended)<br>
                                    - <strong>TBT (Total Blocking Time)</strong>: Total main-thread blocking time between FCP and TTI (≤ 200ms recommended)<br>
                                    - <strong>TTI (Time to Interactive)</strong>: When the page is fully interactive (≤ 3.8s recommended)<br>
                                    - <strong>Speed Index)</strong>: How quickly content is visually displayed (≤ 3.4s recommended)
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ $currentTest->raw_json_pretty ?? 'Preview unavailable.' }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'q-accessibility')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Testing Standards & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Test Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $counts = $currentTest->metrics['violations_count'] ?? [];
                                        $violations = $currentTest->metrics['violations_detail'] ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;

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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Accessibility Certificate
                                            </h1>
                                            <h2>(Web Accessibility Testing)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} pts
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="row g-2">
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-danger">{{ $counts['critical'] ?? 0 }}</div>
                                                            <small>Critical</small>
                                                            <div class="small text-muted">Complete Barriers</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-orange">{{ $counts['serious'] ?? 0 }}</div>
                                                            <small>Serious</small>
                                                            <div class="small text-muted">Major Limitations</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-warning">{{ $counts['moderate'] ?? 0 }}</div>
                                                            <small>Moderate</small>
                                                            <div class="small text-muted">Partial Inconvenience</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-info">{{ $counts['minor'] ?? 0 }}</div>
                                                            <small>Minor</small>
                                                            <div class="small text-muted">Minor Issues</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <strong>Total Violations: {{ $counts['total'] ?? 0 }} issues</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on web accessibility testing performed using the <strong>axe-core engine (Deque Systems)</strong>.<br>
                                            All data was collected according to <u>WCAG 2.1 international standards</u>, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurements at a specific point in time and may vary based on ongoing improvements and optimizations.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website has achieved a grade of
                                                <strong>{{ $grade }}</strong> in accessibility testing,
                                                demonstrating <u>excellent web accessibility standards</u>.<br>
                                                This shows that the website is inclusive and can be used equally by
                                                <strong>all users, including people with disabilities and elderly users</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Violations List -->
                                    @if (!empty($violations))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Major Violations</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="80">Severity</th>
                                                                <th>Issue Description</th>
                                                                <th width="100">Affected Elements</th>
                                                                <th width="150">Category</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach (array_slice($violations, 0, 10) as $violation)
                                                                @php
                                                                    $impactClass = match (strtolower($violation['impact'])) {
                                                                        'critical' => 'badge bg-red-lt text-red-lt-fg',
                                                                        'serious' => 'badge bg-orange-lt text-orange-lt-fg',
                                                                        'moderate' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                                                        default => 'badge bg-cyan-lt text-cyan-lt-fg',
                                                                    };
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        <span class="{{ $impactClass }}">
                                                                            {{ ucfirst($violation['impact']) }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <strong>{{ $violation['help'] }}</strong>
                                                                        @if (!empty($violation['desc']))
                                                                            <br><small class="text-muted">{{ Str::limit($violation['desc'], 100) }}</small>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <small>{{ count($violation['nodes'] ?? []) }} elements</small>
                                                                    </td>
                                                                    <td>
                                                                        @if (!empty($violation['tags']))
                                                                            @foreach (array_slice($violation['tags'], 0, 2) as $tag)
                                                                                <span class="badge bg-azure-lt text-azure-lt-fg small">{{ $tag }}</span><br>
                                                                            @endforeach
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if (count($violations) > 10)
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted">Showing top 10 of {{ count($violations) }} total violations</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Impact Level Distribution -->
                                    <div class="alert alert-info d-block">
                                        <strong>Accessibility Violation Severity Criteria:</strong><br>
                                        <span class="text-danger">● Critical</span>: Issues that prevent users from using specific features (keyboard traps, missing required ARIA)<br>
                                        <span class="text-orange">● Serious</span>: Issues causing significant difficulty with major functions (unlabeled forms, low color contrast)<br>
                                        <span class="text-warning">● Moderate</span>: Issues causing inconvenience for some users (unclear link text)<br>
                                        <span class="text-info">● Minor</span>: Issues causing minor user experience degradation (empty headings, duplicate IDs)
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>WCAG 2.1 Compliance:</strong> Perceivable, Operable, Understandable, Robust</p>
                                        <p class="mb-2"><strong>Legal Requirements:</strong> ADA (US), EN 301 549 (EU), Disability Discrimination Act compliance</p>
                                        <p class="mb-0"><strong>Testing Tool:</strong> axe-core CLI (Deque Systems) - Industry-standard accessibility testing engine</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Accessibility Testing service.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web accessibility testing services based on WCAG 2.1 international standards,
                                            with certificate authenticity verifiable through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Web Accessibility Testing - WCAG 2.1 International Standards Compliance Assessment</h3>
                                <div class="text-muted small mt-1">
                                    Web accessibility is an essential quality indicator that ensures all users, including people with disabilities and elderly users, can use websites equally. WCAG (Web Content Accessibility Guidelines) 2.1 is an international standard established by W3C and is used globally as the standard for web accessibility.
                                    <br><br>
                                    <strong>Testing Tools and Environment</strong><br>
                                    • axe-core CLI (Deque Systems) - Industry-standard accessibility testing engine<br>
                                    • WCAG 2.1 Level AA standards applied<br>
                                    • Automated testing for detectable accessibility issues<br>
                                    • Screen reader and keyboard navigation compatibility verification
                                    <br><br>
                                    <strong>Four Accessibility Principles (POUR)</strong><br>
                                    1. <strong>Perceivable</strong>: All content can be perceived through various senses<br>
                                    2. <strong>Operable</strong>: All functions can be used with keyboard only<br>
                                    3. <strong>Understandable</strong>: Information and UI operations are easy to understand<br>
                                    4. <strong>Robust</strong>: Compatible with various assistive technologies
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>98~100</td>
                                                <td>Critical: 0<br>Serious: 0<br>Moderate: 0~2<br>Minor: 0~5</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>95~97</td>
                                                <td>Critical: 0<br>Serious: 0~1<br>Moderate: 0~5<br>Minor: 0~10</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>90~94</td>
                                                <td>Critical: 0<br>Serious: 0~3<br>Moderate: 0~10<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>80~89</td>
                                                <td>Critical: 0~1<br>Serious: 0~5<br>Moderate: 0~20<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>70~79</td>
                                                <td>Critical: 0~3<br>Serious: 0~10<br>Moderate: Unlimited<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~69</td>
                                                <td>Below the above criteria</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Legal Requirements and Standards</strong><br>
                                    - <strong>United States</strong>: ADA (Americans with Disabilities Act), Section 508<br>
                                    - <strong>European Union</strong>: EN 301 549, Web Accessibility Directive<br>
                                    - <strong>Korea</strong>: Disability Discrimination Act, KWCAG 2.2<br>
                                    - <strong>International</strong>: ISO/IEC 40500, WCAG 2.1 Level AA<br><br>
                                    
                                    Web accessibility is not only a legal requirement but also an important quality indicator that helps serve more users, improve SEO, and enhance brand image.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ $currentTest->raw_json_pretty ?? 'Unable to generate preview.' }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'q-compatibility')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $report = $currentTest->results['report'] ?? [];
                                        $grade = $report['overall']['grade'] ?? 'F';
                                        $gradeClass = match ($grade) {
                                            'A+' => 'badge bg-green-lt text-green-lt-fg',
                                            'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                            'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                            'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                            'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                            'F' => 'badge bg-red-lt text-red-lt-fg',
                                            default => 'badge bg-secondary',
                                        };
                                        $totals = $report['totals'] ?? [];
                                        $okCount = $totals['okCount'] ?? 0;
                                        $jsFirstPartyTotal = $totals['jsFirstPartyTotal'] ?? 0;
                                        $jsThirdPartyTotal = $totals['jsThirdPartyTotal'] ?? null;
                                        $jsNoiseTotal = $totals['jsNoiseTotal'] ?? null;
                                        $cssTotal = $totals['cssTotal'] ?? 0;
                                        $strictMode = !empty($report['strictMode']);
                                        $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Test Certificate
                                            </h1>
                                            <h2>(Cross-Browser Compatibility Test)</h2>
                                            <h3>Certificate Code: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="row g-2">
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $okCount }}/3</h3>
                                                            <small>Browsers OK</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $jsFirstPartyTotal }}</h3>
                                                            <small>JS Errors (First-party)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $cssTotal }}</h3>
                                                            <small>CSS Errors</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h5 class="mb-0">{{ $strictMode ? 'Strict' : 'Standard' }}</h5>
                                                            <small>Test Mode</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (!is_null($jsThirdPartyTotal) || !is_null($jsNoiseTotal))
                                                <div class="mt-2 text-center text-muted small">
                                                    @if (!is_null($jsThirdPartyTotal))
                                                        3rd-party JS errors: {{ $jsThirdPartyTotal }}
                                                    @endif
                                                    @if (!is_null($jsNoiseTotal))
                                                        · Noise: {{ $jsNoiseTotal }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on results from the <strong>Playwright engine (Microsoft)</strong>.<br>
                                            All data was collected across the <u>three major browsers: Chrome, Firefox, and Safari</u>, and authenticity can be verified via QR.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects a snapshot in time and may change with ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site earned a <strong>{{ $grade }}</strong> in the compatibility audit,
                                                demonstrating <u>excellent cross-browser support</u>.<br>
                                                It indicates the site runs reliably across <strong>all major browsers</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Per-browser details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Per-Browser Results</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Browser</th>
                                                            <th>Loaded OK</th>
                                                            <th>JS Errors (First-party)</th>
                                                            <th>CSS Errors</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($report['perBrowser'] as $browser)
                                                            @php
                                                                $jsFirst = $browser['jsFirstPartyCount'] ?? ($browser['jsErrorCount'] ?? 0);
                                                                $jsThird = $browser['jsThirdPartyCount'] ?? null;
                                                                $jsNoise = $browser['jsNoiseCount'] ?? null;
                                                                $browserOk = !empty($browser['ok']);
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ $browser['browser'] ?? '' }}</strong></td>
                                                                <td>
                                                                    @if ($browserOk)
                                                                        <span class="badge bg-green-lt text-green-lt-fg">OK</span>
                                                                    @else
                                                                        <span class="badge bg-red-lt text-red-lt-fg">Not OK</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $jsFirst }}</strong>
                                                                    @if (!is_null($jsThird) || !is_null($jsNoise))
                                                                        <div class="small text-muted">
                                                                            @if (!is_null($jsThird))
                                                                                3rd-party: {{ $jsThird }}
                                                                            @endif
                                                                            @if (!is_null($jsNoise))
                                                                                · Noise: {{ $jsNoise }}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $browser['cssErrorCount'] ?? 0 }}</td>
                                                                <td>
                                                                    @if (!empty($browser['navError']))
                                                                        <span class="text-danger">{{ Str::limit($browser['navError'], 50) }}</span>
                                                                    @else
                                                                        <small class="text-muted">Loaded successfully</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error samples -->
                                    @php
                                        $hasErrors = false;
                                        foreach ($report['perBrowser'] as $browser) {
                                            if (!empty($browser['samples']['jsFirstParty']) || !empty($browser['samples']['css'])) {
                                                $hasErrors = true;
                                                break;
                                            }
                                        }
                                    @endphp

                                    @if ($hasErrors)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Key Error Samples</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Browser</th>
                                                                <th>Error Type</th>
                                                                <th>Message</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($report['perBrowser'] as $browser)
                                                                @php
                                                                    $samples = $browser['samples'] ?? [];
                                                                    $jsFirstSamples = array_slice($samples['jsFirstParty'] ?? [], 0, 2);
                                                                    $cssSamples = array_slice($samples['css'] ?? [], 0, 2);
                                                                @endphp
                                                                @foreach ($jsFirstSamples as $error)
                                                                    <tr>
                                                                        <td>{{ $browser['browser'] }}</td>
                                                                        <td><span class="badge bg-red-lt text-red-lt-fg">JS First-party</span></td>
                                                                        <td><small>{{ Str::limit($error, 100) }}</small></td>
                                                                    </tr>
                                                                @endforeach
                                                                @foreach ($cssSamples as $error)
                                                                    <tr>
                                                                        <td>{{ $browser['browser'] }}</td>
                                                                        <td><span class="badge bg-orange-lt text-orange-lt-fg">CSS</span></td>
                                                                        <td><small>{{ Str::limit($error, 100) }}</small></td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Metrics Measured:</strong> Loaded OK (page fully loaded), JS errors (classified as first-party/third-party/noise), CSS errors (parse/render).<br>
                                        <span class="text-muted">First-party errors originate from the tested domain; third-party errors come from external services.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Test Browsers:</strong> Chromium (Chrome/Edge engine), Firefox (Gecko), WebKit (Safari)</p>
                                        <p class="mb-2"><strong>Test Tool:</strong> Playwright — browser automation by Microsoft</p>
                                        <p class="mb-0"><strong>Criteria:</strong> {{ $strictMode ? 'Strict mode — include all errors' : 'Standard mode — focus on first-party errors' }}</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Verified via DevTeam-Test Cross-Browser Compatibility Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test provides compatibility assessments across major browser engines.
                                            Certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Compatibility Across Chrome, Firefox, and Safari</h3>
                                <div class="text-muted small mt-1">
                                    This cross-browser compatibility test verifies whether your site works correctly on major browsers.
                                    <br><br>
                                    <strong>Tool:</strong> Playwright (Microsoft browser automation)<br>
                                    • Chromium (engine for Chrome, Edge)<br>
                                    • Firefox (Gecko engine)<br>
                                    • WebKit (engine for Safari)
                                    <br><br>
                                    <strong>What We Measure:</strong><br>
                                    • Page load completion (document.readyState === 'complete')<br>
                                    • JavaScript error collection (first-party / third-party / noise)<br>
                                    • CSS error collection (parser/render patterns)<br>
                                    • Per-browser User-Agent details
                                </div>
                                {{-- Grade Criteria --}}
                                <div class="table-responsive my-3">
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
                                                <td>90–100</td>
                                                <td>Chrome/Firefox/Safari <strong>all OK</strong><br>
                                                    First-party JS errors: <strong>0</strong><br>
                                                    CSS rendering errors: <strong>0</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80–89</td>
                                                <td><strong>Good</strong> support in major browsers (≥ 2 OK)<br>
                                                    First-party JS errors <strong>≤ 1</strong><br>
                                                    CSS errors <strong>≤ 1</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–79</td>
                                                <td><strong>Minor differences</strong> across browsers (≥ 2 OK)<br>
                                                    First-party JS errors <strong>≤ 3</strong><br>
                                                    CSS errors <strong>≤ 3</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60–69</td>
                                                <td><strong>Degraded functionality</strong> in some browsers (≥ 1 OK)<br>
                                                    First-party JS errors <strong>≤ 6</strong><br>
                                                    CSS errors <strong>≤ 6</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–59</td>
                                                <td><strong>Numerous issues</strong><br>
                                                    First-party JS errors <strong>≤ 10</strong><br>
                                                    CSS errors <strong>≤ 10</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td><strong>Not functional</strong> in major browsers<br>
                                                    First-party JS errors <strong>> 10</strong><br>
                                                    CSS errors <strong>> 10</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Why Cross-Browser Compatibility Matters</strong><br>
                                    - <strong>User experience</strong>: Consistent experience regardless of browser<br>
                                    - <strong>Market share</strong>: Chrome 65%, Safari 19%, Firefox 3% (as of 2024)<br>
                                    - <strong>Business impact</strong>: Compatibility issues increase churn and reduce revenue<br>
                                    - <strong>SEO impact</strong>: Search engines may penalize pages with JS errors during crawling<br><br>

                                    Cross-browser testing is an essential quality gate before release.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'q-visual')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Testing Standards & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Test Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;
                                        $totals = $results['totals'] ?? [];
                                        $overflowCount = $totals['overflowCount'] ?? 0;
                                        $maxOverflowPx = $totals['maxOverflowPx'] ?? 0;
                                        $reason = $results['overall']['reason'] ?? '';
                                        $perViewport = $results['perViewport'] ?? [];

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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Responsive Design Certificate
                                            </h1>
                                            <h2>(Responsive UI Compatibility Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} pts
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $overflowCount }}</h3>
                                                            <small>Overflow Issues</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $maxOverflowPx }}px</h3>
                                                            <small>Max Overflow</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ 9 - $overflowCount }}/9</h3>
                                                            <small>Passed Viewports</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <small class="text-muted">{{ $reason }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on responsive UI testing performed using the <strong>Playwright engine (Chromium)</strong>.<br>
                                            All data was collected across <u>9 major device viewports</u>, and the authenticity of results can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents objective measurements at a specific point in time and may vary based on ongoing improvements and optimizations.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website has achieved a grade of
                                                <strong>{{ $grade }}</strong> in responsive UI testing,
                                                demonstrating <u>excellent responsive web design</u>.<br>
                                                This shows that the website displays perfectly on
                                                <strong>all devices</strong> without horizontal scrolling,
                                                making it a user-friendly website.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Results by Viewport -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Results by Viewport</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Device</th>
                                                            <th>Viewport Size</th>
                                                            <th>Status</th>
                                                            <th>Overflow Pixels</th>
                                                            <th>Body Render Width</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($perViewport as $vp)
                                                            @php
                                                                $hasOverflow = $vp['overflow'] ?? false;
                                                                $overflowPx = $vp['overflowPx'] ?? 0;
                                                                $hasError = !empty($vp['navError']);
                                                                $deviceName = ucfirst(str_replace('-', ' ', explode('-', $vp['viewport'])[0] ?? ''));
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ $deviceName }}</strong></td>
                                                                <td>{{ $vp['w'] ?? 0 }}×{{ $vp['h'] ?? 0 }}px</td>
                                                                <td>
                                                                    @if ($hasError)
                                                                        <span class="badge bg-secondary">Error</span>
                                                                    @elseif ($hasOverflow)
                                                                        <span class="badge bg-red-lt text-red-lt-fg">Overflow</span>
                                                                    @else
                                                                        <span class="badge bg-green-lt text-green-lt-fg">Passed</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($overflowPx > 0)
                                                                        <strong class="text-danger">+{{ $overflowPx }}px</strong>
                                                                    @else
                                                                        <span class="text-muted">0px</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $vp['bodyRenderWidth'] ?? 0 }}px</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device Group Analysis -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Analysis by Device Group</h4>
                                            <div class="row g-2">
                                                @php
                                                    $mobileCount = 0;
                                                    $tabletCount = 0;
                                                    $desktopCount = 0;
                                                    foreach ($perViewport as $vp) {
                                                        if (!($vp['overflow'] ?? false)) {
                                                            $w = $vp['w'] ?? 0;
                                                            if ($w <= 414) $mobileCount++;
                                                            elseif ($w <= 1024) $tabletCount++;
                                                            else $desktopCount++;
                                                        }
                                                    }
                                                @endphp
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>Mobile (360-414px)</h5>
                                                            <div class="h3">{{ $mobileCount }}/3</div>
                                                            <small>Passed</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>Tablet (672-1024px)</h5>
                                                            <div class="h3">{{ $tabletCount }}/4</div>
                                                            <small>Passed</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>Desktop (1280px+)</h5>
                                                            <div class="h3">{{ $desktopCount }}/2</div>
                                                            <small>Passed</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-block">
                                        <strong>Measurement Method:</strong> Set browser to each viewport → Load page → Measure body element width → Compare with viewport width<br>
                                        <span class="text-muted">When overflow occurs, users need horizontal scrolling, which significantly degrades mobile usability.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Test Viewports:</strong> 3 Mobile, 1 Foldable, 3 Tablet, 2 Desktop (Total 9)</p>
                                        <p class="mb-2"><strong>Measurement Criteria:</strong> document.body.getBoundingClientRect().width vs window.innerWidth</p>
                                        <p class="mb-0"><strong>Stabilization Wait:</strong> 6-second wait after network completion to ensure dynamic content loading</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Responsive UI Testing service.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides responsive UI testing services based on various device environments,
                                            with certificate authenticity verifiable through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Playwright-based Responsive UI Compatibility Testing</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Testing Tool:</strong> Playwright (Chromium Engine)<br>
                                    <strong>Test Purpose:</strong> Verify that web pages render correctly without exceeding viewport boundaries across various device environments<br>
                                    <strong>Test Scope:</strong> 9 major viewports (3 Mobile, 1 Foldable, 3 Tablet, 2 Desktop)<br><br>

                                    <strong>Testing Process:</strong><br>
                                    1. Configure browser to each viewport size<br>
                                    2. Wait for network stabilization after page load (6 seconds)<br>
                                    3. Measure document.body.getBoundingClientRect()<br>
                                    4. Calculate overflow pixels by comparing with viewport width<br><br>

                                    <strong>Test Viewport List:</strong><br>
                                    • Mobile: 360×800, 390×844, 414×896<br>
                                    • Foldable: 672×960<br>
                                    • Tablet: 768×1024, 834×1112, 1024×1366<br>
                                    • Desktop: 1280×800, 1440×900
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td>100</td>
                                                <td>0 overflow issues across all viewports<br>Body render width always within viewport</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>90~95</td>
                                                <td>≤1 overflow and ≤8px<br>0 overflow on narrow mobile (≤390px) viewports</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>80~89</td>
                                                <td>≤2 overflows with ≤16px each<br>Or ≤8px overflow on narrow mobile viewports</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>70~79</td>
                                                <td>≤4 overflow issues or single overflow 17~32px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~69</td>
                                                <td>>4 overflow issues or single overflow 33~64px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Measurement failure or ≥65px overflow</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Importance of Responsive Web Design</strong><br>
                                    - <strong>Mobile First</strong>: Over 60% of web traffic comes from mobile devices (2024 data)<br>
                                    - <strong>User Experience</strong>: Horizontal scrolling increases mobile user bounce rate by 40%<br>
                                    - <strong>SEO Impact</strong>: Google considers mobile-friendliness as a core ranking factor<br>
                                    - <strong>Accessibility</strong>: Provides equal experience for users across diverse devices<br><br>
                                    
                                    Responsive UI is a fundamental requirement in modern web development and directly impacts business success.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'c-links')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results ?? [];
                                        $totals = $results['totals'] ?? [];
                                        $rates = $results['rates'] ?? [];
                                        $overall = $results['overall'] ?? [];
                                        $samples = $results['samples'] ?? [];
                                        
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;
                                        
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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Test Certificate
                                            </h1>
                                            <h2>(Link Validation Test)</h2>
                                            <h3>Certificate Code: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($score, 1) }} points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Checked</th>
                                                            <th>Errors</th>
                                                            <th>Error Rate</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>All Links</strong></td>
                                                            <td>{{ $totals['httpChecked'] ?? 0 }}</td>
                                                            <td>{{ ($totals['internalErrors'] ?? 0) + ($totals['externalErrors'] ?? 0) }}</td>
                                                            <td>
                                                                @if (($rates['overallErrorRate'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">{{ $rates['overallErrorRate'] ?? 0 }}%</span>
                                                                @elseif (($rates['overallErrorRate'] ?? 0) <= 3)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">{{ $rates['overallErrorRate'] ?? 0 }}%</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">{{ $rates['overallErrorRate'] ?? 0 }}%</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Internal Links</strong></td>
                                                            <td>{{ $totals['internalChecked'] ?? 0 }}</td>
                                                            <td>{{ $totals['internalErrors'] ?? 0 }}</td>
                                                            <td>{{ $rates['internalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>External Links</strong></td>
                                                            <td>{{ $totals['externalChecked'] ?? 0 }}</td>
                                                            <td>{{ $totals['externalErrors'] ?? 0 }}</td>
                                                            <td>{{ $rates['externalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Image Links</strong></td>
                                                            <td>{{ $totals['imageChecked'] ?? 0 }}</td>
                                                            <td>{{ $totals['imageErrors'] ?? 0 }}</td>
                                                            <td>{{ $rates['imageErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Anchor Links</strong></td>
                                                            <td>{{ $totals['anchorChecked'] ?? 0 }}</td>
                                                            <td>{{ $totals['anchorErrors'] ?? 0 }}</td>
                                                            <td>{{ $rates['anchorErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Max Redirect Chain</strong></td>
                                                            <td colspan="3">{{ $totals['maxRedirectChainEffective'] ?? 0 }} hops</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on a full site link validation performed with a <strong>Playwright-based Link Validator</strong>.<br>
                                            All data was collected in a <u>real browser environment</u>, including JavaScript-driven dynamic content.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This assessment reflects link status at a point in time; results may change due to external site updates, etc.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> in the link validation test,
                                                proving <u>excellent link integrity</u>.<br>
                                                This indicates outstanding <strong>user experience</strong> and <strong>content accessibility</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Error link samples -->
                                    @if (!empty($samples['links']) || !empty($samples['images']) || !empty($samples['anchors']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Sample Broken Links</h4>
                                                
                                                @if (!empty($samples['links']))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-danger-lt">
                                                            <h5 class="card-title mb-0">Broken Links (Internal/External)</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>URL</th>
                                                                            <th>Status</th>
                                                                            <th>Error</th>
                                                                            <th>Chain</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach (array_slice($samples['links'], 0, 10) as $sample)
                                                                            <tr>
                                                                                <td class="text-break" style="max-width: 400px;">
                                                                                    <code class="small">{{ $sample['url'] ?? '' }}</code>
                                                                                </td>
                                                                                <td><span class="badge bg-red-lt text-red-lt-fg">{{ $sample['status'] ?? 0 }}</span></td>
                                                                                <td class="small">{{ $sample['error'] ?? '' }}</td>
                                                                                <td>{{ $sample['chain'] ?? 0 }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if (count($samples['links']) > 10)
                                                                <div class="text-muted small">... plus {{ count($samples['links']) - 10 }} more errors</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($samples['images']))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-warning-lt">
                                                            <h5 class="card-title mb-0">Broken Image Links</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Image URL</th>
                                                                            <th>Status</th>
                                                                            <th>Error</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach (array_slice($samples['images'], 0, 10) as $sample)
                                                                            <tr>
                                                                                <td class="text-break" style="max-width: 450px;">
                                                                                    <code class="small">{{ $sample['url'] ?? '' }}</code>
                                                                                </td>
                                                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">{{ $sample['status'] ?? 0 }}</span></td>
                                                                                <td class="small">{{ $sample['error'] ?? '' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if (count($samples['images']) > 10)
                                                                <div class="text-muted small">... plus {{ count($samples['images']) - 10 }} more errors</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($samples['anchors']))
                                                    <div class="card">
                                                        <div class="card-header bg-info-lt">
                                                            <h5 class="card-title mb-0">Missing Anchors (#id)</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($samples['anchors'], 0, 10) as $sample)
                                                                    <li><code>{{ $sample['href'] ?? '' }}</code></li>
                                                                @endforeach
                                                            </ul>
                                                            @if (count($samples['anchors']) > 10)
                                                                <div class="text-muted small mt-2">... plus {{ count($samples['anchors']) - 10 }} more errors</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-success d-block">
                                            <strong>✨ All Links Healthy</strong><br>
                                            Every checked link is working correctly.
                                        </div>
                                    @endif

                                    <!-- Additional info -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Link Integrity Matters</strong><br>
                                        - User experience: Broken links erode trust and raise bounce rates<br>
                                        - SEO impact: Many 404s negatively affect rankings<br>
                                        - Accessibility: All content must be reachable to meet standards<br>
                                        - Brand image: Broken images/links undermine professionalism
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Internal Links:</strong> Connections between pages on the same domain</p>
                                        <p class="mb-2"><strong>External Links:</strong> Links to other websites</p>
                                        <p class="mb-2"><strong>Image Links:</strong> Resources in the <code>img</code> tag’s <code>src</code></p>
                                        <p class="mb-2"><strong>Anchor Links:</strong> In-page jumps to sections (#id)</p>
                                        <p class="mb-0"><strong>Redirect Chain:</strong> Number of hops to reach the final destination</p>
                                    </div>
                                    
                                    @if (!empty($totals['navError']))
                                        <div class="alert alert-danger d-block">
                                            <strong>⚠️ Navigation Error</strong><br>
                                            {{ $totals['navError'] }}
                                        </div>
                                    @endif
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Verified via DevTeam-Test Link Validator.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test provides a precise, Playwright-based link validation service.
                                            Certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Playwright-Based Link Validation Tool</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Tool:</strong> Playwright + custom Node.js crawler<br>
                                    <strong>Purpose:</strong> Inspect all links on your site to find broken links, invalid redirects, and missing anchors that harm UX.
                                    <br><br>
                                    <strong>Checks:</strong><br>
                                    • Internal links: HTTP status of all same-domain links<br>
                                    • External links: Validity of outbound links<br>
                                    • Image links: Status of image resources in <code>img[src]</code><br>
                                    • Anchor links: Existence of <code>#id</code> targets in the same page<br>
                                    • Redirect chains: Number of hops and final destination of each link
                                </div>
                                {{-- Grade Criteria --}}
                                <div class="table-responsive my-3">
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
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>90–100</td>
                                                <td>• Internal/External/Image link error rate: 0%<br>
                                                    • Redirect chain ≤ 1 hop<br>
                                                    • Anchor links 100% valid</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80–89</td>
                                                <td>• Overall error rate ≤ 1%<br>
                                                    • Redirect chain ≤ 2 hops<br>
                                                    • Most anchor links valid</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70–79</td>
                                                <td>• Overall error rate ≤ 3%<br>
                                                    • Redirect chain ≤ 3 hops<br>
                                                    • Some anchor links invalid</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>60–69</td>
                                                <td>• Overall error rate ≤ 5%<br>
                                                    • Many link warnings (timeout/SSL issues)<br>
                                                    • Frequent anchor link issues</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50–59</td>
                                                <td>• Overall error rate ≤ 10%<br>
                                                    • Redirect loops or long chains<br>
                                                    • Many broken image links</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0–49</td>
                                                <td>• Overall error rate ≥ 10%<br>
                                                    • Many broken key internal links<br>
                                                    • Widespread anchor/image issues</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Link Maintenance Checklist</strong><br>
                                    <strong>Regular audits:</strong><br>
                                    • Run a full site link check monthly<br>
                                    • Monitor external link validity<br>
                                    • Fix 404 pages immediately<br><br>
                                    
                                    <strong>Optimization tips:</strong><br>
                                    • Minimize redirects: link directly when possible<br>
                                    • Anchor matching: ensure <code>href="#id"</code> matches <code>id="id"</code><br>
                                    • Image hygiene: correct paths and file existence<br>
                                    • Use HTTPS: enforce secure protocols<br><br>
                                    
                                    <strong>Impact metrics:</strong><br>
                                    • Remove broken links → bounce rate ↓ 20%<br>
                                    • Optimize redirects → page speed ↑ 15%<br>
                                    • Fix images → user satisfaction ↑ 25%
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'c-structure')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Testing Standards & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Test Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results ?? [];
                                        $totals = $results['totals'] ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;
                                        
                                        $gradeClass = match ($grade) {
                                            'A+' => 'badge bg-green-lt text-green-lt-fg',
                                            'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                            'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                            'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                            'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                            'F' => 'badge bg-red-lt text-red-lt-fg',
                                            default => 'badge bg-secondary',
                                        };
                                        
                                        $hasJsonLd = ($totals['jsonLdItems'] ?? 0) > 0;
                                        $parseErrors = $results['parseErrors'] ?? [];
                                        $perItem = $results['perItem'] ?? [];
                                        $actions = $results['actions'] ?? [];
                                        $snippets = $results['snippets'] ?? [];
                                        $types = $results['types'] ?? [];
                                        $richTypes = $totals['richEligibleTypes'] ?? [];
                                        $totalErrors = ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Structured Data Certificate
                                            </h1>
                                            <h2>(Structured Data Validation)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($score, 1) }} pts
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Count</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>JSON-LD Blocks</strong></td>
                                                            <td>{{ $totals['jsonLdBlocks'] ?? 0 }} items</td>
                                                            <td>
                                                                @if (($totals['jsonLdBlocks'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Implemented</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Not Found</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Schema Items</strong></td>
                                                            <td>{{ $totals['jsonLdItems'] ?? 0 }} items</td>
                                                            <td>
                                                                @if (($totals['jsonLdItems'] ?? 0) >= 3)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Sufficient</span>
                                                                @elseif (($totals['jsonLdItems'] ?? 0) > 0)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Basic</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">None</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Errors/Warnings</strong></td>
                                                            <td>
                                                                <span class="text-danger">{{ $totalErrors }} errors</span> /
                                                                <span class="text-warning">{{ $totals['itemWarnings'] ?? 0 }} warnings</span>
                                                            </td>
                                                            <td>
                                                                @if ($totalErrors === 0 && ($totals['itemWarnings'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Perfect</span>
                                                                @elseif ($totalErrors === 0)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Good</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Needs Improvement</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Rich Results Types</strong></td>
                                                            <td>{{ is_array($richTypes) ? count($richTypes) : 0 }} types</td>
                                                            <td>
                                                                @if (is_array($richTypes) && count($richTypes) > 0)
                                                                    {{ implode(', ', array_slice($richTypes, 0, 3)) }}
                                                                @else
                                                                    <span class="text-muted">None</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Other Formats</strong></td>
                                                            <td>
                                                                Microdata: {{ !empty($totals['hasMicrodata']) ? '✓' : '✗' }}
                                                                RDFa: {{ !empty($totals['hasRdfa']) ? '✓' : '✗' }}
                                                            </td>
                                                            <td>
                                                                @if (!empty($totals['hasMicrodata']) || !empty($totals['hasRdfa']))
                                                                    <span class="badge">Alternative Formats</span>
                                                                @else
                                                                    <span class="text-muted">JSON-LD Only</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on Schema.org compliance testing performed using <strong>Playwright-based structured data validation tools</strong>.<br>
                                            All data was evaluated according to <u>Google Rich Results Test standards</u> and collected from actual browser rendering environments.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents the structured data state at a specific point in time and may change with website updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website has achieved a grade of
                                                <strong>{{ $grade }}</strong> in structured data validation,
                                                qualifying for <u>Rich Snippets display in search results</u>.<br>
                                                This demonstrates excellent structured data implementation that contributes to
                                                <strong>search visibility optimization</strong> and <strong>click-through rate improvement</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Schema Type Analysis -->
                                    @if (!empty($types))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Schema Type Distribution</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>@type</th>
                                                                <th>Count</th>
                                                                <th>Rich Results Support</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach (array_slice($types, 0, 10) as $row)
                                                                <tr>
                                                                    <td><code>{{ $row['type'] }}</code></td>
                                                                    <td>{{ $row['count'] }}</td>
                                                                    <td>
                                                                        @if (in_array($row['type'], ['Article', 'Product', 'Recipe', 'Event', 'Course', 'FAQPage', 'HowTo', 'JobPosting', 'LocalBusiness', 'Review', 'Video']))
                                                                            <span class="badge bg-green-lt text-green-lt-fg">Supported</span>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Error and Warning Details -->
                                    @if (!empty($parseErrors) || !empty($perItem))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Validation Issues Details</h4>
                                                
                                                @if (!empty($parseErrors))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-danger-lt">
                                                            <h5 class="card-title mb-0">Parsing Errors</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($parseErrors, 0, 5) as $pe)
                                                                    <li class="mb-2">
                                                                        <strong>Block #{{ $pe['index'] }}:</strong> {{ $pe['message'] }}
                                                                        <div class="text-muted small">{{ Str::limit($pe['rawPreview'] ?? '', 100) }}</div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($perItem))
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5 class="card-title mb-0">Item-specific Issues</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            @foreach (array_slice($perItem, 0, 5) as $item)
                                                                @if (!empty($item['errors']) || !empty($item['warnings']))
                                                                    <div class="mb-3">
                                                                        <strong>{{ implode(', ', $item['types'] ?? ['Unknown']) }}</strong>
                                                                        @if (!empty($item['errors']))
                                                                            <div class="text-danger small">
                                                                                Errors: {{ implode(', ', $item['errors']) }}
                                                                            </div>
                                                                        @endif
                                                                        @if (!empty($item['warnings']))
                                                                            <div class="text-warning small">
                                                                                Warnings: {{ implode(', ', $item['warnings']) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Recommended Improvements -->
                                    @if (!empty($actions))
                                        <div class="alert alert-warning d-block">
                                            <strong>⚡ Recommended Improvements</strong><br>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($actions as $action)
                                                    <li>{{ $action }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Example Snippets -->
                                    @if (!empty($snippets))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Recommended JSON-LD Examples</h4>
                                                @foreach (array_slice($snippets, 0, 2) as $snippet)
                                                    <div class="card mb-3">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">{{ $snippet['title'] ?? $snippet['type'] ?? 'JSON-LD' }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <pre class="json-dump" style="max-height: 300px; overflow-y: auto;"><code>{!! json_encode($snippet['json'] ?? (object)[], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</code></pre>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Additional Information -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Structured Data Matters</strong><br>
                                        - Rich Snippets: Display enhanced information like ratings, prices, and images in search results<br>
                                        - Voice Search Optimization: Helps AI assistants understand and respond to information accurately<br>
                                        - Knowledge Graph: Enables information registration in Google's knowledge panels<br>
                                        - Higher CTR: Achieves 30% higher click-through rates compared to regular search results
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>JSON-LD:</strong> JavaScript Object Notation for Linked Data, Google's recommended format</p>
                                        <p class="mb-2"><strong>Schema.org:</strong> Structured data standard jointly developed by Google, Microsoft, Yahoo, and Yandex</p>
                                        <p class="mb-2"><strong>Rich Results:</strong> Visually enhanced search results displayed in search engines</p>
                                        <p class="mb-2"><strong>Essential Schemas:</strong> Organization, WebSite, BreadcrumbList (recommended for all sites)</p>
                                        <p class="mb-0"><strong>Content-specific Schemas:</strong> Article (blogs), Product (e-commerce), LocalBusiness (local businesses)</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Structure Validator service.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides structured data validation services compliant with Google Rich Results standards,
                                            with certificate authenticity verifiable through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Structured Data Validation Tool</h3>
                                <div class="text-muted small mt-1">
                                    Using Playwright-based browser automation to collect structured data from actually rendered pages
                                    and applying Schema.org validation rules compliant with Google Rich Results Test standards.
                                    <br><br>
                                    <strong>📊 Measurement Items:</strong><br>
                                    • Number of JSON-LD blocks and parsing feasibility<br>
                                    • Validation of required/recommended fields by Schema.org type<br>
                                    • Rich Results eligibility assessment<br>
                                    • Detection of other formats like Microdata and RDFa<br><br>
                                    
                                    <strong>🎯 Validated Schemas:</strong><br>
                                    • Organization, WebSite, BreadcrumbList (basic)<br>
                                    • Article, NewsArticle, BlogPosting (content)<br>
                                    • Product, Offer, AggregateRating (e-commerce)<br>
                                    • LocalBusiness, Restaurant, Store (local)<br>
                                    • Event, Course, Recipe (special content)<br>
                                    • FAQPage, HowTo, QAPage (Q&A)<br>
                                    • Person, JobPosting, Review (others)
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>95~100</td>
                                                <td>• Perfect JSON-LD implementation (no parsing errors)<br>
                                                    • 3+ schema types, 2+ Rich Results types<br>
                                                    • All required fields included, 80%+ recommended fields</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85~94</td>
                                                <td>• Proper JSON-LD implementation<br>
                                                    • 2+ schema types, 1+ Rich Results type<br>
                                                    • Required fields complete, 60%+ recommended fields</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75~84</td>
                                                <td>• Basic JSON-LD implementation<br>
                                                    • 1+ schema type<br>
                                                    • Most required fields included</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65~74</td>
                                                <td>• Partial structured data implementation<br>
                                                    • Minor errors present<br>
                                                    • Some required fields missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~64</td>
                                                <td>• Insufficient structured data<br>
                                                    • Parsing errors or critical errors present<br>
                                                    • Many required fields missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>• No structured data<br>
                                                    • JSON-LD not implemented<br>
                                                    • Schema.org not applied</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Structured Data Implementation Checklist</strong><br>
                                    <strong>Required Implementation:</strong><br>
                                    • Organization: Company information, logo, social profiles<br>
                                    • WebSite: Site name, URL, search box<br>
                                    • BreadcrumbList: Page path navigation<br><br>
                                    
                                    <strong>Content-specific Implementation:</strong><br>
                                    • Blog/News: Article, NewsArticle, BlogPosting<br>
                                    • E-commerce: Product, Offer, Review, AggregateRating<br>
                                    • Local Business: LocalBusiness, OpeningHoursSpecification<br>
                                    • Events: Event, EventVenue, EventSchedule<br><br>
                                    
                                    <strong>Performance Metrics:</strong><br>
                                    • Rich Snippets exposure → Average 30% CTR increase<br>
                                    • Voice search optimization → 20% mobile traffic increase<br>
                                    • Knowledge Graph registration → Brand awareness improvement
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'c-crawl')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Verification Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Measurement Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $report = $currentTest->results ?? [];
                                        $grade = $currentTest->overall_grade ?? 'F';
                                        $score = $currentTest->overall_score ?? 0;
                                        $robots = $report['robots'] ?? [];
                                        $sitemap = $report['sitemap'] ?? [];
                                        $pages = $report['pages'] ?? [];
                                        $crawlPlan = $report['crawlPlan'] ?? [];
                                        
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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Test Certificate
                                            </h1>
                                            <h2>(Search Engine Crawling Audit)</h2>
                                            <h3>Certificate Code: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($score, 1) }} points
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Value</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>robots.txt</strong></td>
                                                            <td>{{ $robots['status'] ?? '-' }}</td>
                                                            <td>
                                                                @if ($robots['exists'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Present</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Missing</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>sitemap.xml</strong></td>
                                                            <td>{{ $sitemap['sitemapUrlCount'] ?? 0 }} URLs</td>
                                                            <td>
                                                                @if ($sitemap['hasSitemap'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Present</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Missing</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Pages Checked</strong></td>
                                                            <td>{{ $pages['count'] ?? 0 }}</td>
                                                            <td>Avg {{ number_format($pages['qualityAvg'] ?? 0, 1) }} pts</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Error Rate</strong></td>
                                                            <td>{{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['errorRate4xx5xx'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Good</span>
                                                                @elseif (($pages['errorRate4xx5xx'] ?? 0) < 5)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Fair</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Issue</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Duplication Rate</strong></td>
                                                            <td>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['duplicateRate'] ?? 0) <= 30)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Acceptable</span>
                                                                @else
                                                                    <span class="badge bg-warning-lt text-warning-lt-fg">High</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on the results of a <strong>robots.txt-compliant crawler</strong> crawl.<br>
                                            All data was collected by simulating a <u>real search-engine crawling process</u> and evaluated against SEO quality criteria.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This audit reflects crawl status at a point in time and may change as the website is updated.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> in the crawling audit,
                                                demonstrating <u>excellent search engine optimization</u>.<br>
                                                This indicates strong <strong>crawler friendliness</strong> and <strong>page quality management</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Sitemap file details -->
                                    @if (!empty($sitemap['sitemaps']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Sitemap Files</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Filename</th>
                                                                <th>URL Count</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sitemap['sitemaps'] as $s)
                                                                <tr>
                                                                    <td>{{ basename($s['url']) }}</td>
                                                                    <td>{{ $s['count'] ?? 0 }}</td>
                                                                    <td>
                                                                        @if ($s['ok'])
                                                                            <span class="badge bg-green-lt text-green-lt-fg">OK</span>
                                                                        @else
                                                                            <span class="badge bg-red-lt text-red-lt-fg">Error</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Crawl plan & excluded URLs -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Sample Target URLs</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="small text-muted mb-2">
                                                        {{ $crawlPlan['candidateCount'] ?? 0 }} total, up to 50 checked
                                                    </div>
                                                    @if (!empty($crawlPlan['sample']))
                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                            <ul class="small mb-0">
                                                                @foreach (array_slice($crawlPlan['sample'], 0, 10) as $url)
                                                                    <li class="text-break">{{ $url }}</li>
                                                                @endforeach
                                                                @if (count($crawlPlan['sample']) > 10)
                                                                    <li>... plus {{ count($crawlPlan['sample']) - 10 }} more</li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Excluded URLs</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if (!empty($crawlPlan['skipped']))
                                                        <div class="small text-muted mb-2">
                                                            {{ count($crawlPlan['skipped']) }} total excluded
                                                        </div>
                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                            @foreach (array_slice($crawlPlan['skipped'], 0, 5) as $skip)
                                                                <div class="mb-2 small">
                                                                    <div class="text-danger fw-bold">{{ $skip['reason'] }}</div>
                                                                    <div class="text-break text-muted">{{ $skip['url'] }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted">No excluded URLs ✓</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Problematic pages -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-header bg-danger-lt">
                                                    <h5 class="card-title mb-0">Error Pages (4xx/5xx)</h5>
                                                </div>
                                                <div class="card-body">
                                                    @php $errorPages = $report['samples']['errorPages'] ?? []; @endphp
                                                    @if (empty($errorPages))
                                                        <div class="text-success">No error pages ✓</div>
                                                    @else
                                                        <ul class="small mb-0">
                                                            @foreach (array_slice($errorPages, 0, 5) as $page)
                                                                <li class="mb-1">
                                                                    <span class="badge bg-red-lt text-red-lt-fg">{{ $page['status'] }}</span>
                                                                    <span class="text-break">{{ Str::limit($page['url'], 50) }}</span>
                                                                </li>
                                                            @endforeach
                                                            @if (count($errorPages) > 5)
                                                                <li>... plus {{ count($errorPages) - 5 }} more</li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-header bg-warning-lt">
                                                    <h5 class="card-title mb-0">Low Quality Pages (&lt; 50 pts)</h5>
                                                </div>
                                                <div class="card-body">
                                                    @php
                                                        $lowQuality = collect($report['samples']['lowQuality'] ?? [])
                                                            ->filter(function ($page) {
                                                                return ($page['score'] ?? 100) < 50;
                                                            })
                                                            ->take(5)
                                                            ->values()
                                                            ->toArray();
                                                    @endphp
                                                    @if (empty($lowQuality))
                                                        <div class="text-success">No pages below 50 pts ✓</div>
                                                    @else
                                                        <ul class="small mb-0">
                                                            @foreach ($lowQuality as $page)
                                                                <li class="mb-1">
                                                                    <span class="badge bg-orange-lt text-orange-lt-fg">{{ $page['score'] ?? 0 }} pts</span>
                                                                    <span class="text-break">{{ Str::limit($page['url'], 50) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Duplicate content -->
                                    @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                                        <div class="alert alert-warning d-block">
                                            <strong>⚠️ Duplicate Content Detected</strong><br>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    Pages with duplicate titles: <strong>{{ $pages['dupTitleCount'] ?? 0 }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    Pages with duplicate descriptions: <strong>{{ $pages['dupDescCount'] ?? 0 }}</strong>
                                                </div>
                                            </div>
                                            <div class="small mt-2">
                                                Duplication rate: <strong>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</strong>
                                                – We recommend a unique <code>title</code> and <code>description</code> for every page.
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Additional info -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Crawl Optimization Matters</strong><br>
                                        - Indexing: <code>robots.txt</code> and <code>sitemap.xml</code> are fundamental for search engines to understand your site<br>
                                        - Crawl efficiency: Proper rules prioritize important pages<br>
                                        - SEO: Page quality and duplicate content directly affect rankings<br>
                                        - UX: Maintain a clean structure without 404s
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>robots.txt:</strong> File defining crawler access rules</p>
                                        <p class="mb-2"><strong>sitemap.xml:</strong> List of important pages with metadata</p>
                                        <p class="mb-2"><strong>Quality Score:</strong> Aggregate of title, description, canonical, H1, and content volume</p>
                                        <p class="mb-2"><strong>Error Rate:</strong> Share of inaccessible pages (404, 500, etc.)</p>
                                        <p class="mb-0"><strong>Duplication Rate:</strong> Share of pages reusing the same metadata</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Verified via DevTeam-Test Crawl Inspector.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test provides search-engine–compliant crawling audits.
                                            Certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Search Engine Crawling Compliance & Page Quality Analysis</h3>
                                <div class="text-muted small mt-1">
                                    We analyze <code>robots.txt</code> and <code>sitemap.xml</code> for SEO compliance and evaluate the accessibility and quality of pages listed in the sitemap.
                                    <br><br>
                                    <strong>📋 Audit Process:</strong><br>
                                    1) Check existence and rules in <code>robots.txt</code><br>
                                    2) Locate <code>sitemap.xml</code> and collect URLs<br>
                                    3) Filter allowed URLs per robots.txt rules<br>
                                    4) Sample up to 50 pages and test sequentially<br>
                                    5) Measure HTTP status, metadata, and quality score of each page<br>
                                    6) Analyze duplicate <code>title/description</code> rates
                                </div>
                                {{-- Grade Criteria --}}
                                <div class="table-responsive my-3">
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
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>90–100</td>
                                                <td>robots.txt correctly applied<br>
                                                    sitemap.xml present, no omissions/404s<br>
                                                    All checked pages return 2xx<br>
                                                    Avg page quality ≥ 85 pts<br>
                                                    Duplicate content ≤ 30%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80–89</td>
                                                <td>robots.txt correctly applied<br>
                                                    sitemap.xml present and consistent<br>
                                                    All checked pages return 2xx<br>
                                                    Avg page quality ≥ 85 pts</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70–79</td>
                                                <td>robots.txt and sitemap.xml present<br>
                                                    All checked pages return 2xx<br>
                                                    Avg page quality not required</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>55–69</td>
                                                <td>robots.txt and sitemap.xml present<br>
                                                    Some checked pages include 4xx/5xx errors</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>35–54</td>
                                                <td>robots.txt and sitemap.xml present<br>
                                                    URL list can be generated<br>
                                                    But poor accessibility or quality not measurable</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0–34</td>
                                                <td>No robots.txt and/or no sitemap.xml<br>
                                                    Unable to generate a test list</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Crawl Optimization Checklist</strong><br>
                                    <strong>Must-haves:</strong><br>
                                    • robots.txt: User-agent, Allow/Disallow, sitemap location<br>
                                    • sitemap.xml: All key pages included, maintain <code>lastmod</code><br>
                                    • 404 handling: Custom 404 page and proper 301 redirects<br><br>
                                    
                                    <strong>Improve Quality Score:</strong><br>
                                    • Title: 50–60 chars, unique per page<br>
                                    • Description: 120–160 chars, unique per page<br>
                                    • Canonical URL: Set on every page<br>
                                    • H1 tag: One per page, clear heading<br>
                                    • Content: At least ~1000 chars of substantive copy<br><br>
                                    
                                    <strong>Impact Metrics:</strong><br>
                                    • Crawl optimization → indexing speed ↑ 50%<br>
                                    • Remove duplicates → rankings ↑ 20%<br>
                                    • Fix 404s → bounce rate ↓ 15%
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($test_type == 'c-meta')
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                    class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Testing Standards & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Test Data</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">

                                <div id="certification">
                                    @php
                                        $results = $currentTest->results ?? [];
                                        $metadata = $results['metadata'] ?? [];
                                        $analysis = $results['analysis'] ?? [];
                                        $summary = $results['summary'] ?? [];
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
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                Web Metadata Optimization Certificate
                                            </h1>
                                            <h2>(Metadata Completeness Test)</h2>
                                            <h3>Certificate ID: {{ $certificate->code }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class="card mb-4">
                                                <div class="card-body text-center pt-3 pb-1">
                                                    <div class="mb-3">
                                                        <div class="h1 mb-2">
                                                            <span
                                                                class="{{ $gradeClass }}">{{ $grade }}</span>
                                                        </div>
                                                        @if ($currentTest->overall_score)
                                                            <div class="text-muted h4">
                                                                {{ number_format($currentTest->overall_score, 1) }} pts
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Test Date:
                                                            {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                                                        </small>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Status</th>
                                                            <th>Details</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Title Tag</strong></td>
                                                            <td>
                                                                @if ($analysis['title']['isEmpty'] ?? true)
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Missing</span>
                                                                @elseif ($analysis['title']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                @elseif ($analysis['title']['isAcceptable'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Poor</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['titleLength'] ?? 0 }} chars (Optimal: 50-60 chars)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Meta Description</strong></td>
                                                            <td>
                                                                @if ($analysis['description']['isEmpty'] ?? true)
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Missing</span>
                                                                @elseif ($analysis['description']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                @elseif ($analysis['description']['isAcceptable'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Poor</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['descriptionLength'] ?? 0 }} chars (Optimal: 120-160 chars)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Open Graph</strong></td>
                                                            <td>
                                                                @if ($analysis['openGraph']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Perfect</span>
                                                                @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Basic</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Insufficient</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['openGraphFields'] ?? 0 }} tags configured</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Twitter Cards</strong></td>
                                                            <td>
                                                                @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Perfect</span>
                                                                @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Basic</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Insufficient</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['twitterCardFields'] ?? 0 }} tags configured</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Canonical URL</strong></td>
                                                            <td>
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Configured</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Not Set</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    Duplicate content prevention configured
                                                                @else
                                                                    Configuration required
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Hreflang</strong></td>
                                                            <td>
                                                                @if (($summary['hreflangCount'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">{{ $summary['hreflangCount'] }} tags</span>
                                                                @else
                                                                    <span class="badge">0 tags</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['hreflangCount'] ?? 0 }} language configurations</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on metadata completeness testing performed using <strong>Meta Inspector CLI</strong>.<br>
                                            All data was collected from <u>actual browser rendering environments</u> and evaluated according to SEO best practice standards.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents the metadata state at a specific point in time and may change with website updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website has achieved a grade of
                                                <strong>{{ $grade }}</strong> in metadata completeness testing,
                                                proving it to be a <u>Search Engine Optimization (SEO) excellent site</u>.<br>
                                                This demonstrates that the website is optimized for
                                                <strong>search visibility</strong> and <strong>social media sharing</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed Metadata Status -->
                                    @if ($metadata)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Detailed Metadata Status</h4>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Basic Metadata</h5>
                                                        <div class="mb-3">
                                                            <div class="fw-bold mb-1">Title ({{ $summary['titleLength'] ?? 0 }} chars)</div>
                                                            <div class="text-muted small">{{ $metadata['title'] ?: 'No title' }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="fw-bold mb-1">Description ({{ $summary['descriptionLength'] ?? 0 }} chars)</div>
                                                            <div class="text-muted small">{{ $metadata['description'] ?: 'No description' }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold mb-1">Canonical URL</div>
                                                            <div class="text-muted small">{{ $metadata['canonical'] ?: 'No canonical URL' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (!empty($metadata['openGraph']))
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <h5 class="card-title">Open Graph Tags</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @foreach ($metadata['openGraph'] as $prop => $content)
                                                                            <tr>
                                                                                <td width="30%"><code>og:{{ $prop }}</code></td>
                                                                                <td class="text-break">{{ $content }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($metadata['twitterCards']))
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <h5 class="card-title">Twitter Cards Tags</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @foreach ($metadata['twitterCards'] as $name => $content)
                                                                            <tr>
                                                                                <td width="30%"><code>twitter:{{ $name }}</code></td>
                                                                                <td class="text-break">{{ $content }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($metadata['hreflangs']))
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">Hreflang Configuration</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @foreach ($metadata['hreflangs'] as $hreflang)
                                                                            <tr>
                                                                                <td width="20%">
                                                                                    <code>{{ $hreflang['lang'] }}</code>
                                                                                    @if ($hreflang['lang'] === 'x-default')
                                                                                        <span class="badge bg-primary-lt ms-1">Default</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-break">{{ $hreflang['href'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($results['issues']))
                                        <div class="alert alert-warning d-block">
                                            <strong>⚠️ Issues Found</strong><br>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($results['issues'] as $issue)
                                                    <li>{{ $issue }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Additional Information -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Metadata Matters</strong><br>
                                        - Search Engine Optimization: Proper metadata directly impacts search result visibility and rankings.<br>
                                        - Social Media Sharing: Open Graph and Twitter Cards determine the quality of link previews when shared.<br>
                                        - User Experience: Clear titles and descriptions improve user click-through rates (CTR).<br>
                                        - Duplicate Content Prevention: Canonical URLs prevent search engine penalties.
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Title Tag:</strong> Page title displayed in search results and browser tabs (Optimal: 50-60 chars)</p>
                                        <p class="mb-2"><strong>Meta Description:</strong> Page description displayed in search results (Optimal: 120-160 chars)</p>
                                        <p class="mb-2"><strong>Open Graph:</strong> Social media sharing optimization for Facebook, LinkedIn, etc.</p>
                                        <p class="mb-2"><strong>Twitter Cards:</strong> Card-format optimization for Twitter sharing</p>
                                        <p class="mb-2"><strong>Canonical URL:</strong> Representative URL designation to prevent duplicate content</p>
                                        <p class="mb-0"><strong>Hreflang Tags:</strong> Multi-language page connection configuration</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Meta Inspector service.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides metadata quality measurement services based on international SEO standards,
                                            with certificate authenticity verifiable through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certificate->expires_at->format('Y-m-d') }}</small>
                                        </div>

                                        <div class="signature-line">
                                            <span class="label">Authorized by</span>
                                            <span class="signature">Daniel Ahn</span>
                                            <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                id="tabs-information">
                                <h3>Metadata Completeness Testing Tool</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Meta Inspector CLI</strong> is used to analyze the metadata completeness of web pages.
                                    <br><br>
                                    <strong>📊 Testing Tools and Methods:</strong><br>
                                    • Node.js-based headless browser engine for actual page rendering<br>
                                    • Meta tag extraction and analysis through HTML parsing<br>
                                    • Score calculation based on SEO best practice standards (100 points maximum)<br><br>
                                    
                                    <strong>🎯 Testing Purpose:</strong><br>
                                    • Metadata quality assessment for Search Engine Optimization (SEO)<br>
                                    • Preview quality verification for social media sharing<br>
                                    • Canonical configuration verification for duplicate content prevention<br>
                                    • Hreflang configuration verification for multi-language support
                                </div>
                                {{-- Grade Criteria Guide --}}
                                <div class="table-responsive my-3">
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
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>95~100</td>
                                                <td>Title optimal length (50-60 chars), Description optimal length (120-160 chars)<br>
                                                    Perfect Open Graph implementation, Perfect Twitter Cards implementation<br>
                                                    Accurate Canonical URL, All metadata optimized</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85~94</td>
                                                <td>Title/Description within acceptable range (30-80 chars/80-200 chars)<br>
                                                    Perfect Open Graph implementation, Accurate Canonical URL configuration<br>
                                                    Twitter Cards optional</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75~84</td>
                                                <td>Basic Title/Description written<br>
                                                    Basic Open Graph tags applied<br>
                                                    Some metadata omissions allowed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65~74</td>
                                                <td>Inappropriate Title/Description length<br>
                                                    Incomplete Open Graph (missing key tags)<br>
                                                    Inaccurate or missing Canonical URL</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~64</td>
                                                <td>Serious Title/Description length issues<br>
                                                    Insufficient basic Open Graph tags<br>
                                                    Lack of basic metadata</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>Title/Description not written<br>
                                                    No Open Graph<br>
                                                    Overall metadata not implemented</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Metadata Checklist for SEO Success</strong><br>
                                    - <strong>Title Tag:</strong> 50-60 chars, include core keywords, include brand name<br>
                                    - <strong>Meta Description:</strong> 120-160 chars, include call-to-action phrases<br>
                                    - <strong>Open Graph:</strong> Essential 4 elements: title, description, image, url<br>
                                    - <strong>Twitter Cards:</strong> Basic 3 elements: card, title, description<br>
                                    - <strong>Canonical URL:</strong> Self-referencing canonical recommended for all pages<br>
                                    - <strong>Hreflang:</strong> x-default inclusion mandatory for multi-language sites<br><br>

                                    <strong>🔍 Search Engine Visibility Impact</strong><br>
                                    • Title/Description optimization → Up to 30% CTR improvement<br>
                                    • Open Graph implementation → Up to 40% increase in social sharing<br>
                                    • Canonical configuration → 100% duplicate content penalty prevention<br>
                                    • Comprehensive metadata optimization → Average 20-50% search traffic increase
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                        Copy
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@section('js')
    <script>
        // 전역 스코프에 함수들 정의
        window.copyJsonToClipboard = function() {
            const jsonElement = document.getElementById('json-data');
            if (jsonElement) {
                const text = jsonElement.textContent;

                // Clipboard API를 사용 (최신 브라우저)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        window.showCopySuccess();
                    }).catch(err => {
                        console.error('클립보드 복사 실패:', err);
                        window.fallbackCopyTextToClipboard(text);
                    });
                } else {
                    // fallback (구형 브라우저)
                    window.fallbackCopyTextToClipboard(text);
                }
            }
        };

        window.fallbackCopyTextToClipboard = function(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    window.showCopySuccess();
                } else {
                    window.showCopyError();
                }
            } catch (err) {
                console.error('Fallback: 클립보드 복사 실패', err);
                window.showCopyError();
            }

            document.body.removeChild(textArea);
        };

        window.showCopySuccess = function() {
            const button = document.querySelector('button[onclick="copyJsonToClipboard()"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>복사됨';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }
        };

        window.showCopyError = function() {
            const button = document.querySelector('button[onclick="copyJsonToClipboard()"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-times me-1"></i>실패';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-danger');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }
        };
    </script>
@endsection
