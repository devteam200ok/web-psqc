@section('title')
    <title>🛡️ 보안 취약점 스캔 - OWASP ZAP 패시브 스캔 | DevTeam Test</title>
    <meta name="description" content="OWASP ZAP 패시브 스캔으로 웹사이트의 SQL Injection, XSS, 보안 헤더 등 주요 보안 취약점을 자동 탐지하고 보안 등급을 평가하세요.">
    <meta name="keywords" content="보안 취약점 스캔, OWASP ZAP, 패시브 스캔, SQL Injection, XSS 탐지, 보안 헤더 검사, 웹 보안 테스트, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="보안 취약점 스캔 - OWASP ZAP 패시브 스캔 | DevTeam Test" />
    <meta property="og:description"
        content="SQL Injection, XSS, 보안 헤더 등 주요 보안 취약점을 OWASP ZAP 패시브 스캔으로 자동 탐지하고 A+ 등급까지 평가받을 수 있습니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 보안 취약점 스캔" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="보안 취약점 스캔 - OWASP ZAP 패시브 스캔 | DevTeam Test" />
    <meta name="twitter:description"
        content="OWASP ZAP 패시브 스캔으로 SQL Injection, XSS, 보안 헤더 등 주요 보안 취약점을 자동 탐지하고 개선 가이드를 제공합니다." />
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
    'name' => '보안 취약점 스캔 - OWASP ZAP 패시브 스캔',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'OWASP ZAP 패시브 스캔으로 SQL Injection, XSS, 보안 헤더 등 주요 취약점을 자동 탐지하고 보안 등급을 평가합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🛡️ 보안 취약점 스캔" subtitle="OWASP ZAP 패시브 스캔" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
        :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-info d-block mb-4">
                                    <h5>🔐 로그인 필요</h5>
                                    <p class="mb-2">보안 스캔은 도메인 소유권 인증이 필요한 서비스입니다.</p>
                                    <p class="mb-0">로그인 후 사이드바의 "도메인" 탭에서 도메인을 등록하고 소유권을 인증해주세요.</p>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-xl-12">
                                    <label class="form-label">홈페이지 주소</label>
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
                                                진행 중...
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

                    {{-- 개별 테스트 고유 내용 --}}
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
                                    <h3>OWASP ZAP 패시브 스캔 - 비침입적 보안 취약점 분석</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>측정 도구:</strong> OWASP ZAP (Zed Attack Proxy) - 세계에서 가장 널리 사용되는 오픈소스 웹
                                        애플리케이션 보안 테스팅 도구
                                        <br><br>
                                        <strong>테스트 목적:</strong><br>
                                        • 웹사이트의 HTTP 응답을 분석하여 잠재적 보안 취약점 식별<br>
                                        • 보안 헤더 구성 검증 (HSTS, X-Frame-Options, X-Content-Type-Options 등)<br>
                                        • 민감정보 노출 탐지 (쿠키 설정, 디버그 정보, 서버 정보 등)<br>
                                        • 세션 관리 취약점 점검<br>
                                        • 잠재적 인젝션 포인트 식별<br>
                                        • 사용 중인 기술 스택 탐지
                                        <br><br>
                                        <strong>테스트 방식:</strong><br>
                                        • <strong>패시브 스캔:</strong> 실제 공격을 시도하지 않고 HTTP 요청/응답만 분석<br>
                                        • <strong>스캔 범위:</strong> 지정된 URL의 메인 페이지만 대상 (하위 페이지 탐색 없음)<br>
                                        • <strong>제외 항목:</strong> CSP(Content Security Policy) 관련 경고는 별도 헤더 점검에서 다루므로
                                        제외<br>
                                        • <strong>소요 시간:</strong> 약 10-20초<br>
                                        • <strong>도메인 인증:</strong> 소유권이 확인된 도메인만 스캔 가능
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
                                                    <td>High/Medium 0개<br>보안 헤더 완비 (HTTPS, HSTS, X-Frame-Options
                                                        등)<br>민감정보 노출 없음 (쿠키, 주석, 디버그)<br>서버/프레임워크 버전 정보 최소화</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>High 0, Medium ≤1<br>보안 헤더 대부분 충족, 일부 누락 있음<br>민감정보 노출 없음<br>경미한
                                                        정보 노출 (예: 서버 타입) 존재</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>High ≤1, Medium ≤2<br>일부 보안 헤더 미구현 (HSTS, X-XSS-Protection
                                                        등)<br>세션 쿠키 Secure/HttpOnly 누락<br>주석/메타 정보에 경미한 내부 식별자 노출</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>High ≥2 또는 Medium ≥3<br>주요 보안 헤더 부재<br>민감 파라미터/토큰이 응답 내 직접
                                                        노출<br>세션 관리 취약 (쿠키 속성 전반 미흡)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>Critical ≥1 또는 High ≥3<br>인증/세션 관련 심각한 속성 누락<br>디버그/개발용 정보 노출
                                                        (스택 트레이스, 내부 IP)<br>공개 관리 콘솔/설정 파일 노출</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>광범위한 High 취약점<br>HTTPS 미적용 또는 전면 무력화<br>민감 데이터 평문 전송/노출<br>전반적
                                                        보안 헤더·세션 통제 부재</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $vulnerabilities = $currentTest->results['vulnerabilities'] ?? [];
                                            $technologies = $currentTest->results['technologies'] ?? [];
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

                                        <!-- 취약점 요약 -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">취약점 요약</h5>
                                                <div class="row g-2">
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $vulnerabilities['critical'] ?? 0 }}</div>
                                                                <div class="text-muted">Critical</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $vulnerabilities['high'] ?? 0 }}</div>
                                                                <div class="text-muted">High</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $vulnerabilities['medium'] ?? 0 }}</div>
                                                                <div class="text-muted">Medium</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $vulnerabilities['low'] ?? 0 }}</div>
                                                                <div class="text-muted">Low</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $vulnerabilities['informational'] ?? 0 }}</div>
                                                                <div class="text-muted">Info</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 취약점 상세 목록 -->
                                        @if (isset($vulnerabilities['details']) && count($vulnerabilities['details']) > 0)
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">발견된 취약점 상세</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>취약점명</th>
                                                                    <th>위험도</th>
                                                                    <th>신뢰도</th>
                                                                    <th>발견 수</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($vulnerabilities['details'] as $vuln)
                                                                    <tr>
                                                                        <td style="min-width: 170px">
                                                                            <strong>{{ $vuln['name'] }}</strong>
                                                                            @if (!empty($vuln['description']))
                                                                                <br><small
                                                                                    class="text-muted">{{ Str::limit($vuln['description'], 200) }}</small>
                                                                            @endif
                                                                            @if (!empty($vuln['solution']))
                                                                                <br><small class="text-success">해결:
                                                                                    {{ Str::limit($vuln['solution'], 150) }}</small>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $riskBadgeClass = match (
                                                                                    $vuln['risk']
                                                                                ) {
                                                                                    'critical'
                                                                                        => 'badge bg-red-lt text-red-lt-fg',
                                                                                    'high'
                                                                                        => 'badge bg-orange-lt text-orange-lt-fg',
                                                                                    'medium'
                                                                                        => 'badge bg-yellow-lt text-yellow-lt-fg',
                                                                                    'low'
                                                                                        => 'badge bg-blue-lt text-blue-lt-fg',
                                                                                    default
                                                                                        => 'badge bg-azure-lt text-azure-lt-fg',
                                                                                };
                                                                            @endphp
                                                                            <span
                                                                                class="{{ $riskBadgeClass }}">{{ ucfirst($vuln['risk']) }}</span>
                                                                        </td>
                                                                        <td>{{ $vuln['confidence'] ?? '-' }}</td>
                                                                        <td>{{ $vuln['instances'] ?? 0 }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 발견된 기술 상세 -->
                                        @if (isset($technologies) && count($technologies) > 0)
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h5 class="mb-3">발견된 기술 스택</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-vcenter table-nowrap">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>기술명</th>
                                                                    <th>카테고리</th>
                                                                    <th>설명</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($technologies as $tech)
                                                                    <tr>
                                                                        <td><strong>{{ $tech['name'] }}</strong></td>
                                                                        <td>
                                                                            <span
                                                                                class="badge bg-azure-lt text-azure-lt-fg">{{ $tech['category'] }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <small
                                                                                class="text-muted">{{ Str::limit($tech['description'], 200) }}</small>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>측정 지표 설명</h6>
                                            <p class="mb-2"><strong>Critical:</strong> 즉각적인 조치가 필요한 심각한 보안 취약점 (SQL
                                                Injection, XSS, RCE 등)</p>
                                            <p class="mb-2"><strong>High:</strong> 빠른 시일 내에 수정이 필요한 중요 취약점 (세션 관리 취약,
                                                CSRF 등)</p>
                                            <p class="mb-2"><strong>Medium:</strong> 보안 강화를 위해 개선이 권장되는 취약점 (보안 헤더 누락
                                                등)</p>
                                            <p class="mb-2"><strong>Low:</strong> 낮은 위험도의 취약점 (정보 노출, 구성 문제 등)</p>
                                            <p class="mb-0"><strong>Informational:</strong> 보안에 직접적인 영향은 없으나 참고할 사항
                                            </p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>보안 개선 방안</h6>
                                            <p class="mb-2"><strong>1. 보안 헤더 설정:</strong> HSTS, X-Frame-Options,
                                                X-Content-Type-Options, X-XSS-Protection 헤더를 적절히 구성하여 다양한 공격을 방어합니다.</p>
                                            <p class="mb-2"><strong>2. 세션 보안:</strong> 모든 쿠키에 Secure, HttpOnly,
                                                SameSite 속성을 설정하여 세션 하이재킹을 방지합니다.</p>
                                            <p class="mb-2"><strong>3. 정보 노출 최소화:</strong> 서버 버전, 프레임워크 정보, 디버그 메시지
                                                등의 노출을 차단합니다.</p>
                                            <p class="mb-2"><strong>4. HTTPS 적용:</strong> 모든 페이지에 HTTPS를 적용하고 HTTP를
                                                HTTPS로 리다이렉트합니다.</p>
                                            <p class="mb-0"><strong>5. 정기적인 보안 점검:</strong> 월 1회 이상 보안 스캔을 실행하여 새로운
                                                취약점을 조기에 발견하고 대응합니다.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 보안 취약점 스캔 결과를 확인할 수 있습니다.</p>
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
