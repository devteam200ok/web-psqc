<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="canonical" href="https://www.web-psqc.com/{{ request()->path() != '/' ? request()->path() : '' }}" />

    @include('inc.component.seo')
    @include('inc.component.theme_css')

    <!-- Fonts -->
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
            font-family: 'Inter', 'Noto Sans', system-ui, -apple-system, sans-serif;
            font-size: 12px;
            line-height: 1.34;
            background: transparent !important;
        }

        .print-container {
            width: 185mm;
            margin: 0 auto;
        }

        /* Title */
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

        .title-block h3 {
            font-size: 13px;
            margin: 0;
            color: #6c757d;
        }

        /* Card/Table */
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

        /* Score Card */
        .score-card .h1 {
            font-size: 20px;
            margin: 0;
        }

        .score-card .h4 {
            font-size: 13px;
            margin: 2px 0 0;
        }

        .score-card small {
            font-size: 10.5px;
        }

        /* Signature */
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
            display: inline-block;
            vertical-align: baseline;
            border: 0px
        }

        .sig-meta {
            font-size: 10.5px;
            color: #6b7280;
        }

        /* Category Header */
        .category-header {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 4px;
        }

        .category-performance {
            background: #fff3cd;
            color: #856404;
        }

        .category-security {
            background: #f8d7da;
            color: #721c24;
        }

        .category-quality {
            background: #d4edda;
            color: #155724;
        }

        .category-content {
            background: #e7e3fc;
            color: #6f42c1;
        }

        /* Test Item Table */
        .test-table {
            font-size: 10px;
            margin-bottom: 0;
        }

        .test-table td {
            padding: 3px 5px;
            vertical-align: middle;
        }

        .test-name {
            font-weight: 600;
            width: 30%;
        }

        .test-desc {
            color: #6c757d;
            width: 40%;
            font-size: 9px;
        }

        .test-grade {
            font-weight: 700;
            width: 15%;
            text-align: center;
        }

        .test-weighted {
            width: 15%;
            text-align: right;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-white">
    <div class="print-container">

        @php
            $metrics = $certification->metrics ?? [];
            $testTypes = \App\Models\WebTest::getTestTypes();

            $topPercent = match ($certification->overall_grade) {
                'A+' => '2%',
                'A' => '8%',
                'B' => '15%',
                'C' => '25%',
                'D' => '40%',
                default => '60%+',
            };

            $grade = $certification->overall_grade ?? 'F';
            $gradeClass = match ($grade) {
                'A+' => 'badge bg-green-lt text-green-lt-fg',
                'A' => 'badge bg-lime-lt text-lime-lt-fg',
                'B' => 'badge bg-blue-lt text-blue-lt-fg',
                'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                'D' => 'badge bg-orange-lt text-orange-lt-fg',
                'F' => 'badge bg-red-lt text-red-lt-fg',
                default => 'badge bg-secondary',
            };

            // Category score calculation
            $perf = 0;
            $sec = 0;
            $qual = 0;
            $cont = 0;
            $perf += ($metrics['performance']['p-speed']['score'] ?? 0) * 1.0;
            $perf += ($metrics['performance']['p-load']['score'] ?? 0) * 1.0;
            $perf += ($metrics['performance']['p-mobile']['score'] ?? 0) * 1.0;

            $sec += ($metrics['security']['s-ssl']['score'] ?? 0) * 0.8;
            $sec += ($metrics['security']['s-sslyze']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-header']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-scan']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-nuclei']['score'] ?? 0) * 0.4;

            $qual += ($metrics['quality']['q-lighthouse']['score'] ?? 0) * 1.2;
            $qual += ($metrics['quality']['q-accessibility']['score'] ?? 0) * 0.7;
            $qual += ($metrics['quality']['q-compatibility']['score'] ?? 0) * 0.3;
            $qual += ($metrics['quality']['q-visual']['score'] ?? 0) * 0.3;

            $cont += ($metrics['content']['c-links']['score'] ?? 0) * 0.5;
            $cont += ($metrics['content']['c-structure']['score'] ?? 0) * 0.4;
            $cont += ($metrics['content']['c-crawl']['score'] ?? 0) * 0.4;
            $cont += ($metrics['content']['c-meta']['score'] ?? 0) * 0.2;

            // Test descriptions
            $testDesc = [
                'p-speed' => '8 Global Regions Speed',
                'p-load' => 'K6 Load Testing',
                'p-mobile' => '6 Device Mobile Performance',
                's-ssl' => 'testssl.sh Comprehensive',
                's-sslyze' => 'SSLyze Deep Analysis',
                's-header' => '6 Security Headers',
                's-scan' => 'OWASP ZAP Scan',
                's-nuclei' => 'Latest CVE Vulnerabilities',
                'q-lighthouse' => 'Google Lighthouse',
                'q-accessibility' => 'WCAG 2.1 Accessibility',
                'q-compatibility' => '3 Browser Compatibility',
                'q-visual' => 'Responsive UI Compatibility',
                'c-links' => 'Link Integrity Verification',
                'c-structure' => 'Schema.org Structured Data',
                'c-crawl' => 'Search Engine Crawling',
                'c-meta' => 'Metadata Completeness',
            ];

            // Weights
            $weights = [
                'p-speed' => 1.0,
                'p-load' => 1.0,
                'p-mobile' => 1.0,
                's-ssl' => 0.8,
                's-sslyze' => 0.6,
                's-header' => 0.6,
                's-scan' => 0.6,
                's-nuclei' => 0.4,
                'q-lighthouse' => 1.2,
                'q-accessibility' => 0.7,
                'q-compatibility' => 0.3,
                'q-visual' => 0.3,
                'c-links' => 0.5,
                'c-structure' => 0.4,
                'c-crawl' => 0.4,
                'c-meta' => 0.2,
            ];

            // Grade color classes
            $getGradeClass = function ($grade) {
                return match ($grade) {
                    'A+' => 'badge bg-green-lt text-green-lt-fg',
                    'A' => 'badge bg-lime-lt text-lime-lt-fg',
                    'B' => 'badge bg-blue-lt text-blue-lt-fg',
                    'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                    'D' => 'badge bg-orange-lt text-orange-lt-fg',
                    'F' => 'badge bg-red-lt text-red-lt-fg',
                    default => 'badge bg-secondary',
                };
            };
        @endphp

        <!-- Title -->
        <div class="title-block">
            <div class="title-flex">
                <div class="title-text">
                    <h1>PSQC Comprehensive Certificate</h1>
                    <h2>Performance · Security · Quality · Content</h2>
                    <h3>Certificate Number: {{ $certification->code }}</h3>
                </div>
                <div class="title-qr">
                    {!! QrCode::size(80)->generate(url('/psqc/certified/' . $certification->code)) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Score Card -->
            <div class="col-5">
                <div class="card score-card">
                    <div class="card-body text-center py-3">
                        <div class="h1 mb-1"><span
                                class="{{ $gradeClass }}">{{ $certification->overall_grade }}</span></div>
                        <div class="h4 text-muted">{{ number_format($certification->overall_score, 1) }}/1000 points</div>
                        <div class="my-2">{{ $certification->url }}</div>
                        <small class="text-muted d-block">Assessment Date: {{ $certification->issued_at->format('Y-m-d') }}</small>
                        <small class="text-muted d-block">Valid Until:
                            {{ $certification->expires_at->format('Y-m-d') }}</small>
                    </div>
                </div>
            </div>

            <!-- Right Summary (Table without header) -->
            <div class="col-5 offset-2">
                <div class="table-responsive">
                    <table class="table table-sm mt-2">
                        <tbody>
                            <tr>
                                <td>Performance</td>
                                <td class="text-end">{{ number_format($perf, 0) }}/300</td>
                            </tr>
                            <tr>
                                <td>Security</td>
                                <td class="text-end">{{ number_format($sec, 0) }}/300</td>
                            </tr>
                            <tr>
                                <td>Quality</td>
                                <td class="text-end">{{ number_format($qual, 0) }}/250</td>
                            </tr>
                            <tr>
                                <td>Content</td>
                                <td class="text-end">{{ number_format($cont, 0) }}/150</td>
                            </tr>
                            <tr style="border-bottom: 0px #ffffff solid">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong>{{ number_format($certification->overall_score, 1) }}/1000</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Description (Top margin 10px) -->
        <div class="alert alert-info d-block text-start tight" style="margin-top:10px;">
            <p>This certificate is issued based on weighted evaluation (total 1000 points) across 4 categories and 16 detailed metrics for Performance, Security, Quality, and Content.</p>
            <p class="mb-0">This website has been evaluated as <strong>{{ $certification->overall_grade }}</strong> grade, confirming it belongs to the top
                <strong>{{ $topPercent }}</strong> worldwide in overall quality.</p>
        </div>

        <!-- 16 Test Details (Original scores removed) -->
        <div class="row">
            <div class="col-6">
                <!-- Performance -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-performance">Performance
                            ({{ number_format($perf, 0) }}/300)</div>
                        <table class="table table-sm test-table">
                            @foreach (['p-speed', 'p-load', 'p-mobile'] as $key)
                                @php
                                    $test = $metrics['performance'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <!-- Security -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-security">Security ({{ number_format($sec, 0) }}/300)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['s-ssl', 's-sslyze', 's-header', 's-scan', 's-nuclei'] as $key)
                                @php
                                    $test = $metrics['security'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <!-- Quality -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-quality">Quality ({{ number_format($qual, 0) }}/250)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['q-lighthouse', 'q-accessibility', 'q-compatibility', 'q-visual'] as $key)
                                @php
                                    $test = $metrics['quality'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <!-- Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-content">Content ({{ number_format($cont, 0) }}/150)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['c-links', 'c-structure', 'c-crawl', 'c-meta'] as $key)
                                @php
                                    $test = $metrics['content'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Web Standards Reference -->
        <div class="alert alert-info d-block text-start tight mt-3">
            <div class="fw-semibold mb-1">Global Web Standards Reference and Assessment Framework</div>
            <p class="mb-1">PSQC is an independent assessment certification developed based on international standards including ISO/IEC 25010, WCAG 2.1, Core Web Vitals, and OWASP Top 10.
            </p>
            <p class="mb-1">• <strong>Performance:</strong> Core Web Vitals standards applied (LCP<2.5s, INP<200ms, CLS<0.1)</p>
            <p class="mb-1">• <strong>Security:</strong> Vulnerability scanning based on OWASP Top 10 and CVE database</p>
            <p class="mb-1">• <strong>Quality:</strong> WCAG 2.1 AA level accessibility and Lighthouse quality metrics</p>
            <p class="mb-1">• <strong>Content:</strong> Schema.org structured data and SEO best practices evaluation</p>
            <p class="text-muted mb-0 mt-2">※ Web-PSQC does not guarantee absolute security or perfection, but provides objective data at the time of measurement.</p>
        </div>

        <!-- Signature -->
        <div class="text-center mt-4">
            <div class="signature-line">
                <span class="label">Authorized by</span>
                <span class="signature">Daniel Ahn</span>
                <div class="sig-meta">CEO, DevTeam Co., Ltd. (Web-PSQC)</div>
            </div>
        </div>

    </div>
</body>
</html>