@section('title')
    <title>🚀 K6 부하 테스트 - 웹사이트 성능 및 안정성 검증 | DevTeam Test</title>
    <meta name="description"
        content="K6로 동시 접속자(VUs)·Duration·Think Time을 설정해 실제 트래픽을 시뮬레이션합니다. P95 응답시간, 에러율, 안정성 지표로 웹사이트의 성능·안정성을 평가하고 인증서까지 발급받으세요.">
    <meta name="keywords" content="K6 부하 테스트, 웹사이트 성능 테스트, VU, P95 응답시간, 에러율, 동시 접속자, 부하 처리, 안정성 검증, 성능 인증서, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="K6 부하 테스트 - 웹사이트 성능 및 안정성 검증 | DevTeam Test" />
    <meta property="og:description"
        content="K6 오픈소스로 실제 트래픽을 재현하여 P95 응답시간·에러율·안정성을 측정합니다. 설정 조건에 따라 A+ 등급까지 인증서를 발급받을 수 있습니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test - K6 부하 테스트" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="K6 부하 테스트 - 웹사이트 성능 및 안정성 검증 | DevTeam Test" />
    <meta name="twitter:description" content="VUs·Duration·Think Time으로 실제 사용 패턴을 시뮬레이션하고 P95·에러율을 바탕으로 안정성을 평가하세요." />
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
    'name' => 'K6 부하 테스트 - 웹사이트 성능 및 안정성 검증',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'K6로 동시 접속자 시뮬레이션을 수행하고 P95 응답시간·에러율·안정성 지표로 웹 성능을 평가합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🚀 K6 부하 테스트" subtitle="웹사이트 성능 및 안정성 검증" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 및 설정 --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-info d-block mb-4">
                                    <h5>🔐 로그인 필요</h5>
                                    <p class="mb-2">부하 테스트는 도메인 소유권 인증이 필요한 서비스입니다.</p>
                                    <p class="mb-0">로그인 후 사이드바의 "도메인" 탭에서 도메인을 등록하고 소유권을 인증해주세요.</p>
                                </div>
                            @endif

                            <div class="alert alert-warning d-block alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        ⚠️ <strong>Cloudflare Proxy 활성화 시</strong>
                                        부하 테스트 결과가 비정상적으로 느리게 측정될 수 있습니다.
                                        <br>
                                        정확한 성능 테스트를 위해서는 <strong>해당 도메인의 DNS 레코드를 "DNS only"</strong>
                                        (회색 구름 아이콘) 상태로 설정해주세요.
                                    </div>
                                </div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>

                            <!-- URL 입력 -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">테스트 URL</label>
                                    <div class="input-group">
                                        <input type="url" wire:model="url" wire:keydown.enter="runTest"
                                            class="form-control @error('url') is-invalid @enderror"
                                            placeholder="https://www.example.com"
                                            @if ($isLoading || !Auth::check()) disabled @endif>
                                        <button wire:click="runTest" class="btn btn-primary"
                                            @if ($isLoading || !Auth::check()) disabled @endif>
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
                                    @if (Auth::check())
                                        <div class="form-text">소유권이 인증된 도메인만 테스트 가능합니다.</div>
                                    @endif
                                </div>
                            </div>

                            <!-- 테스트 설정 -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Virtual Users (VUs)</label>
                                    <input type="number" wire:model.live="vus"
                                        class="form-control @error('vus') is-invalid @enderror" min="10"
                                        max="100" @if ($isLoading || !Auth::check()) disabled @endif>
                                    @error('vus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">동시 접속자 수 (10-100)</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Duration (초)</label>
                                    <input type="number" wire:model.live="duration_seconds"
                                        class="form-control @error('duration_seconds') is-invalid @enderror"
                                        min="30" max="100" @if ($isLoading || !Auth::check()) disabled @endif>
                                    @error('duration_seconds')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">테스트 지속 시간 (30-100초)</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">목표 등급</label>
                                    <div class="form-control-plaintext">
                                        <span
                                            class="badge badge-{{ strtolower($maxGrade) === 'a+' ? 'a-plus' : strtolower($maxGrade) }}">
                                            최대 {{ $maxGrade }}등급 ({{ $maxScore }}점)
                                        </span>
                                    </div>
                                    <div class="form-text">현재 설정 기준</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <small class="text-muted">
                                        Think Time: {{ $think_time_min }}-{{ $think_time_max }}초 (고정값)
                                    </small>

                                    @if ($hasProOrAgencyPlan)
                                        <div class="mt-2">
                                            <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                                class="text-primary me-3">테스트 예약</a>
                                            <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                                class="text-primary">스케줄 등록</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($hasProOrAgencyPlan)
                        {{-- 테스트 예약 폼 (공통 컴포넌트) --}}
                        <x-test-shared.schedule-form :show-schedule-form="$showScheduleForm" :schedule-date="$scheduleDate" :schedule-hour="$scheduleHour"
                            :schedule-minute="$scheduleMinute" />

                        {{-- 스케줄 등록 폼 (공통 컴포넌트) --}}
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

                                    <h3>K6 부하 테스트란?</h3>
                                    <div class="text-muted small mt-1 mb-4">
                                        <strong>K6</strong>는 Grafana에서 개발한 현대적인 부하 테스트 도구로, JavaScript로 테스트 시나리오를 작성하여
                                        웹사이트나 API의 성능과 안정성을 검증합니다.<br><br>

                                        <strong>🔧 주요 개념:</strong><br>
                                        • <strong>Virtual Users (VUs)</strong>: 동시에 접속하는 가상 사용자 수<br>
                                        • <strong>Duration</strong>: 테스트를 지속하는 시간<br>
                                        • <strong>Think Time</strong>: 각 요청 사이의 대기 시간 (실제 사용자의 행동 패턴 시뮬레이션)<br>
                                        • <strong>P95 응답시간</strong>: 전체 요청 중 95%가 이 시간 내에 응답받은 시간<br><br>

                                        <strong>📊 왜 P95가 중요한가?</strong><br>
                                        평균 응답시간은 일부 매우 빠른 요청에 의해 왜곡될 수 있습니다.
                                        P95는 대부분의 사용자(95%)가 실제로 경험하는 응답시간을 나타내므로 더 현실적인 지표입니다.<br><br>

                                        <strong>🎯 Think Time의 역할:</strong><br>
                                        실제 사용자는 페이지를 로드한 후 내용을 읽거나 다음 행동을 결정하는 시간이 필요합니다.
                                        Think Time을 설정하면 더 현실적인 부하 패턴을 만들 수 있습니다.
                                    </div>

                                    {{-- 등급 기준 안내 --}}
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>등급</th>
                                                    <th>VU/Duration 조건</th>
                                                    <th>성능 기준</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>100 VUs 이상 + 60초 이상</td>
                                                    <td>P95 < 1000ms<br>에러율 < 0.1%<br>안정성: P90 ≤ 평균값의 200%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>50 VUs 이상 + 45초 이상</td>
                                                    <td>P95 < 1200ms<br>에러율 < 0.5%<br>안정성: P90 ≤ 평균값의 240%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>30 VUs 이상 + 30초 이상</td>
                                                    <td>P95 < 1500ms<br>에러율 < 1.0%<br>안정성: P90 ≤ 평균값의 280%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>20 VUs 이상 + 30초 이상</td>
                                                    <td>P95 < 2000ms<br>에러율 < 2.0%<br>안정성: P90 ≤ 평균값의 320%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>10 VUs 이상 + 30초 이상</td>
                                                    <td>P95 < 3000ms<br>에러율 < 5.0%<br>안정성: P90 ≤ 평균값의 400%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>-</td>
                                                    <td>위 기준에 미달</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-warning d-block mt-3">
                                        <strong>📋 인증서 발급 조건:</strong><br>
                                        • 최소 <strong>30 VUs</strong> + <strong>30초</strong> 테스트 필요<br>
                                        • B등급 이상 달성<br>
                                        • 로그인 및 도메인 소유권 인증 필요<br><br>

                                        <strong>🔐 도메인 소유권 인증 방법:</strong><br>
                                        1. 사이드바 "도메인" 탭에서 도메인 등록<br>
                                        2. TXT 레코드 또는 파일 업로드로 소유권 인증<br>
                                        3. 인증 완료 후 부하 테스트 실행 가능
                                    </div>
                                </div>

                                <!-- 결과 탭 -->
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
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

                                            $metrics = $currentTest->metrics ?? [];
                                            $config = $currentTest->test_config ?? [];

                                            $totalRequests = $metrics['http_reqs'] ?? 0;
                                            $failureRate = ($metrics['http_req_failed'] ?? 0) * 100;
                                            $p95Response = $metrics['http_req_duration_p95'] ?? 0;
                                            $avgResponse = $metrics['http_req_duration_avg'] ?? 0;
                                            $requestsPerSec = $metrics['http_reqs_rate'] ?? 0;
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 주요 메트릭 카드들 -->
                                        <div class="row mb-4">
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($totalRequests) }}</h3>
                                                        <div class="text-muted">Total Requests</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($requestsPerSec, 1) }}
                                                        </h3>
                                                        <div class="text-muted">Req/sec</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-1">{{ number_format($p95Response) }}ms</h3>
                                                        <div class="text-muted">P95 Response</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h3
                                                            class="mb-1 {{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($failureRate, 2) }}%
                                                        </h3>
                                                        <div class="text-muted">Failure Rate</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 상세 결과 테이블 -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h5>테스트 설정</h5>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>Virtual Users</th>
                                                        <td>{{ $config['vus'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Duration</th>
                                                        <td>{{ $config['duration_seconds'] ?? 'N/A' }}초</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Think Time</th>
                                                        <td>{{ $config['think_time_min'] ?? 3 }}-{{ $config['think_time_max'] ?? 10 }}초
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Region</th>
                                                        <td>{{ ucfirst($config['region'] ?? 'seoul') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>응답 시간 분석</h5>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>Average</th>
                                                        <td>{{ number_format($metrics['http_req_duration_avg'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Median</th>
                                                        <td>{{ number_format($metrics['http_req_duration_med'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>P90</th>
                                                        <td>{{ number_format($metrics['http_req_duration_p90'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>P95</th>
                                                        <td>{{ number_format($metrics['http_req_duration_p95'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Max</th>
                                                        <td>{{ number_format($metrics['http_req_duration_max'] ?? 0, 2) }}ms
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>안정성 (P90/평균)</th>
                                                        <td>
                                                            @php
                                                                $avgTime = $metrics['http_req_duration_avg'] ?? 1;
                                                                $p90Time = $metrics['http_req_duration_p90'] ?? 0;
                                                                $stabilityRatio =
                                                                    $avgTime > 0 ? $p90Time / $avgTime : 0;
                                                            @endphp
                                                            {{ number_format($stabilityRatio, 2) }}
                                                            ({{ number_format($stabilityRatio * 100, 1) }}%)
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- 체크 결과 -->
                                        @if (isset($metrics['checks_passes']) || isset($metrics['checks_fails']))
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h5>체크 결과</h5>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>통과</th>
                                                            <td class="text-success">
                                                                {{ $metrics['checks_passes'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>실패</th>
                                                            <td class="text-danger">
                                                                {{ $metrics['checks_fails'] ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>성공률</th>
                                                            <td>
                                                                @php
                                                                    $passes = $metrics['checks_passes'] ?? 0;
                                                                    $fails = $metrics['checks_fails'] ?? 0;
                                                                    $total = $passes + $fails;
                                                                    $rate =
                                                                        $total > 0
                                                                            ? round(($passes / $total) * 100, 2)
                                                                            : 0;
                                                                @endphp
                                                                {{ $rate }}%
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>데이터 전송</h5>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>수신 데이터</th>
                                                            <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }}
                                                                MB</td>
                                                        </tr>
                                                        <tr>
                                                            <th>송신 데이터</th>
                                                            <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }}
                                                                MB</td>
                                                        </tr>
                                                        <tr>
                                                            <th>반복 횟수</th>
                                                            <td>{{ $metrics['iterations'] ?? 0 }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="alert alert-info d-block">
                                            <h6>결과 해석 가이드</h6>
                                            <p class="mb-2"><strong>P95 응답시간:</strong> 전체 요청의 95%가 이 시간 내에 응답을 받았습니다.
                                                사용자 경험의 핵심 지표입니다.</p>
                                            <p class="mb-2"><strong>에러율:</strong> 실패한 요청의 비율입니다. 1% 미만이 바람직합니다.</p>
                                            <p class="mb-2"><strong>Think Time:</strong> 실제 사용자가 페이지 간 이동 시 보이는 자연스러운
                                                행동 패턴을 시뮬레이션합니다.</p>
                                            <p class="mb-0"><strong>안정성:</strong> P90과 평균값의 비율로 응답시간의 일관성을 측정합니다.
                                                낮을수록 안정적입니다.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 부하 테스트 결과를 확인할 수 있습니다.</p>
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
