@section('title')
    <title>🔍 최신 취약점 스캔 - Nuclei CVE 2024-2025 자동 탐지 | DevTeam Test</title>
    <meta name="description"
        content="Nuclei 기반 보안 스캐너로 2024-2025년 CVE 취약점, 구성 오류, 민감정보 노출을 자동 탐지하고 보안 등급을 평가합니다. 최신 보안 위협에 대응하세요.">
    <meta name="keywords" content="취약점 스캔, Nuclei, CVE 2024, CVE 2025, 보안 진단, 자동 스캐닝, 제로데이 취약점 탐지, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="최신 취약점 스캔 - Nuclei CVE 2024-2025 자동 탐지 | DevTeam Test" />
    <meta property="og:description"
        content="Nuclei로 최신 CVE 취약점과 보안 설정 오류를 자동 탐지합니다. 2024-2025년 신규 위협까지 포함한 실시간 보안 진단을 경험하세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test - 최신 취약점 스캔" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="최신 취약점 스캔 - Nuclei CVE 2024-2025 자동 탐지 | DevTeam Test" />
    <meta name="twitter:description" content="Nuclei 기반 최신 보안 취약점 탐지. CVE 2024-2025, 구성 오류, 민감정보 노출까지 정밀 분석." />
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
    'name' => '최신 취약점 스캔 - Nuclei CVE 2024-2025 자동 탐지',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'Nuclei 기반 보안 스캐너로 최신 CVE(2024-2025) 및 설정 오류·민감정보 노출까지 탐지하고 보안 등급을 평가합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔍 최신 취약점 스캔" subtitle="Nuclei 2024-2025 CVE 탐지" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                        data-bs-toggle="tab">Test Info</a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'results')"
                                        class="nav-link {{ $mainTabActive == 'results' ? 'active' : '' }}"
                                        data-bs-toggle="tab">Results</a>
                                </li>
                                <li class="nav-item">
                                    <a href="javascript:void(0);" wire:click="$set('mainTabActive', 'data')"
                                        class="nav-link {{ $mainTabActive == 'data' ? 'active' : '' }}"
                                        data-bs-toggle="tab">Data</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane {{ $mainTabActive == 'information' ? 'active show' : '' }}"
                                    id="tabs-information">
                                    <h3>Nuclei 기반 최신 CVE 취약점 자동 탐지</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>측정 도구:</strong> Nuclei by ProjectDiscovery - 업계 표준 취약점 스캐너로 템플릿 기반 빠른 스캔
                                        제공
                                        <br><br>
                                        <strong>테스트 목적:</strong><br>
                                        • 2024-2025년 신규 발표된 CVE 취약점 탐지<br>
                                        • 최근 공개된 제로데이 및 1-day 취약점 점검<br>
                                        • 구성 오류 및 기본 설정 취약점 발견<br>
                                        • 노출된 패널, 디버그 페이지, 백업 파일 탐지<br>
                                        • 서브도메인 탈취(Subdomain Takeover) 가능성 점검<br>
                                        • 민감정보 노출 (API 키, 토큰, 환경변수) 탐지
                                        <br><br>
                                        <strong>테스트 방식:</strong><br>
                                        • <strong>템플릿 기반:</strong> 2024-2025년 최신 취약점에 특화된 YAML 템플릿 활용<br>
                                        • <strong>비침투적:</strong> 실제 공격 없이 취약점 시그니처만 확인<br>
                                        • <strong>범위:</strong> 단일 URL 대상 (깊이 있는 크롤링 없음)<br>
                                        • <strong>우선순위:</strong> Critical, High 위주로 스캔 후 Medium, Low 순차 점검<br>
                                        • <strong>소요 시간:</strong> 약 30초-3분 (템플릿 수에 따라 변동)<br>
                                        • <strong>도메인 인증:</strong> 소유권이 확인된 도메인만 스캔 가능
                                        <br><br>
                                        <strong>최신 취약점 커버리지:</strong><br>
                                        • Log4Shell, Spring4Shell 같은 주요 RCE 취약점<br>
                                        • 최신 WordPress, Joomla, Drupal 플러그인 취약점<br>
                                        • Apache, Nginx, IIS 웹서버 설정 오류<br>
                                        • Git, SVN, ENV 파일 노출<br>
                                        • GraphQL, REST API 엔드포인트 취약점<br>
                                        • 클라우드 서비스 (AWS, Azure, GCP) 설정 오류
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
                                                    <td>Critical/High 0개, Medium 0개<br>2024-2025 CVE 미검출<br>공개
                                                        디렉터리/디버그/민감파일 노출 없음<br>보안 헤더/배너 노출 양호 (정보 최소화)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>High ≤1, Medium ≤1<br>최근 CVE 직접 노출 없음 (우회/조건 필요)<br>경미한 설정 경고
                                                        (정보성) 수준<br>패치/구성 관리 양호</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>High ≤2 또는 Medium ≤3<br>일부 구성 노출/배너 노출 존재<br>보호된 관리 엔드포인트 존재 (우회
                                                        어려움)<br>패치 지연 경향 (최근 보안 릴리즈 반영 지연)</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>High ≥3 또는 Medium 다수<br>민감 파일/백업/인덱싱 노출 발견<br>구버전 컴포넌트 추정 가능
                                                        (배너/메타 정보)<br>패치/구성 관리 체계적 개선 필요</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>Critical ≥1 또는 악용 난이도 낮은 High<br>최근 (2024-2025) CVE 직접 영향
                                                        추정<br>인증 없이 접근 가능한 위험 엔드포인트/파일<br>빌드/로그/환경 등 민감 정보 노출</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>다수의 Critical/High 동시 존재<br>최신 CVE 대량 미패치/광범위 노출<br>기본 보안 구성 결여
                                                        (방어 헤더/접근통제 부족)<br>전면적 보안 가드레일 부재</td>
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
                                            $templateDetails = $currentTest->results['template_details'] ?? [];
                                            $metrics = $currentTest->metrics ?? [];
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
                                                                    {{ $metrics['vulnerability_counts']['critical'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Critical</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['high'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">High</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['medium'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Medium</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['low'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Low</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg">
                                                        <div class="card card-sm">
                                                            <div class="card-body text-center">
                                                                <div class="text-h1 fw-bold">
                                                                    {{ $metrics['vulnerability_counts']['info'] ?? 0 }}
                                                                </div>
                                                                <div class="text-muted">Info</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (isset($metrics['scan_duration']) && $metrics['scan_duration'] > 0)
                                                    <div class="text-muted small mt-2">
                                                        스캔 시간: {{ $metrics['scan_duration'] }}초 |
                                                        매칭된 템플릿: {{ $metrics['templates_matched'] ?? 0 }}개
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Critical/High 취약점 상세 -->
                                        @foreach (['critical' => 'bg-red-lt text-red-lt-fg', 'high' => 'bg-orange-lt text-orange-lt-fg'] as $severity => $badgeClass)
                                            @if (!empty($vulnerabilities[$severity]))
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            {{ ucfirst($severity) }} 취약점
                                                            ({{ count($vulnerabilities[$severity]) }}개)
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach ($vulnerabilities[$severity] as $vuln)
                                                            <div class="card card-sm mb-2">
                                                                <div class="card-body">
                                                                    <div class="fw-bold">
                                                                        {{ $vuln['name'] ?? 'Unknown' }}</div>
                                                                    @if (!empty($vuln['description']))
                                                                        <div class="text-muted small mb-1">
                                                                            {{ $vuln['description'] }}</div>
                                                                    @endif
                                                                    <div class="small text-muted">
                                                                        템플릿:
                                                                        <code>{{ $vuln['template_id'] ?? '' }}</code>
                                                                        @if (!empty($vuln['matched_at']))
                                                                            | 대상: {{ $vuln['matched_at'] }}
                                                                        @endif
                                                                    </div>
                                                                    @if (!empty($vuln['reference']) && is_array($vuln['reference']))
                                                                        <div class="small mt-1">
                                                                            참고:
                                                                            @foreach (array_slice($vuln['reference'], 0, 2) as $ref)
                                                                                <a href="{{ $ref }}"
                                                                                    target="_blank"
                                                                                    class="text-primary">{{ parse_url($ref, PHP_URL_HOST) ?? 'Link' }}</a>
                                                                                @if (!$loop->last)
                                                                                    |
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- Medium/Low 취약점 요약 -->
                                        @foreach (['medium' => 'bg-yellow-lt text-yellow-lt-fg', 'low' => 'bg-blue-lt text-blue-lt-fg'] as $severity => $badgeClass)
                                            @if (!empty($vulnerabilities[$severity]))
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            {{ ucfirst($severity) }} 취약점
                                                            ({{ count($vulnerabilities[$severity]) }}개)
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>취약점명</th>
                                                                        <th>템플릿 ID</th>
                                                                        <th>대상</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($vulnerabilities[$severity] as $vuln)
                                                                        <tr>
                                                                            <td>{{ $vuln['name'] ?? 'Unknown' }}</td>
                                                                            <td><code>{{ $vuln['template_id'] ?? '' }}</code>
                                                                            </td>
                                                                            <td class="text-muted small">
                                                                                {{ Str::limit($vuln['matched_at'] ?? '', 50) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>측정 지표 설명</h6>
                                            <p class="mb-2"><strong>Critical:</strong> 즉각적인 원격 코드 실행(RCE), 인증 우회, 데이터
                                                유출 등 심각한 취약점</p>
                                            <p class="mb-2"><strong>High:</strong> SQL Injection, XSS, SSRF 등 악용 가능성이
                                                높은 취약점</p>
                                            <p class="mb-2"><strong>Medium:</strong> 정보 노출, 설정 오류, 구버전 소프트웨어 등 중간 위험도
                                            </p>
                                            <p class="mb-2"><strong>Low:</strong> 디렉터리 리스팅, 배너 노출 등 낮은 위험도</p>
                                            <p class="mb-0"><strong>Info:</strong> 보안에 직접적 영향은 없으나 참고할 정보</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>보안 개선 방안</h6>
                                            <p class="mb-2"><strong>1. 즉시 패치:</strong> Critical/High 취약점은 발견 즉시 패치를
                                                적용하거나 임시 방어 조치를 취합니다.</p>
                                            <p class="mb-2"><strong>2. 정기 업데이트:</strong> CMS, 플러그인, 프레임워크를 최신 버전으로
                                                유지합니다.</p>
                                            <p class="mb-2"><strong>3. 설정 강화:</strong> 불필요한 서비스 비활성화, 디버그 모드 제거, 기본
                                                계정 변경</p>
                                            <p class="mb-2"><strong>4. 접근 통제:</strong> 관리자 페이지 IP 제한, 2FA 적용, 최소 권한
                                                원칙 적용</p>
                                            <p class="mb-2"><strong>5. 모니터링:</strong> 보안 로그 모니터링, 이상 행위 탐지 시스템 구축</p>
                                            <p class="mb-0"><strong>6. 정기 스캔:</strong> 월 1회 이상 취약점 스캔을 실행하여 새로운 위협을
                                                조기 발견</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 최신 취약점 스캔 결과를 확인할 수 있습니다.</p>
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
