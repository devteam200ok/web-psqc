<!doctype html>
<html lang="ko">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="canonical" href="https://www.devteam-app.com/{{ request()->path() != '/' ? request()->path() : '' }}" />

    @include('inc.component.seo')
    @include('inc.component.theme_css')

    <!-- Fonts: 본문 Inter + NotoSansKR, 서명 Allura -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+KR:wght@400;500;700&family=Allura&display=swap"
        rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 8mm 8mm 10mm 8mm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html,
        body {
            font-family: 'Inter', 'Noto Sans KR', system-ui, -apple-system, Segoe UI, Roboto, 'Apple SD Gothic Neo', 'Malgun Gothic', sans-serif;
            font-size: 12px;
            line-height: 1.34;
            background: transparent !important;
        }

        /* (선택) 인쇄 시 배경 유지 */
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* A4 한 장에 맞추기: 살짝 축소 (CSS) */
        .print-container {
            width: 185mm;
            margin: 0 auto;
            /* 그대로 유지 */
            /* transform: scale(0.94);  ← 삭제 */
            /* transform-origin: top left; ← 삭제 */
        }

        /* 타이틀 여백 200% 확장 */
        .title-block {
            padding: 28px 0 40px;
            position: relative;
        }

        .title-flex {
            display: flex;
            justify-content: center;
            /* 가운데 정렬 */
            align-items: center;
            position: relative;
        }

        .title-text {
            text-align: center;
        }

        .title-qr {
            position: absolute;
            right: 0;
            /* 오른쪽 끝 */
            top: 50%;
            transform: translateY(-50%);
            /* 수직 가운데 */
        }

        .title-block h1 {
            font-size: 22px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        .title-block h2 {
            font-size: 15px;
            margin: 0;
            font-weight: 600;
        }

        /* 카드/테이블/알럿 컴팩트 (여백 줄여서 1페이지 고정 도움) */
        .card {
            margin-bottom: 8px;
            border-radius: 8px;
        }

        .card-body {
            padding: 8px 10px;
        }

        .table {
            font-size: 11.2px;
        }

        .table th,
        .table td {
            padding: 5px 7px;
        }

        .alert {
            padding: 7px 9px;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .alert .fw-semibold {
            font-weight: 600;
        }

        .tight p {
            margin: 0 0 3px;
            line-height: 1.26;
        }

        /* 설명 줄간격 더 타이트 */

        /* 좌측 점수 카드 더 타이트 */
        .score-card .h1 {
            font-size: 20px;
            margin: 0;
        }

        .score-card .h4 {
            font-size: 13px;
            margin: 2px 0 0;
        }

        .score-card .mb-2 {
            margin-bottom: 6px !important;
        }

        .score-card small {
            font-size: 10.5px;
        }

        /* 표/알럿 중간 페이지 분리 방지 */
        .card,
        .table,
        .alert {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* 서명: 테두리/배경 완전 제거 + 폰트 교체 */
        .signature-line {
            margin-top: 8px;
            padding-bottom: 10px;
        }

        .signature-line .label {
            font-weight: 600;
            margin-right: 10px;
        }

        .signature {
            font-family: 'Allura', cursive;
            font-size: 30px;
            line-height: 1;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block;
            vertical-align: baseline;
        }

        .sig-meta {
            font-size: 10.5px;
            color: #6b7280;
        }

        /* 불필요한 제목 제거: 우측 요약 표 위 제목 숨김 */
        .summary-title {
            display: none;
        }
    </style>
</head>

<body class="bg-white">
    <div class="print-container">

        @if ($test_type == 'p-speed')
            @php
                $results = $currentTest->results['results'] ?? [];
                $probeErrors = $currentTest->results['errors'] ?? [];

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

                $firstTTFB = $firstLoad = $repeatTTFB = $repeatLoad = [];
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

                $originRegion = null;
                $originTTFB = null;
                $originLoad = null;
                if (!empty($firstTTFB)) {
                    $tmp = $firstTTFB;
                    asort($tmp);
                    $originRegion = array_key_first($tmp);
                    $originTTFB = $tmp[$originRegion] ?? null;
                    $originLoad = $firstLoad[$originRegion] ?? (count($firstLoad) ? min($firstLoad) : null);
                }

                $avgTTFB = count($firstTTFB) ? array_sum($firstTTFB) / count($firstTTFB) : null;
                $avgLoad = count($firstLoad) ? array_sum($firstLoad) / count($firstLoad) : null;
                $worstTTFB = count($firstTTFB) ? max($firstTTFB) : null;
                $worstLoad = count($firstLoad) ? max($firstLoad) : null;

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
                $repeatImprovePct = $eligibleRegions ? ($improvedRegions / $eligibleRegions) * 100.0 : null;

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

                $fmt = fn($v, $unit = 'ms') => is_numeric($v) ? number_format($v, 1) . $unit : '데이터 없음';
                $fmtPct = fn($v) => is_numeric($v) ? number_format($v, 1) . '%' : '데이터 없음';
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(글로벌 속도 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 (제목 삭제) -->
                <div class="col-8">
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
                                    <td><strong>Origin ({{ $originRegion ? ucfirst($originRegion) : 'N/A' }})</strong>
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
                                            <span class="text-muted">({{ $improvedRegions }} / {{ $eligibleRegions }}
                                                지역 개선)</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료(타이틀 크기 제거) -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>글로벌 8개 리전 측정망</strong>을 통해 수행된 웹 성능 시험 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>실제 사용자 환경을 시뮬레이션</u>하여 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 전 세계 주요 지역에서 측정한 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>상위 10% 이내의 웹 품질 성능</u>을 입증하였습니다. 이는 <strong>빠른 응답 속도</strong>와
                        <strong>글로벌 사용자 친화성</strong>을 갖춘 우수한 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            @if ($currentTest->metrics)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">국가·지역별 접속 속도</div>
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
                                        $formatMetric = function ($first, $repeat, $unit = 'ms') {
                                            if ($first === null) {
                                                return '<span class="text-muted">No Data</span>';
                                            }
                                            $firstFormatted = is_numeric($first) ? number_format($first, 1) : $first;
                                            $out = "<strong>{$firstFormatted}{$unit}</strong>";
                                            if ($repeat !== null) {
                                                $repeatFormatted = is_numeric($repeat)
                                                    ? number_format($repeat, 1)
                                                    : $repeat;
                                                $delta = $repeat - $first;
                                                $deltaFormatted = ($delta >= 0 ? '+' : '') . number_format($delta, 1);
                                                $deltaClass =
                                                    $delta < 0
                                                        ? 'text-success'
                                                        : ($delta > 0
                                                            ? 'text-danger'
                                                            : 'text-muted');
                                                $out .= "<br><small>{$repeatFormatted}{$unit} <span class='{$deltaClass}'>({$deltaFormatted})</span></small>";
                                            }
                                            return $out;
                                        };
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
            <!-- 추가 정보(줄간격 타이트) -->
            <div class="alert alert-info d-block tight">
                <p><strong>표시 형식:</strong> <span class="fw-bold">첫 방문</span> 값 → <span class="fw-bold">재방문</span> 값 (Δ
                    차이),
                    <span class="text-success">초록 = 개선</span> | <span class="text-danger">빨강 = 악화</span>
                </p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>TTFB(Time To First Byte):</strong> 사용자가 요청을 보낸 순간부터 서버에서 첫 번째 응답 바이트를 받기까지 걸리는 시간</p>
                <p><strong>Load Time:</strong> HTML, CSS, JS, 이미지 등 모든 리소스가 브라우저에 로드되어 페이지가 완전히 표시되기까지 걸리는 시간</p>
                <p><strong>재방문 성능:</strong> 브라우저 캐시, Keep-Alive 연결, CDN 캐싱 등의 효과로 재방문 시 더 빠른 로딩 속도를 보입니다</p>
            </div>
            <!-- 발행/만료 한 줄 + 서명 한 줄 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'p-load')
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
                $requestsPerSec = $metrics['http_reqs_rate'] ?? 0;
                $vus = $config['vus'] ?? 'N/A';
                $duration = $config['duration_seconds'] ?? 'N/A';

                $fmt = fn($v, $unit = 'ms') => is_numeric($v) ? number_format($v, 1) . $unit : '데이터 없음';
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(K6 부하 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>항목</th>
                                    <th>측정값</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Virtual Users × Duration</strong></td>
                                    <td>{{ $vus }} VUs × {{ $duration }}초</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Requests</strong></td>
                                    <td>{{ number_format($totalRequests) }} ({{ number_format($requestsPerSec, 1) }}
                                        req/s)</td>
                                </tr>
                                <tr>
                                    <td><strong>P95 응답시간</strong></td>
                                    <td>{{ number_format($p95Response) }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>에러율</strong></td>
                                    <td class="{{ $failureRate > 5 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($failureRate, 2) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 부하 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>K6 부하 테스트</strong>를 통해 <strong>{{ $vus }}명의 동시 사용자</strong>가
                        <strong>{{ $duration }}초</strong> 동안 실제 사용 패턴을 시뮬레이션한 결과입니다.
                    </p>
                    <p>모든 데이터는 실제 트래픽 환경을 모방하여 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 환경과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 부하 테스트 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>높은 동시 접속 처리 능력</u>을 입증하였습니다. 이는 <strong>안정적인 서비스</strong>와
                        <strong>우수한 서버 성능</strong>을 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">상세 성능 지표</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>응답시간</th>
                                    <th>측정값</th>
                                    <th>데이터 전송</th>
                                    <th>측정값</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Average</td>
                                    <td>{{ $fmt($metrics['http_req_duration_avg'] ?? 0) }}</td>
                                    <td>수신 데이터</td>
                                    <td>{{ number_format(($metrics['data_received'] ?? 0) / 1024 / 1024, 2) }} MB</td>
                                </tr>
                                <tr>
                                    <td>P90</td>
                                    <td>{{ $fmt($metrics['http_req_duration_p90'] ?? 0) }}</td>
                                    <td>송신 데이터</td>
                                    <td>{{ number_format(($metrics['data_sent'] ?? 0) / 1024 / 1024, 2) }} MB</td>
                                </tr>
                                <tr>
                                    <td>P95</td>
                                    <td>{{ $fmt($metrics['http_req_duration_p95'] ?? 0) }}</td>
                                    <td>반복 횟수</td>
                                    <td>{{ $metrics['iterations'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Max</td>
                                    <td>{{ $fmt($metrics['http_req_duration_max'] ?? 0) }}</td>
                                    <td>Think Time</td>
                                    <td>{{ $config['think_time_min'] ?? 3 }}-{{ $config['think_time_max'] ?? 10 }}초
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>에러율 기준:</strong> <span class="text-success">1% 미만 = 우수</span> | <span class="text-danger">5%
                        이상 = 개선 필요</span></p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Virtual Users:</strong> 동시 접속 가상 사용자 수 | <strong>P95:</strong> 95%의 요청이 응답받은 시간</p>
                <p><strong>Think Time:</strong> 실제 사용자의 페이지 간 이동 패턴을 모방한 대기 시간</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'p-mobile')
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(모바일 성능 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>항목</th>
                                    <th>측정값</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Median 응답시간 평균</strong></td>
                                    <td>{{ $overall['medianAvgMs'] ?? 0 }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>Long Tasks 평균</strong></td>
                                    <td>{{ $overall['longTasksAvgMs'] ?? 0 }}ms</td>
                                </tr>
                                <tr>
                                    <td><strong>JS 런타임 에러 (자사/외부)</strong></td>
                                    <td>{{ $overall['jsErrorsFirstPartyTotal'] ?? 0 }} /
                                        {{ $overall['jsErrorsThirdPartyTotal'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>렌더 폭 초과</strong></td>
                                    <td>{{ !empty($overall['bodyOverflowsViewport']) ? '있음' : '없음' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 모바일 성능 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Playwright</strong>를 통해 <strong>6개 대표 모바일 기기</strong>에서 CPU ×4 스로틀링으로 실제 모바일 환경을
                        시뮬레이션한 결과입니다.</p>
                    <p>iOS 3종(iPhone SE, 11, 15 Pro)과 Android 3종(Galaxy S9+, S20 Ultra, Pixel 5)에서 측정되었습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 웹사이트 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 모바일 성능 테스트 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 모바일 최적화 수준</u>을 입증하였습니다. 이는 <strong>빠른 모바일 렌더링</strong>과
                        <strong>안정적인 런타임</strong>을 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">기기별 측정 결과</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>디바이스</th>
                                    <th>Median</th>
                                    <th>TBT</th>
                                    <th>JS(자사/외부)</th>
                                    <th>렌더폭</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($results, 0, 6) as $result)
                                    <tr>
                                        <td><strong>{{ str_replace(['iPhone ', 'Galaxy ', 'Pixel '], ['i', 'G', 'P'], $result['device'] ?? 'Unknown') }}</strong>
                                        </td>
                                        <td>{{ $result['medianMs'] ?? 0 }}ms</td>
                                        <td>{{ $result['longTasksTotalMs'] ?? 0 }}ms</td>
                                        <td>{{ $result['jsErrorsFirstPartyCount'] ?? 0 }}/{{ $result['jsErrorsThirdPartyCount'] ?? 0 }}
                                        </td>
                                        <td>{{ !empty($result['bodyOverflowsViewport']) ? '초과' : '정상' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>측정 환경:</strong> 각 기기별 4회 실행(1회 웜업 제외), CPU ×4 스로틀링 적용</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>Median:</strong> 재방문 로딩 중간값 | <strong>TBT:</strong> JS 차단 시간(50ms 초과분) |
                    <strong>렌더폭:</strong> 수평 스크롤 발생 여부
                </p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-ssl')
            @php
                $results = $currentTest->results;
                $grade = $currentTest->overall_grade ?? 'N/A';
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
                $tlsVersion = $metrics['tls_version'] ?? 'N/A';
                $forwardSecrecy = $metrics['forward_secrecy'] ?? false;
                $hstsEnabled = $metrics['hsts_enabled'] ?? false;

                $vulnerableCount = 0;
                if (isset($results['vulnerabilities'])) {
                    foreach ($results['vulnerabilities'] as $status) {
                        if ($status['vulnerable'] ?? false) {
                            $vulnerableCount++;
                        }
                    }
                }
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(SSL/TLS 보안 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>항목</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>최고 TLS 버전</strong></td>
                                    <td>{{ $tlsVersion }}</td>
                                </tr>
                                <tr>
                                    <td><strong>완전 순방향 보안 (PFS)</strong></td>
                                    <td class="{{ $forwardSecrecy ? 'text-success' : 'text-danger' }}">
                                        {{ $forwardSecrecy ? '지원' : '미지원' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSTS</strong></td>
                                    <td class="{{ $hstsEnabled ? 'text-success' : 'text-warning' }}">
                                        {{ $hstsEnabled ? '활성' : '비활성' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>취약점</strong></td>
                                    <td class="{{ $vulnerableCount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $vulnerableCount > 0 ? $vulnerableCount . '개 발견' : '없음' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ SSL/TLS 보안 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>testssl.sh</strong>를 통해 서버의 SSL/TLS 구성을 종합적으로 검사한 결과입니다.</p>
                    <p>지원 프로토콜, 암호화 스위트, 인증서 유효성, 알려진 취약점 등을 포괄적으로 검증하였습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정과 보안 업데이트에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 SSL/TLS 보안 테스트 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>최고 수준의 보안 설정</u>을 입증하였습니다. 이는 <strong>안전한 암호화 통신</strong>과
                        <strong>최신 보안 표준 준수</strong>를 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">보안 상세 정보</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>인증서 정보</th>
                                    <th>값</th>
                                    <th>프로토콜 지원</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>발급자</td>
                                    <td>{{ substr($results['certificate']['issuer'] ?? 'N/A', 0, 20) }}</td>
                                    <td>지원 프로토콜</td>
                                    <td>{{ isset($results['supported_protocols']) ? implode(', ', array_slice($results['supported_protocols'], 0, 2)) : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>유효기간</td>
                                    <td>{{ $results['cert_expiry'] ?? 'N/A' }}</td>
                                    <td>취약 프로토콜</td>
                                    <td
                                        class="{{ isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0 ? implode(', ', $results['vulnerable_protocols']) : '없음' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>키 크기</td>
                                    <td>{{ $results['certificate']['key_size'] ?? 'N/A' }}</td>
                                    <td>IP 주소</td>
                                    <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($vulnerableCount > 0)
                <div class="alert alert-warning d-block tight">
                    @php
                        $vulnList = [];
                        foreach ($results['vulnerabilities'] as $vuln => $status) {
                            if ($status['vulnerable'] ?? false) {
                                $vulnList[] = strtoupper(str_replace(['_', '-'], ' ', $vuln));
                            }
                        }
                    @endphp
                    <p><strong>취약점:</strong>
                        {{ implode(', ', array_slice($vulnList, 0, 5)) }}{{ count($vulnList) > 5 ? ' 외 ' . (count($vulnList) - 5) . '개' : '' }}
                    </p>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>testssl.sh:</strong> GitHub 10K+ 스타 오픈소스 | <strong>PFS:</strong> 완전 순방향 보안 |
                    <strong>HSTS:</strong> HTTPS 강제
                </p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>검사 항목:</strong> Heartbleed, POODLE, BEAST, CRIME, FREAK 등 주요 SSL/TLS 취약점 종합 검사</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-sslyze')
            @php
                $results = $currentTest->results;
                $analysis = $results['analysis'] ?? [];
                $issues = $results['issues'] ?? [];

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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(SSL/TLS 심층 분석)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>항목</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>TLS 버전</strong></td>
                                    <td>
                                        @if ($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)
                                            TLS 1.3 지원
                                        @elseif ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                            TLS 1.2 (1.3 미지원)
                                        @else
                                            구버전만 지원
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>PFS 비율</strong></td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>OCSP Stapling</strong></td>
                                    <td
                                        class="{{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? 'text-success' : 'text-danger' }}">
                                        {{ ($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL' ? '활성' : '비활성' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSTS</strong></td>
                                    <td
                                        class="{{ !empty($analysis['http_headers']['hsts']) ? 'text-success' : 'text-danger' }}">
                                        {{ !empty($analysis['http_headers']['hsts']) ? '설정됨' : '미설정' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ SSL/TLS 심층 분석 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>SSLyze v5.x</strong>를 통해 SSL/TLS 설정을 종합적으로 분석한 결과입니다.</p>
                    <p>TLS 프로토콜, 암호군, 인증서 체인, OCSP, HSTS 등 모든 보안 요소를 정밀 검사하였습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정 변경에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 SSL/TLS 심층 분석 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>최고 수준의 암호화 보안</u>을 입증하였습니다. 이는 <strong>최신 TLS 프로토콜</strong>과
                        <strong>강력한 암호군 설정</strong>을 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">상세 분석 결과</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>암호군 분석</th>
                                    <th>값</th>
                                    <th>인증서 정보</th>
                                    <th>값</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TLS 1.2 암호군</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['total'] ?? 0 }}개</td>
                                    <td>키 알고리즘</td>
                                    <td>{{ $analysis['certificate']['details']['key_algorithm'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>강한 암호</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['strong'] ?? 0 }}개</td>
                                    <td>키 크기</td>
                                    <td>{{ $analysis['certificate']['details']['key_size'] ?? 'N/A' }}비트</td>
                                </tr>
                                <tr>
                                    <td>약한 암호</td>
                                    <td>{{ $analysis['cipher_suites']['tls_1_2']['weak'] ?? 0 }}개</td>
                                    <td>만료까지</td>
                                    <td>{{ $analysis['certificate']['details']['days_to_expiry'] ?? 'N/A' }}일</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if (!empty($issues) && count($issues) > 0)
                <div class="alert alert-warning d-block tight">
                    <p><strong>보안 이슈:</strong>
                        {{ implode(', ', array_slice($issues, 0, 3)) }}{{ count($issues) > 3 ? ' 외 ' . (count($issues) - 3) . '개' : '' }}
                    </p>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>SSLyze:</strong> Mozilla/Qualys/IETF 권장 도구 | <strong>PFS:</strong>
                    {{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] ?? 0 }}% | <strong>TLS 1.3:</strong>
                    {{ $analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false ? '지원' : '미지원' }}</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>검사 항목:</strong> TLS 프로토콜, 암호군 강도, 인증서 체인, OCSP Stapling, HSTS, 타원곡선 암호</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-header')
            @php
                $report = $currentTest->results;
                $metrics = $currentTest->metrics;
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

                $csp = $metrics['headers']['csp'] ?? [];
                $hsts = $metrics['headers']['hsts'] ?? [];

                $presentHeaders = 0;
                foreach ($metrics['breakdown'] ?? [] as $header) {
                    if (!empty($header['value'])) {
                        $presentHeaders++;
                    }
                }
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(보안 헤더 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>항목</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>적용된 헤더</strong></td>
                                    <td>{{ $presentHeaders }}/6개</td>
                                </tr>
                                <tr>
                                    <td><strong>CSP</strong></td>
                                    <td
                                        class="{{ $csp['present'] ?? false ? ($csp['strong'] ?? false ? 'text-success' : 'text-warning') : 'text-danger' }}">
                                        {{ $csp['present'] ?? false ? ($csp['strong'] ?? false ? '강함' : '약함') : '없음' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>HSTS</strong></td>
                                    <td class="{{ $hsts['present'] ?? false ? 'text-success' : 'text-danger' }}">
                                        {{ $hsts['present'] ?? false ? '설정됨 (' . number_format(($hsts['max_age'] ?? 0) / 86400) . '일)' : '없음' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>X-Frame-Options</strong></td>
                                    <td>
                                        @php
                                            $xfo = '';
                                            foreach ($metrics['breakdown'] ?? [] as $header) {
                                                if ($header['key'] === 'X-Frame-Options') {
                                                    $xfo = $header['value'] ?? '없음';
                                                    break;
                                                }
                                            }
                                        @endphp
                                        {{ substr($xfo ?: '없음', 0, 20) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 보안 헤더 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>6대 핵심 보안 헤더</strong> 종합 검사를 통해 웹 보안 수준을 측정한 결과입니다.</p>
                    <p>CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS를 검사하였습니다.
                    </p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 서버 설정 변경에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 보안 헤더 테스트 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 웹 보안 설정</u>을 입증하였습니다. 이는 <strong>XSS, 클릭재킹</strong> 등
                        주요 웹 취약점에 대한 <strong>강력한 방어 체계</strong>를 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-title">헤더별 점수</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>헤더</th>
                                    <th>값</th>
                                    <th>점수</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($metrics['breakdown'] ?? [], 0, 6) as $item)
                                    <tr>
                                        <td><strong>{{ str_replace(['Content-Security-Policy', 'X-Content-Type-Options', 'Permissions-Policy', 'Strict-Transport-Security'], ['CSP', 'X-C-T-O', 'Perm-Policy', 'HSTS'], $item['key']) }}</strong>
                                        </td>
                                        <td class="text-truncate" style="max-width: 250px;">
                                            {{ substr($item['value'] ?? '없음', 0, 30) }}{{ strlen($item['value'] ?? '') > 30 ? '...' : '' }}
                                        </td>
                                        <td>{{ round((($item['score'] ?? 0) * 100) / 60, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>CSP:</strong> XSS 방어 | <strong>XFO:</strong> 클릭재킹 방지 | <strong>HSTS:</strong> HTTPS 강제</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>6대 헤더:</strong> CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy,
                    Permissions-Policy, HSTS</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-scan')
            @php
                $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                $technologies = $currentTest->results['technologies'] ?? [];
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

                $totalVulns =
                    ($vulnerabilities['critical'] ?? 0) +
                    ($vulnerabilities['high'] ?? 0) +
                    ($vulnerabilities['medium'] ?? 0) +
                    ($vulnerabilities['low'] ?? 0) +
                    ($vulnerabilities['informational'] ?? 0);
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(보안 취약점 스캔)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>위험도</th>
                                    <th>개수</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Critical</strong></td>
                                    <td class="{{ ($vulnerabilities['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $vulnerabilities['critical'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>High</strong></td>
                                    <td class="{{ ($vulnerabilities['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $vulnerabilities['high'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Medium</strong></td>
                                    <td class="{{ ($vulnerabilities['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                        {{ $vulnerabilities['medium'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Low/Info</strong></td>
                                    <td>{{ ($vulnerabilities['low'] ?? 0) + ($vulnerabilities['informational'] ?? 0) }}개
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 보안 취약점 스캔 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>OWASP ZAP</strong> 패시브 스캔을 통해 웹 보안 취약점을 분석한 결과입니다.</p>
                    <p>보안 헤더, 민감정보 노출, 세션 관리 등을 비침입적으로 검사하여 총 {{ $totalVulns }}개의 이슈를 발견했습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 보안 업데이트에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 보안 취약점 스캔 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 보안 수준</u>을 입증하였습니다. 이는 <strong>주요 보안 취약점이 없고</strong>
                        <strong>안전한 구성</strong>을 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">주요 발견사항</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>취약점명</th>
                                        <th>위험도</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($vulnerabilities['details'], 0, 5) as $vuln)
                                        <tr>
                                            <td>{{ substr($vuln['name'], 0, 50) }}{{ strlen($vuln['name']) > 50 ? '...' : '' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ match ($vuln['risk']) {
                                                        'critical' => 'bg-red-lt text-red-lt-fg',
                                                        'high' => 'bg-orange-lt text-orange-lt-fg',
                                                        'medium' => 'bg-yellow-lt text-yellow-lt-fg',
                                                        'low' => 'bg-blue-lt text-blue-lt-fg',
                                                        default => 'bg-azure-lt text-azure-lt-fg',
                                                    } }}">{{ ucfirst($vuln['risk']) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>OWASP ZAP:</strong> 세계 표준 웹 보안 테스팅 도구 | <strong>패시브 스캔:</strong> 비침입적 HTTP 응답 분석</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>검사:</strong> 보안 헤더, 민감정보 노출, 세션 관리, 기술 스택 | <strong>발견:</strong> {{ $totalVulns }}개 이슈,
                    {{ count($technologies) }}개 기술</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 's-nuclei')
            @php
                $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                $metrics = $currentTest->metrics ?? [];
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

                $totalVulns =
                    ($metrics['vulnerability_counts']['critical'] ?? 0) +
                    ($metrics['vulnerability_counts']['high'] ?? 0) +
                    ($metrics['vulnerability_counts']['medium'] ?? 0) +
                    ($metrics['vulnerability_counts']['low'] ?? 0) +
                    ($metrics['vulnerability_counts']['info'] ?? 0);
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(최신 CVE 취약점 스캔)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 (컴팩트) -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span>
                                </div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>위험도</th>
                                    <th>개수</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Critical</strong></td>
                                    <td
                                        class="{{ ($metrics['vulnerability_counts']['critical'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>High</strong></td>
                                    <td
                                        class="{{ ($metrics['vulnerability_counts']['high'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ $metrics['vulnerability_counts']['high'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Medium</strong></td>
                                    <td
                                        class="{{ ($metrics['vulnerability_counts']['medium'] ?? 0) > 0 ? 'text-warning' : '' }}">
                                        {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}개
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Low/Info</strong></td>
                                    <td>{{ ($metrics['vulnerability_counts']['low'] ?? 0) + ($metrics['vulnerability_counts']['info'] ?? 0) }}개
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 최신 CVE 취약점 스캔 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Nuclei by ProjectDiscovery</strong>를 통해 최신 CVE 취약점을 분석한 결과입니다.</p>
                    <p>2024-2025년 신규 CVE, 제로데이, 구성 오류 등을 검사하여 총 {{ $totalVulns }}개의 이슈를 발견했습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 보안 패치에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 최신 CVE 취약점 스캔 결과 <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>최신 보안 위협에 대한 우수한 대응</u>을 입증하였습니다. 이는 <strong>2024-2025년 CVE 패치</strong>와
                        <strong>안전한 구성 관리</strong>를 갖춘 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            @php
                $criticalHighList = [];
                foreach (['critical', 'high'] as $severity) {
                    foreach (array_slice($vulnerabilities[$severity] ?? [], 0, 2) as $vuln) {
                        $criticalHighList[] = ['name' => $vuln['name'] ?? 'Unknown', 'severity' => $severity];
                    }
                }
            @endphp
            @if (count($criticalHighList) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">주요 취약점</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>취약점명</th>
                                        <th>위험도</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($criticalHighList as $item)
                                        <tr>
                                            <td>{{ substr($item['name'], 0, 50) }}{{ strlen($item['name']) > 50 ? '...' : '' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $item['severity'] === 'critical' ? 'bg-red-lt text-red-lt-fg' : 'bg-orange-lt text-orange-lt-fg' }}">
                                                    {{ ucfirst($item['severity']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>Nuclei:</strong> 템플릿 기반 취약점 스캐너 | <strong>스캔:</strong>
                    {{ $metrics['templates_matched'] ?? 0 }}개 템플릿 | <strong>시간:</strong>
                    {{ $metrics['scan_duration'] ?? 0 }}초</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>커버리지:</strong> 2024-2025 CVE, Log4Shell, Spring4Shell, WordPress/Joomla/Drupal, Git/ENV 노출
                </p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-lighthouse')
            @php
                $results = $currentTest->results ?? [];
                $metrics = $currentTest->metrics ?? [];
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(Google Lighthouse 품질 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 4대 영역 점수 -->
                <div class="col-8">
                    <div class="row">
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['performance_score'] ?? 'N/A' }}</h3>
                                    <small>Performance</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['accessibility_score'] ?? 'N/A' }}</h3>
                                    <small>Accessibility</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['best_practices_score'] ?? 'N/A' }}</h3>
                                    <small>Best Practices</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $metrics['seo_score'] ?? 'N/A' }}</h3>
                                    <small>SEO</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Google Lighthouse 엔진</strong>을 통해 수행된 웹 품질 시험 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>실제 브라우저 환경을 시뮬레이션</u>하여 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 Google Lighthouse 품질 측정 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>상위 10% 이내의 웹 품질 수준</u>을 입증하였습니다. 이는 <strong>우수한 성능</strong>과 
                        <strong>높은 접근성, SEO 최적화</strong>를 갖춘 고품질 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            
            <!-- Core Web Vitals -->
            @if(isset($results['audits']))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-title">Core Web Vitals 측정 결과</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>지표</th>
                                        <th>측정값</th>
                                        <th>권장 기준</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($results['audits']['first-contentful-paint']))
                                        <tr>
                                            <td><strong>FCP</strong></td>
                                            <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">1.8초 이내</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['largest-contentful-paint']))
                                        <tr>
                                            <td><strong>LCP</strong></td>
                                            <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">2.5초 이내</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['cumulative-layout-shift']))
                                        <tr>
                                            <td><strong>CLS</strong></td>
                                            <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">0.1 이하</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['total-blocking-time']))
                                        <tr>
                                            <td><strong>TBT</strong></td>
                                            <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">200ms 이내</td>
                                        </tr>
                                    @endif
                                    @if(isset($results['audits']['speed-index']))
                                        <tr>
                                            <td><strong>Speed Index</strong></td>
                                            <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                            <td class="text-muted">3.4초 이내</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>4대 평가 영역:</strong> Performance (성능), Accessibility (접근성), Best Practices (모범 사례), SEO (검색 최적화)</p>
                <p class="text-muted small">각 영역은 100점 만점으로 평가되며, 종합 점수는 4개 영역의 가중 평균입니다.</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>FCP:</strong> 첫 콘텐츠 표시 시간 | <strong>LCP:</strong> 가장 큰 콘텐츠 렌더링 시점</p>
                <p><strong>CLS:</strong> 레이아웃 이동 누적 점수 | <strong>TBT:</strong> 메인 스레드 차단 시간</p>
            </div>
            
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-accessibility')
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(웹 접근성 검사)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 위반 사항 요약 -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-danger">{{ $counts['critical'] ?? 0 }}</div>
                                    <small>Critical</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-orange">{{ $counts['serious'] ?? 0 }}</div>
                                    <small>Serious</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-warning">{{ $counts['moderate'] ?? 0 }}</div>
                                    <small>Moderate</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card card-sm">
                                <div class="card-body text-center py-2">
                                    <div class="h3 mb-0 text-info">{{ $counts['minor'] ?? 0 }}</div>
                                    <small>Minor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <strong>총 위반 건수: {{ $counts['total'] ?? 0 }}건</strong>
                    </div>
                </div>
            </div>
            
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>axe-core 엔진(Deque Systems)</strong>을 통해 수행된 웹 접근성 시험 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>WCAG 2.1 국제 표준</u>에 따라 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 웹 접근성 검사 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 웹 접근성 수준</u>을 입증하였습니다. 이는 <strong>장애인, 고령자를 포함한 모든 사용자</strong>가 
                        동등하게 이용할 수 있는 포용적인 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            
            <!-- 주요 위반 사항 -->
            @if (!empty($violations) && count($violations) > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">주요 위반 사항 (상위 5개)</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">중요도</th>
                                        <th>문제 설명</th>
                                        <th width="60">영향</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($violations, 0, 5) as $violation)
                                        @php
                                            $impactClass = match (strtolower($violation['impact'])) {
                                                'critical' => 'text-danger',
                                                'serious' => 'text-orange',
                                                'moderate' => 'text-warning',
                                                default => 'text-info',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="{{ $impactClass }}">
                                                <strong>{{ ucfirst($violation['impact']) }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($violation['help'], 80) }}</small>
                                            </td>
                                            <td>
                                                <small>{{ count($violation['nodes'] ?? []) }}개</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>접근성 중요도:</strong> 
                    <span class="text-danger">Critical</span> (기능 차단) | 
                    <span class="text-orange">Serious</span> (주요 제한) | 
                    <span class="text-warning">Moderate</span> (부분 불편) | 
                    <span class="text-info">Minor</span> (경미)
                </p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>WCAG 2.1 4대 원칙:</strong> 인지 가능성, 운용 가능성, 이해 가능성, 견고성</p>
                <p><strong>법적 준수:</strong> 한국 장애인차별금지법, 미국 ADA, EU EN 301 549 표준 적용</p>
            </div>
            
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-compatibility')
            @php
                $report = $currentTest->results['report'] ?? [];
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
                $totals = $report['totals'] ?? [];
                $okCount = $totals['okCount'] ?? 0;
                $jsFirstPartyTotal = $totals['jsFirstPartyTotal'] ?? 0;
                $jsThirdPartyTotal = $totals['jsThirdPartyTotal'] ?? null;
                $cssTotal = $totals['cssTotal'] ?? 0;
                $strictMode = !empty($report['strictMode']);
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(브라우저 호환성 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 종합 결과 -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $okCount }}/3</h3>
                                    <small>정상 브라우저</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $jsFirstPartyTotal }}</h3>
                                    <small>JS 오류(자사)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $cssTotal }}</h3>
                                    <small>CSS 오류</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h5 class="mb-0">{{ $strictMode ? '엄격' : '기본' }}</h5>
                                    <small>테스트 모드</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!is_null($jsThirdPartyTotal))
                        <div class="text-center mt-1">
                            <small class="text-muted">타사 JS 오류: {{ $jsThirdPartyTotal }}</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Playwright 엔진(Microsoft)</strong>을 통해 수행된 브라우저 호환성 시험 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>Chrome, Firefox, Safari 3대 주요 브라우저</u>에서 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 브라우저 호환성 검사 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 크로스 브라우저 호환성</u>을 입증하였습니다. 이는 <strong>모든 주요 브라우저</strong>에서 
                        안정적으로 작동하는 고품질 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            
            <!-- 브라우저별 결과 -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">브라우저별 결과</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>브라우저</th>
                                    <th>상태</th>
                                    <th>JS 자사</th>
                                    <th>CSS</th>
                                    <th>비고</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['perBrowser'] as $browser)
                                    @php
                                        $jsFirst = $browser['jsFirstPartyCount'] ?? ($browser['jsErrorCount'] ?? 0);
                                        $browserOk = !empty($browser['ok']);
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $browser['browser'] ?? '' }}</strong></td>
                                        <td>
                                            @if ($browserOk)
                                                <span class="text-success">✓</span>
                                            @else
                                                <span class="text-danger">✗</span>
                                            @endif
                                        </td>
                                        <td>{{ $jsFirst }}</td>
                                        <td>{{ $browser['cssErrorCount'] ?? 0 }}</td>
                                        <td>
                                            @if (!empty($browser['navError']))
                                                <small class="text-danger">오류</small>
                                            @else
                                                <small class="text-muted">정상</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>테스트 브라우저:</strong> Chromium (Chrome/Edge), Firefox (Gecko), WebKit (Safari)</p>
                <p><strong>측정 지표:</strong> 정상 로드 여부, JavaScript 오류 (자사/타사 분류), CSS 파싱 오류</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>시장 점유율:</strong> Chrome 65%, Safari 19%, Firefox 3% (2024년 기준)</p>
                <p><strong>판정 모드:</strong> {{ $strictMode ? '엄격 모드 - 모든 오류 포함' : '기본 모드 - 자사 오류 중심' }}</p>
            </div>
            
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'q-visual')
            @php
                $results = $currentTest->results ?? [];
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(반응형 UI 적합성 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 종합 결과 -->
                <div class="col-8">
                    <div class="row g-1">
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ 9 - $overflowCount }}/9</h3>
                                    <small>정상 뷰포트</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $overflowCount }}</h3>
                                    <small>초과 건수</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-body py-2">
                                    <h3 class="mb-0">{{ $maxOverflowPx }}px</h3>
                                    <small>최대 초과</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Playwright 엔진(Chromium)</strong>을 통해 수행된 반응형 UI 시험 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>9개 주요 디바이스 뷰포트</u>에서 수집되었으며, 결과의 진위 여부는 QR 검증 시스템을 통해 누구나 확인할 수 있습니다.</p>
                    <p class="text-muted small">※ 본 시험은 특정 시점의 객관적 측정 결과로, 지속적인 개선과 최적화 여부에 따라 달라질 수 있습니다.</p>
                </div>
            </div>
            
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 반응형 UI 검사 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>우수한 반응형 웹 디자인</u>을 입증하였습니다. 이는 <strong>모든 디바이스</strong>에서 
                        수평 스크롤 없이 완벽하게 표시되는 사용자 친화적인 웹사이트임을 보여줍니다.
                    </p>
                </div>
            @endif
            
            <!-- 뷰포트별 결과 -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">뷰포트별 측정 결과</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-vcenter table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>디바이스</th>
                                    <th>크기</th>
                                    <th>상태</th>
                                    <th>초과</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($perViewport, 0, 9) as $vp)
                                    @php
                                        $hasOverflow = $vp['overflow'] ?? false;
                                        $overflowPx = $vp['overflowPx'] ?? 0;
                                        $deviceName = ucfirst(str_replace('-', ' ', explode('-', $vp['viewport'])[0] ?? ''));
                                    @endphp
                                    <tr>
                                        <td><small><strong>{{ Str::limit($deviceName, 10) }}</strong></small></td>
                                        <td><small>{{ $vp['w'] ?? 0 }}×{{ $vp['h'] ?? 0 }}</small></td>
                                        <td>
                                            @if ($hasOverflow)
                                                <span class="text-danger">✗</span>
                                            @else
                                                <span class="text-success">✓</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($overflowPx > 0)
                                                <small class="text-danger">+{{ $overflowPx }}px</small>
                                            @else
                                                <small class="text-muted">0</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>테스트 뷰포트:</strong> 모바일(360-414px), 폴더블(672px), 태블릿(768-1024px), 데스크톱(1280-1440px)</p>
                <p><strong>측정 기준:</strong> body 렌더 폭 vs viewport 폭 비교 (초과 시 수평 스크롤 발생)</p>
            </div>
            
            <div class="alert alert-light d-block tight">
                <p><strong>판정 사유:</strong> {{ $reason }}</p>
                <p><strong>모바일 트래픽:</strong> 전체 웹 트래픽의 60% 이상 (2024년 기준)</p>
            </div>
            
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일:
                    {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-links')
            @php
                $results = $currentTest->results ?? [];
                $totals = $results['totals'] ?? [];
                $rates = $results['rates'] ?? [];
                $overall = $results['overall'] ?? [];
                $samples = $results['samples'] ?? [];
                
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(링크 검증 테스트)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>구분</th>
                                    <th>검사</th>
                                    <th>오류</th>
                                    <th>오류율</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>전체</strong></td>
                                    <td>{{ $totals['httpChecked'] ?? 0 }}개</td>
                                    <td>{{ ($totals['internalErrors'] ?? 0) + ($totals['externalErrors'] ?? 0) }}</td>
                                    <td>{{ $rates['overallErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>내부</strong></td>
                                    <td>{{ $totals['internalChecked'] ?? 0 }}개</td>
                                    <td>{{ $totals['internalErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['internalErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>외부</strong></td>
                                    <td>{{ $totals['externalChecked'] ?? 0 }}개</td>
                                    <td>{{ $totals['externalErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['externalErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>이미지</strong></td>
                                    <td>{{ $totals['imageChecked'] ?? 0 }}개</td>
                                    <td>{{ $totals['imageErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['imageErrorRate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>앵커</strong></td>
                                    <td>{{ $totals['anchorChecked'] ?? 0 }}개</td>
                                    <td>{{ $totals['anchorErrors'] ?? 0 }}</td>
                                    <td>{{ $rates['anchorErrorRate'] ?? 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Playwright 기반 링크 검증 도구</strong>를 통해 수행된 전체 링크 유효성 검사 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>실제 브라우저 환경</u>에서 JavaScript 동적 콘텐츠까지 포함하여 수집되었습니다.</p>
                    <p class="text-muted small">※ 본 검사는 특정 시점의 링크 상태로, 외부 사이트 변경 등에 따라 결과가 달라질 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 링크 검증 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>웹사이트 링크 무결성이 우수</u>함을 입증하였습니다.
                    </p>
                </div>
            @endif
            <!-- 오류 링크 샘플 -->
            @php
                $linkSamples = $samples['links'] ?? [];
                $imageSamples = $samples['images'] ?? [];
                $anchorSamples = $samples['anchors'] ?? [];
                $totalErrorSamples = count($linkSamples) + count($imageSamples) + count($anchorSamples);
            @endphp
            @if ($totalErrorSamples > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">오류 링크 샘플</div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>유형</th>
                                        <th>URL/링크</th>
                                        <th>상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sampleCount = 0; @endphp
                                    @foreach (array_slice($linkSamples, 0, 3) as $sample)
                                        @php $sampleCount++; @endphp
                                        <tr>
                                            <td>링크</td>
                                            <td class="text-break small">{{ Str::limit($sample['url'] ?? '', 50) }}</td>
                                            <td>{{ $sample['status'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach (array_slice($imageSamples, 0, 3 - $sampleCount) as $sample)
                                        @php $sampleCount++; @endphp
                                        <tr>
                                            <td>이미지</td>
                                            <td class="text-break small">{{ Str::limit($sample['url'] ?? '', 50) }}</td>
                                            <td>{{ $sample['status'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach (array_slice($anchorSamples, 0, 6 - $sampleCount) as $sample)
                                        <tr>
                                            <td>앵커</td>
                                            <td class="text-break small">{{ $sample['href'] ?? '' }}</td>
                                            <td>없음</td>
                                        </tr>
                                    @endforeach
                                    @if ($totalErrorSamples > 6)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... 총 {{ $totalErrorSamples }}개 오류 감지</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- 리다이렉트 정보 -->
            <div class="alert alert-secondary d-block tight">
                <p><strong>최대 리다이렉트 체인:</strong> {{ $totals['maxRedirectChainEffective'] ?? 0 }}단계
                @if (($totals['maxRedirectChainEffective'] ?? 0) > 2)
                    <span class="text-warning">(최적화 필요)</span>
                @endif
                </p>
                @if (!empty($totals['navError']))
                    <p class="text-danger small mb-0">네비게이션 오류: {{ Str::limit($totals['navError'], 80) }}</p>
                @endif
            </div>
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>링크 무결성 효과:</strong> 이탈률 20%↓, 페이지 속도 15%↑, 사용자 만족도 25%↑</p>
                <p>404 오류 즉시 수정 | 리다이렉트 최소화 | 앵커 매칭 확인 | 정기 검사 필수</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>판정 사유:</strong> {{ Str::limit($overall['reason'] ?? '종합 평가 결과', 100) }}</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-structure')
            @php
                $results = $currentTest->results ?? [];
                $totals = $results['totals'] ?? [];
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
                
                $hasJsonLd = ($totals['jsonLdItems'] ?? 0) > 0;
                $parseErrors = $results['parseErrors'] ?? [];
                $perItem = $results['perItem'] ?? [];
                $actions = $results['actions'] ?? [];
                $types = $results['types'] ?? [];
                $richTypes = $totals['richEligibleTypes'] ?? [];
                $totalErrors = ($totals['parseErrors'] ?? 0) + ($totals['itemErrors'] ?? 0);
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(구조화 데이터 검증)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>구분</th>
                                    <th>수량</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>JSON-LD</strong></td>
                                    <td>{{ $totals['jsonLdBlocks'] ?? 0 }}개</td>
                                    <td>{{ ($totals['jsonLdBlocks'] ?? 0) > 0 ? '구현' : '미구현' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>스키마</strong></td>
                                    <td>{{ $totals['jsonLdItems'] ?? 0 }}개</td>
                                    <td>
                                        @if (($totals['jsonLdItems'] ?? 0) >= 3)
                                            충분
                                        @elseif (($totals['jsonLdItems'] ?? 0) > 0)
                                            기본
                                        @else
                                            없음
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>오류/경고</strong></td>
                                    <td>{{ $totalErrors }}/{{ $totals['itemWarnings'] ?? 0 }}</td>
                                    <td>
                                        @if ($totalErrors === 0 && ($totals['itemWarnings'] ?? 0) === 0)
                                            완벽
                                        @elseif ($totalErrors === 0)
                                            양호
                                        @else
                                            개선필요
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Rich Results</strong></td>
                                    <td>{{ is_array($richTypes) ? count($richTypes) : 0 }}개</td>
                                    <td>
                                        @if (is_array($richTypes) && count($richTypes) > 0)
                                            {{ implode(', ', array_slice($richTypes, 0, 2)) }}
                                        @else
                                            없음
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>기타형식</strong></td>
                                    <td colspan="2">
                                        Microdata: {{ !empty($totals['hasMicrodata']) ? '✓' : '✗' }}
                                        RDFa: {{ !empty($totals['hasRdfa']) ? '✓' : '✗' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Playwright 기반 구조화 데이터 검증 도구</strong>를 통해 수행된 Schema.org 규격 검사 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>Google Rich Results Test 기준</u>에 준하여 평가되었습니다.</p>
                    <p class="text-muted small">※ 본 검사는 특정 시점의 구조화 데이터 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 구조화 데이터 검증 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>검색 결과 Rich Snippets 표시 자격</u>을 갖추었습니다.
                    </p>
                </div>
            @endif
            <!-- 스키마 타입 분포 -->
            @if (!empty($types))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">스키마 타입 분포</div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>@type</th>
                                        <th>개수</th>
                                        <th>Rich Results</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($types, 0, 5) as $row)
                                        <tr>
                                            <td><code>{{ $row['type'] }}</code></td>
                                            <td>{{ $row['count'] }}</td>
                                            <td>
                                                @if (in_array($row['type'], ['Article', 'Product', 'Recipe', 'Event', 'FAQPage', 'LocalBusiness', 'Review']))
                                                    ✓
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if (count($types) > 5)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... 외 {{ count($types) - 5 }}개 타입</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- 검증 이슈 -->
            @if ($totalErrors > 0 || ($totals['itemWarnings'] ?? 0) > 0)
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ 검증 이슈</p>
                    @if (!empty($parseErrors))
                        <p class="small mb-1">파싱 오류: {{ count($parseErrors) }}개 블록</p>
                    @endif
                    @php
                        $errorCount = 0;
                        $warningCount = 0;
                        foreach ($perItem as $item) {
                            if (!empty($item['errors'])) $errorCount++;
                            if (!empty($item['warnings'])) $warningCount++;
                        }
                    @endphp
                    @if ($errorCount > 0)
                        <p class="small mb-1">항목 오류: {{ $errorCount }}개 아이템</p>
                    @endif
                    @if ($warningCount > 0)
                        <p class="small mb-0">항목 경고: {{ $warningCount }}개 아이템</p>
                    @endif
                </div>
            @endif
            <!-- 권장 개선 사항 -->
            @if (!empty($actions))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚡ 권장 개선</p>
                    <ul class="mb-0 small">
                        @foreach (array_slice($actions, 0, 3) as $action)
                            <li>{{ Str::limit($action, 80) }}</li>
                        @endforeach
                        @if (count($actions) > 3)
                            <li>... 외 {{ count($actions) - 3 }}개</li>
                        @endif
                    </ul>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>구조화 데이터 효과:</strong> Rich Snippets 노출로 CTR 30%↑, 음성 검색 최적화, Knowledge Graph 등록</p>
                <p>JSON-LD 권장 | Schema.org 표준 | Organization + WebSite + BreadcrumbList 필수</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>판정 사유:</strong> {{ Str::limit($results['overall']['reason'] ?? '종합 평가 결과', 100) }}</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-crawl')
            @php
                $report = $currentTest->results ?? [];
                $grade = $currentTest->overall_grade ?? 'F';
                $score = $currentTest->overall_score ?? 0;
                $robots = $report['robots'] ?? [];
                $sitemap = $report['sitemap'] ?? [];
                $pages = $report['pages'] ?? [];
                $crawlPlan = $report['crawlPlan'] ?? [];
                
                $gradeClass = match ($grade) {
                    'A+' => 'badge bg-green-lt text-green-lt-fg',
                    'A' => 'badge bg-lime-lt text-lime-lt-fg',
                    'B' => 'badge bg-blue-lt text-blue-lt-fg',
                    'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                    'D' => 'badge bg-orange-lt text-orange-lt-fg',
                    'F' => 'badge bg-red-lt text-red-lt-fg',
                    default => 'badge bg-secondary',
                };
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(검색엔진 크롤링 검사)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($score)
                                    <div class="text-muted h4">{{ number_format($score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>구분</th>
                                    <th>값</th>
                                    <th>상태</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>robots.txt</strong></td>
                                    <td>{{ $robots['status'] ?? '-' }}</td>
                                    <td>{{ ($robots['exists'] ?? false) ? '존재' : '없음' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>sitemap.xml</strong></td>
                                    <td>{{ $sitemap['sitemapUrlCount'] ?? 0 }}개</td>
                                    <td>{{ ($sitemap['hasSitemap'] ?? false) ? '존재' : '없음' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>검사 페이지</strong></td>
                                    <td>{{ $pages['count'] ?? 0 }}개</td>
                                    <td>평균 {{ number_format($pages['qualityAvg'] ?? 0, 1) }}점</td>
                                </tr>
                                <tr>
                                    <td><strong>오류율</strong></td>
                                    <td>{{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%</td>
                                    <td>
                                        @if (($pages['errorRate4xx5xx'] ?? 0) === 0.0)
                                            정상
                                        @elseif (($pages['errorRate4xx5xx'] ?? 0) < 5)
                                            양호
                                        @else
                                            문제
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>중복률</strong></td>
                                    <td>{{ number_format($pages['duplicateRate'] ?? 0, 1) }}%</td>
                                    <td>{{ (($pages['duplicateRate'] ?? 0) <= 30) ? '양호' : '높음' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>robots.txt 준수 크롤러</strong>를 통해 수행된 검색엔진 크롤링 검사 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>실제 검색엔진 크롤링 방식</u>을 시뮬레이션하여 수집되었습니다.</p>
                    <p class="text-muted small">※ 본 검사는 특정 시점의 크롤링 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 크롤링 검사 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>검색엔진 최적화 우수 사이트</u>임을 입증하였습니다.
                    </p>
                </div>
            @endif
            <!-- Sitemap 파일 현황 -->
            @if (!empty($sitemap['sitemaps']))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="section-title">Sitemap 파일 현황</div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>파일명</th>
                                        <th>URL 수</th>
                                        <th>상태</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($sitemap['sitemaps'], 0, 5) as $s)
                                        <tr>
                                            <td>{{ basename($s['url']) }}</td>
                                            <td>{{ $s['count'] ?? 0 }}개</td>
                                            <td>{{ $s['ok'] ? '정상' : '오류' }}</td>
                                        </tr>
                                    @endforeach
                                    @if (count($sitemap['sitemaps']) > 5)
                                        <tr>
                                            <td colspan="3" class="text-muted small">... 외 {{ count($sitemap['sitemaps']) - 5 }}개 파일</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!-- 문제 페이지 요약 -->
            @php
                $errorPages = $report['samples']['errorPages'] ?? [];
                $lowQuality = collect($report['samples']['lowQuality'] ?? [])
                    ->filter(function ($page) {
                        return ($page['score'] ?? 100) < 50;
                    })
                    ->take(3)
                    ->values()
                    ->toArray();
            @endphp
            @if (!empty($errorPages) || !empty($lowQuality))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ 검출된 문제</p>
                    @if (!empty($errorPages))
                        <p class="small mb-1">오류 페이지(4xx/5xx): {{ count($errorPages) }}개 감지</p>
                    @endif
                    @if (!empty($lowQuality))
                        <p class="small mb-1">낮은 품질(50점 미만): {{ count($lowQuality) }}개 페이지</p>
                    @endif
                    @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                        <p class="small mb-0">중복 콘텐츠: 제목 {{ $pages['dupTitleCount'] ?? 0 }}개, 설명 {{ $pages['dupDescCount'] ?? 0 }}개</p>
                    @endif
                </div>
            @endif
            <!-- 크롤링 계획 요약 -->
            <div class="alert alert-secondary d-block tight">
                <p><strong>크롤링 계획:</strong> 총 {{ $crawlPlan['candidateCount'] ?? 0 }}개 URL 중 {{ $pages['count'] ?? 0 }}개 검사 완료</p>
                @if (!empty($crawlPlan['skipped']))
                    <p class="small mb-0">제외 URL: {{ count($crawlPlan['skipped']) }}개 (robots.txt 규칙 또는 외부 도메인)</p>
                @endif
            </div>
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>크롤링 최적화 효과:</strong> 색인 속도 50%↑, 검색 순위 20%↑, 이탈률 15%↓</p>
                <p>robots.txt 필수 | sitemap.xml 필수 | 페이지별 고유 메타데이터 | 404 오류 제거</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>판정 사유:</strong> {{ Str::limit($report['overall']['reason'] ?? '종합 평가 결과', 100) }}</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif

        @if ($test_type == 'c-meta')
            @php
                $results = $currentTest->results ?? [];
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
            @endphp
            <!-- 헤더 -->
            <div class="title-block">
                <div class="title-flex">
                    <div class="title-text">
                        <h1>웹 테스트 인증서 (Web Test Certificate)</h1>
                        <h2>(메타데이터 완성도 검사)</h2>
                        <h3>인증번호: {{ $certificate->code }}</h3>
                    </div>
                    <div class="title-qr">
                        {!! QrCode::size(80)->generate('https://www.devteam-test.com/' . $certificate->code . '/certified') !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- 좌측: 등급/점수/URL/일시 -->
                <div class="col-4">
                    <div class="card mb-4 score-card">
                        <div class="card-body text-center py-3">
                            <div class="mb-2">
                                <div class="h1 mb-1"><span class="{{ $gradeClass }}">{{ $grade }}</span></div>
                                @if ($currentTest->overall_score)
                                    <div class="text-muted h4">{{ number_format($currentTest->overall_score, 1) }}점</div>
                                @endif
                            </div>
                            <div class="mb-1" style="word-break: break-all;">{{ $currentTest->url }}</div>
                            <small class="text-muted d-block">
                                테스트 일시:
                                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- 우측: 요약 테이블 -->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>구분</th>
                                    <th>상태</th>
                                    <th>세부사항</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Title</strong></td>
                                    <td>
                                        @if ($analysis['title']['isOptimal'] ?? false)
                                            최적
                                        @elseif ($analysis['title']['isAcceptable'] ?? false)
                                            허용
                                        @elseif ($analysis['title']['isEmpty'] ?? true)
                                            없음
                                        @else
                                            부적절
                                        @endif
                                    </td>
                                    <td>{{ $summary['titleLength'] ?? 0 }}자</td>
                                </tr>
                                <tr>
                                    <td><strong>Description</strong></td>
                                    <td>
                                        @if ($analysis['description']['isOptimal'] ?? false)
                                            최적
                                        @elseif ($analysis['description']['isAcceptable'] ?? false)
                                            허용
                                        @elseif ($analysis['description']['isEmpty'] ?? true)
                                            없음
                                        @else
                                            부적절
                                        @endif
                                    </td>
                                    <td>{{ $summary['descriptionLength'] ?? 0 }}자</td>
                                </tr>
                                <tr>
                                    <td><strong>Open Graph</strong></td>
                                    <td>
                                        @if ($analysis['openGraph']['isPerfect'] ?? false)
                                            완벽
                                        @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                            기본
                                        @else
                                            부족
                                        @endif
                                    </td>
                                    <td>{{ $summary['openGraphFields'] ?? 0 }}개</td>
                                </tr>
                                <tr>
                                    <td><strong>Twitter Cards</strong></td>
                                    <td>
                                        @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                            완벽
                                        @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                            기본
                                        @else
                                            부족
                                        @endif
                                    </td>
                                    <td>{{ $summary['twitterCardFields'] ?? 0 }}개</td>
                                </tr>
                                <tr>
                                    <td><strong>Canonical/Hreflang</strong></td>
                                    <td>
                                        {{ ($summary['hasCanonical'] ?? false) ? '✓' : '✗' }} / 
                                        {{ $summary['hreflangCount'] ?? 0 }}개
                                    </td>
                                    <td>
                                        {{ ($summary['hasCanonical'] ?? false) ? '설정' : '미설정' }} /
                                        {{ $summary['hreflangCount'] ?? 0 }}개 언어
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 검증 완료 -->
            <div class="alert alert-success d-block text-start mb-3">
                <div class="fw-semibold mb-1">✅ 테스트 결과 검증 완료</div>
                <div class="tight">
                    <p>본 인증서는 <strong>Meta Inspector CLI</strong>를 통해 수행된 메타데이터 완성도 검사 결과에 근거합니다.</p>
                    <p>모든 데이터는 <u>실제 브라우저 렌더링 환경</u>에서 수집되었으며, SEO 모범 사례 기준으로 평가되었습니다.</p>
                    <p class="text-muted small">※ 본 검사는 특정 시점의 메타데이터 상태로, 웹사이트 업데이트에 따라 변경될 수 있습니다.</p>
                </div>
            </div>
            @if (in_array($grade, ['A+', 'A']))
                <div class="alert alert-primary d-block text-start mb-3">
                    <p class="mb-0">🌟 본 사이트는 메타데이터 완성도 검사 결과, <strong>{{ $grade }}</strong> 등급을 획득하여
                        <u>검색엔진 최적화(SEO) 우수 사이트</u>임을 입증하였습니다.
                    </p>
                </div>
            @endif
            <!-- 메타데이터 미리보기 -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="section-title">메타데이터 미리보기</div>
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="mb-2">
                                <div class="fw-bold small">Title ({{ $summary['titleLength'] ?? 0 }}자)</div>
                                <div class="text-muted small">{{ Str::limit($metadata['title'] ?: '제목 없음', 80) }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="fw-bold small">Description ({{ $summary['descriptionLength'] ?? 0 }}자)</div>
                                <div class="text-muted small">{{ Str::limit($metadata['description'] ?: '설명 없음', 150) }}</div>
                            </div>
                            <div>
                                <div class="fw-bold small">Canonical URL</div>
                                <div class="text-muted small text-break">{{ Str::limit($metadata['canonical'] ?: '미설정', 100) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Open Graph & Twitter Cards -->
            @if (!empty($metadata['openGraph']) || !empty($metadata['twitterCards']))
                <div class="row mb-3">
                    @if (!empty($metadata['openGraph']))
                        <div class="col-6">
                            <div class="small fw-bold mb-1">Open Graph 태그 ({{ count($metadata['openGraph']) }}개)</div>
                            <div class="small text-muted">
                                @foreach (array_slice($metadata['openGraph'], 0, 4) as $prop => $content)
                                    <div>• og:{{ $prop }}: {{ Str::limit($content, 30) }}</div>
                                @endforeach
                                @if (count($metadata['openGraph']) > 4)
                                    <div>... 외 {{ count($metadata['openGraph']) - 4 }}개</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (!empty($metadata['twitterCards']))
                        <div class="col-6">
                            <div class="small fw-bold mb-1">Twitter Cards ({{ count($metadata['twitterCards']) }}개)</div>
                            <div class="small text-muted">
                                @foreach (array_slice($metadata['twitterCards'], 0, 4) as $name => $content)
                                    <div>• twitter:{{ $name }}: {{ Str::limit($content, 25) }}</div>
                                @endforeach
                                @if (count($metadata['twitterCards']) > 4)
                                    <div>... 외 {{ count($metadata['twitterCards']) - 4 }}개</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            @if (!empty($results['issues']))
                <div class="alert alert-warning d-block tight">
                    <p class="fw-bold">⚠️ 발견된 문제점</p>
                    <ul class="mb-0 small">
                        @foreach (array_slice($results['issues'], 0, 3) as $issue)
                            <li>{{ Str::limit($issue, 80) }}</li>
                        @endforeach
                        @if (count($results['issues']) > 3)
                            <li>... 외 {{ count($results['issues']) - 3 }}개</li>
                        @endif
                    </ul>
                </div>
            @endif
            <!-- 추가 정보 -->
            <div class="alert alert-info d-block tight">
                <p><strong>메타데이터 중요성:</strong> 검색엔진 최적화(SEO) 성공의 핵심 요소로, 검색 노출과 클릭률에 직접 영향</p>
                <p>Title 50~60자, Description 120~160자 최적 | Open Graph 4대 필수요소 | Canonical URL 중복방지</p>
            </div>
            <div class="alert alert-light d-block tight">
                <p><strong>판정 사유:</strong> {{ Str::limit($results['grade']['reason'] ?? '종합 평가 결과', 100) }}</p>
            </div>
            <!-- 발행/만료 + 서명 -->
            <div class="text-center mt-4">
                <small class="text-muted d-block mb-2">
                    인증서 발행일: {{ $certificate->issued_at->format('Y-m-d') }} | 인증서 만료일: {{ $certificate->expires_at->format('Y-m-d') }}
                </small>

                <div class="signature-line">
                    <span class="label">Authorized by</span>
                    <span class="signature">Daniel Ahn</span>
                    <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
