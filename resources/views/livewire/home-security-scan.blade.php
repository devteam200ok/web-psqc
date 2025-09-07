@section('title')
   <title>🛡️ Security Vulnerability Scan – OWASP ZAP Passive Scan | Web-PSQC</title>
   <meta name="description" content="Use OWASP ZAP passive scanning to automatically detect key web vulnerabilities (SQL injection, XSS, security headers) and assess your security grade.">
   <meta name="keywords" content="security vulnerability scan, OWASP ZAP, passive scan, SQL Injection, XSS detection, security headers, web security test, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow">

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="Security Vulnerability Scan – OWASP ZAP Passive Scan" />
   <meta property="og:description"
       content="Detect SQL injection, XSS, security header issues via OWASP ZAP passive scan and qualify for an A+ certificate." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Security Vulnerability Scan" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="Security Vulnerability Scan – OWASP ZAP Passive | Web-PSQC" />
   <meta name="twitter:description"
       content="OWASP ZAP passive scan detects SQLi, XSS, and header issues with actionable guidance." />
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
   'name' => 'Security Vulnerability Scan – OWASP ZAP Passive',
   'url'  => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url'  => url('/'),
   ],
   'description' => 'OWASP ZAP passive scan automatically detects key issues like SQL injection, XSS, and security headers to assess a security grade.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection

