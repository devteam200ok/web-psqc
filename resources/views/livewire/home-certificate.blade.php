@section('title')
    <title>📘 Certification & Scoring Guide – PSQC Standards, Grades, Methods | Web-PSQC</title>
    <meta name="description"
        content="Comprehensive guide to Web-PSQC’s PSQC (Performance · Security · Quality · Content) certification system, grade criteria (A+–F), and 16 detailed test methods and metrics. Covers global speed, load, mobile performance, SSL, security headers, vulnerabilities, Lighthouse, accessibility, links, structured data, crawling, and metadata standards.">
    <meta name="keywords"
        content="PSQC, web certificate, web quality grades, performance security quality content, global speed, k6 load testing, mobile performance, SSL testing, security headers, Nuclei, Lighthouse, accessibility, link validation, structured data, crawling, metadata">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Certification & Scoring Guide – PSQC Standards, Grades, Methods" />
    <meta property="og:description" content="Overview of the PSQC certification system with 16 detailed test methods and grade criteria across Performance, Security, Quality, and Content." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="Web-PSQC Certification & Scoring Guide" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Certification & Scoring Guide – PSQC Standards, Grades, Methods | Web-PSQC" />
    <meta name="twitter:description" content="All about PSQC certification, grades, and methods. 16 test criteria across Performance, Security, Quality, and Content." />
    <meta name="twitter:image" content="{{ App\Models\Setting::first()->og_image }}" />

    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'TechArticle',
    'headline' => 'Certification & Scoring Guide – PSQC Standards, Grades, Methods',
    'about' => [
        'PSQC Certification',
        'Web Performance',
        'Web Security',
        'Web Quality',
        'Web Content'
    ],
    'inLanguage' => 'en',
    'author' => [
        '@type' => 'Organization',
        'name' => 'DevTeam Co., Ltd.',
        'url'  => url('/'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'DevTeam Co., Ltd.',
        'url'  => url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => App\Models\Setting::first()->og_image ?? url('/images/og/default.png')
        ]
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => url()->current(),
        'name'  => 'Certification & Scoring Guide – Web-PSQC'
    ],
    'url' => url()->current(),
    'name' => 'Certification & Scoring Guide',
    'description' =>
        'An overview of the PSQC certification system and grade criteria (A+–F), with 16 detailed test methods and evaluation metrics.',
    'articleSection' => [
        'Web Test Certificate',
        'PSQC Master Certificate',
        'Performance Scoring',
        'Security Scoring',
        'Quality Scoring',
        'Content Scoring'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
@section('css')
    <style>
        .score-formula {
            background: #f8f9fa;
            border-left: 4px solid #845ef7;
            padding: 1rem;
            margin: 1rem 0;
        }

        .standard-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            margin: 0.25rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .test-title {
            min-width: 100px;
        }

        .test-method {
            min-width: 160px;
        }

        .test-method-content {
            font-size: 0.8rem !important;
        }

        .grade-a-plus {
            color: #28a745 !important;
            font-weight: bold;
        }

        .grade-a {
            color: #377dff !important;
            font-weight: bold;
        }

        .grade-b {
            color: #fd7e14 !important;
            font-weight: bold;
        }

        .table th {
            font-size: 0.9rem;
        }

        .table td {
            font-size: 0.85rem;
        }

        .criteria-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            margin: 4px 0;
            min-width: 250px;
        }

        .criteria-detail {
            font-size: 0.8rem;
            line-height: 1.4;
        }
    </style>
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="page-title">Certification & Scoring Guide</h2>
                <div class="text-muted">A guide to Web-PSQC’s PSQC certification framework and global web quality standards.</div>
            </div>
        </div>

        <!-- Web Test Certificate -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Web Test Certificate</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            An official document certifying the result of a single Web-PSQC test item.
                            Use it to quickly prove performance in a specific area or confirm partial improvements.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold">Features</h5>
                                <ul class="mb-0">
                                    <li>Issued immediately for a single test result</li>
                                    <li>PDF download + QR code verification</li>
                                    <li>Automatic email delivery</li>
                                    <li>Detailed record of the test environment</li>
                                    <li>Official DevTeam signature included</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold">Use Cases</h5>
                                <ul class="mb-0">
                                    <li>Client delivery proof</li>
                                    <li>Demonstrate technical capability in proposals/grants</li>
                                    <li>Internal quality management</li>
                                    <li>Show advantage over competitors</li>
                                    <li>Before/after website improvement comparison</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PSQC Master Certificate -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">PSQC Master Certificate</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            A comprehensive certificate issued after completing all 16 tests across Performance, Security, Quality, and Content,
                            based on a weighted aggregate score.
                        </p>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold">
                                    Grade Scale
                                    <small class="text-muted ms-2">(Based on 500 samples from Google page 1)</small>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>Score</th>
                                                <th>Distribution (est.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>A+</strong></td>
                                                <td>900 – 1000</td>
                                                <td>Top ~2%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>A</strong></td>
                                                <td>800 – 899</td>
                                                <td>Top ~8%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>B</strong></td>
                                                <td>700 – 799</td>
                                                <td>Top ~15%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>C</strong></td>
                                                <td>600 – 699</td>
                                                <td>Top ~25%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>D</strong></td>
                                                <td>500 – 599</td>
                                                <td>Top ~40%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>F</strong></td>
                                                <td>&lt; 500</td>
                                                <td>Remaining (100%)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold">Value of the PSQC Certificate</h5>
                                <ul class="mb-0">
                                    <li><strong>Objective data:</strong> Raw data and detailed environment records for each test</li>
                                    <li><strong>QR verification:</strong> Real‑time authenticity and source data lookup</li>
                                    <li><strong>Transparent criteria:</strong> 16 test methods and scoring process disclosed</li>
                                    <li><strong>Business credibility:</strong> Independent, objective evaluation of web quality</li>
                                    <li><strong>Marketing differentiation:</strong> Quantifiable advantage over competitors</li>
                                    <li><strong>Delivery support:</strong> Evidence for meeting client requirements</li>
                                </ul>

                                <div class="alert alert-info mt-3 mb-0" role="alert">
                                    <small class="text-muted">
                                        <strong>Note:</strong> Web-PSQC does not guarantee absolute security or perfection of any website.
                                        It provides measured data and analysis at the time of testing to offer objective grounds
                                        for web quality improvements and business communications.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scoring Criteria for 16 Individual Tests -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Scoring Criteria for 16 Individual Tests</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Performance 300 points -->
                            <div class="col-12">
                                <h4 class="fw-bold text-warning mb-3">Performance (300 points)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">Test</th>
                                                <th class="test-method">Method</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Global Speed -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/speed">Global Speed</a>
                                                </td>
                                                <td class="test-method-content">
                                                    8 regions, new/return visits<br>
                                                    TTFB &amp; Load<br>
                                                    Performance check
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>200ms</strong>, Load &le;
                                                            <strong>1.5s</strong><br>
                                                            • Global average: TTFB &le; <strong>800 ms</strong>, Load &le;
                                                            <strong>2.5s</strong><br>
                                                            • All regions: TTFB &le; <strong>1.5 s</strong>, Load &le;
                                                            <strong>3s</strong><br>
                                                            • Return visit improvement: <strong>80%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>400ms</strong>, Load &le;
                                                            <strong>2.5s</strong><br>
                                                            • Global average: TTFB &le; <strong>1.2 s</strong>, Load &le;
                                                            <strong>3.5s</strong><br>
                                                            • All regions: TTFB &le; <strong>2 s</strong>, Load &le;
                                                            <strong>4s</strong><br>
                                                            • Return visit improvement: <strong>60%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>800ms</strong>, Load &le;
                                                            <strong>3.5s</strong><br>
                                                            • Global average: TTFB &le; <strong>1.6 s</strong>, Load &le;
                                                            <strong>4.5s</strong><br>
                                                            • All regions: TTFB &le; <strong>2.5 s</strong>, Load &le;
                                                            <strong>5.5s</strong><br>
                                                            • Return visit improvement: <strong>50%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>1.2s</strong>, Load &le;
                                                            <strong>4.5s</strong><br>
                                                            • Global average: TTFB &le; <strong>2.0 s</strong>, Load &le;
                                                            <strong>5.5s</strong><br>
                                                            • All regions: TTFB &le; <strong>3.0 s</strong>, Load &le;
                                                            <strong>6.5s</strong><br>
                                                            • Return visit improvement: <strong>37.5%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>1.6s</strong>, Load &le;
                                                            <strong>6.0s</strong><br>
                                                            • Global average: TTFB &le; <strong>2.5 s</strong>, Load &le;
                                                            <strong>7.0s</strong><br>
                                                            • All regions: TTFB &le; <strong>3.5 s</strong>, Load &le;
                                                            <strong>8.5s</strong><br>
                                                            • Return visit improvement: <strong>25%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Below the above criteria
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Load Test (k6, Virginia region) -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/load">Load Test</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Virginia region<br>
                                                    k6 load test<br>
                                                    P95 response time<br>
                                                    Stability check
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>Basic conditions:</strong><br>
                                                            • <strong>100 VUs</strong> + <strong>60 s</strong><br>
                                                            • Think time: <strong>3–10 s</strong><br><br>
                                                            <strong>Performance criteria:</strong><br>
                                                            • P95 response time: &lt; <strong>1000 ms</strong><br>
                                                            • Error rate: &lt; <strong>0.1%</strong><br>
                                                            • Stability: P90 ≤ <strong>200% of average</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>Basic conditions:</strong><br>
                                                            • <strong>100 VUs</strong> + <strong>60 s</strong><br>
                                                            • Think time: <strong>3–10 s</strong><br><br>
                                                            <strong>Performance criteria:</strong><br>
                                                            • P95 response time: &lt; <strong>1200 ms</strong><br>
                                                            • Error rate: &lt; <strong>0.5%</strong><br>
                                                            • Stability: P90 ≤ <strong>240% of average</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>Basic conditions:</strong><br>
                                                            • <strong>50+ VUs</strong> + <strong>45+ s</strong><br>
                                                            • Think time: <strong>3–10 s</strong><br><br>
                                                            <strong>Performance criteria:</strong><br>
                                                            • P95 response time: &lt; <strong>1500 ms</strong><br>
                                                            • Error rate: &lt; <strong>1.0%</strong><br>
                                                            • Stability: P90 ≤ <strong>280% of average</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>Basic conditions:</strong><br>
                                                            • <strong>30+ VUs</strong> + <strong>30+ s</strong><br>
                                                            • Think time: <strong>3–10 s</strong><br><br>
                                                            <strong>Performance criteria:</strong><br>
                                                            • P95 response time: &lt; <strong>2000 ms</strong><br>
                                                            • Error rate: &lt; <strong>2.0%</strong><br>
                                                            • Stability: P90 ≤ <strong>320% of average</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>Basic conditions:</strong><br>
                                                            • <strong>10+ VUs</strong> + <strong>15+ s</strong><br>
                                                            • Think time: <strong>3–10 s</strong><br><br>
                                                            <strong>Performance criteria:</strong><br>
                                                            • P95 response time: &lt; <strong>3000 ms</strong><br>
                                                            • Error rate: &lt; <strong>5.0%</strong><br>
                                                            • Stability: P90 ≤ <strong>400% of average</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Below the above criteria
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Mobile Performance -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/mobile">Mobile Test</a>
                                                </td>
                                                <td class="test-method-content">
                                                    iPhone/Galaxy<br>
                                                    (Playwright)<br>
                                                    Median response time (repeat visit)<br>
                                                    JS errors · render width overflow
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median response time: <strong>≤ 800 ms</strong><br>
                                                            • JS runtime errors: <strong>0</strong><br>
                                                            • Render width overflow: <strong>None</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median response time: <strong>≤ 1200 ms</strong><br>
                                                            • JS runtime errors: <strong>≤ 1</strong><br>
                                                            • Render width overflow: <strong>None</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median response time: <strong>≤ 2000 ms</strong><br>
                                                            • JS runtime errors: <strong>≤ 2</strong><br>
                                                            • Render width overflow: <strong>Allowed</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median response time: <strong>≤ 3000 ms</strong><br>
                                                            • JS runtime errors: <strong>≤ 3</strong><br>
                                                            • Render width overflow: <strong>Frequent</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median response time: <strong>≤ 4000 ms</strong><br>
                                                            • JS runtime errors: <strong>≤ 5</strong><br>
                                                            • Render width overflow: <strong>Severe</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Below the above criteria
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Security 300 points -->
                            <div class="col-12">
                                <h4 class="fw-bold text-danger mb-3">Security (300 points)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">Test</th>
                                                <th class="test-method">Method</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- SSL Basics (testssl.sh) -->
                                            <tr>
                                                <td>
                                                    <a href="/security/ssl">SSL Basics</a>
                                                </td>
                                                <td class="test-method-content">
                                                    testssl.sh results<br>
                                                    Protocols · ciphers · certificate<br>
                                                    Vulnerabilities summary
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Only latest TLS</strong> used, <strong>no known vulnerabilities</strong><br>
                                                            • <strong>Strong cipher suites</strong> applied<br>
                                                            • Certificate and chain <strong>fully valid</strong><br>
                                                            • <strong>HSTS</strong> and other settings <strong>strong</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.2/1.3</strong> supported; legacy blocked<br>
                                                            • <strong>No major vulnerabilities</strong><br>
                                                            • Possible minor weak ciphers or misconfigurations<br>
                                                            • <strong>Generally safe</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Mostly secure protocols</strong><br>
                                                            • <strong>Some</strong> weak cipher suites present<br>
                                                            • Many testssl.sh <strong>WEAK</strong> warnings<br>
                                                            • <strong>Needs improvement</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Some legacy TLS</strong> enabled<br>
                                                            • <strong>High</strong> use of weak crypto
                                                            • Certificate <strong>near expiry</strong> / simple DV<br>
                                                            • <strong>Few vulnerabilities</strong> found
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>SSLv3/TLS 1.0</strong> permitted<br>
                                                            • <strong>Many</strong> weak ciphers enabled<br>
                                                            • Certificate chain <strong>errors/near expiry</strong><br>
                                                            • <strong>Multiple vulnerabilities</strong> present
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • SSL/TLS configuration <strong>fundamental flaws</strong><br>
                                                            • <strong>Vulnerable protocols</strong> broadly allowed<br>
                                                            • Certificate <strong>expired/self‑signed</strong><br>
                                                            • Many testssl.sh <strong>FAIL/VULNERABLE</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- SSL Deep Dive (sslyze) -->
                                            <tr>
                                                <td>
                                                    <a href="/security/sslyze">SSL Advanced</a>
                                                </td>
                                                <td class="test-method-content">
                                                    SSLyze deep analysis<br>
                                                    Protocols · ciphers · certificate<br>
                                                    OCSP · ALPN
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Only TLS 1.3/1.2</strong> allowed; no weak ciphers (<strong>all PFS</strong>)<br>
                                                            • Certificate <strong>ECDSA</strong> or <strong>RSA≥3072</strong>,
                                                            complete chain; expiry <strong>≥ 60 days</strong><br>
                                                            • <strong>OCSP Stapling</strong> enabled (Must‑Staple when supported)<br>
                                                            • ALPN <strong>h2</strong> negotiated; compression/insecure renegotiation <strong>disabled</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.3/1.2</strong>, strong ciphers first (<strong>mostly PFS</strong>)<br>
                                                            • Certificate <strong>RSA≥2048</strong>, <strong>SHA‑256+</strong>,
                                                            valid chain; expiry <strong>≥ 30 days</strong><br>
                                                            • <strong>OCSP Stapling</strong> active (occasional failure allowed)<br>
                                                            • <strong>h2</strong> supported or proper ALPN; risky features <strong>disabled</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.2</strong> required; 1.3 optional/unsupported; some <strong>CBC</strong><br>
                                                            • Certificate <strong>RSA≥2048</strong>, chain valid (expiry <strong>≥ 14 days</strong>)<br>
                                                            • OCSP Stapling <strong>disabled</strong> (OCSP responses acceptable)<br>
                                                            • h2 may be unsupported; risky features <strong>mostly disabled</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.0/1.1</strong> enabled or <strong>many weak ciphers</strong> (low PFS)<br>
                                                            • Chain missing/<strong>weak signature (SHA‑1)</strong> or expiry
                                                            imminent (<strong>≤ 14 days</strong>)<br>
                                                            • Stapling <strong>absent</strong>; revocation status <strong>unclear</strong><br>
                                                            • h2 <strong>unsupported</strong>; some risky features <strong>enabled</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Obsolete protocols/ciphers (<strong>SSLv3/EXPORT/RC4</strong>) allowed<br>
                                                            • Certificate <strong>mismatch/chain errors</strong> frequent<br>
                                                            • Stapling <strong>fails</strong>; revocation checks <strong>impossible</strong><br>
                                                            • <strong>Compression/insecure renegotiation</strong> enabled
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Defects at the level of <strong>handshake failures</strong><br>
                                                            • <strong>Expired/self‑signed/hostname mismatch</strong><br>
                                                            • Widespread <strong>weak protocols/ciphers</strong> allowed<br>
                                                            • Overall <strong>TLS configuration breakdown</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Security Headers -->
                                            <tr>
                                                <td>
                                                    <a href="/security/headers">Headers</a>
                                                </td>
                                                <td class="test-method-content">Header completeness</td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Strong CSP</strong> (nonce/hash/strict‑dynamic; no unsafe-*)<br>
                                                            • XFO: <strong>DENY/SAMEORIGIN</strong> or limited frame‑ancestors<br>
                                                            • X-Content-Type: <strong>nosniff</strong><br>
                                                            • Referrer-Policy:
                                                            <strong>strict-origin-when-cross-origin</strong> or better<br>
                                                            • Permissions‑Policy: <strong>unneeded features blocked</strong><br>
                                                            • HSTS: <strong>≥ 6 months + include subdomains</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A (possible without CSP if 5 non‑CSP items are strong) -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>CSP present</strong> (weaker allowed) <em>or</em> <strong>non‑CSP 5 items strong</strong><br>
                                                            • <strong>XFO applied</strong> (or frame‑ancestors limited)<br>
                                                            • X-Content-Type: <strong>nosniff</strong><br>
                                                            • Referrer‑Policy: <strong>recommended value</strong><br>
                                                            • Permissions‑Policy: <strong>basic restrictions</strong><br>
                                                            • HSTS: <strong>≥ 6 months</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • CSP <strong>none/weak</strong><br>
                                                            • XFO <strong>applied</strong><br>
                                                            • X-Content-Type: <strong>nosniff present</strong><br>
                                                            • Referrer‑Policy: <strong>okay/average</strong><br>
                                                            • Permissions‑Policy: <strong>partially restricted</strong><br>
                                                            • HSTS: <strong>short</strong> or <strong>no subdomains</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Some</strong> headers present<br>
                                                            • CSP <strong>none/weak</strong><br>
                                                            • Referrer‑Policy <strong>weak</strong><br>
                                                            • X-Content-Type <strong>missing</strong><br>
                                                            • HSTS <strong>absent</strong> or <strong>very short</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Only <strong>1–2 key headers</strong> present<br>
                                                            • <strong>No</strong> CSP<br>
                                                            • Referrer <strong>weak/absent</strong><br>
                                                            • <strong>Many</strong> other headers missing
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Security headers <strong>virtually absent</strong><br>
                                                            • <strong>No</strong> CSP/XFO/X-Content<br>
                                                            • <strong>No</strong> Referrer‑Policy<br>
                                                            • <strong>No</strong> HSTS
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <a href="/security/scan">Vulnerability Scan</a>

                                                </td>

                                                <td class="test-method-content">
                                                    Passive response analysis<br>
                                                    HTTP header/body checks<br>
                                                    (excluding CSP warnings)<br>
                                                    <div class="small text-muted mt-1">
                                                        OWASP ZAP Passive Scan<br>
                                                        Main page only (1 URL)<br>
                                                        No child crawling
                                                    </div>
                                                </td>

                                                {{-- A+ --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High/Medium <strong>0</strong><br>
                                                            • Security headers <strong>complete</strong> (HTTPS, HSTS, X‑Frame‑Options, etc.)<br>
                                                            • <strong>No</strong> sensitive data exposure (cookies, comments, debug)<br>
                                                            • Server/framework version info <strong>minimized</strong><br>
                                                            • CSP checks performed in a separate item
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- A --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>0</strong>, Medium <strong>≤1</strong><br>
                                                            • Security headers <strong>mostly present</strong>, minor gaps<br>
                                                            • <strong>No</strong> sensitive data exposure<br>
                                                            • <strong>Minor info exposure</strong> (e.g., server type)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- B --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤1</strong>, Medium <strong>≤2</strong><br>
                                                            • Some headers <strong>missing</strong> (HSTS, X‑XSS‑Protection, etc.)<br>
                                                            • Session cookies missing <strong>Secure/HttpOnly</strong><br>
                                                            • <strong>Minor internal identifiers</strong> in comments/meta
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- C --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≥ 2</strong> or Medium <strong>≥ 3</strong><br>
                                                            • Key security headers <strong>absent</strong><br>
                                                            • Sensitive parameters/tokens <strong>exposed in response</strong><br>
                                                            • <strong>Weak</strong> session management (cookie attributes inadequate)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- D --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Multiple Highs</strong><br>
                                                            • Authentication/session attributes <strong>severely missing</strong><br>
                                                            • Debug/dev info exposed (<strong>stack traces, internal IPs</strong>)<br>
                                                            • <strong>Exposed admin consoles/config files</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- F --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Widespread High vulnerabilities</strong><br>
                                                            • <strong>No HTTPS</strong> or entirely bypassed<br>
                                                            • Sensitive data <strong>in plaintext/exposed</strong><br>
                                                            • <strong>Lack of</strong> security headers and session controls overall
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <a href="/security/nuclei">CVE Check</a>
                                                </td>

                                                <td class="test-method-content">
                                                    Freshness‑based<br>
                                                    Nuclei templates<br>
                                                    <div class="small text-muted mt-1">
                                                        2024–2025<br>
                                                        (non‑intrusive, single URL)
                                                    </div>
                                                </td>

                                                {{-- A+ --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Critical/High <strong>0</strong>, Medium <strong>0</strong><br>
                                                            • <strong>2024–2025 CVEs</strong> not detected<br>
                                                            • No exposed directories/debug/sensitive files<br>
                                                            • Security headers/banners <strong>minimal</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- A --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤1</strong>, Medium <strong>≤1</strong><br>
                                                            • No direct exposure to recent CVEs (bypass/conditions required)<br>
                                                            • <strong>Minor configuration warnings</strong> (informational)<br>
                                                            • Patching/configuration <strong>good</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- B --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤ 2</strong> or Medium <strong>≤ 3</strong><br>
                                                            • Some <strong>config/banner exposures</strong><br>
                                                            • Admin endpoints protected (<strong>hard to bypass</strong>)<br>
                                                            • Patch delays for <strong>recent security releases</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- C --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≥ 3</strong> or <strong>many</strong> Medium<br>
                                                            • Exposed <strong>sensitive files/backups/indexing</strong><br>
                                                            • <strong>Outdated components</strong> inferred (banners/meta)<br>
                                                            • Patching/configuration <strong>needs systematic improvement</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- D --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Critical <strong>≥ 1</strong> or easily exploitable <strong>High</strong><br>
                                                            • Recent (<strong>2024–2025</strong>) CVEs <strong>directly impactful</strong><br>
                                                            • <strong>Risky endpoints/files</strong> accessible without auth<br>
                                                            • Sensitive info exposed (<strong>build/logs/env</strong>)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- F --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Multiple Critical/High</strong> present simultaneously<br>
                                                            • Latest CVEs <strong>widely unpatched/exposed</strong><br>
                                                            • <strong>Lacking</strong> basic security configs (defensive headers/access control)<br>
                                                            • <strong>Absent</strong> security guardrails overall
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Quality 250 points -->
                            <div class="col-12">
                                <h4 class="fw-bold text-success mb-3">Quality (250 points)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">Test</th>
                                                <th class="test-method">Method</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="/quality/lighthouse">Lighthouse</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Integrated analysis of Performance + SEO + Accessibility<br>
                                                    (Lighthouse)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>90 points+</strong><br>
                                                            • Accessibility: <strong>90 points+</strong><br>
                                                            • Best Practices: <strong>90 points+</strong><br>
                                                            • SEO: <strong>90 points+</strong><br>
                                                            • Overall average: <strong>95 points+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>85 points+</strong><br>
                                                            • Accessibility: <strong>85 points+</strong><br>
                                                            • Best Practices: <strong>85 points+</strong><br>
                                                            • SEO: <strong>85 points+</strong><br>
                                                            • Overall average: <strong>90 points+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>75 points+</strong><br>
                                                            • Accessibility: <strong>75 points+</strong><br>
                                                            • Best Practices: <strong>75 points+</strong><br>
                                                            • SEO: <strong>75 points+</strong><br>
                                                            • Overall average: <strong>80 points+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>65 points+</strong><br>
                                                            • Accessibility: <strong>65 points+</strong><br>
                                                            • Best Practices: <strong>65 points+</strong><br>
                                                            • SEO: <strong>65 points+</strong><br>
                                                            • Overall average: <strong>70 points+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>55 points+</strong><br>
                                                            • Accessibility: <strong>55 points+</strong><br>
                                                            • Best Practices: <strong>55 points+</strong><br>
                                                            • SEO: <strong>55 points+</strong><br>
                                                            • Overall average: <strong>60 points+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>≤ 54 points</strong><br>
                                                            • Accessibility: <strong>≤ 54 points</strong><br>
                                                            • Best Practices: <strong>≤ 54 points</strong><br>
                                                            • SEO: <strong>≤ 54 points</strong><br>
                                                            • Overall average: <strong>≤ 59 points</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/accessibility">Accessibility</a>
                                                </td>
                                                <td class="test-method-content">
                                                    WCAG 2.1 rule‑based<br>
                                                    Automated accessibility checks<br>
                                                    Evaluated via counts of errors/warnings<br>
                                                    (axe‑core)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical=<strong>0</strong>,
                                                            serious=<strong>0</strong><br>
                                                            • Total violations <strong>≤ 3</strong><br>
                                                            • Keyboard/ARIA/alt text/contrast <strong>all good</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical=<strong>0</strong>, serious <strong>≤
                                                                3</strong><br>
                                                            • Total violations <strong>≤ 8</strong><br>
                                                            • Key landmarks/labels <strong>mostly good</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 1</strong>, serious <strong>≤
                                                                6</strong><br>
                                                            • Total violations <strong>≤ 15</strong><br>
                                                            • Some contrast/labels <strong>need improvement</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 3</strong>, serious <strong>≤
                                                                10</strong><br>
                                                            • Total violations <strong>≤ 25</strong><br>
                                                            • Focus/ARIA structure <strong>needs remediation</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 6</strong> or serious <strong>≤
                                                                18</strong><br>
                                                            • Total violations <strong>≤ 40</strong><br>
                                                            • Many <strong>keyboard traps/label omissions</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Exceeds the above (<strong>many critical/serious</strong>)<br>
                                                            • <strong>Difficult</strong> to use with screen readers/keyboard
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/compatibility">Compatibility</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Chrome / Firefox / Safari<br>
                                                    Based on JS/CSS errors<br>
                                                    (Playwright)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Chrome / Firefox / Safari <strong>all pass</strong><br>
                                                            • JS errors: <strong>0</strong><br>
                                                            • CSS rendering errors: <strong>0</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Major browser support <strong>good</strong><br>
                                                            • JS errors <strong>≤ 1</strong><br>
                                                            • CSS errors <strong>≤ 1</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Minor differences</strong> among browsers<br>
                                                            • JS errors <strong>≤ 3</strong><br>
                                                            • CSS errors <strong>≤ 3</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Degraded functionality</strong> in some browsers<br>
                                                            • JS errors <strong>≤ 6</strong><br>
                                                            • CSS errors <strong>≤ 6</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Many</strong> compatibility issues<br>
                                                            • JS errors <strong>≤ 10</strong><br>
                                                            • CSS errors <strong>≤ 10</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>Cannot operate</strong> on major browsers<br>
                                                            • JS errors <strong>&gt; 10</strong><br>
                                                            • CSS errors <strong>&gt; 10</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/visual">Responsive UI</a>
                                                </td>

                                                <!-- Method: single page / brief description -->
                                                <td class="test-method-content">
                                                    By key viewport<br>
                                                    Overflow pixels (px) measurement<br>
                                                    (mobile · foldable · tablet · desktop)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>0 overflows</strong> across all viewports<br>
                                                            • Body render width always within <strong>viewport</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overflows ≤ <strong>1</strong> and each <strong>≤ 8 px</strong><br>
                                                            • On narrow mobile (≤390 px): <strong>0 overflows</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overflows ≤ <strong>2</strong> and each <strong>≤ 16 px</strong><br>
                                                            or on narrow mobile: <strong>≤ 8 px (1)</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overflows ≤ <strong>4</strong> or a single overflow is
                                                            <strong>17–32px</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overflows &gt; <strong>4</strong> or a single overflow is
                                                            <strong>33–64px</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Measurement failed or overflow <strong>≥ 65 px</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Content 150 points -->
                            <div class="col-12">
                                <h4 class="fw-bold mb-3" style="color: #6f42c1;">Content (150 points)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">Test</th>
                                                <th class="test-method">Method</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="/content/links">Links</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Internal/external/image links<br>
                                                    Anchor link status checks<br>
                                                    Grade by error rate<br>
                                                    (Broken Link Checker)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Internal/external/image link <strong>error rate: 0%</strong><br>
                                                            • Redirect chains <strong>≤ 1 hop</strong><br>
                                                            • Anchor links <strong>100% valid</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overall <strong>error rate ≤ 1%</strong><br>
                                                            • Redirect chains ≤ 2 hops<br>
                                                            • Anchor links <strong>mostly valid</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overall <strong>error rate ≤ 3%</strong><br>
                                                            • Redirect chains ≤ 3 hops<br>
                                                            • Some invalid anchor links
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overall <strong>error rate ≤ 5%</strong><br>
                                                            • Many link <strong>warnings</strong> (timeouts/SSL issues)<br>
                                                            • Frequent anchor link errors
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overall <strong>error rate ≤ 10%</strong><br>
                                                            • <strong>Redirect loops</strong> or long chains<br>
                                                            • Many <strong>broken image links</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Overall <strong>error rate ≥ 10%</strong><br>
                                                            • Many <strong>broken internal links</strong><br>
                                                            • Anchor/image links <strong>largely broken</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/structure">Structured Data</a>
                                                </td>
                                                <td class="test-method-content">
                                                    JSON‑LD/Schema.org based<br>
                                                    Structured data errors/warnings
                                                    (Google Rich Results Test)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Schema.org schemas <strong>fully implemented</strong><br>
                                                            • <strong>JSON‑LD</strong> format used<br>
                                                            • Rich snippets <strong>100% recognized</strong><br>
                                                            • <strong>0 errors, no warnings</strong><br>
                                                            • Appropriate schema types applied
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Key schemas <strong>valid</strong><br>
                                                            • Implemented via JSON‑LD<br>
                                                            • Rich snippets <strong>mostly recognized</strong><br>
                                                            • <strong>No errors</strong>, ≤ 2 warnings
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Some <strong>core schemas missing</strong><br>
                                                            • Rich snippets recognized <strong>partially</strong><br>
                                                            • ≤ 1 error, <strong>≤ 5 warnings</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Structured data <strong>incomplete</strong><br>
                                                            • Rich snippets <strong>unstable</strong><br>
                                                            • ≤ 3 errors, <strong>many warnings</strong><br>
                                                            • Some types inappropriate
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Structured data <strong>inconsistent/duplicated</strong><br>
                                                            • Rich snippets <strong>not recognized</strong><br>
                                                            • <strong>≥ 4 errors</strong><br>
                                                            • Many warnings and <strong>wrong types</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Structured data <strong>not implemented</strong><br>
                                                            • <strong>No JSON‑LD/Microdata</strong><br>
                                                            • <strong>Pervasive errors</strong><br>
                                                            • Search engine rich snippets <strong>not possible</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/crawl">Crawl</a>
                                                </td>
                                                <td class="test-method-content">
                                                    robots/sitemap validation<br>
                                                    + full crawl via sitemap<br>
                                                    (internal quality/duplication analysis)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt <strong>correctly applied</strong><br>
                                                            • sitemap.xml present; <strong>no missing/404</strong><br>
                                                            • All target pages return <strong>2xx</strong><br>
                                                            • Site‑wide quality average <strong>≥ 85 points</strong><br>
                                                            • Duplicate content <strong>≤ 30%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt <strong>correctly applied</strong><br>
                                                            • sitemap.xml present; <strong>consistent</strong><br>
                                                            • All target pages return <strong>2xx</strong><br>
                                                            • Site‑wide quality average <strong>≥ 85 points</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt and sitemap.xml <strong>present</strong><br>
                                                            • All target pages return <strong>2xx</strong><br>
                                                            • Site‑wide quality average not required
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt and sitemap.xml present<br>
                                                            • Some targets include <strong>4xx/5xx</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt and sitemap.xml present<br>
                                                            • Can generate target URLs (robots allowed + sitemap collected)<br>
                                                            • However, <strong>low successful access rate</strong> or quality checks not feasible
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>No robots.txt</strong> or <strong>no sitemap.xml</strong><br>
                                                            • <strong>Cannot generate</strong> crawl target list
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/meta">Metadata</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Completeness‑based<br>
                                                    (Meta Inspector CLI)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title: <strong>optimal length (50–60 chars)</strong><br>
                                                            • Description: <strong>optimal length (120–160 chars)</strong><br>
                                                            • Open Graph <strong>fully implemented</strong><br>
                                                            • Canonical <strong>accurate</strong> + Twitter Cards <strong>complete</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>within acceptable range</strong><br>
                                                            • Open Graph <strong>fully implemented</strong><br>
                                                            • Canonical <strong>correctly set</strong><br>
                                                            • Twitter Cards optional
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>basic and valid</strong><br>
                                                            • Open Graph <strong>basic tags</strong><br>
                                                            • Canonical set<br>
                                                            • Some metadata omissions allowed
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>improper length</strong><br>
                                                            • Open Graph incomplete (<strong>key tags missing</strong>)<br>
                                                            • Canonical <strong>inaccurate or missing</strong><br>
                                                            • Overall metadata quality degraded
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>severely improper length</strong><br>
                                                            • Open Graph <strong>insufficient basic tags</strong><br>
                                                            • Canonical <strong>incorrectly set</strong><br>
                                                            • <strong>Insufficient basic metadata</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>not provided</strong><br>
                                                            • Open Graph <strong>absent</strong><br>
                                                            • Metadata <strong>largely not implemented</strong><br>
                                                            • <strong>No basic SEO configurations</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- /card-body -->
                </div>
            </div>
        </div>

        <!-- PSQC Score Formula Details -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">PSQC Overall Score and Grade Criteria</h3>
                    </div>
                    <div class="card-body">

                        <!-- Scoring System -->
                        <div class="score-formula">
                            <h5 class="fw-bold mb-2">How PSQC Scores Are Calculated</h5>
                            <div class="alert alert-info d-block mb-3">
                                <h6>Step 1: Individual test scores (each out of 100)</h6>
                                <p class="mb-1">Every individual test is scored on a 100‑point scale.</p>
                                <small>(e.g., SSL Basics → 85, Mobile Test → 92, Links → 78)</small>
                            </div>

                            <div class="alert alert-info d-block mb-3">
                                <h6>Step 2: Apply weights by area</h6>
                                <div>Performance = (Global Speed×1.0 + Load Test×1.0 + Mobile Test×1.0) = 300 points</div>
                                <div>Security = (SSL Basics×0.8 + SSL Advanced×0.6 + Headers×0.6 + Vulnerability Scan×0.6 + CVE Check×0.4) = 300 points</div>
                                <div>Quality = (Lighthouse×1.2 + Accessibility×0.7 + Compatibility×0.3 + Responsive UI×0.3) = 250 points</div>
                                <div>Content = (Links×0.5 + Structured Data×0.4 + Crawl×0.4 + Metadata×0.2) = 150 points</div>
                            </div>

                            <div class="alert alert-info d-block">
                                <h6>Step 3: Final composite score</h6>
                                <div>Total = Performance (300) + Security (300) + Quality (250) + Content (150) = 1000 points
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success d-block mt-4" role="alert">
                            <h5 class="fw-bold mb-2">🏆 Final Grade Bands</h5>
                            <div class="mt-0">
                                <span class="badge bg-green-lt text-green-lt-fg me-1">A+ (900–1000 points)</span>
                                <span class="badge bg-lime-lt text-lime-lt-fg me-1">A (800–899 points)</span>
                                <span class="badge bg-yellow-lt text-yellow-lt-fg me-1">B (700–799 points)</span>
                                <span class="badge bg-orange-lt text-orange-lt-fg me-1">C (600–699 points)</span>
                                <span class="badge bg-pink-lt text-pink-lt-fg me-1">D (500–599 points)</span>
                                <span class="badge bg-red-lt text-red-lt-fg">F (below 500 points)</span>
                            </div>
                            <div class="mt-2">
                                <h6 class="fw-bold mb-1">⚡ A+ Requirements</h6>
                                <small>
                                    - ≥ 90% in each area<br>
                                    - Total score ≥ 900 points<br>
                                    - 0 critical security vulnerabilities<br>
                                    ※ If total meets the threshold but a requirement is missed, grade adjusts to A
                                </small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Global Web Standards & References -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Global Web Standards & References</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Web-PSQC is an independent website quality assessment service, built with reference to widely recognized web standards.
                        </p>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">ISO/IEC 25010</h5>
                                <p class="small text-muted mb-2">International standard for software quality models</p>
                                <div class="standard-badge bg-blue-lt text-blue-lt-fg">Functional suitability</div>
                                <div class="standard-badge bg-azure-lt text-azure-lt-fg">Performance efficiency</div>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">Security</div>
                                <div class="standard-badge bg-green-lt text-green-lt-fg">Compatibility</div>
                                <p class="small mt-2">
                                    <strong>Web-PSQC mapping:</strong> We reference ISO 25010 quality characteristics to organize the Performance, Security, Quality, and Content areas. (Using Web-PSQC’s own evaluation approach)
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">WCAG 2.1</h5>
                                <p class="small text-muted mb-2">W3C Web Content Accessibility Guidelines</p>
                                <div class="standard-badge bg-teal-lt text-teal-lt-fg">Perceivable</div>
                                <div class="standard-badge bg-cyan-lt text-cyan-lt-fg">Operable</div>
                                <div class="standard-badge bg-indigo-lt text-indigo-lt-fg">Understandable</div>
                                <div class="standard-badge bg-purple-lt text-purple-lt-fg">Robust</div>
                                <p class="small mt-2">
                                    <strong>Web-PSQC mapping:</strong> We reference WCAG 2.1 AA to build the accessibility deep‑dive test and use the axe‑core engine for automated checks.
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Core Web Vitals</h5>
                                <p class="small text-muted mb-2">Google’s page experience metrics</p>
                                <div class="standard-badge bg-lime-lt text-lime-lt-fg">LCP &lt; 2.5 s</div>
                                <div class="standard-badge bg-yellow-lt text-yellow-lt-fg">INP &lt; 200 ms</div>
                                <div class="standard-badge bg-orange-lt text-orange-lt-fg">CLS &lt; 0.1</div>
                                <p class="small mt-2">
                                    <strong>Web-PSQC mapping:</strong> We reference Core Web Vitals for performance assessments and measure real‑world experience via global region tests.
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">OWASP Security</h5>
                                <p class="small text-muted mb-2">Web application security practices</p>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">OWASP Top 10</div>
                                <div class="standard-badge bg-pink-lt text-pink-lt-fg">ZAP dynamic scan</div>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">CVE database</div>
                                <p class="small mt-2">
                                    <strong>Web-PSQC mapping:</strong> We reference OWASP Top 10 and CVE databases to set vulnerability scan criteria, using OWASP ZAP and the Nuclei engine.
                                </p>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <p class="mb-0">
                                Web-PSQC adapts methods and criteria from international standards to modern web environments.
                                We <strong>fully disclose detailed methodologies and measured raw data</strong> for each test to ensure transparent, trustworthy results.
                                Clients can use the provided data to define concrete, actionable website improvements.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics & Benchmarks -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Global Website Quality Benchmarks</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-nowrap">
                                <thead>
                                    <tr>
                                        <th>Metric</th>
                                        <th>Excellence threshold</th>
                                        <th>Global attainment</th>
                                        <th>Source</th>
                                        <th>Related Web-PSQC test</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Lighthouse all categories 90+</strong></td>
                                        <td>Performance, Accessibility,<br>Best Practices, SEO all 90+</td>
                                        <td class="text-danger fw-bold">&lt; 2%</td>
                                        <td>HTTP Archive (Lighthouse distribution)</td>
                                        <td>Quality/lighthouse</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Core Web Vitals pass</strong></td>
                                        <td>LCP &lt; 2.5 s, INP/TBT &lt; 200 ms, CLS &lt; 0.1</td>
                                        <td class="text-warning fw-bold">≈ 43-44%</td>
                                        <td>Chrome UX Report (CrUX)</td>
                                        <td>Performance/speed + Quality/lighthouse</td>
                                    </tr>
                                    <tr>
                                        <td><strong>SSL Labs A+ grade</strong></td>
                                        <td>TLS 1.3, HSTS, hardened configuration</td>
                                        <td class="text-warning fw-bold">≈ 46%</td>
                                        <td>SSL Labs</td>
                                        <td>Security/ssl + Security/sslyze</td>
                                    </tr>
                                    <tr>
                                        <td><strong>WCAG 2.1 AA compliance<br>(automated checks)</strong></td>
                                        <td>0 detected errors</td>
                                        <td class="text-danger fw-bold">≈ 5%<br>(94.8% detection rate)</td>
                                        <td>WebAIM Million</td>
                                        <td>Quality/accessibility</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No OWASP Top 10 vulns</strong></td>
                                        <td>0 major vulnerabilities</td>
                                        <td class="text-warning fw-bold">≈ 30-40%</td>
                                        <td>OWASP Top 10</td>
                                        <td>Security/scan + Security/nuclei</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Schema.org structured data<br>fully implemented</strong></td>
                                        <td>Implemented on all pages</td>
                                        <td class="text-warning fw-bold">≈ 25–35%</td>
                                        <td>W3C structured data stats</td>
                                        <td>Content/structure</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Full browser compatibility</strong></td>
                                        <td>Chrome, Firefox, Safari all OK</td>
                                        <td class="text-success fw-bold">≈ 60–70%</td>
                                        <td>MDN compatibility data</td>
                                        <td>Quality/compatibility</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info d-block mt-4" role="alert">
                            <h5 class="fw-bold mb-2">Statistical Intersection</h5>
                            <p class="mb-2">Sites excelling in all seven areas are nearly 0%. A practical view focuses on the four core indicators: Quality, Performance, Security, and Accessibility.</p>
                            <ul class="mb-0">
                                <li><strong>Theoretical intersection (4 core indicators):</strong> 2% × 43% × 46% × 5% ≈ 0.2%</li>
                                <li><strong>Observed correlation:</strong> High‑quality sites often score well across multiple areas</li>
                                <li><strong>Practical estimate:</strong> <span class="fw-bold text-primary">A+ overall ≈ top ~2% worldwide</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
