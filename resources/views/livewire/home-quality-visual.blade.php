@section('title')
    <title>📱 Responsive UI Test - Viewport Rendering Width Overflow Detection - Web-PSQC</title>
    <meta name="description"
        content="Automatically test your website's responsive UI across 9 major viewports including mobile, tablet, and desktop. Measure body rendering width overflow beyond viewport to diagnose horizontal scrolling issues and provide improvement guidelines.">
    <meta name="keywords"
        content="responsive UI test, viewport compatibility check, mobile optimization, horizontal scroll prevention, responsive web design, UI overflow diagnosis, cross-device testing, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="📱 Responsive UI Test - Viewport Rendering Width Overflow Detection - Web-PSQC" />
    <meta property="og:description"
        content="Check responsive UI compatibility across 9 major viewports, detect horizontal scrolling issues in advance, and receive quality certificates up to A+ grade." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC Responsive UI Test Results" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="📱 Responsive UI Test - Viewport Rendering Width Overflow Detection" />
    <meta name="twitter:description"
        content="Precisely test responsive UI across 9 viewports including mobile, tablet, and desktop to diagnose horizontal scrolling issues. Get your A+ grade certificate with Web-PSQC." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Responsive UI Test - Viewport Rendering Width Overflow Detection',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url' => url('/'),
    ],
    'description' => 'Automatically test responsive UI compatibility across 9 major viewports including mobile, tablet, and desktop to diagnose horizontal scrolling issues and provide improvement recommendations.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (Common Component) --}}
    <x-test-shared.header title="Responsive UI" subtitle="Viewport-based testing" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

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
                                                class="text-primary">Recurring Schedule</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- Schedule Test Form (Common Component) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- Recurring Schedule Form (Common Component) --}}
                        <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                            :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                    @endif

                    {{-- Test Status (Common Component) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- Individual Test Unique Content --}}
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
                                    <h3>Playwright-based Responsive UI Compatibility Testing</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>Testing Tool:</strong> Playwright (Chromium Engine)<br>
                                        <strong>Test Purpose:</strong> Verify that web pages render correctly within viewport boundaries across various device environments<br>
                                        <strong>Test Coverage:</strong> 9 major viewports (3 mobile, 1 foldable, 3 tablet, 2 desktop)<br><br>

                                        <strong>Measurement Items:</strong><br>
                                        • Actual rendering width of body element<br>
                                        • Overflow pixels beyond viewport width<br>
                                        • Overflow occurrence per viewport<br><br>

                                        <strong>Testing Method:</strong><br>
                                        1. Set browser to each viewport size<br>
                                        2. Wait for network stabilization after page load (6 seconds)<br>
                                        3. Measure document.body.getBoundingClientRect()<br>
                                        4. Calculate overflow pixels by comparing with viewport width<br><br>

                                        <strong>Tested Viewports:</strong><br>
                                        • Mobile: 360×800, 390×844, 414×896<br>
                                        • Foldable: 672×960<br>
                                        • Tablet: 768×1024, 834×1112, 1024×1366<br>
                                        • Desktop: 1280×800, 1440×900<br><br>

                                        This test takes approximately <strong>30 seconds to 1 minute</strong> and helps identify
                                        potential horizontal scrollbar issues to improve user experience.
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
                                                    <td>100</td>
                                                    <td>0 overflows across all viewports<br>Body render width always within viewport</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>90~95</td>
                                                    <td>≤1 overflow with ≤8px<br>0 overflows in narrow mobile (≤390px) range</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>80~89</td>
                                                    <td>≤2 overflows with each ≤16px<br>Or ≤8px single overflow in narrow mobile</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>70~79</td>
                                                    <td>≤4 overflows or single overflow 17~32px</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>50~69</td>
                                                    <td>>4 overflows or single overflow 33~64px</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~49</td>
                                                    <td>Measurement failure or ≥65px overflow</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $results = $currentTest->results;
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

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- Overall Results -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Overall Results</h5>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">Overflow Count</div>
                                                                <div class="h3">{{ $overflowCount }} cases</div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">Maximum Overflow Pixels</div>
                                                                <div class="h3">{{ $maxOverflowPx }}px</div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">Assessment Reason</div>
                                                                <div class="small mt-1">{{ $reason }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detailed Results by Viewport -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Detailed Results by Viewport</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Viewport</th>
                                                                <th>Size</th>
                                                                <th>Overflow Status</th>
                                                                <th>Overflow Pixels</th>
                                                                <th>Viewport Width</th>
                                                                <th>Body Render Width</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($perViewport as $vp)
                                                                @php
                                                                    $hasOverflow = $vp['overflow'] ?? false;
                                                                    $overflowPx = $vp['overflowPx'] ?? 0;
                                                                    $hasError = !empty($vp['navError']);
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ str_replace('-', ' ', explode('-', $vp['viewport'])[0] ?? '') }}
                                                                    </td>
                                                                    <td>{{ $vp['w'] ?? 0 }}×{{ $vp['h'] ?? 0 }}</td>
                                                                    <td>
                                                                        @if ($hasError)
                                                                            <span class="badge bg-secondary">Error</span>
                                                                        @elseif ($hasOverflow)
                                                                            <span
                                                                                class="badge bg-red-lt text-red-lt-fg">Overflow</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-green-lt text-green-lt-fg">Normal</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($overflowPx > 0)
                                                                            <strong
                                                                                class="text-danger">{{ $overflowPx }}px</strong>
                                                                        @else
                                                                            <span class="text-muted">0px</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $vp['viewportWidth'] ?? ($vp['w'] ?? 0) }}px
                                                                    </td>
                                                                    <td>{{ $vp['bodyRenderWidth'] ?? 0 }}px</td>
                                                                    <td>
                                                                        @if ($hasError)
                                                                            <small
                                                                                class="text-danger">{{ $vp['navError'] }}</small>
                                                                        @else
                                                                            <span class="text-muted">Normal Measurement</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Measurement Metrics Explanation -->
                                        <div class="alert alert-info d-block">
                                            <strong>💡 Measurement Metrics Explanation</strong><br>
                                            <strong>Viewport Width:</strong> Actual display area width of browser window (window.innerWidth)<br>
                                            <strong>Body Render Width:</strong> Actual rendered width of body element
                                            (getBoundingClientRect().width)<br>
                                            <strong>Overflow Pixels:</strong> Number of pixels by which Body Render Width exceeds Viewport Width<br><br>

                                            When overflow occurs, users must horizontally scroll to view content, which significantly degrades mobile usability.
                                        </div>

                                        <!-- Improvement Recommendations -->
                                        @if ($overflowCount > 0)
                                            <div class="alert alert-info d-block">
                                                <strong>🔧 Improvement Recommendations</strong><br>
                                                @if ($maxOverflowPx > 50)
                                                    • Check fixed-width elements: Use % or vw units instead of fixed px values in width properties<br>
                                                    • Image optimization: Apply max-width: 100% and height: auto<br>
                                                    • Responsive table handling: Use overflow-x: auto or responsive table components<br>
                                                @elseif ($maxOverflowPx > 20)
                                                    • Check padding/margin: Verify box-sizing: border-box is applied<br>
                                                    • Long text handling: Use word-break: break-word or overflow-wrap properties<br>
                                                    • Form element width: Apply width: 100% to input and textarea elements<br>
                                                @else
                                                    • Fine-tuning: Check if specific element padding or borders exceed container boundaries<br>
                                                    • Third-party widgets: Review styles of elements injected by external scripts<br>
                                                @endif
                                                <br>
                                                <strong>Debugging Tip:</strong> Apply * { outline: 1px solid red; } in developer tools to identify overflowing elements
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No Results Yet</h5>
                                            <p class="mb-0">Run a test to see responsive UI compatibility results by viewport.</p>
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
                                            <h5>No Results Yet</h5>
                                            <p class="mb-0">Run a test to view the raw JSON data.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 mb-2">
                    {{-- Sidebar (Common Component) --}}
                    <x-test-shared.sidebar :side-tab-active="$sideTabActive" :test-history="$testHistory" :selected-history-test="$selectedHistoryTest" :user-domains="$userDomains"
                        :scheduled-tests="$scheduledTests" :has-pro-or-agency-plan="$hasProOrAgencyPlan" />

                    {{-- Domain Verification Modal (Common Component) --}}
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
