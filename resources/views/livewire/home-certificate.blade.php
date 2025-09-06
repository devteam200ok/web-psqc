@section('title')
    <title>📘 인증 및 점수체계 종합 가이드 – PSQC 기준·등급·시험방법 | DevTeam Test</title>
    <meta name="description"
        content="DevTeam Test의 PSQC(Performance·Security·Quality·Content) 인증 체계와 등급 기준(A+~F), 16개 세부 시험방법 및 평가 지표를 한눈에 정리한 종합 가이드. 글로벌 속도·부하·모바일 성능, SSL·보안 헤더·취약점, Lighthouse·접근성, 링크·구조화 데이터·크롤링·메타데이터 기준을 제공합니다.">
    <meta name="keywords"
        content="PSQC, 웹 인증서, 웹 품질 등급, 성능 보안 품질 콘텐츠, 글로벌 속도, K6 부하 테스트, 모바일 성능, SSL 테스트, 보안 헤더, Nuclei, Lighthouse, 접근성, 링크 검증, 구조화 데이터, 크롤링, 메타데이터">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="인증 및 점수체계 종합 가이드 – PSQC 기준·등급·시험방법 | DevTeam Test" />
    <meta property="og:description" content="PSQC 인증 체계와 16개 세부 시험방법·등급 기준을 종합 정리. 글로벌 속도/보안/품질/콘텐츠 평가 지표 안내." />
    <meta property="og:locale" content="ko_KR" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="DevTeam Test 인증 및 점수체계 종합 가이드" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="인증 및 점수체계 종합 가이드 – PSQC 기준·등급·시험방법 | DevTeam Test" />
    <meta name="twitter:description" content="PSQC 인증·등급·시험방법을 한눈에. 성능·보안·품질·콘텐츠 16개 항목 평가 기준 안내." />
    <meta name="twitter:image" content="{{ App\Models\Setting::first()->og_image }}" />

    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'TechArticle',
    'headline' => '인증 및 점수체계 종합 가이드 – PSQC 기준·등급·시험방법',
    'about' => [
        'PSQC Certification',
        'Web Performance',
        'Web Security',
        'Web Quality',
        'Web Content'
    ],
    'inLanguage' => 'ko',
    'author' => [
        '@type' => 'Organization',
        'name' => 'DevTeam Co., Ltd.',   // ✅ 필수 필드
        'url'  => url('/'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'DevTeam Co., Ltd.',   // ✅ 필수 필드
        'url'  => url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => App\Models\Setting::first()->og_image ?? url('/images/og/default.png')
        ]
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => url()->current(),
        'name'  => '인증 및 점수체계 종합 가이드 – DevTeam Test'  // ✅ 필수 필드
    ],
    'url' => url()->current(),
    'name' => '인증 및 점수체계 종합 가이드',  // TechArticle 자체에도 name 추가
    'description' =>
        'DevTeam Test의 PSQC(Performance·Security·Quality·Content) 인증 체계와 등급 기준(A+~F), 16개 세부 시험방법 및 평가 지표를 종합 안내합니다.',
    'articleSection' => [
        '웹 테스트 인증서(Web Test Certificate)',
        'PSQC 종합 인증서(Master Certificate)',
        'Performance 점수 체계',
        'Security 점수 체계',
        'Quality 점수 체계',
        'Content 점수 체계'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
@section('css')
    <style>
        .score-formula {
            background: #f8f9fa;
            border-left: 4px solid #845ef7;
            padding: 1rem;
            margin: 1rem 0;
        }

        .standard-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            margin: 0.25rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .test-title {
            min-width: 100px;
        }

        .test-method {
            min-width: 160px;
        }

        .test-method-content {
            font-size: 0.8rem !important;
        }

        .grade-a-plus {
            color: #28a745 !important;
            font-weight: bold;
        }

        .grade-a {
            color: #377dff !important;
            font-weight: bold;
        }

        .grade-b {
            color: #fd7e14 !important;
            font-weight: bold;
        }

        .table th {
            font-size: 0.9rem;
        }

        .table td {
            font-size: 0.85rem;
        }

        .criteria-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            margin: 4px 0;
            min-width: 250px;
        }

        .criteria-detail {
            font-size: 0.8rem;
            line-height: 1.4;
        }
    </style>
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <!-- 헤더 -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="page-title">인증 및 점수체계 종합 가이드</h2>
                <div class="text-muted">DevTeam Test에서 제안하는 PSQC 인증 체계와 글로벌 웹 품질 표준에 대한 가이드입니다.</div>
            </div>
        </div>

        <!-- 개별 인증서 설명 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">웹 테스트 인증서 (Web Test Certificate)</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            DevTeam-Test의 각 테스트 항목에 대한 단일 결과를 증명하는 공식 문서입니다.
                            특정 영역의 성과를 빠르게 입증하거나 부분적 개선을 확인할 때 활용합니다.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold">특징</h5>
                                <ul class="mb-0">
                                    <li>단일 테스트 결과 즉시 발급</li>
                                    <li>PDF 다운로드 + QR 코드 검증</li>
                                    <li>이메일 자동 발송</li>
                                    <li>테스트 환경 정보 상세 기록</li>
                                    <li>DevTeam 공식 서명 포함</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold">활용 사례</h5>
                                <ul class="mb-0">
                                    <li>클라이언트 납품 증빙 자료</li>
                                    <li>정부과제/제안서 기술력 증명</li>
                                    <li>개발팀 내부 품질 관리</li>
                                    <li>경쟁사 대비 우위 입증</li>
                                    <li>웹사이트 개선 전후 비교</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PSQC 종합 인증서 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">PSQC 종합 인증서 (PSQC Master Certificate)</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Performance · Security · Quality · Content 4개 영역, 총 16개 세부 테스트를 모두 완료한 후
                            가중치 기반 점수 합산을 통해 발급되는 종합 인증서입니다.
                        </p>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold">
                                    등급 체계
                                    <small class="text-muted ms-2">(구글 1페이지 500개 샘플 기준)</small>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>등급</th>
                                                <th>점수</th>
                                                <th>분포(추정)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>A+</strong></td>
                                                <td>900 – 1000</td>
                                                <td>상위 ~2%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>A</strong></td>
                                                <td>800 – 899</td>
                                                <td>상위 ~8%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>B</strong></td>
                                                <td>700 – 799</td>
                                                <td>상위 ~15%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>C</strong></td>
                                                <td>600 – 699</td>
                                                <td>상위 ~25%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>D</strong></td>
                                                <td>500 – 599</td>
                                                <td>상위 ~40%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>F</strong></td>
                                                <td>&lt; 500</td>
                                                <td>나머지 (100%)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold">PSQC 인증서의 가치</h5>
                                <ul class="mb-0">
                                    <li><strong>객관적 데이터 제공:</strong> 각 테스트별 Raw 데이터와 측정 환경 정보 상세 기록</li>
                                    <li><strong>QR 검증 시스템:</strong> 인증서 진위 여부와 원본 데이터 실시간 조회 가능</li>
                                    <li><strong>투명한 평가 기준:</strong> 16개 테스트 방식과 점수 산정 과정 공개</li>
                                    <li><strong>비즈니스 신뢰도 향상:</strong> 웹 품질에 대한 제3자 객관적 평가 증빙</li>
                                    <li><strong>마케팅 차별화:</strong> 경쟁사 대비 정량적 우위 입증 자료</li>
                                    <li><strong>프로젝트 납품 지원:</strong> 클라이언트 요구사항 충족 증명</li>
                                </ul>

                                <div class="alert alert-info mt-3 mb-0" role="alert">
                                    <small class="text-muted">
                                        <strong>참고:</strong> DevTeam-Test는 웹사이트의 절대적 보안이나 완벽성을 보장하지 않습니다.
                                        테스트 시점의 측정 데이터와 분석 결과를 제공하여, 웹사이트 품질 개선과
                                        비즈니스 홍보에 활용할 수 있는 객관적 근거를 제공하는 것이 목적입니다.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 16개 개별 테스트 상세 점수 체계 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">16개 개별 테스트 상세 점수 체계</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Performance 300점 -->
                            <div class="col-12">
                                <h4 class="fw-bold text-warning mb-3">Performance (300점)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">테스트</th>
                                                <th class="test-method">시험 방법</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- 글로벌 속도 -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/speed">글로벌 속도</a>
                                                </td>
                                                <td class="test-method-content">
                                                    8지역 신규/재방문<br>
                                                    TTFB &amp; Load<br>
                                                    성능 확인
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>200ms</strong>, Load &le;
                                                            <strong>1.5s</strong><br>
                                                            • 글로벌 평균: TTFB &le; <strong>800ms</strong>, Load &le;
                                                            <strong>2.5s</strong><br>
                                                            • 모든 지역: TTFB &le; <strong>1.5s</strong>, Load &le;
                                                            <strong>3s</strong><br>
                                                            • 재방문 성능향상: <strong>80%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>400ms</strong>, Load &le;
                                                            <strong>2.5s</strong><br>
                                                            • 글로벌 평균: TTFB &le; <strong>1.2s</strong>, Load &le;
                                                            <strong>3.5s</strong><br>
                                                            • 모든 지역: TTFB &le; <strong>2s</strong>, Load &le;
                                                            <strong>4s</strong><br>
                                                            • 재방문 성능향상: <strong>60%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>800ms</strong>, Load &le;
                                                            <strong>3.5s</strong><br>
                                                            • 글로벌 평균: TTFB &le; <strong>1.6s</strong>, Load &le;
                                                            <strong>4.5s</strong><br>
                                                            • 모든 지역: TTFB &le; <strong>2.5s</strong>, Load &le;
                                                            <strong>5.5s</strong><br>
                                                            • 재방문 성능향상: <strong>50%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>1.2s</strong>, Load &le;
                                                            <strong>4.5s</strong><br>
                                                            • 글로벌 평균: TTFB &le; <strong>2.0s</strong>, Load &le;
                                                            <strong>5.5s</strong><br>
                                                            • 모든 지역: TTFB &le; <strong>3.0s</strong>, Load &le;
                                                            <strong>6.5s</strong><br>
                                                            • 재방문 성능향상: <strong>37.5%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Origin: TTFB &le; <strong>1.6s</strong>, Load &le;
                                                            <strong>6.0s</strong><br>
                                                            • 글로벌 평균: TTFB &le; <strong>2.5s</strong>, Load &le;
                                                            <strong>7.0s</strong><br>
                                                            • 모든 지역: TTFB &le; <strong>3.5s</strong>, Load &le;
                                                            <strong>8.5s</strong><br>
                                                            • 재방문 성능향상: <strong>25%+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 위 기준에 미달
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- 부하 테스트 (K6 Load Test, 서울 리전) -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/load">부하 테스트</a>
                                                </td>
                                                <td class="test-method-content">
                                                    서울 리전<br>
                                                    K6 부하테스트<br>
                                                    P95 응답시간<br>
                                                    안정성 확인
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>기본 조건:</strong><br>
                                                            • <strong>100 VUs</strong> + <strong>60초</strong><br>
                                                            • Think Time: <strong>3–10초</strong><br><br>
                                                            <strong>성능 기준:</strong><br>
                                                            • P95 응답시간: &lt; <strong>1000ms</strong><br>
                                                            • 에러율: &lt; <strong>0.1%</strong><br>
                                                            • 안정성: P90 ≤ <strong>평균값의 200%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>기본 조건:</strong><br>
                                                            • <strong>100 VUs</strong> + <strong>60초</strong><br>
                                                            • Think Time: <strong>3–10초</strong><br><br>
                                                            <strong>성능 기준:</strong><br>
                                                            • P95 응답시간: &lt; <strong>1200ms</strong><br>
                                                            • 에러율: &lt; <strong>0.5%</strong><br>
                                                            • 안정성: P90 ≤ <strong>평균값의 240%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>기본 조건:</strong><br>
                                                            • <strong>50+ VUs</strong> + <strong>45+ 초</strong><br>
                                                            • Think Time: <strong>3–10초</strong><br><br>
                                                            <strong>성능 기준:</strong><br>
                                                            • P95 응답시간: &lt; <strong>1500ms</strong><br>
                                                            • 에러율: &lt; <strong>1.0%</strong><br>
                                                            • 안정성: P90 ≤ <strong>평균값의 280%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>기본 조건:</strong><br>
                                                            • <strong>30+ VUs</strong> + <strong>30+ 초</strong><br>
                                                            • Think Time: <strong>3–10초</strong><br><br>
                                                            <strong>성능 기준:</strong><br>
                                                            • P95 응답시간: &lt; <strong>2000ms</strong><br>
                                                            • 에러율: &lt; <strong>2.0%</strong><br>
                                                            • 안정성: P90 ≤ <strong>평균값의 320%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            <strong>기본 조건:</strong><br>
                                                            • <strong>10+ VUs</strong> + <strong>15+ 초</strong><br>
                                                            • Think Time: <strong>3–10초</strong><br><br>
                                                            <strong>성능 기준:</strong><br>
                                                            • P95 응답시간: &lt; <strong>3000ms</strong><br>
                                                            • 에러율: &lt; <strong>5.0%</strong><br>
                                                            • 안정성: P90 ≤ <strong>평균값의 400%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 위 기준에 미달
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- 모바일 성능 -->
                                            <tr>
                                                <td>
                                                    <a href="/performance/mobile">모바일 성능</a>
                                                </td>
                                                <td class="test-method-content">
                                                    iPhone/Galaxy<br>
                                                    (Playwright)<br>
                                                    Median 응답시간(재방문)<br>
                                                    JS 에러 · 렌더 폭 초과
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median 응답시간: <strong>≤ 800ms</strong><br>
                                                            • JS 런타임 에러: <strong>0</strong><br>
                                                            • 렌더 폭 초과: <strong>없음</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median 응답시간: <strong>≤ 1200ms</strong><br>
                                                            • JS 런타임 에러: <strong>≤ 1</strong><br>
                                                            • 렌더 폭 초과: <strong>없음</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median 응답시간: <strong>≤ 2000ms</strong><br>
                                                            • JS 런타임 에러: <strong>≤ 2</strong><br>
                                                            • 렌더 폭 초과: <strong>허용</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median 응답시간: <strong>≤ 3000ms</strong><br>
                                                            • JS 런타임 에러: <strong>≤ 3</strong><br>
                                                            • 렌더 폭 초과: <strong>빈번</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Median 응답시간: <strong>≤ 4000ms</strong><br>
                                                            • JS 런타임 에러: <strong>≤ 5</strong><br>
                                                            • 렌더 폭 초과: <strong>심각</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 위 기준에 미달
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Security 300점 -->
                            <div class="col-12">
                                <h4 class="fw-bold text-danger mb-3">Security (300점)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">테스트</th>
                                                <th class="test-method">시험 방법</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- SSL 기본 testssl.sh -->
                                            <tr>
                                                <td>
                                                    <a href="/security/ssl">SSL 기본</a>
                                                </td>
                                                <td class="test-method-content">
                                                    testssl.sh 결과<br>
                                                    프로토콜·암호·인증서<br>
                                                    취약점 종합
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>최신 TLS만</strong> 사용, <strong>취약점 없음</strong><br>
                                                            • <strong>강력한 암호화 스위트</strong> 적용<br>
                                                            • 인증서 및 체인 <strong>완전 정상</strong><br>
                                                            • <strong>HSTS</strong> 등 보안 설정 <strong>우수</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.2/1.3</strong> 지원, 구버전 차단<br>
                                                            • <strong>주요 취약점 없음</strong><br>
                                                            • 일부 약한 암호나 설정 미흡 가능<br>
                                                            • 전반적으로 <strong>안전한 수준</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>안전한 프로토콜</strong> 위주<br>
                                                            • 약한 암호 스위트 <strong>일부 존재</strong><br>
                                                            • testssl.sh 경고(<strong>WEAK</strong>) 다수<br>
                                                            • <strong>개선 필요</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 구버전 TLS <strong>일부 활성</strong><br>
                                                            • <strong>취약 암호화</strong> 사용률 높음<br>
                                                            • 인증서 <strong>만료 임박</strong>/단순 DV<br>
                                                            • 취약점 <strong>소수 발견</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>SSLv3/TLS 1.0</strong> 허용<br>
                                                            • <strong>취약 암호 다수</strong> 활성<br>
                                                            • 인증서 체인 <strong>오류/만료 임박</strong><br>
                                                            • <strong>다수 취약점</strong> 존재
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • SSL/TLS 설정 <strong>근본적 결함</strong><br>
                                                            • <strong>취약 프로토콜</strong> 전면 허용<br>
                                                            • 인증서 <strong>만료/자가서명</strong><br>
                                                            • testssl.sh <strong>FAIL/VULNERABLE</strong> 다수
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- SSL 심화 sslyze -->
                                            <tr>
                                                <td>
                                                    <a href="/security/sslyze">SSL 심화</a>
                                                </td>
                                                <td class="test-method-content">
                                                    SSLyze 심층 진단<br>
                                                    프로토콜·암호·인증서<br>
                                                    OCSP·ALPN
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.3/1.2만</strong> 허용, 약한 암호군 없음(<strong>전부
                                                                PFS</strong>)<br>
                                                            • 인증서 <strong>ECDSA</strong> 또는 <strong>RSA≥3072</strong>,
                                                            체인 완전·만료 <strong>60일↑</strong><br>
                                                            • <strong>OCSP Stapling</strong> 정상(가능시
                                                            <strong>Must-Staple</strong>)<br>
                                                            • ALPN <strong>h2</strong> 협상, 압축/취약 재협상
                                                            <strong>비활성</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.3/1.2</strong>, 강한 암호 우선(<strong>PFS
                                                                대부분</strong>)<br>
                                                            • 인증서 <strong>RSA≥2048</strong>, <strong>SHA-256+</strong>,
                                                            체인 정상·만료 <strong>30일↑</strong><br>
                                                            • <strong>OCSP Stapling</strong> 활성(간헐 실패 허용)<br>
                                                            • <strong>h2</strong> 지원 또는 ALPN 적정, 위험 기능
                                                            <strong>비활성</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.2</strong> 필수, 1.3 선택/미지원, 일부
                                                            <strong>CBC</strong> 존재<br>
                                                            • 인증서 <strong>RSA≥2048</strong>, 체인 정상(만료
                                                            <strong>14일↑</strong>)<br>
                                                            • OCSP Stapling <strong>미활성</strong>(대신 OCSP 응답 가능)<br>
                                                            • h2 미지원 가능, 위험 기능은 <strong>대체로 비활성</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>TLS 1.0/1.1</strong> 활성 또는 <strong>약한 암호
                                                                다수</strong>(PFS 낮음)<br>
                                                            • 체인 누락/<strong>약한 서명(SHA-1)</strong> 또는 만료
                                                            임박(<strong>≤14일</strong>)<br>
                                                            • Stapling <strong>없음</strong>·폐기 확인
                                                            <strong>불명확</strong><br>
                                                            • h2 <strong>미지원</strong>, 일부 위험 기능 <strong>활성</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 구식 프로토콜/암호(<strong>SSLv3/EXPORT/RC4</strong> 등) 허용<br>
                                                            • 인증서 <strong>불일치/체인 오류</strong> 빈발<br>
                                                            • Stapling <strong>실패</strong>·폐기 확인 <strong>불능</strong><br>
                                                            • <strong>압축/취약 재협상</strong> 활성
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>핸드셰이크 실패</strong> 수준의 결함<br>
                                                            • <strong>만료/자가서명/호스트 불일치</strong><br>
                                                            • 광범위한 <strong>약한 프로토콜·암호</strong> 허용<br>
                                                            • 전반적 <strong>TLS 설정 붕괴</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- 보안 헤더 -->
                                            <tr>
                                                <td>
                                                    <a href="/security/headers">보안 헤더</a>
                                                </td>
                                                <td class="test-method-content">헤더 완성도</td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>CSP 강함</strong>(nonce/hash/strict-dynamic,
                                                            unsafe-* 미사용)<br>
                                                            • XFO: <strong>DENY/SAMEORIGIN</strong> 또는 frame-ancestors
                                                            제한<br>
                                                            • X-Content-Type: <strong>nosniff</strong><br>
                                                            • Referrer-Policy:
                                                            <strong>strict-origin-when-cross-origin</strong> 이상<br>
                                                            • Permissions-Policy: <strong>불필요 기능 차단</strong><br>
                                                            • HSTS: <strong>6개월↑ + 서브도메인</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A (CSP 없어도 가능: 비-CSP 5항목 우수) -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>CSP 존재</strong>(약함 허용) <em>또는</em> <strong>비-CSP
                                                                5항목 우수</strong><br>
                                                            • <strong>XFO 적용</strong>(또는 frame-ancestors 제한)<br>
                                                            • X-Content-Type: <strong>nosniff</strong><br>
                                                            • Referrer-Policy: <strong>권장 값</strong> 사용<br>
                                                            • Permissions-Policy: <strong>기본 제한</strong> 적용<br>
                                                            • HSTS: <strong>6개월↑</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • CSP <strong>없음/약함</strong><br>
                                                            • XFO <strong>정상 적용</strong><br>
                                                            • X-Content-Type: <strong>있음</strong><br>
                                                            • Referrer-Policy: <strong>양호/보통</strong><br>
                                                            • Permissions-Policy: <strong>일부 제한</strong><br>
                                                            • HSTS: <strong>단기</strong> 또는 <strong>서브도메인 미포함</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 헤더 <strong>일부만 존재</strong><br>
                                                            • CSP <strong>없음/약함</strong><br>
                                                            • Referrer-Policy <strong>약함</strong><br>
                                                            • X-Content-Type <strong>누락</strong><br>
                                                            • HSTS <strong>없음</strong> 또는 <strong>매우 짧음</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 핵심 헤더 <strong>1~2개만</strong><br>
                                                            • CSP <strong>없음</strong><br>
                                                            • Referrer <strong>약함/없음</strong><br>
                                                            • 기타 헤더 <strong>다수 누락</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 보안 헤더 <strong>전무에 가까움</strong><br>
                                                            • <strong>CSP/XFO/X-Content 없음</strong><br>
                                                            • Referrer-Policy <strong>없음</strong><br>
                                                            • HSTS <strong>없음</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <a href="/security/scan">패시브 보안 스캔</a>

                                                </td>

                                                <td class="test-method-content">
                                                    패시브 응답 분석<br>
                                                    HTTP 헤더/바디 검사<br>
                                                    (CSP 경고 제외)<br>
                                                    <div class="small text-muted mt-1">
                                                        OWASP ZAP Passive Scan<br>
                                                        메인 페이지 1건<br>
                                                        하위 탐색 없음
                                                    </div>
                                                </td>

                                                {{-- A+ --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High/Medium <strong>0개</strong><br>
                                                            • 보안 헤더 <strong>완비</strong>(HTTPS, HSTS, X-Frame-Options
                                                            등)<br>
                                                            • 민감정보 노출 <strong>없음</strong>(쿠키, 주석, 디버그)<br>
                                                            • 서버/프레임워크 버전 정보 <strong>최소화</strong><br>
                                                            • CSP 관련 점검은 별도 항목에서 수행
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- A --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>0</strong>, Medium <strong>≤1</strong><br>
                                                            • 보안 헤더 <strong>대부분 충족</strong>, 일부 누락 있음<br>
                                                            • 민감정보 노출 <strong>없음</strong><br>
                                                            • <strong>경미한 정보 노출</strong>(예: 서버 타입) 존재
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- B --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤1</strong>, Medium <strong>≤2</strong><br>
                                                            • 일부 보안 헤더 <strong>미구현</strong>(HSTS, X-XSS-Protection
                                                            등)<br>
                                                            • 세션 쿠키 <strong>Secure/HttpOnly 누락</strong><br>
                                                            • 주석/메타 정보에 <strong>경미한 내부 식별자</strong> 노출
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- C --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≥2</strong> 또는 Medium <strong>≥3</strong><br>
                                                            • 주요 보안 헤더 <strong>부재</strong><br>
                                                            • 민감 파라미터/토큰이 응답 내 <strong>직접 노출</strong><br>
                                                            • 세션 관리 <strong>취약</strong>(쿠키 속성 전반 미흡)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- D --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>다수의 High</strong> 존재<br>
                                                            • 인증/세션 관련 <strong>심각한 속성 누락</strong><br>
                                                            • 디버그/개발용 정보 노출(<strong>스택 트레이스, 내부 IP</strong>)<br>
                                                            • <strong>공개 관리 콘솔/설정 파일</strong> 노출
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- F --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>광범위한 High 취약점</strong><br>
                                                            • <strong>HTTPS 미적용</strong> 또는 전면 무력화<br>
                                                            • 민감 데이터 <strong>평문 전송/노출</strong><br>
                                                            • 전반적 보안 헤더·세션 통제 <strong>부재</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <a href="/security/nuclei">최신 취약점</a>
                                                </td>

                                                <td class="test-method-content">
                                                    최신성 기반<br>
                                                    Nuclei 템플릿<br>
                                                    <div class="small text-muted mt-1">
                                                        2024–2025<br>
                                                        (비침투, 단일 URL)
                                                    </div>
                                                </td>

                                                {{-- A+ --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Critical/High <strong>0개</strong>, Medium
                                                            <strong>0개</strong><br>
                                                            • <strong>2024–2025 CVE</strong> 미검출<br>
                                                            • 공개 디렉터리/디버그/민감파일 노출 <strong>無</strong><br>
                                                            • 보안 헤더/배너 노출 <strong>양호</strong>(정보 최소화)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- A --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤1</strong>, Medium <strong>≤1</strong><br>
                                                            • 최근 CVE <strong>직접 노출 없음</strong>(우회/조건 필요)<br>
                                                            • <strong>경미한 설정 경고</strong>(정보성) 수준<br>
                                                            • 패치/구성 관리 <strong>양호</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- B --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≤2</strong> 또는 Medium <strong>≤3</strong><br>
                                                            • 일부 <strong>구성 노출/배너 노출</strong> 존재<br>
                                                            • 보호된 관리 엔드포인트 존재(<strong>우회 어려움</strong>)<br>
                                                            • 패치 지연 경향(<strong>최근 보안 릴리즈</strong> 반영 지연)
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- C --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • High <strong>≥3</strong> 또는 Medium <strong>다수</strong><br>
                                                            • <strong>민감 파일/백업/인덱싱</strong> 노출 발견<br>
                                                            • <strong>구버전 컴포넌트</strong> 추정 가능(배너/메타 정보)<br>
                                                            • 패치/구성 관리 <strong>체계적 개선 필요</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- D --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Critical <strong>≥1</strong> 또는 악용 난이도 낮은
                                                            <strong>High</strong><br>
                                                            • 최근(<strong>2024–2025</strong>) CVE <strong>직접 영향</strong>
                                                            추정<br>
                                                            • 인증 없이 접근 가능한 <strong>위험 엔드포인트/파일</strong><br>
                                                            • <strong>빌드/로그/환경</strong> 등 민감 정보 노출
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- F --}}
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>다수의 Critical/High</strong> 동시 존재<br>
                                                            • 최신 CVE <strong>대량 미패치/광범위 노출</strong><br>
                                                            • 기본 보안 구성 <strong>결여</strong>(방어 헤더/접근통제 부족)<br>
                                                            • 전면적 보안 가드레일 <strong>부재</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Quality 250점 -->
                            <div class="col-12">
                                <h4 class="fw-bold text-success mb-3">Quality (250점)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">테스트</th>
                                                <th class="test-method">시험 방법</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="/quality/lighthouse">종합 품질</a>
                                                </td>
                                                <td class="test-method-content">
                                                    성능+SEO+접근성 통합 분석<br>
                                                    (LightHouse)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>90점+</strong><br>
                                                            • Accessibility: <strong>90점+</strong><br>
                                                            • Best Practices: <strong>90점+</strong><br>
                                                            • SEO: <strong>90점+</strong><br>
                                                            • 전체 평균: <strong>95점+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>85점+</strong><br>
                                                            • Accessibility: <strong>85점+</strong><br>
                                                            • Best Practices: <strong>85점+</strong><br>
                                                            • SEO: <strong>85점+</strong><br>
                                                            • 전체 평균: <strong>90점+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>75점+</strong><br>
                                                            • Accessibility: <strong>75점+</strong><br>
                                                            • Best Practices: <strong>75점+</strong><br>
                                                            • SEO: <strong>75점+</strong><br>
                                                            • 전체 평균: <strong>80점+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>65점+</strong><br>
                                                            • Accessibility: <strong>65점+</strong><br>
                                                            • Best Practices: <strong>65점+</strong><br>
                                                            • SEO: <strong>65점+</strong><br>
                                                            • 전체 평균: <strong>70점+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>55점+</strong><br>
                                                            • Accessibility: <strong>55점+</strong><br>
                                                            • Best Practices: <strong>55점+</strong><br>
                                                            • SEO: <strong>55점+</strong><br>
                                                            • 전체 평균: <strong>60점+</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Performance: <strong>54점 이하</strong><br>
                                                            • Accessibility: <strong>54점 이하</strong><br>
                                                            • Best Practices: <strong>54점 이하</strong><br>
                                                            • SEO: <strong>54점 이하</strong><br>
                                                            • 전체 평균: <strong>59점 이하</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/accessibility">접근성 심화</a>
                                                </td>
                                                <td class="test-method-content">
                                                    WCAG 2.1 규칙 기반<br>
                                                    자동 접근성 검사<br>
                                                    결과(오류·경고 개수)로 검사<br>
                                                    (axe-core 기준)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical=<strong>0</strong>,
                                                            serious=<strong>0</strong><br>
                                                            • 전체 위반 <strong>≤ 3건</strong><br>
                                                            • 키보드/ARIA/대체텍스트/대비 <strong>모두 양호</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical=<strong>0</strong>, serious <strong>≤
                                                                3</strong><br>
                                                            • 전체 위반 <strong>≤ 8건</strong><br>
                                                            • 주요 Landmark/Label <strong>대체로 양호</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 1</strong>, serious <strong>≤
                                                                6</strong><br>
                                                            • 전체 위반 <strong>≤ 15건</strong><br>
                                                            • 일부 contrast/label <strong>개선 필요</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 3</strong>, serious <strong>≤
                                                                10</strong><br>
                                                            • 전체 위반 <strong>≤ 25건</strong><br>
                                                            • 포커스/ARIA 구조 <strong>보완 필요</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • critical <strong>≤ 6</strong> 또는 serious <strong>≤
                                                                18</strong><br>
                                                            • 전체 위반 <strong>≤ 40건</strong><br>
                                                            • 키보드 트랩/레이블 누락 <strong>다수</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 위 기준 초과(<strong>critical/serious 다수</strong>)<br>
                                                            • 스크린리더/키보드 이용 <strong>곤란 수준</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/compatibility">브라우저 호환</a>
                                                </td>
                                                <td class="test-method-content">
                                                    Chrome / Firefox / Safari<br>
                                                    JS·CSS 오류 기반<br>
                                                    (Playwright)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Chrome / Firefox / Safari <strong>모두 정상</strong><br>
                                                            • JS 오류: <strong>0개</strong><br>
                                                            • CSS 렌더링 오류: <strong>0개</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 주요 브라우저 지원 <strong>양호</strong><br>
                                                            • JS 오류 <strong>≤ 1</strong><br>
                                                            • CSS 오류 <strong>≤ 1</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 브라우저별 <strong>경미한 차이</strong> 존재<br>
                                                            • JS 오류 <strong>≤ 3</strong><br>
                                                            • CSS 오류 <strong>≤ 3</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 일부 브라우저에서 <strong>기능 저하</strong><br>
                                                            • JS 오류 <strong>≤ 6</strong><br>
                                                            • CSS 오류 <strong>≤ 6</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 호환성 문제 <strong>다수</strong><br>
                                                            • JS 오류 <strong>≤ 10</strong><br>
                                                            • CSS 오류 <strong>≤ 10</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 주요 브라우저 <strong>정상 동작 불가</strong><br>
                                                            • JS 오류 <strong>10개 초과</strong><br>
                                                            • CSS 오류 <strong>10개 초과</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/quality/visual">반응형 UI</a>
                                                </td>

                                                <!-- 시험방법: 단일 페이지 / 간단 설명 -->
                                                <td class="test-method-content">
                                                    주요 뷰포트 별<br>
                                                    폭 초과 픽셀(px) 측정<br>
                                                    (모바일·폴더블·태블릿·데스크톱)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전 뷰포트 <strong>초과 0건</strong><br>
                                                            • body 렌더 폭이 항상 <strong>viewport 이내</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 초과 ≤ <strong>1건</strong>이며 <strong>≤ 8px</strong><br>
                                                            • 모바일 협폭(≤390px) 구간에서는 <strong>초과 0건</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 초과 ≤ <strong>2건</strong>이고 각 <strong>≤ 16px</strong><br>
                                                            또는 모바일 협폭에서 <strong>≤ 8px 1건</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 초과 ≤ <strong>4건</strong> 또는 단일 초과가
                                                            <strong>17–32px</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 초과 &gt; <strong>4건</strong> 또는 단일 초과가
                                                            <strong>33–64px</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 측정 실패 또는 <strong>≥ 65px</strong> 초과 발생
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Content 150점 -->
                            <div class="col-12">
                                <h4 class="fw-bold mb-3" style="color: #6f42c1;">Content (150점)</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="test-title">테스트</th>
                                                <th class="test-method">시험 방법</th>
                                                <th class="grade-a-plus">A+</th>
                                                <th class="grade-a">A</th>
                                                <th class="grade-b">B</th>
                                                <th>C</th>
                                                <th>D</th>
                                                <th>F</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="/content/links">링크 검증</a>
                                                </td>
                                                <td class="test-method-content">
                                                    내부/외부/이미지<br>
                                                    앵커 링크 상태 검사<br>
                                                    오류율로 등급 산정<br>
                                                    (Broken Link Checker)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 내부/외부/이미지 링크 <strong>오류율: 0%</strong><br>
                                                            • 리다이렉트 체인 <strong>≤1단계</strong><br>
                                                            • 앵커 링크 <strong>100% 정상</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전체 <strong>오류율 ≤1%</strong><br>
                                                            • 리다이렉트 체인 ≤2단계<br>
                                                            • 앵커 링크 <strong>대부분 정상</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전체 <strong>오류율 ≤3%</strong><br>
                                                            • 리다이렉트 체인 ≤3단계<br>
                                                            • 일부 앵커 링크 불량
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전체 <strong>오류율 ≤5%</strong><br>
                                                            • 다수 링크 <strong>경고</strong> (타임아웃/SSL 문제)<br>
                                                            • 앵커 링크 오류 빈번
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전체 <strong>오류율 ≤10%</strong><br>
                                                            • <strong>리다이렉트 루프</strong> 또는 긴 체인<br>
                                                            • 이미지 링크 <strong>다수 깨짐</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 전체 <strong>오류율 10% 이상</strong><br>
                                                            • 주요 내부 링크 <strong>다수 깨짐</strong><br>
                                                            • 앵커/이미지 <strong>전반 불량</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/structure">구조화 데이터</a>
                                                </td>
                                                <td class="test-method-content">
                                                    JSON-LD/Schema.org 기반<br>
                                                    구조화 데이터 오류·경고 여부<br>
                                                    (Google Rich Results Test)
                                                </td>
                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Schema.org 스키마 <strong>완벽 구현</strong><br>
                                                            • <strong>JSON-LD 형식</strong> 사용<br>
                                                            • Rich Snippets <strong>100% 인식</strong><br>
                                                            • <strong>오류 0개, 경고 없음</strong><br>
                                                            • 적절한 스키마 타입 적용
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 주요 스키마 <strong>정상</strong><br>
                                                            • JSON-LD 기반 구현<br>
                                                            • Rich Snippets <strong>대부분 인식</strong><br>
                                                            • <strong>오류 없음</strong>, 경고 ≤2개
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 핵심 스키마 <strong>일부 누락</strong><br>
                                                            • Rich Snippets 제한적 인식<br>
                                                            • 오류 ≤1개, <strong>경고 ≤5개</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 구조화 데이터 <strong>불완전</strong><br>
                                                            • Rich Snippets <strong>불안정</strong><br>
                                                            • 오류 ≤3개, <strong>경고 다수</strong><br>
                                                            • 일부 타입 부적절
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 구조화 데이터 <strong>불일치/중복</strong><br>
                                                            • Rich Snippets <strong>미인식</strong><br>
                                                            • <strong>오류 ≥4개</strong><br>
                                                            • 경고 다수 및 <strong>잘못된 타입</strong> 적용
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • 구조화 데이터 <strong>미구현</strong><br>
                                                            • <strong>JSON-LD/마이크로데이터 전무</strong><br>
                                                            • <strong>오류 전면적 발생</strong><br>
                                                            • 검색엔진 Rich Snippets <strong>불가</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/crawl">사이트 크롤링</a>
                                                </td>
                                                <td class="test-method-content">
                                                    robots/sitemap 검증<br>
                                                    + sitemap 기반 전체 크롤링<br>
                                                    (내부 품질·중복 분석)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt <strong>정상 적용</strong><br>
                                                            • sitemap.xml 존재 및 <strong>누락/404 없음</strong><br>
                                                            • 검사 대상 페이지 <strong>전부 2xx</strong><br>
                                                            • 전체 페이지 품질 평균 <strong>≥ 85점</strong><br>
                                                            • 중복 콘텐츠 <strong>≤ 30%</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt <strong>정상 적용</strong><br>
                                                            • sitemap.xml 존재 및 <strong>정합성 확보</strong><br>
                                                            • 검사 대상 페이지 <strong>전부 2xx</strong><br>
                                                            • 전체 페이지 품질 평균 <strong>≥ 85점</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt 및 sitemap.xml <strong>존재</strong><br>
                                                            • 검사 대상 페이지 <strong>전부 2xx</strong><br>
                                                            • 전체 페이지 품질 평균 무관
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt 및 sitemap.xml 존재<br>
                                                            • 검사 리스트 일부 <strong>4xx/5xx 오류</strong> 포함
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • robots.txt 및 sitemap.xml 존재<br>
                                                            • 검사 대상 URL 생성 가능(robots 허용 + sitemap 수집)<br>
                                                            • 단, <strong>정상 접근률 낮거나</strong> 품질 점검 불가
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • <strong>robots.txt 부재</strong> 또는 <strong>sitemap.xml
                                                                부재</strong><br>
                                                            • 검사 리스트 자체 <strong>생성 불가</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="/content/meta">메타데이터</a>
                                                </td>
                                                <td class="test-method-content">
                                                    완성도 기반<br>
                                                    (Meta Inspector CLI)
                                                </td>

                                                <!-- A+ -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title: <strong>최적 길이(50~60자)</strong><br>
                                                            • Description: <strong>최적 길이(120~160자)</strong><br>
                                                            • Open Graph <strong>완벽 구현</strong><br>
                                                            • Canonical <strong>정확</strong> + Twitter Cards
                                                            <strong>완벽</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- A -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>허용 범위</strong><br>
                                                            • Open Graph <strong>완벽 구현</strong><br>
                                                            • Canonical <strong>정확 설정</strong><br>
                                                            • Twitter Cards 선택사항
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- B -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>기본 작성</strong><br>
                                                            • Open Graph <strong>기본 태그</strong><br>
                                                            • Canonical 설정됨<br>
                                                            • 일부 메타데이터 누락 허용
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- C -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>길이 부적절</strong><br>
                                                            • Open Graph 불완전(<strong>주요 태그 누락</strong>)<br>
                                                            • Canonical <strong>부정확 또는 누락</strong><br>
                                                            • 메타데이터 품질 저하
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- D -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>심각한 길이 문제</strong><br>
                                                            • Open Graph <strong>기본 태그 부족</strong><br>
                                                            • Canonical <strong>잘못 설정</strong><br>
                                                            • <strong>기본 메타데이터 부족</strong>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- F -->
                                                <td>
                                                    <div class="criteria-box">
                                                        <div class="criteria-detail">
                                                            • Title/Description <strong>미작성</strong><br>
                                                            • Open Graph <strong>부재</strong><br>
                                                            • 메타데이터 <strong>전반 미구현</strong><br>
                                                            • <strong>SEO 기초 설정 없음</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- /card-body -->
                </div>
            </div>
        </div>

        <!-- PSQC 점수 공식 상세 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">PSQC 종합 점수 및 등급 기준</h3>
                    </div>
                    <div class="card-body">

                        <!-- 점수 체계 -->
                        <div class="score-formula">
                            <h5 class="fw-bold mb-2">PSQC 점수 계산 방식</h5>
                            <div class="alert alert-info d-block mb-3">
                                <h6>1단계: 개별 테스트 점수 (각 100점 만점)</h6>
                                <p class="mb-1">모든 개별 테스트는 동일한 100점 척도로 평가됩니다.</p>
                                <small>(예: SSL 기본 → 85점, 모바일 성능 → 92점, 링크 검증 → 78점)</small>
                            </div>

                            <div class="alert alert-info d-block mb-3">
                                <h6>2단계: 영역별 가중치 적용</h6>
                                <div>Performance = (속도×1.0 + 부하×1.0 + 모바일×1.0) = 300점 만점</div>
                                <div>Security = (SSL×0.8 + SSLyze×0.6 + 헤더×0.6 + 스캔×0.6 + Nuclei×0.4) = 300점 만점</div>
                                <div>Quality = (Lighthouse×1.2 + 접근성×0.7 + 호환성×0.3 + 반응형×0.3) = 250점 만점</div>
                                <div>Content = (링크×0.5 + 구조화×0.4 + 크롤링×0.4 + 메타×0.2) = 150점 만점</div>
                            </div>

                            <div class="alert alert-info d-block">
                                <h6>3단계: 최종 종합 점수</h6>
                                <div>총점 = Performance(300) + Security(300) + Quality(250) + Content(150) = 1000점 만점
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success d-block mt-4" role="alert">
                            <h5 class="fw-bold mb-2">🏆 최종 등급 산정</h5>
                            <div class="mt-0">
                                <span class="badge bg-green-lt text-green-lt-fg me-1">A+ (900–1000점)</span>
                                <span class="badge bg-lime-lt text-lime-lt-fg me-1">A (800–899점)</span>
                                <span class="badge bg-yellow-lt text-yellow-lt-fg me-1">B (700–799점)</span>
                                <span class="badge bg-orange-lt text-orange-lt-fg me-1">C (600–699점)</span>
                                <span class="badge bg-pink-lt text-pink-lt-fg me-1">D (500–599점)</span>
                                <span class="badge bg-red-lt text-red-lt-fg">F (500점 미만)</span>
                            </div>
                            <div class="mt-2">
                                <h6 class="fw-bold mb-1">⚡ A+ 조건</h6>
                                <small>
                                    - 각 영역별 90% 이상<br>
                                    - 총점 900점 이상<br>
                                    - 치명적 보안 취약점 0개<br>
                                    ※ 총점만 충족해도 조건 미달 시 A 등급으로 조정
                                </small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- 글로벌 웹 표준 참조 및 연계 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">글로벌 웹 표준 참조 및 연계</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            DevTeam-Test는 독립적인 웹사이트 품질 평가 서비스로, 업계에서 널리 인정받는 웹 표준 가이드라인을 참조하여 개발되었습니다.
                        </p>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">ISO/IEC 25010 참조</h5>
                                <p class="small text-muted mb-2">소프트웨어 품질 모델 국제 표준 참조</p>
                                <div class="standard-badge bg-blue-lt text-blue-lt-fg">기능 적합성</div>
                                <div class="standard-badge bg-azure-lt text-azure-lt-fg">성능 효율성</div>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">보안성</div>
                                <div class="standard-badge bg-green-lt text-green-lt-fg">호환성</div>
                                <p class="small mt-2">
                                    <strong>DevTeam 적용:</strong> ISO 25010의 품질 특성 관점을 참조하여 Performance, Security,
                                    Quality, Content 영역을 구성했습니다. (※ DevTeam-Test만의 독자적 평가 방식 적용)
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">WCAG 2.1 가이드라인 참조</h5>
                                <p class="small text-muted mb-2">W3C 웹 접근성 가이드라인 참조</p>
                                <div class="standard-badge bg-teal-lt text-teal-lt-fg">인식가능성</div>
                                <div class="standard-badge bg-cyan-lt text-cyan-lt-fg">운용가능성</div>
                                <div class="standard-badge bg-indigo-lt text-indigo-lt-fg">이해가능성</div>
                                <div class="standard-badge bg-purple-lt text-purple-lt-fg">견고성</div>
                                <p class="small mt-2">
                                    <strong>DevTeam 적용:</strong> WCAG 2.1 AA 수준의 접근성 원칙을 참조하여 접근성 심화 테스트를 구성했습니다.
                                    Axe-core 엔진을 활용한 자동 검사를 수행합니다.
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Core Web Vitals 지표 활용</h5>
                                <p class="small text-muted mb-2">구글 페이지 경험 지표 활용</p>
                                <div class="standard-badge bg-lime-lt text-lime-lt-fg">LCP &lt;2.5초</div>
                                <div class="standard-badge bg-yellow-lt text-yellow-lt-fg">INP &lt;200ms</div>
                                <div class="standard-badge bg-orange-lt text-orange-lt-fg">CLS &lt;0.1</div>
                                <p class="small mt-2">
                                    <strong>DevTeam 적용:</strong> 구글의 Core Web Vitals 지표를 성능 평가에 참고자료로 활용하며, 글로벌 리전 테스트를
                                    통해 실제 사용자 경험을 측정합니다.
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">OWASP 보안 기준 참조</h5>
                                <p class="small text-muted mb-2">웹 애플리케이션 보안 기준 참조</p>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">OWASP Top 10</div>
                                <div class="standard-badge bg-pink-lt text-pink-lt-fg">ZAP 동적 스캔</div>
                                <div class="standard-badge bg-red-lt text-red-lt-fg">CVE 데이터베이스</div>
                                <p class="small mt-2">
                                    <strong>DevTeam 적용:</strong> OWASP Top 10과 CVE 데이터베이스를 참조하여 보안 취약점 스캔 기준을 수립했습니다.
                                    OWASP ZAP과 Nuclei 엔진을 활용합니다.
                                </p>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <p class="mb-0">
                                DevTeam-Test는 국제 표준 기관에서 제시한 시험 방법과 평가 기준을 <strong>한국 웹 개발 환경과 특성에 맞게 최적화</strong>한 독립적인
                                평가 서비스입니다.
                                각 테스트별로 <strong>상세한 시험 방법론과 측정된 Raw Data를 완전 공개</strong>하여 결과의 투명성과 신뢰성을 보장합니다.
                                고객사는 제공된 데이터를 바탕으로 구체적이고 실행 가능한 웹사이트 개선 방안을 수립할 수 있습니다.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 상세 통계 및 벤치마크 -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">글로벌 웹사이트 품질 벤치마크 상세</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-nowrap">
                                <thead>
                                    <tr>
                                        <th>측정 지표</th>
                                        <th>우수 기준</th>
                                        <th>글로벌 달성률</th>
                                        <th>출처</th>
                                        <th>DevTeam 연계 테스트</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Lighthouse 모든 항목 90+</strong></td>
                                        <td>Performance, Accessibility,<br>Best Practices, SEO 모두 90+</td>
                                        <td class="text-danger fw-bold">&lt; 2%</td>
                                        <td>HTTP Archive (Lighthouse 분포 분석)</td>
                                        <td>Quality/lighthouse</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Core Web Vitals 통과</strong></td>
                                        <td>LCP &lt; 2.5초, INP/TBT &lt; 200ms, CLS &lt; 0.1</td>
                                        <td class="text-warning fw-bold">≈ 43-44%</td>
                                        <td>Chrome UX Report (CrUX 실측)</td>
                                        <td>Performance/speed + Quality/lighthouse</td>
                                    </tr>
                                    <tr>
                                        <td><strong>SSL Labs A+ 등급</strong></td>
                                        <td>TLS 1.3, HSTS, 완벽한 설정</td>
                                        <td class="text-warning fw-bold">≈ 46%</td>
                                        <td>SSL Labs</td>
                                        <td>Security/ssl + Security/sslyze</td>
                                    </tr>
                                    <tr>
                                        <td><strong>WCAG 2.1 AA 준수<br>(자동 검사 기준)</strong></td>
                                        <td>감지된 오류 0개</td>
                                        <td class="text-danger fw-bold">≈ 5%<br>(접근성 결함 감지율 94.8%)</td>
                                        <td>WebAIM Million 프로젝트</td>
                                        <td>Quality/accessibility</td>
                                    </tr>
                                    <tr>
                                        <td><strong>OWASP Top 10 취약점 없음</strong></td>
                                        <td>주요 취약점 0개</td>
                                        <td class="text-warning fw-bold">≈ 30-40%</td>
                                        <td>OWASP Top 10</td>
                                        <td>Security/scan + Security/nuclei</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Schema.org 구조화 데이터<br>완전 구현</strong></td>
                                        <td>모든 페이지에 구현</td>
                                        <td class="text-warning fw-bold">≈ 25-35%</td>
                                        <td>W3C 구조화 데이터 통계</td>
                                        <td>Content/structure</td>
                                    </tr>
                                    <tr>
                                        <td><strong>브라우저 호환성 완전</strong></td>
                                        <td>Chrome, Firefox, Safari 모두 정상</td>
                                        <td class="text-success fw-bold">≈ 60-70%</td>
                                        <td>MDN 호환성 데이터</td>
                                        <td>Quality/compatibility</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info d-block mt-4" role="alert">
                            <h5 class="fw-bold mb-2">통계적 교집합 분석</h5>
                            <p class="mb-2">7개 영역 모두 우수한 웹사이트는 사실상 0%에 가깝습니다.
                                따라서 현실적인 분석에서는 핵심 지표(품질·성능·보안·접근성) 4가지를 기준으로 합니다.</p>
                            <ul class="mb-0">
                                <li><strong>이론적 교집합 (4개 핵심 지표):</strong> 2% × 43% × 46% × 5% ≈ 0.2%</li>
                                <li><strong>실제 상관관계:</strong> 우수한 사이트는 여러 영역에서 동시 우수할 확률이 높음</li>
                                <li><strong>현실적 추정:</strong> <span class="fw-bold text-primary">A+ 종합 기준은 전 세계 상위 약 2%
                                        이내</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
