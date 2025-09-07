@section('title')
   <title>📋 Structured Data Test - JSON-LD Schema.org Validation | Web-PSQC</title>
   <meta name="description"
       content="Automatically validate JSON-LD and Schema.org structured data on websites and evaluate Google Rich Results compatibility. Detect errors and warnings while providing improvement recommendations and example snippets.">
   <meta name="keywords"
       content="structured data validation, JSON-LD test, Schema.org audit, structured markup, microdata, RDFa, Google Rich Snippets, SEO optimization, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index, follow" />

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="📋 Structured Data Test - JSON-LD Schema.org Validation | Web-PSQC" />
   <meta property="og:description"
       content="Analyze webpage structured data to support search engine Rich Results optimization. Provides JSON-LD parsing, Schema.org required field validation, and improvement guidance." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Structured Data Test Results" />
   @endif

   <!-- Twitter -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="📋 Structured Data Test - JSON-LD Schema.org Validation" />
   <meta name="twitter:description"
       content="Validate JSON-LD and Schema.org structured data and evaluate Google Rich Results compatibility. Includes errors, warnings, and improvement guidance." />
   @if ($setting && $setting->og_image)
       <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
   @endif

   {{-- JSON-LD: WebPage --}}
   <script type="application/ld+json">
{!! json_encode([
   '@' . 'context' => 'https://schema.org',
   '@type' => 'WebPage',
   'name' => 'Structured Data Test - JSON-LD Schema.org Validation',
   'url' => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url' => url('/'),
   ],
   'description' => 'Validate website JSON-LD and Schema.org structured data to evaluate Google Rich Results compatibility. Provides errors, warnings, and improvement guidance.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection

@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="📋 Structured Data Test" subtitle="JSON-LD / Schema.org Validation" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                   <h3>{{ $testInformation['title'] }}</h3>
                                   <div class="text-muted small mt-1">
                                       {{ $testInformation['description'] }}
                                   </div>

                                   <h4 class="mt-4">Measurement Items</h4>
                                   <ul class="text-muted small">
                                       @foreach ($testInformation['details'] as $detail)
                                           <li>{{ $detail }}</li>
                                       @endforeach
                                   </ul>

                                   <h4 class="mt-4">Validated Schema Types</h4>
                                   <ul class="text-muted small">
                                       @foreach ($testInformation['test_items'] as $item)
                                           <li>{{ $item }}</li>
                                       @endforeach
                                   </ul>

                                   <h4 class="mt-4">Benefits of Structured Data</h4>
                                   <ul class="text-muted small">
                                       @foreach ($testInformation['benefits'] as $benefit)
                                           <li>{{ $benefit }}</li>
                                       @endforeach
                                   </ul>

                                   <h4 class="mt-4">Testing Tool</h4>
                                   <p class="text-muted small">
                                       Playwright-based browser automation collects structured data from actually rendered pages and 
                                       applies Schema.org validation rules based on Google Rich Results Test standards.
                                       Performs JSON-LD parsing, required field validation, and Rich Results compatibility assessment.
                                   </p>

                                   {{-- Grade criteria guide --}}
                                   <h4 class="mt-4">Grade Criteria</h4>
                                   <div class="table-responsive">
                                       <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                           <thead>
                                               <tr>
                                                   <th>Grade</th>
                                                   <th>Score</th>
                                                   <th>Criteria</th>
                                               </tr>
                                           </thead>
                                           <tbody>
                                               @foreach ($gradeCriteria as $grade => $info)
                                                   <tr>
                                                       <td>
                                                           <span
                                                               class="badge {{ $grade === 'A+'
                                                                   ? 'badge-a-plus'
                                                                   : ($grade === 'A'
                                                                       ? 'badge-a'
                                                                       : ($grade === 'B'
                                                                           ? 'badge-b'
                                                                           : ($grade === 'C'
                                                                               ? 'badge-c'
                                                                               : ($grade === 'D'
                                                                                   ? 'badge-d'
                                                                                   : 'badge-f')))) }}">{{ $info['label'] }}</span>
                                                       </td>
                                                       <td>{{ $info['score'] }}</td>
                                                       <td>
                                                           @foreach ($info['criteria'] as $criterion)
                                                               • {{ $criterion }}<br>
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
                                   @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                       @php
                                           $results = $currentTest->results;
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
                                           $totals = $results['totals'] ?? [];
                                           $hasJsonLd = ($totals['jsonLdItems'] ?? 0) > 0;
                                           $parseErrors = $results['parseErrors'] ?? [];
                                           $perItem = $results['perItem'] ?? [];
                                           $actions = $results['actions'] ?? [];
                                           $snippets = $results['snippets'] ?? [];
                                           $types = $results['types'] ?? [];
                                       @endphp

                                       <x-test-shared.certificate :current-test="$currentTest" />

                                       <!-- Overall summary -->
                                       <div class="row mb-4">
                                           <div class="col-12">
                                               <h5 class="mb-3">Test Results Summary</h5>
                                               <div class="card">
                                                   <div class="card-body">
                                                       <div class="row g-3">
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">JSON-LD Blocks</div>
                                                               <div class="h4 mb-0">
                                                                   {{ $totals['jsonLdBlocks'] ?? 0 }} blocks</div>
                                                               @if (($totals['jsonLdBlocks'] ?? 0) === 0)
                                                                   <span
                                                                       class="badge bg-red-lt text-red-lt-fg">Not implemented</span>
                                                               @endif
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Schema Items</div>
                                                               <div class="h4 mb-0">
                                                                   {{ $totals['jsonLdItems'] ?? 0 }} items</div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Errors</div>
                                                               <div class="h4 mb-0 text-danger">
                                                                   {{ ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0) }} errors
                                                               </div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Warnings</div>
                                                               <div class="h4 mb-0 text-warning">
                                                                   {{ $totals['itemWarnings'] ?? 0 }} warnings
                                                               </div>
                                                           </div>
                                                       </div>
                                                       <div class="row g-3 mt-2">
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Rich Types</div>
                                                               @php $rich = $totals['richEligibleTypes'] ?? []; @endphp
                                                               <div class="h4 mb-0">
                                                                   {{ is_array($rich) ? count($rich) : 0 }} types</div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Microdata</div>
                                                               <div class="h4 mb-0">
                                                                   {{ !empty($totals['hasMicrodata']) ? 'Present' : 'None' }}
                                                               </div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">RDFa</div>
                                                               <div class="h4 mb-0">
                                                                   {{ !empty($totals['hasRdfa']) ? 'Present' : 'None' }}
                                                               </div>
                                                           </div>
                                                           <div class="col-md-3">
                                                               <div class="text-muted small">Assessment Reason</div>
                                                               <div class="small">
                                                                   {{ $results['overall']['reason'] ?? '' }}</div>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       <!-- Recommended actions -->
                                       @if (!empty($actions))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Recommended Improvements</h5>
                                                   <div class="card">
                                                       <div class="card-body">
                                                           <ul class="mb-0">
                                                               @foreach ($actions as $action)
                                                                   <li>{{ $action }}</li>
                                                               @endforeach
                                                           </ul>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Recommended JSON-LD snippets -->
                                       @if (!empty($snippets))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Example JSON-LD Snippets</h5>
                                                   @foreach ($snippets as $snippet)
                                                       <div class="card mb-3">
                                                           <div class="card-header">
                                                               <h6 class="card-title mb-0">
                                                                   {{ $snippet['title'] ?? ($snippet['type'] ?? 'JSON-LD') }}
                                                               </h6>
                                                           </div>
                                                           <div class="card-body">
                                                               <pre class="json-dump"><code>{!! json_encode($snippet['json'] ?? (object) [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</code></pre>
                                                           </div>
                                                       </div>
                                                   @endforeach
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Schema type distribution -->
                                       @if (!empty($types))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Schema Type Distribution</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter table-nowrap">
                                                           <thead>
                                                               <tr>
                                                                   <th>@type</th>
                                                                   <th>Count</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach (array_slice($types, 0, 10) as $row)
                                                                   <tr>
                                                                       <td>{{ $row['type'] }}</td>
                                                                       <td>{{ $row['count'] }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- JSON-LD parsing error details -->
                                       @if (!empty($parseErrors))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">JSON-LD Parsing Errors</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter">
                                                           <thead>
                                                               <tr>
                                                                   <th>Block</th>
                                                                   <th>Message</th>
                                                                   <th>Raw Preview</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($parseErrors as $pe)
                                                                   <tr>
                                                                       <td>{{ $pe['index'] }}</td>
                                                                       <td class="text-danger">{{ $pe['message'] }}
                                                                       </td>
                                                                       <td class="text-muted small">
                                                                           {{ \Illuminate\Support\Str::limit($pe['rawPreview'] ?? '', 100) }}
                                                                       </td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Per-item error/warning details -->
                                       @if (!empty($perItem))
                                           <div class="row mb-4">
                                               <div class="col-12">
                                                   <h5 class="mb-3">Per-Item Detailed Analysis</h5>
                                                   <div class="table-responsive">
                                                       <table class="table table-sm table-vcenter">
                                                           <thead>
                                                               <tr>
                                                                   <th>Source Block</th>
                                                                   <th>@type</th>
                                                                   <th>Errors</th>
                                                                   <th>Warnings</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($perItem as $item)
                                                                   <tr>
                                                                       <td>{{ $item['sourceIndex'] }}</td>
                                                                       <td>{{ implode(', ', $item['types'] ?? []) }}
                                                                       </td>
                                                                       <td>
                                                                           @if (!empty($item['errors']))
                                                                               <ul class="text-danger mb-0">
                                                                                   @foreach ($item['errors'] as $error)
                                                                                       <li>{{ $error }}</li>
                                                                                   @endforeach
                                                                               </ul>
                                                                           @else
                                                                               <span class="text-muted">-</span>
                                                                           @endif
                                                                       </td>
                                                                       <td>
                                                                           @if (!empty($item['warnings']))
                                                                               <ul class="text-warning mb-0">
                                                                                   @foreach ($item['warnings'] as $warning)
                                                                                       <li>{{ $warning }}</li>
                                                                                   @endforeach
                                                                               </ul>
                                                                           @else
                                                                               <span class="text-muted">-</span>
                                                                           @endif
                                                                       </td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       <!-- Measurement criteria descriptions -->
                                       <div class="alert alert-info d-block">
                                           <h6>Measurement Criteria Descriptions</h6>
                                           <p class="mb-2"><strong>JSON-LD Blocks:</strong> Number of &lt;script
                                               type="application/ld+json"&gt; tags</p>
                                           <p class="mb-2"><strong>Schema Items:</strong> Number of Schema.org
                                               objects defined within each JSON-LD block</p>
                                           <p class="mb-2"><strong>Parsing Errors:</strong> Cases where parsing is impossible due to JSON syntax errors</p>
                                           <p class="mb-2"><strong>Item Errors:</strong> Schema.org specification violations such as missing required fields</p>
                                           <p class="mb-2"><strong>Warnings:</strong> Missing recommended fields or items that can be improved</p>
                                           <p class="mb-0"><strong>Rich Types:</strong> Schema types supported by Google Rich Results detected</p>
                                       </div>

                                       <!-- Improvement recommendations -->
                                       <div class="alert alert-info d-block">
                                           <h6>Structured Data Improvement Recommendations</h6>
                                           <p class="mb-2">1. <strong>Add Basic Schemas:</strong> Organization, WebSite,
                                               BreadcrumbList are recommended for all sites</p>
                                           <p class="mb-2">2. <strong>Content-Specific Schemas:</strong> Add Article,
                                               Product, FAQPage etc. appropriate to page type</p>
                                           <p class="mb-2">3. <strong>Complete Required Fields:</strong> Always include required properties for each schema type</p>
                                           <p class="mb-2">4. <strong>Use JSON-LD Format:</strong> Prioritize JSON-LD
                                               format recommended by Google</p>
                                           <p class="mb-2">5. <strong>Utilize Nested Structure:</strong> Structure related information as nested objects</p>
                                           <p class="mb-0">6. <strong>Use Testing Tools:</strong> Perform final validation with Google Rich Results Test</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view structured data validation results.</p>
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