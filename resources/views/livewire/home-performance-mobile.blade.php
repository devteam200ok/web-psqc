@section('title')
    <title>📱 Mobile Performance Test – Playwright · iPhone/Galaxy · 6 Devices | Web-PSQC</title>
    <meta name="description" content="Simulate 6 devices including iPhone SE/11/15 Pro, Galaxy S9+/S20 Ultra, Pixel 5 with Playwright. Comprehensively analyze median response time, JS runtime errors, and render width overflow to evaluate/certify mobile user experience up to A+ grade.">
    <meta name="keywords" content="mobile performance test, Playwright, iPhone test, Galaxy test, mobile web optimization, JS runtime errors, render width overflow, responsive test, mobile UX, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Mobile Performance Test – Playwright · iPhone/Galaxy · 6 Devices" />
    <meta property="og:description" content="Simulate real mobile devices and analyze median response time, JS errors, and render width overflow. Diagnose mobile performance and qualify for an A+ certificate." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC – Mobile Performance Test" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Mobile Performance Test – Playwright · iPhone/Galaxy · 6 Devices | Web-PSQC" />
    <meta name="twitter:description" content="Simulate 6 devices to assess mobile UX: median, JS errors, and render width overflow." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'DevTeam Co., Ltd.',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Mobile Performance Test – Playwright · iPhone/Galaxy · 6 Devices',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Playwright-based simulation of 6 mobile devices to analyze median response time, JS errors, and render width overflow for graded performance.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (shared component) --}}
    <x-test-shared.header title="Mobile Test" subtitle="Performance in mobile environments" :user-plan-usage="$userPlanUsage"
        :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL form --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- URL input form -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">Test URL</label>
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
                                                Running test...
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
                                                class="text-primary">Add Recurring Schedule</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Metrics: <strong>Median (repeat visit)</strong> · <strong>JS runtime errors (first/third‑party/unique)</strong> ·
                                        <strong>Render width overflow</strong>
                                    </small>
                                </div>
                            </div>
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

                    {{-- 메인 컨텐츠 카드 --}}
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
                                <!-- Test Info tab -->
                                <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                    id="tabs-information">

                                    <h3>What is the Mobile Performance Test?</h3>
                                    <div class="text-muted small mt-1 mb-4">
                                        Using <strong>Playwright</strong>, we simulate real mobile device environments to precisely measure your website’s mobile performance and stability.
                                    </div>

                                    <!-- 측정 개요 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📊 Measurement Overview</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Tool</strong>: Playwright (headless browser, CPU throttling ×4)</li>
                                            <li><strong>Runs</strong>: <strong>4 total</strong> per device → skip <strong>1 warm‑up</strong>; use <strong>median of 3</strong></li>
                                            <li><strong>Key metrics</strong>:
                                                <ul class="mt-1">
                                                    <li>Repeat‑visit <strong>median</strong> load time (ms)</li>
                                                    <li><strong>Long Tasks total</strong> — TBT‑like (sum over 50 ms)
                                                    </li>
                                                    <li><strong>JS runtime errors</strong> — grouped by first/third‑party</li>
                                                    <li><strong>Render width overflow</strong> — body exceeds viewport width</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- 대표 테스트 기기 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📱 Representative Test Devices (6)</h4>
                                        <div class="row small text-muted">
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>iOS</strong></div>
                                                <ul class="mb-2">
                                                    <li>iPhone SE (older · small viewport)</li>
                                                    <li>iPhone 11 (mid‑range · common resolution)</li>
                                                    <li>iPhone 15 Pro (latest · high performance)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>Android</strong></div>
                                                <ul class="mb-0">
                                                    <li>Galaxy S9+ (older)</li>
                                                    <li>Galaxy S20 Ultra (high resolution)</li>
                                                    <li>Pixel 5 (reference Android)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            Note: Uses Playwright’s built‑in device profiles; if unavailable, a close substitute is used.
                                        </div>
                                    </div>

                                    <!-- Playwright 소개 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎭 What is Playwright?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Developed by Microsoft</strong>: a modern web automation tool using real browser engines for accurate measurements.</li>
                                            <li><strong>Headless execution</strong>: runs without UI in the background — stable in server environments.</li>
                                            <li><strong>CPU throttling</strong>: limits CPU (×4) to simulate real mobile constraints.</li>
                                            <li><strong>Precise metrics</strong>: measures JS execution time, errors, and rendering performance.</li>
                                        </ul>
                                    </div>

                                    <!-- 왜 구형이 더 빠를 수 있나 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">❓ Why can older devices appear faster?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Lighter assets served</strong>: smaller viewport/resolution may receive lighter images/layouts.</li>
                                            <li><strong>Uniform CPU throttle</strong>: same ×4 throttle applied to all, so resource weight matters more than raw device power.</li>
                                            <li><strong>Conditional loading differences</strong>: ads/widgets/scripts may load differently by UA/media queries/breakpoints.</li>
                                        </ul>
                                    </div>

                                    <!-- 테스트의 의미 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎯 Why this test matters</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Perceived mobile rendering</strong>: focuses on repeat‑visit median and long tasks to capture JS/layout burden.</li>
                                            <li><strong>Runtime stability</strong>: separates JS errors by first/third‑party to pinpoint ownership of issues.</li>
                                            <li><strong>Responsive suitability</strong>: auto‑detects body overflow beyond viewport to catch missing mobile handling.</li>
                                            <li><strong>Repeatable</strong>: fixed devices/runs/throttle/waiting rules — ideal for regression comparison and goals.</li>
                                        </ul>
                                    </div>

                                    {{-- Grade criteria --}}
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Grade</th>
                                                    <th>Score</th>
                                                    <th>Performance criteria</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90–100</td>
                                                    <td>Median response time: <strong>≤ 800 ms</strong><br>JS runtime errors:
                                                        <strong>0</strong><br>Render width overflow: <strong>None</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80–89</td>
                                                    <td>Median response time: <strong>≤ 1200 ms</strong><br>JS runtime errors: <strong>≤
                                                            1</strong><br>Render width overflow: <strong>None</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70–79</td>
                                                    <td>Median response time: <strong>≤ 2000 ms</strong><br>JS runtime errors: <strong>≤
                                                            2</strong><br>Render width overflow: <strong>Allowed</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60–69</td>
                                                    <td>Median response time: <strong>≤ 3000 ms</strong><br>JS runtime errors: <strong>≤
                                                            3</strong><br>Render width overflow: <strong>Frequent</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50–59</td>
                                                    <td>Median response time: <strong>≤ 4000 ms</strong><br>JS runtime errors: <strong>≤
                                                            5</strong><br>Render width overflow: <strong>Severe</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0–49</td>
                                                    <td>Below the above criteria</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-info d-block mt-3">
                                        <div><strong>Summary</strong> — 6 devices · 4 runs each (1 warm‑up skipped) · CPU ×4 · Metrics: Median / Long Tasks / JS errors (first/third‑party) / Render width overflow</div>
                                        <div class="mt-1">Older devices reading faster can be normal (lighter assets + uniform throttle). This test aims to continuously track <strong>mobile rendering cost and stability</strong> under realistic conditions.
                                        </div>
                                    </div>
                                </div>

                                        <!-- Results tab -->
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
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

                                            $gradeMap = [
                                                'A+' => 'badge-a-plus',
                                                'A' => 'badge-a',
                                                'B' => 'badge-b',
                                                'C' => 'badge-c',
                                                'D' => 'badge-d',
                                                'F' => 'badge-f',
                                            ];
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 종합 결과 -->
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">Overall Results</h5>

                                                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                                    <div>Median average:
                                                        <strong>{{ $overall['medianAvgMs'] ?? 0 }}</strong>ms
                                                    </div>
                                                    <div>Long Tasks average:
                                                        <strong>{{ $overall['longTasksAvgMs'] ?? 0 }}</strong>ms
                                                    </div>
                                                    <div>JS errors (first‑party):
                                                        <strong>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }}</strong>
                                                    </div>
                                                    <div>JS errors (third‑party):
                                                        <strong>{{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</strong>
                                                    </div>
                                                    <div>Render width overflow:
                                                        <strong>{{ !empty($overall['bodyOverflowsViewport']) ? 'Yes' : 'No' }}</strong>
                                                    </div>
                                                </div>

                                                @if (!empty($overall['reason']))
                                                    @php
                                                        $reasonParts = explode(' / ', $overall['reason']);
                                                    @endphp
                                                    <div class="mt-3">
                                                        @foreach ($reasonParts as $part)
                                                            <div class="fw-bold text-dark mb-1">{{ trim($part) }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Per-device detailed results -->
                                        <div class="mb-4">
                                            <h5 class="mb-3">Per-device Details</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter table-nowrap">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Device</th>
                                                            <th>Median(ms)</th>
                                                            <th>TBT(LongTasks, ms)</th>
                                                            <th>JS (first)</th>
                                                            <th>JS (third)</th>
                                                            <th>JS (unique)</th>
                                                            <th>Overflow</th>
                                                            <th>Viewport</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($results as $result)
                                                            <tr class="device-row">
                                                                <td><strong>{{ $result['device'] ?? 'Unknown' }}</strong>
                                                                </td>
                                                                <td>{{ $result['medianMs'] ?? 0 }}</td>
                                                                <td>{{ $result['longTasksTotalMs'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsFirstPartyCount'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsThirdPartyCount'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsUniqueCount'] ?? 0 }}</td>
                                                                <td>{{ !empty($result['bodyOverflowsViewport']) ? 'Yes' : 'No' }}
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

                                        <!-- 등급 기준 안내 삭제됨 -->

                                        <!-- 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>Metric descriptions</h6>
                                            <p class="mb-2"><strong>Median response time:</strong> the median page load time on repeat visit.</p>
                                            <p class="mb-2"><strong>TBT (Long Tasks):</strong> total main‑thread blocking time from JS execution.</p>
                                            <p class="mb-2"><strong>JS errors:</strong> first‑party vs third‑party JavaScript runtime errors.</p>
                                            <p class="mb-0"><strong>Render width overflow:</strong> whether the page body exceeds the mobile viewport width.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a test to view mobile performance results.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Data tab -->
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
                                            <h5>No data yet</h5>
                                            <p class="mb-0">Run a test to view the raw JSON data.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 사이드바 -->
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
@endsection
