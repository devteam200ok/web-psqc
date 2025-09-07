@section('title')
   <title>♿ Advanced Web Accessibility Test - WCAG 2.1 · axe-core Analysis | Web-PSQC</title>
   <meta name="description"
       content="In-depth analysis of WCAG 2.1 A/AA standards using axe-core engine. Evaluate core web accessibility elements including keyboard navigation, screen reader compatibility, ARIA attributes, color contrast, alternative text, and more with improvement guidance.">
   <meta name="keywords"
       content="advanced web accessibility test, WCAG 2.1 audit, axe-core accessibility analysis, keyboard navigation, screen reader compatibility, ARIA attributes testing, color contrast, alternative text, accessibility level A AA, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow">

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="♿ Advanced Web Accessibility Test - WCAG 2.1 · axe-core Analysis | Web-PSQC" />
   <meta property="og:description"
       content="In-depth evaluation of web accessibility according to WCAG 2.1 A/AA rules. Comprehensive analysis of screen readers, keyboard navigation, color contrast, ARIA attributes with A+ grade certification available." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Advanced Web Accessibility Analysis" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="Advanced Web Accessibility Test - WCAG 2.1 · axe-core Analysis | Web-PSQC" />
   <meta name="twitter:description"
       content="Test WCAG 2.1 A/AA compliance using axe-core engine and comprehensively analyze accessibility elements including keyboard navigation, screen readers, and color contrast." />
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
   'name' => 'Advanced Web Accessibility Test - WCAG 2.1 · axe-core Analysis',
   'url'  => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url'  => url('/'),
   ],
   'description' => 'In-depth evaluation of website accessibility using axe-core engine to test WCAG 2.1 A/AA rules. Analyze core elements including screen readers, keyboard navigation, ARIA attributes, and color contrast.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection

