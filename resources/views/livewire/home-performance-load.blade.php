@section('title')
    <title>🚀 K6 Load Test – Validate Web Performance & Stability | Web-PSQC</title>
    <meta name="description"
        content="Simulate real traffic with K6 by configuring concurrent users (VUs), duration, and think time. Evaluate performance and stability via P95 response time, error rate, and stability — and issue a certificate.">
    <meta name="keywords" content="K6 load testing, website performance test, VU, P95 response time, error rate, concurrency, load handling, stability validation, performance certificate, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="K6 Load Test – Validate Web Performance & Stability" />
    <meta property="og:description"
        content="Reproduce real traffic with K6 to measure P95 response time, error rate, and stability. Issue an A+ grade certificate when conditions are met." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC – K6 Load Test" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="K6 Load Test – Validate Web Performance & Stability | Web-PSQC" />
    <meta name="twitter:description" content="Simulate usage with VUs, duration, and think time; evaluate P95 and error rate for stability." />
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
    'name' => 'K6 Load Test – Web Performance & Stability',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Simulate concurrent users with K6 and evaluate web performance via P95, error rate, and stability.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (shared component) --}}
    <x-test-shared.header title="🚀 K6 Load Test" subtitle="Validate website performance and stability" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL form and settings --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-info d-block mb-4">
                                    <h5>🔐 Sign‑in Required</h5>
                                    <p class="mb-2">Load testing requires domain ownership verification.</p>
                                    <p class="mb-0">Sign in, then register and verify your domain in the “Domains” tab in the sidebar.</p>
                                </div>
                            @endif

                            <div class="alert alert-warning d-block alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        ⚠️ <strong>When Cloudflare Proxy is enabled</strong>, load test results may appear abnormally slow.<br>
                                        For accurate testing, set your domain’s DNS record to <strong>“DNS only”</strong> (grey cloud icon).
                                    </div>
                                </div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>

                            <!-- URL input -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">Test URL</label>
                                    <div class="input-group">
                                        <input type="url" wire:model="url" wire:keydown.enter="runTest"
                                            class="form-control @error('url') is-invalid @enderror"
                                            placeholder="https://www.example.com"
                                            @if ($isLoading || !Auth::check()) disabled @endif>
                                        <button wire:click="runTest" class="btn btn-primary"
                                            @if ($isLoading || !Auth::check()) disabled @endif>
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
                                    @if (Auth::check())
                                        <div class="form-text">Only domains with verified ownership can be tested.</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Test settings -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Virtual Users (VUs)</label>
                                    <input type="number" wire:model.live="vus"
                                        class="form-control @error('vus') is-invalid @enderror" min="10"
                                        max="100" @if ($isLoading || !Auth::check()) disabled @endif>
                                    @error('vus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Concurrent users (10–100)</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Duration (seconds)</label>
                                    <input type="number" wire:model.live="duration_seconds"
                                        class="form-control @error('duration_seconds') is-invalid @enderror"
                                        min="30" max="100" @if ($isLoading || !Auth::check()) disabled @endif>
                                    @error('duration_seconds')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Test duration (30–100 seconds)</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Target Grade</label>
                                    <div class="form-control-plaintext">
                                        <span
                                            class="badge badge-{{ strtolower($maxGrade) === 'a+' ? 'a-plus' : strtolower($maxGrade) }}">
                                            Up to {{ $maxGrade }} ({{ $maxScore }} points)
                                        </span>
                                    </div>
                                    <div class="form-text">Based on current settings</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Think Time: {{ $think_time_min }}–{{ $think_time_max }} s (fixed)
                                    </small>

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
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- 테스트 예약 폼 (공통 컴포넌트) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- 스케줄 등록 폼 (공통 컴포넌트) --}}
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

                                    <h3>What is a K6 Load Test?</h3>
                                    <div class="text-muted small mt-1 mb-4">
                                        <strong>K6</strong> is a modern load testing tool by Grafana. You write test scenarios in JavaScript to validate the performance and stability of websites and APIs.<br><br>

                                        <strong>🔧 Key concepts:</strong><br>
                                        • <strong>Virtual Users (VUs)</strong>: number of concurrent virtual users<br>
                                        • <strong>Duration</strong>: how long the test runs<br>
                                        • <strong>Think Time</strong>: wait time between requests (simulates real user behavior)<br>
                                        • <strong>P95 response time</strong>: time under which 95% of requests complete<br><br>

                                        <strong>📊 Why P95 matters:</strong><br>
                                        Averages can be skewed by a few very fast requests. P95 reflects what most users (95%) actually experience, so it’s more realistic.<br><br>

                                        <strong>🎯 Role of Think Time:</strong><br>
                                        Real users pause to read or decide the next action. Think time produces more realistic load patterns.
                                    </div>

                                    {{-- 등급 기준 안내 --}}
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Grade</th>
                                                    <th>VU/Duration conditions</th>
                                                    <th>Performance criteria</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>≥ 100 VUs + ≥ 60 s</td>
                                                    <td>P95 < 1000 ms<br>Error rate < 0.1%<br>Stability: P90 ≤ 200% of average</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>≥ 50 VUs + ≥ 45 s</td>
                                                    <td>P95 < 1200 ms<br>Error rate < 0.5%<br>Stability: P90 ≤ 240% of average</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>≥ 30 VUs + ≥ 30 s</td>
                                                    <td>P95 < 1500 ms<br>Error rate < 1.0%<br>Stability: P90 ≤ 280% of average</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>≥ 20 VUs + ≥ 30 s</td>
                                                    <td>P95 < 2000 ms<br>Error rate < 2.0%<br>Stability: P90 ≤ 320% of average</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>≥ 10 VUs + ≥ 30 s</td>
                                                    <td>P95 < 3000 ms<br>Error rate < 5.0%<br>Stability: P90 ≤ 400% of average</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>-</td>
                                                    <td>Below the above criteria</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-warning d-block mt-3">
                                        <strong>📋 Certificate issuance requirements:</strong><br>
                                        • At least <strong>30 VUs</strong> + <strong>30 s</strong><br>
                                        • Achieve grade <strong>B</strong> or higher<br>
                                        • Sign‑in and domain ownership verification required<br><br>

                                        <strong>🔐 How to verify domain ownership:</strong><br>
                                        1) Register your domain in the “Domains” tab in the sidebar<br>
                                        2) Verify via TXT record or file upload<br>
                                        3) Run the load test once verification is complete
                                    </div>
                                </div>

                                <!-- Results tab -->
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
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
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 주요 메트릭 카드들 -->
                                        <div class="row mb-4">
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($totalRequests) }}</h3>
                                                        <div class="text-muted">Total Requests</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($requestsPerSec, 1) }}
                                                        </h3>
                                                        <div class="text-muted">Req/sec</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($p95Response) }}ms</h3>
                                                        <div class="text-muted">P95 Response</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3
                                                            class="mb-1 {{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($failureRate, 2) }}%
                                                        </h3>
                                                        <div class="text-muted">Failure Rate</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detailed results table -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h5>Test Settings</h5>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>Virtual Users</th>
                                                        <td>{{ $config['vus'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Duration</th>
                                                        <td>{{ $config['duration_seconds'] ?? 'N/A' }} s</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Think Time</th>
                                                        <td>{{ $config['think_time_min'] ?? 3 }}–{{ $config['think_time_max'] ?? 10 }} s
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Region</th>
                                                        <td>{{ ucfirst($config['region'] ?? 'seoul') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Response Time Analysis</h5>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>Average</th>
                                                        <td>{{ number_format($metrics['http_req_duration_avg'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Median</th>
                                                        <td>{{ number_format($metrics['http_req_duration_med'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>P90</th>
                                                        <td>{{ number_format($metrics['http_req_duration_p90'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>P95</th>
                                                        <td>{{ number_format($metrics['http_req_duration_p95'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Max</th>
                                                        <td>{{ number_format($metrics['http_req_duration_max'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Stability (P90/Avg)</th>
                                                        <td>
                                                            @php
                                                                $avgTime = $metrics['http_req_duration_avg'] ?? 1;
                                                                $p90Time = $metrics['http_req_duration_p90'] ?? 0;
                                                                $stabilityRatio =
                                                                    $avgTime > 0 ? $p90Time / $avgTime : 0;
                                                            @endphp
                                                            {{ number_format($stabilityRatio, 2) }}
                                                            ({{ number_format($stabilityRatio * 100, 1) }}%)
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Check results -->
                                        @if (isset($metrics['checks_passes']) || isset($metrics['checks_fails']))
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h5>Check Results</h5>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Pass</th>
                                                            <td class="text-success">
                                                                {{ $metrics['checks_passes'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Fail</th>
                                                            <td class="text-danger">
                                                                {{ $metrics['checks_fails'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Success Rate</th>
                                                            <td>
                                                                @php
                                                                    $passes = $metrics['checks_passes'] ?? 0;
                                                                    $fails = $metrics['checks_fails'] ?? 0;
                                                                    $total = $passes + $fails;
                                                                    $rate =
                                                                        $total > 0
                                                                            ? round(($passes / $total) * 100, 2)
                                                                            : 0;
                                                                @endphp
                                                                {{ $rate }}%
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Data Transfer</h5>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Data Received</th>
                                                            <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }}
                                                                MB</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Data Sent</th>
                                                            <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }}
                                                                MB</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Iterations</th>
                                                            <td>{{ $metrics['iterations'] ?? 0 }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="alert alert-info d-block">
                                            <h6>How to read results</h6>
                                            <p class="mb-2"><strong>P95 response time:</strong> 95% of requests completed within this time. A key UX indicator.</p>
                                            <p class="mb-2"><strong>Error rate:</strong> Share of failed requests. Preferably below 1%.</p>
                                            <p class="mb-2"><strong>Think time:</strong> Simulates realistic user behavior between page actions.</p>
                                            <p class="mb-0"><strong>Stability:</strong> Ratio of P90 to average, indicating consistency; lower is better.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a test to see the load test results.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}" id="tabs-data">
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
