@section('title')
@section('title')
    <title>⚡ 글로벌 속도 테스트 - 8지역 리전별 로딩 속도 측정 - DevTeam Test</title>
    <meta name="description"
        content="전 세계 8개 지역(서울, 도쿄, 싱가포르, 시드니, 버지니아, 오레곤, 프랑크푸르트, 런던)에서 웹사이트 로딩 속도를 동시 측정하고 성능 등급을 평가받으세요. TTFB, 로드타임 분석과 글로벌 사용자 경험 최적화 인사이트를 제공합니다.">
    <meta name="keywords"
        content="글로벌 속도 테스트, 웹사이트 성능 측정, TTFB 테스트, 로드타임 분석, 다지역 성능 테스트, 웹 속도 최적화, 글로벌 CDN 테스트, 웹사이트 성능 등급, 리전별 속도 측정, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="⚡ 글로벌 속도 테스트 - 8지역 리전별 로딩 속도 측정 - DevTeam Test" />
    <meta property="og:description" content="전 세계 8개 지역에서 웹사이트 성능을 동시 측정하여 글로벌 사용자 경험을 분석하고 A+ 등급까지 성능 인증서를 발급받을 수 있습니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property='og:image' content='{{ url('/') }}/storage/{{ $setting->og_image }}' />
        <meta property='og:image:alt' content='DevTeam Test – 글로벌 속도 테스트' />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="⚡ 글로벌 속도 테스트 - 8지역 리전별 로딩 속도 측정 - DevTeam Test" />
    <meta name="twitter:description" content="8개 리전 TTFB/LoadTime 동시 측정, 결과 등급과 인증서 발급 지원." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage + BreadcrumbList --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '글로벌 속도 테스트',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => '전 세계 8개 리전에서 TTFB와 Load Time을 동시 측정해 글로벌 사용자 경험을 평가하는 테스트 페이지.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
@endsection
@section('css')
@include('components.test-shared.css')
@endsection