@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="♿ Advanced Web Accessibility Test" subtitle="WCAG 2.1 Accessibility Assessment" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
       :ip-address="$ipAddress ?? null" />

   <div class="page-body">
       <div class="container-xl">
           @include('inc.component.message')
           <div class="row">
               <div class="col-xl-8 d-block mb-2">
                   {{-- URL form (individual component) --}}
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

                   {{-- Individual test-specific content --}}
                   <div class="card">
                       <div class="card-header">
                           <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                               <li class="nav-item">
                                   <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                       class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}">Test Info</a>
                               </li>
                               <li class="nav-item">
                                   <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                       class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}">Results</a>
                               </li>
                               <li class="nav-item">
                                   <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                       class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}">Data</a>
                               </li>
                           </ul>
                       </div>
                       <div class="card-body">
                           <div class="tab-content">
                               <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                   id="tabs-information">
                                   <h3>{{ $testInformation['title'] }}</h3>
                                   <div class="text-muted small mt-1">
                                       {{ $testInformation['description'] }}
                                       <br><br>
                                       @foreach ($testInformation['details'] as $detail)
                                           {{ $detail }}<br>
                                       @endforeach
                                       <br>
                                       <strong>Test Duration:</strong> {{ $testInformation['test_duration'] }}<br>
                                       <strong>Testing Tool:</strong> axe-core CLI (Deque Systems)<br>
                                       <strong>Test Method:</strong> {{ $testInformation['test_method'] }}
                                       <br><br>
                                       <strong>Testing Objectives:</strong><br>
                                       This test evaluates whether all users, including people with disabilities and elderly users, can equally access and use the website.
                                       Web accessibility is not only a legal requirement but also an important quality indicator that helps serve more users and
                                       improve SEO.
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
                                               @foreach ($gradeCriteria as $grade => $criteria)
                                                   <tr>
                                                       <td>
                                                           <span
                                                               class="badge badge-grade {{ $grade === 'A+'
                                                                   ? 'badge-a-plus'
                                                                   : (strtolower($grade) === 'a'
                                                                       ? 'badge-a'
                                                                       : (strtolower($grade) === 'b'
                                                                           ? 'badge-b'
                                                                           : (strtolower($grade) === 'c'
                                                                               ? 'badge-c'
                                                                               : (strtolower($grade) === 'd'
                                                                                   ? 'badge-d'
                                                                                   : 'badge-f')))) }}">{{ $grade }}</span>
                                                       </td>
                                                       <td>{{ $criteria['score'] }}</td>
                                                       <td>
                                                           @foreach ($criteria['criteria'] as $criterion)
                                                               {{ $criterion }}<br>
                                                           @endforeach
                                                       </td>
                                                   </tr>
                                               @endforeach
                                           </tbody>
                                       </table>
                                   </div>
                               </div>

                               <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                   id="tabs-results">
                                   @if ($currentTest && $currentTest->status === 'completed' && $currentTest->metrics)
                                       @php
                                           $counts = $currentTest->metrics['violations_count'] ?? [];
                                           $violations = $currentTest->metrics['violations_detail'] ?? [];
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

                                       <!-- Accessibility violations summary -->
                                       <div class="row mb-4">
                                           <div class="col-12">
                                               <h5 class="mb-3">Accessibility Violations Summary</h5>
                                               <div class="row g-2">
                                                   <div class="col-6 col-md-3">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="h1 mb-1 text-danger">
                                                                   {{ $counts['critical'] ?? 0 }}</div>
                                                               <div class="text-muted">Critical</div>
                                                               <div class="small text-muted">Severe accessibility barriers</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-3">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="h1 mb-1 text-orange">
                                                                   {{ $counts['serious'] ?? 0 }}</div>
                                                               <div class="text-muted">Serious</div>
                                                               <div class="small text-muted">Major functionality limits</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-3">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="h1 mb-1 text-warning">
                                                                   {{ $counts['moderate'] ?? 0 }}</div>
                                                               <div class="text-muted">Moderate</div>
                                                               <div class="small text-muted">Partial inconvenience</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-3">
                                                       <div class="card card-sm">
                                                           <div class="card-body text-center">
                                                               <div class="h1 mb-1 text-info">
                                                                   {{ $counts['minor'] ?? 0 }}</div>
                                                               <div class="text-muted">Minor</div>
                                                               <div class="small text-muted">Minor issues</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="mt-2 text-center">
                                                   <strong>Total Violations: {{ $counts['total'] ?? 0 }} issues</strong>
                                               </div>
                                           </div>
                                       </div>

                                       <!-- Detailed violations list -->
                                       @if (!empty($violations))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Detailed Violation Report</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter">
                                                           <thead class="table-light">
                                                               <tr>
                                                                   <th width="100">Severity</th>
                                                                   <th>Issue Description</th>
                                                                   <th>Affected Elements</th>
                                                                   <th>Category</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach (array_slice($violations, 0, 20) as $violation)
                                                                   @php
                                                                       $impactClass = match (
                                                                           strtolower($violation['impact'])
                                                                       ) {
                                                                           'critical'
                                                                               => 'badge bg-red-lt text-red-lt-fg',
                                                                           'serious'
                                                                               => 'badge bg-orange-lt text-orange-lt-fg',
                                                                           'moderate'
                                                                               => 'badge bg-yellow-lt text-yellow-lt-fg',
                                                                           default
                                                                               => 'badge bg-cyan-lt text-cyan-lt-fg',
                                                                       };
                                                                   @endphp
                                                                   <tr>
                                                                       <td>
                                                                           <span class="{{ $impactClass }}">
                                                                               {{ ucfirst($violation['impact']) }}
                                                                           </span>
                                                                       </td>
                                                                       <td>
                                                                           <strong>{{ $violation['help'] }}</strong>
                                                                           @if (!empty($violation['desc']))
                                                                               <br><small
                                                                                   class="text-muted">{{ $violation['desc'] }}</small>
                                                                           @endif
                                                                           @if (!empty($violation['helpUrl']))
                                                                               <br>
                                                                                   href="{{ $violation['helpUrl'] }}"
                                                                                   target="_blank" class="small">Learn more</a>
                                                                           @endif
                                                                       </td>
                                                                       <td>
                                                                           <small>{{ count($violation['nodes'] ?? []) }} elements</small>
                                                                           @if (!empty($violation['nodes'][0]['target']))
                                                                               <br><code
                                                                                   class="small">{{ implode(' ', array_slice($violation['nodes'][0]['target'], 0, 2)) }}</code>
                                                                           @endif
                                                                       </td>
                                                                       <td>
                                                                           @if (!empty($violation['tags']))
                                                                               @foreach (array_slice($violation['tags'], 0, 3) as $tag)
                                                                                   <span
                                                                                       class="badge bg-azure-lt text-azure-lt-fg small mb-2 me-2">{{ $tag }}</span><br>
                                                                               @endforeach
                                                                           @endif
                                                                       </td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                                   @if (count($violations) > 20)
                                                       <div class="text-center mt-2">
                                                           <small class="text-muted">Showing top 20 of {{ count($violations) }} total issues</small>
                                                       </div>
                                                   @endif
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Measurement criteria descriptions -->
                                       <div class="alert alert-info d-block">
                                           <h6>Accessibility Violation Severity Descriptions</h6>
                                           <p class="mb-2"><strong>Critical:</strong> Issues that completely prevent users from accessing specific functionality. Examples: keyboard traps, missing essential ARIA attributes</p>
                                           <p class="mb-2"><strong>Serious:</strong> Issues that cause serious difficulty in using major functionality. Examples: form elements without labels, low color contrast</p>
                                           <p class="mb-2"><strong>Moderate:</strong> Issues that cause inconvenience for some users. Examples: non-standard ARIA usage, unclear link text</p>
                                           <p class="mb-0"><strong>Minor:</strong> Issues that slightly degrade user experience. Examples: empty headings, duplicate IDs</p>
                                       </div>

                                       <!-- Improvement recommendations -->
                                       <div class="alert alert-info d-block">
                                           <h6>Accessibility Improvement Recommendations</h6>
                                           @if ($counts['critical'] > 0)
                                               <p class="mb-2">🔴 <strong>Prioritize fixing Critical issues.</strong> Keyboard traps and screen reader blocking issues require immediate attention.</p>
                                           @endif
                                           @if ($counts['serious'] > 0)
                                               <p class="mb-2">🟠 <strong>Serious issue improvements:</strong> Add labels to all form elements, ensure color contrast of 4.5:1 or higher, provide image alternative text</p>
                                           @endif
                                           <p class="mb-2">✅ <strong>Basic Recommendations:</strong></p>
                                           <ul class="mb-0">
                                               <li>All interactive elements must be keyboard accessible</li>
                                               <li>Provide appropriate alternative text for images and icons</li>
                                               <li>Use proper heading structure (h1~h6) to indicate page hierarchy</li>
                                               <li>Use ARIA attributes correctly to support screen readers</li>
                                               <li>Ensure sufficient color contrast (4.5:1 for normal text, 3:1 for large text)</li>
                                           </ul>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view web accessibility assessment results.</p>
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