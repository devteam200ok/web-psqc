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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(SSL/TLS 보안 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>항목</th>
                                                            <th>상태</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>최고 TLS 버전</strong></td>
                                                            <td>{{ $tlsVersion }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>완전 순방향 보안 (PFS)</strong></td>
                                                            <td
                                                                class="{{ $forwardSecrecy ? 'text-success' : 'text-danger' }}">
                                                                {{ $forwardSecrecy ? '지원' : '미지원' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ $hstsEnabled ? 'text-success' : 'text-warning' }}">
                                                                {{ $hstsEnabled ? '활성' : '비활성' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>취약점</strong></td>
                                                            <td
                                                                class="{{ $vulnerableCount > 0 ? 'text-danger' : 'text-success' }}">
                                                                {{ $vulnerableCount > 0 ? $vulnerableCount . '개 발견' : '없음' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS 보안 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>testssl.sh</strong>를 통해 수행된 SSL/TLS 보안 시험 결과에 근거합니다.<br>
                                            서버의 SSL/TLS 구성, 지원 프로토콜, 암호화 스위트, 알려진 취약점 등을
                                            포괄적으로 검사하여 측정되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정 변경과 보안 업데이트에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 SSL/TLS 보안 테스트 결과 <strong>{{ $grade }}</strong> 등급을
                                                획득하여
                                                <u>최고 수준의 보안 설정</u>을 입증하였습니다.<br>
                                                이는 <strong>안전한 암호화 통신</strong>과 <strong>최신 보안 표준 준수</strong>를 갖춘 웹사이트임을
                                                보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 상세 보안 정보 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">상세 보안 정보</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">인증서 정보</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>발급자</td>
                                                                    <td>{{ $results['certificate']['issuer'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>유효기간</td>
                                                                    <td>{{ $results['cert_expiry'] ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>서명 알고리즘</td>
                                                                    <td>{{ $results['certificate']['signature_algorithm'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>키 크기</td>
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
                                                                    <th colspan="2">프로토콜 지원</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (isset($results['supported_protocols']) && count($results['supported_protocols']) > 0)
                                                                    <tr>
                                                                        <td>지원 프로토콜</td>
                                                                        <td>{{ implode(', ', $results['supported_protocols']) }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if (isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0)
                                                                    <tr>
                                                                        <td>취약 프로토콜</td>
                                                                        <td class="text-danger">
                                                                            {{ implode(', ', $results['vulnerable_protocols']) }}
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td>취약 프로토콜</td>
                                                                        <td class="text-success">없음</td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <td>IP 주소</td>
                                                                    <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>포트</td>
                                                                    <td>{{ $results['port'] ?? '443' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 취약점 요약 -->
                                    @if ($vulnerableCount > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">발견된 취약점</h4>
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
                                                    <strong>{{ $vulnerableCount }}개의 취약점이 발견되었습니다:</strong>
                                                    {{ implode(', ', $vulnList) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>보안 수준:</strong>
                                        @if ($grade === 'A+')
                                            최고 수준의 보안 설정 (모든 최신 표준 준수)
                                        @elseif ($grade === 'A')
                                            우수한 보안 설정 (대부분의 표준 준수)
                                        @elseif ($grade === 'B')
                                            양호한 보안 설정 (일부 개선 필요)
                                        @else
                                            보안 설정 개선 필요
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>testssl.sh:</strong> GitHub 10,000+ 스타의 오픈소스 SSL/TLS
                                            테스터로 업계 표준 도구</p>
                                        <p class="mb-2"><strong>완전 순방향 보안(PFS):</strong> 과거 통신 내용이 미래에 해독되는 것을 방지하는
                                            보안 기능</p>
                                        <p class="mb-0"><strong>HSTS:</strong> HTTP Strict Transport Security로 HTTPS
                                            연결을 강제하는 보안 헤더</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 SSL/TLS Security Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제적 기준에 근거한 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>SSL/TLS 보안 테스트 검증 환경</h3>
                                <div class="text-muted small mt-1">
                                    testssl.sh는 SSL/TLS 구성을 종합적으로 검사하는 오픈소스 도구로,
                                    웹사이트의 HTTPS 보안 설정을 정밀하게 분석합니다.
                                    <br><br>
                                    • <strong>검사 도구</strong>: testssl.sh (GitHub 10,000+ 스타 오픈소스 프로젝트)<br>
                                    • <strong>검사 항목</strong>: SSL/TLS 프로토콜, 암호화 스위트, 인증서, 알려진 취약점<br>
                                    • <strong>취약점 검사</strong>: Heartbleed, POODLE, BEAST, CRIME, FREAK 등 주요 취약점<br>
                                    • <strong>보안 기능</strong>: PFS, HSTS, OCSP Stapling 등 최신 보안 기능 지원 여부
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>보안 기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>최신 TLS만 사용, 취약점 없음<br>강력한 암호화 스위트 적용<br>인증서 및 체인 완전 정상<br>HSTS 등 보안
                                                    설정 우수</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.2/1.3 지원, 구버전 차단<br>주요 취약점 없음<br>일부 약한 암호나 설정 미흡 가능<br>전반적으로
                                                    안전한 수준</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>안전한 프로토콜 위주<br>약한 암호 스위트 일부 존재<br>경고(WEAK) 다수<br>개선 필요</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>구버전 TLS 일부 활성<br>취약 암호화 사용률 높음<br>인증서 만료 임박/단순 DV<br>취약점 소수 발견</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>SSLv3/TLS 1.0 허용<br>취약 암호 다수 활성<br>인증서 체인 오류/만료 임박<br>다수 취약점 존재</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>SSL/TLS 설정 근본적 결함<br>취약 프로토콜 전면 허용<br>인증서 만료/자가서명<br>FAIL/VULNERABLE
                                                    다수</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 주요 검사 항목</strong><br>
                                    - <strong>SSL/TLS 프로토콜</strong>: SSL 2.0/3.0, TLS 1.0~1.3 지원 여부 검사<br>
                                    - <strong>암호화 스위트</strong>: 지원 알고리즘, PFS, 약한 암호화 탐지<br>
                                    - <strong>SSL 인증서</strong>: 유효성, 만료일, 체인 완전성, OCSP Stapling<br>
                                    - <strong>보안 취약점</strong>: Heartbleed, POODLE, BEAST, CRIME, FREAK 등<br><br>

                                    <strong>🌍 왜 SSL/TLS 검사가 중요한가</strong><br>
                                    • <strong>데이터 보호</strong>: 사용자와 서버 간 전송되는 모든 데이터의 암호화 품질 보장<br>
                                    • <strong>신뢰성 확보</strong>: 브라우저 경고 없이 안전한 HTTPS 연결 제공<br>
                                    • <strong>규정 준수</strong>: GDPR, PCI-DSS 등 보안 규정 요구사항 충족<br>
                                    • <strong>SEO 향상</strong>: 검색엔진에서 HTTPS 사이트 우대<br><br>

                                    <strong>📊 보안 개선 권장사항</strong><br>
                                    - 구버전 프로토콜(SSL 2.0/3.0, TLS 1.0/1.1) 완전 비활성화<br>
                                    - 강력한 암호화 스위트(AES-GCM, ChaCha20-Poly1305) 사용<br>
                                    - HSTS, OCSP Stapling 등 보안 기능 활성화<br>
                                    - 정기적인 보안 업데이트 및 인증서 관리
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw testssl.sh Output</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="데이터 복사">
                                        복사
                                    </button>
                                </div>
                                <pre class="bg-dark text-light p-3 rounded json-dump" id="json-data"
                                    style="max-height: 600px; overflow-y: auto; font-size: 11px; line-height: 1.2;">{{ $currentTest->results['raw_output'] ?? '데이터 없음' }}</pre>
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(SSL/TLS 심층 분석)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>항목</th>
                                                            <th>상태</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>TLS 버전</strong></td>
                                                            <td>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)
                                                                    TLS 1.3 지원
                                                                @elseif ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                                                    TLS 1.2 (1.3 미지원)
                                                                @else
                                                                    구버전만 지원
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>PFS 비율</strong></td>
                                                            <td>{{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}%
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>OCSP Stapling</strong></td>
                                                            <td
                                                                class="{{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'text-success' : 'text-danger' }}">
                                                                {{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? '활성' : '비활성' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ !empty($analysis['http_headers']['hsts']) ? 'text-success' : 'text-danger' }}">
                                                                {{ !empty($analysis['http_headers']['hsts']) ? '설정됨' : '미설정' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS 심층 분석 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>SSLyze v5.x</strong>를 통해 수행된 SSL/TLS 심층 보안 분석 결과에 근거합니다.<br>
                                            TLS 프로토콜 버전, 암호군 강도, 인증서 체인, OCSP Stapling, HTTP 보안 헤더 등을
                                            종합적으로 검사하여 측정되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정 변경과 보안 업데이트에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 SSL/TLS 심층 분석 결과 <strong>{{ $grade }}</strong> 등급을
                                                획득하여
                                                <u>최고 수준의 암호화 보안</u>을 입증하였습니다.<br>
                                                이는 <strong>최신 TLS 프로토콜</strong>과 <strong>강력한 암호군 설정</strong>을 갖춘 웹사이트임을
                                                보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 상세 분석 결과 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">상세 분석 결과</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">암호군 분석</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['cipher_suites']['tls_1_2']))
                                                                    <tr>
                                                                        <td>TLS 1.2 암호군</td>
                                                                        <td>{{ $analysis['cipher_suites']['tls_1_2']['total'] ?? 0 }}개
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>강한 암호</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['strong'] ?? 0 }}개
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>약한 암호</td>
                                                                        <td
                                                                            class="{{ ($analysis['cipher_suites']['tls_1_2']['weak'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['weak'] ?? 0 }}개
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if (!empty($analysis['cipher_suites']['tls_1_3']))
                                                                    <tr>
                                                                        <td>TLS 1.3 암호군</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_3']['total'] ?? 0 }}개
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
                                                                    <th colspan="2">인증서 정보</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['certificate']['details']))
                                                                    <tr>
                                                                        <td>키 알고리즘</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_algorithm'] ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>키 크기</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_size'] ?? 'N/A' }}비트
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>서명 알고리즘</td>
                                                                        <td>{{ $analysis['certificate']['details']['signature_algorithm'] ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>만료까지</td>
                                                                        <td
                                                                            class="{{ ($analysis['certificate']['details']['days_to_expiry'] ?? 31) <= 30 ? 'text-warning' : '' }}">
                                                                            {{ $analysis['certificate']['details']['days_to_expiry'] ?? 'N/A' }}일
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

                                    <!-- 발견된 이슈 -->
                                    @if (!empty($issues))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">발견된 보안 이슈</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($issues) }}개의 이슈가 발견되었습니다:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach (array_slice($issues, 0, 5) as $issue)
                                                            <li>{{ $issue }}</li>
                                                        @endforeach
                                                        @if (count($issues) > 5)
                                                            <li>외 {{ count($issues) - 5 }}개...</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>보안 수준:</strong>
                                        @if ($grade === 'A+')
                                            최고 수준의 SSL/TLS 보안 설정 (TLS 1.3, 강한 암호군, 완벽한 보안 헤더)
                                        @elseif ($grade === 'A')
                                            우수한 SSL/TLS 보안 설정 (TLS 1.2+, 대부분 강한 암호군)
                                        @elseif ($grade === 'B')
                                            양호한 SSL/TLS 보안 설정 (일부 개선 필요)
                                        @else
                                            SSL/TLS 보안 설정 개선 필요
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>SSLyze:</strong> Mozilla, Qualys, IETF가 권장하는 오픈소스
                                            SSL/TLS 스캐너</p>
                                        <p class="mb-2"><strong>PFS:</strong> Perfect Forward Secrecy - 과거 통신 내용의 미래
                                            해독 방지</p>
                                        <p class="mb-0"><strong>OCSP Stapling:</strong> 인증서 폐기 상태를 효율적으로 확인하는 메커니즘
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 SSLyze Deep Analysis를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제적 기준에 근거한 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>SSLyze SSL/TLS 심층 분석 검증 환경</h3>
                                <div class="text-muted small mt-1">
                                    SSLyze v5.x는 Mozilla, Qualys, IETF 등이 권장하는 오픈소스 SSL/TLS 스캐너로,
                                    웹사이트의 SSL/TLS 설정을 종합적으로 진단합니다.
                                    <br><br>
                                    • <strong>검사 도구</strong>: SSLyze v5.x - 업계 표준 SSL/TLS 분석 도구<br>
                                    • <strong>TLS 프로토콜</strong>: SSL 2.0/3.0, TLS 1.0/1.1/1.2/1.3 지원 여부<br>
                                    • <strong>암호군 분석</strong>: 강도, PFS 지원, 약한 암호 검출<br>
                                    • <strong>인증서 체인</strong>: 유효성, 만료일, 서명 알고리즘, 키 크기<br>
                                    • <strong>보안 기능</strong>: OCSP Stapling, HSTS, 타원곡선 암호
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>TLS 1.3/1.2만 허용, 약한 암호군 없음(전부 PFS)<br>
                                                    인증서 ECDSA 또는 RSA≥3072, 체인 완전·만료 60일↑<br>
                                                    OCSP Stapling 정상(가능시 Must-Staple)<br>
                                                    HSTS 활성, max-age ≥ 1년, includeSubDomains, preload</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.3/1.2, 강한 암호 우선(PFS 대부분)<br>
                                                    인증서 RSA≥2048, SHA-256+, 체인 정상·만료 30일↑<br>
                                                    OCSP Stapling 활성(간헐 실패 허용)<br>
                                                    HSTS 활성, max-age ≥ 6개월</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>65~79</td>
                                                <td>TLS 1.2 필수, 1.3 선택/미지원, 일부 CBC 존재<br>
                                                    인증서 RSA≥2048, 체인 정상(만료 14일↑)<br>
                                                    OCSP Stapling 미활성(대신 OCSP 응답 가능)<br>
                                                    HSTS 설정 있으나 일부 미흡</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>50~64</td>
                                                <td>TLS 1.0/1.1 활성 또는 약한 암호 다수(PFS 낮음)<br>
                                                    체인 누락/약한 서명(SHA-1) 또는 만료 임박(≤14일)<br>
                                                    Stapling 없음·폐기 확인 불명확<br>
                                                    HSTS 미설정</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>35~49</td>
                                                <td>구식 프로토콜/암호(SSLv3/EXPORT/RC4 등) 허용<br>
                                                    인증서 불일치/체인 오류 빈발<br>
                                                    Stapling 실패·폐기 확인 불능<br>
                                                    보안 헤더 전반적 미흡</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~34</td>
                                                <td>핸드셰이크 실패 수준의 결함<br>
                                                    만료/자가서명/호스트 불일치<br>
                                                    광범위한 약한 프로토콜·암호 허용<br>
                                                    전반적 TLS 설정 붕괴</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 SSLyze 심층 분석 특징</strong><br>
                                    - <strong>종합적 검사</strong>: TLS 프로토콜, 암호군, 인증서, 보안 헤더 전체 분석<br>
                                    - <strong>정밀한 진단</strong>: 각 암호군의 강도와 PFS 지원 여부 개별 평가<br>
                                    - <strong>실시간 검증</strong>: OCSP Stapling과 인증서 체인 실시간 확인<br>
                                    - <strong>타원곡선 분석</strong>: 지원하는 타원곡선 목록과 강도 평가<br><br>

                                    <strong>🌍 왜 SSLyze 심층 분석이 중요한가</strong><br>
                                    • <strong>세밀한 보안 진단</strong>: 단순 등급을 넘어 구체적 취약점 식별<br>
                                    • <strong>최신 표준 준수</strong>: TLS 1.3 지원 등 최신 보안 요구사항 확인<br>
                                    • <strong>성능 최적화</strong>: 불필요한 약한 암호 제거로 핸드셰이크 성능 개선<br>
                                    • <strong>규정 준수 검증</strong>: PCI-DSS, HIPAA 등 규정 요구사항 충족 확인<br><br>

                                    <strong>📊 보안 개선 권장사항</strong><br>
                                    - TLS 1.3 활성화 및 TLS 1.0/1.1 완전 비활성화<br>
                                    - PFS 지원 ECDHE/DHE 암호군만 사용<br>
                                    - RSA 최소 3072비트 또는 ECDSA 256비트 인증서 사용<br>
                                    - OCSP Stapling과 HSTS 헤더 필수 설정
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(보안 헤더 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>항목</th>
                                                            <th>상태</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>적용된 헤더</strong></td>
                                                            <td>{{ $presentHeaders }}/6개</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>CSP</strong></td>
                                                            <td
                                                                class="{{ $csp['present'] ?? false ? ($csp['strong'] ?? false ? 'text-success' : 'text-warning') : 'text-danger' }}">
                                                                @if ($csp['present'] ?? false)
                                                                    {{ $csp['strong'] ?? false ? '강함' : '약함' }}
                                                                @else
                                                                    없음
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td
                                                                class="{{ $hsts['present'] ?? false ? 'text-success' : 'text-danger' }}">
                                                                @if ($hsts['present'] ?? false)
                                                                    설정됨
                                                                    ({{ number_format(($hsts['max_age'] ?? 0) / 86400) }}일)
                                                                @else
                                                                    없음
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
                                                                            $xfo = $header['value'] ?? '없음';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ $xfo ?: '없음' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 보안 헤더 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>6대 핵심 보안 헤더</strong> 종합 검사를 통해 수행된 웹 보안 시험 결과에 근거합니다.<br>
                                            CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy,
                                            Permissions-Policy, HSTS 등
                                            주요 HTTP 보안 헤더를 검사하여 측정되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정 변경에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 보안 헤더 테스트 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>우수한 웹 보안 설정</u>을 입증하였습니다.<br>
                                                이는 <strong>XSS, 클릭재킹, MIME 스니핑</strong> 등 주요 웹 취약점에 대한 <strong>강력한 방어
                                                    체계</strong>를 갖춘 웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 헤더별 점수 상세 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">헤더별 점수 분석</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>헤더</th>
                                                            <th>값</th>
                                                            <th>점수</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($metrics['breakdown'] ?? [] as $item)
                                                            <tr>
                                                                <td><strong>{{ $item['key'] }}</strong></td>
                                                                <td class="text-truncate" style="max-width: 400px;"
                                                                    title="{{ $item['value'] ?? '(설정되지 않음)' }}">
                                                                    {{ $item['value'] ?? '(설정되지 않음)' }}
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

                                    <!-- 등급 사유 -->
                                    @if (!empty($report['reasons']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <strong>등급 평가 사유:</strong><br>
                                                    {{ implode(' · ', $report['reasons']) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>보안 수준:</strong>
                                        @if ($grade === 'A+')
                                            최고 수준의 보안 헤더 설정 (강한 CSP 포함 모든 헤더 적용)
                                        @elseif ($grade === 'A')
                                            우수한 보안 헤더 설정 (대부분의 헤더 적용)
                                        @elseif ($grade === 'B')
                                            양호한 보안 헤더 설정 (핵심 헤더 적용)
                                        @else
                                            보안 헤더 설정 개선 필요
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>CSP:</strong> XSS 공격과 데이터 주입 공격을 방어하는 가장 강력한 보안 메커니즘
                                        </p>
                                        <p class="mb-2"><strong>X-Frame-Options:</strong> 클릭재킹 공격 방지를 위한 iframe 삽입 차단
                                        </p>
                                        <p class="mb-0"><strong>HSTS:</strong> HTTPS 강제 연결로 중간자 공격과 프로토콜 다운그레이드 방지
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Security Headers Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제적 기준에 근거한 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>6대 핵심 보안 헤더 종합 검사</h3>
                                <div class="text-muted small mt-1">
                                    HTTP 응답 헤더를 통해 브라우저의 보안 기능을 활성화하여 웹 애플리케이션을 다양한 공격으로부터 보호합니다.
                                    <br><br>
                                    • <strong>Content-Security-Policy (CSP)</strong>: 리소스 로드 출처 제한, XSS·서드파티 스크립트 악용
                                    방지<br>
                                    • <strong>X-Frame-Options</strong>: iframe 삽입 차단, 클릭재킹·피싱형 오버레이 방지<br>
                                    • <strong>X-Content-Type-Options</strong>: MIME 스니핑 차단, 잘못된 실행 취약점 방어<br>
                                    • <strong>Referrer-Policy</strong>: 외부 전송 시 URL 정보 최소화, 개인정보·내부경로 노출 방지<br>
                                    • <strong>Permissions-Policy</strong>: 위치·마이크·카메라 등 브라우저 기능 제한, 프라이버시 보호<br>
                                    • <strong>Strict-Transport-Security (HSTS)</strong>: HTTPS 강제, 중간자 공격·다운그레이드 방지
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>95-100</td>
                                                <td>CSP 강함(nonce/hash/strict-dynamic, unsafe-* 미사용)<br>
                                                    XFO: DENY/SAMEORIGIN 또는 frame-ancestors 제한<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: strict-origin-when-cross-origin 이상<br>
                                                    Permissions-Policy: 불필요 기능 차단<br>
                                                    HSTS: 6개월↑ + 서브도메인</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>85-94</td>
                                                <td>CSP 존재(약함 허용) 또는 비-CSP 5항목 우수<br>
                                                    XFO 적용(또는 frame-ancestors 제한)<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: 권장 값 사용<br>
                                                    Permissions-Policy: 기본 제한 적용<br>
                                                    HSTS: 6개월↑</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70-84</td>
                                                <td>CSP 없음/약함<br>
                                                    XFO 정상 적용<br>
                                                    X-Content-Type: 있음<br>
                                                    Referrer-Policy: 양호/보통<br>
                                                    Permissions-Policy: 일부 제한<br>
                                                    HSTS: 단기 또는 서브도메인 미포함</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>55-69</td>
                                                <td>헤더 일부만 존재<br>
                                                    CSP 없음/약함<br>
                                                    Referrer-Policy 약함<br>
                                                    X-Content-Type 누락<br>
                                                    HSTS 없음 또는 매우 짧음</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>40-54</td>
                                                <td>핵심 헤더 1~2개만<br>
                                                    CSP 없음<br>
                                                    Referrer 약함/없음<br>
                                                    기타 헤더 다수 누락</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0-39</td>
                                                <td>보안 헤더 전무에 가까움<br>
                                                    CSP/XFO/X-Content 없음<br>
                                                    Referrer-Policy 없음<br>
                                                    HSTS 없음</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 보안 헤더의 중요성</strong><br>
                                    - <strong>XSS 방어</strong>: CSP는 스크립트 주입 공격을 원천 차단<br>
                                    - <strong>클릭재킹 방지</strong>: X-Frame-Options로 악의적 iframe 삽입 차단<br>
                                    - <strong>MIME 스니핑 방어</strong>: X-Content-Type-Options로 파일 타입 위장 방지<br>
                                    - <strong>정보 유출 차단</strong>: Referrer-Policy로 민감한 URL 정보 보호<br><br>

                                    <strong>🌍 설정 위치</strong><br>
                                    • <strong>CDN 레벨</strong>: Cloudflare, CloudFront 등에서 설정<br>
                                    • <strong>웹서버 레벨</strong>: Nginx, Apache 설정 파일<br>
                                    • <strong>애플리케이션 레벨</strong>: Laravel, Express.js 등 미들웨어<br><br>

                                    <strong>📊 등급 정책</strong><br>
                                    - A+ 등급은 강한 CSP가 필수<br>
                                    - CSP 없어도 다른 5개 헤더가 우수하면 A 등급 가능<br>
                                    - 모든 헤더가 함께 적용될 때 가장 강력한 보안 효과
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(보안 취약점 스캔)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>항목</th>
                                                            <th>수량</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['critical'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['high'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td
                                                                class="{{ ($vulnerabilities['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $vulnerabilities['medium'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($vulnerabilities['low'] ?? 0) + ($vulnerabilities['informational'] ?? 0) }}개
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 보안 취약점 스캔 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>OWASP ZAP</strong> 패시브 스캔을 통해 수행된 웹 보안 취약점 분석 결과에 근거합니다.<br>
                                            HTTP 응답 분석을 통해 보안 헤더, 민감정보 노출, 세션 관리, 잠재적 취약점 등을
                                            비침입적으로 검사하여 측정되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 웹사이트 업데이트와 보안 패치에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 보안 취약점 스캔 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>우수한 보안 수준</u>을 입증하였습니다.<br>
                                                이는 <strong>주요 보안 취약점이 없고</strong> <strong>안전한 구성</strong>을 갖춘 웹사이트임을
                                                보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 취약점 요약 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">취약점 분석 결과</h4>
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

                                    <!-- 주요 발견사항 -->
                                    @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">주요 발견사항</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($vulnerabilities['details']) }}개의 보안 이슈가
                                                        발견되었습니다.</strong>
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
                                                            <li>외 {{ count($vulnerabilities['details']) - 5 }}개...
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 발견된 기술 -->
                                    @if (isset($technologies) && count($technologies) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">탐지된 기술 스택</h4>
                                                <div>
                                                    @foreach (array_slice($technologies, 0, 10) as $tech)
                                                        <span
                                                            class="badge bg-azure-lt text-azure-lt-fg me-1 mb-1">{{ $tech['name'] }}</span>
                                                    @endforeach
                                                    @if (count($technologies) > 10)
                                                        <span
                                                            class="badge bg-secondary me-1 mb-1">+{{ count($technologies) - 10 }}개</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>보안 수준:</strong>
                                        @if ($grade === 'A+')
                                            최고 수준의 보안 (Critical/High 취약점 없음, 보안 헤더 완비)
                                        @elseif ($grade === 'A')
                                            우수한 보안 (Critical 없음, High 최소, 보안 설정 양호)
                                        @elseif ($grade === 'B')
                                            양호한 보안 (일부 개선 필요)
                                        @else
                                            보안 개선 필요
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>OWASP ZAP:</strong> 세계에서 가장 널리 사용되는 오픈소스 웹 보안 테스팅 도구
                                        </p>
                                        <p class="mb-2"><strong>패시브 스캔:</strong> 실제 공격 없이 HTTP 응답만 분석하는 비침입적 검사</p>
                                        <p class="mb-0"><strong>검사 범위:</strong> 보안 헤더, 민감정보 노출, 세션 관리, 기술 스택 탐지</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 OWASP ZAP Security Scan을 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제적 기준에 근거한 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>OWASP ZAP 패시브 스캔 - 비침입적 보안 취약점 분석</h3>
                                <div class="text-muted small mt-1">
                                    OWASP ZAP (Zed Attack Proxy)는 세계에서 가장 널리 사용되는 오픈소스 웹 애플리케이션 보안 테스팅 도구입니다.
                                    <br><br>
                                    • <strong>측정 도구</strong>: OWASP ZAP - 업계 표준 웹 보안 테스팅 도구<br>
                                    • <strong>테스트 방식</strong>: 패시브 스캔 (실제 공격 없이 HTTP 응답만 분석)<br>
                                    • <strong>검사 항목</strong>: 보안 헤더, 민감정보 노출, 세션 관리, 잠재적 인젝션 포인트<br>
                                    • <strong>기술 스택 탐지</strong>: 사용 중인 서버, 프레임워크, 라이브러리 식별<br>
                                    • <strong>소요 시간</strong>: 약 10-20초
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>High/Medium 0개<br>보안 헤더 완비 (HTTPS, HSTS, X-Frame-Options 등)<br>민감정보
                                                    노출 없음<br>서버/프레임워크 버전 정보 최소화</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>High 0, Medium ≤1<br>보안 헤더 대부분 충족<br>민감정보 노출 없음<br>경미한 정보 노출 존재</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>High ≤1, Medium ≤2<br>일부 보안 헤더 미구현<br>세션 쿠키 Secure/HttpOnly
                                                    누락<br>경미한 내부 식별자 노출</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>High ≥2 또는 Medium ≥3<br>주요 보안 헤더 부재<br>민감 파라미터/토큰 노출<br>세션 관리 취약
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Critical ≥1 또는 High ≥3<br>인증/세션 관련 심각한 속성 누락<br>디버그/개발용 정보 노출<br>공개
                                                    관리 콘솔/설정 파일 노출</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>광범위한 High 취약점<br>HTTPS 미적용 또는 전면 무력화<br>민감 데이터 평문 전송/노출<br>전반적 보안
                                                    헤더·세션 통제 부재</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 OWASP ZAP 패시브 스캔의 특징</strong><br>
                                    - <strong>비침입적 검사</strong>: 실제 공격 없이 HTTP 응답만 분석<br>
                                    - <strong>빠른 검사</strong>: 10-20초 내 주요 취약점 식별<br>
                                    - <strong>안전한 테스트</strong>: 서비스 영향 없이 보안 수준 평가<br>
                                    - <strong>종합적 분석</strong>: 보안 헤더, 세션, 정보 노출 등 다각도 검사<br><br>

                                    <strong>🌍 취약점 위험도 분류</strong><br>
                                    • <strong>Critical</strong>: 즉각 조치 필요 (SQL Injection, XSS, RCE)<br>
                                    • <strong>High</strong>: 빠른 수정 필요 (세션 관리 취약, CSRF)<br>
                                    • <strong>Medium</strong>: 개선 권장 (보안 헤더 누락)<br>
                                    • <strong>Low</strong>: 낮은 위험도 (정보 노출, 구성 문제)<br>
                                    • <strong>Info</strong>: 참고 사항<br><br>

                                    <strong>📊 보안 개선 권장사항</strong><br>
                                    - 보안 헤더 설정 (HSTS, X-Frame-Options, X-Content-Type-Options)<br>
                                    - 쿠키에 Secure, HttpOnly, SameSite 속성 설정<br>
                                    - 서버 버전, 디버그 메시지 등 정보 노출 차단<br>
                                    - 월 1회 이상 정기적인 보안 스캔 실행
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(최신 CVE 취약점 스캔)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>항목</th>
                                                            <th>수량</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['high'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td
                                                                class="{{ ($metrics['vulnerability_counts']['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($metrics['vulnerability_counts']['low'] ?? 0) + ($metrics['vulnerability_counts']['info'] ?? 0) }}개
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 최신 CVE 취약점 스캔 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Nuclei by ProjectDiscovery</strong>를 통해 수행된 최신 CVE 취약점 분석 결과에
                                            근거합니다.<br>
                                            2024-2025년 신규 발표된 CVE, 제로데이 취약점, 구성 오류, 민감정보 노출 등을
                                            템플릿 기반으로 정밀 검사하여 측정되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 보안 패치와 업데이트에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 최신 CVE 취약점 스캔 결과 <strong>{{ $grade }}</strong> 등급을
                                                획득하여
                                                <u>최신 보안 위협에 대한 우수한 대응</u>을 입증하였습니다.<br>
                                                이는 <strong>2024-2025년 CVE 패치</strong>와 <strong>안전한 구성 관리</strong>를 갖춘
                                                웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 취약점 요약 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">취약점 분석 결과</h4>
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
                                                    스캔 시간: {{ $metrics['scan_duration'] }}초 |
                                                    매칭된 템플릿: {{ $metrics['templates_matched'] ?? 0 }}개
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Critical/High 취약점 -->
                                    @php
                                        $criticalHighCount = 0;
                                        foreach (['critical', 'high'] as $severity) {
                                            $criticalHighCount += count($vulnerabilities[$severity] ?? []);
                                        }
                                    @endphp

                                    @if ($criticalHighCount > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Critical/High 취약점</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ $criticalHighCount }}개의 고위험 취약점이 발견되었습니다.</strong>
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
                                                            <li>외 {{ $criticalHighCount - 6 }}개...</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>보안 수준:</strong>
                                        @if ($grade === 'A+')
                                            최고 수준의 보안 (Critical/High 0개, 2024-2025 CVE 미검출)
                                        @elseif ($grade === 'A')
                                            우수한 보안 (최신 CVE 직접 노출 없음, 패치 관리 양호)
                                        @elseif ($grade === 'B')
                                            양호한 보안 (일부 구성 개선 필요)
                                        @else
                                            보안 개선 필요
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Nuclei:</strong> ProjectDiscovery의 업계 표준 취약점 스캐너, 템플릿
                                            기반 빠른 스캔</p>
                                        <p class="mb-2"><strong>CVE 커버리지:</strong> 2024-2025년 신규 CVE, Log4Shell,
                                            Spring4Shell 등 주요 취약점</p>
                                        <p class="mb-0"><strong>검사 범위:</strong> WordPress/Joomla/Drupal 플러그인, Git/ENV
                                            노출, API 엔드포인트</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Nuclei CVE Scan을 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제적 기준에 근거한 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>Nuclei 기반 최신 CVE 취약점 자동 탐지</h3>
                                <div class="text-muted small mt-1">
                                    Nuclei by ProjectDiscovery는 업계 표준 취약점 스캐너로 템플릿 기반 빠른 스캔을 제공합니다.
                                    <br><br>
                                    • <strong>측정 도구</strong>: Nuclei - 템플릿 기반 취약점 스캐너<br>
                                    • <strong>테스트 범위</strong>: 2024-2025년 신규 발표 CVE 취약점<br>
                                    • <strong>검사 항목</strong>: 제로데이, 구성 오류, 민감정보 노출, 백업 파일<br>
                                    • <strong>주요 취약점</strong>: Log4Shell, Spring4Shell 같은 주요 RCE<br>
                                    • <strong>소요 시간</strong>: 약 30초-3분 (템플릿 수에 따라 변동)
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Critical/High 0개, Medium 0개<br>2024-2025 CVE 미검출<br>공개 디렉터리/디버그/민감파일
                                                    노출 없음<br>보안 헤더/배너 노출 양호</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>High ≤1, Medium ≤1<br>최근 CVE 직접 노출 없음<br>경미한 설정 경고 수준<br>패치/구성 관리 양호
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>High ≤2 또는 Medium ≤3<br>일부 구성 노출/배너 노출 존재<br>보호된 관리 엔드포인트 존재<br>패치
                                                    지연 경향</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>High ≥3 또는 Medium 다수<br>민감 파일/백업/인덱싱 노출 발견<br>구버전 컴포넌트 추정
                                                    가능<br>패치/구성 관리 체계적 개선 필요</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Critical ≥1 또는 악용 난이도 낮은 High<br>최근 (2024-2025) CVE 직접 영향 추정<br>인증
                                                    없이 접근 가능한 위험 엔드포인트<br>빌드/로그/환경 등 민감 정보 노출</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>다수의 Critical/High 동시 존재<br>최신 CVE 대량 미패치/광범위 노출<br>기본 보안 구성
                                                    결여<br>전면적 보안 가드레일 부재</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Nuclei 스캔의 특징</strong><br>
                                    - <strong>템플릿 기반</strong>: YAML 템플릿으로 정확한 취약점 식별<br>
                                    - <strong>비침투적</strong>: 실제 공격 없이 시그니처만 확인<br>
                                    - <strong>빠른 스캔</strong>: 최적화된 템플릿으로 30초-3분 내 완료<br>
                                    - <strong>최신 CVE</strong>: 2024-2025년 신규 취약점 즉시 반영<br><br>

                                    <strong>🌍 최신 취약점 커버리지</strong><br>
                                    • <strong>주요 RCE</strong>: Log4Shell, Spring4Shell 등<br>
                                    • <strong>CMS 플러그인</strong>: WordPress, Joomla, Drupal<br>
                                    • <strong>웹서버 설정</strong>: Apache, Nginx, IIS<br>
                                    • <strong>노출 탐지</strong>: Git, SVN, ENV 파일<br>
                                    • <strong>API 취약점</strong>: GraphQL, REST API<br>
                                    • <strong>클라우드</strong>: AWS, Azure, GCP 설정 오류<br><br>

                                    <strong>📊 보안 개선 권장사항</strong><br>
                                    - Critical/High 취약점 즉시 패치<br>
                                    - CMS, 플러그인, 프레임워크 최신 버전 유지<br>
                                    - 불필요한 서비스 비활성화, 디버그 모드 제거<br>
                                    - 월 1회 이상 정기 취약점 스캔 실행
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(Google Lighthouse 품질 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Google Lighthouse 엔진</strong>을 통해 수행된 웹 품질 시험 결과에 근거합니다.<br>
                                            모든 데이터는 <u>실제 브라우저 환경을 시뮬레이션</u>하여 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해
                                            누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 Google Lighthouse 품질 측정 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>상위 10% 이내의 웹 품질 수준</u>을 입증하였습니다.<br>
                                                이는 <strong>우수한 성능</strong>과 <strong>높은 접근성, SEO 최적화</strong>를 갖춘
                                                고품질 웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Core Web Vitals -->
                                    @if(isset($results['audits']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Core Web Vitals 측정 결과</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>지표</th>
                                                                <th>측정값</th>
                                                                <th>권장 기준</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($results['audits']['first-contentful-paint']))
                                                                <tr>
                                                                    <td><strong>First Contentful Paint (FCP)</strong></td>
                                                                    <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">1.8초 이내</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['largest-contentful-paint']))
                                                                <tr>
                                                                    <td><strong>Largest Contentful Paint (LCP)</strong></td>
                                                                    <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">2.5초 이내</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['cumulative-layout-shift']))
                                                                <tr>
                                                                    <td><strong>Cumulative Layout Shift (CLS)</strong></td>
                                                                    <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">0.1 이하</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['speed-index']))
                                                                <tr>
                                                                    <td><strong>Speed Index</strong></td>
                                                                    <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">3.4초 이내</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['total-blocking-time']))
                                                                <tr>
                                                                    <td><strong>Total Blocking Time (TBT)</strong></td>
                                                                    <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">200ms 이내</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['interactive']))
                                                                <tr>
                                                                    <td><strong>Time to Interactive (TTI)</strong></td>
                                                                    <td>{{ $results['audits']['interactive']['displayValue'] ?? 'N/A' }}</td>
                                                                    <td class="text-muted">3.8초 이내</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 개선 기회 -->
                                        @php
                                            $opportunities = collect($results['audits'])->filter(function($audit) {
                                                return isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 0;
                                            })->sortByDesc('details.overallSavingsMs');
                                        @endphp
                                        @if($opportunities->count() > 0)
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h4 class="mb-3">개선 기회 분석</h4>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>개선 항목</th>
                                                                    <th>예상 개선 효과</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($opportunities->take(5) as $key => $opportunity)
                                                                    <tr>
                                                                        <td>{{ $opportunity['title'] ?? $key }}</td>
                                                                        <td>{{ round($opportunity['details']['overallSavingsMs'] ?? 0) }}ms 단축 가능</td>
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
                                        <strong>4대 평가 영역:</strong> Performance (성능), Accessibility (접근성), Best Practices (모범 사례), SEO (검색 최적화)<br>
                                        <span class="text-muted">각 영역은 100점 만점으로 평가되며, 종합 점수는 4개 영역의 가중 평균입니다.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>FCP:</strong> 페이지 로드 시작부터 첫 콘텐츠가 화면에 표시되는 시간</p>
                                        <p class="mb-2"><strong>LCP:</strong> 가장 큰 콘텐츠 요소가 화면에 렌더링되는 시점</p>
                                        <p class="mb-2"><strong>CLS:</strong> 페이지 로드 중 발생하는 예상치 못한 레이아웃 이동의 누적 점수</p>
                                        <p class="mb-0"><strong>TBT:</strong> 메인 스레드가 차단되어 사용자 입력에 응답할 수 없는 시간</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Lighthouse Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 Google Lighthouse 엔진 기반의 웹 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>Google Lighthouse - 웹사이트 종합 품질 측정 도구</h3>
                                <div class="text-muted small mt-1">
                                    Google Lighthouse는 구글이 개발한 오픈소스 웹 품질 측정 도구로, Chrome DevTools에 내장되어 있으며
                                    웹사이트의 성능, 접근성, SEO, 모범 사례 준수 여부를 종합적으로 분석합니다.
                                    <br><br>
                                    <strong>측정 도구 및 환경</strong><br>
                                    • Lighthouse 최신 버전 (Chrome 브라우저 엔진 기반)<br>
                                    • Headless Chrome으로 실제 브라우저 환경 시뮬레이션<br>
                                    • 모바일 3G/4G 네트워크 및 중급 성능 디바이스 기준 측정<br>
                                    • 실제 사용자 경험을 반영한 Core Web Vitals 측정
                                    <br><br>
                                    <strong>4대 평가 영역</strong><br>
                                    1. <strong>Performance (성능)</strong>: 페이지 로딩 속도, Core Web Vitals, 리소스 최적화<br>
                                    2. <strong>Accessibility (접근성)</strong>: ARIA 레이블, 색상 대비, 키보드 탐색 지원<br>
                                    3. <strong>Best Practices (모범 사례)</strong>: HTTPS 사용, 콘솔 오류, 이미지 비율<br>
                                    4. <strong>SEO (검색 최적화)</strong>: 메타 태그, 구조화된 데이터, 모바일 친화성
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>95~100</td>
                                                <td>Performance: 90점+<br>Accessibility: 90점+<br>Best Practices: 90점+<br>SEO: 90점+<br>전체 평균: 95점+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>90~94</td>
                                                <td>Performance: 85점+<br>Accessibility: 85점+<br>Best Practices: 85점+<br>SEO: 85점+<br>전체 평균: 90점+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>80~89</td>
                                                <td>Performance: 75점+<br>Accessibility: 75점+<br>Best Practices: 75점+<br>SEO: 75점+<br>전체 평균: 80점+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>70~79</td>
                                                <td>Performance: 65점+<br>Accessibility: 65점+<br>Best Practices: 65점+<br>SEO: 65점+<br>전체 평균: 70점+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>60~69</td>
                                                <td>Performance: 55점+<br>Accessibility: 55점+<br>Best Practices: 55점+<br>SEO: 55점+<br>전체 평균: 60점+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~59</td>
                                                <td>위 기준에 미달</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Core Web Vitals 지표 설명</strong><br>
                                    - <strong>FCP (First Contentful Paint)</strong>: 페이지 로드가 시작된 시점부터 콘텐츠의 일부가 화면에 처음 렌더링되는 시점까지의 시간<br>
                                    - <strong>LCP (Largest Contentful Paint)</strong>: 뷰포트에서 가장 큰 콘텐츠 요소가 화면에 렌더링되는 시점. 2.5초 이내가 권장됨<br>
                                    - <strong>CLS (Cumulative Layout Shift)</strong>: 페이지 로드 중 발생하는 예상치 못한 레이아웃 이동의 누적 점수. 0.1 이하가 권장됨<br>
                                    - <strong>TBT (Total Blocking Time)</strong>: FCP와 TTI 사이에 메인 스레드가 차단된 총 시간. 200ms 이내가 권장됨<br>
                                    - <strong>TTI (Time to Interactive)</strong>: 페이지가 완전히 상호작용 가능하게 되는 시점. 3.8초 이내가 권장됨<br>
                                    - <strong>Speed Index</strong>: 페이지의 콘텐츠가 얼마나 빨리 표시되는지를 나타내는 지표. 3.4초 이내가 권장됨
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ $currentTest->raw_json_pretty ?? '미리보기를 생성할 수 없습니다.' }}</pre>
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(웹 접근성 검사)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <div class="small text-muted">심각한 차단</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-orange">{{ $counts['serious'] ?? 0 }}</div>
                                                            <small>Serious</small>
                                                            <div class="small text-muted">주요 제한</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-warning">{{ $counts['moderate'] ?? 0 }}</div>
                                                            <small>Moderate</small>
                                                            <div class="small text-muted">부분 불편</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-info">{{ $counts['minor'] ?? 0 }}</div>
                                                            <small>Minor</small>
                                                            <div class="small text-muted">경미한 문제</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <strong>총 위반 건수: {{ $counts['total'] ?? 0 }}건</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>axe-core 엔진(Deque Systems)</strong>을 통해 수행된 웹 접근성 시험 결과에 근거합니다.<br>
                                            모든 데이터는 <u>WCAG 2.1 국제 표준</u>에 따라 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해
                                            누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 웹 접근성 검사 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>우수한 웹 접근성 수준</u>을 입증하였습니다.<br>
                                                이는 <strong>장애인, 고령자를 포함한 모든 사용자</strong>가 동등하게 이용할 수 있는
                                                포용적인 웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 위반 상세 목록 -->
                                    @if (!empty($violations))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">주요 위반 사항</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="80">중요도</th>
                                                                <th>문제 설명</th>
                                                                <th width="100">영향 요소</th>
                                                                <th width="150">카테고리</th>
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
                                                                        <small>{{ count($violation['nodes'] ?? []) }}개 요소</small>
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
                                                        <small class="text-muted">총 {{ count($violations) }}개 중 상위 10개만 표시</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 영향도별 분포 -->
                                    <div class="alert alert-info d-block">
                                        <strong>접근성 위반 중요도 기준:</strong><br>
                                        <span class="text-danger">● Critical</span>: 사용자가 특정 기능을 전혀 사용할 수 없게 만드는 문제 (키보드 트랩, 필수 ARIA 누락)<br>
                                        <span class="text-orange">● Serious</span>: 주요 기능 사용에 심각한 어려움 (레이블 없는 폼, 낮은 색상 대비)<br>
                                        <span class="text-warning">● Moderate</span>: 일부 사용자에게 불편 (불명확한 링크 텍스트)<br>
                                        <span class="text-info">● Minor</span>: 경미한 사용자 경험 저하 (빈 헤딩, 중복 ID)
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>WCAG 2.1 준수 사항:</strong> 인지 가능성, 운용 가능성, 이해 가능성, 견고성</p>
                                        <p class="mb-2"><strong>법적 요구사항:</strong> 한국 장애인차별금지법, 미국 ADA, EU EN 301 549 준수</p>
                                        <p class="mb-0"><strong>검사 도구:</strong> axe-core CLI (Deque Systems) - 업계 표준 접근성 검사 엔진</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Accessibility Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 WCAG 2.1 국제 표준 기반의 웹 접근성 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>웹 접근성 검사 - WCAG 2.1 국제 표준 준수 평가</h3>
                                <div class="text-muted small mt-1">
                                    웹 접근성은 장애인, 고령자를 포함한 모든 사용자가 웹사이트를 동등하게 이용할 수 있도록 보장하는
                                    필수적인 품질 지표입니다. WCAG (Web Content Accessibility Guidelines) 2.1은
                                    W3C에서 제정한 국제 표준으로, 전 세계적으로 웹 접근성의 기준으로 사용됩니다.
                                    <br><br>
                                    <strong>측정 도구 및 환경</strong><br>
                                    • axe-core CLI (Deque Systems) - 업계 표준 접근성 검사 엔진<br>
                                    • WCAG 2.1 Level AA 기준 적용<br>
                                    • 자동화 검사로 탐지 가능한 접근성 문제 점검<br>
                                    • 스크린 리더, 키보드 탐색 호환성 검증
                                    <br><br>
                                    <strong>4대 접근성 원칙 (POUR)</strong><br>
                                    1. <strong>인지 가능성(Perceivable)</strong>: 모든 콘텐츠를 다양한 감각으로 인지 가능<br>
                                    2. <strong>운용 가능성(Operable)</strong>: 키보드만으로 모든 기능 사용 가능<br>
                                    3. <strong>이해 가능성(Understandable)</strong>: 정보와 UI 조작이 이해하기 쉬움<br>
                                    4. <strong>견고성(Robust)</strong>: 다양한 보조 기술과 호환
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>98~100</td>
                                                <td>Critical: 0건<br>Serious: 0건<br>Moderate: 0~2건<br>Minor: 0~5건</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>95~97</td>
                                                <td>Critical: 0건<br>Serious: 0~1건<br>Moderate: 0~5건<br>Minor: 0~10건</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>90~94</td>
                                                <td>Critical: 0건<br>Serious: 0~3건<br>Moderate: 0~10건<br>Minor: 무제한</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>80~89</td>
                                                <td>Critical: 0~1건<br>Serious: 0~5건<br>Moderate: 0~20건<br>Minor: 무제한</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>70~79</td>
                                                <td>Critical: 0~3건<br>Serious: 0~10건<br>Moderate: 무제한<br>Minor: 무제한</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~69</td>
                                                <td>위 기준에 미달</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 법적 요구사항 및 표준</strong><br>
                                    - <strong>한국</strong>: 장애인차별금지법, 한국형 웹 콘텐츠 접근성 지침(KWCAG 2.2)<br>
                                    - <strong>미국</strong>: ADA (Americans with Disabilities Act), Section 508<br>
                                    - <strong>유럽</strong>: EN 301 549, Web Accessibility Directive<br>
                                    - <strong>국제</strong>: ISO/IEC 40500, WCAG 2.1 Level AA<br><br>
                                    
                                    웹 접근성은 법적 의무사항일 뿐만 아니라, 더 많은 사용자에게 서비스를 제공하고,
                                    SEO 개선, 브랜드 이미지 향상에도 도움이 되는 중요한 품질 지표입니다.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
                                    </button>
                                </div>
                                <pre class="json-dump text-start" id="json-data">{{ $currentTest->raw_json_pretty ?? '미리보기를 생성할 수 없습니다.' }}</pre>
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(브라우저 호환성 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <small>정상 브라우저</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $jsFirstPartyTotal }}</h3>
                                                            <small>JS 오류(자사)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $cssTotal }}</h3>
                                                            <small>CSS 오류</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h5 class="mb-0">{{ $strictMode ? '엄격' : '기본' }}</h5>
                                                            <small>테스트 모드</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (!is_null($jsThirdPartyTotal) || !is_null($jsNoiseTotal))
                                                <div class="mt-2 text-center text-muted small">
                                                    @if (!is_null($jsThirdPartyTotal))
                                                        타사 JS 오류: {{ $jsThirdPartyTotal }}
                                                    @endif
                                                    @if (!is_null($jsNoiseTotal))
                                                        · 노이즈: {{ $jsNoiseTotal }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Playwright 엔진(Microsoft)</strong>을 통해 수행된 브라우저 호환성 시험 결과에 근거합니다.<br>
                                            모든 데이터는 <u>Chrome, Firefox, Safari 3대 주요 브라우저</u>에서 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해
                                            누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 브라우저 호환성 검사 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>우수한 크로스 브라우저 호환성</u>을 입증하였습니다.<br>
                                                이는 <strong>모든 주요 브라우저</strong>에서 안정적으로 작동하는
                                                고품질 웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 브라우저별 상세 결과 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">브라우저별 상세 결과</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>브라우저</th>
                                                            <th>정상 로드</th>
                                                            <th>JS 오류(자사)</th>
                                                            <th>CSS 오류</th>
                                                            <th>판정 사유</th>
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
                                                                        <span class="badge bg-green-lt text-green-lt-fg">정상</span>
                                                                    @else
                                                                        <span class="badge bg-red-lt text-red-lt-fg">비정상</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $jsFirst }}</strong>
                                                                    @if (!is_null($jsThird) || !is_null($jsNoise))
                                                                        <div class="small text-muted">
                                                                            @if (!is_null($jsThird))
                                                                                타사: {{ $jsThird }}
                                                                            @endif
                                                                            @if (!is_null($jsNoise))
                                                                                · 노이즈: {{ $jsNoise }}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $browser['cssErrorCount'] ?? 0 }}</td>
                                                                <td>
                                                                    @if (!empty($browser['navError']))
                                                                        <span class="text-danger">{{ Str::limit($browser['navError'], 50) }}</span>
                                                                    @else
                                                                        <small class="text-muted">정상 로드</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 오류 샘플 (주요 오류만) -->
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
                                                <h4 class="mb-3">주요 오류 내역</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>브라우저</th>
                                                                <th>오류 유형</th>
                                                                <th>오류 내용</th>
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
                                                                        <td><span class="badge bg-red-lt text-red-lt-fg">JS 자사</span></td>
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
                                        <strong>측정 지표:</strong> 정상 로드 (페이지 완전 로드 확인), JS 오류 (자사/타사/노이즈 분류), CSS 오류 (파싱 및 렌더링)<br>
                                        <span class="text-muted">자사 오류는 테스트 대상 도메인에서 발생한 오류, 타사는 외부 서비스 오류입니다.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>테스트 브라우저:</strong> Chromium (Chrome/Edge 엔진), Firefox (Gecko), WebKit (Safari)</p>
                                        <p class="mb-2"><strong>테스트 도구:</strong> Playwright - Microsoft에서 개발한 브라우저 자동화 도구</p>
                                        <p class="mb-0"><strong>판정 기준:</strong> {{ $strictMode ? '엄격 모드 - 모든 오류 포함' : '기본 모드 - 자사 오류 중심' }}</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Cross-Browser Compatibility Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 주요 브라우저 엔진 기반의 호환성 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>Chrome, Firefox, Safari 3대 주요 브라우저 호환성 검사</h3>
                                <div class="text-muted small mt-1">
                                    웹사이트가 주요 브라우저에서 정상적으로 작동하는지 검사하는 크로스 브라우저 호환성 테스트입니다.
                                    <br><br>
                                    <strong>측정 도구:</strong> Playwright (Microsoft에서 개발한 브라우저 자동화 도구)<br>
                                    • Chromium (Chrome, Edge의 기반 엔진)<br>
                                    • Firefox (Gecko 엔진)<br>
                                    • WebKit (Safari의 기반 엔진)
                                    <br><br>
                                    <strong>측정 항목:</strong><br>
                                    • 페이지 정상 로드 여부 (document.readyState === 'complete')<br>
                                    • JavaScript 오류 수집 (자사/타사/노이즈 분류)<br>
                                    • CSS 오류 수집 (파서 오류 패턴 기반)<br>
                                    • 브라우저별 User-Agent 정보
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Chrome/Firefox/Safari <strong>모두 정상</strong><br>
                                                    자사 JS 오류: <strong>0개</strong><br>
                                                    CSS 렌더링 오류: <strong>0개</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>주요 브라우저 지원 <strong>양호</strong> (2개 이상 정상)<br>
                                                    자사 JS 오류 <strong>≤ 1</strong><br>
                                                    CSS 오류 <strong>≤ 1</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>브라우저별 <strong>경미한 차이</strong> 존재 (2개 이상 정상)<br>
                                                    자사 JS 오류 <strong>≤ 3</strong><br>
                                                    CSS 오류 <strong>≤ 3</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>일부 브라우저에서 <strong>기능 저하</strong> (1개 이상 정상)<br>
                                                    자사 JS 오류 <strong>≤ 6</strong><br>
                                                    CSS 오류 <strong>≤ 6</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>호환성 문제 <strong>다수</strong><br>
                                                    자사 JS 오류 <strong>≤ 10</strong><br>
                                                    CSS 오류 <strong>≤ 10</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>주요 브라우저 <strong>정상 동작 불가</strong><br>
                                                    자사 JS 오류 <strong>10개 초과</strong><br>
                                                    CSS 오류 <strong>10개 초과</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 브라우저 호환성의 중요성</strong><br>
                                    - <strong>사용자 경험</strong>: 모든 사용자가 브라우저와 관계없이 동일한 경험을 누릴 수 있습니다<br>
                                    - <strong>시장 점유율</strong>: Chrome 65%, Safari 19%, Firefox 3% (2024년 기준)<br>
                                    - <strong>비즈니스 영향</strong>: 호환성 문제는 이탈률 증가와 매출 감소로 직결됩니다<br>
                                    - <strong>SEO 영향</strong>: 검색엔진은 크롤링 시 JavaScript 오류를 부정적으로 평가합니다<br><br>
                                    
                                    크로스 브라우저 테스트는 개발 완료 후 반드시 수행해야 하는 필수 품질 검증 과정입니다.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(반응형 UI 적합성 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <small>초과 건수</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ $maxOverflowPx }}px</h3>
                                                            <small>최대 초과</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ 9 - $overflowCount }}/9</h3>
                                                            <small>정상 뷰포트</small>
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
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Playwright 엔진(Chromium)</strong>을 통해 수행된 반응형 UI 시험 결과에 근거합니다.<br>
                                            모든 데이터는 <u>9개 주요 디바이스 뷰포트</u>에서 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해
                                            누구나 확인할 수 있습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 반응형 UI 검사 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>우수한 반응형 웹 디자인</u>을 입증하였습니다.<br>
                                                이는 <strong>모든 디바이스</strong>에서 수평 스크롤 없이 완벽하게 표시되는
                                                사용자 친화적인 웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 뷰포트별 상세 결과 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">뷰포트별 측정 결과</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>디바이스</th>
                                                            <th>뷰포트 크기</th>
                                                            <th>상태</th>
                                                            <th>초과 픽셀</th>
                                                            <th>Body 렌더 폭</th>
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
                                                                        <span class="badge bg-secondary">오류</span>
                                                                    @elseif ($hasOverflow)
                                                                        <span class="badge bg-red-lt text-red-lt-fg">초과</span>
                                                                    @else
                                                                        <span class="badge bg-green-lt text-green-lt-fg">정상</span>
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

                                    <!-- 디바이스 그룹별 요약 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">디바이스 그룹별 분석</h4>
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
                                                            <h5>모바일 (360-414px)</h5>
                                                            <div class="h3">{{ $mobileCount }}/3</div>
                                                            <small>정상 표시</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>태블릿 (672-1024px)</h5>
                                                            <div class="h3">{{ $tabletCount }}/4</div>
                                                            <small>정상 표시</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>데스크톱 (1280px+)</h5>
                                                            <div class="h3">{{ $desktopCount }}/2</div>
                                                            <small>정상 표시</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-block">
                                        <strong>측정 방식:</strong> 각 뷰포트로 브라우저 설정 → 페이지 로드 → body 요소 폭 측정 → viewport 폭과 비교<br>
                                        <span class="text-muted">초과 발생 시 사용자는 수평 스크롤이 필요하며, 이는 모바일 사용성을 크게 저하시킵니다.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>테스트 뷰포트:</strong> 모바일 3개, 폴더블 1개, 태블릿 3개, 데스크톱 2개 (총 9개)</p>
                                        <p class="mb-2"><strong>측정 기준:</strong> document.body.getBoundingClientRect().width vs window.innerWidth</p>
                                        <p class="mb-0"><strong>안정화 대기:</strong> 네트워크 완료 후 6초 대기하여 동적 콘텐츠 로드 확인</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Responsive UI Test를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 다양한 디바이스 환경 기반의 반응형 UI 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>Playwright 기반 반응형 UI 적합성 검사</h3>
                                <div class="text-muted small mt-1">
                                    <strong>측정 도구:</strong> Playwright (Chromium 엔진)<br>
                                    <strong>테스트 목적:</strong> 다양한 디바이스 환경에서 웹페이지가 viewport 경계를 벗어나지 않고 올바르게 렌더링되는지 검증<br>
                                    <strong>검사 대상:</strong> 9개 주요 뷰포트 (모바일 3개, 폴더블 1개, 태블릿 3개, 데스크톱 2개)<br><br>

                                    <strong>테스트 방식:</strong><br>
                                    1. 각 뷰포트 크기로 브라우저 설정<br>
                                    2. 페이지 로드 후 네트워크 안정화 대기 (6초)<br>
                                    3. document.body.getBoundingClientRect() 측정<br>
                                    4. viewport 폭과 비교하여 초과 픽셀 계산<br><br>

                                    <strong>검사 뷰포트 목록:</strong><br>
                                    • 모바일: 360×800, 390×844, 414×896<br>
                                    • 폴더블: 672×960<br>
                                    • 태블릿: 768×1024, 834×1112, 1024×1366<br>
                                    • 데스크톱: 1280×800, 1440×900
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>100</td>
                                                <td>전 뷰포트 초과 0건<br>body 렌더 폭이 항상 viewport 이내</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>90~95</td>
                                                <td>초과 ≤1건이며 ≤8px<br>모바일 협폭(≤390px) 구간에서는 초과 0건</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>80~89</td>
                                                <td>초과 ≤2건이고 각 ≤16px<br>또는 모바일 협폭에서 ≤8px 1건</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>70~79</td>
                                                <td>초과 ≤4건 또는 단일 초과가 17~32px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~69</td>
                                                <td>초과 >4건 또는 단일 초과가 33~64px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>측정 실패 또는 ≥65px 초과 발생</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 반응형 웹 디자인의 중요성</strong><br>
                                    - <strong>모바일 우선</strong>: 전체 웹 트래픽의 60% 이상이 모바일에서 발생 (2024년 기준)<br>
                                    - <strong>사용자 경험</strong>: 수평 스크롤은 모바일 사용자의 이탈률을 40% 증가시킴<br>
                                    - <strong>SEO 영향</strong>: Google은 모바일 친화성을 핵심 순위 요소로 평가<br>
                                    - <strong>접근성</strong>: 다양한 디바이스 사용자 모두에게 동등한 경험 제공<br><br>
                                    
                                    반응형 UI는 현대 웹 개발의 필수 요구사항이며, 비즈니스 성공에 직접적인 영향을 미칩니다.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(링크 검증 테스트)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>구분</th>
                                                            <th>검사 수</th>
                                                            <th>오류</th>
                                                            <th>오류율</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>전체 링크</strong></td>
                                                            <td>{{ $totals['httpChecked'] ?? 0 }}개</td>
                                                            <td>{{ ($totals['internalErrors'] ?? 0) + ($totals['externalErrors'] ?? 0) }}개</td>
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
                                                            <td><strong>내부 링크</strong></td>
                                                            <td>{{ $totals['internalChecked'] ?? 0 }}개</td>
                                                            <td>{{ $totals['internalErrors'] ?? 0 }}개</td>
                                                            <td>{{ $rates['internalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>외부 링크</strong></td>
                                                            <td>{{ $totals['externalChecked'] ?? 0 }}개</td>
                                                            <td>{{ $totals['externalErrors'] ?? 0 }}개</td>
                                                            <td>{{ $rates['externalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>이미지 링크</strong></td>
                                                            <td>{{ $totals['imageChecked'] ?? 0 }}개</td>
                                                            <td>{{ $totals['imageErrors'] ?? 0 }}개</td>
                                                            <td>{{ $rates['imageErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>앵커 링크</strong></td>
                                                            <td>{{ $totals['anchorChecked'] ?? 0 }}개</td>
                                                            <td>{{ $totals['anchorErrors'] ?? 0 }}개</td>
                                                            <td>{{ $rates['anchorErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>최대 리다이렉트</strong></td>
                                                            <td colspan="3">{{ $totals['maxRedirectChainEffective'] ?? 0 }}단계 체인</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Playwright 기반 링크 검증 도구</strong>를 통해 수행된 전체 링크 유효성 검사 결과에 근거합니다.<br>
                                            모든 데이터는 <u>실제 브라우저 환경</u>에서 JavaScript 동적 콘텐츠까지 포함하여 수집되었습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 검사는 특정 시점의 링크 상태로, 외부 사이트 변경 등에 따라 결과가 달라질 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 링크 검증 테스트 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>웹사이트 링크 무결성이 우수</u>함을 입증하였습니다.<br>
                                                이는 <strong>사용자 경험</strong>과 <strong>콘텐츠 접근성</strong>이 뛰어난
                                                웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 오류 링크 상세 -->
                                    @if (!empty($samples['links']) || !empty($samples['images']) || !empty($samples['anchors']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">오류 링크 샘플</h4>
                                                
                                                @if (!empty($samples['links']))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-danger-lt">
                                                            <h5 class="card-title mb-0">깨진 링크 (내부/외부)</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>URL</th>
                                                                            <th>상태</th>
                                                                            <th>오류</th>
                                                                            <th>체인</th>
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
                                                                <div class="text-muted small">... 외 {{ count($samples['links']) - 10 }}개 오류</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($samples['images']))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-warning-lt">
                                                            <h5 class="card-title mb-0">깨진 이미지 링크</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>이미지 URL</th>
                                                                            <th>상태</th>
                                                                            <th>오류</th>
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
                                                                <div class="text-muted small">... 외 {{ count($samples['images']) - 10 }}개 오류</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($samples['anchors']))
                                                    <div class="card">
                                                        <div class="card-header bg-info-lt">
                                                            <h5 class="card-title mb-0">존재하지 않는 앵커 (#id)</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($samples['anchors'], 0, 10) as $sample)
                                                                    <li><code>{{ $sample['href'] ?? '' }}</code></li>
                                                                @endforeach
                                                            </ul>
                                                            @if (count($samples['anchors']) > 10)
                                                                <div class="text-muted small mt-2">... 외 {{ count($samples['anchors']) - 10 }}개 오류</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-success d-block">
                                            <strong>✨ 완벽한 링크 상태</strong><br>
                                            검사된 모든 링크가 정상적으로 작동하고 있습니다.
                                        </div>
                                    @endif

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 링크 무결성이 중요한 이유</strong><br>
                                        - 사용자 경험: 깨진 링크는 사용자 신뢰도를 떨어뜨리고 이탈률을 높입니다<br>
                                        - SEO 영향: 404 오류가 많으면 검색엔진 순위에 부정적 영향을 미칩니다<br>
                                        - 접근성: 모든 콘텐츠가 정상적으로 접근 가능해야 웹 표준을 준수합니다<br>
                                        - 브랜드 이미지: 깨진 이미지나 링크는 전문성을 해치는 요소입니다
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>내부 링크:</strong> 동일 도메인 내의 페이지 간 연결</p>
                                        <p class="mb-2"><strong>외부 링크:</strong> 다른 웹사이트로의 연결</p>
                                        <p class="mb-2"><strong>이미지 링크:</strong> img 태그의 src 속성 리소스</p>
                                        <p class="mb-2"><strong>앵커 링크:</strong> 페이지 내 특정 섹션으로 이동 (#id)</p>
                                        <p class="mb-0"><strong>리다이렉트 체인:</strong> 최종 목적지까지의 리다이렉트 횟수</p>
                                    </div>
                                    
                                    @if (!empty($totals['navError']))
                                        <div class="alert alert-danger d-block">
                                            <strong>⚠️ 네비게이션 오류</strong><br>
                                            {{ $totals['navError'] }}
                                        </div>
                                    @endif
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Link Validator를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 Playwright 기반의 정밀한 링크 검증 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>Playwright 기반 링크 검증 도구</h3>
                                <div class="text-muted small mt-1">
                                    <strong>측정 도구:</strong> Playwright + Node.js 기반 커스텀 크롤러<br>
                                    <strong>테스트 목적:</strong> 웹사이트의 모든 링크 상태를 검사하여 사용자 경험을 해치는 깨진 링크, 잘못된 리다이렉트, 존재하지 않는 앵커 등을 찾아냅니다.
                                    <br><br>
                                    <strong>검사 항목:</strong><br>
                                    • 내부 링크: 동일 도메인 내 모든 페이지 링크의 HTTP 상태<br>
                                    • 외부 링크: 외부 도메인으로 연결되는 링크의 유효성<br>
                                    • 이미지 링크: img 태그의 src 속성에 있는 이미지 리소스 상태<br>
                                    • 앵커 링크: 동일 페이지 내 #id 형태의 앵커 존재 여부<br>
                                    • 리다이렉트 체인: 각 링크의 리다이렉트 단계 수와 최종 도착지
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>90~100</td>
                                                <td>• 내부/외부/이미지 링크 오류율: 0%<br>
                                                    • 리다이렉트 체인 ≤1단계<br>
                                                    • 앵커 링크 100% 정상</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80~89</td>
                                                <td>• 전체 오류율 ≤1%<br>
                                                    • 리다이렉트 체인 ≤2단계<br>
                                                    • 앵커 링크 대부분 정상</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70~79</td>
                                                <td>• 전체 오류율 ≤3%<br>
                                                    • 리다이렉트 체인 ≤3단계<br>
                                                    • 일부 앵커 링크 불량</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>60~69</td>
                                                <td>• 전체 오류율 ≤5%<br>
                                                    • 다수 링크 경고 (타임아웃/SSL 문제)<br>
                                                    • 앵커 링크 오류 빈번</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~59</td>
                                                <td>• 전체 오류율 ≤10%<br>
                                                    • 리다이렉트 루프 또는 긴 체인<br>
                                                    • 이미지 링크 다수 깨짐</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>• 전체 오류율 10% 이상<br>
                                                    • 주요 내부 링크 다수 깨짐<br>
                                                    • 앵커/이미지 전반 불량</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 링크 관리 체크리스트</strong><br>
                                    <strong>정기 점검:</strong><br>
                                    • 월 1회 전체 링크 검사 실행<br>
                                    • 외부 링크 유효성 모니터링<br>
                                    • 404 오류 페이지 즉시 수정<br><br>
                                    
                                    <strong>최적화 방안:</strong><br>
                                    • 리다이렉트 최소화: 직접 링크 사용<br>
                                    • 앵커 매칭: href="#id"와 id="id" 일치<br>
                                    • 이미지 최적화: 올바른 경로와 파일 존재 확인<br>
                                    • HTTPS 사용: 보안 프로토콜 적용<br><br>
                                    
                                    <strong>성과 지표:</strong><br>
                                    • 깨진 링크 제거 → 이탈률 20% 감소<br>
                                    • 리다이렉트 최적화 → 페이지 속도 15% 향상<br>
                                    • 이미지 정상화 → 사용자 만족도 25% 증가
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(구조화 데이터 검증)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>구분</th>
                                                            <th>수량</th>
                                                            <th>상태</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>JSON-LD 블록</strong></td>
                                                            <td>{{ $totals['jsonLdBlocks'] ?? 0 }}개</td>
                                                            <td>
                                                                @if (($totals['jsonLdBlocks'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">구현</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">미구현</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>스키마 아이템</strong></td>
                                                            <td>{{ $totals['jsonLdItems'] ?? 0 }}개</td>
                                                            <td>
                                                                @if (($totals['jsonLdItems'] ?? 0) >= 3)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">충분</span>
                                                                @elseif (($totals['jsonLdItems'] ?? 0) > 0)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">기본</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>오류/경고</strong></td>
                                                            <td>
                                                                <span class="text-danger">{{ $totalErrors }}개</span> /
                                                                <span class="text-warning">{{ $totals['itemWarnings'] ?? 0 }}개</span>
                                                            </td>
                                                            <td>
                                                                @if ($totalErrors === 0 && ($totals['itemWarnings'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">완벽</span>
                                                                @elseif ($totalErrors === 0)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">양호</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">개선필요</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Rich Results 유형</strong></td>
                                                            <td>{{ is_array($richTypes) ? count($richTypes) : 0 }}개</td>
                                                            <td>
                                                                @if (is_array($richTypes) && count($richTypes) > 0)
                                                                    {{ implode(', ', array_slice($richTypes, 0, 3)) }}
                                                                @else
                                                                    <span class="text-muted">없음</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>기타 형식</strong></td>
                                                            <td>
                                                                Microdata: {{ !empty($totals['hasMicrodata']) ? '✓' : '✗' }}
                                                                RDFa: {{ !empty($totals['hasRdfa']) ? '✓' : '✗' }}
                                                            </td>
                                                            <td>
                                                                @if (!empty($totals['hasMicrodata']) || !empty($totals['hasRdfa']))
                                                                    <span class="badge">보조형식 감지</span>
                                                                @else
                                                                    <span class="text-muted">JSON-LD 전용</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Playwright 기반 구조화 데이터 검증 도구</strong>를 통해 수행된 Schema.org 규격 검사 결과에 근거합니다.<br>
                                            모든 데이터는 <u>Google Rich Results Test 기준</u>에 준하여 평가되었으며, 실제 브라우저 렌더링 환경에서 수집되었습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 검사는 특정 시점의 구조화 데이터 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 구조화 데이터 검증 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>검색 결과 풍부한 스니펫(Rich Snippets) 표시 자격</u>을 갖추었습니다.<br>
                                                이는 <strong>검색 노출 최적화</strong>와 <strong>클릭률 향상</strong>에 기여하는
                                                우수한 구조화 데이터 구현을 입증합니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 스키마 타입 분석 -->
                                    @if (!empty($types))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">스키마 타입 분포</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>@type</th>
                                                                <th>개수</th>
                                                                <th>Rich Results 지원</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach (array_slice($types, 0, 10) as $row)
                                                                <tr>
                                                                    <td><code>{{ $row['type'] }}</code></td>
                                                                    <td>{{ $row['count'] }}</td>
                                                                    <td>
                                                                        @if (in_array($row['type'], ['Article', 'Product', 'Recipe', 'Event', 'Course', 'FAQPage', 'HowTo', 'JobPosting', 'LocalBusiness', 'Review', 'Video']))
                                                                            <span class="badge bg-green-lt text-green-lt-fg">지원</span>
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

                                    <!-- 오류 및 경고 상세 -->
                                    @if (!empty($parseErrors) || !empty($perItem))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">검증 이슈 상세</h4>
                                                
                                                @if (!empty($parseErrors))
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-danger-lt">
                                                            <h5 class="card-title mb-0">파싱 오류</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($parseErrors, 0, 5) as $pe)
                                                                    <li class="mb-2">
                                                                        <strong>블록 #{{ $pe['index'] }}:</strong> {{ $pe['message'] }}
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
                                                            <h5 class="card-title mb-0">항목별 이슈</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            @foreach (array_slice($perItem, 0, 5) as $item)
                                                                @if (!empty($item['errors']) || !empty($item['warnings']))
                                                                    <div class="mb-3">
                                                                        <strong>{{ implode(', ', $item['types'] ?? ['Unknown']) }}</strong>
                                                                        @if (!empty($item['errors']))
                                                                            <div class="text-danger small">
                                                                                오류: {{ implode(', ', $item['errors']) }}
                                                                            </div>
                                                                        @endif
                                                                        @if (!empty($item['warnings']))
                                                                            <div class="text-warning small">
                                                                                경고: {{ implode(', ', $item['warnings']) }}
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

                                    <!-- 권장 개선 사항 -->
                                    @if (!empty($actions))
                                        <div class="alert alert-warning d-block">
                                            <strong>⚡ 권장 개선 사항</strong><br>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($actions as $action)
                                                    <li>{{ $action }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- 예시 스니펫 -->
                                    @if (!empty($snippets))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">권장 JSON-LD 예시</h4>
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

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 구조화 데이터가 중요한 이유</strong><br>
                                        - Rich Snippets: 검색 결과에 별점, 가격, 이미지 등 풍부한 정보 표시<br>
                                        - 음성 검색 최적화: AI 어시스턴트가 정보를 정확히 이해하고 답변<br>
                                        - Knowledge Graph: Google 지식 패널에 정보 등록 가능<br>
                                        - 클릭률 향상: 일반 검색 결과 대비 평균 30% 높은 CTR
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>JSON-LD:</strong> JavaScript Object Notation for Linked Data, Google 권장 형식</p>
                                        <p class="mb-2"><strong>Schema.org:</strong> Google, Microsoft, Yahoo, Yandex가 공동 개발한 구조화 데이터 표준</p>
                                        <p class="mb-2"><strong>Rich Results:</strong> 검색 결과에 표시되는 시각적으로 향상된 결과</p>
                                        <p class="mb-2"><strong>필수 스키마:</strong> Organization, WebSite, BreadcrumbList (모든 사이트 권장)</p>
                                        <p class="mb-0"><strong>콘텐츠별 스키마:</strong> Article (블로그), Product (쇼핑몰), LocalBusiness (로컬업체)</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Structure Validator를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 Google Rich Results 기준에 준하는 구조화 데이터 검증 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>구조화 데이터 검증 도구</h3>
                                <div class="text-muted small mt-1">
                                    Playwright 기반 브라우저 자동화를 통해 실제 렌더링된 페이지에서 구조화 데이터를 수집하고,
                                    Google Rich Results Test 기준에 준하는 Schema.org 검증 규칙을 적용합니다.
                                    <br><br>
                                    <strong>📊 측정 항목:</strong><br>
                                    • JSON-LD 블록 수 및 파싱 가능 여부<br>
                                    • Schema.org 타입별 필수/권장 필드 검증<br>
                                    • Rich Results 적합성 평가<br>
                                    • Microdata, RDFa 등 기타 형식 감지<br><br>
                                    
                                    <strong>🎯 검증 대상 스키마:</strong><br>
                                    • Organization, WebSite, BreadcrumbList (기본)<br>
                                    • Article, NewsArticle, BlogPosting (콘텐츠)<br>
                                    • Product, Offer, AggregateRating (쇼핑)<br>
                                    • LocalBusiness, Restaurant, Store (로컬)<br>
                                    • Event, Course, Recipe (특수 콘텐츠)<br>
                                    • FAQPage, HowTo, QAPage (Q&A)<br>
                                    • Person, JobPosting, Review (기타)
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>95~100</td>
                                                <td>• JSON-LD 완벽 구현 (파싱 오류 없음)<br>
                                                    • 3개 이상 스키마 타입, Rich Results 2개 이상<br>
                                                    • 모든 필수 필드 포함, 권장 필드 80% 이상</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85~94</td>
                                                <td>• JSON-LD 정상 구현<br>
                                                    • 2개 이상 스키마 타입, Rich Results 1개 이상<br>
                                                    • 필수 필드 완성, 권장 필드 60% 이상</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75~84</td>
                                                <td>• JSON-LD 기본 구현<br>
                                                    • 1개 이상 스키마 타입<br>
                                                    • 필수 필드 대부분 포함</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65~74</td>
                                                <td>• 구조화 데이터 부분 구현<br>
                                                    • 경미한 오류 존재<br>
                                                    • 일부 필수 필드 누락</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~64</td>
                                                <td>• 구조화 데이터 미흡<br>
                                                    • 파싱 오류 또는 중대 오류 존재<br>
                                                    • 다수 필수 필드 누락</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>• 구조화 데이터 없음<br>
                                                    • JSON-LD 미구현<br>
                                                    • Schema.org 미적용</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 구조화 데이터 구현 체크리스트</strong><br>
                                    <strong>필수 구현:</strong><br>
                                    • Organization: 회사 정보, 로고, 소셜 프로필<br>
                                    • WebSite: 사이트명, URL, 검색박스<br>
                                    • BreadcrumbList: 페이지 경로 네비게이션<br><br>
                                    
                                    <strong>콘텐츠별 구현:</strong><br>
                                    • 블로그/뉴스: Article, NewsArticle, BlogPosting<br>
                                    • 쇼핑몰: Product, Offer, Review, AggregateRating<br>
                                    • 로컬 비즈니스: LocalBusiness, OpeningHoursSpecification<br>
                                    • 이벤트: Event, EventVenue, EventSchedule<br><br>
                                    
                                    <strong>성과 지표:</strong><br>
                                    • Rich Snippets 노출 → CTR 평균 30% 상승<br>
                                    • 음성 검색 최적화 → 모바일 트래픽 20% 증가<br>
                                    • Knowledge Graph 등록 → 브랜드 인지도 향상
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(검색엔진 크롤링 검사)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>구분</th>
                                                            <th>값</th>
                                                            <th>상태</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>robots.txt</strong></td>
                                                            <td>{{ $robots['status'] ?? '-' }}</td>
                                                            <td>
                                                                @if ($robots['exists'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">존재</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>sitemap.xml</strong></td>
                                                            <td>{{ $sitemap['sitemapUrlCount'] ?? 0 }}개 URL</td>
                                                            <td>
                                                                @if ($sitemap['hasSitemap'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">존재</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>검사 페이지</strong></td>
                                                            <td>{{ $pages['count'] ?? 0 }}개</td>
                                                            <td>평균 {{ number_format($pages['qualityAvg'] ?? 0, 1) }}점</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>오류율</strong></td>
                                                            <td>{{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['errorRate4xx5xx'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">정상</span>
                                                                @elseif (($pages['errorRate4xx5xx'] ?? 0) < 5)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">양호</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">문제</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>중복률</strong></td>
                                                            <td>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['duplicateRate'] ?? 0) <= 30)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">양호</span>
                                                                @else
                                                                    <span class="badge bg-warning-lt text-warning-lt-fg">높음</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>robots.txt 준수 크롤러</strong>를 통해 수행된 검색엔진 크롤링 검사 결과에 근거합니다.<br>
                                            모든 데이터는 <u>실제 검색엔진 크롤링 방식</u>을 시뮬레이션하여 수집되었으며, SEO 품질 기준으로 평가되었습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 검사는 특정 시점의 크롤링 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 검색엔진 크롤링 검사 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>검색엔진 최적화 우수 사이트</u>임을 입증하였습니다.<br>
                                                이는 <strong>검색 크롤러 친화성</strong>과 <strong>페이지 품질 관리</strong>가 우수한
                                                웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Sitemap 파일 상세 -->
                                    @if (!empty($sitemap['sitemaps']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Sitemap 파일 현황</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>파일명</th>
                                                                <th>URL 수</th>
                                                                <th>상태</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sitemap['sitemaps'] as $s)
                                                                <tr>
                                                                    <td>{{ basename($s['url']) }}</td>
                                                                    <td>{{ $s['count'] ?? 0 }}개</td>
                                                                    <td>
                                                                        @if ($s['ok'])
                                                                            <span class="badge bg-green-lt text-green-lt-fg">정상</span>
                                                                        @else
                                                                            <span class="badge bg-red-lt text-red-lt-fg">오류</span>
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

                                    <!-- 크롤링 계획 및 제외 URL -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">검사 대상 URL 샘플</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="small text-muted mb-2">
                                                        총 {{ $crawlPlan['candidateCount'] ?? 0 }}개 중 최대 50개 검사
                                                    </div>
                                                    @if (!empty($crawlPlan['sample']))
                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                            <ul class="small mb-0">
                                                                @foreach (array_slice($crawlPlan['sample'], 0, 10) as $url)
                                                                    <li class="text-break">{{ $url }}</li>
                                                                @endforeach
                                                                @if (count($crawlPlan['sample']) > 10)
                                                                    <li>... 외 {{ count($crawlPlan['sample']) - 10 }}개</li>
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
                                                    <h5 class="card-title mb-0">제외된 URL</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if (!empty($crawlPlan['skipped']))
                                                        <div class="small text-muted mb-2">
                                                            총 {{ count($crawlPlan['skipped']) }}개 제외
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
                                                        <div class="text-muted">제외된 URL 없음 ✓</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 문제 페이지 상세 -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-header bg-danger-lt">
                                                    <h5 class="card-title mb-0">오류 페이지 (4xx/5xx)</h5>
                                                </div>
                                                <div class="card-body">
                                                    @php $errorPages = $report['samples']['errorPages'] ?? []; @endphp
                                                    @if (empty($errorPages))
                                                        <div class="text-success">오류 페이지 없음 ✓</div>
                                                    @else
                                                        <ul class="small mb-0">
                                                            @foreach (array_slice($errorPages, 0, 5) as $page)
                                                                <li class="mb-1">
                                                                    <span class="badge bg-red-lt text-red-lt-fg">{{ $page['status'] }}</span>
                                                                    <span class="text-break">{{ Str::limit($page['url'], 50) }}</span>
                                                                </li>
                                                            @endforeach
                                                            @if (count($errorPages) > 5)
                                                                <li>... 외 {{ count($errorPages) - 5 }}개</li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-header bg-warning-lt">
                                                    <h5 class="card-title mb-0">낮은 품질 페이지 (50점 미만)</h5>
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
                                                        <div class="text-success">50점 미만 페이지 없음 ✓</div>
                                                    @else
                                                        <ul class="small mb-0">
                                                            @foreach ($lowQuality as $page)
                                                                <li class="mb-1">
                                                                    <span class="badge bg-orange-lt text-orange-lt-fg">{{ $page['score'] ?? 0 }}점</span>
                                                                    <span class="text-break">{{ Str::limit($page['url'], 50) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 중복 콘텐츠 현황 -->
                                    @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                                        <div class="alert alert-warning d-block">
                                            <strong>⚠️ 중복 콘텐츠 감지</strong><br>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    중복 제목 페이지: <strong>{{ $pages['dupTitleCount'] ?? 0 }}개</strong>
                                                </div>
                                                <div class="col-6">
                                                    중복 설명 페이지: <strong>{{ $pages['dupDescCount'] ?? 0 }}개</strong>
                                                </div>
                                            </div>
                                            <div class="small mt-2">
                                                중복률: <strong>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</strong>
                                                - 각 페이지마다 고유한 title과 description 작성을 권장합니다.
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 크롤링 최적화가 중요한 이유</strong><br>
                                        - 검색엔진 색인: robots.txt와 sitemap.xml은 검색엔진이 사이트를 이해하는 기본 도구<br>
                                        - 크롤링 효율: 정확한 크롤링 규칙으로 중요 페이지 우선 색인<br>
                                        - SEO 점수: 페이지 품질과 중복 콘텐츠는 검색 순위에 직접 영향<br>
                                        - 사용자 경험: 404 오류 없는 깨끗한 사이트 구조 유지
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>robots.txt:</strong> 검색엔진 크롤러의 접근 규칙을 정의하는 파일</p>
                                        <p class="mb-2"><strong>sitemap.xml:</strong> 사이트의 모든 중요 페이지 목록과 메타데이터</p>
                                        <p class="mb-2"><strong>품질 점수:</strong> title, description, canonical, H1, 콘텐츠량 종합 평가</p>
                                        <p class="mb-2"><strong>오류율:</strong> 404, 500 등 접근 불가 페이지 비율</p>
                                        <p class="mb-0"><strong>중복률:</strong> 동일한 메타데이터를 사용하는 페이지 비율</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Crawl Inspector를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 검색엔진 크롤링 표준을 준수하는 검사 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>검색엔진 크롤링 준수 및 페이지 품질 종합 분석</h3>
                                <div class="text-muted small mt-1">
                                    웹사이트의 robots.txt와 sitemap.xml을 분석하여 SEO 준수 여부를 검증하고,
                                    sitemap에 등록된 페이지들의 접근성과 품질을 종합적으로 평가합니다.
                                    <br><br>
                                    <strong>📋 검사 프로세스:</strong><br>
                                    1. robots.txt 파일 존재 여부 및 규칙 확인<br>
                                    2. sitemap.xml 파일 검색 및 URL 수집<br>
                                    3. robots.txt 규칙에 따른 크롤링 허용 URL 필터링<br>
                                    4. 최대 50개 페이지 샘플링 및 순차 검사<br>
                                    5. 각 페이지의 HTTP 상태, 메타데이터, 품질 점수 측정<br>
                                    6. 중복 콘텐츠(title/description) 비율 분석
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>90~100</td>
                                                <td>robots.txt 정상 적용<br>
                                                    sitemap.xml 존재 및 누락/404 없음<br>
                                                    검사 대상 페이지 전부 2xx<br>
                                                    전체 페이지 품질 평균 ≥ 85점<br>
                                                    중복 콘텐츠 ≤ 30%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80~89</td>
                                                <td>robots.txt 정상 적용<br>
                                                    sitemap.xml 존재 및 정합성 확보<br>
                                                    검사 대상 페이지 전부 2xx<br>
                                                    전체 페이지 품질 평균 ≥ 85점</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70~79</td>
                                                <td>robots.txt 및 sitemap.xml 존재<br>
                                                    검사 대상 페이지 전부 2xx<br>
                                                    전체 페이지 품질 평균 무관</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>55~69</td>
                                                <td>robots.txt 및 sitemap.xml 존재<br>
                                                    검사 리스트 일부 4xx/5xx 오류 포함</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>35~54</td>
                                                <td>robots.txt 및 sitemap.xml 존재<br>
                                                    검사 대상 URL 생성 가능<br>
                                                    단, 정상 접근률 낮거나 품질 점검 불가</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~34</td>
                                                <td>robots.txt 부재 또는 sitemap.xml 부재<br>
                                                    검사 리스트 자체 생성 불가</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 크롤링 최적화 체크리스트</strong><br>
                                    <strong>필수 구현:</strong><br>
                                    • robots.txt: User-agent, Allow/Disallow, Sitemap 위치 명시<br>
                                    • sitemap.xml: 모든 중요 페이지 포함, lastmod 날짜 관리<br>
                                    • 404 처리: 커스텀 404 페이지, 301 리다이렉트 설정<br><br>
                                    
                                    <strong>품질 점수 향상:</strong><br>
                                    • Title: 50-60자, 페이지별 고유 제목<br>
                                    • Description: 120-160자, 페이지별 고유 설명<br>
                                    • Canonical URL: 모든 페이지에 설정<br>
                                    • H1 태그: 페이지당 1개, 명확한 제목<br>
                                    • 콘텐츠: 최소 1000자 이상 실질적 내용<br><br>
                                    
                                    <strong>성과 지표:</strong><br>
                                    • 크롤링 최적화 → 색인 속도 50% 향상<br>
                                    • 중복 콘텐츠 제거 → 검색 순위 20% 상승<br>
                                    • 404 오류 제거 → 사용자 이탈률 15% 감소
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
                                    data-bs-toggle="tab">인증 결과 요약</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">검증 기준 및 환경</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">세부 측정 데이터</a>
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
                                                웹 테스트 인증서 (Web Test Certificate)
                                            </h1>
                                            <h2>(메타데이터 완성도 검사)</h2>
                                            <h3>인증번호: {{ $certificate->code }}</h3>
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
                                                                {{ number_format($currentTest->overall_score, 1) }}점
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        {{ $currentTest->url }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            테스트 일시:
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
                                                            <th>구분</th>
                                                            <th>상태</th>
                                                            <th>세부사항</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Title Tag</strong></td>
                                                            <td>
                                                                @if ($analysis['title']['isEmpty'] ?? true)
                                                                    <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                @elseif ($analysis['title']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">최적</span>
                                                                @elseif ($analysis['title']['isAcceptable'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">허용</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부적절</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['titleLength'] ?? 0 }}자 (최적: 50~60자)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Meta Description</strong></td>
                                                            <td>
                                                                @if ($analysis['description']['isEmpty'] ?? true)
                                                                    <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                @elseif ($analysis['description']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">최적</span>
                                                                @elseif ($analysis['description']['isAcceptable'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">허용</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부적절</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['descriptionLength'] ?? 0 }}자 (최적: 120~160자)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Open Graph</strong></td>
                                                            <td>
                                                                @if ($analysis['openGraph']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">완벽</span>
                                                                @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">기본</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부족</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['openGraphFields'] ?? 0 }}개 태그 설정</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Twitter Cards</strong></td>
                                                            <td>
                                                                @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">완벽</span>
                                                                @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">기본</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부족</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['twitterCardFields'] ?? 0 }}개 태그 설정</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Canonical URL</strong></td>
                                                            <td>
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">설정</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">미설정</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    중복 콘텐츠 방지 설정됨
                                                                @else
                                                                    설정 필요
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Hreflang</strong></td>
                                                            <td>
                                                                @if (($summary['hreflangCount'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">{{ $summary['hreflangCount'] }}개</span>
                                                                @else
                                                                    <span class="badge">0개</span>
                                                                @endif
                                                            </td>
                                                            <td>다국어 설정 {{ $summary['hreflangCount'] ?? 0 }}개</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ 테스트 결과 검증 완료</h4>
                                        <p class="mb-1">
                                            본 인증서는 <strong>Meta Inspector CLI</strong>를 통해 수행된 메타데이터 완성도 검사 결과에 근거합니다.<br>
                                            모든 데이터는 <u>실제 브라우저 렌더링 환경</u>에서 수집되었으며, SEO 모범 사례 기준으로 평가되었습니다.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ 본 검사는 특정 시점의 메타데이터 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 본 사이트는 메타데이터 완성도 검사 결과,
                                                <strong>{{ $grade }}</strong> 등급을 획득하여
                                                <u>검색엔진 최적화(SEO) 우수 사이트</u>임을 입증하였습니다.<br>
                                                이는 <strong>검색 노출</strong>과 <strong>소셜 미디어 공유</strong>에 최적화된
                                                웹사이트임을 보여줍니다.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- 메타데이터 상세 현황 -->
                                    @if ($metadata)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">메타데이터 상세 현황</h4>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title">기본 메타데이터</h5>
                                                        <div class="mb-3">
                                                            <div class="fw-bold mb-1">Title ({{ $summary['titleLength'] ?? 0 }}자)</div>
                                                            <div class="text-muted small">{{ $metadata['title'] ?: '제목 없음' }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="fw-bold mb-1">Description ({{ $summary['descriptionLength'] ?? 0 }}자)</div>
                                                            <div class="text-muted small">{{ $metadata['description'] ?: '설명 없음' }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold mb-1">Canonical URL</div>
                                                            <div class="text-muted small">{{ $metadata['canonical'] ?: 'Canonical URL 없음' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (!empty($metadata['openGraph']))
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <h5 class="card-title">Open Graph 태그</h5>
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
                                                            <h5 class="card-title">Twitter Cards 태그</h5>
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
                                                            <h5 class="card-title">Hreflang 설정</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        @foreach ($metadata['hreflangs'] as $hreflang)
                                                                            <tr>
                                                                                <td width="20%">
                                                                                    <code>{{ $hreflang['lang'] }}</code>
                                                                                    @if ($hreflang['lang'] === 'x-default')
                                                                                        <span class="badge bg-primary-lt ms-1">기본</span>
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
                                            <strong>⚠️ 발견된 문제점</strong><br>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($results['issues'] as $issue)
                                                    <li>{{ $issue }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 메타데이터가 중요한 이유</strong><br>
                                        - 검색엔진 최적화: 적절한 메타데이터는 검색 결과 노출과 순위에 직접적인 영향을 줍니다.<br>
                                        - 소셜 미디어 공유: Open Graph와 Twitter Cards는 링크 공유 시 미리보기 품질을 결정합니다.<br>
                                        - 사용자 경험: 명확한 제목과 설명은 사용자의 클릭률(CTR)을 향상시킵니다.<br>
                                        - 중복 콘텐츠 방지: Canonical URL은 검색엔진 패널티를 예방합니다.
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Title Tag:</strong> 검색 결과와 브라우저 탭에 표시되는 페이지 제목 (최적: 50~60자)</p>
                                        <p class="mb-2"><strong>Meta Description:</strong> 검색 결과에 표시되는 페이지 설명 (최적: 120~160자)</p>
                                        <p class="mb-2"><strong>Open Graph:</strong> Facebook, LinkedIn 등 소셜 미디어 공유 최적화</p>
                                        <p class="mb-2"><strong>Twitter Cards:</strong> Twitter 공유 시 카드 형태 최적화</p>
                                        <p class="mb-2"><strong>Canonical URL:</strong> 중복 콘텐츠 방지를 위한 대표 URL 지정</p>
                                        <p class="mb-0"><strong>Hreflang Tags:</strong> 다국어 페이지 연결 설정</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ 본 결과는 DevTeam-Test의 Meta Inspector를 통해 검증되었습니다.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            DevTeam-Test는 국제 SEO 표준에 근거한 메타데이터 품질 측정 서비스를 제공하며,
                                            인증서는 실시간 QR 검증으로 진위를 확인할 수 있습니다.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">인증서 발행일:
                                                {{ $certificate->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
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
                                <h3>메타데이터 완성도 검사 도구</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Meta Inspector CLI</strong>를 활용하여 웹페이지의 메타데이터 완성도를 분석합니다.
                                    <br><br>
                                    <strong>📊 측정 도구 및 방식:</strong><br>
                                    • Node.js 기반 헤드리스 브라우저 엔진으로 실제 페이지 렌더링<br>
                                    • HTML 파싱을 통한 메타태그 추출 및 분석<br>
                                    • SEO 모범 사례 기준으로 점수 산정 (100점 만점)<br><br>
                                    
                                    <strong>🎯 테스트 목적:</strong><br>
                                    • 검색엔진 최적화(SEO)를 위한 메타데이터 품질 평가<br>
                                    • 소셜 미디어 공유 시 미리보기 품질 확인<br>
                                    • 중복 콘텐츠 방지를 위한 Canonical 설정 검증<br>
                                    • 다국어 지원을 위한 Hreflang 설정 확인
                                </div>
                                {{-- 등급 기준 안내 --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>기준</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                <td>95~100</td>
                                                <td>Title 최적 길이(50~60자), Description 최적 길이(120~160자)<br>
                                                    Open Graph 완벽 구현, Twitter Cards 완벽 구현<br>
                                                    Canonical URL 정확, 모든 메타데이터 최적화</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85~94</td>
                                                <td>Title/Description 허용 범위(30~80자/80~200자)<br>
                                                    Open Graph 완벽 구현, Canonical URL 정확 설정<br>
                                                    Twitter Cards는 선택사항</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75~84</td>
                                                <td>Title/Description 기본 작성<br>
                                                    Open Graph 기본 태그 적용<br>
                                                    일부 메타데이터 누락 허용</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65~74</td>
                                                <td>Title/Description 길이 부적절<br>
                                                    Open Graph 불완전 (주요 태그 누락)<br>
                                                    Canonical URL 부정확 또는 누락</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~64</td>
                                                <td>Title/Description 심각한 길이 문제<br>
                                                    Open Graph 기본 태그 부족<br>
                                                    기본 메타데이터 부족</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>Title/Description 미작성<br>
                                                    Open Graph 부재<br>
                                                    메타데이터 전반 미구현</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 SEO 성공을 위한 메타데이터 체크리스트</strong><br>
                                    - <strong>Title Tag:</strong> 50-60자, 핵심 키워드 포함, 브랜드명 포함<br>
                                    - <strong>Meta Description:</strong> 120-160자, 행동 유도 문구 포함<br>
                                    - <strong>Open Graph:</strong> title, description, image, url 필수 4대 요소<br>
                                    - <strong>Twitter Cards:</strong> card, title, description 기본 3요소<br>
                                    - <strong>Canonical URL:</strong> 모든 페이지에 self-referencing canonical 권장<br>
                                    - <strong>Hreflang:</strong> 다국어 사이트의 경우 x-default 포함 필수<br><br>

                                    <strong>🔍 검색엔진 노출 영향도</strong><br>
                                    • Title/Description 최적화 → 클릭률(CTR) 최대 30% 향상<br>
                                    • Open Graph 구현 → 소셜 공유율 최대 40% 증가<br>
                                    • Canonical 설정 → 중복 콘텐츠 패널티 100% 방지<br>
                                    • 메타데이터 종합 최적화 → 검색 트래픽 평균 20-50% 상승
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                        복사
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
