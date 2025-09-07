@section('title')
   <title>🔒 Security Headers Check – Analyze 6 Core Headers (CSP, XFO, HSTS…) | Web-PSQC</title>
   <meta name="description"
       content="Analyze six core security headers — CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, and HSTS — to diagnose issues and provide improvement guidance.">
   <meta name="keywords"
       content="security headers check, CSP analysis, X-Frame-Options, HSTS, Referrer-Policy, Permissions-Policy, web security scan, XSS protection, clickjacking prevention, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow">

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="Security Headers Check – Analyze 6 Core Headers" />
   <meta property="og:description" content="Automatically analyze six security headers to assess security posture and qualify for an A+ certificate." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Security Headers Check" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="Security Headers Check – CSP · XFO · HSTS | Web-PSQC" />
   <meta name="twitter:description" content="Check CSP, XFO, HSTS, and more. Automated diagnostics with improvement guidance." />
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
   'name' => 'Security Headers Check – Six Core Header Analysis',
   'url'  => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url'  => url('/'),
   ],
   'description' => 'Analyze six core security headers (CSP, X-Frame-Options, HSTS, etc.) to diagnose weaknesses and recommend fixes.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection
@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="🔒 Security Headers Check" subtitle="CSP / XFO / X-Content-Type / Referrer / Permissions / HSTS"
       :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

   <div class="page-body">
       <div class="container-xl">
           @include('inc.component.message')
           <div class="row">
               <div class="col-xl-8 d-block mb-2">
                   {{-- URL form (page-specific) --}}
                   <div class="card mb-3">
                       <div class="card-body">
                           <!-- URL input form -->
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
                                   <small class="text-muted">HEAD → GET fallback · redirect tracking (max 5)</small>

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
                       {{-- Schedule test form (shared component) --}}
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
                                   <h3>Comprehensive Check of 6 Core Security Headers</h3>
                                   <div class="text-muted small mt-1">
                                       Enable browser security features via HTTP response headers to protect your application from common attacks.
                                       <br><br>
                                       <strong>Tooling:</strong> custom Node.js script (axios HTTP client)
                                       <br>
                                       <strong>Goal:</strong> evaluate defenses against XSS, clickjacking, MIME sniffing, and data leakage
                                       <br><br>
                                       <strong>Headers evaluated:</strong>
                                       <br>
                                       • <strong>Content-Security-Policy (CSP)</strong> — restricts resource sources; mitigates XSS/third‑party script abuse
                                       <br>
                                       • <strong>X-Frame-Options / frame-ancestors</strong> — blocks framing; prevents clickjacking/phishing overlays
                                       <br>
                                       • <strong>X-Content-Type-Options</strong> — prevents MIME sniffing; mitigates incorrect execution
                                       <br>
                                       • <strong>Referrer-Policy</strong> — minimizes referrer data; prevents sensitive URL exposure
                                       <br>
                                       • <strong>Permissions-Policy</strong> — limits browser features (location, mic, camera) to protect privacy
                                       <br>
                                       • <strong>Strict-Transport-Security (HSTS)</strong> — forces HTTPS; prevents MITM/downgrade attacks
                                       <br><br>
                                       <strong>Where to configure:</strong> CDN (Cloudflare), web server (Nginx/Apache), application (e.g., Laravel)
                                       <br>
                                       Applying headers together yields the strongest protection.
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
                                                   <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                   <td>95–100</td>
                                                   <td>
                                                       <strong>Strong CSP</strong> (nonce/hash/strict-dynamic; no unsafe-*)<br>
                                                       XFO: DENY/SAMEORIGIN or limited frame-ancestors<br>
                                                       X-Content-Type: nosniff<br>
                                                       Referrer-Policy: strict-origin-when-cross-origin or better<br>
                                                       Permissions-Policy: unneeded features blocked<br>
                                                       HSTS: ≥ 6 months + include subdomains
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                   <td>85–94</td>
                                                   <td>
                                                       CSP present (weaker allowed) <strong>or</strong> 5 non‑CSP items strong<br>
                                                       XFO applied (or frame‑ancestors limited)<br>
                                                       X-Content-Type: nosniff<br>
                                                       Referrer‑Policy: recommended value<br>
                                                       Permissions‑Policy: basic restrictions<br>
                                                       HSTS: ≥ 6 months
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                   <td>70–84</td>
                                                   <td>
                                                       CSP none/weak<br>
                                                       XFO applied
                                                       <br>X-Content-Type: present (nosniff)
                                                       <br>Referrer‑Policy: okay/average<br>
                                                       Permissions‑Policy: partially restricted<br>
                                                       HSTS: short or no subdomains
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                   <td>55–69</td>
                                                   <td>
                                                       Some headers present<br>
                                                       CSP none/weak<br>
                                                       Referrer‑Policy weak<br>
                                                       X-Content-Type missing<br>
                                                       HSTS absent or very short
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                   <td>40–54</td>
                                                   <td>
                                                       Only 1–2 key headers present<br>
                                                       No CSP<br>
                                                       Referrer weak/absent<br>
                                                       Many other headers missing
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                   <td>0–39</td>
                                                   <td>
                                                       Security headers virtually absent<br>
                                                       No CSP/XFO/X-Content<br>
                                                       No Referrer‑Policy<br>
                                                       No HSTS
                                                   </td>
                                               </tr>
                                           </tbody>
                                       </table>
                                   </div>

                                   <div class="alert alert-info d-block mt-3">
                                       <strong>Grading policy:</strong> A+ requires a strong CSP. If CSP is absent, an A can still be awarded when the five non‑CSP headers (XFO, X‑Content‑Type‑Options, Referrer‑Policy, Permissions‑Policy, HSTS) are all strong.
                                   </div>
                               </div>

                               <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                   id="tabs-results">
                                   @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                       @php
                                           $report = $currentTest->results;
                                           $metrics = $currentTest->metrics;
                                           $grade = $currentTest->overall_grade ?? 'F';
                                           $score = $currentTest->overall_score ?? 0;

                                           $gradeClass = match ($grade) {
                                               'A+' => 'bg-green-lt text-green-lt-fg',
                                               'A' => 'bg-lime-lt text-lime-lt-fg',
                                               'B' => 'bg-blue-lt text-blue-lt-fg',
                                               'C' => 'bg-yellow-lt text-yellow-lt-fg',
                                               'D' => 'bg-orange-lt text-orange-lt-fg',
                                               'F' => 'bg-red-lt text-red-lt-fg',
                                               default => 'bg-secondary',
                                           };

                                           $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);

                                           // CSP and HSTS status analysis
                                           $csp = $metrics['headers']['csp'] ?? [];
                                           $hsts = $metrics['headers']['hsts'] ?? [];

                                           $cspBadge = 'bg-azure-lt text-azure-lt-fg';
                                           $cspText = 'CSP: None';
                                           if ($csp['present'] ?? false) {
                                               if ($csp['strong'] ?? false) {
                                                   $cspBadge = 'bg-green-lt text-green-lt-fg';
                                                   $cspText = 'CSP: Strong';
                                               } else {
                                                   $cspBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                   $cspText = 'CSP: Weak';
                                               }
                                           }

                                           $hstsBadge = 'bg-azure-lt text-azure-lt-fg';
                                           $hstsText = 'HSTS: None';
                                           if ($hsts['present'] ?? false) {
                                               $six = 15552000;
                                               $has6m = ($hsts['max_age'] ?? 0) >= $six;
                                               $inc = $hsts['include_sub_domains'] ?? false;
                                               if ($has6m && $inc) {
                                                   $hstsBadge = 'bg-green-lt text-green-lt-fg';
                                                   $hstsText = 'HSTS: 6+ months, includes subdomains';
                                               } elseif ($has6m) {
                                                   $hstsBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                   $hstsText = 'HSTS: 6+ months (no subdomains)';
                                               } else {
                                                   $hstsBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                   $hstsText = 'HSTS: Short duration';
                                               }
                                           }
                                       @endphp

                                       <x-test-shared.certificate :current-test="$currentTest" />

                                       <!-- Per-header score details -->
                                       <div class="card mb-4">
                                           <div class="card-header">
                                               <h3 class="card-title">Per‑Header Score Analysis</h3>
                                           </div>
                                           <div class="card-body">
                                               <div class="table-responsive">
                                                   <table class="table table-sm table-vcenter">
                                                       <thead>
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
                                                                   <td class="text-truncate"
                                                                       style="max-width: 400px;"
                                                                       title="{{ $item['value'] ?? '(not set)' }}">
                                                                       {{ $item['value'] ?? '(not set)' }}
                                                                   </td>
                                                                   <td>{{ round((($item['score'] ?? 0) * 100) / 60, 1) }}
                                                                   </td>
                                                               </tr>
                                                           @endforeach
                                                       </tbody>
                                                   </table>
                                               </div>
                                           </div>
                                       </div>

                                       <!-- URL info -->
                                       @if (isset($report['url']) || isset($report['finalUrl']))
                                           <div class="card mb-4">
                                               <div class="card-body">
                                                   <div class="row">
                                                       <div class="col-12">
                                                           <strong>Test URL:</strong> {{ $report['url'] ?? '' }}
                                                           @if (isset($report['finalUrl']) && $report['finalUrl'] !== $report['url'])
                                                               <br><strong>Final URL:</strong> {{ $report['finalUrl'] }}
                                                           @endif
                                                           @if (isset($report['status']))
                                                               <br><strong>HTTP status:</strong> {{ $report['status'] }}
                                                           @endif
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Grading rationale -->
                                       @if (!empty($report['reasons']))
                                           <div class="card mb-4">
                                               <div class="card-body">
                                                   <strong>Grading rationale:</strong><br>
                                                   {{ implode(' · ', $report['reasons']) }}
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Security headers explainer -->
                                       <div class="alert alert-info d-block">
                                           <h5>💡 Key security headers</h5>
                                           <p class="mb-2"><strong>Content-Security-Policy (CSP):</strong> restricts sources for executable resources; strongest defense against XSS and injection.</p>
                                           <p class="mb-2"><strong>X-Frame-Options:</strong> controls framing (iframe/frame/embed/object) to prevent clickjacking.</p>
                                           <p class="mb-2"><strong>X-Content-Type-Options:</strong> prevents MIME type sniffing; blocks scripts running under wrong types.</p>
                                           <p class="mb-2"><strong>Referrer-Policy:</strong> controls referrer information sent to other sites; prevents sensitive URL leakage.</p>
                                           <p class="mb-2"><strong>Permissions-Policy:</strong> limits access to browser features/APIs (camera, mic, geolocation, etc.).</p>
                                           <p class="mb-0"><strong>Strict-Transport-Security (HSTS):</strong> forces HTTPS; prevents MITM and protocol downgrade.</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No results yet</h5>
                                           <p class="mb-0">Run a test to view the security headers analysis.</p>
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