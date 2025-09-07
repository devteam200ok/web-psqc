@section('title')
    <title>🕷️ Site Crawling Test - robots.txt · sitemap.xml SEO Technical Audit - Web-PSQC</title>
    <meta name="description"
        content="Analyze robots.txt and sitemap.xml configuration to optimize search engine crawling. Comprehensive evaluation of page accessibility, duplicate content, and SEO technical elements to improve site search visibility quality.">
    <meta name="keywords"
        content="site crawling test, robots.txt analysis, sitemap.xml validation, SEO technical audit, crawling optimization, duplicate content analysis, page quality assessment, search engine optimization, Web-PSQC">
    <meta name="author" content="Web-PSQC Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="🕷️ Site Crawling Test - robots.txt · sitemap.xml SEO Technical Audit - Web-PSQC" />
    <meta property="og:description"
        content="Analyze robots.txt and sitemap.xml files to verify search engine crawling compliance and comprehensively diagnose overall page quality and SEO optimization status." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC Site Crawling Test Results" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="🕷️ Site Crawling Test - robots.txt · sitemap.xml SEO Technical Audit" />
    <meta name="twitter:description"
        content="Verify robots.txt and sitemap.xml configuration to evaluate site crawling optimization and SEO quality. Get A+ grade certificates with Web-PSQC." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Site Crawling Test - robots.txt · sitemap.xml SEO Technical Audit',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url' => url('/'),
    ],
    'description' => 'Check robots.txt and sitemap.xml configuration to analyze crawling optimization and SEO quality, providing improvement recommendations.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (Shared Component) --}}
    <x-test-shared.header title="🕷️ Site Crawling Test" subtitle="SEO Technical Audit based on robots.txt/sitemap.xml" :user-plan-usage="$userPlanUsage"
        :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL Form (Individual Component) --}}
                    <div class="card mb-3">
                        <div class="card-body">
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
                                                class="text-primary">Register Schedule</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- Schedule Test Form (Shared Component) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- Recurring Schedule Form (Shared Component) --}}
                        <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                            :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                    @endif

                    {{-- Test Status (Shared Component) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- Individual Test Specific Content --}}
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
                                    <h3>Search Engine Crawling Compliance and Page Quality Comprehensive Analysis</h3>
                                    <div class="text-muted small mt-1">
                                        Analyze your website's robots.txt and sitemap.xml to verify SEO compliance and 
                                        comprehensively evaluate the accessibility and quality of pages registered in the sitemap.
                                        <br><br>
                                        <strong>📋 Test Process:</strong><br>
                                        1. Check robots.txt file existence and rules<br>
                                        2. Search sitemap.xml files and collect URLs<br>
                                        3. Filter crawling-allowed URLs according to robots.txt rules<br>
                                        4. Sample up to 50 pages and test sequentially<br>
                                        5. Measure HTTP status, metadata, and quality score for each page<br>
                                        6. Analyze duplicate content (title/description) ratio<br><br>

                                        <strong>🎯 Measurement Tools:</strong><br>
                                        • Custom Node.js-based crawler (robots.txt compliant)<br>
                                        • sitemap.xml parser (supports recursive index file processing)<br>
                                        • HTML parser for metadata extraction<br>
                                        • Quality scoring algorithm (100-point scale)<br><br>

                                        <strong>💯 Quality Score Calculation Criteria:</strong><br>
                                        • Title tag length (under 5 characters: -15 points)<br>
                                        • Description meta tag (under 20 characters: -10 points)<br>
                                        • Missing canonical URL (-5 points)<br>
                                        • Missing H1 tag (-10 points) / Excessive use (-5 points)<br>
                                        • Insufficient content (under 1000 characters: -10 points)<br><br>

                                        <strong>🚀 Test Purpose:</strong><br>
                                        • Verify that search engines can properly crawl your site<br>
                                        • Validate that all pages registered in sitemap are normally accessible<br>
                                        • Diagnose SEO penalty risks from duplicate content<br>
                                        • Derive improvement points through page-by-page quality scores<br><br>

                                        This test takes approximately <strong>30 seconds to 2 minutes</strong>.
                                    </div>
                                    {{-- Grade Criteria Guide --}}
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
                                                    <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                    <td>90~100</td>
                                                    <td>robots.txt properly applied<br>
                                                        sitemap.xml exists with no missing/404 errors<br>
                                                        All test pages return 2xx status<br>
                                                        Overall page quality average ≥ 85 points<br>
                                                        Duplicate content ≤ 30%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>80~89</td>
                                                    <td>robots.txt properly applied<br>
                                                        sitemap.xml exists with integrity maintained<br>
                                                        All test pages return 2xx status<br>
                                                        Overall page quality average ≥ 85 points</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>70~79</td>
                                                    <td>robots.txt and sitemap.xml exist<br>
                                                        All test pages return 2xx status<br>
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
                                                        Test URL list can be generated<br>
                                                        However, low normal access rate or quality check impossible</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~34</td>
                                                    <td>Missing robots.txt or sitemap.xml<br>
                                                        Cannot generate test list</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $report = $currentTest->results;
                                            $grade = $report['overall']['grade'] ?? 'F';
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
                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- Overall Status -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title mb-3">Overall Status</h5>
                                                        <div class="row g-3">
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div class="h4 mb-0">{{ $pages['count'] ?? 0 }}
                                                                    </div>
                                                                    <div class="small text-muted">Tested Pages</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div class="h4 mb-0">
                                                                        {{ number_format($pages['qualityAvg'] ?? 0, 1) }}
                                                                    </div>
                                                                    <div class="small text-muted">Avg Quality Score</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="h4 mb-0 {{ ($pages['errorRate4xx5xx'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                                        {{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%
                                                                    </div>
                                                                    <div class="small text-muted">Error Rate</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="h4 mb-0 {{ ($pages['duplicateRate'] ?? 0) > 30 ? 'text-warning' : '' }}">
                                                                        {{ number_format($pages['duplicateRate'] ?? 0, 1) }}%
                                                                    </div>
                                                                    <div class="small text-muted">Duplicate Rate</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>Grade Reason:</strong>
                                                            {{ $report['overall']['reason'] ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- robots.txt & sitemap.xml -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">robots.txt</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Status:</span>
                                                            <span
                                                                class="{{ $robots['exists'] ?? false ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                {{ $robots['exists'] ?? false ? 'Exists' : 'Missing' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>HTTP Status:</span>
                                                            <span>{{ $robots['status'] ?? 0 }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">sitemap.xml</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Status:</span>
                                                            <span
                                                                class="{{ $sitemap['hasSitemap'] ?? false ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                {{ $sitemap['hasSitemap'] ?? false ? 'Exists' : 'Missing' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Total URLs:</span>
                                                            <span>{{ $sitemap['sitemapUrlCount'] ?? 0 }} URLs</span>
                                                        </div>

                                                        @if (!empty($sitemap['sitemaps']))
                                                            <div class="mt-3">
                                                                <div class="small text-muted mb-2">Sitemap Files:</div>
                                                                @foreach ($sitemap['sitemaps'] as $s)
                                                                    <div class="small d-flex justify-content-between">
                                                                        <span class="text-truncate me-2"
                                                                            style="max-width:70%">
                                                                            {{ basename($s['url']) }}
                                                                        </span>
                                                                        <span
                                                                            class="{{ $s['ok'] ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                            {{ $s['ok'] ? 'OK' : 'NG' }}
                                                                            ({{ $s['count'] ?? 0 }})
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Crawling Plan -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Crawling Plan</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="small text-muted mb-2">Target URLs for Testing (Total
                                                            {{ $crawlPlan['candidateCount'] ?? 0 }} URLs)</div>
                                                        @if (!empty($crawlPlan['sample']))
                                                            <div
                                                                style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem; background: #f8f9fa;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($crawlPlan['sample'] as $url)
                                                                        <li class="text-break mb-1">•
                                                                            {{ $url }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="small text-muted mb-2">Excluded URLs
                                                            ({{ count($crawlPlan['skipped'] ?? []) }} URLs)</div>
                                                        @if (!empty($crawlPlan['skipped']))
                                                            <div
                                                                style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem; background: #f8f9fa;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach (array_slice($crawlPlan['skipped'], 0, 10) as $skip)
                                                                        <li class="mb-2">
                                                                            <div class="fw-bold text-danger">
                                                                                {{ $skip['reason'] }}</div>
                                                                            <div class="text-break small">
                                                                                {{ $skip['url'] }}</div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <div class="text-muted small">No excluded URLs ✓</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Issue Samples -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Error Pages (4xx/5xx)</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @php $errorPages = $report['samples']['errorPages'] ?? []; @endphp
                                                        @if (empty($errorPages))
                                                            <div class="text-muted">No error pages ✓</div>
                                                        @else
                                                            <div style="max-height: 200px; overflow-y: auto;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($errorPages as $page)
                                                                        <li class="mb-2">
                                                                            <span
                                                                                class="badge bg-red-lt text-red-lt-fg me-1">{{ $page['status'] }}</span>
                                                                            <span
                                                                                class="text-break">{{ $page['url'] }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Low Quality Pages (Under 50 points)</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @php
                                                            $lowQuality = collect(
                                                                $report['samples']['lowQuality'] ?? [],
                                                            )
                                                                ->filter(function ($page) {
                                                                    return ($page['score'] ?? 100) < 50;
                                                                })
                                                                ->take(10)
                                                                ->values()
                                                                ->toArray();
                                                        @endphp
                                                        @if (empty($lowQuality))
                                                            <div class="text-muted">No pages under 50 points ✓</div>
                                                        @else
                                                            <div style="max-height: 200px; overflow-y: auto;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($lowQuality as $page)
                                                                        <li class="mb-2">
                                                                            <span
                                                                                class="badge bg-orange-lt text-orange-lt-fg me-1">{{ $page['score'] ?? 0 }} pts</span>
                                                                            <span
                                                                                class="text-break">{{ $page['url'] }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Duplicate Content Information -->
                                        @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Duplicate Content</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0 text-warning">
                                                                    {{ $pages['dupTitleCount'] ?? 0 }}</div>
                                                                <div class="small text-muted">Duplicate Title Pages</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0 text-warning">
                                                                    {{ $pages['dupDescCount'] ?? 0 }}</div>
                                                                <div class="small text-muted">Duplicate Description Pages</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Measurement Metrics Explanation -->
                                        <div class="alert alert-info d-block">
                                            <h6>📊 Measurement Metrics Explanation</h6>
                                            <p class="mb-2"><strong>Tested Pages:</strong> Number of pages actually tested from URLs collected from sitemap.xml and allowed by robots.txt</p>
                                            <p class="mb-2"><strong>Avg Quality Score:</strong> Average of comprehensive evaluation scores for each page's SEO quality elements (title, description, canonical, H1, content volume)</p>
                                            <p class="mb-2"><strong>Error Rate:</strong> Percentage of pages with 4xx, 5xx errors among tested pages</p>
                                            <p class="mb-0"><strong>Duplicate Rate:</strong> Percentage of pages using identical title or description</p>
                                        </div>

                                        <!-- Improvement Recommendations -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 Improvement Recommendations</h6>
                                            @if (!($robots['exists'] ?? false))
                                                <p class="mb-2">⚠️ <strong>Create robots.txt file:</strong> Create a robots.txt file in the root directory to specify crawling rules.</p>
                                            @endif
                                            @if (!($sitemap['hasSitemap'] ?? false))
                                                <p class="mb-2">⚠️ <strong>Create sitemap.xml file:</strong> Generate a sitemap.xml including all important pages of your site.</p>
                                            @endif
                                            @if (($pages['errorRate4xx5xx'] ?? 0) > 0)
                                                <p class="mb-2">⚠️ <strong>Fix error pages:</strong> Fix pages generating 404, 500 errors or remove them from the sitemap.</p>
                                            @endif
                                            @if (($pages['duplicateRate'] ?? 0) > 30)
                                                <p class="mb-2">⚠️ <strong>Improve duplicate content:</strong> Write unique titles and descriptions for each page to enhance SEO effectiveness.</p>
                                            @endif
                                            @if (($pages['qualityAvg'] ?? 0) < 70)
                                                <p class="mb-2">⚠️ <strong>Improve page quality:</strong> Enhance quality scores through meta tag optimization, H1 tag usage, and writing sufficient content.</p>
                                            @endif
                                            @if ($grade === 'A+')
                                                <p class="mb-0">✅ <strong>Optimization Complete:</strong> Your current crawling optimization status is excellent. Maintain quality through continuous monitoring.</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a test to see crawling test results.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                    id="tabs-data">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Raw JSON Data</h5>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="copyJsonToClipboard()" title="Copy JSON Data">
                                                Copy
                                            </button>
                                        </div>
                                        <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a test to see Raw JSON data.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mb-2">
                    {{-- Sidebar (Shared Component) --}}
                    <x-test-shared.sidebar :side-tab-active="$sideTabActive" :test-history="$testHistory" :selected-history-test="$selectedHistoryTest" :user-domains="$userDomains"
                        :scheduled-tests="$scheduledTests" :has-pro-or-agency-plan="$hasProOrAgencyPlan" />

                    {{-- Domain Verification Modal (Shared Component) --}}
                    <x-test-shared.domain-verification-modal :show-verification-modal="$showVerificationModal" :current-verification-domain="$currentVerificationDomain" :verification-message="$verificationMessage"
                        :verification-message-type="$verificationMessageType" />
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
    @include('components.test-shared.js')
@endsection