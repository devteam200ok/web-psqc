@section('title')
    <title>🔒 SSL Deep Analysis – Comprehensive TLS Security with SSLyze | Web-PSQC</title>
    <meta name="description"
        content="In‑depth SSL/TLS analysis with SSLyze: protocol compatibility, cipher strength, certificate validity, OCSP Stapling, HSTS, PFS, elliptic curves, and more — evaluated up to A+.">
    <meta name="keywords"
        content="SSL deep analysis, SSLyze, TLS protocols, cipher suites, certificate validation, OCSP Stapling, HSTS, Perfect Forward Secrecy, ECC, SSL security grade, Web-PSQC">
    <meta name="author" content="Web-PSQC Team">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="SSL Deep Analysis – Comprehensive TLS Security with SSLyze" />
    <meta property="og:description"
        content="Analyze TLS protocols, cipher suites, certificates, OCSP Stapling, HSTS, ECC and more with SSLyze to assess SSL/TLS security up to A+." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC SSLyze Deep Security Analysis" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="SSL Deep Analysis – Comprehensive TLS Security with SSLyze | Web-PSQC" />
    <meta name="twitter:description"
        content="Use SSLyze to analyze TLS/SSL configuration: protocols, cipher suites, certificate state, HSTS, OCSP Stapling, ECC — with grades and guidance." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Web-PSQC',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'SSL Deep Analysis – SSLyze‑Based Comprehensive TLS Security',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Leverage SSLyze to analyze TLS protocols, cipher suites, certificate status, HSTS, OCSP Stapling, and ECC; assess SSL/TLS security up to A+. ',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (shared component) --}}
    <x-test-shared.header title="🔒 SSL Deep Analysis" subtitle="SSLyze comprehensive diagnostics" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL form --}}
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
                                                class="text-primary">Set Recurring</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- Schedule form (shared component) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- Recurring schedule form (shared component) --}}
                        <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                            :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                    @endif

                    {{-- Test status (shared component) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- Main content --}}
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
                                    <h3>SSL/TLS Deep Analysis with SSLyze</h3>
                                    <div class="text-muted small mt-3">
                                        <strong>Testing Tool:</strong> SSLyze v5.x - Open-source SSL/TLS scanner recommended by Mozilla, Qualys, IETF and others<br>
                                        <strong>Purpose:</strong> Comprehensively diagnose website SSL/TLS configuration to identify security vulnerabilities and provide improvement recommendations<br><br>

                                        <strong>Test Coverage:</strong><br>
                                        • <strong>TLS Protocol Versions</strong> - SSL 2.0/3.0, TLS 1.0/1.1/1.2/1.3 support detection<br>
                                        • <strong>Cipher Suites</strong> - Strength assessment, PFS (Perfect Forward Secrecy) support, weak cipher detection<br>
                                        • <strong>Certificate Chain</strong> - Validity, expiration, signature algorithms, key size, chain completeness<br>
                                        • <strong>OCSP Stapling</strong> - Real-time certificate revocation status verification mechanism<br>
                                        • <strong>HTTP Security Headers</strong> - HSTS (HTTP Strict Transport Security) configuration<br>
                                        • <strong>Elliptic Curve Cryptography</strong> - Supported curve list and strength evaluation<br><br>

                                        <strong>Web-PSQC</strong> scans the target server's SSL/TLS configuration using the SSLyze engine
                                        and calculates security grades based on collected data.<br>
                                        This process typically takes <strong>30 seconds to 3 minutes</strong>.
                                    </div>

                                    {{-- Grading criteria guide --}}
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
                                                    <td><strong>TLS 1.3/1.2 only</strong>, no weak ciphers (<strong>all PFS</strong>)<br>
                                                        Certificate <strong>ECDSA</strong> or <strong>RSA≥3072</strong>, complete chain, expires in <strong>60+ days</strong><br>
                                                        <strong>OCSP Stapling</strong> working (ideally with <strong>Must-Staple</strong>)<br>
                                                        HSTS enabled, max-age ≥ 1 year, includeSubDomains, preload
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>80~89</td>
                                                    <td><strong>TLS 1.3/1.2</strong>, strong ciphers prioritized (<strong>mostly PFS</strong>)<br>
                                                        Certificate <strong>RSA≥2048</strong>, <strong>SHA-256+</strong>, valid chain, expires in <strong>30+ days</strong><br>
                                                        <strong>OCSP Stapling</strong> enabled (occasional failures allowed)<br>
                                                        HSTS enabled, max-age ≥ 6 months
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>65~79</td>
                                                    <td><strong>TLS 1.2</strong> required, 1.3 optional/unsupported, some <strong>CBC</strong> present<br>
                                                        Certificate <strong>RSA≥2048</strong>, valid chain (expires in <strong>14+ days</strong>)<br>
                                                        OCSP Stapling <strong>disabled</strong> (but OCSP responses available)<br>
                                                        HSTS configured but partially inadequate</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>50~64</td>
                                                    <td><strong>TLS 1.0/1.1</strong> enabled or <strong>many weak ciphers</strong> (low PFS)<br>
                                                        Missing chain/<strong>weak signatures (SHA-1)</strong> or expires soon (<strong>≤14 days</strong>)<br>
                                                        No Stapling, <strong>unclear</strong> revocation checking<br>
                                                        HSTS <strong>not configured</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>35~49</td>
                                                    <td>Legacy protocols/ciphers (<strong>SSLv3/EXPORT/RC4</strong> etc.) allowed<br>
                                                        Certificate <strong>mismatch/chain errors</strong> frequent<br>
                                                        Stapling <strong>fails</strong>, revocation checking <strong>impossible</strong><br>
                                                        Security headers generally inadequate</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~34</td>
                                                    <td><strong>Handshake failure</strong> level defects<br>
                                                        <strong>Expired/self-signed/hostname mismatch</strong><br>
                                                        Widespread <strong>weak protocols and ciphers</strong> allowed<br>
                                                        Overall <strong>TLS configuration failure</strong>
                                                    </td>
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

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- Detected Issues -->
                                        @if (!empty($issues))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">Detected Security Issues ({{ count($issues) }})</h5>
                                                    <div class="list-group">
                                                        @foreach ($issues as $issue)
                                                            <div class="list-group-item list-group-item-danger">
                                                                ⚠️ {{ $issue }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Recommendations -->
                                        @if (!empty($recommendations))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">Recommendations</h5>
                                                    <div class="list-group">
                                                        @foreach ($recommendations as $recommendation)
                                                            <div class="list-group-item list-group-item-info">
                                                                💡 {{ $recommendation }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Detailed Analysis -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">Detailed Analysis</h5>

                                                <!-- TLS Version Analysis -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">TLS Protocol Versions</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>TLS 1.2:</strong>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Supported</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">Not supported</span>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>TLS 1.3:</strong>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">Supported</span>
                                                                @else
                                                                    <span class="badge bg-orange-lt text-orange-lt-fg">Not supported</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if (!empty($analysis['tls_versions']['issues']))
                                                            <div class="mt-3">
                                                                <strong class="text-danger">Issues Found:</strong>
                                                                <ul class="mb-0">
                                                                    @foreach ($analysis['tls_versions']['issues'] as $issue)
                                                                        <li>{{ $issue }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Cipher Suite Analysis -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">Cipher Suites Analysis</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['cipher_suites']['tls_1_2']))
                                                            <div class="mb-3">
                                                                <strong>TLS 1.2 Cipher Suites:</strong>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">Total</div>
                                                                        <div class="h4">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['total'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">Strong</div>
                                                                        <div class="h4 text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['strong'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">Weak</div>
                                                                        <div class="h4 text-danger">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['weak'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">PFS Ratio</div>
                                                                        <div class="h4">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] }}%
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (!empty($analysis['cipher_suites']['tls_1_3']))
                                                            <div>
                                                                <strong>TLS 1.3 Cipher Suites:</strong>
                                                                {{ $analysis['cipher_suites']['tls_1_3']['total'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Certificate Analysis -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">Certificate Analysis</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['certificate']['details']))
                                                            <div class="row">
                                                                @if (isset($analysis['certificate']['details']['key_algorithm']))
                                                                    <div class="col-md-6">
                                                                        <strong>Public Key Algorithm:</strong>
                                                                        {{ $analysis['certificate']['details']['key_algorithm'] }}
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['key_size']))
                                                                    <div class="col-md-6">
                                                                        <strong>Key Size:</strong>
                                                                        {{ $analysis['certificate']['details']['key_size'] }} bits
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['signature_algorithm']))
                                                                    <div class="col-md-6">
                                                                        <strong>Signature Algorithm:</strong>
                                                                        {{ $analysis['certificate']['details']['signature_algorithm'] }}
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['days_to_expiry']))
                                                                    <div class="col-md-6">
                                                                        <strong>Days to Expiry:</strong>
                                                                        @if ($analysis['certificate']['details']['days_to_expiry'] <= 14)
                                                                            <span class="text-danger">{{ $analysis['certificate']['details']['days_to_expiry'] }} days</span>
                                                                        @elseif ($analysis['certificate']['details']['days_to_expiry'] <= 30)
                                                                            <span class="text-warning">{{ $analysis['certificate']['details']['days_to_expiry'] }} days</span>
                                                                        @else
                                                                            <span class="text-success">{{ $analysis['certificate']['details']['days_to_expiry'] }} days</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- OCSP Stapling -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">OCSP Stapling</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <strong>Status:</strong>
                                                        @if (($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL')
                                                            <span class="badge bg-green-lt text-green-lt-fg">Enabled</span>
                                                        @else
                                                            <span class="badge bg-red-lt text-red-lt-fg">Disabled</span>
                                                        @endif

                                                        @if (isset($analysis['ocsp']['certificate_status']))
                                                            <div class="mt-2">
                                                                <strong>Certificate Status:</strong>
                                                                {{ $analysis['ocsp']['certificate_status'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- HTTP Security Headers -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">HTTP Security Headers</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['http_headers']['hsts']))
                                                            <strong>HSTS:</strong> <span class="badge bg-green-lt text-green-lt-fg">Configured</span>
                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">max-age</div>
                                                                    <div>
                                                                        {{ number_format($analysis['http_headers']['hsts']['max_age']) }} seconds
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">includeSubDomains</div>
                                                                    <div>
                                                                        @if ($analysis['http_headers']['hsts']['include_subdomains'] ?? false)
                                                                            <span class="badge bg-green-lt text-green-lt-fg">Yes</span>
                                                                        @else
                                                                            <span class="badge bg-orange-lt text-orange-lt-fg">No</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">preload</div>
                                                                    <div>
                                                                        @if ($analysis['http_headers']['hsts']['preload'] ?? false)
                                                                            <span class="badge bg-green-lt text-green-lt-fg">Yes</span>
                                                                        @else
                                                                            <span class="badge bg-orange-lt text-orange-lt-fg">No</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <strong>HSTS:</strong> <span class="badge bg-red-lt text-red-lt-fg">Not configured</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Elliptic Curves -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">Elliptic Curve Cryptography</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['elliptic_curves']['supported']))
                                                            <strong>Supported Curves:</strong>
                                                            <div class="mt-2">
                                                                @foreach ($analysis['elliptic_curves']['supported'] as $curve)
                                                                    <span class="badge bg-azure-lt text-azure-lt-fg me-1">{{ $curve }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No elliptic curve information available</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Metric descriptions -->
                                        <div class="alert alert-info d-block">
                                            <h6>Metric descriptions</h6>
                                            <p class="mb-2"><strong>TLS versions:</strong> protocol versions for encrypted transport. TLS 1.2+ is safe; TLS 1.3 is latest and recommended.</p>
                                            <p class="mb-2"><strong>PFS (Perfect Forward Secrecy):</strong> prevents future decryption of past traffic.</p>
                                            <p class="mb-2"><strong>OCSP Stapling:</strong> efficient mechanism for checking certificate revocation.</p>
                                            <p class="mb-2"><strong>HSTS:</strong> policy forcing browsers to always use HTTPS.</p>
                                            <p class="mb-0"><strong>Elliptic curves:</strong> efficient public‑key crypto; X25519 and secp256r1 are safe choices.</p>
                                        </div>

                                        <!-- Recommendations -->
                                        <div class="alert alert-info d-block">
                                            <h6>SSL/TLS Hardening</h6>
                                            <p class="mb-2"><strong>1. Use modern protocols:</strong> enable TLS 1.3; disable TLS 1.0/1.1.</p>
                                            <p class="mb-2"><strong>2. Prefer strong ciphers:</strong> prioritize PFS (ECDHE/DHE); remove weak ciphers (RC4, DES).</p>
                                            <p class="mb-2"><strong>3. Certificate hygiene:</strong> RSA ≥ 2048 (3072 recommended) or ECDSA 256‑bit.</p>
                                            <p class="mb-2"><strong>4. Enable OCSP Stapling:</strong> improves performance and security.</p>
                                            <p class="mb-0"><strong>5. Set HSTS:</strong> max‑age ≥ 1 year (31536000); includeSubDomains + preload.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>No results yet</h5>
                                            <p class="mb-0">Run a test to view the SSL/TLS security analysis.</p>
                                        </div>
                                    @endif
                                </div>

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
                                        <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results['raw_json'] ?? $currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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

                <div class="col-xl-4 mb-2">
                    {{-- Sidebar (shared component) --}}
                    <x-test-shared.sidebar :side-tab-active="$sideTabActive" :test-history="$testHistory" :selected-history-test="$selectedHistoryTest" :user-domains="$userDomains"
                        :scheduled-tests="$scheduledTests" :has-pro-or-agency-plan="$hasProOrAgencyPlan" />

                    {{-- Domain verification modal (shared component) --}}
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