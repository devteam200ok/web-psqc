@section('title')
    <title>🔒 SSL Deep Analysis – Comprehensive TLS Security with SSLyze | Web-PSQC</title>
    <meta name="description"
        content="In‑depth SSL/TLS analysis with SSLyze: protocol compatibility, cipher strength, certificate validity, OCSP Stapling, HSTS, PFS, elliptic curves, and more — evaluated up to A+.">
    <meta name="keywords"
        content="SSL deep analysis, SSLyze, TLS protocols, cipher suites, certificate validation, OCSP Stapling, HSTS, Perfect Forward Secrecy, ECC, SSL security grade, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="SSL Deep Analysis – Comprehensive TLS Security with SSLyze" />
    <meta property="og:description"
        content="Analyze TLS protocols, cipher suites, certificates, OCSP Stapling, HSTS, ECC and more with SSLyze to assess SSL/TLS security up to A+." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC SSLyze Deep Security Analysis" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="SSL Deep Analysis – Comprehensive TLS Security with SSLyze | Web-PSQC" />
    <meta name="twitter:description"
        content="Use SSLyze to analyze TLS/SSL configuration: protocols, cipher suites, certificate state, HSTS, OCSP Stapling, ECC — with grades and guidance." />
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
    'name' => 'SSL Deep Analysis – SSLyze‑Based Comprehensive TLS Security',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Leverage SSLyze to analyze TLS protocols, cipher suites, certificate status, HSTS, OCSP Stapling, and ECC; assess SSL/TLS security up to A+. ',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔒 SSL 심화 분석" subtitle="SSLyze 종합 진단" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                    <h3>SSLyze를 활용한 SSL/TLS 심층 분석</h3>
                                    <div class="text-muted small mt-3">
                                        <strong>측정 도구:</strong> SSLyze v5.x - Mozilla, Qualys, IETF 등이 권장하는 오픈소스 SSL/TLS
                                        스캐너<br>
                                        <strong>테스트 목적:</strong> 웹사이트의 SSL/TLS 설정을 종합적으로 진단하여 보안 취약점을 식별하고 개선 방안을
                                        제시<br><br>

                                        <strong>검사 항목:</strong><br>
                                        • <strong>TLS 프로토콜 버전</strong> - SSL 2.0/3.0, TLS 1.0/1.1/1.2/1.3 지원 여부<br>
                                        • <strong>암호군(Cipher Suites)</strong> - 강도, PFS(Perfect Forward Secrecy) 지원, 약한
                                        암호 검출<br>
                                        • <strong>인증서 체인</strong> - 유효성, 만료일, 서명 알고리즘, 키 크기, 체인 완전성<br>
                                        • <strong>OCSP Stapling</strong> - 인증서 폐기 상태 실시간 확인 메커니즘<br>
                                        • <strong>HTTP 보안 헤더</strong> - HSTS(HTTP Strict Transport Security) 설정<br>
                                        • <strong>타원곡선 암호</strong> - 지원하는 타원곡선 목록 및 강도 평가<br><br>

                                        <strong>DevTeam Test</strong>는 SSLyze 엔진을 통해 대상 서버의 SSL/TLS 설정을 스캔하고,
                                        수집된 데이터를 기반으로 보안 등급을 산출합니다.<br>
                                        이 과정은 약 <strong>30초~3분</strong> 정도 소요됩니다.
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
                                                    <td><strong>TLS 1.3/1.2만</strong> 허용, 약한 암호군 없음(<strong>전부
                                                            PFS</strong>)<br>
                                                        인증서 <strong>ECDSA</strong> 또는 <strong>RSA≥3072</strong>, 체인
                                                        완전·만료 <strong>60일↑</strong><br>
                                                        <strong>OCSP Stapling</strong> 정상(가능시
                                                        <strong>Must-Staple</strong>)<br>
                                                        HSTS 활성, max-age ≥ 1년, includeSubDomains, preload
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>80~89</td>
                                                    <td><strong>TLS 1.3/1.2</strong>, 강한 암호 우선(<strong>PFS
                                                            대부분</strong>)<br>
                                                        인증서 <strong>RSA≥2048</strong>, <strong>SHA-256+</strong>, 체인
                                                        정상·만료 <strong>30일↑</strong><br>
                                                        <strong>OCSP Stapling</strong> 활성(간헐 실패 허용)<br>
                                                        HSTS 활성, max-age ≥ 6개월
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>65~79</td>
                                                    <td><strong>TLS 1.2</strong> 필수, 1.3 선택/미지원, 일부 <strong>CBC</strong>
                                                        존재<br>
                                                        인증서 <strong>RSA≥2048</strong>, 체인 정상(만료
                                                        <strong>14일↑</strong>)<br>
                                                        OCSP Stapling <strong>미활성</strong>(대신 OCSP 응답 가능)<br>
                                                        HSTS 설정 있으나 일부 미흡</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>50~64</td>
                                                    <td><strong>TLS 1.0/1.1</strong> 활성 또는 <strong>약한 암호 다수</strong>(PFS
                                                        낮음)<br>
                                                        체인 누락/<strong>약한 서명(SHA-1)</strong> 또는 만료
                                                        임박(<strong>≤14일</strong>)<br>
                                                        Stapling <strong>없음</strong>·폐기 확인 <strong>불명확</strong><br>
                                                        HSTS <strong>미설정</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>35~49</td>
                                                    <td>구식 프로토콜/암호(<strong>SSLv3/EXPORT/RC4</strong> 등) 허용<br>
                                                        인증서 <strong>불일치/체인 오류</strong> 빈발<br>
                                                        Stapling <strong>실패</strong>·폐기 확인 <strong>불능</strong><br>
                                                        보안 헤더 전반적 미흡</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~34</td>
                                                    <td><strong>핸드셰이크 실패</strong> 수준의 결함<br>
                                                        <strong>만료/자가서명/호스트 불일치</strong><br>
                                                        광범위한 <strong>약한 프로토콜·암호</strong> 허용<br>
                                                        전반적 <strong>TLS 설정 붕괴</strong>
                                                    </td>
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
                                            $analysis = $results['analysis'] ?? [];
                                            $issues = $results['issues'] ?? [];
                                            $recommendations = $results['recommendations'] ?? [];

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

                                        <!-- 발견된 이슈 -->
                                        @if (!empty($issues))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">발견된 보안 이슈 ({{ count($issues) }}개)</h5>
                                                    <div class="list-group">
                                                        @foreach ($issues as $issue)
                                                            <div class="list-group-item list-group-item-danger">
                                                                ⚠️ {{ $issue }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 개선 권장사항 -->
                                        @if (!empty($recommendations))
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">개선 권장사항</h5>
                                                    <div class="list-group">
                                                        @foreach ($recommendations as $recommendation)
                                                            <div class="list-group-item list-group-item-info">
                                                                💡 {{ $recommendation }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 상세 분석 결과 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">상세 분석 결과</h5>

                                                <!-- TLS 버전 분석 -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">TLS 프로토콜 버전</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>TLS 1.2:</strong>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_2'] ?? false)
                                                                    <span
                                                                        class="badge bg-green-lt text-green-lt-fg">지원</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-red-lt text-red-lt-fg">미지원</span>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>TLS 1.3:</strong>
                                                                @if ($analysis['tls_versions']['supported_versions']['tls_1_3'] ?? false)
                                                                    <span
                                                                        class="badge bg-green-lt text-green-lt-fg">지원</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-orange-lt text-orange-lt-fg">미지원</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if (!empty($analysis['tls_versions']['issues']))
                                                            <div class="mt-3">
                                                                <strong class="text-danger">발견된 이슈:</strong>
                                                                <ul class="mb-0">
                                                                    @foreach ($analysis['tls_versions']['issues'] as $issue)
                                                                        <li>{{ $issue }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- 암호군 분석 -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">암호군(Cipher Suites) 분석</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['cipher_suites']['tls_1_2']))
                                                            <div class="mb-3">
                                                                <strong>TLS 1.2 암호군:</strong>
                                                                <div class="row mt-2">
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">전체</div>
                                                                        <div class="h4">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['total'] }}개
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">강한 암호</div>
                                                                        <div class="h4 text-success">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['strong'] }}개
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">약한 암호</div>
                                                                        <div class="h4 text-danger">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['weak'] }}개
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="text-muted">PFS 비율</div>
                                                                        <div class="h4">
                                                                            {{ $analysis['cipher_suites']['tls_1_2']['pfs_ratio'] }}%
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (!empty($analysis['cipher_suites']['tls_1_3']))
                                                            <div>
                                                                <strong>TLS 1.3 암호군:</strong>
                                                                {{ $analysis['cipher_suites']['tls_1_3']['total'] }}개
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- 인증서 분석 -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">인증서 분석</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['certificate']['details']))
                                                            <div class="row">
                                                                @if (isset($analysis['certificate']['details']['key_algorithm']))
                                                                    <div class="col-md-6">
                                                                        <strong>공개키 알고리즘:</strong>
                                                                        {{ $analysis['certificate']['details']['key_algorithm'] }}
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['key_size']))
                                                                    <div class="col-md-6">
                                                                        <strong>키 크기:</strong>
                                                                        {{ $analysis['certificate']['details']['key_size'] }}비트
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['signature_algorithm']))
                                                                    <div class="col-md-6">
                                                                        <strong>서명 알고리즘:</strong>
                                                                        {{ $analysis['certificate']['details']['signature_algorithm'] }}
                                                                    </div>
                                                                @endif
                                                                @if (isset($analysis['certificate']['details']['days_to_expiry']))
                                                                    <div class="col-md-6">
                                                                        <strong>만료까지:</strong>
                                                                        @if ($analysis['certificate']['details']['days_to_expiry'] <= 14)
                                                                            <span
                                                                                class="text-danger">{{ $analysis['certificate']['details']['days_to_expiry'] }}일</span>
                                                                        @elseif ($analysis['certificate']['details']['days_to_expiry'] <= 30)
                                                                            <span
                                                                                class="text-warning">{{ $analysis['certificate']['details']['days_to_expiry'] }}일</span>
                                                                        @else
                                                                            <span
                                                                                class="text-success">{{ $analysis['certificate']['details']['days_to_expiry'] }}일</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- OCSP Stapling -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">OCSP Stapling</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <strong>상태:</strong>
                                                        @if (($analysis['ocsp']['status'] ?? '') === 'SUCCESSFUL')
                                                            <span class="badge bg-green-lt text-green-lt-fg">활성</span>
                                                        @else
                                                            <span class="badge bg-red-lt text-red-lt-fg">비활성</span>
                                                        @endif

                                                        @if (isset($analysis['ocsp']['certificate_status']))
                                                            <div class="mt-2">
                                                                <strong>인증서 상태:</strong>
                                                                {{ $analysis['ocsp']['certificate_status'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- HTTP 보안 헤더 -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">HTTP 보안 헤더</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['http_headers']['hsts']))
                                                            <strong>HSTS:</strong> <span
                                                                class="badge bg-green-lt text-green-lt-fg">설정됨</span>
                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">max-age</div>
                                                                    <div>
                                                                        {{ number_format($analysis['http_headers']['hsts']['max_age']) }}초
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">includeSubDomains</div>
                                                                    <div>
                                                                        @if ($analysis['http_headers']['hsts']['include_subdomains'] ?? false)
                                                                            <span
                                                                                class="badge bg-green-lt text-green-lt-fg">Yes</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-orange-lt text-orange-lt-fg">No</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="text-muted">preload</div>
                                                                    <div>
                                                                        @if ($analysis['http_headers']['hsts']['preload'] ?? false)
                                                                            <span
                                                                                class="badge bg-green-lt text-green-lt-fg">Yes</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-orange-lt text-orange-lt-fg">No</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <strong>HSTS:</strong> <span
                                                                class="badge bg-red-lt text-red-lt-fg">미설정</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- 타원곡선 -->
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">타원곡선 암호</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (!empty($analysis['elliptic_curves']['supported']))
                                                            <strong>지원 곡선:</strong>
                                                            <div class="mt-2">
                                                                @foreach ($analysis['elliptic_curves']['supported'] as $curve)
                                                                    <span
                                                                        class="badge bg-azure-lt text-azure-lt-fg me-1">{{ $curve }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted">타원곡선 정보 없음</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>측정 지표 설명</h6>
                                            <p class="mb-2"><strong>TLS 버전:</strong> 암호화 통신 프로토콜의 버전. TLS 1.2 이상이
                                                안전하며, TLS 1.3이 가장 최신이고 안전합니다.</p>
                                            <p class="mb-2"><strong>PFS (Perfect Forward Secrecy):</strong> 과거 통신 내용이
                                                미래에 해독되는 것을 방지하는 암호화 속성</p>
                                            <p class="mb-2"><strong>OCSP Stapling:</strong> 인증서 폐기 상태를 효율적으로 확인하는
                                                메커니즘</p>
                                            <p class="mb-2"><strong>HSTS:</strong> 브라우저가 항상 HTTPS로 접속하도록 강제하는 보안 정책
                                            </p>
                                            <p class="mb-0"><strong>타원곡선:</strong> 효율적인 공개키 암호화를 위한 수학적 구조. X25519,
                                                secp256r1 등이 안전합니다.</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>SSL/TLS 보안 개선 방안</h6>
                                            <p class="mb-2"><strong>1. 최신 프로토콜 사용:</strong> TLS 1.3을 활성화하고 TLS
                                                1.0/1.1을 비활성화하세요.</p>
                                            <p class="mb-2"><strong>2. 강한 암호군 설정:</strong> PFS를 지원하는 ECDHE/DHE 암호군을
                                                우선 사용하고 RC4, DES 등 약한 암호는 제거하세요.</p>
                                            <p class="mb-2"><strong>3. 인증서 관리:</strong> RSA는 최소 2048비트(권장 3072비트) 또는
                                                ECDSA 256비트를 사용하세요.</p>
                                            <p class="mb-2"><strong>4. OCSP Stapling 활성화:</strong> 서버 설정에서 OCSP
                                                Stapling을 활성화하여 성능과 보안을 개선하세요.</p>
                                            <p class="mb-0"><strong>5. HSTS 헤더 설정:</strong> max-age를 최소
                                                1년(31536000초)로 설정하고 includeSubDomains와 preload를 활성화하세요.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 SSL/TLS 보안 분석 결과를 확인할 수 있습니다.</p>
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
                                        <pre class="json-dump" id="json-data">{{ json_encode($currentTest->results['raw_json'] ?? $currentTest->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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