<div class="page-wrapper">
{{-- 헤더 (공통 컴포넌트) --}}
<x-test-shared.header title="⚡ 글로벌 속도 테스트" subtitle="8지역 리전별 로딩 속도 측정" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                <h3>서울, 도쿄, 시드니, 싱가포르, 프랑크푸르트, 버지니아, 오레곤, 런던 8개 지역</h3>
                                <div class="text-muted small mt-1">
                                    전 세계 주요 인터넷 거점(Asia, North America, Europe, Oceania)에 분산된 8개 리전을 통해
                                    실제 글로벌 사용자의 접속 속도를 시뮬레이션합니다.
                                    <br><br>
                                    • 아시아(서울, 도쿄, 싱가포르) → 동아시아 & 동남아시아 커버<br>
                                    • 오세아니아(시드니) → 호주 및 태평양 지역<br>
                                    • 북미(버지니아, 오레곤) → 동부·서부 양대 거점<br>
                                    • 유럽(프랑크푸르트, 런던) → 서유럽 및 중부 유럽 주요 허브
                                    <br><br>
                                    이 8개 지역은 Cloudflare, AWS, GCP 등 글로벌 인프라 사업자들이 공통적으로 운영하는 핵심 거점으로,
                                    전 세계 인터넷 트래픽의 대부분을 대표할 수 있습니다.
                                    <br><br>
                                    <strong>DevTeam Test</strong>는 자체 구축한 각 리전별 테스팅 서버에 API 요청을 전송하고,
                                    모든 결과를 집계한 뒤 리포트를 생성합니다.<br>
                                    이 과정은 약 <strong>30초~2분</strong> 정도 소요됩니다.
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
                                                <td><span class="badge badge-a-plus">A+</span></td>
                                                <td>90~100</td>
                                                <td>Origin: TTFB ≤ 200ms, Load ≤ 1.5s<br>글로벌 평균: TTFB ≤ 800ms, Load
                                                    ≤ 2.5s<br>모든 지역: TTFB ≤ 1.5s, Load ≤ 3s<br>재방문 성능향상: 80%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-a">A</span></td>
                                                <td>80~89</td>
                                                <td>Origin: TTFB ≤ 400ms, Load ≤ 2.5s<br>글로벌 평균: TTFB ≤ 1.2s, Load ≤
                                                    3.5s<br>모든 지역: TTFB ≤ 2s, Load ≤ 4s<br>재방문 성능향상: 60%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-b">B</span></td>
                                                <td>70~79</td>
                                                <td>Origin: TTFB ≤ 800ms, Load ≤ 3.5s<br>글로벌 평균: TTFB ≤ 1.6s, Load ≤
                                                    4.5s<br>모든 지역: TTFB ≤ 2.5s, Load ≤ 5.5s<br>재방문 성능향상: 50%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-c">C</span></td>
                                                <td>60~69</td>
                                                <td>Origin: TTFB ≤ 1.2s, Load ≤ 4.5s<br>글로벌 평균: TTFB ≤ 2.0s, Load ≤
                                                    5.5s<br>모든 지역: TTFB ≤ 3.0s, Load ≤ 6.5s<br>재방문 성능향상: 37.5%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-d">D</span></td>
                                                <td>50~59</td>
                                                <td>Origin: TTFB ≤ 1.6s, Load ≤ 6.0s<br>글로벌 평균: TTFB ≤ 2.5s, Load ≤
                                                    7.0s<br>모든 지역: TTFB ≤ 3.5s, Load ≤ 8.5s<br>재방문 성능향상: 25%+</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-f">F</span></td>
                                                <td>0~49</td>
                                                <td>위 기준에 미달</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                id="tabs-results">
                                @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                    @php
                                        $results = $currentTest->results['results'] ?? [];
                                        $probeErrors = $currentTest->results['errors'] ?? [];

                                        // 기존 계산 로직은 그대로 유지...
                                        $regionLabels = [
                                            'seoul' => 'Seoul',
                                            'tokyo' => 'Tokyo',
                                            'singapore' => 'Singapore',
                                            'virginia' => 'Virginia',
                                            'oregon' => 'Oregon',
                                            'frankfurt' => 'Frankfurt',
                                            'london' => 'London',
                                            'sydney' => 'Sydney',
                                        ];

                                        $firstTTFB = [];
                                        $firstLoad = [];
                                        $repeatTTFB = [];
                                        $repeatLoad = [];

                                        foreach ($regionLabels as $region => $label) {
                                            $m = $currentTest->getRegionMetrics($region);
                                            if (!$m) {
                                                continue;
                                            }

                                            $ft = data_get($m, 'first.ttfb');
                                            $fl = data_get($m, 'first.load');
                                            $rt = data_get($m, 'repeat.ttfb');
                                            $rl = data_get($m, 'repeat.load');

                                            if (is_numeric($ft)) {
                                                $firstTTFB[$region] = (float) $ft;
                                            }
                                            if (is_numeric($fl)) {
                                                $firstLoad[$region] = (float) $fl;
                                            }
                                            if (is_numeric($rt)) {
                                                $repeatTTFB[$region] = (float) $rt;
                                            }
                                            if (is_numeric($rl)) {
                                                $repeatLoad[$region] = (float) $rl;
                                            }
                                        }

                                        // Origin = TTFB가 가장 빠른 리전
                                        $originRegion = null;
                                        $originTTFB = null;
                                        $originLoad = null;
                                        if (!empty($firstTTFB)) {
                                            $tmp = $firstTTFB;
                                            asort($tmp);
                                            $originRegion = array_key_first($tmp);
                                            $originTTFB = $tmp[$originRegion] ?? null;
                                            $originLoad =
                                                $firstLoad[$originRegion] ??
                                                (count($firstLoad) ? min($firstLoad) : null);
                                        }

                                        $avgTTFB = count($firstTTFB) ? array_sum($firstTTFB) / count($firstTTFB) : null;
                                        $avgLoad = count($firstLoad) ? array_sum($firstLoad) / count($firstLoad) : null;
                                        $worstTTFB = count($firstTTFB) ? max($firstTTFB) : null;
                                        $worstLoad = count($firstLoad) ? max($firstLoad) : null;

                                        // 재방문 성능향상 계산
                                        $improvedRegions = 0;
                                        $eligibleRegions = 0;
                                        foreach ($firstLoad as $r => $fl) {
                                            $rl = $repeatLoad[$r] ?? null;
                                            if (is_numeric($fl) && is_numeric($rl) && $fl > 0) {
                                                $eligibleRegions++;
                                                if ($rl < $fl) {
                                                    $improvedRegions++;
                                                }
                                            }
                                        }
                                        $repeatImprovePct = $eligibleRegions
                                            ? ($improvedRegions / $eligibleRegions) * 100.0
                                            : null;

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
                                        $fmt = fn($v, $unit = 'ms') => is_numeric($v)
                                            ? number_format($v, 1) . $unit
                                            : '데이터 없음';
                                        $fmtPct = fn($v) => is_numeric($v) ? number_format($v, 1) . '%' : '데이터 없음';
                                    @endphp

                                    <x-test-shared.certificate :current-test="$currentTest" />

                                    <!-- 성능 지표 요약 -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">성능 지표 요약</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>항목</th>
                                                            <th>TTFB</th>
                                                            <th>Load Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Origin
                                                                    ({{ $originRegion ? ucfirst($originRegion) : 'N/A' }})</strong>
                                                            </td>
                                                            <td>{{ $fmt($originTTFB) }}</td>
                                                            <td>{{ $fmt($originLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>글로벌 평균</strong></td>
                                                            <td>{{ $fmt($avgTTFB) }}</td>
                                                            <td>{{ $fmt($avgLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>모든 지역 (최댓값)</strong></td>
                                                            <td>{{ $fmt($worstTTFB) }}</td>
                                                            <td>{{ $fmt($worstLoad) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>재방문 성능향상</strong></td>
                                                            <td colspan="2">
                                                                {{ $fmtPct($repeatImprovePct) }}
                                                                @if ($eligibleRegions)
                                                                    <span
                                                                        class="text-muted">({{ $improvedRegions }}
                                                                        / {{ $eligibleRegions }} 지역 개선)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 지역별 상세 결과 -->
                                    @if ($currentTest->metrics)
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">지역별 상세 결과</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter table-nowrap">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>지역</th>
                                                                <th>TTFB</th>
                                                                <th>로드 타임</th>
                                                                <th>전송 용량</th>
                                                                <th>리소스 개수</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $formatMetric = function (
                                                                    $first,
                                                                    $repeat,
                                                                    $unit = 'ms',
                                                                ) {
                                                                    if ($first === null) {
                                                                        return '<span class="text-muted">No Data</span>';
                                                                    }
                                                                    $firstFormatted = is_numeric($first)
                                                                        ? number_format($first, 1)
                                                                        : $first;
                                                                    $output = "<strong>{$firstFormatted}{$unit}</strong>";
                                                                    if ($repeat !== null) {
                                                                        $repeatFormatted = is_numeric($repeat)
                                                                            ? number_format($repeat, 1)
                                                                            : $repeat;
                                                                        $delta = $repeat - $first;
                                                                        $deltaFormatted =
                                                                            ($delta >= 0 ? '+' : '') .
                                                                            number_format($delta, 1);
                                                                        $deltaClass =
                                                                            $delta < 0
                                                                                ? 'text-success'
                                                                                : ($delta > 0
                                                                                    ? 'text-danger'
                                                                                    : 'text-muted');
                                                                        $output .= "<br><small>{$repeatFormatted}{$unit} <span class='{$deltaClass}'>({$deltaFormatted})</span></small>";
                                                                    }
                                                                    return $output;
                                                                };
                                                                $regionLabels = [
                                                                    'seoul' => '서울',
                                                                    'tokyo' => '도쿄',
                                                                    'singapore' => '싱가포르',
                                                                    'virginia' => '버지니아',
                                                                    'oregon' => '오레곤',
                                                                    'frankfurt' => '프랑크푸르트',
                                                                    'london' => '런던',
                                                                    'sydney' => '시드니',
                                                                ];
                                                            @endphp

                                                            @foreach ($regionLabels as $region => $label)
                                                                @php
                                                                    $metrics = $currentTest->getRegionMetrics($region);
                                                                    $hasData = $metrics !== null;
                                                                    $rowClass = $hasData ? '' : 'table-secondary';
                                                                @endphp
                                                                <tr class="{{ $rowClass }}">
                                                                    <td><strong>{{ $label }}</strong></td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.ttfb'), data_get($metrics, 'repeat.ttfb'), 'ms') !!}</td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.load'), data_get($metrics, 'repeat.load'), 'ms') !!}</td>
                                                                    <td>{!! $formatMetric(
                                                                        data_get($metrics, 'first.bytes') ? data_get($metrics, 'first.bytes') / 1024 : null,
                                                                        data_get($metrics, 'repeat.bytes') ? data_get($metrics, 'repeat.bytes') / 1024 : null,
                                                                        'KB',
                                                                    ) !!}</td>
                                                                    <td>{!! $formatMetric(data_get($metrics, 'first.resources'), data_get($metrics, 'repeat.resources'), '') !!}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>표시 형식:</strong> <span class="fw-bold">첫 방문</span> 값 → <span
                                            class="fw-bold">재방문</span> 값 (Δ 차이)<br>
                                        <span class="text-success">초록 = 개선(재방문 속도 향상)</span> | <span
                                            class="text-danger">빨강 = 악화(재방문 속도 저하)</span>
                                    </div>

                                    <div class="alert alert-light d-block">
                                        <h6>성능 지표 설명</h6>
                                        <p class="mb-2"><strong>TTFB(Time To First Byte):</strong> 사용자가 요청을 보낸
                                            순간부터 서버에서 첫 번째 응답 바이트를 받기까지 걸리는 시간</p>
                                        <p class="mb-2"><strong>Load Time:</strong> HTML, CSS, JS, 이미지 등 모든 리소스가
                                            브라우저에 로드되어 페이지가 완전히 표시되기까지 걸리는 시간</p>
                                        <p class="mb-0"><strong>재방문 성능:</strong> 브라우저 캐시, Keep-Alive 연결, CDN 캐싱
                                            등의 효과로 재방문 시 더 빠른 로딩 속도를 보입니다</p>
                                    </div>

                                    <!-- 추가 정보 -->
                                    <div class="alert alert-info d-block">
                                        <strong>💡 왜 재방문이 더 빠를까요?</strong><br>
                                        - 브라우저 캐시: 이미지·JS·CSS 같은 정적 리소스가 캐시에 저장되어 다시 다운로드할 필요가 없습니다.<br>
                                        - Keep-Alive & 세션 재사용: 서버와의 연결이 유지되어 핸드셰이크/SSL 인증 과정이 생략됩니다.<br>
                                        - CDN 캐싱 효과: 글로벌 CDN에서 이미 준비된 리소스를 가져오기 때문에 지연 시간이 줄어듭니다.<br>
                                        그 결과, <span class="fw-bold">재방문 성능(Repeat Visit)</span>은 보통 첫 방문보다 훨씬 짧은
                                        시간이 소요됩니다.
                                    </div>

                                    <div class="alert alert-secondary d-block">
                                        <strong>📌 TTFB와 Load Time의 차이</strong><br>
                                        - <strong>TTFB(Time To First Byte)</strong>: 사용자가 요청을 보낸 순간부터 서버에서 첫 번째 응답
                                        바이트를 받기까지 걸리는 시간.<br>
                                        - <strong>Load Time</strong>: HTML, CSS, JS, 이미지 등 모든 리소스가 브라우저에 로드되어 페이지가
                                        완전히 표시되기까지 걸리는 시간.<br><br>

                                        <strong>🌍 네트워크 왕복(RTT) 구조</strong><br>
                                        • TCP 핸드셰이크 1회 + TLS 핸드셰이크 1회 + 실제 데이터 요청/응답 1회 → 최소 3번 왕복이 필요합니다.<br>
                                        • 따라서 <u>물리적으로 오리진 서버에서 먼 지역일수록</u> 지연 시간이 누적됩니다.<br><br>

                                        <strong>📊 지역별 최소 지연 시간</strong><br>
                                        - 동일 대륙(예: 서울→도쿄/싱가포르): TTFB가 수십 ms ~ 200ms 수준.<br>
                                        - 대륙 간(서울→미국/유럽): 광케이블 왕복 지연만으로도 150~250ms 이상.<br>
                                        - TLS/데이터 요청까지 포함하면 <u>최소 400~600ms 이상의 TTFB</u>가 발생할 수 있습니다.<br>
                                        - Load Time은 리소스 크기와 수에 따라 수 초까지 늘어나며, 특히 이미지·JS가 많으면 <u>5초 이상</u>도
                                        흔합니다.<br><br>

                                        즉, <span class="fw-bold">오리진과 물리적으로 가장 먼 지역(예: 한국 서버 → 미국 동부/유럽)</span>은
                                        아무리 최적화해도 <u>최소 수백 ms 이상의 TTFB</u>와 <u>2~5초 이상의 Load Time</u>은 불가피합니다.
                                        이를 줄이려면 CDN, 캐싱, Edge 서버 배포가 필수입니다.
                                    </div>
                                @else
                                    <div class="alert alert-info d-block">
                                        <h5>아직 결과가 없습니다</h5>
                                        <p class="mb-0">테스트를 실행하면 지역별 글로벌 성능 결과를 확인할 수 있습니다.</p>
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
