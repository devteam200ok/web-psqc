@section('title')
    <title>📱 반응형 UI 테스트 - 뷰포트별 렌더링 폭 초과 검사 - DevTeam Test</title>
    <meta name="description"
        content="모바일, 태블릿, 데스크톱 등 9개 주요 뷰포트에서 웹사이트의 반응형 UI를 자동 검사합니다. body 렌더링 폭이 viewport를 초과하는지 측정하여 수평 스크롤 발생 여부를 진단하고 개선 가이드를 제공합니다.">
    <meta name="keywords"
        content="반응형 UI 테스트, 뷰포트 호환성 검사, 모바일 최적화, 수평 스크롤 방지, 반응형 웹 디자인, UI 오버플로우 진단, 크로스 디바이스 테스트, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="📱 반응형 UI 테스트 - 뷰포트별 렌더링 폭 초과 검사 - DevTeam Test" />
    <meta property="og:description"
        content="9개 주요 뷰포트에서 반응형 UI 적합성을 점검하고, 수평 스크롤 문제를 사전에 탐지하여 A+ 등급까지 품질 인증서를 발급받을 수 있습니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 반응형 UI 테스트 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="📱 반응형 UI 테스트 - 뷰포트별 렌더링 폭 초과 검사" />
    <meta name="twitter:description"
        content="모바일·태블릿·데스크톱 9개 뷰포트에서 반응형 UI를 정밀 검사하고 수평 스크롤 문제를 진단합니다. DevTeam Test로 A+ 등급 인증서를 발급받으세요." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '반응형 UI 테스트 - 뷰포트별 렌더링 폭 초과 검사',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => '모바일, 태블릿, 데스크톱 등 9개 주요 뷰포트에서 반응형 UI 적합성을 자동 검사하여 수평 스크롤 발생 여부를 진단하고 개선 방안을 제공합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="📱 반응형 UI 테스트" subtitle="뷰포트별 렌더링 폭 초과 측정" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                    <h3>Playwright 기반 반응형 UI 적합성 검사</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>측정 도구:</strong> Playwright (Chromium 엔진)<br>
                                        <strong>테스트 목적:</strong> 다양한 디바이스 환경에서 웹페이지가 viewport 경계를 벗어나지 않고 올바르게 렌더링되는지
                                        검증<br>
                                        <strong>검사 대상:</strong> 9개 주요 뷰포트 (모바일 3개, 폴더블 1개, 태블릿 3개, 데스크톱 2개)<br><br>

                                        <strong>측정 항목:</strong><br>
                                        • body 요소의 실제 렌더링 폭<br>
                                        • viewport 폭 대비 초과 픽셀 수<br>
                                        • 각 뷰포트별 초과 발생 여부<br><br>

                                        <strong>테스트 방식:</strong><br>
                                        1. 각 뷰포트 크기로 브라우저 설정<br>
                                        2. 페이지 로드 후 네트워크 안정화 대기 (6초)<br>
                                        3. document.body.getBoundingClientRect() 측정<br>
                                        4. viewport 폭과 비교하여 초과 픽셀 계산<br><br>

                                        <strong>검사 뷰포트 목록:</strong><br>
                                        • 모바일: 360×800, 390×844, 414×896<br>
                                        • 폴더블: 672×960<br>
                                        • 태블릿: 768×1024, 834×1112, 1024×1366<br>
                                        • 데스크톱: 1280×800, 1440×900<br><br>

                                        이 테스트는 약 <strong>30초~1분</strong> 정도 소요되며,
                                        수평 스크롤바 발생 가능성을 사전에 발견하여 사용자 경험을 개선할 수 있습니다.
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
                                                <tr>
                                                    <td><span class="badge bg-green-lt text-green-lt-fg">A+</span></td>
                                                    <td>100</td>
                                                    <td>전 뷰포트 초과 0건<br>body 렌더 폭이 항상 viewport 이내</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>90~95</td>
                                                    <td>초과 ≤1건이며 ≤8px<br>모바일 협폭(≤390px) 구간에서는 초과 0건</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>80~89</td>
                                                    <td>초과 ≤2건이고 각 ≤16px<br>또는 모바일 협폭에서 ≤8px 1건</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>70~79</td>
                                                    <td>초과 ≤4건 또는 단일 초과가 17~32px</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>50~69</td>
                                                    <td>초과 >4건 또는 단일 초과가 33~64px</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~49</td>
                                                    <td>측정 실패 또는 ≥65px 초과 발생</td>
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
                                            $grade = $currentTest->overall_grade ?? 'F';
                                            $score = $currentTest->overall_score ?? 0;
                                            $totals = $results['totals'] ?? [];
                                            $overflowCount = $totals['overflowCount'] ?? 0;
                                            $maxOverflowPx = $totals['maxOverflowPx'] ?? 0;
                                            $reason = $results['overall']['reason'] ?? '';
                                            $perViewport = $results['perViewport'] ?? [];

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

                                        <!-- 종합 결과 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">종합 결과</h5>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">초과 건수</div>
                                                                <div class="h3">{{ $overflowCount }}건</div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">최대 초과 픽셀</div>
                                                                <div class="h3">{{ $maxOverflowPx }}px</div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <div class="text-muted small">판정 사유</div>
                                                                <div class="small mt-1">{{ $reason }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 뷰포트별 상세 결과 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">뷰포트별 상세 결과</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>뷰포트</th>
                                                                <th>크기</th>
                                                                <th>초과 여부</th>
                                                                <th>초과 픽셀</th>
                                                                <th>Viewport 폭</th>
                                                                <th>Body 렌더 폭</th>
                                                                <th>상태</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($perViewport as $vp)
                                                                @php
                                                                    $hasOverflow = $vp['overflow'] ?? false;
                                                                    $overflowPx = $vp['overflowPx'] ?? 0;
                                                                    $hasError = !empty($vp['navError']);
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ str_replace('-', ' ', explode('-', $vp['viewport'])[0] ?? '') }}
                                                                    </td>
                                                                    <td>{{ $vp['w'] ?? 0 }}×{{ $vp['h'] ?? 0 }}</td>
                                                                    <td>
                                                                        @if ($hasError)
                                                                            <span class="badge bg-secondary">오류</span>
                                                                        @elseif ($hasOverflow)
                                                                            <span
                                                                                class="badge bg-red-lt text-red-lt-fg">초과</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-green-lt text-green-lt-fg">정상</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($overflowPx > 0)
                                                                            <strong
                                                                                class="text-danger">{{ $overflowPx }}px</strong>
                                                                        @else
                                                                            <span class="text-muted">0px</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $vp['viewportWidth'] ?? ($vp['w'] ?? 0) }}px
                                                                    </td>
                                                                    <td>{{ $vp['bodyRenderWidth'] ?? 0 }}px</td>
                                                                    <td>
                                                                        @if ($hasError)
                                                                            <small
                                                                                class="text-danger">{{ $vp['navError'] }}</small>
                                                                        @else
                                                                            <span class="text-muted">정상 측정</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <strong>💡 측정 지표 설명</strong><br>
                                            <strong>Viewport 폭:</strong> 브라우저 창의 실제 표시 영역 너비 (window.innerWidth)<br>
                                            <strong>Body 렌더 폭:</strong> body 요소가 실제로 렌더링된 너비
                                            (getBoundingClientRect().width)<br>
                                            <strong>초과 픽셀:</strong> Body 렌더 폭이 Viewport 폭을 초과한 픽셀 수<br><br>

                                            초과가 발생하면 사용자는 수평 스크롤을 해야 콘텐츠를 볼 수 있으며, 이는 모바일 사용성을 크게 저하시킵니다.
                                        </div>

                                        <!-- 개선 방안 -->
                                        @if ($overflowCount > 0)
                                            <div class="alert alert-info d-block">
                                                <strong>🔧 개선 방안</strong><br>
                                                @if ($maxOverflowPx > 50)
                                                    • 고정 폭 요소 확인: width 속성에 고정 px 값 대신 % 또는 vw 단위 사용<br>
                                                    • 이미지 최적화: max-width: 100% 및 height: auto 적용<br>
                                                    • 테이블 반응형 처리: overflow-x: auto 또는 반응형 테이블 컴포넌트 사용<br>
                                                @elseif ($maxOverflowPx > 20)
                                                    • padding/margin 점검: box-sizing: border-box 적용 확인<br>
                                                    • 긴 텍스트 처리: word-break: break-word 또는 overflow-wrap 속성 사용<br>
                                                    • 폼 요소 너비: input, textarea에 width: 100% 적용<br>
                                                @else
                                                    • 미세 조정: 특정 요소의 padding이나 border가 container를 벗어나는지 확인<br>
                                                    • 서드파티 위젯: 외부 스크립트가 주입하는 요소들의 스타일 점검<br>
                                                @endif
                                                <br>
                                                <strong>디버깅 팁:</strong> 개발자 도구에서 * { outline: 1px solid red; } 적용하여 초과
                                                요소 찾기
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 뷰포트별 반응형 UI 적합성 결과를 확인할 수 있습니다.</p>
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
