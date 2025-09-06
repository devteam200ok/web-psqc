@section('title')
    <title>🌐 브라우저 호환성 테스트 - Chrome · Firefox · Safari 3대 브라우저 검사 - DevTeam Test</title>
    <meta name="description"
        content="Playwright 기반으로 Chrome, Firefox, Safari(WebKit) 3대 브라우저에서 웹사이트의 JavaScript 및 CSS 호환성을 정밀 검사합니다. 크로스 브라우저 환경에서 발생하는 오류를 탐지하고, A+ 등급까지 인증서를 발급받을 수 있습니다.">
    <meta name="keywords"
        content="브라우저 호환성 테스트, 크로스 브라우저 검사, Chrome 호환성, Firefox 호환성, Safari(WebKit) 호환성, JavaScript 오류, CSS 렌더링, Playwright 테스트, 웹 표준 검사, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="🌐 브라우저 호환성 테스트 - Chrome · Firefox · Safari 3대 브라우저 검사" />
    <meta property="og:description"
        content="Chrome, Firefox, Safari(WebKit) 브라우저 환경에서 자사/타사 코드 오류를 구분하여 크로스 브라우저 호환성을 평가합니다. 웹 표준 기반 진단과 개선 가이드 제공." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 브라우저 호환성 검사 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="브라우저 호환성 테스트 - Chrome · Firefox · Safari 3대 브라우저 검사" />
    <meta name="twitter:description"
        content="Playwright 기반의 브라우저 자동화로 JavaScript, CSS 오류를 정밀 검사하고 A+ 등급까지 호환성 인증서를 발급받을 수 있습니다." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '브라우저 호환성 테스트 - Chrome · Firefox · Safari 3대 브라우저 검사',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => 'Chrome, Firefox, Safari(WebKit) 3대 주요 브라우저 환경에서 웹 호환성을 검사합니다. Playwright 기반의 자동화로 CSS·JavaScript 오류를 구분하여 정확한 진단과 개선 가이드를 제공합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header 
        title="🌐 브라우저 호환성 테스트" 
        subtitle="Chrome · Firefox · Safari 3대 브라우저 호환 검사" 
        :user-plan-usage="$userPlanUsage" 
        :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 --}}
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
                                                class="text-primary">스케줄 등록</a>
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

                        {{-- 스케줄 등록 폼 (공통 컴포넌트) --}}
                        <x-test-shared.recurring-schedule-form :show-recurring-form="$showRecurringForm" :recurring-start-date="$recurringStartDate" :recurring-end-date="$recurringEndDate"
                            :recurring-hour="$recurringHour" :recurring-minute="$recurringMinute" />
                    @endif

                    {{-- 테스트 상태 (공통 컴포넌트) --}}
                    <x-test-shared.test-status :current-test="$currentTest" :selected-history-test="$selectedHistoryTest" />

                    {{-- 메인 콘텐츠 --}}
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
                                    <h3>Chrome, Firefox, Safari 3대 주요 브라우저 호환성 검사</h3>
                                    <div class="text-muted small mt-1">
                                        웹사이트가 주요 브라우저에서 정상적으로 작동하는지 검사하는 크로스 브라우저 호환성 테스트입니다.
                                        <br><br>
                                        <strong>측정 도구:</strong> Playwright (Microsoft에서 개발한 브라우저 자동화 도구)<br>
                                        • Chromium (Chrome, Edge의 기반 엔진)<br>
                                        • Firefox (Gecko 엔진)<br>
                                        • WebKit (Safari의 기반 엔진)
                                        <br><br>
                                        <strong>테스트 목적:</strong><br>
                                        • 다양한 브라우저 환경에서 웹사이트의 정상 작동 여부 확인<br>
                                        • JavaScript 런타임 오류 검출 및 자사/타사 코드 분리<br>
                                        • CSS 파싱 및 렌더링 오류 감지<br>
                                        • 브라우저별 호환성 문제 사전 발견
                                        <br><br>
                                        <strong>측정 항목:</strong><br>
                                        • 페이지 정상 로드 여부 (document.readyState === 'complete')<br>
                                        • JavaScript 오류 수집 (자사/타사/노이즈 분류)<br>
                                        • CSS 오류 수집 (파서 오류 패턴 기반)<br>
                                        • 브라우저별 User-Agent 정보
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
                                                    <td>90~100</td>
                                                    <td>Chrome/Firefox/Safari <strong>모두 정상</strong><br>
                                                        자사 JS 오류: <strong>0개</strong><br>
                                                        CSS 렌더링 오류: <strong>0개</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>80~89</td>
                                                    <td>주요 브라우저 지원 <strong>양호</strong> (2개 이상 정상)<br>
                                                        자사 JS 오류 <strong>≤ 1</strong><br>
                                                        CSS 오류 <strong>≤ 1</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>70~79</td>
                                                    <td>브라우저별 <strong>경미한 차이</strong> 존재 (2개 이상 정상)<br>
                                                        자사 JS 오류 <strong>≤ 3</strong><br>
                                                        CSS 오류 <strong>≤ 3</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>60~69</td>
                                                    <td>일부 브라우저에서 <strong>기능 저하</strong> (1개 이상 정상)<br>
                                                        자사 JS 오류 <strong>≤ 6</strong><br>
                                                        CSS 오류 <strong>≤ 6</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>50~59</td>
                                                    <td>호환성 문제 <strong>다수</strong><br>
                                                        자사 JS 오류 <strong>≤ 10</strong><br>
                                                        CSS 오류 <strong>≤ 10</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~49</td>
                                                    <td>주요 브라우저 <strong>정상 동작 불가</strong><br>
                                                        자사 JS 오류 <strong>10개 초과</strong><br>
                                                        CSS 오류 <strong>10개 초과</strong></td>
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

                                        {{-- 종합 결과 --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">종합 결과</h5>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">정상 브라우저</div>
                                                                <div class="h3 mb-0">{{ $okCount }}/3</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">JS 오류(자사)</div>
                                                                <div class="h3 mb-0">{{ $jsFirstPartyTotal }}</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">CSS 오류</div>
                                                                <div class="h3 mb-0">{{ $cssTotal }}</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="text-muted small">테스트 모드</div>
                                                                <div class="h5 mb-0">{{ $strictMode ? '엄격 모드' : '기본 모드' }}</div>
                                                            </div>
                                                        </div>
                                                        @if (!is_null($jsThirdPartyTotal) || !is_null($jsNoiseTotal))
                                                            <div class="mt-3 pt-3 border-top">
                                                                <div class="text-muted small">추가 정보</div>
                                                                @if (!is_null($jsThirdPartyTotal))
                                                                    타사 JS 오류: {{ $jsThirdPartyTotal }}
                                                                @endif
                                                                @if (!is_null($jsNoiseTotal))
                                                                    · 노이즈: {{ $jsNoiseTotal }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="mt-2 text-muted small">
                                                            판정 사유: {{ $report['overall']['reason'] ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 브라우저별 상세 결과 --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">브라우저별 상세 결과</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead>
                                                            <tr>
                                                                <th>브라우저</th>
                                                                <th>정상 로드</th>
                                                                <th>JS 오류(자사)</th>
                                                                <th>CSS 오류</th>
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
                                                                            <span class="badge bg-green-lt text-green-lt-fg">정상</span>
                                                                        @else
                                                                            <span class="badge bg-red-lt text-red-lt-fg">비정상</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <strong>{{ $jsFirst }}</strong>
                                                                        @if (!is_null($jsThird) || !is_null($jsNoise))
                                                                            <div class="small text-muted">
                                                                                @if (!is_null($jsThird))
                                                                                    타사: {{ $jsThird }}
                                                                                @endif
                                                                                @if (!is_null($jsNoise))
                                                                                    · 노이즈: {{ $jsNoise }}
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

                                                                {{-- 네비게이션 오류 --}}
                                                                @if (!empty($browser['navError']))
                                                                    <tr>
                                                                        <td colspan="5">
                                                                            <div class="alert alert-danger d-block mb-0">
                                                                                <strong>네비게이션 오류:</strong> {{ $browser['navError'] }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                                {{-- 오류 샘플 --}}
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
                                                                                            <h6 class="mb-2">JS 오류 샘플(자사)</h6>
                                                                                            <ul class="small mb-0">
                                                                                                @foreach (array_slice($samples['jsFirstParty'], 0, 5) as $error)
                                                                                                    <li class="text-danger">{{ $error }}</li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    @endif

                                                                                    @if ($hasJsThirdParty)
                                                                                        <div class="col-md-6">
                                                                                            <h6 class="mb-2">JS 오류 샘플(타사)</h6>
                                                                                            <ul class="small mb-0">
                                                                                                @foreach (array_slice($samples['jsThirdParty'], 0, 5) as $error)
                                                                                                    <li class="text-warning">{{ $error }}</li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    @endif

                                                                                    @if ($hasCss)
                                                                                        <div class="col-12">
                                                                                            <h6 class="mb-2">CSS 오류 샘플</h6>
                                                                                            <ul class="small mb-0">
                                                                                                @foreach (array_slice($samples['css'], 0, 5) as $error)
                                                                                                    <li class="text-warning">{{ $error }}</li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    @endif

                                                                                    @if ($hasJsNoise)
                                                                                        <div class="col-12">
                                                                                            <h6 class="mb-2">노이즈 샘플 (무시된 항목)</h6>
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

                                        {{-- 측정 지표 설명 --}}
                                        <div class="alert alert-info d-block">
                                            <h6>📊 측정 지표 설명</h6>
                                            <p class="mb-2"><strong>정상 로드:</strong> 페이지 진입 성공 + document.readyState === 'complete' + 브라우저 크래시 없음</p>
                                            <p class="mb-2"><strong>자사 JS 오류:</strong> 테스트 대상 도메인에서 발생한 JavaScript 런타임 오류</p>
                                            <p class="mb-2"><strong>타사 JS 오류:</strong> 외부 도메인(광고, 분석 도구 등)에서 발생한 JavaScript 오류</p>
                                            <p class="mb-2"><strong>CSS 오류:</strong> CSS 파싱 실패, 잘못된 속성값, 지원하지 않는 속성 등</p>
                                            <p class="mb-0"><strong>노이즈:</strong> SameSite 쿠키 경고 등 무시해도 되는 브라우저 메시지</p>
                                        </div>

                                        {{-- 개선 방안 --}}
                                        <div class="alert alert-info d-block">
                                            <h6>💡 브라우저 호환성 개선 방안</h6>
                                            @if ($grade === 'F' || $grade === 'D')
                                                <p class="mb-2">🔴 <strong>심각한 호환성 문제가 발견되었습니다.</strong></p>
                                                <p class="mb-1">• 콘솔에서 JavaScript 오류를 확인하고 수정하세요</p>
                                                <p class="mb-1">• CSS 벤더 프리픽스(-webkit-, -moz- 등)를 추가하세요</p>
                                                <p class="mb-1">• Polyfill을 사용해 구형 브라우저 지원을 개선하세요</p>
                                                <p class="mb-1">• Can I Use 사이트에서 브라우저 지원 현황을 확인하세요</p>
                                            @elseif ($grade === 'C' || $grade === 'B')
                                                <p class="mb-2">🟡 <strong>일부 브라우저에서 경미한 문제가 있습니다.</strong></p>
                                                <p class="mb-1">• 브라우저별 개발자 도구에서 오류를 확인하세요</p>
                                                <p class="mb-1">• Autoprefixer로 CSS 호환성을 자동화하세요</p>
                                                <p class="mb-1">• Babel로 최신 JavaScript를 트랜스파일하세요</p>
                                            @else
                                                <p class="mb-2">🟢 <strong>브라우저 호환성이 우수합니다!</strong></p>
                                                <p class="mb-1">• 정기적으로 호환성 테스트를 실행하세요</p>
                                                <p class="mb-1">• 새로운 기능 추가 시 브라우저 지원 현황을 확인하세요</p>
                                                <p class="mb-1">• 성능 최적화와 접근성 개선도 고려해보세요</p>
                                            @endif
                                        </div>
                                    @elseif ($error)
                                        <div class="alert alert-danger d-block">
                                            <h5>오류 발생</h5>
                                            <p class="mb-0">{!! nl2br(e($error)) !!}</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 브라우저별 호환성 결과를 확인할 수 있습니다.</p>
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