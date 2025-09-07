<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="canonical" href="https://www.web-psqc.com/{{ request()->path() != '/' ? request()->path() : '' }}" />

    @include('inc.component.seo')
    @include('inc.component.theme_css')

    <!-- Fonts: Main Inter + Noto Sans, Signature Allura -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans:wght@400;500;700&family=Allura&display=swap"
        rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 8mm 8mm 10mm 8mm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html,
        body {
            font-family: 'Inter', 'Noto Sans', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.34;
            background: transparent !important;
        }

        /* Print background preservation */
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* A4 fit: slightly reduced (CSS) */
        .print-container {
            width: 185mm;
            margin: 0 auto;
        }

        /* Title spacing 200% expansion */
        .title-block {
            padding: 28px 0 40px;
            position: relative;
        }

        .title-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .title-text {
            text-align: center;
        }

        .title-qr {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .title-block h1 {
            font-size: 22px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        .title-block h2 {
            font-size: 15px;
            margin: 0;
            font-weight: 600;
        }

        /* Card/Table/Alert compact (reduce spacing for single page) */
        .card {
            margin-bottom: 8px;
            border-radius: 8px;
        }

        .card-body {
            padding: 8px 10px;
        }

        .table {
            font-size: 11.2px;
        }

        .table th,
        .table td {
            padding: 5px 7px;
        }

        .alert {
            padding: 7px 9px;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .alert .fw-semibold {
            font-weight: 600;
        }

        .tight p {
            margin: 0 0 3px;
            line-height: 1.26;
        }

        /* Left score card more compact */
        .score-card .h1 {
            font-size: 20px;
            margin: 0;
        }

        .score-card .h4 {
            font-size: 13px;
            margin: 2px 0 0;
        }

        .score-card .mb-2 {
            margin-bottom: 6px !important;
        }

        .score-card small {
            font-size: 10.5px;
        }

        /* Prevent page breaks within cards/tables/alerts */
        .card,
        .table,
        .alert {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Signature: complete border/background removal + font replacement */
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

        /* Remove unnecessary titles: hide summary table title */
        .summary-title {
            display: none;
        }
    </style>
</head>

<body class="bg-white">
    <div class="print-container">

        @if ($test_type == 'p-speed')
            @php
                $results = $currentTest->results['results'] ?? [];
                $probeErrors = $currentTest->results['errors'] ?? [];

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

                $firstTTFB = $firstLoad = $repeatTTFB = $repeatLoad = [];
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

                $originRegion = null;
                $originTTFB = null;
                $originLoad = null;
                if (!empty($firstTTFB)) {
                    $tmp = $firstTTFB;
                    asort($tmp);
                    $originRegion = array_key_first($tmp);
                    $originTTFB = $tmp[$originRegion] ?? null;
                    $originLoad = $firstLoad[$originRegion] ?? (count($firstLoad) ? min($firstLoad) : null);
                }

                $avgTTFB = count($firstTTFB) ? array_sum($firstTTFB) / count($firstTTFB) : null;
                $avgLoad = count($firstLoad) ? array_sum($firstLoad) / count($firstLoad) : null;
                $worstTTFB = count($firstTTFB) ? max($firstTTFB) : null;
                $worstLoad = count($firstLoad) ? max($firstLoad) : null;

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
                $repeatImprovePct = $eligibleRegions ? ($improvedRegions / $eligibleRegions) * 100.0 : null;

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

                $fmt = fn($v, $unit = 'ms') => is_numeric($v) ? number_format($v, 1) . $unit : 'No Data';
                $fmtPct = fn($v) => is_numeric($v) ? number_format($v, 1) . '%' : 'No Data';
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Global Speed Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table (Title removed) -->
                <div class="col-8">
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
                                    <td><strong>Origin ({{ $originRegion ? ucfirst($originRegion) : 'N/A' }})</strong>
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
                                    <td><strong>All Regions (Max)</strong></td>
                                    <td>{{ $fmt($worstTTFB) }}</td>
                                    <td>{{ $fmt($worstLoad) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Repeat Visit Improvement</strong></td>
                                    <td colspan="2">
                                        {{ $fmtPct($repeatImprovePct) }}
                                        @if ($eligibleRegions)
                                            <span class="text-muted">({{ $improvedRegions }} / {{ $eligibleRegions }}
                                                regions improved)</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete (title size removed) -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on web performance test results conducted through our <strong>global 8-region measurement network</strong>.</p>
                    <p>All data was collected by <u>simulating real user environments</u>, and the authenticity of results can be verified by anyone through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade based on measurements from major global regions,
                        proving <u>top 10% web quality performance</u>. This demonstrates an excellent website with
                        <strong>fast response times</strong> and <strong>global user-friendliness</strong>.
                    </p>
                </div>
            @endif
            @if ($currentTest->metrics)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">Regional Access Speeds</div>
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
                                        $formatMetric = function ($first, $repeat, $unit = 'ms') {
                                            if ($first === null) {
                                                return '<span class="text-muted">No Data</span>';
                                            }
                                            $firstFormatted = is_numeric($first) ? number_format($first, 1) : $first;
                                            $out = "<strong>{$firstFormatted}{$unit}</strong>";
                                            if ($repeat !== null) {
                                                $repeatFormatted = is_numeric($repeat)
                                                    ? number_format($repeat, 1)
                                                    : $repeat;
                                                $delta = $repeat - $first;
                                                $deltaFormatted = ($delta >= 0 ? '+' : '') . number_format($delta, 1);
                                                $deltaClass =
                                                    $delta < 0
                                                        ? 'text-success'
                                                        : ($delta > 0
                                                            ? 'text-danger'
                                                            : 'text-muted');
                                                $out .= "<br><small>{$repeatFormatted}{$unit} <span class='{$deltaClass}'>({$deltaFormatted})</span></small>";
                                            }
                                            return $out;
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
            <!-- Additional Information (tight line spacing) -->
            <div class="alert alert-info d-block tight">
                <p><strong>Display Format:</strong> <span class="fw-bold">First Visit</span> value → <span class="fw-bold">Repeat Visit</span> value (Δ
                    difference),
                    <span class="text-success">Green = Improvement</span> | <span class="text-danger">Red = Degradation</span>
                </p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>TTFB (Time To First Byte):</strong> Time from when user sends request until receiving the first response byte from server</p>
                <p><strong>Load Time:</strong> Time until page is completely displayed with all resources (HTML, CSS, JS, images) loaded in browser</p>
                <p><strong>Repeat Visit Performance:</strong> Shows faster loading speeds on repeat visits due to browser cache, Keep-Alive connections, and CDN caching effects</p>
            </div>
            <!-- Issue/Expiry single line + Signature single line -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'p-load')
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
                $requestsPerSec = $metrics['http_reqs_rate'] ?? 0;
                $vus = $config['vus'] ?? 'N/A';
                $duration = $config['duration_seconds'] ?? 'N/A';

                $fmt = fn($v, $unit = 'ms') => is_numeric($v) ? number_format($v, 1) . $unit : 'No Data';
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(K6 Load Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td>{{ number_format($totalRequests) }} ({{ number_format($requestsPerSec, 1) }}
                                        req/s)</td>
                                </tr>
                                <tr>
                                    <td><strong>P95 Response Time</strong></td>
                                    <td>{{ number_format($p95Response) }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>Error Rate</strong></td>
                                    <td class="{{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($failureRate, 2) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Load Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on results from <strong>K6 load testing</strong> with <strong>{{ $vus }} concurrent users</strong>
                        simulating real usage patterns for <strong>{{ $duration }} seconds</strong>.
                    </p>
                    <p>All data was collected by mimicking actual traffic environments, and the authenticity of results can be verified through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on server environment and optimization efforts.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from load testing results,
                        proving <u>high concurrent connection handling capacity</u>. This demonstrates a website with
                        <strong>stable service</strong> and <strong>excellent server performance</strong>.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">Detailed Performance Metrics</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Response Time</th>
                                    <th>Value</th>
                                    <th>Data Transfer</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Average</td>
                                    <td>{{ $fmt($metrics['http_req_duration_avg'] ?? 0) }}</td>
                                    <td>Data Received</td>
                                    <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }} MB</td>
                                </tr>
                                <tr>
                                    <td>P90</td>
                                    <td>{{ $fmt($metrics['http_req_duration_p90'] ?? 0) }}</td>
                                    <td>Data Sent</td>
                                    <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }} MB</td>
                                </tr>
                                <tr>
                                    <td>P95</td>
                                    <td>{{ $fmt($metrics['http_req_duration_p95'] ?? 0) }}</td>
                                    <td>Iterations</td>
                                    <td>{{ $metrics['iterations'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Max</td>
                                    <td>{{ $fmt($metrics['http_req_duration_max'] ?? 0) }}</td>
                                    <td>Think Time</td>
                                    <td>{{ $config['think_time_min'] ?? 3 }}-{{ $config['think_time_max'] ?? 10 }} seconds
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Error Rate Standards:</strong> <span class="text-success">Under 1% = Excellent</span> | <span class="text-danger">5% or higher = Needs improvement</span></p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Virtual Users:</strong> Number of concurrent virtual users | <strong>P95:</strong> Time within which 95% of requests were responded to</p>
                <p><strong>Think Time:</strong> Wait time mimicking real user page navigation patterns</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'p-mobile')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Mobile Performance Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td><strong>Median Response Time Average</strong></td>
                                    <td>{{ $overall['medianAvgMs'] ?? 0 }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>Long Tasks Average</strong></td>
                                    <td>{{ $overall['longTasksAvgMs'] ?? 0 }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>JS Runtime Errors (First/Third Party)</strong></td>
                                    <td>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }} /
                                        {{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Viewport Overflow</strong></td>
                                    <td>{{ !empty($overall['bodyOverflowsViewport']) ? 'Present' : 'None' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Mobile Performance Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on results from simulating real mobile environments on <strong>6 representative mobile devices</strong>
                        using <strong>Playwright</strong> with 4x CPU throttling.</p>
                    <p>Tests were conducted on 3 iOS devices (iPhone SE, 11, 15 Pro) and 3 Android devices (Galaxy S9+, S20 Ultra, Pixel 5).</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on website optimization efforts.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from mobile performance testing,
                        proving <u>excellent mobile optimization level</u>. This demonstrates a website with
                        <strong>fast mobile rendering</strong> and <strong>stable runtime</strong>.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">Device-by-Device Results</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Device</th>
                                    <th>Median</th>
                                    <th>TBT</th>
                                    <th>JS (First/Third)</th>
                                    <th>Viewport</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($results, 0, 6) as $result)
                                    <tr>
                                        <td><strong>{{ str_replace(['iPhone ', 'Galaxy ', 'Pixel '], ['i', 'G', 'P'], $result['device'] ?? 'Unknown') }}</strong>
                                        </td>
                                        <td>{{ $result['medianMs'] ?? 0 }}ms</td>
                                        <td>{{ $result['longTasksTotalMs'] ?? 0 }}ms</td>
                                        <td>{{ $result['jsErrorsFirstPartyCount'] ?? 0 }}/{{ $result['jsErrorsThirdPartyCount'] ?? 0 }}
                                        </td>
                                        <td>{{ !empty($result['bodyOverflowsViewport']) ? 'Overflow' : 'Normal' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Test Environment:</strong> 4 runs per device (excluding 1 warmup), 4x CPU throttling applied</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Median:</strong> Repeat visit loading median | <strong>TBT:</strong> JS blocking time (over 50ms) |
                    <strong>Viewport:</strong> Whether horizontal scrolling occurs
                </p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-ssl')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(SSL/TLS Security Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td class="{{ $forwardSecrecy ? 'text-success' : 'text-danger' }}">
                                        {{ $forwardSecrecy ? 'Supported' : 'Not Supported' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSTS</strong></td>
                                    <td class="{{ $hstsEnabled ? 'text-success' : 'text-warning' }}">
                                        {{ $hstsEnabled ? 'Enabled' : 'Disabled' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Vulnerabilities</strong></td>
                                    <td class="{{ $vulnerableCount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $vulnerableCount > 0 ? $vulnerableCount . ' found' : 'None' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ SSL/TLS Security Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on comprehensive examination of the server's SSL/TLS configuration using <strong>testssl.sh</strong>.</p>
                    <p>Supported protocols, cipher suites, certificate validity, and known vulnerabilities were comprehensively verified.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration and security updates.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from SSL/TLS security testing,
                        proving <u>highest level security configuration</u>. This demonstrates a website with
                        <strong>secure encrypted communication</strong> and <strong>latest security standards compliance</strong>.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">Security Details</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Certificate Info</th>
                                    <th>Value</th>
                                    <th>Protocol Support</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Issuer</td>
                                    <td>{{ substr($results['certificate']['issuer'] ?? 'N/A', 0, 20) }}</td>
                                    <td>Supported Protocols</td>
                                    <td>{{ isset($results['supported_protocols']) ? implode(', ', array_slice($results['supported_protocols'], 0, 2)) : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Valid Until</td>
                                    <td>{{ $results['cert_expiry'] ?? 'N/A' }}</td>
                                    <td>Vulnerable Protocols</td>
                                    <td
                                        class="{{ isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0 ? implode(', ', $results['vulnerable_protocols']) : 'None' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Key Size</td>
                                    <td>{{ $results['certificate']['key_size'] ?? 'N/A' }}</td>
                                    <td>IP Address</td>
                                    <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($vulnerableCount > 0)
                <div class="alert alert-warning d-block tight">
                    @php
                        $vulnList = [];
                        foreach ($results['vulnerabilities'] as $vuln => $status) {
                            if ($status['vulnerable'] ?? false) {
                                $vulnList[] = strtoupper(str_replace(['_', '-'], ' ', $vuln));
                            }
                        }
                    @endphp
                    <p><strong>Vulnerabilities:</strong>
                        {{ implode(', ', array_slice($vulnList, 0, 5)) }}{{ count($vulnList) > 5 ? ' and ' . (count($vulnList) - 5) . ' more' : '' }}
                    </p>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>testssl.sh:</strong> GitHub 10K+ stars open source | <strong>PFS:</strong> Perfect Forward Secrecy |
                    <strong>HSTS:</strong> HTTPS enforcement
                </p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Inspection Items:</strong> Comprehensive examination of major SSL/TLS vulnerabilities including Heartbleed, POODLE, BEAST, CRIME, FREAK</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-sslyze')
            @php
                $results = $currentTest->results;
                $analysis = $results['analysis'] ?? [];
                $issues = $results['issues'] ?? [];

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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(SSL/TLS Deep Analysis)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}%</td>
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
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ SSL/TLS Deep Analysis Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on comprehensive SSL/TLS configuration analysis using <strong>SSLyze v5.x</strong>.</p>
                    <p>All security elements including TLS protocols, cipher suites, certificate chains, OCSP, and HSTS were precisely examined.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration changes.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from SSL/TLS deep analysis,
                        proving <u>highest level encryption security</u>. This demonstrates a website with
                        <strong>latest TLS protocols</strong> and <strong>strong cipher suite configuration</strong>.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">Detailed Analysis Results</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Cipher Suite Analysis</th>
                                    <th>Value</th>
                                    <th>Certificate Info</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TLS 1.2 Cipher Suites</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['total'] ?? 0 }} suites</td>
                                    <td>Key Algorithm</td>
                                    <td>{{ $analysis['certificate']['details']['key_algorithm'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Strong Ciphers</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['strong'] ?? 0 }} suites</td>
                                    <td>Key Size</td>
                                    <td>{{ $analysis['certificate']['details']['key_size'] ?? 'N/A' }} bits</td>
                                </tr>
                                <tr>
                                    <td>Weak Ciphers</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['weak'] ?? 0 }} suites</td>
                                    <td>Days to Expiry</td>
                                    <td>{{ $analysis['certificate']['details']['days_to_expiry'] ?? 'N/A' }} days</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if (!empty($issues) && count($issues) > 0)
                <div class="alert alert-warning d-block tight">
                    <p><strong>Security Issues:</strong>
                        {{ implode(', ', array_slice($issues, 0, 3)) }}{{ count($issues) > 3 ? ' and ' . (count($issues) - 3) . ' more' : '' }}
                    </p>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>SSLyze:</strong> Mozilla/Qualys/IETF recommended tool | <strong>PFS:</strong>
                    {{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}% | <strong>TLS 1.3:</strong>
                    {{ $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false ? 'Supported' : 'Not Supported' }}</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Inspection Items:</strong> TLS protocols, cipher suite strength, certificate chains, OCSP Stapling, HSTS, elliptic curve cryptography</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-header')
            @php
                $report = $currentTest->results;
                $metrics = $currentTest->metrics;
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

                $csp = $metrics['headers']['csp'] ?? [];
                $hsts = $metrics['headers']['hsts'] ?? [];

                $presentHeaders = 0;
                foreach ($metrics['breakdown'] ?? [] as $header) {
                    if (!empty($header['value'])) {
                        $presentHeaders++;
                    }
                }
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Security Headers Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                        {{ $csp['present'] ?? false ? ($csp['strong'] ?? false ? 'Strong' : 'Weak') : 'None' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSTS</strong></td>
                                    <td class="{{ $hsts['present'] ?? false ? 'text-success' : 'text-danger' }}">
                                        {{ $hsts['present'] ?? false ? 'Configured (' . number_format(($hsts['max_age'] ?? 0) / 86400) . ' days)' : 'None' }}
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
                                        {{ substr($xfo ?: 'None', 0, 20) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Security Headers Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on comprehensive examination of <strong>6 core security headers</strong> to measure web security level.</p>
                    <p>CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, and HSTS were examined.
                    </p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on server configuration changes.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from security headers testing,
                        proving <u>excellent web security configuration</u>. This demonstrates a website with
                        <strong>strong defense systems</strong> against major web vulnerabilities such as <strong>XSS and clickjacking</strong>.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">Header-by-Header Scores</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Header</th>
                                    <th>Value</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($metrics['breakdown'] ?? [], 0, 6) as $item)
                                    <tr>
                                        <td><strong>{{ str_replace(['Content-Security-Policy', 'X-Content-Type-Options', 'Permissions-Policy', 'Strict-Transport-Security'], ['CSP', 'X-C-T-O', 'Perm-Policy', 'HSTS'], $item['key']) }}</strong>
                                        </td>
                                        <td class="text-truncate" style="max-width: 250px;">
                                            {{ substr($item['value'] ?? 'None', 0, 30) }}{{ strlen($item['value'] ?? '') > 30 ? '...' : '' }}
                                        </td>
                                        <td>{{ round((($item['score'] ?? 0) * 100) / 60, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>CSP:</strong> XSS Defense | <strong>XFO:</strong> Clickjacking Prevention | <strong>HSTS:</strong> HTTPS Enforcement</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>6 Core Headers:</strong> CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy,
                    Permissions-Policy, HSTS</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-scan')
            @php
                $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                $technologies = $currentTest->results['technologies'] ?? [];
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

                $totalVulns =
                    ($vulnerabilities['critical'] ?? 0) +
                    ($vulnerabilities['high'] ?? 0) +
                    ($vulnerabilities['medium'] ?? 0) +
                    ($vulnerabilities['low'] ?? 0) +
                    ($vulnerabilities['informational'] ?? 0);
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Security Vulnerability Scan)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Risk Level</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Critical</strong></td>
                                    <td class="{{ ($vulnerabilities['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $vulnerabilities['critical'] ?? 0 }} issues
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>High</strong></td>
                                    <td class="{{ ($vulnerabilities['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $vulnerabilities['high'] ?? 0 }} issues
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Medium</strong></td>
                                    <td class="{{ ($vulnerabilities['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
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
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Security Vulnerability Scan Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on web security vulnerability analysis using <strong>OWASP ZAP</strong> passive scanning.</p>
                    <p>Security headers, sensitive information exposure, and session management were non-intrusively examined, discovering a total of {{ $totalVulns }} issues.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on security updates.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from security vulnerability scanning,
                        proving <u>excellent security level</u>. This demonstrates a website with
                        <strong>no major security vulnerabilities</strong> and <strong>secure configuration</strong>.
                    </p>
                </div>
            @endif
            @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">Key Findings</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vulnerability Name</th>
                                        <th>Risk Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($vulnerabilities['details'], 0, 5) as $vuln)
                                        <tr>
                                            <td>{{ substr($vuln['name'], 0, 50) }}{{ strlen($vuln['name']) > 50 ? '...' : '' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ match ($vuln['risk']) {
                                                        'critical' => 'bg-red-lt text-red-lt-fg',
                                                        'high' => 'bg-orange-lt text-orange-lt-fg',
                                                        'medium' => 'bg-yellow-lt text-yellow-lt-fg',
                                                        'low' => 'bg-blue-lt text-blue-lt-fg',
                                                        default => 'bg-azure-lt text-azure-lt-fg',
                                                    } }}">{{ ucfirst($vuln['risk']) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>OWASP ZAP:</strong> Global standard web security testing tool | <strong>Passive Scan:</strong> Non-intrusive HTTP response analysis</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Inspection:</strong> Security headers, sensitive information exposure, session management, technology stack | <strong>Findings:</strong> {{ $totalVulns }} issues,
                    {{ count($technologies) }} technologies</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-nuclei')
            @php
                $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
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

                $totalVulns =
                    ($metrics['vulnerability_counts']['critical'] ?? 0) +
                    ($metrics['vulnerability_counts']['high'] ?? 0) +
                    ($metrics['vulnerability_counts']['medium'] ?? 0) +
                    ($metrics['vulnerability_counts']['low'] ?? 0) +
                    ($metrics['vulnerability_counts']['info'] ?? 0);
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Latest CVE Vulnerability Scan)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date (Compact) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Risk Level</th>
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
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Latest CVE Vulnerability Scan Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on latest CVE vulnerability analysis using <strong>Nuclei by ProjectDiscovery</strong>.</p>
                    <p>2024-2025 new CVEs, zero-days, and configuration errors were examined, discovering a total of {{ $totalVulns }} issues.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on security patches.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from latest CVE vulnerability scanning,
                        proving <u>excellent response to latest security threats</u>. This demonstrates a website with
                        <strong>2024-2025 CVE patches</strong> and <strong>secure configuration management</strong>.
                    </p>
                </div>
            @endif
            @php
                $criticalHighList = [];
                foreach (['critical', 'high'] as $severity) {
                    foreach (array_slice($vulnerabilities[$severity] ?? [], 0, 2) as $vuln) {
                        $criticalHighList[] = ['name' => $vuln['name'] ?? 'Unknown', 'severity' => $severity];
                    }
                }
            @endphp
            @if (count($criticalHighList) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">Major Vulnerabilities</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vulnerability Name</th>
                                        <th>Risk Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($criticalHighList as $item)
                                        <tr>
                                            <td>{{ substr($item['name'], 0, 50) }}{{ strlen($item['name']) > 50 ? '...' : '' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $item['severity'] === 'critical' ? 'bg-red-lt text-red-lt-fg' : 'bg-orange-lt text-orange-lt-fg' }}">
                                                    {{ ucfirst($item['severity']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Nuclei:</strong> Template-based vulnerability scanner | <strong>Scanned:</strong>
                    {{ $metrics['templates_matched'] ?? 0 }} templates | <strong>Duration:</strong>
                    {{ $metrics['scan_duration'] ?? 0 }} seconds</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Coverage:</strong> 2024-2025 CVEs, Log4Shell, Spring4Shell, WordPress/Joomla/Drupal, Git/ENV exposure
                </p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-lighthouse')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Google Lighthouse Quality Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: 4 Category Scores -->
                <div class="col-8">
                    <div class="row">
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['performance_score'] ?? 'N/A' }}</h3>
                                    <small>Performance</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['accessibility_score'] ?? 'N/A' }}</h3>
                                    <small>Accessibility</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['best_practices_score'] ?? 'N/A' }}</h3>
                                    <small>Best Practices</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['seo_score'] ?? 'N/A' }}</h3>
                                    <small>SEO</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on web quality test results conducted through the <strong>Google Lighthouse engine</strong>.</p>
                    <p>All data was collected by <u>simulating real browser environments</u>, and the authenticity of results can be verified by anyone through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from Google Lighthouse quality measurement,
                        proving <u>top 10% web quality level</u>. This demonstrates a high-quality website with
                        <strong>excellent performance</strong> and <strong>high accessibility and SEO optimization</strong>.
                    </p>
                </div>
            @endif
            
            <!-- Core Web Vitals -->
            @if(isset($results['audits']))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">Core Web Vitals Results</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Metric</th>
                                        <th>Measured Value</th>
                                        <th>Recommended Standard</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($results['audits']['first-contentful-paint']))
                                        <tr>
                                            <td><strong>FCP</strong></td>
                                            <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">Within 1.8s</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['largest-contentful-paint']))
                                        <tr>
                                            <td><strong>LCP</strong></td>
                                            <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">Within 2.5s</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['cumulative-layout-shift']))
                                        <tr>
                                            <td><strong>CLS</strong></td>
                                            <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">Below 0.1</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['total-blocking-time']))
                                        <tr>
                                            <td><strong>TBT</strong></td>
                                            <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">Within 200ms</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['speed-index']))
                                        <tr>
                                            <td><strong>Speed Index</strong></td>
                                            <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">Within 3.4s</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>4 Assessment Categories:</strong> Performance, Accessibility, Best Practices, SEO</p>
                <p class="text-muted small">Each category is evaluated out of 100 points, and the overall score is a weighted average of the 4 categories.</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>FCP:</strong> First Contentful Paint time | <strong>LCP:</strong> Largest Contentful Paint timing</p>
                <p><strong>CLS:</strong> Cumulative Layout Shift score | <strong>TBT:</strong> Total Blocking Time</p>
            </div>
            
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-accessibility')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Web Accessibility Audit)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Violation Summary -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-danger">{{ $counts['critical'] ?? 0 }}</div>
                                    <small>Critical</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-orange">{{ $counts['serious'] ?? 0 }}</div>
                                    <small>Serious</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-warning">{{ $counts['moderate'] ?? 0 }}</div>
                                    <small>Moderate</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-info">{{ $counts['minor'] ?? 0 }}</div>
                                    <small>Minor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <strong>Total Violations: {{ $counts['total'] ?? 0 }} issues</strong>
                    </div>
                </div>
            </div>
            
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on web accessibility test results conducted through the <strong>axe-core engine (Deque Systems)</strong>.</p>
                    <p>All data was collected according to <u>WCAG 2.1 international standards</u>, and the authenticity of results can be verified by anyone through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from web accessibility audit,
                        proving <u>excellent web accessibility level</u>. This demonstrates an inclusive website that can be 
                        equally used by <strong>all users including people with disabilities and seniors</strong>.
                    </p>
                </div>
            @endif
            
            <!-- Major Violations -->
            @if (!empty($violations) && count($violations) > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">Major Violations (Top 5)</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">Severity</th>
                                        <th>Issue Description</th>
                                        <th width="60">Impact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($violations, 0, 5) as $violation)
                                        @php
                                            $impactClass = match (strtolower($violation['impact'])) {
                                                'critical' => 'text-danger',
                                                'serious' => 'text-orange',
                                                'moderate' => 'text-warning',
                                                default => 'text-info',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="{{ $impactClass }}">
                                                <strong>{{ ucfirst($violation['impact']) }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($violation['help'], 80) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ count($violation['nodes'] ?? []) }} nodes</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Accessibility Severity:</strong> 
                    <span class="text-danger">Critical</span> (Function blocking) | 
                    <span class="text-orange">Serious</span> (Major limitations) | 
                    <span class="text-warning">Moderate</span> (Partial inconvenience) | 
                    <span class="text-info">Minor</span> (Minor issues)
                </p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>WCAG 2.1 4 Principles:</strong> Perceivable, Operable, Understandable, Robust</p>
                <p><strong>Legal Compliance:</strong> Korea Disability Discrimination Act, US ADA, EU EN 301 549 standards applied</p>
            </div>
            
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-compatibility')
            @php
                $report = $currentTest->results['report'] ?? [];
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
                $totals = $report['totals'] ?? [];
                $okCount = $totals['okCount'] ?? 0;
                $jsFirstPartyTotal = $totals['jsFirstPartyTotal'] ?? 0;
                $jsThirdPartyTotal = $totals['jsThirdPartyTotal'] ?? null;
                $cssTotal = $totals['cssTotal'] ?? 0;
                $strictMode = !empty($report['strictMode']);
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Browser Compatibility Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Overall Results -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $okCount }}/3</h3>
                                    <small>Normal Browsers</small>
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
                                    <h5 class="mb-0">{{ $strictMode ? 'Strict' : 'Default' }}</h5>
                                    <small>Test Mode</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!is_null($jsThirdPartyTotal))
                        <div class="text-center mt-1">
                            <small class="text-muted">Third-party JS errors: {{ $jsThirdPartyTotal }}</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on browser compatibility test results conducted through the <strong>Playwright engine (Microsoft)</strong>.</p>
                    <p>All data was collected from <u>3 major browsers: Chrome, Firefox, and Safari</u>, and the authenticity of results can be verified by anyone through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from browser compatibility testing,
                        proving <u>excellent cross-browser compatibility</u>. This demonstrates a high-quality website that 
                        operates stably across <strong>all major browsers</strong>.
                    </p>
                </div>
            @endif
            
            <!-- Browser-by-Browser Results -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">Browser-by-Browser Results</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Browser</th>
                                    <th>Status</th>
                                    <th>JS First-party</th>
                                    <th>CSS</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['perBrowser'] as $browser)
                                    @php
                                        $jsFirst = $browser['jsFirstPartyCount'] ?? ($browser['jsErrorCount'] ?? 0);
                                        $browserOk = !empty($browser['ok']);
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $browser['browser'] ?? '' }}</strong></td>
                                        <td>
                                            @if ($browserOk)
                                                <span class="text-success">✓</span>
                                            @else
                                                <span class="text-danger">✗</span>
                                            @endif
                                        </td>
                                        <td>{{ $jsFirst }}</td>
                                        <td>{{ $browser['cssErrorCount'] ?? 0 }}</td>
                                        <td>
                                            @if (!empty($browser['navError']))
                                                <small class="text-danger">Error</small>
                                            @else
                                                <small class="text-muted">Normal</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Test Browsers:</strong> Chromium (Chrome/Edge), Firefox (Gecko), WebKit (Safari)</p>
                <p><strong>Measurement Metrics:</strong> Normal loading status, JavaScript errors (first-party/third-party classification), CSS parsing errors</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>Market Share:</strong> Chrome 65%, Safari 19%, Firefox 3% (2024 data)</p>
                <p><strong>Test Mode:</strong> {{ $strictMode ? 'Strict mode - includes all errors' : 'Default mode - focuses on first-party errors' }}</p>
            </div>
            
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-visual')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Responsive UI Compatibility Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Overall Results -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ 9 - $overflowCount }}/9</h3>
                                    <small>Normal Viewports</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $overflowCount }}</h3>
                                    <small>Overflow Count</small>
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
                    </div>
                </div>
            </div>
            
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on responsive UI test results conducted through the <strong>Playwright engine (Chromium)</strong>.</p>
                    <p>All data was collected from <u>9 major device viewports</u>, and the authenticity of results can be verified by anyone through our QR verification system.</p>
                    <p class="text-muted small">※ This test represents objective measurement results at a specific point in time and may vary depending on continuous improvement and optimization efforts.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from responsive UI testing,
                        proving <u>excellent responsive web design</u>. This demonstrates a user-friendly website that 
                        displays perfectly on <strong>all devices</strong> without horizontal scrolling.
                    </p>
                </div>
            @endif
            
            <!-- Viewport-by-Viewport Results -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">Viewport-by-Viewport Results</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Device</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Overflow</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($perViewport, 0, 9) as $vp)
                                    @php
                                        $hasOverflow = $vp['overflow'] ?? false;
                                        $overflowPx = $vp['overflowPx'] ?? 0;
                                        $deviceName = ucfirst(str_replace('-', ' ', explode('-', $vp['viewport'])[0] ?? ''));
                                    @endphp
                                    <tr>
                                        <td><small><strong>{{ Str::limit($deviceName, 10) }}</strong></small></td>
                                        <td><small>{{ $vp['w'] ?? 0 }}×{{ $vp['h'] ?? 0 }}</small></td>
                                        <td>
                                            @if ($hasOverflow)
                                                <span class="text-danger">✗</span>
                                            @else
                                                <span class="text-success">✓</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($overflowPx > 0)
                                                <small class="text-danger">+{{ $overflowPx }}px</small>
                                            @else
                                                <small class="text-muted">0</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Test Viewports:</strong> Mobile (360-414px), Foldable (672px), Tablet (768-1024px), Desktop (1280-1440px)</p>
                <p><strong>Measurement Criteria:</strong> body render width vs viewport width comparison (horizontal scrolling occurs when exceeded)</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>Assessment Reason:</strong> {{ $reason }}</p>
                <p><strong>Mobile Traffic:</strong> Over 60% of total web traffic (2024 data)</p>
            </div>
            
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-links')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Link Validation Test)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td><strong>Total</strong></td>
                                    <td>{{ $totals['httpChecked'] ?? 0 }} links</td>
                                    <td>{{ ($totals['internalErrors'] ?? 0) + ($totals['externalErrors'] ?? 0) }}</td>
                                    <td>{{ $rates['overallErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Internal</strong></td>
                                    <td>{{ $totals['internalChecked'] ?? 0 }} links</td>
                                    <td>{{ $totals['internalErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['internalErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>External</strong></td>
                                    <td>{{ $totals['externalChecked'] ?? 0 }} links</td>
                                    <td>{{ $totals['externalErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['externalErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Images</strong></td>
                                    <td>{{ $totals['imageChecked'] ?? 0 }} links</td>
                                    <td>{{ $totals['imageErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['imageErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Anchors</strong></td>
                                    <td>{{ $totals['anchorChecked'] ?? 0 }} links</td>
                                    <td>{{ $totals['anchorErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['anchorErrorRate'] ?? 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on comprehensive link validation results conducted through <strong>Playwright-based link validation tools</strong>.</p>
                    <p>All data was collected in <u>real browser environments</u> including JavaScript dynamic content.</p>
                    <p class="text-muted small">※ This inspection represents link status at a specific point in time and may vary due to external site changes.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from link validation testing,
                        proving <u>excellent website link integrity</u>.
                    </p>
                </div>
            @endif
            <!-- Error Link Samples -->
            @php
                $linkSamples = $samples['links'] ?? [];
                $imageSamples = $samples['images'] ?? [];
                $anchorSamples = $samples['anchors'] ?? [];
                $totalErrorSamples = count($linkSamples) + count($imageSamples) + count($anchorSamples);
            @endphp
            @if ($totalErrorSamples > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">Error Link Samples</div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>URL/Link</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sampleCount = 0; @endphp
                                    @foreach (array_slice($linkSamples, 0, 3) as $sample)
                                        @php $sampleCount++; @endphp
                                        <tr>
                                            <td>Link</td>
                                            <td class="text-break small">{{ Str::limit($sample['url'] ?? '', 50) }}</td>
                                            <td>{{ $sample['status'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach (array_slice($imageSamples, 0, 3 - $sampleCount) as $sample)
                                        @php $sampleCount++; @endphp
                                        <tr>
                                            <td>Image</td>
                                            <td class="text-break small">{{ Str::limit($sample['url'] ?? '', 50) }}</td>
                                            <td>{{ $sample['status'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach (array_slice($anchorSamples, 0, 6 - $sampleCount) as $sample)
                                        <tr>
                                            <td>Anchor</td>
                                            <td class="text-break small">{{ $sample['href'] ?? '' }}</td>
                                            <td>Missing</td>
                                        </tr>
                                    @endforeach
                                    @if ($totalErrorSamples > 6)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... Total {{ $totalErrorSamples }} errors detected</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Redirect Information -->
            <div class="alert alert-secondary d-block tight">
                <p><strong>Max Redirect Chain:</strong> {{ $totals['maxRedirectChainEffective'] ?? 0 }} steps
                @if (($totals['maxRedirectChainEffective'] ?? 0) > 2)
                    <span class="text-warning">(optimization needed)</span>
                @endif
                </p>
                @if (!empty($totals['navError']))
                    <p class="text-danger small mb-0">Navigation Error: {{ Str::limit($totals['navError'], 80) }}</p>
                @endif
            </div>
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Link Integrity Effects:</strong> 20%↓ bounce rate, 15%↑ page speed, 25%↑ user satisfaction</p>
                <p>Immediate 404 error fixes | Minimize redirects | Verify anchor matching | Regular inspection required</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Assessment Reason:</strong> {{ Str::limit($overall['reason'] ?? 'Comprehensive evaluation results', 100) }}</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-structure')
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
                $types = $results['types'] ?? [];
                $richTypes = $totals['richEligibleTypes'] ?? [];
                $totalErrors = ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0);
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Structured Data Validation)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>JSON-LD</strong></td>
                                    <td>{{ $totals['jsonLdBlocks'] ?? 0 }} blocks</td>
                                    <td>{{ ($totals['jsonLdBlocks'] ?? 0) > 0 ? 'Implemented' : 'Not Implemented' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Schemas</strong></td>
                                    <td>{{ $totals['jsonLdItems'] ?? 0 }} items</td>
                                    <td>
                                        @if (($totals['jsonLdItems'] ?? 0) >= 3)
                                            Sufficient
                                        @elseif (($totals['jsonLdItems'] ?? 0) > 0)
                                            Basic
                                        @else
                                            None
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Errors/Warnings</strong></td>
                                    <td>{{ $totalErrors }}/{{ $totals['itemWarnings'] ?? 0 }}</td>
                                    <td>
                                        @if ($totalErrors === 0 && ($totals['itemWarnings'] ?? 0) === 0)
                                            Perfect
                                        @elseif ($totalErrors === 0)
                                            Good
                                        @else
                                            Needs Improvement
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Rich Results</strong></td>
                                    <td>{{ is_array($richTypes) ? count($richTypes) : 0 }} types</td>
                                    <td>
                                        @if (is_array($richTypes) && count($richTypes) > 0)
                                            {{ implode(', ', array_slice($richTypes, 0, 2)) }}
                                        @else
                                            None
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Other Formats</strong></td>
                                    <td colspan="2">
                                        Microdata: {{ !empty($totals['hasMicrodata']) ? '✓' : '✗' }}
                                        RDFa: {{ !empty($totals['hasRdfa']) ? '✓' : '✗' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on Schema.org specification inspection results conducted through <strong>Playwright-based structured data validation tools</strong>.</p>
                    <p>All data was evaluated according to <u>Google Rich Results Test standards</u>.</p>
                    <p class="text-muted small">※ This inspection represents structured data status at a specific point in time and may change with website updates.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from structured data validation,
                        qualifying for <u>Rich Snippets display in search results</u>.
                    </p>
                </div>
            @endif
            <!-- Schema Type Distribution -->
            @if (!empty($types))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">Schema Type Distribution</div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>@type</th>
                                        <th>Count</th>
                                        <th>Rich Results</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($types, 0, 5) as $row)
                                        <tr>
                                            <td><code>{{ $row['type'] }}</code></td>
                                            <td>{{ $row['count'] }}</td>
                                            <td>
                                                @if (in_array($row['type'], ['Article', 'Product', 'Recipe', 'Event', 'FAQPage', 'LocalBusiness', 'Review']))
                                                    ✓
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if (count($types) > 5)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... and {{ count($types) - 5 }} more types</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Validation Issues -->
            @if ($totalErrors > 0 || ($totals['itemWarnings'] ?? 0) > 0)
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ Validation Issues</p>
                    @if (!empty($parseErrors))
                        <p class="small mb-1">Parse errors: {{ count($parseErrors) }} blocks</p>
                    @endif
                    @php
                        $errorCount = 0;
                        $warningCount = 0;
                        foreach ($perItem as $item) {
                            if (!empty($item['errors'])) $errorCount++;
                            if (!empty($item['warnings'])) $warningCount++;
                        }
                    @endphp
                    @if ($errorCount > 0)
                        <p class="small mb-1">Item errors: {{ $errorCount }} items</p>
                    @endif
                    @if ($warningCount > 0)
                        <p class="small mb-0">Item warnings: {{ $warningCount }} items</p>
                    @endif
                </div>
            @endif
            <!-- Recommended Improvements -->
            @if (!empty($actions))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚡ Recommended Improvements</p>
                    <ul class="mb-0 small">
                        @foreach (array_slice($actions, 0, 3) as $action)
                            <li>{{ Str::limit($action, 80) }}</li>
                        @endforeach
                        @if (count($actions) > 3)
                            <li>... and {{ count($actions) - 3 }} more</li>
                        @endif
                    </ul>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Structured Data Benefits:</strong> 30%↑ CTR through Rich Snippets, voice search optimization, Knowledge Graph registration</p>
                <p>JSON-LD recommended | Schema.org standards | Organization + WebSite + BreadcrumbList required</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Assessment Reason:</strong> {{ Str::limit($results['overall']['reason'] ?? 'Comprehensive evaluation results', 100) }}</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-crawl')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Search Engine Crawling Audit)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td>{{ ($robots['exists'] ?? false) ? 'Exists' : 'Missing' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>sitemap.xml</strong></td>
                                    <td>{{ $sitemap['sitemapUrlCount'] ?? 0 }} URLs</td>
                                    <td>{{ ($sitemap['hasSitemap'] ?? false) ? 'Exists' : 'Missing' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Inspected Pages</strong></td>
                                    <td>{{ $pages['count'] ?? 0 }} pages</td>
                                    <td>Avg {{ number_format($pages['qualityAvg'] ?? 0, 1) }} points</td>
                                </tr>
                                <tr>
                                    <td><strong>Error Rate</strong></td>
                                    <td>{{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%</td>
                                    <td>
                                        @if (($pages['errorRate4xx5xx'] ?? 0) === 0.0)
                                            Normal
                                        @elseif (($pages['errorRate4xx5xx'] ?? 0) < 5)
                                            Good
                                        @else
                                            Issues
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Duplicate Rate</strong></td>
                                    <td>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</td>
                                    <td>{{ (($pages['duplicateRate'] ?? 0) <= 30) ? 'Good' : 'High' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on search engine crawling inspection results conducted through <strong>robots.txt compliant crawlers</strong>.</p>
                    <p>All data was collected by simulating <u>actual search engine crawling methods</u>.</p>
                    <p class="text-muted small">※ This inspection represents crawling status at a specific point in time and may change with website updates.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from crawling inspection,
                        proving it is a <u>search engine optimization excellent site</u>.
                    </p>
                </div>
            @endif
            <!-- Sitemap File Status -->
            @if (!empty($sitemap['sitemaps']))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">Sitemap File Status</div>
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
                                    @foreach (array_slice($sitemap['sitemaps'], 0, 5) as $s)
                                        <tr>
                                            <td>{{ basename($s['url']) }}</td>
                                            <td>{{ $s['count'] ?? 0 }} URLs</td>
                                            <td>{{ $s['ok'] ? 'Normal' : 'Error' }}</td>
                                        </tr>
                                    @endforeach
                                    @if (count($sitemap['sitemaps']) > 5)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... and {{ count($sitemap['sitemaps']) - 5 }} more files</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Problem Pages Summary -->
            @php
                $errorPages = $report['samples']['errorPages'] ?? [];
                $lowQuality = collect($report['samples']['lowQuality'] ?? [])
                    ->filter(function ($page) {
                        return ($page['score'] ?? 100) < 50;
                    })
                    ->take(3)
                    ->values()
                    ->toArray();
            @endphp
            @if (!empty($errorPages) || !empty($lowQuality))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ Detected Issues</p>
                    @if (!empty($errorPages))
                        <p class="small mb-1">Error pages (4xx/5xx): {{ count($errorPages) }} detected</p>
                    @endif
                    @if (!empty($lowQuality))
                        <p class="small mb-1">Low quality (under 50 points): {{ count($lowQuality) }} pages</p>
                    @endif
                    @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                        <p class="small mb-0">Duplicate content: {{ $pages['dupTitleCount'] ?? 0 }} titles, {{ $pages['dupDescCount'] ?? 0 }} descriptions</p>
                    @endif
                </div>
            @endif
            <!-- Crawling Plan Summary -->
            <div class="alert alert-secondary d-block tight">
                <p><strong>Crawling Plan:</strong> {{ $pages['count'] ?? 0 }} out of {{ $crawlPlan['candidateCount'] ?? 0 }} URLs inspected</p>
                @if (!empty($crawlPlan['skipped']))
                    <p class="small mb-0">Excluded URLs: {{ count($crawlPlan['skipped']) }} (robots.txt rules or external domains)</p>
                @endif
            </div>
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Crawling Optimization Benefits:</strong> 50%↑ indexing speed, 20%↑ search ranking, 15%↓ bounce rate</p>
                <p>robots.txt required | sitemap.xml required | unique metadata per page | remove 404 errors</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Assessment Reason:</strong> {{ Str::limit($report['overall']['reason'] ?? 'Comprehensive evaluation results', 100) }}</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-meta')
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
            @endphp
            <!-- Header -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>Web Test Certificate</h1>
                        <h2>(Metadata Completeness Audit)</h2>
                        <h3>Certificate Number: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.web-psqc.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Left: Grade/Score/URL/Date -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }} points</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                Test Date:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="col-8">
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
                                    <td><strong>Title</strong></td>
                                    <td>
                                        @if ($analysis['title']['isOptimal'] ?? false)
                                            Optimal
                                        @elseif ($analysis['title']['isAcceptable'] ?? false)
                                            Acceptable
                                        @elseif ($analysis['title']['isEmpty'] ?? true)
                                            Missing
                                        @else
                                            Inappropriate
                                        @endif
                                    </td>
                                    <td>{{ $summary['titleLength'] ?? 0 }} chars</td>
                                </tr>
                                <tr>
                                    <td><strong>Description</strong></td>
                                    <td>
                                        @if ($analysis['description']['isOptimal'] ?? false)
                                            Optimal
                                        @elseif ($analysis['description']['isAcceptable'] ?? false)
                                            Acceptable
                                        @elseif ($analysis['description']['isEmpty'] ?? true)
                                            Missing
                                        @else
                                            Inappropriate
                                        @endif
                                    </td>
                                    <td>{{ $summary['descriptionLength'] ?? 0 }} chars</td>
                                </tr>
                                <tr>
                                    <td><strong>Open Graph</strong></td>
                                    <td>
                                        @if ($analysis['openGraph']['isPerfect'] ?? false)
                                            Perfect
                                        @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                            Basic
                                        @else
                                            Insufficient
                                        @endif
                                    </td>
                                    <td>{{ $summary['openGraphFields'] ?? 0 }} fields</td>
                                </tr>
                                <tr>
                                    <td><strong>Twitter Cards</strong></td>
                                    <td>
                                        @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                            Perfect
                                        @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                            Basic
                                        @else
                                            Insufficient
                                        @endif
                                    </td>
                                    <td>{{ $summary['twitterCardFields'] ?? 0 }} fields</td>
                                </tr>
                                <tr>
                                    <td><strong>Canonical/Hreflang</strong></td>
                                    <td>
                                        {{ ($summary['hasCanonical'] ?? false) ? '✓' : '✗' }} / 
                                        {{ $summary['hreflangCount'] ?? 0 }} langs
                                    </td>
                                    <td>
                                        {{ ($summary['hasCanonical'] ?? false) ? 'Configured' : 'Not Set' }} /
                                        {{ $summary['hreflangCount'] ?? 0 }} languages
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Verification Complete -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ Test Results Verified</div>
                <div class="tight">
                    <p>This certificate is based on metadata completeness inspection results conducted through <strong>Meta Inspector CLI</strong>.</p>
                    <p>All data was collected in <u>real browser rendering environments</u> and evaluated according to SEO best practice standards.</p>
                    <p class="text-muted small">※ This inspection represents metadata status at a specific point in time and may change with website updates.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 This website has achieved <strong>{{ $grade }}</strong> grade from metadata completeness inspection,
                        proving it is a <u>search engine optimization (SEO) excellent site</u>.
                    </p>
                </div>
            @endif
            <!-- Metadata Preview -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">Metadata Preview</div>
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="mb-2">
                                <div class="fw-bold small">Title ({{ $summary['titleLength'] ?? 0 }} chars)</div>
                                <div class="text-muted small">{{ Str::limit($metadata['title'] ?: 'No title', 80) }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="fw-bold small">Description ({{ $summary['descriptionLength'] ?? 0 }} chars)</div>
                                <div class="text-muted small">{{ Str::limit($metadata['description'] ?: 'No description', 150) }}</div>
                            </div>
                            <div>
                                <div class="fw-bold small">Canonical URL</div>
                                <div class="text-muted small text-break">{{ Str::limit($metadata['canonical'] ?: 'Not set', 100) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Open Graph & Twitter Cards -->
            @if (!empty($metadata['openGraph']) || !empty($metadata['twitterCards']))
                <div class="row mb-3">
                    @if (!empty($metadata['openGraph']))
                        <div class="col-6">
                            <div class="small fw-bold mb-1">Open Graph Tags ({{ count($metadata['openGraph']) }} tags)</div>
                            <div class="small text-muted">
                                @foreach (array_slice($metadata['openGraph'], 0, 4) as $prop => $content)
                                    <div>• og:{{ $prop }}: {{ Str::limit($content, 30) }}</div>
                                @endforeach
                                @if (count($metadata['openGraph']) > 4)
                                    <div>... and {{ count($metadata['openGraph']) - 4 }} more</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (!empty($metadata['twitterCards']))
                        <div class="col-6">
                            <div class="small fw-bold mb-1">Twitter Cards ({{ count($metadata['twitterCards']) }} tags)</div>
                            <div class="small text-muted">
                                @foreach (array_slice($metadata['twitterCards'], 0, 4) as $name => $content)
                                    <div>• twitter:{{ $name }}: {{ Str::limit($content, 25) }}</div>
                                @endforeach
                                @if (count($metadata['twitterCards']) > 4)
                                    <div>... and {{ count($metadata['twitterCards']) - 4 }} more</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            @if (!empty($results['issues']))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ Identified Issues</p>
                    <ul class="mb-0 small">
                        @foreach (array_slice($results['issues'], 0, 3) as $issue)
                            <li>{{ Str::limit($issue, 80) }}</li>
                        @endforeach
                        @if (count($results['issues']) > 3)
                            <li>... and {{ count($results['issues']) - 3 }} more</li>
                        @endif
                    </ul>
                </div>
            @endif
            <!-- Additional Information -->
            <div class="alert alert-info d-block tight">
                <p><strong>Metadata Importance:</strong> Key element for SEO success, directly affecting search exposure and click-through rates</p>
                <p>Title 50-60 chars optimal, Description 120-160 chars optimal | Open Graph 4 essential elements | Canonical URL prevents duplicates</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Assessment Reason:</strong> {{ Str::limit($results['grade']['reason'] ?? 'Comprehensive evaluation results', 100) }}</p>
            </div>
            <!-- Issue/Expiry + Signature -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    Certificate Issued: {{ $certificate->issued_at->format('Y-m-d') }} | Certificate Expires: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
