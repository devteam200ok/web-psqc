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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(Google Lighthouse 품질 테스트)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(웹 접근성 검사)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(브라우저 호환성 테스트)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(반응형 UI 적합성 테스트)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(링크 검증 테스트)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(구조화 데이터 검증)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(검색엔진 크롤링 검사)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
                                                PSQC 종합 인증서 - 세부 검사내역
                                            </h1>
                                            <h2>(메타데이터 완성도 검사)</h2>
                                            <h3>인증번호: {{ $certification->code }}</h3>
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
                                                {{ $certification->issued_at->format('Y-m-d') }}</small>
                                            <small class="d-block">만료일:
                                                {{ $certification->expires_at->format('Y-m-d') }}</small>
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
