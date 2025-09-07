@section('title')
    <title>🔍 Latest Vulnerability Scan - Nuclei CVE 2024-2025 Auto Detection | Web-PSQC</title>
    <meta name="description"
        content="Nuclei-based security scanner that automatically detects 2024-2025 CVE vulnerabilities, misconfigurations, and sensitive data exposure with security grade assessment. Stay protected against latest security threats.">
    <meta name="keywords" content="vulnerability scan, Nuclei, CVE 2024, CVE 2025, security assessment, automated scanning, zero-day vulnerability detection, Web-PSQC">
    <meta name="author" content="Web-PSQC Global">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Latest Vulnerability Scan - Nuclei CVE 2024-2025 Auto Detection | Web-PSQC" />
    <meta property="og:description"
        content="Automatically detect latest CVE vulnerabilities and security misconfigurations with Nuclei. Experience real-time security assessment including 2024-2025 new threats." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC - Latest Vulnerability Scan" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Latest Vulnerability Scan - Nuclei CVE 2024-2025 Auto Detection | Web-PSQC" />
    <meta name="twitter:description" content="Nuclei-based latest security vulnerability detection. Precise analysis of CVE 2024-2025, misconfigurations, and sensitive data exposure." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Web-PSQC Global',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Latest Vulnerability Scan - Nuclei CVE 2024-2025 Auto Detection',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Nuclei-based security scanner that detects latest CVE (2024-2025) vulnerabilities, misconfigurations, and sensitive data exposure with security grade assessment.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (Shared Component) --}}
    <x-test-shared.header title="CVE Check" subtitle="Latest known vulnerabilities" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL Form --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-info d-block mb-4">
                                    <h5>🔐 Login Required</h5>
                                    <p class="mb-2">Security scanning requires domain ownership verification.</p>
                                    <p class="mb-0">Please login and register your domain in the "Domains" tab in the sidebar to verify ownership.</p>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">Website URL</label>
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
                                                Running...
                                            @else
                                                Test
                                            @endif
                                        </button>
                                    </div>
                                    @error('url')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if (Auth::check())
                                        <div class="form-text">Only verified domains can be tested.</div>
                                    @endif

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
                                    <h3>Nuclei-based Latest CVE Vulnerability Auto Detection</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>Testing Tool:</strong> Nuclei by ProjectDiscovery - Industry standard vulnerability scanner providing fast template-based scanning
                                        <br><br>
                                        <strong>Test Purpose:</strong><br>
                                        • Detect newly published CVE vulnerabilities from 2024-2025<br>
                                        • Check recently disclosed zero-day and 1-day vulnerabilities<br>
                                        • Discover misconfigurations and default setting vulnerabilities<br>
                                        • Detect exposed panels, debug pages, and backup files<br>
                                        • Check subdomain takeover possibilities<br>
                                        • Detect sensitive information exposure (API keys, tokens, environment variables)
                                        <br><br>
                                        <strong>Testing Method:</strong><br>
                                        • <strong>Template-based:</strong> Utilizes YAML templates specialized for 2024-2025 latest vulnerabilities<br>
                                        • <strong>Non-intrusive:</strong> Verifies vulnerability signatures without actual attacks<br>
                                        • <strong>Scope:</strong> Single URL target (no deep crawling)<br>
                                        • <strong>Priority:</strong> Scans Critical, High first, then Medium, Low sequentially<br>
                                        • <strong>Duration:</strong> Approximately 30 seconds to 3 minutes (varies by template count)<br>
                                        • <strong>Domain Verification:</strong> Only verified domains can be scanned
                                        <br><br>
                                        <strong>Latest Vulnerability Coverage:</strong><br>
                                        • Major RCE vulnerabilities like Log4Shell, Spring4Shell<br>
                                        • Latest WordPress, Joomla, Drupal plugin vulnerabilities<br>
                                        • Apache, Nginx, IIS web server misconfigurations<br>
                                        • Git, SVN, ENV file exposure<br>
                                        • GraphQL, REST API endpoint vulnerabilities<br>
                                        • Cloud service (AWS, Azure, GCP) misconfigurations
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
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90~100</td>
                                                    <td>0 Critical/High, 0 Medium<br>No 2024-2025 CVE detected<br>No exposed directories/debug/sensitive files<br>Good security headers/banner exposure (minimal information)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>High ≤1, Medium ≤1<br>No direct recent CVE exposure (requires bypass/conditions)<br>Minor configuration warnings (informational level)<br>Good patch/configuration management</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>High ≤2 or Medium ≤3<br>Some configuration/banner exposure exists<br>Protected admin endpoints exist (difficult to bypass)<br>Patch delay tendency (delayed security release adoption)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>High ≥3 or multiple Medium<br>Sensitive files/backups/indexing exposure found<br>Old version components detectable (banner/meta info)<br>Systematic improvement needed in patch/configuration management</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>Critical ≥1 or low-difficulty High exploitation<br>Recent (2024-2025) CVE direct impact estimated<br>Risky endpoints/files accessible without authentication<br>Build/log/environment sensitive information exposure</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>Multiple Critical/High simultaneously<br>Massive unpatched/widespread exposure of latest CVE<br>Lack of basic security configuration (missing defense headers/access control)<br>Complete absence of security guardrails</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
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
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- Vulnerability Summary -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Vulnerability Summary</h5>
                                                <div class="row g-2">
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Critical</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['high'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">High</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Medium</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['low'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Low</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['info'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Info</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (isset($metrics['scan_duration']) && $metrics['scan_duration'] > 0)
                                                    <div class="text-muted small mt-2">
                                                        Scan Time: {{ $metrics['scan_duration'] }} seconds |
                                                        Templates Matched: {{ $metrics['templates_matched'] ?? 0 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Critical/High Vulnerability Details -->
                                        @foreach (['critical' => 'bg-red-lt text-red-lt-fg', 'high' => 'bg-orange-lt text-orange-lt-fg'] as $severity => $badgeClass)
                                            @if (!empty($vulnerabilities[$severity]))
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            {{ ucfirst($severity) }} Vulnerabilities
                                                            ({{ count($vulnerabilities[$severity]) }})
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach ($vulnerabilities[$severity] as $vuln)
                                                            <div class="card card-sm mb-2">
                                                                <div class="card-body">
                                                                    <div class="fw-bold">
                                                                        {{ $vuln['name'] ?? 'Unknown' }}</div>
                                                                    @if (!empty($vuln['description']))
                                                                        <div class="text-muted small mb-1">
                                                                            {{ $vuln['description'] }}</div>
                                                                    @endif
                                                                    <div class="small text-muted">
                                                                        Template:
                                                                        <code>{{ $vuln['template_id'] ?? '' }}</code>
                                                                        @if (!empty($vuln['matched_at']))
                                                                            | Target: {{ $vuln['matched_at'] }}
                                                                        @endif
                                                                    </div>
                                                                    @if (!empty($vuln['reference']) && is_array($vuln['reference']))
                                                                        <div class="small mt-1">
                                                                            Reference:
                                                                            @foreach (array_slice($vuln['reference'], 0, 2) as $ref)
                                                                                <a href="{{ $ref }}"
                                                                                    target="_blank"
                                                                                    class="text-primary">{{ parse_url($ref, PHP_URL_HOST) ?? 'Link' }}</a>
                                                                                @if (!$loop->last)
                                                                                    |
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- Medium/Low Vulnerability Summary -->
                                        @foreach (['medium' => 'bg-yellow-lt text-yellow-lt-fg', 'low' => 'bg-blue-lt text-blue-lt-fg'] as $severity => $badgeClass)
                                            @if (!empty($vulnerabilities[$severity]))
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            {{ ucfirst($severity) }} Vulnerabilities
                                                            ({{ count($vulnerabilities[$severity]) }})
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Vulnerability Name</th>
                                                                        <th>Template ID</th>
                                                                        <th>Target</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($vulnerabilities[$severity] as $vuln)
                                                                        <tr>
                                                                            <td>{{ $vuln['name'] ?? 'Unknown' }}</td>
                                                                            <td><code>{{ $vuln['template_id'] ?? '' }}</code>
                                                                            </td>
                                                                            <td class="text-muted small">
                                                                                {{ Str::limit($vuln['matched_at'] ?? '', 50) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- Measurement Metrics Explanation -->
                                        <div class="alert alert-info d-block">
                                            <h6>Measurement Metrics Explanation</h6>
                                            <p class="mb-2"><strong>Critical:</strong> Immediate remote code execution (RCE), authentication bypass, data breach, and other severe vulnerabilities</p>
                                            <p class="mb-2"><strong>High:</strong> SQL Injection, XSS, SSRF, and other highly exploitable vulnerabilities</p>
                                            <p class="mb-2"><strong>Medium:</strong> Information disclosure, misconfigurations, outdated software, and medium-risk issues</p>
                                            <p class="mb-2"><strong>Low:</strong> Directory listing, banner exposure, and low-risk issues</p>
                                            <p class="mb-0"><strong>Info:</strong> No direct security impact but informational findings</p>
                                        </div>

                                        <!-- Improvement Recommendations -->
                                        <div class="alert alert-info d-block">
                                            <h6>Security Improvement Recommendations</h6>
                                            <p class="mb-2"><strong>1. Immediate Patching:</strong> Apply patches immediately for Critical/High vulnerabilities or implement temporary defense measures.</p>
                                            <p class="mb-2"><strong>2. Regular Updates:</strong> Keep CMS, plugins, and frameworks up to date with the latest versions.</p>
                                            <p class="mb-2"><strong>3. Configuration Hardening:</strong> Disable unnecessary services, remove debug mode, change default accounts</p>
                                            <p class="mb-2"><strong>4. Access Control:</strong> Apply IP restrictions to admin pages, enable 2FA, apply principle of least privilege</p>
                                            <p class="mb-2"><strong>5. Monitoring:</strong> Monitor security logs, establish anomaly detection systems</p>
                                            <p class="mb-0"><strong>6. Regular Scanning:</strong> Run vulnerability scans at least monthly to detect new threats early</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No Results Yet</h5>
                                            <p class="mb-0">Run a test to see the latest vulnerability scan results.</p>
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
                                            <p class="mb-0">Run a test to see the Raw JSON data.</p>
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
