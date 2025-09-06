@section('title')
    <title>📋 구조화 데이터 테스트 - JSON-LD Schema.org 검증 - DevTeam Test</title>
    <meta name="description"
        content="웹사이트의 JSON-LD, Schema.org 구조화 데이터를 자동 검증하고 Google Rich Results 적합성을 평가합니다. 오류와 경고를 탐지하고 개선 권장사항과 예시 스니펫을 제공합니다.">
    <meta name="keywords"
        content="구조화 데이터 검증, JSON-LD 테스트, Schema.org 검사, 구조화 마크업, 마이크로데이터, RDFa, Google Rich Snippets, SEO 최적화, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index, follow" />

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="📋 구조화 데이터 테스트 - JSON-LD Schema.org 검증 - DevTeam Test" />
    <meta property="og:description"
        content="웹페이지의 구조화 데이터를 분석하여 검색엔진 Rich Results 최적화를 지원합니다. JSON-LD 파싱, Schema.org 필수 필드 검증, 개선 가이드를 제공합니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 구조화 데이터 검사 결과" />
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="📋 구조화 데이터 테스트 - JSON-LD Schema.org 검증" />
    <meta name="twitter:description"
        content="JSON-LD와 Schema.org 구조화 데이터를 검증하고 Google Rich Results 적합성을 평가합니다. 오류, 경고, 개선 가이드 포함." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '구조화 데이터 테스트 - JSON-LD Schema.org 검증',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => '웹사이트의 JSON-LD, Schema.org 구조화 데이터를 검증하여 Google Rich Results 적합성을 평가합니다. 오류, 경고, 개선 가이드를 제공합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="📋 구조화 데이터 테스트" subtitle="JSON-LD / Schema.org 검증" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 (개별 컴포넌트) --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- URL 입력 폼 -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">홈페이지 주소</label>
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
                                                진행 중...
                                            @else
                                                테스트
                                            @endif
                                        </button>
                                    </div>
                                    @error('url')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    @if ($hasProOrAgencyPlan)
                                        <div class="mt-2">
                                            <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                                class="text-primary me-3">검사 예약</a>
                                            <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                                class="text-primary">스케쥴 등록</a>
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

                    {{-- 테스트 상태 (공통 컴포넌트) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- 개별 테스트만의 고유 내용 --}}
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'information')"
                                        class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}"
                                        data-bs-toggle="tab">테스트 정보</a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                        class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                        data-bs-toggle="tab">결과</a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                        class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                        data-bs-toggle="tab">데이터</a>
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

                                    <h4 class="mt-4">측정 항목</h4>
                                    <ul class="text-muted small">
                                        @foreach ($testInformation['details'] as $detail)
                                            <li>{{ $detail }}</li>
                                        @endforeach
                                    </ul>

                                    <h4 class="mt-4">검증 대상 스키마 타입</h4>
                                    <ul class="text-muted small">
                                        @foreach ($testInformation['test_items'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>

                                    <h4 class="mt-4">구조화 데이터의 이점</h4>
                                    <ul class="text-muted small">
                                        @foreach ($testInformation['benefits'] as $benefit)
                                            <li>{{ $benefit }}</li>
                                        @endforeach
                                    </ul>

                                    <h4 class="mt-4">측정 도구</h4>
                                    <p class="text-muted small">
                                        Playwright 기반 브라우저 자동화를 통해 실제 렌더링된 페이지에서 구조화 데이터를 수집하고,
                                        Google Rich Results Test 기준에 준하는 Schema.org 검증 규칙을 적용합니다.
                                        JSON-LD 파싱, 필수 필드 검사, Rich Results 적합성 평가를 수행합니다.
                                    </p>

                                    {{-- 등급 기준 안내 --}}
                                    <h4 class="mt-4">등급 기준</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>등급</th>
                                                    <th>점수</th>
                                                    <th>기준</th>
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

                                        <!-- 종합 요약 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">검사 결과 요약</h5>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">JSON-LD 블록</div>
                                                                <div class="h4 mb-0">
                                                                    {{ $totals['jsonLdBlocks'] ?? 0 }}개</div>
                                                                @if (($totals['jsonLdBlocks'] ?? 0) === 0)
                                                                    <span
                                                                        class="badge bg-red-lt text-red-lt-fg">미구현</span>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">스키마 아이템</div>
                                                                <div class="h4 mb-0">
                                                                    {{ $totals['jsonLdItems'] ?? 0 }}개</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">오류</div>
                                                                <div class="h4 mb-0 text-danger">
                                                                    {{ ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0) }}개
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">경고</div>
                                                                <div class="h4 mb-0 text-warning">
                                                                    {{ $totals['itemWarnings'] ?? 0 }}개
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3 mt-2">
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">Rich 유형</div>
                                                                @php $rich = $totals['richEligibleTypes'] ?? []; @endphp
                                                                <div class="h4 mb-0">
                                                                    {{ is_array($rich) ? count($rich) : 0 }}개</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">Microdata</div>
                                                                <div class="h4 mb-0">
                                                                    {{ !empty($totals['hasMicrodata']) ? '있음' : '없음' }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">RDFa</div>
                                                                <div class="h4 mb-0">
                                                                    {{ !empty($totals['hasRdfa']) ? '있음' : '없음' }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">판정 사유</div>
                                                                <div class="small">
                                                                    {{ $results['overall']['reason'] ?? '' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 권장 액션 -->
                                        @if (!empty($actions))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">권장 개선 사항</h5>
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

                                        <!-- 권장 JSON-LD 스니펫 -->
                                        @if (!empty($snippets))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">예시 JSON-LD 스니펫</h5>
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

                                        <!-- 타입 분포 -->
                                        @if (!empty($types))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">스키마 타입 분포</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter table-nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th>@type</th>
                                                                    <th>개수</th>
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

                                        <!-- JSON-LD 파싱 오류 상세 -->
                                        @if (!empty($parseErrors))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">JSON-LD 파싱 오류</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter">
                                                            <thead>
                                                                <tr>
                                                                    <th>블록</th>
                                                                    <th>메시지</th>
                                                                    <th>원문 미리보기</th>
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

                                        <!-- 항목별 오류/경고 상세 -->
                                        @if (!empty($perItem))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">항목별 상세 분석</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter">
                                                            <thead>
                                                                <tr>
                                                                    <th>소스 블록</th>
                                                                    <th>@type</th>
                                                                    <th>오류</th>
                                                                    <th>경고</th>
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

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>📊 측정 지표 설명</h6>
                                            <p class="mb-2"><strong>JSON-LD 블록:</strong> &lt;script
                                                type="application/ld+json"&gt; 태그의 개수</p>
                                            <p class="mb-2"><strong>스키마 아이템:</strong> 각 JSON-LD 블록 내 정의된 Schema.org
                                                객체 수</p>
                                            <p class="mb-2"><strong>파싱 오류:</strong> JSON 문법 오류로 파싱이 불가능한 경우</p>
                                            <p class="mb-2"><strong>항목 오류:</strong> 필수 필드 누락 등 Schema.org 규격 위반</p>
                                            <p class="mb-2"><strong>경고:</strong> 권장 필드 누락 또는 개선 가능 사항</p>
                                            <p class="mb-0"><strong>Rich 유형:</strong> Google Rich Results에서 지원하는 스키마
                                                타입 감지</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 구조화 데이터 개선 방안</h6>
                                            <p class="mb-2">1. <strong>기본 스키마 추가:</strong> Organization, WebSite,
                                                BreadcrumbList는 모든 사이트에 권장</p>
                                            <p class="mb-2">2. <strong>콘텐츠별 스키마:</strong> 페이지 성격에 맞는 Article,
                                                Product, FAQPage 등 추가</p>
                                            <p class="mb-2">3. <strong>필수 필드 완성:</strong> 각 스키마 타입별 required 속성은 반드시
                                                포함</p>
                                            <p class="mb-2">4. <strong>JSON-LD 형식 사용:</strong> Google이 권장하는 JSON-LD
                                                형식 우선 적용</p>
                                            <p class="mb-2">5. <strong>중첩 구조 활용:</strong> 연관된 정보는 중첩 객체로 구조화</p>
                                            <p class="mb-0">6. <strong>테스트 도구 활용:</strong> Google Rich Results Test로
                                                최종 검증 수행</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 구조화 데이터 검증 결과를 확인할 수 있습니다.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                    id="tabs-data">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Raw JSON Data</h5>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="copyJsonToClipboard()" title="JSON 데이터 복사">
                                                복사
                                            </button>
                                        </div>
                                        <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 Raw JSON 데이터를 확인할 수 있습니다.</p>
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
