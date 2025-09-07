@section('title')
   <title>📋 Metadata Test - SEO Meta Tag Quality & Optimization Analysis | Web-PSQC</title>
   <meta name="description"
       content="Comprehensively analyze core metadata including Title, Description, Canonical, Open Graph, Twitter Cards, and more. Evaluate SEO completeness and receive quality certificates up to A+ grade.">
   <meta name="keywords"
       content="metadata test, SEO meta tag analysis, Title optimization, Meta Description, Open Graph tags, Twitter Cards, Canonical URL, Hreflang setup, SEO quality certification, Web-PSQC">
   <meta name="author" content="DevTeam Co., Ltd.">
   <meta name="robots" content="index,follow" />

   <link rel="canonical" href="{{ url()->current() }}" />

   <!-- Open Graph -->
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />
   <meta property="og:site_name" content="Web-PSQC" />
   <meta property="og:title" content="📋 Metadata Test - SEO Meta Tag Quality & Optimization Analysis | Web-PSQC" />
   <meta property="og:description"
       content="Analyze webpage Title, Description, OG, Twitter Cards and other metadata to diagnose SEO optimization level. Get improvement suggestions and quality certificates." />
   @php $setting = \App\Models\Setting::first(); @endphp
   @if ($setting && $setting->og_image)
       <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
       <meta property="og:image:alt" content="Web-PSQC Metadata Test Results" />
   @endif

   <!-- Twitter Card -->
   <meta name="twitter:card" content="summary_large_image" />
   <meta name="twitter:title" content="📋 Metadata Test - SEO Meta Tag Quality & Optimization Analysis" />
   <meta name="twitter:description"
       content="Test SEO core metadata completeness to evaluate search optimization status. Check Title, Description, Canonical, OG, Twitter Cards analysis results." />
   @if ($setting && $setting->og_image)
       <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
   @endif

   {{-- JSON-LD: WebPage --}}
   <script type="application/ld+json">
{!! json_encode([
   '@' . 'context' => 'https://schema.org',
   '@type' => 'WebPage',
   'name' => 'Metadata Test - SEO Meta Tag Quality & Optimization Analysis',
   'url' => url()->current(),
   'isPartOf' => [
       '@type' => 'WebSite',
       'name' => 'Web-PSQC',
       'url' => url('/'),
   ],
   'description' => 'Comprehensively analyze core metadata including Title, Description, Canonical, OG, Twitter Cards, and more. Evaluate SEO completeness and receive quality certificates up to A+ grade.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
   </script>
@endsection

@section('css')
   @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
   {{-- Header (shared component) --}}
   <x-test-shared.header title="📋 Metadata Test" subtitle="SEO Meta Tag Completeness Analysis" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                   <label class="form-label">Page URL</label>
                                   <div class="input-group">
                                       <input type="url" wire:model="url" wire:keydown.enter="runTest"
                                           class="form-control @error('url') is-invalid @enderror"
                                           placeholder="https://www.example.com/page"
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

                   {{-- Main tabs --}}
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
                                   <h3>Metadata Completeness Testing Tool</h3>
                                   <div class="text-muted small mt-1">
                                       Analyze webpage metadata completeness using <strong>Meta Inspector CLI</strong>.
                                       <br><br>
                                       <strong>Testing Tool & Method:</strong><br>
                                       • Node.js-based headless browser engine for actual page rendering<br>
                                       • Meta tag extraction and analysis through HTML parsing<br>
                                       • Score calculation based on SEO best practices (100-point scale)<br><br>
                                       
                                       <strong>Testing Objectives:</strong><br>
                                       • Evaluate metadata quality for Search Engine Optimization (SEO)<br>
                                       • Verify preview quality for social media sharing<br>
                                       • Validate Canonical settings for duplicate content prevention<br>
                                       • Check Hreflang configuration for multilingual support<br><br>
                                       
                                       <strong>Test Items:</strong><br>
                                       • <strong>Title Tag:</strong> Page title length and quality<br>
                                       • <strong>Meta Description:</strong> Page description length and quality<br>
                                       • <strong>Open Graph:</strong> Social media sharing optimization for Facebook, LinkedIn, etc.<br>
                                       • <strong>Twitter Cards:</strong> Card format optimization for Twitter sharing<br>
                                       • <strong>Canonical URL:</strong> Representative URL setting for duplicate content prevention<br>
                                       • <strong>Hreflang Tags:</strong> Multilingual page connection settings<br><br>
                                       
                                       <strong>Web-PSQC</strong> provides in-depth analysis of single-page metadata and
                                       specific improvement suggestions to maximize SEO performance.
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
                                                   <td>95~100</td>
                                                   <td>Title optimal length (50-60 chars), Description optimal length (120-160 chars)<br>
                                                       Perfect Open Graph implementation, Perfect Twitter Cards implementation<br>
                                                       Accurate Canonical URL, All metadata optimized</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                   <td>85~94</td>
                                                   <td>Title/Description acceptable range (30-80 chars/80-200 chars)<br>
                                                       Perfect Open Graph implementation, Accurate Canonical URL<br>
                                                       Twitter Cards optional</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                   <td>75~84</td>
                                                   <td>Basic Title/Description written<br>
                                                       Basic Open Graph tags applied<br>
                                                       Some metadata omissions allowed</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                   <td>65~74</td>
                                                   <td>Inappropriate Title/Description length<br>
                                                       Incomplete Open Graph (missing key tags)<br>
                                                       Inaccurate or missing Canonical URL</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                   <td>50~64</td>
                                                   <td>Serious Title/Description length issues<br>
                                                       Insufficient basic Open Graph tags<br>
                                                       Insufficient basic metadata</td>
                                               </tr>
                                               <tr>
                                                   <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                   <td>0~49</td>
                                                   <td>Title/Description not written<br>
                                                       Open Graph absent<br>
                                                       Overall metadata unimplemented</td>
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
                                           $metadata = $results['metadata'] ?? [];
                                           $analysis = $results['analysis'] ?? [];
                                           $summary = $results['summary'] ?? [];
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

                                       {{-- Overall status --}}
                                       <div class="card mb-4">
                                           <div class="card-header">
                                               <h5 class="card-title mb-0">Overall Status</h5>
                                           </div>
                                           <div class="card-body">
                                               <div class="row g-3">
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">{{ $summary['titleLength'] ?? 0 }}</div>
                                                           <div class="small text-muted">Title Length</div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">{{ $summary['descriptionLength'] ?? 0 }}</div>
                                                           <div class="small text-muted">Description Length</div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">{{ $summary['openGraphFields'] ?? 0 }}</div>
                                                           <div class="small text-muted">OG Tags</div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">{{ $summary['twitterCardFields'] ?? 0 }}</div>
                                                           <div class="small text-muted">Twitter Tags</div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">{{ $summary['hreflangCount'] ?? 0 }}</div>
                                                           <div class="small text-muted">Hreflang</div>
                                                       </div>
                                                   </div>
                                                   <div class="col-6 col-md-2">
                                                       <div class="text-center">
                                                           <div class="h4 mb-0">
                                                               @if ($summary['hasCanonical'] ?? false)
                                                                   ✅
                                                               @else
                                                                   ❌
                                                               @endif
                                                           </div>
                                                           <div class="small text-muted">Canonical</div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="mt-3">
                                                   <div class="small text-muted mb-2">
                                                       <strong>Assessment Reason:</strong> {{ $results['grade']['reason'] ?? '' }}
                                                   </div>
                                                   <div class="small text-muted">
                                                       <strong>Final URL:</strong> {{ $results['finalUrl'] ?? $results['url'] ?? '' }}
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       {{-- Detected issues --}}
                                       @if (!empty($results['issues']))
                                           <div class="card mb-4">
                                               <div class="card-header bg-warning-lt">
                                                   <h5 class="card-title mb-0">Detected Issues</h5>
                                               </div>
                                               <div class="card-body">
                                                   <ul class="mb-0">
                                                       @foreach ($results['issues'] as $issue)
                                                           <li class="mb-1">{{ $issue }}</li>
                                                       @endforeach
                                                   </ul>
                                               </div>
                                           </div>
                                       @endif

                                       {{-- Metadata preview --}}
                                       <div class="card mb-4">
                                           <div class="card-header">
                                               <h5 class="card-title mb-0">Metadata Preview</h5>
                                           </div>
                                           <div class="card-body">
                                               <div class="mb-3">
                                                   <div class="fw-bold mb-1">Title ({{ $summary['titleLength'] ?? 0 }} chars)</div>
                                                   <div class="text-muted">{{ $metadata['title'] ?: 'No title' }}</div>
                                               </div>
                                               <div class="mb-3">
                                                   <div class="fw-bold mb-1">Description ({{ $summary['descriptionLength'] ?? 0 }} chars)</div>
                                                   <div class="text-muted">{{ $metadata['description'] ?: 'No description' }}</div>
                                               </div>
                                               <div>
                                                   <div class="fw-bold mb-1">Canonical URL</div>
                                                   <div class="text-muted">{{ $metadata['canonical'] ?: 'No canonical URL' }}</div>
                                               </div>
                                           </div>
                                       </div>

                                       {{-- Detailed analysis --}}
                                       <div class="row g-3 mb-4">
                                           <div class="col-md-6">
                                               <div class="card">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Title & Description</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="mb-3">
                                                           <div class="d-flex justify-content-between align-items-center mb-2">
                                                               <span class="fw-bold">Title</span>
                                                               <span class="small">
                                                                   @if ($analysis['title']['isEmpty'] ?? true)
                                                                       <span class="badge bg-red-lt text-red-lt-fg">None</span>
                                                                   @elseif ($analysis['title']['isOptimal'] ?? false)
                                                                       <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                   @elseif ($analysis['title']['isAcceptable'] ?? false)
                                                                       <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                   @else
                                                                       <span class="badge bg-red-lt text-red-lt-fg">Inappropriate</span>
                                                                   @endif
                                                               </span>
                                                           </div>
                                                           <div class="small text-muted">
                                                               Length: {{ $analysis['title']['length'] ?? 0 }} chars (Acceptable: 30-80 chars, Optimal: 50-60 chars)
                                                           </div>
                                                       </div>
                                                       
                                                       <div class="mb-0">
                                                           <div class="d-flex justify-content-between align-items-center mb-2">
                                                               <span class="fw-bold">Description</span>
                                                               <span class="small">
                                                                   @if ($analysis['description']['isEmpty'] ?? true)
                                                                       <span class="badge bg-red-lt text-red-lt-fg">None</span>
                                                                   @elseif ($analysis['description']['isOptimal'] ?? false)
                                                                       <span class="badge bg-green-lt text-green-lt-fg">Optimal</span>
                                                                   @elseif ($analysis['description']['isAcceptable'] ?? false)
                                                                       <span class="badge bg-yellow-lt text-yellow-lt-fg">Acceptable</span>
                                                                   @else
                                                                       <span class="badge bg-red-lt text-red-lt-fg">Inappropriate</span>
                                                                   @endif
                                                               </span>
                                                           </div>
                                                           <div class="small text-muted">
                                                               Length: {{ $analysis['description']['length'] ?? 0 }} chars (Acceptable: 80-200 chars, Optimal: 120-160 chars)
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>

                                           <div class="col-md-6">
                                               <div class="card">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Open Graph</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="d-flex justify-content-between align-items-center mb-2">
                                                           <span class="fw-bold">Status</span>
                                                           <span>
                                                               @if ($analysis['openGraph']['isPerfect'] ?? false)
                                                                   <span class="badge bg-green-lt text-green-lt-fg">Perfect</span>
                                                               @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                                                   <span class="badge bg-yellow-lt text-yellow-lt-fg">Basic</span>
                                                               @else
                                                                   <span class="badge bg-red-lt text-red-lt-fg">Insufficient</span>
                                                               @endif
                                                           </span>
                                                       </div>
                                                       <div class="small text-muted mb-2">
                                                           Configured tags: {{ $summary['openGraphFields'] ?? 0 }} tags
                                                       </div>
                                                       @if (!empty($analysis['openGraph']['missing']))
                                                           <div class="small text-danger">
                                                               Missing: {{ implode(', ', $analysis['openGraph']['missing']) }}
                                                           </div>
                                                       @endif
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       <div class="row g-3 mb-4">
                                           <div class="col-md-6">
                                               <div class="card">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Twitter Cards</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="d-flex justify-content-between align-items-center mb-2">
                                                           <span class="fw-bold">Status</span>
                                                           <span>
                                                               @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                                                   <span class="badge bg-green-lt text-green-lt-fg">Perfect</span>
                                                               @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                                                   <span class="badge bg-yellow-lt text-yellow-lt-fg">Basic</span>
                                                               @else
                                                                   <span class="badge bg-red-lt text-red-lt-fg">Insufficient</span>
                                                               @endif
                                                           </span>
                                                       </div>
                                                       <div class="small text-muted mb-2">
                                                           Configured tags: {{ $summary['twitterCardFields'] ?? 0 }} tags
                                                       </div>
                                                       @if (!empty($analysis['twitterCards']['missing']))
                                                           <div class="small text-danger">
                                                               Missing: {{ implode(', ', $analysis['twitterCards']['missing']) }}
                                                           </div>
                                                       @endif
                                                   </div>
                                               </div>
                                           </div>

                                           <div class="col-md-6">
                                               <div class="card">
                                                   <div class="card-header">
                                                       <h5 class="card-title mb-0">Other Settings</h5>
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="row g-2">
                                                           <div class="col-6">
                                                               <div class="text-center">
                                                                   <div class="mb-1">
                                                                       @if ($summary['hasCanonical'] ?? false)
                                                                           <span class="badge bg-green-lt text-green-lt-fg">✓</span>
                                                                       @else
                                                                           <span class="badge bg-red-lt text-red-lt-fg">✗</span>
                                                                       @endif
                                                                   </div>
                                                                   <div class="small text-muted">Canonical</div>
                                                               </div>
                                                           </div>
                                                           <div class="col-6">
                                                               <div class="text-center">
                                                                   <div class="mb-1">
                                                                       @if (($summary['hreflangCount'] ?? 0) > 0)
                                                                           <span class="badge bg-green-lt text-green-lt-fg">{{ $summary['hreflangCount'] }}</span>
                                                                       @else
                                                                           <span class="badge bg-secondary">0</span>
                                                                       @endif
                                                                   </div>
                                                                   <div class="small text-muted">Hreflang</div>
                                                               </div>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>

                                       {{-- Open Graph details --}}
                                       @if (!empty($metadata['openGraph']))
                                           <div class="card mb-4">
                                               <div class="card-header">
                                                   <h5 class="card-title mb-0">Open Graph Details</h5>
                                               </div>
                                               <div class="card-body">
                                                   <div class="table-responsive">
                                                       <table class="table table-sm">
                                                           <thead>
                                                               <tr>
                                                                   <th>Property</th>
                                                                   <th>Content</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($metadata['openGraph'] as $prop => $content)
                                                                   <tr>
                                                                       <td><code>og:{{ $prop }}</code></td>
                                                                       <td class="text-break">{{ $content }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       {{-- Twitter Cards details --}}
                                       @if (!empty($metadata['twitterCards']))
                                           <div class="card mb-4">
                                               <div class="card-header">
                                                   <h5 class="card-title mb-0">Twitter Cards Details</h5>
                                               </div>
                                               <div class="card-body">
                                                   <div class="table-responsive">
                                                       <table class="table table-sm">
                                                           <thead>
                                                               <tr>
                                                                   <th>Name</th>
                                                                   <th>Content</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($metadata['twitterCards'] as $name => $content)
                                                                   <tr>
                                                                       <td><code>twitter:{{ $name }}</code></td>
                                                                       <td class="text-break">{{ $content }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       {{-- Hreflang details --}}
                                       @if (!empty($metadata['hreflangs']))
                                           <div class="card mb-4">
                                               <div class="card-header">
                                                   <h5 class="card-title mb-0">Hreflang Settings</h5>
                                               </div>
                                               <div class="card-body">
                                                   <div class="table-responsive">
                                                       <table class="table table-sm">
                                                           <thead>
                                                               <tr>
                                                                   <th>Language</th>
                                                                   <th>URL</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                               @foreach ($metadata['hreflangs'] as $hreflang)
                                                                   <tr>
                                                                       <td>
                                                                           <code>{{ $hreflang['lang'] }}</code>
                                                                           @if ($hreflang['lang'] === 'x-default')
                                                                               <span class="badge bg-primary-lt ms-1">default</span>
                                                                           @endif
                                                                       </td>
                                                                       <td class="text-break">{{ $hreflang['href'] }}</td>
                                                                   </tr>
                                                               @endforeach
                                                           </tbody>
                                                       </table>
                                                   </div>
                                               </div>
                                           </div>
                                       @endif

                                       {{-- Improvement suggestions --}}
                                       @if (!empty($improvementSuggestions))
                                           <div class="alert alert-info d-block">
                                               <h5>Improvement Suggestions</h5>
                                               <ul class="mb-0">
                                                   @foreach ($improvementSuggestions as $suggestion)
                                                       <li>{{ $suggestion }}</li>
                                                   @endforeach
                                               </ul>
                                           </div>
                                       @endif

                                       {{-- Metadata descriptions --}}
                                       <div class="alert alert-info d-block">
                                           <h5>Metadata Indicator Descriptions</h5>
                                           <p class="mb-2"><strong>Title Tag:</strong> Page title displayed in search results and browser tabs. Optimal length is 50-60 characters and should include key keywords.</p>
                                           <p class="mb-2"><strong>Meta Description:</strong> Page description shown in search results. Optimal length is 120-160 characters and should encourage user clicks.</p>
                                           <p class="mb-2"><strong>Open Graph:</strong> Information displayed when sharing links on social media like Facebook and LinkedIn. Title, description, image, and url are required.</p>
                                           <p class="mb-2"><strong>Twitter Cards:</strong> Card-format information displayed when sharing links on Twitter. Card, title, and description are basic requirements.</p>
                                           <p class="mb-2"><strong>Canonical URL:</strong> Representative URL designation to prevent duplicate content issues. Essential when identical content exists at multiple URLs.</p>
                                           <p class="mb-0"><strong>Hreflang Tags:</strong> Multilingual page connection settings. Informs search engines about different language versions of the same content.</p>
                                       </div>
                                   @else
                                       <div class="alert alert-info d-block">
                                           <h5>No Results Yet</h5>
                                           <p class="mb-0">Run a test to view metadata analysis results.</p>
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