@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="🛡️ Security Vulnerability Scan" subtitle="OWASP ZAP Passive Scan" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                   <h5>🔐 Sign‑in Required</h5>
                                   <p class="mb-2">Security scanning requires domain ownership verification.</p>
                                   <p class="mb-0">Sign in, then register and verify your domain in the "Domains" tab in the sidebar.</p>
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
                                               Scan
                                           @endif
                                       </button>
                                   </div>
                                   @error('url')
                                       <div class="invalid-feedback d-block">{{ $message }}</div>
                                   @enderror
                                   @if (Auth::check())
                                       <div class="form-text">Only domains with verified ownership can be scanned.</div>
                                   @endif

                                   @if ($hasProOrAgencyPlan)
                                       <div class="mt-2">
                                       <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                           class="text-primary me-3">Schedule Scan</a>
                                       <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                           class="text-primary">Add Recurring Schedule</a>
                                       </div>
                                   @endif
                               </div>
                           </div>
                       </div>
                   </div>

                   @if ($hasProOrAgencyPlan)
                       {{-- Schedule scan form (shared component) --}}
                       <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                           :schedule-minute="$scheduleMinute" />

                       {{-- Recurring schedule form (shared component) --}}
                       <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                           :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                   @endif

                   {{-- Test status (shared component) --}}
                   <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                   {{-- Individual test-specific content --}}
                   <div class="card">
                       <div class="card-header">
                           <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                               <li class="nav-item">
                                   <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                       class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                       data-bs-toggle="tab">Scan Info</a>
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
                                   <h3>OWASP ZAP Passive Scan — Non‑intrusive Security Analysis</h3>
                                   <div class="text-muted small mt-1">
                                       <strong>Tool:</strong> OWASP ZAP (Zed Attack Proxy) — a widely used open‑source web security testing tool
                                       <br><br>
                                       <strong>Goals:</strong><br>
                                       • Analyze HTTP responses to identify potential vulnerabilities<br>
                                       • Validate security header configuration (HSTS, X-Frame-Options, X-Content-Type-Options, etc.)<br>
                                       • Detect sensitive information exposure (cookies, debug info, server banners)<br>
                                       • Check session management weaknesses<br>
                                       • Identify potential injection points<br>
                                       • Detect technology stack in use
                                       <br><br>
                                       <strong>Method:</strong><br>
                                       • <strong>Passive scan:</strong> analyzes HTTP requests/responses without active attacks<br>
                                       • <strong>Scope:</strong> main page of the specified URL (no crawling)<br>
                                       • <strong>Excludes:</strong> CSP warnings (covered in headers test)<br>
                                       • <strong>Time:</strong> ~10–20 seconds<br>
                                       • <strong>Domain verification:</strong> only verified domains can be scanned
                                   </div>

                                   {{-- Grade criteria guide --}}
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
                                                   <td>0 High/Medium vulnerabilities<br>Complete security headers (HTTPS, HSTS, X-Frame-Options etc.)<br>No sensitive information exposure (cookies, comments, debug)<br>Minimal server/framework version disclosure</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-a">A</span></td>
                                                   <td>80–89</td>
                                                   <td>High 0, Medium ≤ 1<br>Most security headers present, minor gaps<br>No sensitive data exposure<br>Minor info exposure (e.g., server type)</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-b">B</span></td>
                                                   <td>70–79</td>
                                                   <td>High ≤ 1, Medium ≤ 2<br>Some headers missing (HSTS, X‑XSS‑Protection)<br>Session cookies missing Secure/HttpOnly<br>Minor internal identifiers in comments/meta</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-c">C</span></td>
                                                   <td>60–69</td>
                                                   <td>High ≥ 2 or Medium ≥ 3<br>Key headers absent<br>Sensitive parameters/tokens exposed in responses<br>Weak session management (cookie attributes lacking)</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-d">D</span></td>
                                                   <td>50–59</td>
                                                   <td>Critical ≥ 1 or High ≥ 3<br>Severe auth/session attribute gaps<br>Debug/dev info exposed (stack traces, internal IPs)<br>Exposed admin consoles/config files</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-f">F</span></td>
                                                   <td>0–49</td>
                                                   <td>Widespread High vulnerabilities<br>No HTTPS or effectively disabled<br>Sensitive data in plaintext/exposed<br>Lack of security headers/session controls overall</td>
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
                                                                   {{ $vulnerabilities['critical'] ?? 0 }}</div>
                                                               <div class="text-muted">Critical</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-lg">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="text-h1 fw-bold">
                                                                   {{ $vulnerabilities['high'] ?? 0 }}</div>
                                                               <div class="text-muted">High</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-lg">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="text-h1 fw-bold">
                                                                   {{ $vulnerabilities['medium'] ?? 0 }}</div>
                                                               <div class="text-muted">Medium</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-lg">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="text-h1 fw-bold">
                                                                   {{ $vulnerabilities['low'] ?? 0 }}</div>
                                                               <div class="text-muted">Low</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-12 col-lg">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="text-h1 fw-bold">
                                                                   {{ $vulnerabilities['informational'] ?? 0 }}</div>
                                                               <div class="text-muted">Info</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       <!-- Vulnerability details -->
                                       @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Detected Vulnerabilities</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter">
                                                           <thead class="table-light">
                                                               <tr>
                                                                   <th>Vulnerability</th>
                                                                   <th>Risk Level</th>
                                                                   <th>Confidence</th>
                                                                   <th>Count</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($vulnerabilities['details'] as $vuln)
                                                                   <tr>
                                                                       <td style="min-width: 170px">
                                                                           <strong>{{ $vuln['name'] }}</strong>
                                                                           @if (!empty($vuln['description']))
                                                                               <br><small
                                                                                   class="text-muted">{{ Str::limit($vuln['description'], 200) }}</small>
                                                                           @endif
                                                                           @if (!empty($vuln['solution']))
                                                                               <br><small class="text-success">Solution:
                                                                                   {{ Str::limit($vuln['solution'], 150) }}</small>
                                                                           @endif
                                                                       </td>
                                                                       <td>
                                                                           @php
                                                                               $riskBadgeClass = match (
                                                                                   $vuln['risk']
                                                                               ) {
                                                                                   'critical'
                                                                                       => 'badge bg-red-lt text-red-lt-fg',
                                                                                   'high'
                                                                                       => 'badge bg-orange-lt text-orange-lt-fg',
                                                                                   'medium'
                                                                                       => 'badge bg-yellow-lt text-yellow-lt-fg',
                                                                                   'low'
                                                                                       => 'badge bg-blue-lt text-blue-lt-fg',
                                                                                   default
                                                                                       => 'badge bg-azure-lt text-azure-lt-fg',
                                                                               };
                                                                           @endphp
                                                                           <span
                                                                               class="{{ $riskBadgeClass }}">{{ ucfirst($vuln['risk']) }}</span>
                                                                       </td>
                                                                       <td>{{ $vuln['confidence'] ?? '-' }}</td>
                                                                       <td>{{ $vuln['instances'] ?? 0 }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Detected technology details -->
                                       @if (isset($technologies) && count($technologies) > 0)
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Detected Technology Stack</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter table-nowrap">
                                                           <thead class="table-light">
                                                               <tr>
                                                                   <th>Technology</th>
                                                                   <th>Category</th>
                                                                   <th>Description</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($technologies as $tech)
                                                                   <tr>
                                                                       <td><strong>{{ $tech['name'] }}</strong></td>
                                                                       <td>
                                                                           <span
                                                                               class="badge bg-azure-lt text-azure-lt-fg">{{ $tech['category'] }}</span>
                                                                       </td>
                                                                       <td>
                                                                           <small
                                                                               class="text-muted">{{ Str::limit($tech['description'], 200) }}</small>
                                                                       </td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Metric descriptions -->
                                       <div class="alert alert-info d-block">
                                           <h6>Metric Descriptions</h6>
                                           <p class="mb-2"><strong>Critical:</strong> Severe security vulnerabilities requiring immediate action (SQL Injection, XSS, RCE, etc.)</p>
                                           <p class="mb-2"><strong>High:</strong> Important vulnerabilities requiring prompt fixes (session management flaws, CSRF, etc.)</p>
                                           <p class="mb-2"><strong>Medium:</strong> Vulnerabilities recommended for security improvement (missing security headers, etc.)</p>
                                           <p class="mb-2"><strong>Low:</strong> Low-risk vulnerabilities (information disclosure, configuration issues, etc.)</p>
                                           <p class="mb-0"><strong>Informational:</strong> Items for reference that don't directly impact security</p>
                                       </div>

                                       <!-- Security improvement recommendations -->
                                       <div class="alert alert-info d-block">
                                           <h6>Security Improvement Recommendations</h6>
                                           <p class="mb-2"><strong>1. Security Header Configuration:</strong> Properly configure HSTS, X-Frame-Options, X-Content-Type-Options, X-XSS-Protection headers to defend against various attacks.</p>
                                           <p class="mb-2"><strong>2. Session Security:</strong> Set Secure, HttpOnly, SameSite attributes on all cookies to prevent session hijacking.</p>
                                           <p class="mb-2"><strong>3. Minimize Information Disclosure:</strong> Block exposure of server versions, framework information, debug messages, etc.</p>
                                           <p class="mb-2"><strong>4. HTTPS Implementation:</strong> Apply HTTPS to all pages and redirect HTTP to HTTPS.</p>
                                           <p class="mb-0"><strong>5. Regular Security Checks:</strong> Run security scans at least monthly to detect and respond to new vulnerabilities early.</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view security vulnerability scan results.</p>
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
                                       <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view Raw JSON data.</p>
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