@section('title')
   <title>🌐 Browser Compatibility Test - Chrome · Firefox · Safari Cross-Browser Testing | Web-PSQC</title>
   <meta name="description"
       content="Playwright-based precision testing of website JavaScript and CSS compatibility across Chrome, Firefox, and Safari (WebKit) browsers. Detects cross-browser errors and issues A+ grade certificates for browser compatibility assessment.">
   <meta name="keywords"
       content="browser compatibility test, cross-browser testing, Chrome compatibility, Firefox compatibility, Safari WebKit compatibility, JavaScript errors, CSS rendering, Playwright testing, web standards testing, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow">

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="🌐 Browser Compatibility Test - Chrome · Firefox · Safari Cross-Browser Testing" />
   <meta property="og:description"
       content="Evaluate cross-browser compatibility across Chrome, Firefox, and Safari (WebKit) environments by distinguishing first-party and third-party code errors. Web standards-based diagnostics and improvement guidance provided." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Browser Compatibility Test Results" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="Browser Compatibility Test - Chrome · Firefox · Safari Cross-Browser Testing" />
   <meta name="twitter:description"
       content="Playwright-based browser automation for precision JavaScript and CSS error detection with A+ grade compatibility certificates available." />
   @if ($setting && $setting->og_image)
       <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
   @endif

   {{-- JSON-LD: WebPage --}}
   <script type="application/ld+json">
{!! json_encode([
   '@' . 'context' => 'https://schema.org',
   '@type' => 'WebPage',
   'name' => 'Browser Compatibility Test - Chrome · Firefox · Safari Cross-Browser Testing',
   'url' => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url' => url('/'),
   ],
   'description' => 'Test web compatibility across the 3 major browsers: Chrome, Firefox, and Safari (WebKit). Playwright-based automation distinguishes CSS and JavaScript errors to provide accurate diagnostics and improvement guidance.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection

@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header 
       title="🌐 Browser Compatibility Test" 
       subtitle="Chrome · Firefox · Safari Cross-Browser Testing" 
       :user-plan-usage="$userPlanUsage" 
       :ip-usage="$ipUsage ?? null"
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
                                   <h3>Chrome, Firefox, Safari Cross-Browser Compatibility Testing</h3>
                                   <div class="text-muted small mt-1">
                                       Cross-browser compatibility testing to verify your website functions properly across major browsers.
                                       <br><br>
                                       <strong>Testing Tool:</strong> Playwright (Browser automation tool developed by Microsoft)<br>
                                       • Chromium (Base engine for Chrome, Edge)<br>
                                       • Firefox (Gecko engine)<br>
                                       • WebKit (Base engine for Safari)
                                       <br><br>
                                       <strong>Testing Objectives:</strong><br>
                                       • Verify website functions properly across different browser environments<br>
                                       • Detect JavaScript runtime errors and separate first-party/third-party code<br>
                                       • Identify CSS parsing and rendering errors<br>
                                       • Proactively discover browser-specific compatibility issues
                                       <br><br>
                                       <strong>Measurement Criteria:</strong><br>
                                       • Normal page loading (document.readyState === 'complete')<br>
                                       • JavaScript error collection (classified as first-party/third-party/noise)<br>
                                       • CSS error collection (based on parser error patterns)<br>
                                       • Browser-specific User-Agent information
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
                                                   <td>90~100</td>
                                                   <td>Chrome/Firefox/Safari <strong>all normal</strong><br>
                                                       First-party JS errors: <strong>0</strong><br>
                                                       CSS rendering errors: <strong>0</strong></td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                   <td>80~89</td>
                                                   <td>Major browser support <strong>good</strong> (2+ normal)<br>
                                                       First-party JS errors <strong>≤ 1</strong><br>
                                                       CSS errors <strong>≤ 1</strong></td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                   <td>70~79</td>
                                                   <td>Browser-specific <strong>minor differences</strong> (2+ normal)<br>
                                                       First-party JS errors <strong>≤ 3</strong><br>
                                                       CSS errors <strong>≤ 3</strong></td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                   <td>60~69</td>
                                                   <td>Some browsers have <strong>degraded functionality</strong> (1+ normal)<br>
                                                       First-party JS errors <strong>≤ 6</strong><br>
                                                       CSS errors <strong>≤ 6</strong></td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                   <td>50~59</td>
                                                   <td><strong>Multiple</strong> compatibility issues<br>
                                                       First-party JS errors <strong>≤ 10</strong><br>
                                                       CSS errors <strong>≤ 10</strong></td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                   <td>0~49</td>
                                                   <td>Major browsers <strong>fail to function properly</strong><br>
                                                       First-party JS errors <strong>10+</strong><br>
                                                       CSS errors <strong>10+</strong></td>
                                               </tr>
                                           </tbody>
                                       </table>
                                   </div>
                               </div>

                               <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                   id="tabs-results">
                                   @if ($currentTest && $currentTest->status === 'completed' && $report)
                                       @php
                                           $grade = $report['overall']['grade'] ?? 'F';
                                           $gradeClass = match ($grade) {
                                               'A+' => 'badge bg-green-lt text-green-lt-fg',
                                               'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                               'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                               'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                               'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                               'F' => 'badge bg-red-lt text-red-lt-fg',
                                               default => 'badge bg-secondary',
                                           };

                                           $totals = $report['totals'] ?? [];
                                           $okCount = $totals['okCount'] ?? 0;
                                           $jsFirstPartyTotal = $totals['jsFirstPartyTotal'] ?? 0;
                                           $jsThirdPartyTotal = $totals['jsThirdPartyTotal'] ?? null;
                                           $jsNoiseTotal = $totals['jsNoiseTotal'] ?? null;
                                           $cssTotal = $totals['cssTotal'] ?? 0;
                                           $strictMode = !empty($report['strictMode']);
                                           
                                           $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                       @endphp

                                       <x-test-shared.certificate :current-test="$currentTest" />

                                       {{-- Overall results --}}
                                       <div class="row mb-4">
                                           <div class="col-12">
                                               <h5 class="mb-3">Overall Results</h5>
                                               <div class="card">
                                                   <div class="card-body">
                                                       <div class="row g-3">
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Working Browsers</div>
                                                               <div class="h3 mb-0">{{ $okCount }}/3</div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">JS Errors (First-party)</div>
                                                               <div class="h3 mb-0">{{ $jsFirstPartyTotal }}</div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">CSS Errors</div>
                                                               <div class="h3 mb-0">{{ $cssTotal }}</div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Test Mode</div>
                                                               <div class="h5 mb-0">{{ $strictMode ? 'Strict Mode' : 'Standard Mode' }}</div>
                                                           </div>
                                                       </div>
                                                       @if (!is_null($jsThirdPartyTotal) || !is_null($jsNoiseTotal))
                                                           <div class="mt-3 pt-3 border-top">
                                                               <div class="text-muted small">Additional Information</div>
                                                               @if (!is_null($jsThirdPartyTotal))
                                                                   Third-party JS errors: {{ $jsThirdPartyTotal }}
                                                               @endif
                                                               @if (!is_null($jsNoiseTotal))
                                                                   · Noise: {{ $jsNoiseTotal }}
                                                               @endif
                                                           </div>
                                                       @endif
                                                       <div class="mt-2 text-muted small">
                                                           Assessment Reason: {{ $report['overall']['reason'] ?? '' }}
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       {{-- Detailed results by browser --}}
                                       <div class="row mb-4">
                                           <div class="col-12">
                                               <h5 class="mb-3">Detailed Results by Browser</h5>
                                               <div class="table-responsive">
                                                   <table class="table table-sm table-vcenter">
                                                       <thead>
                                                           <tr>
                                                               <th>Browser</th>
                                                               <th>Normal Load</th>
                                                               <th>JS Errors (First-party)</th>
                                                               <th>CSS Errors</th>
                                                               <th>User-Agent</th>
                                                           </tr>
                                                       </thead>
                                                       <tbody>
                                                           @foreach ($report['perBrowser'] as $browser)
                                                               @php
                                                                   $jsFirst = $browser['jsFirstPartyCount'] ?? ($browser['jsErrorCount'] ?? 0);
                                                                   $jsThird = $browser['jsThirdPartyCount'] ?? null;
                                                                   $jsNoise = $browser['jsNoiseCount'] ?? null;
                                                                   $browserOk = !empty($browser['ok']);
                                                               @endphp
                                                               <tr>
                                                                   <td><strong>{{ $browser['browser'] ?? '' }}</strong></td>
                                                                   <td>
                                                                       @if ($browserOk)
                                                                           <span class="badge bg-green-lt text-green-lt-fg">Normal</span>
                                                                       @else
                                                                           <span class="badge bg-red-lt text-red-lt-fg">Error</span>
                                                                       @endif
                                                                   </td>
                                                                   <td>
                                                                       <strong>{{ $jsFirst }}</strong>
                                                                       @if (!is_null($jsThird) || !is_null($jsNoise))
                                                                           <div class="small text-muted">
                                                                               @if (!is_null($jsThird))
                                                                                   Third-party: {{ $jsThird }}
                                                                               @endif
                                                                               @if (!is_null($jsNoise))
                                                                                   · Noise: {{ $jsNoise }}
                                                                               @endif
                                                                           </div>
                                                                       @endif
                                                                   </td>
                                                                   <td>{{ $browser['cssErrorCount'] ?? 0 }}</td>
                                                                   <td>
                                                                       <div class="text-truncate small text-muted" style="max-width: 300px;">
                                                                           {{ $browser['userAgent'] ?? '' }}
                                                                       </div>
                                                                   </td>
                                                               </tr>

                                                               {{-- Navigation errors --}}
                                                               @if (!empty($browser['navError']))
                                                                   <tr>
                                                                       <td colspan="5">
                                                                           <div class="alert alert-danger d-block mb-0">
                                                                               <strong>Navigation Error:</strong> {{ $browser['navError'] }}
                                                                           </div>
                                                                       </td>
                                                                   </tr>
                                                               @endif

                                                               {{-- Error samples --}}
                                                               @php
                                                                   $samples = $browser['samples'] ?? [];
                                                                   $hasJsFirstParty = !empty($samples['jsFirstParty']);
                                                                   $hasJsThirdParty = !empty($samples['jsThirdParty']);
                                                                   $hasJsNoise = !empty($samples['jsNoise']);
                                                                   $hasCss = !empty($samples['css']);
                                                               @endphp

                                                               @if ($hasJsFirstParty || $hasJsThirdParty || $hasJsNoise || $hasCss)
                                                                   <tr>
                                                                       <td colspan="5">
                                                                           <div class="p-3 bg-light">
                                                                               <div class="row g-3">
                                                                                   @if ($hasJsFirstParty)
                                                                                       <div class="col-md-6">
                                                                                           <h6 class="mb-2">JS Error Samples (First-party)</h6>
                                                                                           <ul class="small mb-0">
                                                                                               @foreach (array_slice($samples['jsFirstParty'], 0, 5) as $error)
                                                                                                   <li class="text-danger">{{ $error }}</li>
                                                                                               @endforeach
                                                                                           </ul>
                                                                                       </div>
                                                                                   @endif

                                                                                   @if ($hasJsThirdParty)
                                                                                       <div class="col-md-6">
                                                                                           <h6 class="mb-2">JS Error Samples (Third-party)</h6>
                                                                                           <ul class="small mb-0">
                                                                                               @foreach (array_slice($samples['jsThirdParty'], 0, 5) as $error)
                                                                                                   <li class="text-warning">{{ $error }}</li>
                                                                                               @endforeach
                                                                                           </ul>
                                                                                       </div>
                                                                                   @endif

                                                                                   @if ($hasCss)
                                                                                       <div class="col-12">
                                                                                           <h6 class="mb-2">CSS Error Samples</h6>
                                                                                           <ul class="small mb-0">
                                                                                               @foreach (array_slice($samples['css'], 0, 5) as $error)
                                                                                                   <li class="text-warning">{{ $error }}</li>
                                                                                               @endforeach
                                                                                           </ul>
                                                                                       </div>
                                                                                   @endif

                                                                                   @if ($hasJsNoise)
                                                                                       <div class="col-12">
                                                                                           <h6 class="mb-2">Noise Samples (Ignored Items)</h6>
                                                                                           <ul class="small mb-0">
                                                                                               @foreach (array_slice($samples['jsNoise'], 0, 3) as $error)
                                                                                                   <li class="text-muted">{{ $error }}</li>
                                                                                               @endforeach
                                                                                           </ul>
                                                                                       </div>
                                                                                   @endif
                                                                               </div>
                                                                           </div>
                                                                       </td>
                                                                   </tr>
                                                               @endif
                                                           @endforeach
                                                       </tbody>
                                                   </table>
                                               </div>
                                           </div>
                                       </div>

                                       {{-- Measurement criteria descriptions --}}
                                       <div class="alert alert-info d-block">
                                           <h6>Measurement Criteria Descriptions</h6>
                                           <p class="mb-2"><strong>Normal Load:</strong> Successful page entry + document.readyState === 'complete' + no browser crashes</p>
                                           <p class="mb-2"><strong>First-party JS Errors:</strong> JavaScript runtime errors occurring on the test target domain</p>
                                           <p class="mb-2"><strong>Third-party JS Errors:</strong> JavaScript errors from external domains (ads, analytics tools, etc.)</p>
                                           <p class="mb-2"><strong>CSS Errors:</strong> CSS parsing failures, invalid property values, unsupported properties, etc.</p>
                                           <p class="mb-0"><strong>Noise:</strong> Ignorable browser messages like SameSite cookie warnings</p>
                                       </div>

                                       {{-- Improvement recommendations --}}
                                       <div class="alert alert-info d-block">
                                           <h6>Browser Compatibility Improvement Recommendations</h6>
                                           @if ($grade === 'F' || $grade === 'D')
                                               <p class="mb-2">🔴 <strong>Serious compatibility issues detected.</strong></p>
                                               <p class="mb-1">• Check and fix JavaScript errors in the console</p>
                                               <p class="mb-1">• Add CSS vendor prefixes (-webkit-, -moz-, etc.)</p>
                                               <p class="mb-1">• Use polyfills to improve legacy browser support</p>
                                               <p class="mb-1">• Check browser support status on Can I Use website</p>
                                           @elseif ($grade === 'C' || $grade === 'B')
                                               <p class="mb-2">🟡 <strong>Minor issues detected in some browsers.</strong></p>
                                               <p class="mb-1">• Check errors in browser-specific developer tools</p>
                                               <p class="mb-1">• Automate CSS compatibility with Autoprefixer</p>
                                               <p class="mb-1">• Transpile modern JavaScript with Babel</p>
                                           @else
                                               <p class="mb-2">🟢 <strong>Excellent browser compatibility!</strong></p>
                                               <p class="mb-1">• Run compatibility tests regularly</p>
                                               <p class="mb-1">• Check browser support when adding new features</p>
                                               <p class="mb-1">• Consider performance optimization and accessibility improvements</p>
                                           @endif
                                       </div>
                                   @elseif ($error)
                                       <div class="alert alert-danger d-block">
                                           <h5>Error Occurred</h5>
                                           <p class="mb-0">{!! nl2br(e($error)) !!}</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view browser compatibility results.</p>
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