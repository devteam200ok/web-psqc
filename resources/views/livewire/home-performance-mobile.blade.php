@section('title')
    <title>📱 모바일 성능 테스트 - Playwright · iPhone/Galaxy · 6종 기기 성능 평가 | DevTeam Test</title>
    <meta name="description" content="Playwright로 iPhone SE·11·15 Pro, Galaxy S9+·S20 Ultra, Pixel 5 등 6종 기기를 시뮬레이션합니다. Median 응답시간, JS 런타임 에러, 렌더 폭 초과를 종합 분석해 모바일 사용자 경험을 A+ 등급까지 평가/인증합니다.">
    <meta name="keywords" content="모바일 성능 테스트, Playwright, iPhone 테스트, Galaxy 테스트, 모바일 웹 최적화, JS 런타임 에러, 렌더링 폭 초과, 반응형 테스트, 모바일 UX, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="모바일 성능 테스트 - Playwright · iPhone/Galaxy · 6종 기기 성능 평가 | DevTeam Test" />
    <meta property="og:description" content="실제 모바일 기기 환경을 시뮬레이션하여 Median 응답시간·JS 에러·렌더 폭 초과를 분석합니다. 모바일 성능을 진단하고 A+ 등급까지 인증서를 발급받으세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test - 모바일 성능 테스트" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="모바일 성능 테스트 - Playwright · iPhone/Galaxy · 6종 기기 성능 평가 | DevTeam Test" />
    <meta name="twitter:description" content="6종 기기 시뮬레이션으로 모바일 사용자 경험을 정밀 진단. Median·JS 에러·렌더 폭 초과까지 한 번에 확인." />
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
    'name' => '모바일 성능 테스트 - Playwright · iPhone/Galaxy · 6종 기기 성능 평가',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'Playwright 기반 6종 모바일 기기 시뮬레이션으로 Median 응답시간·JS 에러·렌더 폭 초과를 분석하고 모바일 성능을 등급화합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="📱 모바일 성능 테스트" subtitle="Playwright · iPhone/Galaxy · 6종 기기 성능 평가" :user-plan-usage="$userPlanUsage"
        :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- URL 입력 폼 -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">테스트 URL</label>
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
                                                테스트 중...
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

                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">
                                        측정 항목: <strong>Median(재방문)</strong> · <strong>JS 런타임 에러(자사/외부/고유)</strong> ·
                                        <strong>렌더 폭 초과</strong>
                                    </small>
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

                    {{-- 메인 컨텐츠 카드 --}}
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
                                <!-- 테스트 정보 탭 -->
                                <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                    id="tabs-information">

                                    <h3>모바일 성능 테스트란?</h3>
                                    <div class="text-muted small mt-1 mb-4">
                                        <strong>Playwright</strong>를 사용하여 실제 모바일 기기 환경을 시뮬레이션하고,
                                        웹사이트의 모바일 성능과 안정성을 정밀하게 측정합니다.
                                    </div>

                                    <!-- 측정 개요 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📊 측정 개요</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>도구</strong>: Playwright (헤드리스 브라우저, CPU 스로틀 ×4 적용)</li>
                                            <li><strong>실행</strong>: 기기별 총 <strong>4회</strong> 실행 → <strong>1회 웜업
                                                    제외</strong>, 나머지 <strong>3회의 중간값(Median)</strong> 사용</li>
                                            <li><strong>주요 지표</strong>:
                                                <ul class="mt-1">
                                                    <li>재방문 <strong>Median</strong> 로드 시간 (ms)</li>
                                                    <li><strong>Long Tasks 합계</strong> - TBT 유사 (50ms 초과 작업의 초과분 합산)
                                                    </li>
                                                    <li><strong>JS 런타임 에러</strong> - 자사/외부 도메인별 분리 집계</li>
                                                    <li><strong>렌더 폭 초과</strong> - body 요소가 viewport 너비 초과 여부</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- 대표 테스트 기기 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📱 대표 테스트 기기 (6종)</h4>
                                        <div class="row small text-muted">
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>iOS</strong></div>
                                                <ul class="mb-2">
                                                    <li>iPhone SE (구형·소형 뷰포트)</li>
                                                    <li>iPhone 11 (중급·보편 해상도)</li>
                                                    <li>iPhone 15 Pro (최신·고성능)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>Android</strong></div>
                                                <ul class="mb-0">
                                                    <li>Galaxy S9+ (구형)</li>
                                                    <li>Galaxy S20 Ultra (고해상도)</li>
                                                    <li>Pixel 5 (표준 Android 레퍼런스)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            ※ Playwright 내장 디바이스 프로필을 사용하며, 부재 시 인접 모델로 안전 대체합니다.
                                        </div>
                                    </div>

                                    <!-- Playwright 소개 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎭 Playwright란?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Microsoft 개발</strong>: 현대적인 웹 자동화 도구로, 실제 브라우저 엔진을 사용하여 정확한 성능
                                                측정이 가능합니다.</li>
                                            <li><strong>헤드리스 실행</strong>: UI 없이 백그라운드에서 실행되어 서버 환경에서도 안정적으로 동작합니다.</li>
                                            <li><strong>CPU 스로틀링</strong>: CPU 성능을 인위적으로 제한(×4)하여 실제 모바일 환경의 성능 제약을
                                                시뮬레이션합니다.</li>
                                            <li><strong>정밀한 메트릭 수집</strong>: JavaScript 실행 시간, 에러, 렌더링 성능 등을 정확하게 측정할 수
                                                있습니다.</li>
                                        </ul>
                                    </div>

                                    <!-- 왜 구형이 더 빠를 수 있나 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">❓ 왜 구형 기기가 더 빠르게 보일 수 있나요?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>가벼운 자산 제공</strong>: 작은 뷰포트/해상도(User Agent)에 맞춰 더 낮은 용량의
                                                이미지·레이아웃이 제공될 수 있습니다.</li>
                                            <li><strong>균일한 CPU 스로틀</strong>: 모든 기기에 동일한 ×4 스로틀을 적용하므로, 순수 "단말 성능 차"보다는
                                                그 기기에 제공된 리소스 무게가 속도에 더 큰 영향을 줍니다.</li>
                                            <li><strong>조건부 로딩 차이</strong>: UA/미디어쿼리/임계치 등에 따라 광고·위젯·스크립트가 기기별로 달리 로드될 수
                                                있습니다.</li>
                                        </ul>
                                    </div>

                                    <!-- 테스트의 의미 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎯 이 테스트가 의미 있는 이유</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>모바일 체감 렌더링</strong>을 정조준: 캐시가 채워진 재방문 상황에서 JS/레이아웃 부담을 Median과
                                                Long Tasks로 파악</li>
                                            <li><strong>런타임 안정성</strong>: JS 에러를 자사/외부로 구분 집계 → 실제 품질 이슈의 책임 소재 파악 용이
                                            </li>
                                            <li><strong>반응형 적합성</strong>: body가 viewport를 넘는지 자동 검출 → 모바일 화면 대응 누락을 조기
                                                발견</li>
                                            <li><strong>재현 가능성</strong>: 기기·횟수·스로틀·웨이팅 규칙을 고정해 릴리즈 간 회귀 비교와 목표 관리에 최적
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- 등급 기준 안내 --}}
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>등급</th>
                                                    <th>점수</th>
                                                    <th>성능 기준</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90~100</td>
                                                    <td>Median 응답시간: <strong>≤ 800ms</strong><br>JS 런타임 에러:
                                                        <strong>0</strong><br>렌더 폭 초과: <strong>없음</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>Median 응답시간: <strong>≤ 1200ms</strong><br>JS 런타임 에러: <strong>≤
                                                            1</strong><br>렌더 폭 초과: <strong>없음</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>Median 응답시간: <strong>≤ 2000ms</strong><br>JS 런타임 에러: <strong>≤
                                                            2</strong><br>렌더 폭 초과: <strong>허용</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>Median 응답시간: <strong>≤ 3000ms</strong><br>JS 런타임 에러: <strong>≤
                                                            3</strong><br>렌더 폭 초과: <strong>빈번</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>Median 응답시간: <strong>≤ 4000ms</strong><br>JS 런타임 에러: <strong>≤
                                                            5</strong><br>렌더 폭 초과: <strong>심각</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>위 기준에 미달</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-info d-block mt-3">
                                        <div><strong>요약</strong> — 기기 6종 · 각 4회(1 웜업 제외) · CPU ×4 · 지표: Median / Long
                                            Tasks / JS 에러(자사·외부) / 렌더 폭 초과</div>
                                        <div class="mt-1">구형이 더 빠르게 측정되어도 정상일 수 있습니다. 이는 가벼운 자산 제공 + 균일 스로틀의 결과이며, 본
                                            테스트는 실제 사용자 환경에서의 <strong>모바일 렌더링 비용과 안정성</strong>을 지속적으로 추적하는 데 목적이 있습니다.
                                        </div>
                                    </div>
                                </div>

                                <!-- 결과 탭 -->
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $report = $currentTest->results;
                                            $overall = $report['overall'] ?? [];
                                            $results = $report['results'] ?? [];

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

                                            $gradeMap = [
                                                'A+' => 'badge-a-plus',
                                                'A' => 'badge-a',
                                                'B' => 'badge-b',
                                                'C' => 'badge-c',
                                                'D' => 'badge-d',
                                                'F' => 'badge-f',
                                            ];
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 종합 결과 -->
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">종합 결과</h5>

                                                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                                    <div>Median 평균:
                                                        <strong>{{ $overall['medianAvgMs'] ?? 0 }}</strong>ms
                                                    </div>
                                                    <div>Long Tasks 평균:
                                                        <strong>{{ $overall['longTasksAvgMs'] ?? 0 }}</strong>ms
                                                    </div>
                                                    <div>JS 에러(자사):
                                                        <strong>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }}</strong>
                                                    </div>
                                                    <div>JS 에러(외부):
                                                        <strong>{{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</strong>
                                                    </div>
                                                    <div>렌더 폭 초과:
                                                        <strong>{{ !empty($overall['bodyOverflowsViewport']) ? '있음' : '없음' }}</strong>
                                                    </div>
                                                </div>

                                                @if (!empty($overall['reason']))
                                                    @php
                                                        $reasonParts = explode(' / ', $overall['reason']);
                                                    @endphp
                                                    <div class="mt-3">
                                                        @foreach ($reasonParts as $part)
                                                            <div class="fw-bold text-dark mb-1">{{ trim($part) }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 기기별 상세 결과 -->
                                        <div class="mb-4">
                                            <h5 class="mb-3">기기별 상세 결과</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-vcenter table-nowrap">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>디바이스</th>
                                                            <th>Median(ms)</th>
                                                            <th>TBT(LongTasks, ms)</th>
                                                            <th>JS(자사)</th>
                                                            <th>JS(외부)</th>
                                                            <th>JS(고유)</th>
                                                            <th>렌더 폭 초과</th>
                                                            <th>Viewport</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($results as $result)
                                                            <tr class="device-row">
                                                                <td><strong>{{ $result['device'] ?? 'Unknown' }}</strong>
                                                                </td>
                                                                <td>{{ $result['medianMs'] ?? 0 }}</td>
                                                                <td>{{ $result['longTasksTotalMs'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsFirstPartyCount'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsThirdPartyCount'] ?? 0 }}</td>
                                                                <td>{{ $result['jsErrorsUniqueCount'] ?? 0 }}</td>
                                                                <td>{{ !empty($result['bodyOverflowsViewport']) ? '있음' : '없음' }}
                                                                </td>
                                                                <td>
                                                                    @if (!empty($result['viewport']))
                                                                        {{ $result['viewport']['w'] ?? '?' }}×{{ $result['viewport']['h'] ?? '?' }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- 등급 기준 안내 삭제됨 -->

                                        <!-- 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>측정 지표 설명</h6>
                                            <p class="mb-2"><strong>Median 응답시간:</strong> 재방문 시 페이지 로딩에 걸리는 중간값
                                                시간입니다.</p>
                                            <p class="mb-2"><strong>TBT (Long Tasks):</strong> JavaScript 실행으로 인한 메인
                                                스레드 차단 시간의 합계입니다.</p>
                                            <p class="mb-2"><strong>JS 에러:</strong> 자사는 테스트 도메인, 외부는 서드파티에서 발생한
                                                JavaScript 런타임 에러입니다.</p>
                                            <p class="mb-0"><strong>렌더 폭 초과:</strong> 웹페이지의 body 요소가 모바일 뷰포트 너비를
                                                초과하는지 여부입니다.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 모바일 성능 결과를 확인할 수 있습니다.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- 데이터 탭 -->
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

                <!-- 사이드바 -->
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
