@section('title')
    @include('inc.component.seo')
@endsection
@section('css')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+KR:wght@400;500;700&family=Allura&display=swap"
        rel="stylesheet">
    @include('components.test-shared.css')

    <style>

        /* 타이틀 */
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

        /* 카드/테이블 */
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

        /* 점수 카드 */
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

        /* 서명 */
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

        /* 카테고리 헤더 */
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

        /* 테스트 항목 테이블 */
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
            <div class="mx-auto">
                <select wire:model.change="test_type" class="form-select">
                    <option value="psqc">PSQC Comprehensive Certificate</option>
                    
                    <!-- Performance Group -->
                    <optgroup label="Performance">
                        <option value="p-speed">Global Speed – Test speed across 8 global regions</option>
                        <option value="p-load">Load Test – K6 load testing</option>
                        <option value="p-mobile">Mobile Performance – 6 types of mobile performance tests</option>
                    </optgroup>
                    
                    <!-- Security Group -->
                    <optgroup label="Security">
                        <option value="s-ssl">SSL Basic – testssl.sh comprehensive check</option>
                        <option value="s-sslyze">SSL Advanced – SSLyze deep analysis</option>
                        <option value="s-header">Security Headers – 6 essential headers</option>
                        <option value="s-scan">Vulnerability Scan – OWASP ZAP scan</option>
                        <option value="s-nuclei">Latest Vulnerabilities – Latest CVE checks</option>
                    </optgroup>
                    
                    <!-- Quality Group -->
                    <optgroup label="Quality">
                        <option value="q-lighthouse">Overall Quality – Google Lighthouse</option>
                        <option value="q-accessibility">Accessibility Advanced – WCAG 2.1 compliance</option>
                        <option value="q-compatibility">Browser Compatibility – 3 major browsers</option>
                        <option value="q-visual">Responsive UI – Responsive design validation</option>
                    </optgroup>
                    
                    <!-- Content Group -->
                    <optgroup label="Content">
                        <option value="c-links">Link Validation – Integrity of links</option>
                        <option value="c-structure">Structured Data – Schema.org validation</option>
                        <option value="c-crawl">Site Crawling – Search engine crawlability</option>
                        <option value="c-meta">Metadata – Metadata completeness</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="text-center mb-4">

            @if($test_type == 'psqc')
                <div class="card">
                    <div class="card-body px-4 py-3">
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

                            // Sum by category
                            $perf = 0;
                            $sec  = 0;
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
                                'p-speed'         => 'Speed across 8 global regions',
                                'p-load'          => 'K6 load testing',
                                'p-mobile'        => 'Six mobile performance checks',
                                's-ssl'           => 'testssl.sh comprehensive audit',
                                's-sslyze'        => 'SSLyze deep analysis',
                                's-header'        => 'Six essential security headers',
                                's-scan'          => 'OWASP ZAP scan',
                                's-nuclei'        => 'Latest CVE vulnerability checks',
                                'q-lighthouse'    => 'Google Lighthouse',
                                'q-accessibility' => 'WCAG 2.1 accessibility',
                                'q-compatibility' => 'Compatibility across 3 major browsers',
                                'q-visual'        => 'Responsive UI compliance',
                                'c-links'         => 'Link integrity verification',
                                'c-structure'     => 'Schema.org structured data',
                                'c-crawl'         => 'Search engine crawlability',
                                'c-meta'          => 'Metadata completeness',
                            ];

                            // Weights
                            $weights = [
                                'p-speed'         => 1.0,
                                'p-load'          => 1.0,
                                'p-mobile'        => 1.0,
                                's-ssl'           => 0.8,
                                's-sslyze'        => 0.6,
                                's-header'        => 0.6,
                                's-scan'          => 0.6,
                                's-nuclei'        => 0.4,
                                'q-lighthouse'    => 1.2,
                                'q-accessibility' => 0.7,
                                'q-compatibility' => 0.3,
                                'q-visual'        => 0.3,
                                'c-links'         => 0.5,
                                'c-structure'     => 0.4,
                                'c-crawl'         => 0.4,
                                'c-meta'          => 0.2,
                            ];

                            // Grade color classes
                            $getGradeClass = function ($grade) {
                                return match ($grade) {
                                    'A+' => 'badge bg-green-lt text-green-lt-fg',
                                    'A'  => 'badge bg-lime-lt text-lime-lt-fg',
                                    'B'  => 'badge bg-blue-lt text-blue-lt-fg',
                                    'C'  => 'badge bg-yellow-lt text-yellow-lt-fg',
                                    'D'  => 'badge bg-orange-lt text-orange-lt-fg',
                                    'F'  => 'badge bg-red-lt text-red-lt-fg',
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
                                    <h3>Certificate ID: {{ $certification->code }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left score card -->
                            <div class="col-xl-5 mb-2">
                                <div class="card score-card">
                                    <div class="card-body text-center py-3">
                                        <div class="h1 mb-1">
                                            <span class="{{ $gradeClass }}">{{ $certification->overall_grade }}</span>
                                        </div>
                                        <div class="h4 text-muted">{{ number_format($certification->overall_score, 1) }}/1000 pts</div>
                                        <div class="my-2">{{ $certification->url }}</div>
                                        <small class="text-muted d-block">Evaluated on: {{ $certification->issued_at->format('Y-m-d') }}</small>
                                        <small class="text-muted d-block">Valid until: {{ $certification->expires_at->format('Y-m-d') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right overall summary (table without header) -->
                            <div class="col-xl-5 offset-xl-2 mb-2">
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

                        <!-- Description -->
                        <div class="alert alert-info d-block text-start tight" style="margin-top:10px;">
                            <p>This certificate is issued based on a weighted evaluation (total 1000 points) across four categories—Performance, Security, Quality, and Content—covering 16 detailed checks.</p>
                            <p class="mb-0">This website is rated <strong>{{ $certification->overall_grade }}</strong>, placing it in the
                                <strong>top {{ $topPercent }}</strong> of overall quality worldwide.</p>
                        </div>

                        <!-- 16 detailed tests (raw scores hidden) -->
                        <div class="row">
                            <div class="col-xl-6 mb-2">
                                <!-- Performance -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-header category-performance">Performance ({{ number_format($perf, 0) }}/300)</div>
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
                                                    <td class="test-grade">
                                                        <span class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
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
                                        <div class="category-header category-security">Security ({{ number_format($sec, 0) }}/300)</div>
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
                                                    <td class="test-grade">
                                                        <span class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                                    </td>
                                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 mb-2">
                                <!-- Quality -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-header category-quality">Quality ({{ number_format($qual, 0) }}/250)</div>
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
                                                    <td class="test-grade">
                                                        <span class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
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
                                        <div class="category-header category-content">Content ({{ number_format($cont, 0) }}/150)</div>
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
                                                    <td class="test-grade">
                                                        <span class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                                    </td>
                                                    <td class="test-weighted">{{ number_format($weighted, 0) }} pts</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Global web standards reference -->
                        <div class="alert alert-info d-block text-start tight mt-3">
                            <div class="fw-semibold mb-1">Global Web Standards & Evaluation Framework</div>
                            <p class="mb-1">PSQC is an independent evaluation and certification developed with reference to international standards such as ISO/IEC 25010, WCAG 2.1, Core Web Vitals, and the OWASP Top 10.</p>
                            <p class="mb-1">• <strong>Performance:</strong> Applies Core Web Vitals thresholds (LCP &lt; 2.5s, INP &lt; 200ms, CLS &lt; 0.1)</p>
                            <p class="mb-1">• <strong>Security:</strong> Vulnerability scanning based on OWASP Top 10 and CVE databases</p>
                            <p class="mb-1">• <strong>Quality:</strong> WCAG 2.1 AA accessibility and Lighthouse quality metrics</p>
                            <p class="mb-1">• <strong>Content:</strong> Schema.org structured data and SEO best-practice compliance</p>
                            <p class="text-muted mb-0 mt-2">※ Web-PSQC does not guarantee absolute security or perfection, and reflects objective data at the time of measurement.</p>
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
                </div>
            @endif

            @if ($test_type == 'p-speed')
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
                                    data-bs-toggle="tab">Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Metrics</a>
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

                                        // Existing calculation logic remains unchanged...
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

                                        // Origin = region with the fastest TTFB
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

                                        // Repeat-visit improvement
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

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                PSQC Comprehensive Certificate — Detailed Test Report
                                            </h1>
                                            <h2>(Global Speed Test)</h2>
                                            <h3>Certificate ID: {{ $certification->code }}</h3>
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
                                                            Tested at:
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
                                                            <td><strong>All Regions (Max)</strong></td>
                                                            <td>{{ $fmt($worstTTFB) }}</td>
                                                            <td>{{ $fmt($worstLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Repeat-visit Improvement</strong></td>
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
                                            This certificate is based on web performance tests conducted via a <strong>global network of 8 regions</strong>.<br>
                                            All data was collected by <u>simulating real user conditions</u>, and authenticity can be verified by anyone through our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ These results reflect objective measurements at a specific point in time and may vary with ongoing optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 Based on measurements across major regions worldwide,
                                                this site achieved a <strong>{{ $grade }}</strong> rating,
                                                placing it in the <u>top 10% of web performance</u> globally.<br>
                                                This demonstrates <strong>fast responsiveness</strong> and <strong>strong global user experience</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Speed by country/region -->
                                    @if ($currentTest->metrics)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Speed by Country/Region</h4>
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

                                    <!-- Additional info -->
                                    <div class="alert alert-info d-block">
                                        <strong>Display format:</strong> <span class="fw-bold">First visit</span> → <span
                                            class="fw-bold">Repeat visit</span> (Δ difference)<br>
                                        <span class="text-success">Green = improved (faster on repeat)</span> | <span
                                            class="text-danger">Red = regressed (slower on repeat)</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>TTFB (Time To First Byte):</strong> Time from the user’s request until the first byte of the response is received from the server.</p>
                                        <p class="mb-2"><strong>Load Time:</strong> Time until all resources (HTML, CSS, JS, images, etc.) are loaded and the page is fully rendered.</p>
                                        <p class="mb-0"><strong>Repeat-visit performance:</strong> Caching, persistent connections, and CDN edge caching often make repeat visits faster.</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Results verified by Web-PSQC Verification Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards.
                                            Certificate authenticity can be checked in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued on:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expires on:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Seoul, Tokyo, Sydney, Singapore, Frankfurt, Virginia, Oregon, London (8 regions)</h3>
                                <div class="text-muted small mt-1">
                                    We simulate real global user speeds via 8 regions distributed across Asia, North America, Europe, and Oceania.
                                    <br><br>
                                    • Asia (Seoul, Tokyo, Singapore) → East & Southeast Asia coverage<br>
                                    • Oceania (Sydney) → Australia & Pacific region<br>
                                    • North America (Virginia, Oregon) → East & West coasts<br>
                                    • Europe (Frankfurt, London) → Major hubs in Western & Central Europe
                                    <br><br>
                                    These 8 regions are core PoPs commonly operated by providers such as Cloudflare, AWS, and GCP, and broadly represent global internet traffic.
                                </div>
                                {{-- Grade criteria --}}
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
                                                <td>Origin: TTFB ≤ 200ms, Load ≤ 1.5s<br>Global Avg: TTFB ≤ 800ms, Load ≤ 2.5s<br>All Regions: TTFB ≤ 1.5s, Load ≤ 3s<br>Repeat-visit Improvement: 80%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80–89</td>
                                                <td>Origin: TTFB ≤ 400ms, Load ≤ 2.5s<br>Global Avg: TTFB ≤ 1.2s, Load ≤ 3.5s<br>All Regions: TTFB ≤ 2s, Load ≤ 4s<br>Repeat-visit Improvement: 60%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–79</td>
                                                <td>Origin: TTFB ≤ 800ms, Load ≤ 3.5s<br>Global Avg: TTFB ≤ 1.6s, Load ≤ 4.5s<br>All Regions: TTFB ≤ 2.5s, Load ≤ 5.5s<br>Repeat-visit Improvement: 50%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60–69</td>
                                                <td>Origin: TTFB ≤ 1.2s, Load ≤ 4.5s<br>Global Avg: TTFB ≤ 2.0s, Load ≤ 5.5s<br>All Regions: TTFB ≤ 3.0s, Load ≤ 6.5s<br>Repeat-visit Improvement: 37.5%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–59</td>
                                                <td>Origin: TTFB ≤ 1.6s, Load ≤ 6.0s<br>Global Avg: TTFB ≤ 2.5s, Load ≤ 7.0s<br>All Regions: TTFB ≤ 3.5s, Load ≤ 8.5s<br>Repeat-visit Improvement: 25%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td>Below the above thresholds</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Difference between TTFB and Load Time</strong><br>
                                    - <strong>TTFB (Time To First Byte)</strong>: Time from the user’s request until the first byte is received from the server.<br>
                                    - <strong>Load Time</strong>: Time until all resources (HTML, CSS, JS, images, etc.) are loaded and the page is fully rendered.<br><br>

                                    <strong>🌍 Network round trips (RTT)</strong><br>
                                    • 1× TCP handshake + 1× TLS handshake + 1× request/response → at least 3 round trips are required.<br>
                                    • Therefore, <u>the further a region is from the origin</u>, the more latency accumulates.<br><br>

                                    <strong>📊 Minimum latency by region</strong><br>
                                    - Same continent (e.g., Seoul→Tokyo/Singapore): TTFB in tens of ms up to ~200ms.<br>
                                    - Intercontinental (Seoul→US/Europe): Fiber RTT alone is often 150–250ms+.<br>
                                    - Including TLS and data requests, <u>TTFB of 400–600ms+</u> can occur.<br>
                                    - Load Time grows with resource size and count; heavy images/JS can take <u>5s or more</u>.<br><br>

                                    In short, for <span class="fw-bold">regions physically far from the origin (e.g., KR origin → US East/Europe)</span>,
                                    <u>hundreds of ms TTFB</u> and <u>2–5s+ Load Time</u> are common even after optimization.
                                    Use a CDN, caching, and edge deployment to reduce this.
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON data">
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
                                    data-bs-toggle="tab">Certification Summary</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                    class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Criteria & Environment</a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                    class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                    data-bs-toggle="tab">Detailed Metrics</a>
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

                                        $totalRequests   = $metrics['http_reqs'] ?? 0;
                                        $failureRate     = ($metrics['http_req_failed'] ?? 0) * 100;
                                        $p95Response     = $metrics['http_req_duration_p95'] ?? 0;
                                        $avgResponse     = $metrics['http_req_duration_avg'] ?? 0;
                                        $requestsPerSec  = $metrics['http_reqs_rate'] ?? 0;
                                        $vus             = $config['vus'] ?? 'N/A';
                                        $duration        = $config['duration_seconds'] ?? 'N/A';

                                        $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                    @endphp

                                    <div class="mt-4 mb-5">
                                        <div class="text-center">
                                            <h1>
                                                PSQC Comprehensive Certificate — Detailed Test Report
                                            </h1>
                                            <h2>(K6 Load Test)</h2>
                                            <h3>Certificate ID: {{ $certification->code }}</h3>
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
                                                            Tested at:
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
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Virtual Users × Duration</strong></td>
                                                            <td>{{ $vus }} VUs × {{ $duration }}s</td>
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
                                                            <td class="{{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
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
                                            This certificate is based on web performance tests conducted with the <strong>K6 load testing tool</strong>.<br>
                                            Measurements simulate real usage with <strong>{{ $vus }} concurrent virtual users</strong> over <strong>{{ $duration }} seconds</strong>.
                                            Anyone can verify authenticity via our QR verification system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ Results reflect objective measurements at a specific point in time and may vary depending on server environment and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site earned a <strong>{{ $grade }}</strong> rating in the load test,
                                                demonstrating <u>excellent concurrent handling capacity</u>.<br>
                                                This indicates a <strong>stable service</strong> and <strong>high server performance</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed performance metrics -->
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
                                                                    <td>{{ number_format($metrics['http_req_duration_avg'] ?? 0, 2) }}ms</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Median</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_med'] ?? 0, 2) }}ms</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>P90</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_p90'] ?? 0, 2) }}ms</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>P95</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_p95'] ?? 0, 2) }}ms</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Max</td>
                                                                    <td>{{ number_format($metrics['http_req_duration_max'] ?? 0, 2) }}ms</td>
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
                                                                    <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }} MB</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Data Sent</td>
                                                                    <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }} MB</td>
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
                                        <strong>Display format:</strong> Think Time {{ $config['think_time_min'] ?? 3 }}–{{ $config['think_time_max'] ?? 10 }}s applied<br>
                                        <span class="text-success">Error rate &lt; 1% = Excellent</span> | <span class="text-danger">Error rate ≥ 5% = Needs improvement</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Virtual Users (VUs):</strong> Number of concurrent virtual users simulating real traffic load.</p>
                                        <p class="mb-2"><strong>P95 Response Time:</strong> 95% of all requests completed within this time (key UX indicator).</p>
                                        <p class="mb-0"><strong>Think Time:</strong> Idle time between requests to emulate real user navigation patterns.</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ Results verified by Web-PSQC K6 Load Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards.
                                            Certificate authenticity can be verified in real time via QR code.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued on: {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expires on: {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>K6 Load Test Environment</h3>
                                <div class="text-muted small mt-1">
                                    K6 is a modern load-testing tool by Grafana. Test scenarios are written in JavaScript to validate the performance and stability of websites and APIs.
                                    <br><br>
                                    • <strong>Virtual Users (VUs)</strong>: Number of concurrent virtual users<br>
                                    • <strong>Duration</strong>: How long the test runs<br>
                                    • <strong>Think Time</strong>: Wait time between requests (simulates real user behavior)<br>
                                    • <strong>P95 Response Time</strong>: Time within which 95% of requests complete
                                    <br><br>
                                    Averages can be skewed by very fast outliers, so P95 more accurately reflects real user experience.
                                </div>
                                {{-- Grade criteria --}}
                                <div class="table-responsive my-3">
                                    <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Grade</th>
                                                <th>VU/Duration Conditions</th>
                                                <th>Performance Criteria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>100+ VUs & 60s+</td>
                                                <td>P95 &lt; 1000ms<br>Error rate &lt; 0.1%<br>Stability: P90 ≤ 200% of average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>50+ VUs & 45s+</td>
                                                <td>P95 &lt; 1200ms<br>Error rate &lt; 0.5%<br>Stability: P90 ≤ 240% of average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>30+ VUs & 30s+</td>
                                                <td>P95 &lt; 1500ms<br>Error rate &lt; 1.0%<br>Stability: P90 ≤ 280% of average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>20+ VUs & 30s+</td>
                                                <td>P95 &lt; 2000ms<br>Error rate &lt; 2.0%<br>Stability: P90 ≤ 320% of average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>10+ VUs & 30s+</td>
                                                <td>P95 &lt; 3000ms<br>Error rate &lt; 5.0%<br>Stability: P90 ≤ 400% of average</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>-</td>
                                                <td>Below the above thresholds</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Highlights of K6 Load Testing</strong><br>
                                    - <strong>Realistic user behavior</strong>: Reproduces natural usage via Think Time<br>
                                    - <strong>Concurrent traffic simulation</strong>: VUs emulate real-world load<br>
                                    - <strong>Comprehensive metrics</strong>: Response times, error rate, throughput, and more<br><br>

                                    <strong>🌍 Test Execution Environment</strong><br>
                                    • Test region: {{ ucfirst($config['region'] ?? 'seoul') }}<br>
                                    • Virtual Users: {{ $vus }} VUs<br>
                                    • Duration: {{ $duration }}s<br>
                                    • Think Time: {{ $config['think_time_min'] ?? 3 }}–{{ $config['think_time_max'] ?? 10 }}s<br><br>

                                    <strong>📊 Interpreting Performance</strong><br>
                                    - P95 &lt; 1s: Excellent UX<br>
                                    - P95 &lt; 2s: Good UX<br>
                                    - P95 &gt; 3s: Needs improvement<br>
                                    - Error rate &lt; 1%: Stable service<br>
                                    - Error rate &gt; 5%: Immediate remediation needed
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Raw JSON Data</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="copyJsonToClipboard()" title="Copy JSON data">
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Mobile Performance Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Measured Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Median Response Time Avg.</strong></td>
                                                            <td>{{ $overall['medianAvgMs'] ?? 0 }}ms</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Long Tasks Avg.</strong></td>
                                                            <td>{{ $overall['longTasksAvgMs'] ?? 0 }}ms</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>JS Runtime Errors (1st/3rd Party)</strong></td>
                                                            <td>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }} /
                                                                {{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Overflow Detected</strong></td>
                                                            <td>{{ !empty($overall['bodyOverflowsViewport']) ? 'Yes' : 'No' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Mobile Performance Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on the results of a mobile web performance test performed using the <strong>Playwright headless browser</strong>.<br>
                                            Measurements were conducted on <strong>6 representative mobile devices</strong> (3 iOS, 3 Android) with CPU ×4 throttling to simulate real mobile conditions.  
                                            The authenticity of the results can be verified through the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may vary depending on website optimization and device compatibility.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website has achieved a <strong>{{ $grade }}</strong> grade in the Mobile Performance Test, demonstrating an <u>excellent level of mobile optimization</u>.<br>
                                                This indicates <strong>fast mobile rendering</strong> and a <strong>stable runtime environment</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Device-specific results -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Results by Device</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter table-nowrap">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Device</th>
                                                            <th>Median(ms)</th>
                                                            <th>TBT(ms)</th>
                                                            <th>JS Errors (1st)</th>
                                                            <th>JS Errors (3rd)</th>
                                                            <th>Overflow</th>
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
                                        <strong>Test Environment:</strong> 4 runs per device (1 warm-up excluded, median of 3 runs used)<br>
                                        <span class="text-success">No JS Errors = Excellent</span> | <span class="text-danger">Overflow = Needs Responsive Improvement</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Median Response Time:</strong> Median page load time on revisit (with cache)</p>
                                        <p class="mb-2"><strong>TBT (Total Blocking Time):</strong> Total time main thread is blocked due to JavaScript execution (over 50ms)</p>
                                        <p class="mb-0"><strong>Overflow:</strong> Whether the body element exceeds viewport width causing horizontal scroll</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Mobile Performance Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurement services based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                    Using Playwright to simulate real mobile device environments,  
                                    the mobile performance and stability of websites are precisely measured.
                                    <br><br>
                                    • <strong>Test Devices</strong>: 3 iOS (iPhone SE, 11, 15 Pro), 3 Android (Galaxy S9+, S20 Ultra, Pixel 5)<br>
                                    • <strong>Measurement Method</strong>: 4 runs per device, 1 warm-up excluded, 3 medians used<br>
                                    • <strong>CPU Throttling</strong>: ×4 applied to simulate real-world performance constraints<br>
                                    • <strong>Key Metrics</strong>: Revisit load time, Long Tasks (TBT), JS runtime errors, Overflow detection
                                </div>
                                {{-- Grade Criteria --}}
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
                                                <td>Median Response Time: ≤ 800ms<br>JS Runtime Errors: 0<br>Overflow: None</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>Median Response Time: ≤ 1200ms<br>JS Runtime Errors: ≤ 1<br>Overflow: None</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Median Response Time: ≤ 2000ms<br>JS Runtime Errors: ≤ 2<br>Overflow: Allowed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Median Response Time: ≤ 3000ms<br>JS Runtime Errors: ≤ 3<br>Overflow: Frequent</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Median Response Time: ≤ 4000ms<br>JS Runtime Errors: ≤ 5<br>Overflow: Severe</td>
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
                                    <strong>📌 Features of Playwright Testing</strong><br>
                                    - <strong>Developed by Microsoft</strong>: Modern automation tool for accurate performance measurement<br>
                                    - <strong>Headless Execution</strong>: Runs in background without UI for stable operation<br>
                                    - <strong>CPU Throttling</strong>: Precisely simulates real mobile performance limitations<br><br>

                                    <strong>🌍 Interpretation of Metrics</strong><br>
                                    • <strong>Older device faster</strong>: Lighter assets may be served due to smaller viewport<br>
                                    • <strong>Uniform CPU Throttle</strong>: ×4 applied across devices, making resource weight directly impact speed<br>
                                    • <strong>JS Error Separation</strong>: 1st party (test domain) vs 3rd party (external) errors separated<br><br>

                                    <strong>📊 Why This Test Matters</strong><br>
                                    - Precisely measures perceived mobile rendering performance<br>
                                    - Identifies runtime stability and error accountability<br>
                                    - Automatically verifies responsive design compliance<br>
                                    - Optimizes regression comparison and release targets
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(SSL/TLS Security Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                                {{ $vulnerableCount > 0 ? $vulnerableCount . ' found' : 'None' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS Security Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on the SSL/TLS security test results performed with <strong>testssl.sh</strong>.<br>
                                            The server’s SSL/TLS configuration, supported protocols, cipher suites, and known vulnerabilities were comprehensively examined.  
                                            The authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may change depending on server configuration and security updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website obtained a <strong>{{ $grade }}</strong> grade in the SSL/TLS Security Test,  
                                                demonstrating a <u>top-tier security configuration</u>.<br>
                                                This indicates <strong>secure encrypted communication</strong> and <strong>compliance with modern security standards</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed security information -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Security Information</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Certificate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Issuer</td>
                                                                    <td>{{ $results['certificate']['issuer'] ?? 'N/A' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Validity</td>
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

                                    <!-- Vulnerability summary -->
                                    @if ($vulnerableCount > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Detected Vulnerabilities</h4>
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
                                                    <strong>{{ $vulnerableCount }} vulnerabilities detected:</strong>
                                                    {{ implode(', ', $vulnList) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Top-tier security configuration (meets all modern standards)
                                        @elseif ($grade === 'A')
                                            Excellent security configuration (meets most standards)
                                        @elseif ($grade === 'B')
                                            Good security configuration (some improvements recommended)
                                        @else
                                            Security configuration improvements required
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>testssl.sh:</strong> An industry-standard open-source SSL/TLS tester with 10,000+ GitHub stars</p>
                                        <p class="mb-2"><strong>Perfect Forward Secrecy (PFS):</strong> A security property that prevents past sessions from being decrypted in the future</p>
                                        <p class="mb-0"><strong>HSTS:</strong> HTTP Strict Transport Security that enforces HTTPS connections</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC SSL/TLS Security Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                    testssl.sh is an open-source tool that comprehensively examines SSL/TLS configurations  
                                    and precisely analyzes a website’s HTTPS security settings.
                                    <br><br>
                                    • <strong>Tool</strong>: testssl.sh (Open-source project with 10,000+ GitHub stars)<br>
                                    • <strong>Coverage</strong>: SSL/TLS protocols, cipher suites, certificates, known vulnerabilities<br>
                                    • <strong>Vulnerability Checks</strong>: Heartbleed, POODLE, BEAST, CRIME, FREAK, etc.<br>
                                    • <strong>Security Features</strong>: PFS, HSTS, OCSP Stapling support status
                                </div>
                                {{-- Grade Criteria --}}
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
                                                <td>Only modern TLS enabled; no vulnerabilities<br>Strong cipher suites applied<br>Certificate & chain fully valid<br>HSTS and other security settings in good state</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.2/1.3 supported; legacy blocked<br>No major vulnerabilities<br>Some weak ciphers or minor gaps possible<br>Overall secure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Primarily safe protocols<br>Some weak cipher suites present<br>Multiple WEAK warnings<br>Improvements recommended</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Some legacy TLS enabled<br>High usage of weak crypto<br>Certificate close to expiry / simple DV<br>Few vulnerabilities found</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>SSLv3/TLS 1.0 allowed<br>Many weak ciphers enabled<br>Chain errors / near-expiry<br>Multiple vulnerabilities present</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>Fundamental SSL/TLS misconfiguration<br>Vulnerable protocols widely enabled<br>Expired/self-signed certificates<br>Many FAIL/VULNERABLE findings</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Key Inspection Items</strong><br>
                                    - <strong>SSL/TLS Protocols</strong>: SSL 2.0/3.0, TLS 1.0–1.3 support<br>
                                    - <strong>Cipher Suites</strong>: Supported algorithms, PFS, weak cipher detection<br>
                                    - <strong>Certificates</strong>: Validity, expiry, chain integrity, OCSP Stapling<br>
                                    - <strong>Security Vulnerabilities</strong>: Heartbleed, POODLE, BEAST, CRIME, FREAK, etc.<br><br>

                                    <strong>🌍 Why SSL/TLS Testing Matters</strong><br>
                                    • <strong>Data Protection</strong>: Ensures encryption quality for all data in transit<br>
                                    • <strong>Trust</strong>: Provides safe HTTPS with no browser warnings<br>
                                    • <strong>Compliance</strong>: Meets requirements like GDPR, PCI-DSS<br>
                                    • <strong>SEO</strong>: Search engines prefer HTTPS sites<br><br>

                                    <strong>📊 Security Recommendations</strong><br>
                                    - Fully disable legacy protocols (SSL 2.0/3.0, TLS 1.0/1.1)<br>
                                    - Use strong cipher suites (AES-GCM, ChaCha20-Poly1305)<br>
                                    - Enable HSTS, OCSP Stapling, and related security headers<br>
                                    - Perform regular security updates and certificate management
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
                                    style="max-height: 600px; overflow-y: auto; font-size: 11px; line-height: 1.2;">{{ $currentTest->results['raw_output'] ?? 'No data' }}</pre>
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(SSL/TLS Deep Analysis)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                                    TLS 1.3 supported
                                                                @elseif ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                                                    TLS 1.2 (1.3 not supported)
                                                                @else
                                                                    Only legacy versions supported
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>PFS Ratio</strong></td>
                                                            <td>{{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>OCSP Stapling</strong></td>
                                                            <td class="{{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'text-success' : 'text-danger' }}">
                                                                {{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'Enabled' : 'Disabled' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>HSTS</strong></td>
                                                            <td class="{{ !empty($analysis['http_headers']['hsts']) ? 'text-success' : 'text-danger' }}">
                                                                {{ !empty($analysis['http_headers']['hsts']) ? 'Configured' : 'Not Configured' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ SSL/TLS Deep Analysis Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on results from a <strong>SSLyze v5.x</strong> deep security analysis.<br>
                                            TLS protocol versions, cipher strength, certificate chain, OCSP stapling, and HTTP security headers were comprehensively examined.  
                                            The authenticity of these results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may change with server configuration updates and security patches.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in the SSL/TLS deep analysis,  
                                                demonstrating <u>top-tier cryptographic security</u>.<br>
                                                This indicates support for <strong>modern TLS protocols</strong> and <strong>strong cipher configurations</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Detailed analysis -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Detailed Analysis Results</h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="2">Cipher Suites</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['cipher_suites']['tls_1_2']))
                                                                    <tr>
                                                                        <td>TLS 1.2 Suites</td>
                                                                        <td>{{ $analysis['cipher_suites']['tls_1_2']['total'] ?? 0 }} total</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Strong Ciphers</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['strong'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Weak Ciphers</td>
                                                                        <td class="{{ ($analysis['cipher_suites']['tls_1_2']['weak'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['weak'] ?? 0 }} suites
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if (!empty($analysis['cipher_suites']['tls_1_3']))
                                                                    <tr>
                                                                        <td>TLS 1.3 Suites</td>
                                                                        <td class="text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_3']['total'] ?? 0 }} total
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
                                                                    <th colspan="2">Certificate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (!empty($analysis['certificate']['details']))
                                                                    <tr>
                                                                        <td>Key Algorithm</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_algorithm'] ?? 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Key Size</td>
                                                                        <td>{{ $analysis['certificate']['details']['key_size'] ?? 'N/A' }} bits</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Signature Algorithm</td>
                                                                        <td>{{ $analysis['certificate']['details']['signature_algorithm'] ?? 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Days to Expiry</td>
                                                                        <td class="{{ ($analysis['certificate']['details']['days_to_expiry'] ?? 31) <= 30 ? 'text-warning' : '' }}">
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

                                    <!-- Detected issues -->
                                    @if (!empty($issues))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Detected Security Issues</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($issues) }} issues detected:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach (array_slice($issues, 0, 5) as $issue)
                                                            <li>{{ $issue }}</li>
                                                        @endforeach
                                                        @if (count($issues) > 5)
                                                            <li>and {{ count($issues) - 5 }} more…</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Top-tier SSL/TLS security (TLS 1.3, strong suites, robust security headers)
                                        @elseif ($grade === 'A')
                                            Excellent SSL/TLS security (TLS 1.2+, mostly strong suites)
                                        @elseif ($grade === 'B')
                                            Good SSL/TLS security (some improvements recommended)
                                        @else
                                            SSL/TLS security improvements required
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>SSLyze:</strong> An open-source SSL/TLS scanner recommended by Mozilla, Qualys, and IETF</p>
                                        <p class="mb-2"><strong>PFS:</strong> Perfect Forward Secrecy — prevents future decryption of past sessions</p>
                                        <p class="mb-0"><strong>OCSP Stapling:</strong> Mechanism to efficiently verify certificate revocation status</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC SSLyze Deep Analysis.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                    SSLyze v5.x is an open-source SSL/TLS scanner recommended by Mozilla, Qualys, and the IETF,  
                                    providing comprehensive diagnostics of a website’s SSL/TLS configuration.
                                    <br><br>
                                    • <strong>Tool</strong>: SSLyze v5.x — industry-standard SSL/TLS analysis tool<br>
                                    • <strong>TLS Protocols</strong>: Support checks for SSL 2.0/3.0, TLS 1.0/1.1/1.2/1.3<br>
                                    • <strong>Cipher Suites</strong>: Strength, PFS support, weak cipher detection<br>
                                    • <strong>Certificate Chain</strong>: Validity, expiry, signature algorithm, key size<br>
                                    • <strong>Security Features</strong>: OCSP Stapling, HSTS, elliptic-curve support
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
                                                <td>90~100</td>
                                                <td>Only TLS 1.3/1.2 enabled; no weak suites (all PFS)<br>
                                                    Certificate ECDSA or RSA ≥ 3072; chain intact; ≥ 60 days to expiry<br>
                                                    OCSP Stapling successful (Must-Staple if applicable)<br>
                                                    HSTS enabled; max-age ≥ 1 year; includeSubDomains; preload</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>TLS 1.3/1.2, strong suites preferred (mostly PFS)<br>
                                                    Certificate RSA ≥ 2048, SHA-256+; chain intact; ≥ 30 days to expiry<br>
                                                    OCSP Stapling enabled (occasional failures allowed)<br>
                                                    HSTS enabled; max-age ≥ 6 months</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>65~79</td>
                                                <td>TLS 1.2 required; 1.3 optional/not supported; some CBC present<br>
                                                    Certificate RSA ≥ 2048; chain valid (≥ 14 days to expiry)<br>
                                                    OCSP Stapling disabled (fallback OCSP acceptable)<br>
                                                    HSTS present but partially insufficient</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>50~64</td>
                                                <td>TLS 1.0/1.1 enabled or many weak suites (low PFS)<br>
                                                    Chain missing/weak signature (SHA-1) or imminent expiry (≤ 14 days)<br>
                                                    No stapling; revocation status unclear<br>
                                                    HSTS not configured</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>35~49</td>
                                                <td>Obsolete protocols/ciphers allowed (SSLv3/EXPORT/RC4, etc.)<br>
                                                    Frequent certificate mismatch/chain errors<br>
                                                    Stapling failures; revocation checks unreliable<br>
                                                    Security headers broadly insufficient</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~34</td>
                                                <td>Handshake-level failures<br>
                                                    Expired/self-signed/host mismatch<br>
                                                    Broadly enabled weak protocols/ciphers<br>
                                                    Severely misconfigured TLS</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 SSLyze Deep Analysis Highlights</strong><br>
                                    - <strong>Comprehensive</strong>: Full coverage of TLS protocols, ciphers, certificates, and security headers<br>
                                    - <strong>Granular</strong>: Per-cipher strength and PFS evaluation<br>
                                    - <strong>Real-time Verification</strong>: OCSP stapling and certificate chain checks<br>
                                    - <strong>Elliptic-Curve Review</strong>: Supported curves and strength assessment<br><br>

                                    <strong>🌍 Why SSLyze Deep Analysis Matters</strong><br>
                                    • <strong>Fine-grained Security Diagnosis</strong>: Identifies concrete weaknesses beyond simple grades<br>
                                    • <strong>Standards Compliance</strong>: Confirms modern requirements like TLS 1.3<br>
                                    • <strong>Performance Optimization</strong>: Improves handshakes by removing weak suites<br>
                                    • <strong>Regulatory Alignment</strong>: Validates compliance for PCI-DSS, HIPAA, etc.<br><br>

                                    <strong>📊 Recommendations</strong><br>
                                    - Enable TLS 1.3 and fully disable TLS 1.0/1.1<br>
                                    - Use only PFS-capable ECDHE/DHE suites<br>
                                    - Use RSA ≥ 3072-bit or ECDSA 256-bit certificates<br>
                                    - Require OCSP Stapling and set HSTS headers
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

                                        // Header status analysis
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Security Headers Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                            <td><strong>Headers Applied</strong></td>
                                                            <td>{{ $presentHeaders }}/6</td>
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
                                        <h4 class="mb-2">✅ Security Headers Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on a comprehensive inspection of the <strong>six core security headers</strong>.<br>
                                            Key HTTP security headers such as CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy,
                                            Permissions-Policy, and HSTS were evaluated.  
                                            The authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may change depending on server configuration.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in the Security Headers Test,  
                                                demonstrating <u>excellent web security configuration</u>.<br>
                                                This indicates a <strong>strong defense</strong> against major web vulnerabilities such as <strong>XSS, clickjacking, and MIME sniffing</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Per-header score details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Per-Header Score Analysis</h4>
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
                                                                    title="{{ $item['value'] ?? '(Not configured)' }}">
                                                                    {{ $item['value'] ?? '(Not configured)' }}
                                                                </td>
                                                                <td>{{ round((($item['score'] ?? 0) * 100) / 60, 1) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reasons for grade -->
                                    @if (!empty($report['reasons']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <strong>Reasons for Grade:</strong><br>
                                                    {{ implode(' · ', $report['reasons']) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Top-tier security header configuration (all headers applied with strong CSP)
                                        @elseif ($grade === 'A')
                                            Excellent security header configuration (most headers applied)
                                        @elseif ($grade === 'B')
                                            Good security header configuration (core headers applied)
                                        @else
                                            Security header configuration improvements required
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>CSP:</strong> The most powerful mechanism to defend against XSS and injection attacks</p>
                                        <p class="mb-2"><strong>X-Frame-Options:</strong> Blocks iframe embedding to prevent clickjacking</p>
                                        <p class="mb-0"><strong>HSTS:</strong> Forces HTTPS to prevent MITM and protocol downgrade attacks</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Security Headers Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Comprehensive Test of 6 Core Security Headers</h3>
                                <div class="text-muted small mt-1">
                                    HTTP response headers activate browser security features to protect web applications from various attacks.
                                    <br><br>
                                    • <strong>Content-Security-Policy (CSP)</strong>: Restricts resource origins; prevents XSS and third-party script abuse<br>
                                    • <strong>X-Frame-Options</strong>: Blocks iframe embedding; prevents clickjacking and phishing overlays<br>
                                    • <strong>X-Content-Type-Options</strong>: Disables MIME sniffing; prevents unintended execution vulnerabilities<br>
                                    • <strong>Referrer-Policy</strong>: Minimizes URL data on external requests; prevents leakage of personal/internal paths<br>
                                    • <strong>Permissions-Policy</strong>: Limits browser features (location, mic, camera, etc.); protects privacy<br>
                                    • <strong>Strict-Transport-Security (HSTS)</strong>: Enforces HTTPS; prevents MITM and protocol downgrade attacks
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
                                                <td>Strong CSP (nonce/hash/strict-dynamic; no unsafe-*)<br>
                                                    XFO: DENY/SAMEORIGIN or restricted frame-ancestors<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: strict-origin-when-cross-origin or stronger<br>
                                                    Permissions-Policy: unnecessary features blocked<br>
                                                    HSTS: ≥ 6 months + subdomains</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>85–94</td>
                                                <td>CSP present (weak allowed) or 5 non-CSP headers strong<br>
                                                    XFO applied (or frame-ancestors restricted)<br>
                                                    X-Content-Type: nosniff<br>
                                                    Referrer-Policy: recommended value<br>
                                                    Permissions-Policy: baseline restrictions<br>
                                                    HSTS: ≥ 6 months</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–84</td>
                                                <td>No/weak CSP<br>
                                                    XFO correctly applied<br>
                                                    X-Content-Type: present<br>
                                                    Referrer-Policy: fair/average<br>
                                                    Permissions-Policy: partial restrictions<br>
                                                    HSTS: short or no subdomains</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>55–69</td>
                                                <td>Only some headers present<br>
                                                    No/weak CSP<br>
                                                    Weak Referrer-Policy<br>
                                                    X-Content-Type missing<br>
                                                    HSTS missing or very short</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>40–54</td>
                                                <td>Only 1–2 core headers<br>
                                                    No CSP<br>
                                                    Referrer weak/absent<br>
                                                    Many other headers missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–39</td>
                                                <td>Security headers nearly absent<br>
                                                    No CSP/XFO/X-Content-Type<br>
                                                    No Referrer-Policy<br>
                                                    No HSTS</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Why Security Headers Matter</strong><br>
                                    - <strong>XSS Defense</strong>: CSP blocks script-injection at the source<br>
                                    - <strong>Clickjacking Prevention</strong>: X-Frame-Options blocks malicious iframes<br>
                                    - <strong>MIME Sniffing Defense</strong>: X-Content-Type-Options prevents type spoofing<br>
                                    - <strong>Leak Prevention</strong>: Referrer-Policy protects sensitive URL data<br><br>

                                    <strong>🌍 Where to Configure</strong><br>
                                    • <strong>CDN level</strong>: Cloudflare, CloudFront<br>
                                    • <strong>Web server level</strong>: Nginx, Apache configs<br>
                                    • <strong>Application level</strong>: Middleware in Laravel, Express.js, etc.<br><br>

                                    <strong>📊 Grading Policy</strong><br>
                                    - Strong CSP is required for A+<br>
                                    - A grade possible without CSP if the other five are strong<br>
                                    - Best protection when all headers are applied together
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Security Vulnerability Scan)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td class="{{ ($vulnerabilities['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['critical'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td class="{{ ($vulnerabilities['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $vulnerabilities['high'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td class="{{ ($vulnerabilities['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $vulnerabilities['medium'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($vulnerabilities['low'] ?? 0) + ($vulnerabilities['informational'] ?? 0) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Security Vulnerability Scan Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on web vulnerability analysis results from an <strong>OWASP ZAP</strong> passive scan.<br>
                                            By analyzing HTTP responses non-intrusively, it evaluates security headers, sensitive data exposure, session management, and potential vulnerabilities.  
                                            The authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may change with website updates and security patches.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in the Security Vulnerability Scan,  
                                                demonstrating an <u>excellent security posture</u>.<br>
                                                This indicates <strong>no major vulnerabilities</strong> and a <strong>secure configuration</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vulnerability summary -->
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

                                    <!-- Key findings -->
                                    @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Key Findings</h4>
                                                <div class="alert alert-warning">
                                                    <strong>{{ count($vulnerabilities['details']) }} security issues detected.</strong>
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
                                                            <li>and {{ count($vulnerabilities['details']) - 5 }} more…</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Detected technologies -->
                                    @if (isset($technologies) && count($technologies) > 0)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Detected Tech Stack</h4>
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
                                            Top-tier security (No Critical/High, full security headers)
                                        @elseif ($grade === 'A')
                                            Excellent security (No Critical, minimal High, solid configuration)
                                        @elseif ($grade === 'B')
                                            Good security (some improvements recommended)
                                        @else
                                            Security improvements required
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>OWASP ZAP:</strong> The world’s most widely used open-source web security testing tool</p>
                                        <p class="mb-2"><strong>Passive Scan:</strong> Non-intrusive inspection that analyzes HTTP responses without active attacks</p>
                                        <p class="mb-0"><strong>Scope:</strong> Security headers, sensitive data exposure, session management, technology fingerprinting</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC OWASP ZAP Security Scan.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>OWASP ZAP Passive Scan — Non-Intrusive Vulnerability Analysis</h3>
                                <div class="text-muted small mt-1">
                                    OWASP ZAP (Zed Attack Proxy) is the world’s most widely used open-source web application security testing tool.
                                    <br><br>
                                    • <strong>Tool</strong>: OWASP ZAP — industry-standard web security testing tool<br>
                                    • <strong>Method</strong>: Passive scanning (analyzes HTTP responses without real attacks)<br>
                                    • <strong>Checks</strong>: Security headers, sensitive data exposure, session management, potential injection points<br>
                                    • <strong>Tech Detection</strong>: Identifies servers, frameworks, and libraries in use<br>
                                    • <strong>Duration</strong>: Approximately 10–20 seconds
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
                                                <td>High/Medium = 0<br>Security headers complete (HTTPS, HSTS, X-Frame-Options, etc.)<br>No sensitive data exposure<br>Minimal server/framework version disclosure</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80–89</td>
                                                <td>High = 0, Medium ≤ 1<br>Most security headers satisfied<br>No sensitive data exposure<br>Minor informational disclosures exist</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–79</td>
                                                <td>High ≤ 1, Medium ≤ 2<br>Some security headers missing<br>Session cookies missing Secure/HttpOnly<br>Minor internal identifiers exposed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60–69</td>
                                                <td>High ≥ 2 or Medium ≥ 3<br>Key security headers absent<br>Sensitive parameters/tokens exposed<br>Weak session management</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–59</td>
                                                <td>Critical ≥ 1 or High ≥ 3<br>Severe auth/session attribute gaps<br>Debug/developer info exposed<br>Public admin consoles/config files</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td>Widespread High issues<br>HTTPS not enforced or disabled<br>Sensitive data in cleartext<br>Broad lack of headers/session controls</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Highlights of OWASP ZAP Passive Scan</strong><br>
                                    - <strong>Non-intrusive</strong>: Analyzes HTTP responses without active attacks<br>
                                    - <strong>Fast</strong>: Identifies key issues within 10–20 seconds<br>
                                    - <strong>Safe</strong>: Assesses security posture without service impact<br>
                                    - <strong>Comprehensive</strong>: Evaluates headers, sessions, and information disclosure<br><br>

                                    <strong>🌍 Risk Levels</strong><br>
                                    • <strong>Critical</strong>: Immediate action (SQLi, XSS, RCE)<br>
                                    • <strong>High</strong>: Prompt fixes (session weaknesses, CSRF)<br>
                                    • <strong>Medium</strong>: Recommended improvements (missing headers)<br>
                                    • <strong>Low</strong>: Lower risk (info disclosure, config issues)<br>
                                    • <strong>Info</strong>: Informational items<br><br>

                                    <strong>📊 Recommendations</strong><br>
                                    - Configure security headers (HSTS, X-Frame-Options, X-Content-Type-Options)<br>
                                    - Set Secure, HttpOnly, and SameSite on cookies<br>
                                    - Suppress server version, debug messages, and other disclosures<br>
                                    - Run regular monthly security scans
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Latest CVE Vulnerability Scan)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Critical</strong></td>
                                                            <td class="{{ ($metrics['vulnerability_counts']['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>High</strong></td>
                                                            <td class="{{ ($metrics['vulnerability_counts']['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['high'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Medium</strong></td>
                                                            <td class="{{ ($metrics['vulnerability_counts']['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                                                {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Low/Info</strong></td>
                                                            <td>{{ ($metrics['vulnerability_counts']['low'] ?? 0) + ($metrics['vulnerability_counts']['info'] ?? 0) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Latest CVE Scan Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on analysis performed with <strong>Nuclei by ProjectDiscovery</strong>.<br>
                                            Newly published CVEs from 2024–2025, zero-day vulnerabilities, misconfigurations, and sensitive data exposure were
                                            examined precisely using template-based scanning.  
                                            The authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may change with patches and updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in the latest CVE scan,  
                                                demonstrating <u>excellent responsiveness to emerging threats</u>.<br>
                                                This indicates up-to-date <strong>2024–2025 CVE patching</strong> and <strong>secure configuration management</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vulnerability summary -->
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
                                                    Scan Duration: {{ $metrics['scan_duration'] }}s |
                                                    Templates Matched: {{ $metrics['templates_matched'] ?? 0 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Critical/High vulnerabilities -->
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
                                                    <strong>{{ $criticalHighCount }} high-risk issues detected.</strong>
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
                                                            <li>and {{ $criticalHighCount - 6 }} more…</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info d-block">
                                        <strong>Security Level:</strong>
                                        @if ($grade === 'A+')
                                            Top-tier security (0 Critical/High; no 2024–2025 CVEs detected)
                                        @elseif ($grade === 'A')
                                            Excellent security (no recent CVE exposure; patch management is strong)
                                        @elseif ($grade === 'B')
                                            Good security (some configuration improvements recommended)
                                        @else
                                            Security improvements required
                                        @endif
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Nuclei:</strong> ProjectDiscovery’s industry-standard, template-driven vulnerability scanner</p>
                                        <p class="mb-2"><strong>CVE Coverage:</strong> Newly published 2024–2025 CVEs and major issues like Log4Shell, Spring4Shell</p>
                                        <p class="mb-0"><strong>Scope:</strong> WordPress/Joomla/Drupal plugins, Git/ENV exposure, API endpoints</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Nuclei CVE Scan.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on international standards,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Automated Detection of Latest CVEs with Nuclei</h3>
                                <div class="text-muted small mt-1">
                                    Nuclei by ProjectDiscovery is an industry-standard scanner offering fast, template-based detection.
                                    <br><br>
                                    • <strong>Tool</strong>: Nuclei — template-based vulnerability scanner<br>
                                    • <strong>Coverage Window</strong>: Newly published CVEs in 2024–2025<br>
                                    • <strong>Checks</strong>: Zero-days, misconfigurations, sensitive data exposure, backup files<br>
                                    • <strong>Major Issues</strong>: High-impact RCEs such as Log4Shell, Spring4Shell<br>
                                    • <strong>Duration</strong>: ~30 seconds to 3 minutes (varies by template count)
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
                                                <td>Critical/High = 0; Medium = 0<br>No 2024–2025 CVEs detected<br>No exposure of public dirs/debug/sensitive files<br>Good header/banner hygiene</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80–89</td>
                                                <td>High ≤ 1; Medium ≤ 1<br>No direct exposure to recent CVEs<br>Minor configuration warnings only<br>Good patch/config management</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–79</td>
                                                <td>High ≤ 2 or Medium ≤ 3<br>Some config/banner exposures<br>Protected admin endpoints present<br>Tendency for delayed patching</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60–69</td>
                                                <td>High ≥ 3 or many Medium<br>Sensitive files/backups/indexing exposed<br>Outdated components inferred<br>Systematic improvements required</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–59</td>
                                                <td>Critical ≥ 1 or easily exploitable High<br>Likely impacted by recent (2024–2025) CVEs<br>Risky endpoints accessible without auth<br>Build/log/env data exposed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td>Multiple Critical/High simultaneously<br>Widespread exposure to latest CVEs/unpatched state<br>Missing basic hardening<br>Lack of guardrails across TLS/app layers</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Nuclei Scan Highlights</strong><br>
                                    - <strong>Template-Driven</strong>: Accurate detection via YAML templates<br>
                                    - <strong>Non-intrusive</strong>: Signature checks without active exploitation<br>
                                    - <strong>Fast</strong>: Optimized templates finish in 30s–3m<br>
                                    - <strong>Latest CVEs</strong>: Rapid incorporation of 2024–2025 disclosures<br><br>

                                    <strong>🌍 Coverage Areas</strong><br>
                                    • <strong>Major RCEs</strong>: Log4Shell, Spring4Shell, etc.<br>
                                    • <strong>CMS Plugins</strong>: WordPress, Joomla, Drupal<br>
                                    • <strong>Web Server Configs</strong>: Apache, Nginx, IIS<br>
                                    • <strong>Exposure Detection</strong>: Git, SVN, ENV files<br>
                                    • <strong>API Weaknesses</strong>: GraphQL, REST<br>
                                    • <strong>Cloud</strong>: AWS, Azure, GCP misconfigurations<br><br>

                                    <strong>📊 Recommendations</strong><br>
                                    - Patch Critical/High issues immediately<br>
                                    - Keep CMS, plugins, and frameworks up to date<br>
                                    - Disable unnecessary services; remove debug modes<br>
                                    - Run monthly vulnerability scans
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Google Lighthouse Quality Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                            This certificate is based on results from the <strong>Google Lighthouse engine</strong>.<br>
                                            All data was collected by <u>simulating a real browser environment</u>, and the authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may vary depending on ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> grade in the Google Lighthouse assessment,  
                                                demonstrating a <u>top 10% web quality level</u>.<br>
                                                This indicates <strong>excellent performance</strong> along with <strong>high accessibility and SEO optimization</strong>.
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
                                                                <th>Recommendation</th>
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
                                                    <h4 class="mb-3">Opportunity Analysis</h4>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Estimated Improvement</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($opportunities->take(5) as $key => $opportunity)
                                                                    <tr>
                                                                        <td>{{ $opportunity['title'] ?? $key }}</td>
                                                                        <td>Up to {{ round($opportunity['details']['overallSavingsMs'] ?? 0) }}ms faster</td>
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
                                        <span class="text-muted">Each area is scored out of 100, and the overall score is a weighted average of the four.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>FCP:</strong> Time from navigation until the first content is rendered</p>
                                        <p class="mb-2"><strong>LCP:</strong> Time when the largest content element is rendered</p>
                                        <p class="mb-2"><strong>CLS:</strong> Cumulative score of unexpected layout shifts during load</p>
                                        <p class="mb-0"><strong>TBT:</strong> Total time the main thread is blocked and unable to respond to input</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Lighthouse Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web quality measurements based on the Google Lighthouse engine,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Google Lighthouse — Comprehensive Website Quality Measurement</h3>
                                <div class="text-muted small mt-1">
                                    Google Lighthouse is an open-source quality auditing tool by Google, built into Chrome DevTools.  
                                    It evaluates performance, accessibility, SEO, and adherence to best practices.
                                    <br><br>
                                    <strong>Tool & Environment</strong><br>
                                    • Latest Lighthouse version (Chrome engine based)<br>
                                    • Headless Chrome simulating a real browser environment<br>
                                    • Mobile 3G/4G network and mid-range device profile<br>
                                    • Core Web Vitals reflecting real user experience
                                    <br><br>
                                    <strong>The Four Areas</strong><br>
                                    1. <strong>Performance</strong>: Load speed, Core Web Vitals, resource optimization<br>
                                    2. <strong>Accessibility</strong>: ARIA labels, color contrast, keyboard navigation<br>
                                    3. <strong>Best Practices</strong>: HTTPS usage, console errors, image aspect ratio<br>
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
                                    - <strong>FCP (First Contentful Paint)</strong>: Time from load start until the first content is rendered<br>
                                    - <strong>LCP (Largest Contentful Paint)</strong>: When the largest element in the viewport is rendered (≤ 2.5s recommended)<br>
                                    - <strong>CLS (Cumulative Layout Shift)</strong>: Cumulative score of unexpected layout shifts (≤ 0.1 recommended)<br>
                                    - <strong>TBT (Total Blocking Time)</strong>: Total time the main thread is blocked between FCP and TTI (≤ 200ms recommended)<br>
                                    - <strong>TTI (Time to Interactive)</strong>: When the page becomes fully interactive (≤ 3.8s recommended)<br>
                                    - <strong>Speed Index</strong>: How quickly content is visually displayed (≤ 3.4s recommended)
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Web Accessibility Audit)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-danger">{{ $counts['critical'] ?? 0 }}</div>
                                                            <small>Critical</small>
                                                            <div class="small text-muted">Blocking issues</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-orange">{{ $counts['serious'] ?? 0 }}</div>
                                                            <small>Serious</small>
                                                            <div class="small text-muted">Major limitations</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-warning">{{ $counts['moderate'] ?? 0 }}</div>
                                                            <small>Moderate</small>
                                                            <div class="small text-muted">Partial inconvenience</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card card-sm">
                                                        <div class="card-body text-center py-2">
                                                            <div class="h2 mb-0 text-info">{{ $counts['minor'] ?? 0 }}</div>
                                                            <small>Minor</small>
                                                            <div class="small text-muted">Minor issues</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <strong>Total Violations: {{ $counts['total'] ?? 0 }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on results from the <strong>axe-core engine (Deque Systems)</strong>.<br>
                                            All data is collected in accordance with the <u>WCAG 2.1 international standard</u>, and the authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may vary depending on ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> grade in the accessibility audit,  
                                                demonstrating an <u>excellent level of web accessibility</u>.<br>
                                                This indicates an inclusive website where <strong>all users, including people with disabilities and older adults</strong>, can participate equally.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Violation details -->
                                    @if (!empty($violations))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Key Violations</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="80">Impact</th>
                                                                <th>Description</th>
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
                                                        <small class="text-muted">Showing top 10 of {{ count($violations) }} total</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Impact scale -->
                                    <div class="alert alert-info d-block">
                                        <strong>Accessibility Violation Impact Scale:</strong><br>
                                        <span class="text-danger">● Critical</span>: Prevents users from using a feature entirely (keyboard traps, missing required ARIA)<br>
                                        <span class="text-orange">● Serious</span>: Causes severe difficulty with major features (unlabeled forms, low color contrast)<br>
                                        <span class="text-warning">● Moderate</span>: Inconvenient for some users (ambiguous link text)<br>
                                        <span class="text-info">● Minor</span>: Slight UX degradation (empty headings, duplicate IDs)
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>WCAG 2.1 Conformance:</strong> Perceivable, Operable, Understandable, Robust</p>
                                        <p class="mb-2"><strong>Legal Requirements:</strong> Korea Anti-Discrimination Act, U.S. ADA, EU EN 301 549</p>
                                        <p class="mb-0"><strong>Tooling:</strong> axe-core CLI (Deque Systems) — industry-standard accessibility engine</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Accessibility Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides web accessibility assessments based on the WCAG 2.1 international standard,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Web Accessibility Audit — WCAG 2.1 International Standard Compliance</h3>
                                <div class="text-muted small mt-1">
                                    Web accessibility ensures that all users — including people with disabilities and older adults — can use your website equally.  
                                    WCAG (Web Content Accessibility Guidelines) 2.1 is an international standard established by the W3C and used worldwide as the benchmark for accessibility.
                                    <br><br>
                                    <strong>Tools & Environment</strong><br>
                                    • axe-core CLI (Deque Systems) — industry-standard accessibility engine<br>
                                    • WCAG 2.1 Level AA criteria<br>
                                    • Automated checks for detectable accessibility issues<br>
                                    • Compatibility validation with screen readers and keyboard navigation
                                    <br><br>
                                    <strong>The Four Accessibility Principles (POUR)</strong><br>
                                    1. <strong>Perceivable</strong>: Content perceivable through multiple senses<br>
                                    2. <strong>Operable</strong>: All functionality available via keyboard alone<br>
                                    3. <strong>Understandable</strong>: Information and UI interactions are easy to understand<br>
                                    4. <strong>Robust</strong>: Compatible with a wide range of assistive technologies
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
                                                <td>98–100</td>
                                                <td>Critical: 0<br>Serious: 0<br>Moderate: 0–2<br>Minor: 0–5</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>95–97</td>
                                                <td>Critical: 0<br>Serious: 0–1<br>Moderate: 0–5<br>Minor: 0–10</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>90–94</td>
                                                <td>Critical: 0<br>Serious: 0–3<br>Moderate: 0–10<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>80–89</td>
                                                <td>Critical: 0–1<br>Serious: 0–5<br>Moderate: 0–20<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>70–79</td>
                                                <td>Critical: 0–3<br>Serious: 0–10<br>Moderate: Unlimited<br>Minor: Unlimited</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–69</td>
                                                <td>Below the thresholds above</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Legal Requirements & Standards</strong><br>
                                    - <strong>Korea</strong>: Anti-Discrimination Act, KWCAG 2.2 (Korean Web Content Accessibility Guidelines)<br>
                                    - <strong>USA</strong>: ADA (Americans with Disabilities Act), Section 508<br>
                                    - <strong>EU</strong>: EN 301 549, Web Accessibility Directive<br>
                                    - <strong>International</strong>: ISO/IEC 40500, WCAG 2.1 Level AA<br><br>

                                    Web accessibility is not only a legal obligation but also a crucial quality metric that expands your audience,  
                                    improves SEO, and enhances brand perception.
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Cross-Browser Compatibility Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                            <small>Browsers Passed</small>
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
                                                        Third-party JS errors: {{ $jsThirdPartyTotal }}
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
                                            All data was collected across the <u>three major browsers: Chrome, Firefox, and Safari</u>, and the authenticity of the results can be verified by anyone via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may vary depending on ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> grade in the cross-browser compatibility audit,  
                                                demonstrating <u>excellent compatibility across browsers</u>.<br>
                                                This indicates a high-quality website that runs reliably on <strong>all major browsers</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Per-browser details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Per-Browser Detailed Results</h4>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Browser</th>
                                                            <th>Loaded</th>
                                                            <th>JS Errors (First-party)</th>
                                                            <th>CSS Errors</th>
                                                            <th>Notes</th>
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
                                                                        <span class="badge bg-green-lt text-green-lt-fg">Pass</span>
                                                                    @else
                                                                        <span class="badge bg-red-lt text-red-lt-fg">Fail</span>
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

                                    <!-- Error samples (key issues only) -->
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
                                        <strong>Metrics:</strong> Successful load (full page load confirmed), JS errors (first-party/third-party/noise), CSS errors (parsing & rendering)<br>
                                        <span class="text-muted">First-party errors originate from the test domain; third-party errors come from external services.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Tested Browsers:</strong> Chromium (Chrome/Edge engine), Firefox (Gecko), WebKit (Safari)</p>
                                        <p class="mb-2"><strong>Tool:</strong> Playwright — browser automation by Microsoft</p>
                                        <p class="mb-0"><strong>Evaluation Mode:</strong> {{ $strictMode ? 'Strict Mode — all errors included' : 'Standard Mode — focused on first-party errors' }}</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Cross-Browser Compatibility Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides compatibility assessments based on major browser engines,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Compatibility Across the Three Major Browsers: Chrome, Firefox, and Safari</h3>
                                <div class="text-muted small mt-1">
                                    A cross-browser compatibility audit that checks whether your site functions correctly on the major browsers.
                                    <br><br>
                                    <strong>Tool:</strong> Playwright (Microsoft’s browser automation framework)<br>
                                    • Chromium (engine used by Chrome and Edge)<br>
                                    • Firefox (Gecko engine)<br>
                                    • WebKit (engine used by Safari)
                                    <br><br>
                                    <strong>Measurements:</strong><br>
                                    • Page load success (document.readyState === 'complete')<br>
                                    • JavaScript error collection (categorized as first-party / third-party / noise)<br>
                                    • CSS error collection (parser-pattern based)<br>
                                    • Per-browser User-Agent information
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
                                                <td><strong>All</strong> of Chrome/Firefox/Safari pass<br>
                                                    First-party JS errors: <strong>0</strong><br>
                                                    CSS rendering errors: <strong>0</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80–89</td>
                                                <td><strong>Good</strong> support (≥ 2 browsers pass)<br>
                                                    First-party JS errors <strong>≤ 1</strong><br>
                                                    CSS errors <strong>≤ 1</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70–79</td>
                                                <td><strong>Minor differences</strong> across browsers (≥ 2 pass)<br>
                                                    First-party JS errors <strong>≤ 3</strong><br>
                                                    CSS errors <strong>≤ 3</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60–69</td>
                                                <td><strong>Degraded functionality</strong> in some browsers (≥ 1 pass)<br>
                                                    First-party JS errors <strong>≤ 6</strong><br>
                                                    CSS errors <strong>≤ 6</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–59</td>
                                                <td><strong>Numerous</strong> compatibility issues<br>
                                                    First-party JS errors <strong>≤ 10</strong><br>
                                                    CSS errors <strong>≤ 10</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td><strong>Unable to run</strong> properly on major browsers<br>
                                                    First-party JS errors <strong>> 10</strong><br>
                                                    CSS errors <strong>> 10</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Why Cross-Browser Compatibility Matters</strong><br>
                                    - <strong>User experience</strong>: All users get a consistent experience regardless of browser<br>
                                    - <strong>Market share</strong>: Chrome 65%, Safari 19%, Firefox 3% (as of 2024)<br>
                                    - <strong>Business impact</strong>: Compatibility issues directly increase churn and reduce revenue<br>
                                    - <strong>SEO impact</strong>: Search engines negatively assess JavaScript errors during crawling<br><br>
                                    
                                    Cross-browser testing is an essential quality gate that must be performed after development is complete.
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
                                                PSQC Comprehensive Certificate - Detailed Test Results
                                            </h1>
                                            <h2>(Responsive UI Compliance Test)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                <div class="col-4">
                                                    <div class="card text-center">
                                                        <div class="card-body py-2">
                                                            <h3 class="mb-0">{{ 9 - $overflowCount }}/9</h3>
                                                            <small>Viewports Passing</small>
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
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on results from the <strong>Playwright engine (Chromium)</strong>.<br>
                                            All data was collected across <u>nine key device viewports</u>, and anyone can verify the authenticity via the QR validation system.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test reflects objective results at a specific point in time and may vary depending on ongoing improvements and optimization.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> grade in the responsive UI audit,
                                                demonstrating <u>excellent responsive web design</u>.<br>
                                                This indicates a user-friendly website that renders perfectly <strong>without horizontal scrolling</strong> across <strong>all devices</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Per-viewport results -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Per-Viewport Measurements</h4>
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
                                                                        <span class="badge bg-green-lt text-green-lt-fg">Pass</span>
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

                                    <!-- By device group -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">Breakdown by Device Group</h4>
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
                                                            <h5>Mobile (360–414px)</h5>
                                                            <div class="h3">{{ $mobileCount }}/3</div>
                                                            <small>Pass</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>Tablet (672–1024px)</h5>
                                                            <div class="h3">{{ $tabletCount }}/4</div>
                                                            <small>Pass</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5>Desktop (1280px+)</h5>
                                                            <div class="h3">{{ $desktopCount }}/2</div>
                                                            <small>Pass</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-block">
                                        <strong>Measurement Method:</strong> Set browser to each viewport → load page → measure body width → compare with viewport width<br>
                                        <span class="text-muted">When overflow occurs, users need to scroll horizontally, which significantly degrades mobile usability.</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Test Viewports:</strong> 3 mobile, 1 foldable, 3 tablet, 2 desktop (total 9)</p>
                                        <p class="mb-2"><strong>Measurement Basis:</strong> document.body.getBoundingClientRect().width vs window.innerWidth</p>
                                        <p class="mb-0"><strong>Stabilization Wait:</strong> Wait 6 seconds after network idle to account for dynamic content</p>
                                    </div>

                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC Responsive UI Test.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides responsive UI assessments across diverse device environments,  
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Playwright-Based Responsive UI Compliance Audit</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Tool:</strong> Playwright (Chromium engine)<br>
                                    <strong>Objective:</strong> Verify that pages render correctly across devices without exceeding viewport bounds<br>
                                    <strong>Targets:</strong> 9 key viewports (3 mobile, 1 foldable, 3 tablet, 2 desktop)<br><br>

                                    <strong>Test Method:</strong><br>
                                    1. Set the browser to each viewport size<br>
                                    2. Wait for network stabilization (6 seconds) after load<br>
                                    3. Measure document.body.getBoundingClientRect()<br>
                                    4. Compare with viewport width and compute overflow pixels<br><br>

                                    <strong>Viewport List:</strong><br>
                                    • Mobile: 360×800, 390×844, 414×896<br>
                                    • Foldable: 672×960<br>
                                    • Tablet: 768×1024, 834×1112, 1024×1366<br>
                                    • Desktop: 1280×800, 1440×900
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
                                                <td>100</td>
                                                <td>No overflow in any viewport<br>Body render width always within viewport</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>90–95</td>
                                                <td>≤ 1 overflow and ≤ 8px each<br>No overflow in narrow mobile (≤ 390px)</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>80–89</td>
                                                <td>≤ 2 overflows and each ≤ 16px<br>or ≤ 8px once in narrow mobile</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>70–79</td>
                                                <td>≤ 4 overflows or a single overflow of 17–32px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50–69</td>
                                                <td>> 4 overflows or a single overflow of 33–64px</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0–49</td>
                                                <td>Measurement failure or ≥ 65px overflow</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Why Responsive Design Matters</strong><br>
                                    - <strong>Mobile-first:</strong> Over 60% of web traffic is mobile (as of 2024)<br>
                                    - <strong>User experience:</strong> Horizontal scrolling increases mobile bounce by ~40%<br>
                                    - <strong>SEO impact:</strong> Google treats mobile friendliness as a key ranking factor<br>
                                    - <strong>Accessibility:</strong> Delivers an equitable experience across diverse devices<br><br>
                                    
                                    Responsive UI is an essential requirement in modern web development and directly impacts business success.
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
                                                Web Quality Certificate - Detailed Report
                                            </h1>
                                            <h2>(Link Validation Test)</h2>
                                            <h3>Certificate ID: {{ $certification->code }}</h3>
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
                                                                {{ number_format($score, 1) }} Points
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
                                                            <th>Tested</th>
                                                            <th>Errors</th>
                                                            <th>Error Rate</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>All Links</strong></td>
                                                            <td>{{ $totals['httpChecked'] ?? 0 }} links</td>
                                                            <td>{{ ($totals['internalErrors'] ?? 0) + ($totals['externalErrors'] ?? 0) }} errors</td>
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
                                                            <td>{{ $totals['internalChecked'] ?? 0 }} links</td>
                                                            <td>{{ $totals['internalErrors'] ?? 0 }} errors</td>
                                                            <td>{{ $rates['internalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>External Links</strong></td>
                                                            <td>{{ $totals['externalChecked'] ?? 0 }} links</td>
                                                            <td>{{ $totals['externalErrors'] ?? 0 }} errors</td>
                                                            <td>{{ $rates['externalErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Image Links</strong></td>
                                                            <td>{{ $totals['imageChecked'] ?? 0 }} images</td>
                                                            <td>{{ $totals['imageErrors'] ?? 0 }} errors</td>
                                                            <td>{{ $rates['imageErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Anchor Links</strong></td>
                                                            <td>{{ $totals['anchorChecked'] ?? 0 }} anchors</td>
                                                            <td>{{ $totals['anchorErrors'] ?? 0 }} errors</td>
                                                            <td>{{ $rates['anchorErrorRate'] ?? 0 }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Max Redirect Chain</strong></td>
                                                            <td colspan="3">{{ $totals['maxRedirectChainEffective'] ?? 0 }} step chain</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on comprehensive link validity testing results conducted through <strong>Playwright-based link validation tools</strong>.<br>
                                            All data was collected in <u>real browser environments</u> including JavaScript dynamic content.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents link status at a specific point in time and may vary due to external site changes.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in link validation testing,
                                                demonstrating <u>excellent website link integrity</u>.<br>
                                                This shows that it is a website with excellent <strong>user experience</strong> and <strong>content accessibility</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Error Link Details -->
                                    @if (!empty($samples['links']) || !empty($samples['images']) || !empty($samples['anchors']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Error Link Samples</h4>
                                                
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
                                                                <div class="text-muted small">... Plus {{ count($samples['links']) - 10 }} more errors</div>
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
                                                                <div class="text-muted small">... Plus {{ count($samples['images']) - 10 }} more errors</div>
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
                                                                <div class="text-muted small mt-2">... Plus {{ count($samples['anchors']) - 10 }} more errors</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-success d-block">
                                            <strong>✨ Perfect Link Status</strong><br>
                                            All tested links are functioning properly.
                                        </div>
                                    @endif

                                    <!-- Additional Information -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Link Integrity Matters</strong><br>
                                        - User Experience: Broken links reduce user trust and increase bounce rates<br>
                                        - SEO Impact: High numbers of 404 errors negatively affect search engine rankings<br>
                                        - Accessibility: All content must be properly accessible to comply with web standards<br>
                                        - Brand Image: Broken images or links are detrimental to professionalism
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Internal Links:</strong> Connections between pages within the same domain</p>
                                        <p class="mb-2"><strong>External Links:</strong> Links to other websites</p>
                                        <p class="mb-2"><strong>Image Links:</strong> Resources in img tag src attributes</p>
                                        <p class="mb-2"><strong>Anchor Links:</strong> Navigation to specific sections within a page (#id)</p>
                                        <p class="mb-0"><strong>Redirect Chain:</strong> Number of redirects to reach final destination</p>
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
                                            ✔ This result has been verified through Web-PSQC's Link Validator.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides precise Playwright-based link validation services,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Playwright-based Link Validation Tool</h3>
                                <div class="text-muted small mt-1">
                                    <strong>Testing Tool:</strong> Playwright + Node.js based custom crawler<br>
                                    <strong>Test Purpose:</strong> Examine all link statuses on the website to identify broken links, incorrect redirects, and missing anchors that harm user experience.
                                    <br><br>
                                    <strong>Test Coverage:</strong><br>
                                    • Internal Links: HTTP status of all page links within the same domain<br>
                                    • External Links: Validity of links to external domains<br>
                                    • Image Links: Status of image resources in img tag src attributes<br>
                                    • Anchor Links: Existence of #id format anchors within the same page<br>
                                    • Redirect Chains: Number of redirect steps and final destinations for each link
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
                                                <td>90~100</td>
                                                <td>• Internal/External/Image link error rate: 0%<br>
                                                    • Redirect chain ≤1 step<br>
                                                    • Anchor links 100% functional</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80~89</td>
                                                <td>• Overall error rate ≤1%<br>
                                                    • Redirect chain ≤2 steps<br>
                                                    • Most anchor links functional</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70~79</td>
                                                <td>• Overall error rate ≤3%<br>
                                                    • Redirect chain ≤3 steps<br>
                                                    • Some anchor link issues</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>60~69</td>
                                                <td>• Overall error rate ≤5%<br>
                                                    • Multiple link warnings (timeout/SSL issues)<br>
                                                    • Frequent anchor link errors</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50~59</td>
                                                <td>• Overall error rate ≤10%<br>
                                                    • Redirect loops or long chains<br>
                                                    • Many broken image links</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~49</td>
                                                <td>• Overall error rate >10%<br>
                                                    • Many major internal links broken<br>
                                                    • Overall anchor/image issues</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Link Management Checklist</strong><br>
                                    <strong>Regular Monitoring:</strong><br>
                                    • Run full link checks monthly<br>
                                    • Monitor external link validity<br>
                                    • Fix 404 errors immediately<br><br>
                                    
                                    <strong>Optimization Strategies:</strong><br>
                                    • Minimize redirects: Use direct links<br>
                                    • Anchor matching: Ensure href="#id" matches id="id"<br>
                                    • Image optimization: Verify correct paths and file existence<br>
                                    • Use HTTPS: Apply secure protocols<br><br>
                                    
                                    <strong>Performance Metrics:</strong><br>
                                    • Broken link removal → 20% bounce rate reduction<br>
                                    • Redirect optimization → 15% page speed improvement<br>
                                    • Image normalization → 25% user satisfaction increase
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
                                                PSQC Comprehensive Certificate - Detailed Test Report
                                            </h1>
                                            <h2>(Structured Data Validation)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                            <th>Count</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>JSON-LD Blocks</strong></td>
                                                            <td>{{ $totals['jsonLdBlocks'] ?? 0 }}</td>
                                                            <td>
                                                                @if (($totals['jsonLdBlocks'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Implemented</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Not Implemented</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Schema Items</strong></td>
                                                            <td>{{ $totals['jsonLdItems'] ?? 0 }}</td>
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
                                                            <td><strong>Errors / Warnings</strong></td>
                                                            <td>
                                                                <span class="text-danger">{{ $totalErrors }}</span> /
                                                                <span class="text-warning">{{ $totals['itemWarnings'] ?? 0 }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($totalErrors === 0 && ($totals['itemWarnings'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Flawless</span>
                                                                @elseif ($totalErrors === 0)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Good</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Needs Improvement</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Rich Results Types</strong></td>
                                                            <td>{{ is_array($richTypes) ? count($richTypes) : 0 }}</td>
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
                                                                    <span class="badge">Auxiliary formats detected</span>
                                                                @else
                                                                    <span class="text-muted">JSON-LD only</span>
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
                                            This certificate is based on results from the <strong>Playwright-based Structured Data Validator</strong> that checks against Schema.org specifications.<br>
                                            All data was evaluated in accordance with the <u>Google Rich Results Test</u> criteria and collected in a real browser rendering environment.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This assessment reflects the structured data at a specific point in time and may change as your site is updated.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> in the structured data validation,
                                                demonstrating eligibility for <u>Rich Snippets in search results</u>.<br>
                                                This proves a high-quality structured data implementation that supports <strong>better visibility</strong> and <strong>higher CTR</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Schema type distribution -->
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

                                    <!-- Error & warning details -->
                                    @if (!empty($parseErrors) || !empty($perItem))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Validation Issue Details</h4>
                                                
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
                                                            <h5 class="card-title mb-0">Item-level Issues</h5>
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

                                    <!-- Recommended improvements -->
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

                                    <!-- Example snippets -->
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

                                    <!-- Extra info -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Structured Data Matters</strong><br>
                                        - Rich Snippets: Show ratings, prices, images, and more in search results<br>
                                        - Voice Search: Helps assistants understand and answer accurately<br>
                                        - Knowledge Graph: Eligible for Google knowledge panels<br>
                                        - Higher CTR: Typically ~30% better than plain results
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>JSON-LD:</strong> JavaScript Object Notation for Linked Data; Google’s recommended format</p>
                                        <p class="mb-2"><strong>Schema.org:</strong> A shared structured data vocabulary by Google, Microsoft, Yahoo, and Yandex</p>
                                        <p class="mb-2"><strong>Rich Results:</strong> Visually enhanced results displayed in SERPs</p>
                                        <p class="mb-2"><strong>Must-have Schemas:</strong> Organization, WebSite, BreadcrumbList (recommended for all sites)</p>
                                        <p class="mb-0"><strong>Content-specific Schemas:</strong> Article (blogs), Product (commerce), LocalBusiness (local)</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC’s Structure Validator.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides structured data validation aligned with Google Rich Results guidelines,
                                            and certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                    Using Playwright-based browser automation, structured data is collected from the <em>rendered</em> page
                                    and validated with Schema.org rules aligned to the Google Rich Results Test.
                                    <br><br>
                                    <strong>📊 Metrics:</strong><br>
                                    • Number of JSON-LD blocks & parseability<br>
                                    • Required/recommended fields by Schema.org type<br>
                                    • Rich Results eligibility evaluation<br>
                                    • Detection of Microdata, RDFa, and other formats<br><br>
                                    
                                    <strong>🎯 Target Schemas:</strong><br>
                                    • Organization, WebSite, BreadcrumbList (baseline)<br>
                                    • Article, NewsArticle, BlogPosting (content)<br>
                                    • Product, Offer, AggregateRating (commerce)<br>
                                    • LocalBusiness, Restaurant, Store (local)<br>
                                    • Event, Course, Recipe (special content)<br>
                                    • FAQPage, HowTo, QAPage (Q&A)<br>
                                    • Person, JobPosting, Review (others)
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
                                                <td>95–100</td>
                                                <td>• JSON-LD fully implemented (no parse errors)<br>
                                                    • ≥ 3 schema types, ≥ 2 Rich Results types<br>
                                                    • All required fields, ≥ 80% of recommended fields</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85–94</td>
                                                <td>• JSON-LD implemented correctly<br>
                                                    • ≥ 2 schema types, ≥ 1 Rich Results type<br>
                                                    • Required fields complete, ≥ 60% of recommended fields</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75–84</td>
                                                <td>• Basic JSON-LD implementation<br>
                                                    • ≥ 1 schema type<br>
                                                    • Most required fields present</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65–74</td>
                                                <td>• Partial implementation<br>
                                                    • Minor errors present<br>
                                                    • Some required fields missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50–64</td>
                                                <td>• Insufficient structured data<br>
                                                    • Parse errors or major issues<br>
                                                    • Many required fields missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0–49</td>
                                                <td>• No structured data<br>
                                                    • JSON-LD not implemented<br>
                                                    • Schema.org not applied</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Structured Data Implementation Checklist</strong><br>
                                    <strong>Required (baseline):</strong><br>
                                    • Organization: company details, logo, social profiles<br>
                                    • WebSite: site name, URL, search box<br>
                                    • BreadcrumbList: navigation path<br><br>
                                    
                                    <strong>By content type:</strong><br>
                                    • Blogs/News: Article, NewsArticle, BlogPosting<br>
                                    • Commerce: Product, Offer, Review, AggregateRating<br>
                                    • Local business: LocalBusiness, OpeningHoursSpecification<br>
                                    • Events: Event, EventVenue, EventSchedule<br><br>
                                    
                                    <strong>Impact metrics:</strong><br>
                                    • Rich Snippets → ~30% CTR uplift on average<br>
                                    • Voice search optimization → ~20% mobile traffic increase<br>
                                    • Knowledge Graph presence → improved brand recognition
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
                                                Web Quality Certificate - Detailed Report
                                            </h1>
                                            <h2>(Search Engine Crawl Inspection)</h2>
                                            <h3>Certificate ID: {{ $certification->code }}</h3>
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
                                                                {{ number_format($score, 1) }} Points
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
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Exists</span>
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
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Exists</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Missing</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Tested Pages</strong></td>
                                                            <td>{{ $pages['count'] ?? 0 }} pages</td>
                                                            <td>Average {{ number_format($pages['qualityAvg'] ?? 0, 1) }} points</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Error Rate</strong></td>
                                                            <td>{{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['errorRate4xx5xx'] ?? 0) === 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Normal</span>
                                                                @elseif (($pages['errorRate4xx5xx'] ?? 0) < 5)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Good</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Issues</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Duplicate Rate</strong></td>
                                                            <td>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</td>
                                                            <td>
                                                                @if (($pages['duplicateRate'] ?? 0) <= 30)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Good</span>
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
                                        <h4 class="mb-2">✅ Test Results Verified</h4>
                                        <p class="mb-1">
                                            This certificate is based on search engine crawl inspection results conducted through <strong>robots.txt compliant crawler</strong>.<br>
                                            All data was collected by simulating <u>actual search engine crawling methods</u> and evaluated against SEO quality standards.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This test represents crawling status at a specific point in time and may change with website updates.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This website achieved a <strong>{{ $grade }}</strong> grade in search engine crawl inspection,
                                                demonstrating that it is <u>an SEO-optimized excellent site</u>.<br>
                                                This shows that it is a website with excellent <strong>search crawler friendliness</strong> and <strong>page quality management</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Sitemap File Details -->
                                    @if (!empty($sitemap['sitemaps']))
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Sitemap File Status</h4>
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
                                                                    <td>{{ $s['count'] ?? 0 }} URLs</td>
                                                                    <td>
                                                                        @if ($s['ok'])
                                                                            <span class="badge bg-green-lt text-green-lt-fg">Normal</span>
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

                                    <!-- Crawl Plan and Excluded URLs -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Tested URL Samples</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="small text-muted mb-2">
                                                        Testing max 50 out of {{ $crawlPlan['candidateCount'] ?? 0 }} total
                                                    </div>
                                                    @if (!empty($crawlPlan['sample']))
                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                            <ul class="small mb-0">
                                                                @foreach (array_slice($crawlPlan['sample'], 0, 10) as $url)
                                                                    <li class="text-break">{{ $url }}</li>
                                                                @endforeach
                                                                @if (count($crawlPlan['sample']) > 10)
                                                                    <li>... Plus {{ count($crawlPlan['sample']) - 10 }} more</li>
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

                                    <!-- Problem Page Details -->
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
                                                                <li>... Plus {{ count($errorPages) - 5 }} more</li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <div class="card">
                                                <div class="card-header bg-warning-lt">
                                                    <h5 class="card-title mb-0">Low Quality Pages (Under 50 points)</h5>
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
                                                        <div class="text-success">No pages under 50 points ✓</div>
                                                    @else
                                                        <ul class="small mb-0">
                                                            @foreach ($lowQuality as $page)
                                                                <li class="mb-1">
                                                                    <span class="badge bg-orange-lt text-orange-lt-fg">{{ $page['score'] ?? 0 }} points</span>
                                                                    <span class="text-break">{{ Str::limit($page['url'], 50) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Duplicate Content Status -->
                                    @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                                        <div class="alert alert-warning d-block">
                                            <strong>⚠️ Duplicate Content Detected</strong><br>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    Duplicate title pages: <strong>{{ $pages['dupTitleCount'] ?? 0 }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    Duplicate description pages: <strong>{{ $pages['dupDescCount'] ?? 0 }}</strong>
                                                </div>
                                            </div>
                                            <div class="small mt-2">
                                                Duplicate rate: <strong>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</strong>
                                                - We recommend writing unique title and description for each page.
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Additional Information -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Crawl Optimization Matters</strong><br>
                                        - Search engine indexing: robots.txt and sitemap.xml are basic tools for search engines to understand your site<br>
                                        - Crawl efficiency: Accurate crawling rules prioritize important pages for indexing<br>
                                        - SEO score: Page quality and duplicate content directly affect search rankings<br>
                                        - User experience: Maintain clean site structure without 404 errors
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>robots.txt:</strong> File that defines access rules for search engine crawlers</p>
                                        <p class="mb-2"><strong>sitemap.xml:</strong> List of all important pages on the site with metadata</p>
                                        <p class="mb-2"><strong>Quality score:</strong> Comprehensive evaluation of title, description, canonical, H1, and content volume</p>
                                        <p class="mb-2"><strong>Error rate:</strong> Percentage of inaccessible pages (404, 500, etc.)</p>
                                        <p class="mb-0"><strong>Duplicate rate:</strong> Percentage of pages using identical metadata</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC's Crawl Inspector.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides inspection services compliant with search engine crawling standards,
                                            and certificates can be verified for authenticity through real-time QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Certificate Issue Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Search Engine Crawling Compliance and Page Quality Comprehensive Analysis</h3>
                                <div class="text-muted small mt-1">
                                    Analyzes website's robots.txt and sitemap.xml to verify SEO compliance and
                                    comprehensively evaluates accessibility and quality of pages registered in sitemap.
                                    <br><br>
                                    <strong>📋 Testing Process:</strong><br>
                                    1. Check robots.txt file existence and rules<br>
                                    2. Search for sitemap.xml file and collect URLs<br>
                                    3. Filter crawling-allowed URLs according to robots.txt rules<br>
                                    4. Sample and sequentially test up to 50 pages<br>
                                    5. Measure HTTP status, metadata, and quality score for each page<br>
                                    6. Analyze duplicate content (title/description) ratio
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
                                                <td>90~100</td>
                                                <td>robots.txt properly applied<br>
                                                    sitemap.xml exists with no missing/404s<br>
                                                    All tested pages return 2xx<br>
                                                    Overall page quality average ≥ 85 points<br>
                                                    Duplicate content ≤ 30%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>80~89</td>
                                                <td>robots.txt properly applied<br>
                                                    sitemap.xml exists with integrity ensured<br>
                                                    All tested pages return 2xx<br>
                                                    Overall page quality average ≥ 85 points</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>70~79</td>
                                                <td>robots.txt and sitemap.xml exist<br>
                                                    All tested pages return 2xx<br>
                                                    Overall page quality average irrelevant</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>55~69</td>
                                                <td>robots.txt and sitemap.xml exist<br>
                                                    Test list includes some 4xx/5xx errors</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>35~54</td>
                                                <td>robots.txt and sitemap.xml exist<br>
                                                    Test URL list generation possible<br>
                                                    However, low normal access rate or quality check impossible</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0~34</td>
                                                <td>robots.txt missing or sitemap.xml missing<br>
                                                    Test list generation impossible</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 Crawl Optimization Checklist</strong><br>
                                    <strong>Essential Implementation:</strong><br>
                                    • robots.txt: Specify User-agent, Allow/Disallow, Sitemap location<br>
                                    • sitemap.xml: Include all important pages, manage lastmod dates<br>
                                    • 404 handling: Custom 404 page, 301 redirect setup<br><br>
                                    
                                    <strong>Quality Score Improvement:</strong><br>
                                    • Title: 50-60 characters, unique title per page<br>
                                    • Description: 120-160 characters, unique description per page<br>
                                    • Canonical URL: Set for all pages<br>
                                    • H1 tag: One per page, clear title<br>
                                    • Content: Minimum 1000 characters of substantial content<br><br>
                                    
                                    <strong>Performance Metrics:</strong><br>
                                    • Crawl optimization → 50% faster indexing speed<br>
                                    • Duplicate content removal → 20% search ranking improvement<br>
                                    • 404 error removal → 15% user bounce rate reduction
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
                                                PSQC Comprehensive Certificate - Detailed Test Report
                                            </h1>
                                            <h2>(Metadata Completeness Audit)</h2>
                                            <h3>Certification Code: {{ $certification->code }}</h3>
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
                                                                    <span class="badge bg-red-lt text-red-lt-fg">None</span>
                                                                @elseif ($analysis['title']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                @elseif ($analysis['title']['isAcceptable'] ?? false)
                                .                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Inadequate</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['titleLength'] ?? 0 }} chars (Optimal: 50–60)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Meta Description</strong></td>
                                                            <td>
                                                                @if ($analysis['description']['isEmpty'] ?? true)
                                                                    <span class="badge bg-red-lt text-red-lt-fg">None</span>
                                                                @elseif ($analysis['description']['isOptimal'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                @elseif ($analysis['description']['isAcceptable'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Inadequate</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $summary['descriptionLength'] ?? 0 }} chars (Optimal: 120–160)</td>
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
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Set</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Not Set</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    Duplicate-content prevention configured
                                                                @else
                                                                    Configuration required
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Hreflang</strong></td>
                                                            <td>
                                                                @if (($summary['hreflangCount'] ?? 0) > 0)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">{{ $summary['hreflangCount'] }} set</span>
                                                                @else
                                                                    <span class="badge">0</span>
                                                                @endif
                                                            </td>
                                                            <td>Multilingual links: {{ $summary['hreflangCount'] ?? 0 }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-block text-start mb-3">
                                        <h4 class="mb-2">✅ Test Verification Completed</h4>
                                        <p class="mb-1">
                                            This certificate is based on results from the <strong>Meta Inspector CLI</strong> metadata completeness audit.<br>
                                            All data was collected in a <u>real browser rendering environment</u> and scored against SEO best practices.
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            ※ This audit reflects the metadata at a specific point in time and may change as your site is updated.
                                        </p>
                                    </div>

                                    @if (in_array($grade, ['A+', 'A']))
                                        <div class="alert alert-primary d-block text-start mb-3">
                                            <p class="mb-0">
                                                🌟 This site achieved a <strong>{{ $grade }}</strong> in the metadata audit,
                                                demonstrating <u>excellent search engine optimization</u>.<br>
                                                It is optimized for both <strong>search visibility</strong> and <strong>social sharing</strong>.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Metadata details -->
                                    @if ($metadata)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h4 class="mb-3">Metadata Details</h4>
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
                                                            <h5 class="card-title">Hreflang Settings</h5>
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

                                    <!-- Extra info -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 Why Metadata Matters</strong><br>
                                        - SEO: Proper metadata directly influences visibility and ranking.<br>
                                        - Social Sharing: Open Graph & Twitter Cards determine preview quality on social platforms.<br>
                                        - UX: Clear titles and descriptions improve click-through rate (CTR).<br>
                                        - Canonicalization: Canonical URLs prevent duplicate-content penalties.
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <p class="mb-2"><strong>Title Tag:</strong> Page title shown in SERPs and browser tabs (Optimal: 50–60 chars)</p>
                                        <p class="mb-2"><strong>Meta Description:</strong> Snippet shown in SERPs (Optimal: 120–160 chars)</p>
                                        <p class="mb-2"><strong>Open Graph:</strong> Optimizes link previews on Facebook, LinkedIn, etc.</p>
                                        <p class="mb-2"><strong>Twitter Cards:</strong> Optimizes preview cards on Twitter/X</p>
                                        <p class="mb-2"><strong>Canonical URL:</strong> Declares the preferred URL to prevent duplication</p>
                                        <p class="mb-0"><strong>Hreflang Tags:</strong> Connects localized/translated pages</p>
                                    </div>
                                    
                                    <hr>
                                    <div class="text-center mt-5">
                                        <p class="fw-bold mb-1">
                                            ✔ This result has been verified through Web-PSQC’s Meta Inspector.
                                        </p>

                                        <small class="text-muted d-block mb-2">
                                            Web-PSQC provides metadata quality auditing aligned with international SEO standards.
                                            Certificates can be authenticated in real time via QR verification.
                                        </small>

                                        <div class="mt-3 mb-4">
                                            <small class="d-block">Issued Date:
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">Expiration Date:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                <h3>Metadata Completeness Audit Tool</h3>
                                <div class="text-muted small mt-1">
                                    We analyze the completeness of your page metadata using the <strong>Meta Inspector CLI</strong>.
                                    <br><br>
                                    <strong>📊 Tools & Method:</strong><br>
                                    • Node.js headless browser engine renders the actual page<br>
                                    • HTML parsing to extract and analyze meta tags<br>
                                    • Scored against SEO best practices (out of 100)<br><br>
                                    
                                    <strong>🎯 Objectives:</strong><br>
                                    • Evaluate SEO-ready metadata quality<br>
                                    • Check preview quality for social sharing<br>
                                    • Verify canonical configuration to prevent duplication<br>
                                    • Confirm hreflang setup for multilingual support
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
                                                <td>95–100</td>
                                                <td>Optimal title length (50–60), optimal description length (120–160)<br>
                                                    Complete Open Graph, complete Twitter Cards<br>
                                                    Accurate canonical URL, all metadata optimized</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                <td>85–94</td>
                                                <td>Acceptable title/description ranges (30–80 / 80–200)<br>
                                                    Complete Open Graph, accurate canonical<br>
                                                    Twitter Cards optional</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                <td>75–84</td>
                                                <td>Basic title/description present<br>
                                                    Open Graph basic tags configured<br>
                                                    Some metadata omissions allowed</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                <td>65–74</td>
                                                <td>Poor title/description lengths<br>
                                                    Incomplete Open Graph (key tags missing)<br>
                                                    Canonical inaccurate or missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                <td>50–64</td>
                                                <td>Severely suboptimal title/description<br>
                                                    Insufficient Open Graph basics<br>
                                                    Basic metadata missing</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                <td>0–49</td>
                                                <td>No title/description<br>
                                                    No Open Graph<br>
                                                    Metadata largely unimplemented</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary d-block">
                                    <strong>📌 SEO Metadata Checklist</strong><br>
                                    - <strong>Title Tag:</strong> 50–60 chars, include core keyword and brand<br>
                                    - <strong>Meta Description:</strong> 120–160 chars, include a call to action<br>
                                    - <strong>Open Graph:</strong> Required 4: <code>og:title</code>, <code>og:description</code>, <code>og:image</code>, <code>og:url</code><br>
                                    - <strong>Twitter Cards:</strong> Basics: <code>card</code>, <code>title</code>, <code>description</code><br>
                                    - <strong>Canonical URL:</strong> Prefer self-referencing canonical on every page<br>
                                    - <strong>Hreflang:</strong> For multilingual sites, include <code>x-default</code><br><br>

                                    <strong>🔍 Impact on Search Visibility</strong><br>
                                    • Optimized title/description → CTR up to ~30% lift<br>
                                    • Open Graph implemented → Social share rate up to ~40% increase<br>
                                    • Canonical set → 100% avoidance of duplicate-content penalties<br>
                                    • Overall metadata optimization → ~20–50% average search traffic lift
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
