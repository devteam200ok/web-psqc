@section('title')
    <title>🔗 Link Validation Test - Broken Links · Redirect Chains · Anchor Validity Analysis - Web-PSQC</title>
    <meta name="description"
        content="Crawl all internal, external, and image links on your website to detect broken links and errors. Analyze 404/500 status codes, redirect chains, and anchor validity to evaluate web quality and user experience.">
    <meta name="keywords"
        content="link validation, Broken Link Checker, broken link detection, 404 error check, anchor link validity, redirect chain analysis, HTTP status code inspection, website quality evaluation, Web-PSQC">
    <meta name="author" content="Web-PSQC Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="🔗 Link Validation Test - Broken Links · Redirect Chains · Anchor Validity Analysis - Web-PSQC" />
    <meta property="og:description"
        content="Check internal/external/image link status to find broken links, analyze redirect chains and anchor validity to evaluate site quality. Error rate-based grading and A+ certificate issuance support." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC Link Validation Results" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="🔗 Link Validation Test - Broken Links · Redirect Chains · Anchor Validity Analysis" />
    <meta name="twitter:description"
        content="Check all link statuses to detect broken links and errors, analyze redirect chains and anchor validity to evaluate website quality. Get your A+ certificate with Web-PSQC." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Link Validation Test - Broken Links · Redirect Chains · Anchor Validity Analysis',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url' => url('/'),
    ],
    'description' => 'Crawl all internal, external, and image links on your website to detect broken links and errors. Analyze 404/500 status codes, redirect chains, and anchor validity to evaluate web quality and user experience.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (Common Component) --}}
    <x-test-shared.header title="🔗 Link Validation" subtitle="Internal/External/Image Links + Anchor Status Check" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL Form (Individual Component) --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- URL Input Form -->
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
                                                Running...
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
                                                class="text-primary">Add Schedule</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- Schedule Form (Common Component) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- Recurring Schedule Form (Common Component) --}}
                        <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                            :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                    @endif

                    {{-- Test Status (Common Component) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- Unique content for individual test --}}
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
                                    <h3>Playwright-based Link Validation Tool</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>Measurement Tool:</strong> Custom crawler based on Playwright + Node.js<br>
                                        <strong>Test Purpose:</strong> Check the status of all links on your website to identify broken links, incorrect redirects, and non-existent anchors that harm user experience.
                                        <br><br>
                                        <strong>Test Items:</strong><br>
                                        • Internal Links: HTTP status of all page links within the same domain<br>
                                        • External Links: Validity of links connecting to external domains<br>
                                        • Image Links: Status of image resources in img tag src attributes<br>
                                        • Anchor Links: Existence of anchors in #id format within the same page<br>
                                        • Redirect Chains: Number of redirect steps and final destination for each link<br>
                                        <br>
                                        <strong>Web-PSQC</strong> uses Playwright to run real browsers and perfectly inspect even dynamic content links generated by JavaScript. OAuth/SSO-related redirects are considered normal and excluded from grading.
                                        <br><br>
                                        The test takes approximately <strong>30 seconds to 4 minutes</strong>, depending on the number of links on the page.
                                    </div>
                                    {{-- Grading Criteria Guide --}}
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
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90~100</td>
                                                    <td>• Internal/External/Image link error rate: 0%<br>
                                                        • Redirect chains ≤1 step<br>
                                                        • 100% normal anchor links</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>• Overall error rate ≤1%<br>
                                                        • Redirect chains ≤2 steps<br>
                                                        • Most anchor links normal</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>• Overall error rate ≤3%<br>
                                                        • Redirect chains ≤3 steps<br>
                                                        • Some anchor link issues</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>• Overall error rate ≤5%<br>
                                                        • Multiple link warnings (timeout/SSL issues)<br>
                                                        • Frequent anchor link errors</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>• Overall error rate ≤10%<br>
                                                        • Redirect loops or long chains<br>
                                                        • Many broken image links</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>• Overall error rate 10% or higher<br>
                                                        • Many major internal links broken<br>
                                                        • Overall poor anchor/image quality</td>
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
                                            $totals = $results['totals'] ?? [];
                                            $rates = $results['rates'] ?? [];
                                            $overall = $results['overall'] ?? [];
                                            $samples = $results['samples'] ?? [];
                                            
                                            $grade = $currentTest->overall_grade ?? 'F';
                                            $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- Overall Results -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="mb-3">Overall Results</h5>
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">Overall Error Rate</div>
                                                                <div class="h3 {{ $this->getErrorRateBadgeClass($rates['overallErrorRate'] ?? 0) }}">
                                                                    {{ $rates['overallErrorRate'] ?? 0 }}%
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">Max Redirect Chain</div>
                                                                <div class="h3">
                                                                    {{ $totals['maxRedirectChainEffective'] ?? 0 }} steps
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">Links Checked</div>
                                                                <div class="h3">
                                                                    {{ $totals['httpChecked'] ?? 0 }} links
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 text-muted small">
                                                            Evaluation Reason: {{ $overall['reason'] ?? '' }}
                                                        </div>
                                                        @if (!empty($totals['navError']))
                                                            <div class="mt-2 text-danger small">
                                                                Navigation Error: {{ $totals['navError'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detailed Results by Category -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Details by Category</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
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
                                                                <td><strong>Internal Links</strong></td>
                                                                <td>{{ $totals['internalChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['internalErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['internalErrorRate'] ?? 0) }}">
                                                                        {{ $rates['internalErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>External Links</strong></td>
                                                                <td>{{ $totals['externalChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['externalErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['externalErrorRate'] ?? 0) }}">
                                                                        {{ $rates['externalErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Image Links</strong></td>
                                                                <td>{{ $totals['imageChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['imageErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['imageErrorRate'] ?? 0) }}">
                                                                        {{ $rates['imageErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Anchor Links</strong></td>
                                                                <td>{{ $totals['anchorChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['anchorErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['anchorErrorRate'] ?? 0) }}">
                                                                        {{ $rates['anchorErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Error Samples -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Link Error Samples</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $linkSamples = $samples['links'] ?? []; @endphp
                                                        @if (empty($linkSamples))
                                                            <div class="text-muted">No errors</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($linkSamples, 0, 10) as $sample)
                                                                    <li class="mb-2">
                                                                        <div class="text-truncate" style="max-width: 100%;">
                                                                            <code>{{ $sample['url'] ?? '' }}</code>
                                                                        </div>
                                                                        <div class="text-muted">
                                                                            Status: {{ $sample['status'] ?? 0 }} • 
                                                                            Chain: {{ $sample['chain'] ?? 0 }} • 
                                                                            {{ $sample['error'] ?? '' }}
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Image Error Samples</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $imgSamples = $samples['images'] ?? []; @endphp
                                                        @if (empty($imgSamples))
                                                            <div class="text-muted">No errors</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($imgSamples, 0, 10) as $sample)
                                                                    <li class="mb-2">
                                                                        <div class="text-truncate" style="max-width: 100%;">
                                                                            <code>{{ $sample['url'] ?? '' }}</code>
                                                                        </div>
                                                                        <div class="text-muted">
                                                                            Status: {{ $sample['status'] ?? 0 }} • 
                                                                            Chain: {{ $sample['chain'] ?? 0 }} • 
                                                                            {{ $sample['error'] ?? '' }}
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Anchor Error Samples (Same Page #id)</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $anchorSamples = $samples['anchors'] ?? []; @endphp
                                                        @if (empty($anchorSamples))
                                                            <div class="text-muted">No errors</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($anchorSamples, 0, 10) as $sample)
                                                                    <li>
                                                                        <code>{{ $sample['href'] ?? '' }}</code>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Measurement Metrics Explanation -->
                                        <div class="alert alert-info d-block">
                                            <h6>📊 Measurement Metrics Explanation</h6>
                                            <p class="mb-2"><strong>Error Rate:</strong> Percentage calculated as (Error Links ÷ Total Links) × 100.</p>
                                            <p class="mb-2"><strong>Redirect Chain:</strong> Number of redirects to reach the final destination. Shorter is better.</p>
                                            <p class="mb-2"><strong>HTTP Status Code:</strong> 200s (Success), 300s (Redirect), 400s (Client Error), 500s (Server Error)</p>
                                            <p class="mb-0"><strong>Anchor Link:</strong> Links in #id format that navigate to specific locations within a page.</p>
                                        </div>

                                        <!-- Improvement Recommendations -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 Improvement Recommendations</h6>
                                            @if ($rates['overallErrorRate'] > 0)
                                                <p class="mb-2">• <strong>Fix Broken Links:</strong> Correct or remove links returning 404 errors to proper URLs.</p>
                                            @endif
                                            @if ($totals['maxRedirectChainEffective'] > 2)
                                                <p class="mb-2">• <strong>Shorten Redirect Chains:</strong> Connect multi-step redirects directly to final destinations.</p>
                                            @endif
                                            @if ($rates['imageErrorRate'] > 0)
                                                <p class="mb-2">• <strong>Check Image Paths:</strong> Fix paths to non-existent image files or provide alternative images.</p>
                                            @endif
                                            @if ($rates['anchorErrorRate'] > 0)
                                                <p class="mb-2">• <strong>Match Anchor IDs:</strong> Ensure id="section" elements exist on the page for href="#section" links.</p>
                                            @endif
                                            @if ($rates['externalErrorRate'] > 5)
                                                <p class="mb-2">• <strong>Monitor External Links:</strong> Check regularly as external sites may change or be deleted.</p>
                                            @endif
                                            <p class="mb-0">• <strong>Regular Checks:</strong> Periodically test as website link status can change over time.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No Results Yet</h5>
                                            <p class="mb-0">Run the test to view link validation results.</p>
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
                                            <p class="mb-0">Run the test to view Raw JSON data.</p>
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