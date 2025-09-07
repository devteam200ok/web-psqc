@section('title')
    <title>🔍 Nuclei Vulnerability Scan – CVE 2024–2025 | Web-PSQC</title>
    <meta name="description"
        content="Automated security scanning with Nuclei: detect 2024–2025 CVEs, misconfigurations, and sensitive data exposures. Assess risk with clear grading.">
    <meta name="keywords" content="vulnerability scan, Nuclei, CVE 2024, CVE 2025, security assessment, automated scanning, zero-day detection, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Nuclei Vulnerability Scan – CVE 2024–2025 | Web-PSQC" />
    <meta property="og:description"
        content="Use Nuclei to automatically detect recent CVEs, misconfigurations, and exposures — including 2024–2025 threats." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC – Nuclei Vulnerability Scan" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Nuclei Vulnerability Scan – CVE 2024–2025 | Web-PSQC" />
    <meta name="twitter:description" content="Nuclei-based detection of latest security issues: CVE 2024–2025, misconfigurations, and data exposures." />
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
    'name' => 'Nuclei Vulnerability Scan – CVE 2024–2025',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'Nuclei-powered scanner that detects recent CVEs (2024–2025), misconfigurations, and sensitive exposures, with risk grading.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (shared component) --}}
    <x-test-shared.header title="🔍 Nuclei Vulnerability Scan" subtitle="CVE 2024–2025 Detection" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL form --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-info d-block mb-4">
                                    <h5>🔐 Sign-in required</h5>
                                    <p class="mb-2">Security scans require domain ownership verification.</p>
                                    <p class="mb-0">After signing in, register your domain under the “Domains” tab in the sidebar and complete verification.</p>
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
                                                Run Scan
                                            @endif
                                        </button>
                                    </div>
                                    @error('url')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if (Auth::check())
                                        <div class="form-text">Only verified domains can be scanned.</div>
                                    @endif

                                    @if ($hasProOrAgencyPlan)
                                        <div class="mt-2">
                                            <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                                class="text-primary me-3">Schedule Scan</a>
                                            <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                                class="text-primary">Set Up Recurring</a>
                                        </div>
                                    @endif
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

                    {{-- Test status (shared component) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- Page-specific content --}}
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
                                    <h3>Automated detection of the latest CVEs with Nuclei</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>Tool:</strong> ProjectDiscovery Nuclei — an industry‑standard, template‑based vulnerability scanner with fast execution
                                        <br><br>
                                        <strong>Goals:</strong><br>
                                        • Detect newly disclosed CVEs (2024–2025)<br>
                                        • Check recent zero‑day and 1‑day issues<br>
                                        • Identify misconfigurations and insecure defaults<br>
                                        • Discover exposed admin panels, debug pages, and backup files<br>
                                        • Assess subdomain takeover risk<br>
                                        • Detect sensitive data exposure (API keys, tokens, env vars)
                                        <br><br>
                                        <strong>How it works:</strong><br>
                                        • <strong>Template‑based:</strong> YAML templates focused on recent 2024–2025 vulnerabilities<br>
                                        • <strong>Non‑intrusive:</strong> Validates signatures without active exploitation<br>
                                        • <strong>Scope:</strong> Single target URL (no deep crawling)<br>
                                        • <strong>Priority:</strong> Scans Critical/High first, then Medium/Low<br>
                                        • <strong>Duration:</strong> ~30 seconds to 3 minutes (varies by templates)<br>
                                        • <strong>Domain verification:</strong> Only verified domains can be scanned
                                        <br><br>
                                        <strong>Coverage highlights:</strong><br>
                                        • Major RCEs such as Log4Shell and Spring4Shell<br>
                                        • Latest WordPress, Joomla, and Drupal plugin issues<br>
                                        • Apache, Nginx, IIS web server misconfigurations<br>
                                        • Exposed Git/SVN/.env files<br>
                                        • GraphQL and REST API endpoint weaknesses<br>
                                        • Cloud service misconfigurations (AWS, Azure, GCP)
                                    </div>

                                    {{-- Grading criteria --}}
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
                                                    <td>90–100</td>
                                                    <td>0 Critical/High and 0 Medium findings<br>No 2024–2025 CVEs detected<br>No open directories/debug pages/sensitive files<br>Security headers/banners minimized</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80–89</td>
                                                    <td>≤1 High and ≤1 Medium<br>No direct exposure to recent CVEs (may require conditions/bypass)<br>Minor configuration warnings (informational)<br>Solid patching/config management</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70–79</td>
                                                    <td>≤2 High or ≤3 Medium<br>Some configuration/banner exposure
Protected admin endpoints present (hard to bypass)
Tendency to delay patches</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60–69</td>
                                                    <td>≥3 High or many Medium findings<br>Sensitive files/backups/indexing exposed
Older components inferred (banners/meta)
Needs systematic patching and hardening</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50–59</td>
                                                    <td>≥1 Critical or easily exploitable High<br>Likely directly affected by recent (2024–2025) CVEs<br>Risky unauthenticated endpoints/files
Sensitive build/log/env data exposed</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0–49</td>
                                                    <td>Multiple Critical/High present simultaneously<br>Many recent CVEs unpatched/widely exposed
Missing basic security controls (headers/access control)
No overarching security guardrails</td>
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

                                        <!-- Vulnerability summary -->
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
                                                        Scan time: {{ $metrics['scan_duration'] }}s |
                                                        Templates matched: {{ $metrics['templates_matched'] ?? 0 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Critical/High vulnerabilities -->
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
                                                                            References:
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

                                        <!-- Medium/Low vulnerability summary -->
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
                                                                        <th>Vulnerability</th>
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

                                        <!-- Severity guide -->
                                        <div class="alert alert-info d-block">
                                            <h6>Severity Guide</h6>
                                            <p class="mb-2"><strong>Critical:</strong> Immediate RCE, auth bypass, or data exfiltration risk</p>
                                            <p class="mb-2"><strong>High:</strong> Likely exploitable issues (e.g., SQLi, XSS, SSRF)</p>
                                            <p class="mb-2"><strong>Medium:</strong> Information disclosure, misconfigurations, outdated software</p>
                                            <p class="mb-2"><strong>Low:</strong> Low risk items (e.g., directory listing, banner exposure)</p>
                                            <p class="mb-0"><strong>Info:</strong> Informational findings without direct security impact</p>
                                        </div>

                                        <!-- Recommendations -->
                                        <div class="alert alert-info d-block">
                                            <h6>Security recommendations</h6>
                                            <p class="mb-2"><strong>1. Patch immediately:</strong> Apply fixes or mitigations for Critical/High findings as soon as they are discovered.</p>
                                            <p class="mb-2"><strong>2. Keep software current:</strong> Maintain up‑to‑date CMS, plugins, and frameworks.</p>
                                            <p class="mb-2"><strong>3. Harden configurations:</strong> Disable unnecessary services, remove debug modes, and change default accounts.</p>
                                            <p class="mb-2"><strong>4. Enforce access control:</strong> Restrict admin by IP, enable 2FA, and apply least privilege.</p>
                                            <p class="mb-2"><strong>5. Monitor continuously:</strong> Review security logs and deploy anomaly detection.</p>
                                            <p class="mb-0"><strong>6. Scan regularly:</strong> Run vulnerability scans at least monthly to catch new threats early.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a scan to view the latest vulnerability results.</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                    id="tabs-data">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Raw JSON Data</h5>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="copyJsonToClipboard()" title="Copy JSON">
                                                Copy
                                            </button>
                                        </div>
                                        <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a scan to view the raw JSON data.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
