@section('title')
    <title>🔒 SSL Basic Test – testssl.sh · Certificate · Protocol · Vulnerabilities | Web-PSQC</title>
    <meta name="description"
        content="SSL/TLS security diagnostics with testssl.sh: validate certificates, supported protocols, cipher strength, and known vulnerabilities. Evaluate HTTPS security up to A+ with actionable guidance.">
    <meta name="keywords"
        content="SSL test, TLS scan, testssl.sh, certificate validation, encryption protocols, security vulnerabilities, HTTPS security, SSL grading, Web-PSQC">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="SSL Basic Test – testssl.sh · Certificate · Protocol · Vulnerabilities" />
    <meta property="og:description"
        content="Analyze certificates, protocols, cipher strength, and vulnerabilities with testssl.sh to evaluate HTTPS security and qualify for an A+ certificate." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC SSL Security Test" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="SSL Basic Test – testssl.sh · Certificate · Protocol · Vulnerabilities | Web-PSQC" />
    <meta name="twitter:description" content="Use testssl.sh to scan SSL/TLS certificates, protocols, and vulnerabilities; review grades and improvement guidance." />
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
    'name' => 'SSL Basic Test – testssl.sh · Certificate · Protocol · Vulnerabilities',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' => 'Comprehensive analysis of SSL/TLS certificates, protocols, cipher strength, and vulnerabilities using testssl.sh to evaluate HTTPS security up to A+.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- Header (shared component) --}}
    <x-test-shared.header title="🔒 SSL Basic Test" subtitle="testssl.sh · Certificate · Protocol · Vulnerabilities" :user-plan-usage="$userPlanUsage"
        :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL form --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- URL input form -->
                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">Website URL</label>
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
                                                Running test...
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
                                        <h4 class="h6 fw-bold mb-2">🔧 What is testssl.sh?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Open‑source SSL/TLS tester</strong>: industry‑standard tool with 10k+ GitHub stars.
                                            </li>
                                            <li><strong>Comprehensive coverage</strong>: similar to SSL Labs with deeper technical details.</li>
                                            <li><strong>Live analysis</strong>: connects directly to your server to validate actual settings.</li>
                                            <li><strong>Vulnerability detection</strong>: scans for Heartbleed, POODLE, BEAST, and more.</li>
                                        </ul>
                                    </div>

                                    <!-- 검사 항목 -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">📋 Key checks</h4>
                                        <div class="row small text-muted">
                                            <div class="col-md-6">
                                                <div class="mb-2"><strong>🔐 SSL/TLS Protocols</strong></div>
                                                <ul class="mb-3">
                                                    <li>Supported protocol versions (SSL 2.0/3.0, TLS 1.0–1.3)</li>
                                                    <li>Detect vulnerable legacy protocols</li>
                                                    <li>Check TLS 1.3 support</li>
                                                </ul>

                                                <div class="mb-2"><strong>📜 SSL Certificates</strong></div>
                                                <ul class="mb-3">
                                                    <li>Certificate validity/expiry</li>
                                                    <li>인증서 체인 완전성</li>
                                                    <li>Subject Alternative Names (SAN)</li>
                                                    <li>OCSP Stapling 지원</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2"><strong>🔒 Cipher Suites</strong></div>
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
                                                    <li>HTTP security headers (HSTS, etc.)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Why it matters -->
                                    <div class="mb-4">
                                        <h4 class="h6 fw-bold mb-2">🎯 Why is SSL/TLS testing important?</h4>
                                        <ul class="text-muted small mb-0">
                                            <li><strong>Data protection</strong>: ensures encryption quality for all data in transit.</li>
                                            <li><strong>Trust</strong>: delivers HTTPS without browser warnings.</li>
                                            <li><strong>Compliance</strong>: meets standards like GDPR and PCI‑DSS.</li>
                                            <li><strong>SEO</strong>: HTTPS is favored by search engines.</li>
                                            <li><strong>Prevention</strong>: guards against known SSL/TLS vulnerabilities.</li>
                                        </ul>
                                    </div>

                                    <!-- 등급 기준 안내 -->
                                    <div class="table-responsive">
                                        <table class="table table-sm criteria-table table-vcenter table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Grade</th>
                                                    <th>Score</th>
                                                    <th>Security criteria</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="badge badge-a-plus">A+</span></td>
                                                    <td>90–100</td>
                                                    <td><strong>Only latest TLS</strong> used, <strong>no vulnerabilities</strong><br><strong>Strong cipher suites</strong><br>Certificate and chain <strong>fully valid</strong><br><strong>HSTS</strong> and related settings <strong>strong</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80–89</td>
                                                    <td><strong>TLS 1.2/1.3</strong> supported; legacy blocked<br><strong>No major vulnerabilities</strong><br>Possible minor weak ciphers or misconfigs<br><strong>Generally safe</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70–79</td>
                                                    <td><strong>Mostly secure protocols</strong><br><strong>Some</strong> weak ciphers present<br>Many testssl.sh <strong>WEAK</strong> warnings<br><strong>Needs improvement</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60–69</td>
                                                    <td><strong>Some legacy TLS</strong> enabled<br><strong>High</strong> use of weak crypto<br>Certificate <strong>near expiry</strong>/simple DV<br><strong>Few vulnerabilities</strong> found</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50–59</td>
                                                    <td><strong>SSLv3/TLS 1.0</strong> permitted<br><strong>Many</strong> weak ciphers enabled<br>Certificate chain <strong>errors/near expiry</strong><br><strong>Multiple vulnerabilities</strong> present</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0–49</td>
                                                    <td>SSL/TLS configuration <strong>fundamental flaws</strong><br><strong>Vulnerable protocols</strong> broadly allowed<br>Certificate <strong>expired/self‑signed</strong><br>Many testssl.sh <strong>FAIL/VULNERABLE</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-warning d-block mt-3">
                                        <strong>📋 Certificate issuance requirements:</strong><br>
                                        • Grade <strong>B</strong> or higher<br>
                                        • No major security vulnerabilities<br>
                                        • Valid SSL certificate present<br>
                                        • Sign‑in required<br><br>

                                        <strong>⏰ Typical duration:</strong> ~5–10 minutes (varies by server response)<br>
                                        <strong>🔄 Recommended cadence:</strong> monthly checks (certificate expiry, new CVEs)
                                    </div>
                                </div>

                                <!-- Results tab -->
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
                                        <h4>📋 Overview</h4>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h6>Basics</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>Target URL</th>
                                                        <td>{{ $currentTest->url }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP Address</th>
                                                        <td>{{ $results['ip_address'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port</th>
                                                        <td>{{ $results['port'] ?? '443' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Server Banner</th>
                                                        <td>{{ $results['server_banner'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Test Time</th>
                                                        <td>{{ $results['scan_time'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>SSL/TLS Summary</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>SSL Grade</th>
                                                        <td><span
                                                                class="badge {{ $gradeClass }}">{{ $grade }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Supported Protocols</th>
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
                                        <h4>🛡️ Vulnerabilities
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
                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg ms-2">{{ $vulnerableCount }} found</span>
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

                                                <!-- High-risk vulnerabilities notice -->
                                                @if ($criticalCount > 0)
                                                    <div class="alert alert-danger mb-4">
                                                        <h6><strong>{{ $criticalCount }} high-risk vulnerabilities</strong> detected. Immediate action is recommended.</h6>
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
                                                                    ? 'Safe'
                                                                    : ($severity === 'high'
                                                                        ? 'High'
                                                                        : 'Warn');

                                                                // Vulnerability descriptions (EN)
                                                                $koreanDesc = match ($vuln) {
                                                                    'heartbleed'
                                                                        => 'OpenSSL memory disclosure; sensitive data may leak from server memory.',
                                                                    'ccs'
                                                                        => 'OpenSSL ChangeCipherSpec processing; enables man‑in‑the‑middle (MITM) attacks.',
                                                                    'rc4'
                                                                        => 'Weaknesses in RC4 cipher may allow decryption of encrypted data.',
                                                                    'beast'
                                                                        => 'TLS 1.0 CBC mode vulnerability; encryption can be broken under certain conditions.',
                                                                    'crime'
                                                                        => 'Exploits TLS compression to extract secrets from compressed data.',
                                                                    'breach'
                                                                        => 'Uses HTTP compression to extract secret tokens from web responses.',
                                                                    'drown'
                                                                        => 'Abuses SSLv2 weaknesses to attack TLS connections.',
                                                                    'freak'
                                                                        => 'Forces use of weak RSA keys, weakening encryption.',
                                                                    'robot'
                                                                        => 'RSA padding oracle attack; can expose RSA private keys.',
                                                                    'logjam'
                                                                        => 'Targets weak primes in Diffie‑Hellman key exchange.',
                                                                    'poodle'
                                                                        => 'SSLv3 padding oracle enabling decryption of encrypted data.',
                                                                    'lucky13'
                                                                        => 'Timing attack on CBC mode; may recover plaintext.',
                                                                    'sweet32'
                                                                        => 'Birthday attack on 64‑bit block ciphers; risky on large transfers.',
                                                                    'winshock'
                                                                        => 'Windows Schannel vulnerability; may allow remote code execution.',
                                                                    'ticketbleed'
                                                                        => 'Memory disclosure in TLS session tickets.',
                                                                    default => 'Known SSL/TLS security vulnerability.',
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
