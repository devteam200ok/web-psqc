<!doctype html>
<html lang="ko">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="canonical" href="https://www.devteam-app.com/{{ request()->path() != '/' ? request()->path() : '' }}" />

    @include('inc.component.seo')
    @include('inc.component.theme_css')

    <!-- Fonts -->
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
            font-family: 'Inter', 'Noto Sans KR', system-ui, -apple-system, sans-serif;
            font-size: 12px;
            line-height: 1.34;
            background: transparent !important;
        }

        .print-container {
            width: 185mm;
            margin: 0 auto;
        }

        /* 타이틀 */
        .title-block {
            padding: 28px 0 40px;
            position: relative;
        }

        .title-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .title-text {
            text-align: center;
        }

        .title-qr {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
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

        .title-block h3 {
            font-size: 13px;
            margin: 0;
            color: #6c757d;
        }

        /* 카드/테이블 */
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

        /* 점수 카드 */
        .score-card .h1 {
            font-size: 20px;
            margin: 0;
        }

        .score-card .h4 {
            font-size: 13px;
            margin: 2px 0 0;
        }

        .score-card small {
            font-size: 10.5px;
        }

        /* 서명 */
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
            display: inline-block;
            vertical-align: baseline;
            border: 0px
        }

        .sig-meta {
            font-size: 10.5px;
            color: #6b7280;
        }

        /* 카테고리 헤더 */
        .category-header {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 4px;
        }

        .category-performance {
            background: #fff3cd;
            color: #856404;
        }

        .category-security {
            background: #f8d7da;
            color: #721c24;
        }

        .category-quality {
            background: #d4edda;
            color: #155724;
        }

        .category-content {
            background: #e7e3fc;
            color: #6f42c1;
        }

        /* 테스트 항목 테이블 */
        .test-table {
            font-size: 10px;
            margin-bottom: 0;
        }

        .test-table td {
            padding: 3px 5px;
            vertical-align: middle;
        }

        .test-name {
            font-weight: 600;
            width: 30%;
        }

        .test-desc {
            color: #6c757d;
            width: 40%;
            font-size: 9px;
        }

        .test-grade {
            font-weight: 700;
            width: 15%;
            text-align: center;
        }

        .test-weighted {
            width: 15%;
            text-align: right;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-white">
    <div class="print-container">

        @php
            $metrics = $certification->metrics ?? [];
            $testTypes = \App\Models\WebTest::getTestTypes();

            $topPercent = match ($certification->overall_grade) {
                'A+' => '2%',
                'A' => '8%',
                'B' => '15%',
                'C' => '25%',
                'D' => '40%',
                default => '60%+',
            };

            $grade = $certification->overall_grade ?? 'F';
            $gradeClass = match ($grade) {
                'A+' => 'badge bg-green-lt text-green-lt-fg',
                'A' => 'badge bg-lime-lt text-lime-lt-fg',
                'B' => 'badge bg-blue-lt text-blue-lt-fg',
                'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                'D' => 'badge bg-orange-lt text-orange-lt-fg',
                'F' => 'badge bg-red-lt text-red-lt-fg',
                default => 'badge bg-secondary',
            };

            // 영역별 점수 합산
            $perf = 0;
            $sec = 0;
            $qual = 0;
            $cont = 0;
            $perf += ($metrics['performance']['p-speed']['score'] ?? 0) * 1.0;
            $perf += ($metrics['performance']['p-load']['score'] ?? 0) * 1.0;
            $perf += ($metrics['performance']['p-mobile']['score'] ?? 0) * 1.0;

            $sec += ($metrics['security']['s-ssl']['score'] ?? 0) * 0.8;
            $sec += ($metrics['security']['s-sslyze']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-header']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-scan']['score'] ?? 0) * 0.6;
            $sec += ($metrics['security']['s-nuclei']['score'] ?? 0) * 0.4;

            $qual += ($metrics['quality']['q-lighthouse']['score'] ?? 0) * 1.2;
            $qual += ($metrics['quality']['q-accessibility']['score'] ?? 0) * 0.7;
            $qual += ($metrics['quality']['q-compatibility']['score'] ?? 0) * 0.3;
            $qual += ($metrics['quality']['q-visual']['score'] ?? 0) * 0.3;

            $cont += ($metrics['content']['c-links']['score'] ?? 0) * 0.5;
            $cont += ($metrics['content']['c-structure']['score'] ?? 0) * 0.4;
            $cont += ($metrics['content']['c-crawl']['score'] ?? 0) * 0.4;
            $cont += ($metrics['content']['c-meta']['score'] ?? 0) * 0.2;

            // 테스트별 설명
            $testDesc = [
                'p-speed' => '8개 글로벌 리전 속도',
                'p-load' => 'K6 부하 테스트',
                'p-mobile' => '6종 모바일 성능',
                's-ssl' => 'testssl.sh 종합진단',
                's-sslyze' => 'SSLyze 심층분석',
                's-header' => '6대 보안헤더',
                's-scan' => 'OWASP ZAP 스캔',
                's-nuclei' => '최신 CVE 취약점',
                'q-lighthouse' => 'Google Lighthouse',
                'q-accessibility' => 'WCAG 2.1 접근성',
                'q-compatibility' => '3대 브라우저 호환',
                'q-visual' => '반응형 UI 적합성',
                'c-links' => '링크 무결성 검증',
                'c-structure' => 'Schema.org 구조화',
                'c-crawl' => '검색엔진 크롤링',
                'c-meta' => '메타데이터 완성도',
            ];

            // 가중치
            $weights = [
                'p-speed' => 1.0,
                'p-load' => 1.0,
                'p-mobile' => 1.0,
                's-ssl' => 0.8,
                's-sslyze' => 0.6,
                's-header' => 0.6,
                's-scan' => 0.6,
                's-nuclei' => 0.4,
                'q-lighthouse' => 1.2,
                'q-accessibility' => 0.7,
                'q-compatibility' => 0.3,
                'q-visual' => 0.3,
                'c-links' => 0.5,
                'c-structure' => 0.4,
                'c-crawl' => 0.4,
                'c-meta' => 0.2,
            ];

            // 등급별 색상 클래스
            $getGradeClass = function ($grade) {
                return match ($grade) {
                    'A+' => 'badge bg-green-lt text-green-lt-fg',
                    'A' => 'badge bg-lime-lt text-lime-lt-fg',
                    'B' => 'badge bg-blue-lt text-blue-lt-fg',
                    'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                    'D' => 'badge bg-orange-lt text-orange-lt-fg',
                    'F' => 'badge bg-red-lt text-red-lt-fg',
                    default => 'badge bg-secondary',
                };
            };
        @endphp

        <!-- 타이틀 -->
        <div class="title-block">
            <div class="title-flex">
                <div class="title-text">
                    <h1>PSQC 종합 인증서</h1>
                    <h2>성능(Performance) · 보안(Security) · 품질(Quality) · 콘텐츠(Content)</h2>
                    <h3>인증번호: {{ $certification->code }}</h3>
                </div>
                <div class="title-qr">
                    {!! QrCode::size(80)->generate(url('/psqc/certified/' . $certification->code)) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 좌측 점수 카드 -->
            <div class="col-5">
                <div class="card score-card">
                    <div class="card-body text-center py-3">
                        <div class="h1 mb-1"><span
                                class="{{ $gradeClass }}">{{ $certification->overall_grade }}</span></div>
                        <div class="h4 text-muted">{{ number_format($certification->overall_score, 1) }}/1000점</div>
                        <div class="my-2">{{ $certification->url }}</div>
                        <small class="text-muted d-block">평가일: {{ $certification->issued_at->format('Y-m-d') }}</small>
                        <small class="text-muted d-block">유효기간:
                            {{ $certification->expires_at->format('Y-m-d') }}</small>
                    </div>
                </div>
            </div>

            <!-- 우측 종합 요약 (헤더 없는 테이블) -->
            <div class="col-5 offset-2">
                <div class="table-responsive">
                    <table class="table table-sm mt-2">
                        <tbody>
                            <tr>
                                <td>성능(Performance)</td>
                                <td class="text-end">{{ number_format($perf, 0) }}/300</td>
                            </tr>
                            <tr>
                                <td>보안(Security)</td>
                                <td class="text-end">{{ number_format($sec, 0) }}/300</td>
                            </tr>
                            <tr>
                                <td>품질(Quality)</td>
                                <td class="text-end">{{ number_format($qual, 0) }}/250</td>
                            </tr>
                            <tr>
                                <td>콘텐츠(Content)</td>
                                <td class="text-end">{{ number_format($cont, 0) }}/150</td>
                            </tr>
                            <tr style="border-bottom: 0px #ffffff solid">
                                <td><strong>합계</strong></td>
                                <td class="text-end"><strong>{{ number_format($certification->overall_score, 1) }}/1000</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 설명 (상단 마진 20px) -->
        <div class="alert alert-info d-block text-start tight" style="margin-top:10px;">
            <p>본 인증서는 성능·보안·품질·콘텐츠 4개 영역, 16개 세부 지표를 기반으로 가중 평가(총 1000점)한 결과에 따라 발급됩니다.</p>
            <p class="mb-0">본 웹사이트는 <strong>{{ $certification->overall_grade }}</strong> 등급으로 평가되어 전 세계 상위
                <strong>{{ $topPercent }}</strong> 이내의 종합 품질에 해당함을 확인합니다.</p>
        </div>

        <!-- 16개 테스트 상세 (원점수 제거) -->
        <div class="row">
            <div class="col-6">
                <!-- 성능(Performance) -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-performance">성능(Performance)
                            ({{ number_format($perf, 0) }}/300)</div>
                        <table class="table table-sm test-table">
                            @foreach (['p-speed', 'p-load', 'p-mobile'] as $key)
                                @php
                                    $test = $metrics['performance'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }}점</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <!-- 보안(Security) -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-security">보안(Security) ({{ number_format($sec, 0) }}/300)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['s-ssl', 's-sslyze', 's-header', 's-scan', 's-nuclei'] as $key)
                                @php
                                    $test = $metrics['security'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }}점</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <!-- 품질(Quality) -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-quality">품질(Quality) ({{ number_format($qual, 0) }}/250)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['q-lighthouse', 'q-accessibility', 'q-compatibility', 'q-visual'] as $key)
                                @php
                                    $test = $metrics['quality'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }}점</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <!-- 콘텐츠(Content) -->
                <div class="card">
                    <div class="card-body">
                        <div class="category-header category-content">콘텐츠(Content) ({{ number_format($cont, 0) }}/150)
                        </div>
                        <table class="table table-sm test-table">
                            @foreach (['c-links', 'c-structure', 'c-crawl', 'c-meta'] as $key)
                                @php
                                    $test = $metrics['content'][$key] ?? null;
                                    $score = $test['score'] ?? 0;
                                    $weighted = $score * $weights[$key];
                                @endphp
                                <tr>
                                    <td class="test-name">{{ $testTypes[$key] }}</td>
                                    <td class="test-desc">{{ $testDesc[$key] }}</td>
                                    <td class="test-grade"><span
                                            class="{{ $getGradeClass($test['grade'] ?? 'F') }}">{{ $test['grade'] ?? '-' }}</span>
                                    </td>
                                    <td class="test-weighted">{{ number_format($weighted, 0) }}점</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 글로벌 웹 표준 참조 -->
        <div class="alert alert-info d-block text-start tight mt-3">
            <div class="fw-semibold mb-1">글로벌 웹 표준 참조 및 평가 체계</div>
            <p class="mb-1">PSQC는 ISO/IEC 25010, WCAG 2.1, Core Web Vitals, OWASP Top 10 등 국제 표준을 참조하여 개발된 독립적인 평가 인증입니다.
            </p>
            <p class="mb-1">• <strong>성능(Performance):</strong> Core Web Vitals (LCP<2.5초, INP<200ms, CLS<0.1) 기준
                    적용</p>
                    <p class="mb-1">• <strong>보안(Security):</strong> OWASP Top 10, CVE 데이터베이스 기반 취약점 스캔</p>
                    <p class="mb-1">• <strong>품질(Quality):</strong> WCAG 2.1 AA 수준 접근성, Lighthouse 품질 지표 활용</p>
                    <p class="mb-1">• <strong>콘텐츠(Content):</strong> Schema.org 구조화, SEO 모범 사례 준수 평가</p>
                    <p class="text-muted mb-0 mt-2">※ DevTeam-Test는 절대적 보안이나 완벽성을 보장하지 않으며, 측정 시점의 객관적 데이터를 제공합니다.</p>
        </div>

        <!-- 서명 -->
        <div class="text-center mt-4">
            <div class="signature-line">
                <span class="label">Authorized by</span>
                <span class="signature">Daniel Ahn</span>
                <div class="sig-meta">CEO, DevTeam Co., Ltd. (DevTeam-Test)</div>
            </div>
        </div>

    </div>
</body>
</htm
