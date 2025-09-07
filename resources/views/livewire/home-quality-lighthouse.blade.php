@section('title')
   <title>🔍 Comprehensive Quality Test - Lighthouse Performance · SEO · Accessibility Analysis | Web-PSQC</title>
   <meta name="description"
       content="Comprehensive website quality analysis using Google Lighthouse covering Performance, Accessibility, Best Practices, and SEO metrics. Includes Core Web Vitals (FCP, LCP, CLS) evaluation and provides A+ to F grade certificates for overall website quality and user experience assessment.">
   <meta name="keywords"
       content="Lighthouse comprehensive test, website quality analysis, performance optimization, SEO audit, accessibility evaluation, Best Practices, Core Web Vitals, FCP, LCP, CLS, web standards, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow">

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="🔍 Comprehensive Quality Test - Lighthouse Performance · SEO · Accessibility Analysis | Web-PSQC" />
   <meta property="og:description"
       content="Comprehensive website quality analysis using Google Lighthouse. Evaluate Performance, Accessibility, SEO, and Best Practices with integrated scoring and receive A+ grade certificates." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Lighthouse Comprehensive Quality Test Results" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="🔍 Comprehensive Quality Test - Lighthouse Performance · SEO · Accessibility Analysis" />
   <meta name="twitter:description"
       content="Google Lighthouse-based comprehensive website quality testing. Evaluate Performance, Accessibility, SEO, and Best Practices with integrated assessment and Core Web Vitals-included certificates." />
   @if ($setting && $setting->og_image)
       <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
   @endif

   {{-- JSON-LD: WebPage --}}
   <script type="application/ld+json">
{!! json_encode([
   '@' . 'context' => 'https://schema.org',
   '@type' => 'WebPage',
   'name' => 'Comprehensive Quality Test - Lighthouse Performance · SEO · Accessibility Analysis',
   'url' => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url' => url('/'),
   ],
   'description' => 'Google Lighthouse-based comprehensive measurement of website Performance, Accessibility, SEO, and Best Practices to issue web quality certificates. Includes Core Web Vitals (FCP, LCP, CLS).',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection
@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="🔍 Comprehensive Quality Test" subtitle="Lighthouse Performance+SEO+Accessibility Analysis" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
       :ip-address="$ipAddress ?? null" />

   <div class="page-body">
       <div class="container-xl">
           @include('inc.component.message')
           <div class="row">
               <div class="col-xl-8 d-block mb-2">
                   {{-- URL form (individual component) --}}
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
                                   <h3>Google Lighthouse - Comprehensive Website Quality Measurement Tool</h3>
                                   <div class="text-muted small mt-1">
                                       Google Lighthouse is an open-source web quality measurement tool developed by Google, built into Chrome DevTools, 
                                       that comprehensively analyzes website performance, accessibility, SEO, and best practices compliance.
                                       <br><br>
                                       <strong>Measurement Tool & Environment</strong><br>
                                       • Latest Lighthouse version (Chrome browser engine based)<br>
                                       • Real browser environment simulation with Headless Chrome<br>
                                       • Mobile 3G/4G network and mid-tier device performance baseline<br>
                                       • Core Web Vitals measurement reflecting real user experience
                                       <br><br>
                                       <strong>Testing Objectives</strong><br>
                                       • Assess overall website quality level<br>
                                       • Identify performance bottlenecks affecting user experience<br>
                                       • Verify Search Engine Optimization (SEO) compliance<br>
                                       • Check Web Content Accessibility Guidelines (WCAG) adherence<br>
                                       • Evaluate web standards and security best practices implementation
                                       <br><br>
                                       <strong>4 Core Assessment Areas</strong><br>
                                       1. <strong>Performance</strong>: Page loading speed, Core Web Vitals, resource optimization<br>
                                       2. <strong>Accessibility</strong>: ARIA labels, color contrast, keyboard navigation support<br>
                                       3. <strong>Best Practices</strong>: HTTPS usage, console errors, image aspect ratios<br>
                                       4. <strong>SEO</strong>: Meta tags, structured data, mobile-friendliness
                                       <br><br>
                                       Testing takes approximately <strong>30 seconds to 2 minutes</strong>, varying based on network conditions and website complexity.
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
                                                   <td>95~100</td>
                                                   <td>Performance: 90+<br>Accessibility: 90+<br>Best Practices: 90+<br>SEO: 90+<br>Overall Average: 95+</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-a">A</span></td>
                                                   <td>90~94</td>
                                                   <td>Performance: 85+<br>Accessibility: 85+<br>Best Practices: 85+<br>SEO: 85+<br>Overall Average: 90+</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-b">B</span></td>
                                                   <td>80~89</td>
                                                   <td>Performance: 75+<br>Accessibility: 75+<br>Best Practices: 75+<br>SEO: 75+<br>Overall Average: 80+</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-c">C</span></td>
                                                   <td>70~79</td>
                                                   <td>Performance: 65+<br>Accessibility: 65+<br>Best Practices: 65+<br>SEO: 65+<br>Overall Average: 70+</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-d">D</span></td>
                                                   <td>60~69</td>
                                                   <td>Performance: 55+<br>Accessibility: 55+<br>Best Practices: 55+<br>SEO: 55+<br>Overall Average: 60+</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge badge-f">F</span></td>
                                                   <td>0~59</td>
                                                   <td>Below the above criteria</td>
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
                                           $metrics = $currentTest->metrics ?? [];
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
                                           $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                       @endphp

                                       <x-test-shared.certificate :current-test="$currentTest" />

                                       <!-- 4 core area scores -->
                                       <div class="row mb-4">
                                           <div class="col-md-3">
                                               <div class="card text-center">
                                                   <div class="card-body">
                                                       <h3>{{ $metrics['performance_score'] ?? 'N/A' }}</h3>
                                                       <p>Performance</p>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="col-md-3">
                                               <div class="card text-center">
                                                   <div class="card-body">
                                                       <h3>{{ $metrics['accessibility_score'] ?? 'N/A' }}</h3>
                                                       <p>Accessibility</p>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="col-md-3">
                                               <div class="card text-center">
                                                   <div class="card-body">
                                                       <h3>{{ $metrics['best_practices_score'] ?? 'N/A' }}</h3>
                                                       <p>Best Practices</p>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="col-md-3">
                                               <div class="card text-center">
                                                   <div class="card-body">
                                                       <h3>{{ $metrics['seo_score'] ?? 'N/A' }}</h3>
                                                       <p>SEO</p>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       <!-- Core Web Vitals -->
                                       @if(isset($results['audits']))
                                           <div class="card mb-4">
                                               <div class="card-header">
                                                   <h5 class="card-title mb-0">Core Web Vitals</h5>
                                               </div>
                                               <div class="card-body">
                                                   <div class="table-responsive">
                                                       <table class="table table-sm">
                                                           @if(isset($results['audits']['first-contentful-paint']))
                                                               <tr>
                                                                   <th>First Contentful Paint (FCP)</th>
                                                                   <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                           @if(isset($results['audits']['largest-contentful-paint']))
                                                               <tr>
                                                                   <th>Largest Contentful Paint (LCP)</th>
                                                                   <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                           @if(isset($results['audits']['cumulative-layout-shift']))
                                                               <tr>
                                                                   <th>Cumulative Layout Shift (CLS)</th>
                                                                   <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                           @if(isset($results['audits']['speed-index']))
                                                               <tr>
                                                                   <th>Speed Index</th>
                                                                   <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                           @if(isset($results['audits']['total-blocking-time']))
                                                               <tr>
                                                                   <th>Total Blocking Time (TBT)</th>
                                                                   <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                           @if(isset($results['audits']['interactive']))
                                                               <tr>
                                                                   <th>Time to Interactive (TTI)</th>
                                                                   <td>{{ $results['audits']['interactive']['displayValue'] ?? 'N/A' }}</td>
                                                               </tr>
                                                           @endif
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>

                                           <!-- Improvement opportunities -->
                                           @php
                                               $opportunities = collect($results['audits'])->filter(function($audit) {
                                                   return isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 0;
                                               })->sortByDesc('details.overallSavingsMs');
                                           @endphp
                                           @if($opportunities->count() > 0)
                                               <div class="card mb-4">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Improvement Opportunities</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="table-responsive">
                                                           <table class="table table-sm">
                                                               @foreach($opportunities->take(10) as $key => $opportunity)
                                                                   <tr>
                                                                       <td>{{ $opportunity['title'] ?? $key }}</td>
                                                                       <td>{{ $opportunity['displayValue'] ?? '' }}</td>
                                                                       <td class="text-end">{{ round($opportunity['details']['overallSavingsMs'] ?? 0) }}ms potential improvement</td>
                                                                   </tr>
                                                               @endforeach
                                                           </table>
                                                       </div>
                                                   </div>
                                               </div>
                                           @endif

                                           <!-- Diagnostic results -->
                                           @php
                                               $diagnostics = collect($results['audits'])->filter(function($audit) {
                                                   return isset($audit['details']['type']) && $audit['details']['type'] === 'table' && isset($audit['score']) && $audit['score'] < 1;
                                               });
                                           @endphp
                                           @if($diagnostics->count() > 0)
                                               <div class="card mb-4">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Diagnostic Results</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="table-responsive">
                                                           <table class="table table-sm">
                                                               @foreach($diagnostics->take(10) as $key => $diagnostic)
                                                                   <tr>
                                                                       <td>{{ $diagnostic['title'] ?? $key }}</td>
                                                                       <td>{{ $diagnostic['displayValue'] ?? $diagnostic['description'] ?? '' }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </table>
                                                       </div>
                                                   </div>
                                               </div>
                                           @endif
                                       @endif

                                       <!-- Metric descriptions -->
                                       <div class="alert alert-info d-block">
                                           <h5>Core Web Vitals Metric Descriptions</h5>
                                           <p class="mb-2"><strong>FCP (First Contentful Paint):</strong> Time from page load start until the first content is rendered on screen</p>
                                           <p class="mb-2"><strong>LCP (Largest Contentful Paint):</strong> Time when the largest content element in the viewport is rendered. Recommended under 2.5 seconds</p>
                                           <p class="mb-2"><strong>CLS (Cumulative Layout Shift):</strong> Cumulative score of unexpected layout shifts during page load. Recommended under 0.1</p>
                                           <p class="mb-2"><strong>TBT (Total Blocking Time):</strong> Total time the main thread was blocked between FCP and TTI. Recommended under 200ms</p>
                                           <p class="mb-0"><strong>TTI (Time to Interactive):</strong> Time when the page becomes fully interactive. Recommended under 3.8 seconds</p>
                                       </div>

                                       <!-- Improvement recommendations -->
                                       <div class="alert alert-info d-block">
                                           <h5>Performance Improvement Recommendations</h5>
                                           <p class="mb-2">📌 <strong>Image Optimization:</strong> Use WebP format, proper sizing, apply lazy loading</p>
                                           <p class="mb-2">📌 <strong>JavaScript Optimization:</strong> Remove unnecessary scripts, code splitting, asynchronous loading</p>
                                           <p class="mb-2">📌 <strong>CSS Optimization:</strong> Remove unused CSS, inline critical CSS, file compression</p>
                                           <p class="mb-2">📌 <strong>Caching Strategy:</strong> Set browser cache headers, utilize CDN, implement Service Workers</p>
                                           <p class="mb-2">📌 <strong>Server Response Improvement:</strong> Optimize TTFB, Gzip/Brotli compression, HTTP/2 utilization</p>
                                           <p class="mb-0">📌 <strong>Rendering Optimization:</strong> Remove render-blocking resources, font optimization, minimize Critical Path</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view comprehensive quality analysis results.</p>
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
                                       <pre class="json-dump" id="json-data">{{ $currentTest->raw_json_pretty ?? 'Cannot generate preview.' }}</pre>
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