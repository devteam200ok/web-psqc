@section('title')
    <title>🔒 SSL 기본 테스트 - testssl.sh · 인증서·프로토콜·취약점 검사 | DevTeam Test</title>
    <meta name="description"
        content="testssl.sh 기반 SSL/TLS 보안 진단: 인증서 유효성, 지원 프로토콜, 암호화 강도, 취약점 여부를 종합 검사합니다. HTTPS 보안성을 A+ 등급까지 평가하고 개선 가이드를 제공합니다.">
    <meta name="keywords"
        content="SSL 테스트, TLS 검사, testssl.sh, SSL 인증서 검증, 암호화 프로토콜, 보안 취약점, HTTPS 보안성, SSL 등급 평가, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="SSL 기본 테스트 - testssl.sh · 인증서·프로토콜·취약점 검사 | DevTeam Test" />
    <meta property="og:description"
        content="testssl.sh로 SSL/TLS 인증서, 프로토콜, 암호화 강도, 취약점을 종합 분석해 HTTPS 설정의 보안 수준을 정밀 평가하고 A+ 등급까지 인증서를 발급받으세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test SSL 보안 테스트" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="SSL 기본 테스트 - testssl.sh · 인증서·프로토콜·취약점 검사 | DevTeam Test" />
    <meta name="twitter:description" content="SSL/TLS 인증서·프로토콜·취약점을 testssl.sh로 종합 검사하고 보안 등급과 개선 가이드를 확인하세요." />
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
    'name' => 'SSL 기본 테스트 - testssl.sh · 인증서·프로토콜·취약점 검사',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'testssl.sh 도구로 SSL/TLS 인증서, 프로토콜, 암호화 강도, 취약점을 종합 분석하여 HTTPS 보안성을 A+ 등급까지 평가합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔒 SSL 기본 테스트" subtitle="testssl.sh · 인증서·프로토콜·취약점 종합 검사" :user-plan-usage="$userPlanUsage"
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
                                    <label class="form-label">웹사이트 URL</label>
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
                                                실행
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

                            <div class="alert alert-info d-block">
                                <div class="d-flex">
                                    <div>
                                        ℹ️ <strong>testssl.sh 보안 검사</strong><br>
                                        SSL/TLS 인증서, 암호화 프로토콜, 보안 취약점을 종합 분석합니다. 검사 시간은 약 5-10분 소요됩니다.
                                    </div>
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

                                    <h3>SSL 기본 테스트란?</h3>
                                    <div class="text-muted small mt-1 mb-4">
                                        <strong>testssl.sh</strong>는 SSL/TLS 구성을 종합적으로 검사하는 오픈소스 도구로,
                                        웹사이트의 HTTPS 보안 설정을 정밀하게 분석합니다.
                                    </div>

                                    <!-- testssl.sh 소개 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🔧 testssl.sh란?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>오픈소스 SSL/TLS 테스터</strong>: GitHub에서 10,000+ 스타를 받은 업계 표준 도구입니다.
                                            </li>
                                            <li><strong>포괄적 검사</strong>: SSL Labs와 유사하지만 더 상세한 기술적 정보를 제공합니다.</li>
                                            <li><strong>실시간 분석</strong>: 서버에 직접 연결하여 실제 SSL/TLS 설정을 검증합니다.</li>
                                            <li><strong>취약점 탐지</strong>: Heartbleed, POODLE, BEAST 등 주요 SSL/TLS 취약점을 자동
                                                검사합니다.</li>
                                        </ul>
                                    </div>

                                    <!-- 검사 항목 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📋 주요 검사 항목</h4>
                                        <div class="row small text-muted">
                                            <div class="col-md-6">
                                                <div class="mb-2"><strong>🔐 SSL/TLS 프로토콜</strong></div>
                                                <ul class="mb-3">
                                                    <li>지원 프로토콜 버전 (SSL 2.0/3.0, TLS 1.0~1.3)</li>
                                                    <li>취약한 구버전 프로토콜 탐지</li>
                                                    <li>최신 TLS 1.3 지원 여부</li>
                                                </ul>

                                                <div class="mb-2"><strong>📜 SSL 인증서</strong></div>
                                                <ul class="mb-3">
                                                    <li>인증서 유효성 및 만료일</li>
                                                    <li>인증서 체인 완전성</li>
                                                    <li>Subject Alternative Names (SAN)</li>
                                                    <li>OCSP Stapling 지원</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2"><strong>🔒 암호화 스위트</strong></div>
                                                <ul class="mb-3">
                                                    <li>지원하는 암호화 알고리즘</li>
                                                    <li>Perfect Forward Secrecy (PFS)</li>
                                                    <li>약한 암호화 스위트 탐지</li>
                                                </ul>

                                                <div class="mb-2"><strong>🛡️ 보안 취약점</strong></div>
                                                <ul class="mb-0">
                                                    <li>Heartbleed, POODLE, BEAST</li>
                                                    <li>CRIME, BREACH, FREAK</li>
                                                    <li>DROWN, LOGJAM, SWEET32</li>
                                                    <li>HTTP 보안 헤더 (HSTS 등)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 왜 중요한가 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎯 왜 SSL/TLS 검사가 중요한가?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>데이터 보호</strong>: 사용자와 서버 간 전송되는 모든 데이터의 암호화 품질을 보장합니다.</li>
                                            <li><strong>신뢰성 확보</strong>: 브라우저 경고 없이 안전한 HTTPS 연결을 제공합니다.</li>
                                            <li><strong>규정 준수</strong>: GDPR, PCI-DSS 등 보안 규정 요구사항을 충족합니다.</li>
                                            <li><strong>SEO 향상</strong>: Google 등 검색엔진에서 HTTPS 사이트를 우대합니다.</li>
                                            <li><strong>취약점 예방</strong>: 알려진 SSL/TLS 취약점으로부터 사전 보호합니다.</li>
                                        </ul>
                                    </div>

                                    <!-- 등급 기준 안내 -->
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>등급</th>
                                                    <th>점수</th>
                                                    <th>보안 기준</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90~100</td>
                                                    <td><strong>최신 TLS만</strong> 사용, <strong>취약점
                                                            없음</strong><br><strong>강력한 암호화 스위트</strong> 적용<br>인증서 및 체인
                                                        <strong>완전 정상</strong><br><strong>HSTS</strong> 등 보안 설정
                                                        <strong>우수</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td><strong>TLS 1.2/1.3</strong> 지원, 구버전 차단<br><strong>주요 취약점
                                                            없음</strong><br>일부 약한 암호나 설정 미흡 가능<br>전반적으로 <strong>안전한
                                                            수준</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td><strong>안전한 프로토콜</strong> 위주<br>약한 암호 스위트 <strong>일부
                                                            존재</strong><br>testssl.sh 경고(<strong>WEAK</strong>)
                                                        다수<br><strong>개선 필요</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>구버전 TLS <strong>일부 활성</strong><br><strong>취약 암호화</strong> 사용률
                                                        높음<br>인증서 <strong>만료 임박</strong>/단순 DV<br>취약점 <strong>소수
                                                            발견</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td><strong>SSLv3/TLS 1.0</strong> 허용<br><strong>취약 암호 다수</strong>
                                                        활성<br>인증서 체인 <strong>오류/만료 임박</strong><br><strong>다수
                                                            취약점</strong> 존재</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>SSL/TLS 설정 <strong>근본적 결함</strong><br><strong>취약 프로토콜</strong>
                                                        전면 허용<br>인증서 <strong>만료/자가서명</strong><br>testssl.sh
                                                        <strong>FAIL/VULNERABLE</strong> 다수
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-warning d-block mt-3">
                                        <strong>📋 인증서 발급 조건:</strong><br>
                                        • B등급 이상 달성<br>
                                        • 주요 보안 취약점 없음<br>
                                        • 유효한 SSL 인증서 보유<br>
                                        • 로그인 필요<br><br>

                                        <strong>⏰ 검사 시간:</strong> 약 5-10분 (서버 응답속도에 따라 차이)<br>
                                        <strong>🔄 권장 주기:</strong> 월 1회 정기 검사 (인증서 만료, 새로운 취약점 대응)
                                    </div>
                                </div>

                                <!-- 결과 탭 -->
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $results = $currentTest->results;
                                            $grade = $currentTest->overall_grade ?? 'N/A';
                                            $gradeClass = match ($grade) {
                                                'A+' => 'bg-green-lt text-green-lt-fg',
                                                'A' => 'bg-green-lt text-green-lt-fg',
                                                'B' => 'bg-yellow-lt text-yellow-lt-fg',
                                                'C' => 'bg-orange-lt text-orange-lt-fg',
                                                'D' => 'bg-red-lt text-red-lt-fg',
                                                'E' => 'bg-red-lt text-red-lt-fg',
                                                'F' => 'bg-red-lt text-red-lt-fg',
                                                default => 'bg-azure-lt text-azure-lt-fg',
                                            };

                                            $metrics = $currentTest->metrics ?? [];
                                            $tlsVersion = $metrics['tls_version'] ?? 'N/A';
                                            $forwardSecrecy = $metrics['forward_secrecy'] ?? false;
                                            $hstsEnabled = $metrics['hsts_enabled'] ?? false;
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- SSL/TLS 기본 정보 -->
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3>{{ $tlsVersion }}</h3>
                                                        <p class="mb-0">최고 TLS 버전</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3 class="text-{{ $forwardSecrecy ? 'success' : 'danger' }}">
                                                            {{ $forwardSecrecy ? '지원' : '미지원' }}
                                                        </h3>
                                                        <p class="mb-0">완전 순방향 보안</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3 class="text-{{ $hstsEnabled ? 'success' : 'warning' }}">
                                                            {{ $hstsEnabled ? '활성' : '비활성' }}
                                                        </h3>
                                                        <p class="mb-0">HSTS</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 개요 -->
                                        <h4>📋 개요</h4>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h6>기본 정보</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>대상 URL</th>
                                                        <td>{{ $currentTest->url }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP 주소</th>
                                                        <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>포트</th>
                                                        <td>{{ $results['port'] ?? '443' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>서버 배너</th>
                                                        <td>{{ $results['server_banner'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>테스트 시간</th>
                                                        <td>{{ $results['scan_time'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>SSL/TLS 요약</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>SSL 등급</th>
                                                        <td><span
                                                                class="badge {{ $gradeClass }}">{{ $grade }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>지원 프로토콜</th>
                                                        <td>
                                                            @if (isset($results['supported_protocols']) && count($results['supported_protocols']) > 0)
                                                                {{ implode(', ', $results['supported_protocols']) }}
                                                            @else
                                                                없음
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>취약 프로토콜</th>
                                                        <td>
                                                            @if (isset($results['vulnerable_protocols']) && count($results['vulnerable_protocols']) > 0)
                                                                <span
                                                                    class="text-danger">{{ implode(', ', $results['vulnerable_protocols']) }}</span>
                                                            @else
                                                                <span class="text-success">없음</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>인증서 유효기간</th>
                                                        <td>{{ $results['cert_expiry'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- 인증서 -->
                                        <h4>🔒 인증서</h4>
                                        <div class="mb-4">
                                            @if (isset($results['certificate']))
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>발급자 (Issuer)</th>
                                                        <td>{{ $results['certificate']['issuer'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>주체 (Subject)</th>
                                                        <td>{{ $results['certificate']['subject'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>일반 이름 (CN)</th>
                                                        <td>{{ $results['certificate']['common_name'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>유효 시작일</th>
                                                        <td>{{ $results['certificate']['valid_from'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>유효 만료일</th>
                                                        <td>{{ $results['certificate']['valid_until'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>서명 알고리즘</th>
                                                        <td>{{ $results['certificate']['signature_algorithm'] ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>키 크기</th>
                                                        <td>{{ $results['certificate']['key_size'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <div class="alert alert-warning">인증서 정보를 찾을 수 없습니다.</div>
                                            @endif
                                        </div>

                                        <hr>

                                        <!-- 프로토콜 -->
                                        <h4>🔐 프로토콜</h4>
                                        <div class="mb-4">
                                            @if (isset($results['protocol_support']) && count($results['protocol_support']) > 0)
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>프로토콜</th>
                                                            <th>지원 여부</th>
                                                            <th>보안 등급</th>
                                                            <th>권장사항</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($results['protocol_support'] as $protocol => $info)
                                                            @php
                                                                $supported = $info['supported'] ?? false;
                                                                $security = $info['security_level'] ?? 'unknown';
                                                                $badgeClass = match ($security) {
                                                                    'secure' => 'bg-green-lt text-green-lt-fg',
                                                                    'weak' => 'bg-yellow-lt text-yellow-lt-fg',
                                                                    'insecure' => 'bg-red-lt text-red-lt-fg',
                                                                    default => 'bg-azure-lt text-azure-lt-fg',
                                                                };

                                                                $recommendation = match ($protocol) {
                                                                    'TLS 1.3' => $supported ? '권장' : '활성화 권장',
                                                                    'TLS 1.2' => $supported ? '권장' : '필수',
                                                                    'TLS 1.1', 'TLS 1' => $supported
                                                                        ? '비활성화 권장'
                                                                        : '올바른 설정',
                                                                    'SSLv3', 'SSLv2' => $supported
                                                                        ? '즉시 비활성화 필요'
                                                                        : '올바른 설정',
                                                                    default => '-',
                                                                };
                                                            @endphp
                                                            <tr
                                                                class="{{ $supported && in_array($protocol, ['SSLv2', 'SSLv3', 'TLS 1', 'TLS 1.1']) ? 'table-danger' : '' }}">
                                                                <td><strong>{{ strtoupper($protocol) }}</strong></td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $supported ? ($security === 'insecure' || $security === 'weak' ? 'bg-red-lt text-red-lt-fg' : 'bg-green-lt text-green-lt-fg') : 'bg-azure-lt text-azure-lt-fg' }}">
                                                                        {{ $supported ? '지원' : '미지원' }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if ($supported)
                                                                        <span class="badge {{ $badgeClass }}">
                                                                            @switch($security)
                                                                                @case('secure')
                                                                                    안전
                                                                                @break

                                                                                @case('weak')
                                                                                    약함
                                                                                @break

                                                                                @case('insecure')
                                                                                    취약
                                                                                @break

                                                                                @default
                                                                                    미상
                                                                            @endswitch
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td><small>{{ $recommendation }}</small></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-warning">프로토콜 지원 정보를 찾을 수 없습니다.</div>
                                            @endif
                                        </div>

                                        <hr>

                                        <!-- 취약점 -->
                                        <h4>🛡️ 취약점
                                            @if (isset($results['vulnerabilities']) && count($results['vulnerabilities']) > 0)
                                                @php
                                                    $vulnerableCount = 0;
                                                    foreach ($results['vulnerabilities'] as $status) {
                                                        if ($status['vulnerable'] ?? false) {
                                                            $vulnerableCount++;
                                                        }
                                                    }
                                                @endphp
                                                @if ($vulnerableCount > 0)
                                                    <span
                                                        class="badge bg-yellow-lt text-yellow-lt-fg ms-2">{{ $vulnerableCount }}개
                                                        발견</span>
                                                @endif
                                            @endif
                                        </h4>
                                        <div class="mb-4">
                                            @if (isset($results['vulnerabilities']) && count($results['vulnerabilities']) > 0)
                                                @php
                                                    $criticalCount = 0;
                                                    foreach ($results['vulnerabilities'] as $status) {
                                                        if (
                                                            ($status['vulnerable'] ?? false) &&
                                                            ($status['severity'] ?? '') === 'high'
                                                        ) {
                                                            $criticalCount++;
                                                        }
                                                    }
                                                @endphp

                                                <!-- 고위험 취약점 알림만 유지 -->
                                                @if ($criticalCount > 0)
                                                    <div class="alert alert-danger mb-4">
                                                        <h6><strong>{{ $criticalCount }}개의 고위험 취약점</strong>이 포함되어 있습니다.
                                                            즉시 조치가 필요합니다.</h6>
                                                    </div>
                                                @endif

                                                <!-- 취약점 상세 목록 -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        @foreach ($results['vulnerabilities'] as $vuln => $status)
                                                            @php
                                                                $isVulnerable = $status['vulnerable'] ?? false;
                                                                $severity = $status['severity'] ?? 'low';

                                                                $badgeClass = '';
                                                                if (!$isVulnerable) {
                                                                    $badgeClass = 'bg-green-lt text-green-lt-fg';
                                                                } elseif ($severity === 'high') {
                                                                    $badgeClass = 'bg-red-lt text-red-lt-fg';
                                                                } else {
                                                                    $badgeClass = 'bg-yellow-lt text-yellow-lt-fg';
                                                                }

                                                                $vulnName = str_replace(
                                                                    ['_', '-'],
                                                                    ' ',
                                                                    strtoupper($vuln),
                                                                );
                                                                $badgeText = !$isVulnerable
                                                                    ? '안전'
                                                                    : ($severity === 'high'
                                                                        ? '위험'
                                                                        : '유의');

                                                                // 취약점별 한글 설명
                                                                $koreanDesc = match ($vuln) {
                                                                    'heartbleed'
                                                                        => 'OpenSSL 라이브러리의 메모리 누수 취약점으로, 서버 메모리에서 민감한 정보가 노출될 수 있습니다.',
                                                                    'ccs'
                                                                        => 'OpenSSL의 ChangeCipherSpec 메시지 처리 취약점으로, 중간자 공격이 가능할 수 있습니다.',
                                                                    'rc4'
                                                                        => 'RC4 암호화 알고리즘의 약점으로 인해 암호화된 데이터가 해독될 위험이 있습니다.',
                                                                    'beast'
                                                                        => 'TLS 1.0의 CBC 모드 취약점으로, 특정 조건에서 암호화가 깨질 수 있습니다.',
                                                                    'crime'
                                                                        => 'TLS 압축을 이용한 공격으로, 압축된 데이터에서 비밀 정보를 추출할 수 있습니다.',
                                                                    'breach'
                                                                        => 'HTTP 압축을 이용한 공격으로, 웹 응답에서 비밀 토큰을 추출할 수 있습니다.',
                                                                    'drown'
                                                                        => 'SSLv2의 취약점을 이용해 TLS 연결을 공격하는 방법입니다.',
                                                                    'freak'
                                                                        => '약한 RSA 키를 강제로 사용하게 만드는 공격으로, 암호화를 약화시킬 수 있습니다.',
                                                                    'robot'
                                                                        => 'RSA 패딩 오라클 공격으로, RSA 개인키를 추출할 수 있는 취약점입니다.',
                                                                    'logjam'
                                                                        => 'Diffie-Hellman 키 교환의 약한 소수를 이용한 공격입니다.',
                                                                    'poodle'
                                                                        => 'SSLv3의 패딩 오라클 공격으로, 암호화된 데이터를 해독할 수 있습니다.',
                                                                    'lucky13'
                                                                        => 'CBC 모드 암호화의 타이밍 공격으로, 평문 데이터를 복구할 수 있습니다.',
                                                                    'sweet32'
                                                                        => '64비트 블록 암호의 생일 공격으로, 대량의 데이터 전송 시 위험합니다.',
                                                                    'winshock'
                                                                        => 'Windows Schannel의 취약점으로, 원격 코드 실행이 가능할 수 있습니다.',
                                                                    'ticketbleed'
                                                                        => 'TLS 세션 티켓의 메모리 누수 취약점입니다.',
                                                                    default => '알려진 SSL/TLS 보안 취약점입니다.',
                                                                };
                                                            @endphp
                                                            <div class="mb-3 p-3 border rounded">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start mb-2">
                                                                    <h5 class="mb-0">{{ $vulnName }}</h5>
                                                                    <span
                                                                        class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                                                </div>

                                                                <p class="text-dark mb-2">
                                                                    <small>{{ $koreanDesc }}</small>
                                                                </p>

                                                                @if (isset($status['description']))
                                                                    <p class="text-muted mb-0">
                                                                        <small>({{ $status['description'] }})</small>
                                                                    </p>
                                                                @endif

                                                                @if ($isVulnerable && $severity === 'high')
                                                                    <div class="alert alert-danger mt-2 p-2">
                                                                        <small><strong>고위험:</strong> 즉시 조치가
                                                                            필요합니다.</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <h6>취약점 정보 없음</h6>
                                                    <p class="mb-0">취약점 검사 결과를 찾을 수 없습니다. 테스트가 완전히 완료되지 않았을 수 있습니다.
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <hr>

                                        <!-- SSL/TLS 보안 개선 권장사항 -->
                                        <div class="alert alert-info d-block">
                                            <h6>SSL/TLS 보안 개선 권장사항</h6>
                                            <ul class="mb-0">
                                                @if (!$hstsEnabled)
                                                    <li><strong>HSTS 활성화:</strong> HTTP Strict Transport Security 헤더를
                                                        추가하여 HTTPS 강제화</li>
                                                @endif
                                                @if (!$forwardSecrecy)
                                                    <li><strong>완전 순방향 보안:</strong> ECDHE 키 교환을 지원하는 암호화 스위트 활성화</li>
                                                @endif
                                                @if (in_array($grade, ['C', 'D', 'F']))
                                                    <li><strong>구버전 프로토콜 비활성화:</strong> SSL 2.0/3.0, TLS 1.0/1.1 완전 차단
                                                    </li>
                                                    <li><strong>강력한 암호화 스위트:</strong> AES-GCM, ChaCha20-Poly1305 등 AEAD
                                                        암호 사용</li>
                                                @endif
                                                <li><strong>정기 업데이트:</strong> 웹 서버 소프트웨어 및 SSL 라이브러리 최신 버전 유지</li>
                                                <li><strong>인증서 관리:</strong> 만료 전 자동 갱신 설정 및 인증서 체인 완전성 확인</li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 SSL/TLS 보안 검사 결과를 확인할 수 있습니다.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- 데이터 탭 -->
                                <div class="tab-pane {{ $mainTabActive == 'data' ? 'active show' : '' }}"
                                    id="tabs-data">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Raw testssl.sh Output</h5>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="copyJsonToClipboard()" title="데이터 복사">
                                                복사
                                            </button>
                                        </div>
                                        <pre class="bg-dark text-light p-3 rounded json-dump" id="json-data"
                                            style="max-height: 600px; overflow-y: auto; font-size: 11px; line-height: 1.2;">{{ $currentTest->results['raw_output'] ?? '데이터 없음' }}</pre>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 Raw testssl.sh 출력을 확인할 수 있습니다.</p>
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
