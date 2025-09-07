@section('title')
    <title>♿ 웹 접근성 심화 테스트 - WCAG 2.1 · axe-core 기반 분석 - DevTeam Test</title>
    <meta name="description"
        content="axe-core 엔진을 활용해 WCAG 2.1 A/AA 기준을 심층 분석합니다. 키보드 탐색, 스크린 리더 호환성, ARIA 속성, 색상 대비, 대체 텍스트 등 웹 접근성의 핵심 요소를 평가하고 개선 가이드를 제공합니다.">
    <meta name="keywords"
        content="웹 접근성 심화 테스트, WCAG 2.1 검사, axe-core 접근성 분석, 키보드 탐색, 스크린 리더 호환성, ARIA 속성 검사, 색상 대비, 대체 텍스트, 접근성 레벨 A AA, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="♿ 웹 접근성 심화 테스트 - WCAG 2.1 · axe-core 기반 분석 - DevTeam Test" />
    <meta property="og:description"
        content="WCAG 2.1 A/AA 규칙에 따라 웹 접근성을 심층 평가합니다. 스크린 리더, 키보드 탐색, 색상 대비, ARIA 속성 등을 종합 분석하여 A+ 등급까지 인증 가능합니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 웹 접근성 심화 분석" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="웹 접근성 심화 테스트 - WCAG 2.1 · axe-core 기반 분석 - DevTeam Test" />
    <meta name="twitter:description"
        content="axe-core 엔진으로 WCAG 2.1 A/AA 기준을 준수하는지 검사하고, 키보드 탐색·스크린 리더·색상 대비 등 접근성 요소를 종합 분석합니다." />
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
    'name' => '웹 접근성 심화 테스트 - WCAG 2.1 · axe-core 기반 분석',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'axe-core 엔진으로 WCAG 2.1 A/AA 규칙을 검사하여 웹사이트 접근성을 심층 평가합니다. 스크린 리더, 키보드 탐색, ARIA 속성, 색상 대비 등 핵심 요소를 분석합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="♿ 웹 접근성 심화 테스트" subtitle="WCAG 2.1 기반 접근성 검사" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 (개별 컴포넌트) --}}
                    <div class="card mb-3">
                        <div class="card-body">
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
                                        class="nav-link {{ $mainTabActive == 'information' ? 'active' : '' }}">테스트
                                        정보</a>
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
                                        <strong>테스트 소요 시간:</strong> {{ $testInformation['test_duration'] }}<br>
                                        <strong>측정 도구:</strong> axe-core CLI (Deque Systems)<br>
                                        <strong>테스트 방식:</strong> {{ $testInformation['test_method'] }}
                                        <br><br>
                                        <strong>테스트 목적:</strong><br>
                                        이 테스트는 장애인, 고령자 등 모든 사용자가 웹사이트를 동등하게 이용할 수 있는지 평가합니다.
                                        웹 접근성은 법적 요구사항일 뿐만 아니라, 더 많은 사용자에게 서비스를 제공하고
                                        SEO 개선에도 도움이 되는 중요한 품질 지표입니다.
                                    </div>

                                    {{-- 등급 기준 안내 --}}
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>등급</th>
                                                    <th>점수</th>
                                                    <th>기준</th>
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

                                        <!-- 위반 사항 요약 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">접근성 위반 사항 요약</h5>
                                                <div class="row g-2">
                                                    <div class="col-6 col-md-3">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="h1 mb-1 text-danger">
                                                                    {{ $counts['critical'] ?? 0 }}</div>
                                                                <div class="text-muted">Critical</div>
                                                                <div class="small text-muted">심각한 접근성 차단</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="h1 mb-1 text-orange">
                                                                    {{ $counts['serious'] ?? 0 }}</div>
                                                                <div class="text-muted">Serious</div>
                                                                <div class="small text-muted">주요 기능 제한</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="h1 mb-1 text-warning">
                                                                    {{ $counts['moderate'] ?? 0 }}</div>
                                                                <div class="text-muted">Moderate</div>
                                                                <div class="small text-muted">부분적 불편</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="h1 mb-1 text-info">
                                                                    {{ $counts['minor'] ?? 0 }}</div>
                                                                <div class="text-muted">Minor</div>
                                                                <div class="small text-muted">경미한 문제</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-2 text-center">
                                                    <strong>총 위반 건수: {{ $counts['total'] ?? 0 }}건</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 위반 상세 목록 -->
                                        @if (!empty($violations))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">위반 상세 내역</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="100">중요도</th>
                                                                    <th>문제 설명</th>
                                                                    <th>영향받는 요소</th>
                                                                    <th>카테고리</th>
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
                                                                                <br><a
                                                                                    href="{{ $violation['helpUrl'] }}"
                                                                                    target="_blank" class="small">자세히
                                                                                    보기</a>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <small>{{ count($violation['nodes'] ?? []) }}개
                                                                                요소</small>
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
                                                            <small class="text-muted">총 {{ count($violations) }}개 중 상위
                                                                20개만 표시</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>접근성 위반 중요도 설명</h6>
                                            <p class="mb-2"><strong>Critical (심각):</strong> 사용자가 특정 기능을 전혀 사용할 수 없게
                                                만드는 문제. 예: 키보드 트랩, 필수 ARIA 속성 누락</p>
                                            <p class="mb-2"><strong>Serious (중대):</strong> 주요 기능 사용에 심각한 어려움을 주는 문제.
                                                예: 레이블 없는 폼 요소, 낮은 색상 대비</p>
                                            <p class="mb-2"><strong>Moderate (보통):</strong> 일부 사용자에게 불편을 주는 문제. 예:
                                                비표준 ARIA 사용, 불명확한 링크 텍스트</p>
                                            <p class="mb-0"><strong>Minor (경미):</strong> 사용자 경험을 약간 저하시키는 문제. 예: 비어있는
                                                헤딩, 중복 ID</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 접근성 개선 방안</h6>
                                            @if ($counts['critical'] > 0)
                                                <p class="mb-2">🔴 <strong>Critical 문제를 최우선으로 해결하세요.</strong> 키보드 트랩,
                                                    스크린 리더 차단 등은 즉시 수정이 필요합니다.</p>
                                            @endif
                                            @if ($counts['serious'] > 0)
                                                <p class="mb-2">🟠 <strong>Serious 문제 개선:</strong> 모든 폼 요소에 레이블 추가,
                                                    색상 대비 4.5:1 이상 확보, 이미지 대체 텍스트 제공</p>
                                            @endif
                                            <p class="mb-2">✅ <strong>기본 권장사항:</strong></p>
                                            <ul class="mb-0">
                                                <li>모든 인터랙티브 요소는 키보드로 접근 가능해야 함</li>
                                                <li>이미지, 아이콘에 적절한 대체 텍스트 제공</li>
                                                <li>페이지 구조를 나타내는 적절한 헤딩(h1~h6) 사용</li>
                                                <li>ARIA 속성을 올바르게 사용하여 스크린 리더 지원</li>
                                                <li>충분한 색상 대비 확보 (일반 텍스트 4.5:1, 큰 텍스트 3:1)</li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 웹 접근성 검사 결과를 확인할 수 있습니다.</p>
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
                                        <pre class="json-dump" id="json-data">{{ $currentTest->raw_json_pretty ?? '미리보기를 생성할 수 없습니다.' }}</pre>
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
