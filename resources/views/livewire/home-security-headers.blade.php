@section('title')
    <title>🔒 보안 헤더 검사 - CSP·XFO·HSTS 등 6대 핵심 헤더 분석 | DevTeam Test</title>
    <meta name="description"
        content="CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS 등 6대 핵심 보안 헤더를 분석하여 웹사이트 보안 취약점을 진단하고 개선 가이드를 제공합니다.">
    <meta name="keywords"
        content="보안 헤더 검사, CSP 분석, X-Frame-Options, HSTS, Referrer-Policy, Permissions-Policy, 웹 보안 점검, XSS 방어, 클릭재킹 방지, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="보안 헤더 검사 - CSP·XFO·HSTS 등 6대 핵심 헤더 분석 | DevTeam Test" />
    <meta property="og:description" content="웹사이트의 6대 보안 헤더를 자동 분석해 보안 수준을 평가하고 A+ 등급까지 인증서를 발급받으세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 보안 헤더 검사" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="보안 헤더 검사 - CSP·XFO·HSTS 등 6대 핵심 헤더 분석 | DevTeam Test" />
    <meta name="twitter:description" content="CSP, XFO, HSTS 등 주요 보안 헤더 점검. 웹 보안 강화를 위한 자동 진단과 개선 가이드 제공." />
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
    'name' => '보안 헤더 검사 - CSP·XFO·HSTS 등 6대 핵심 헤더 분석',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
    'description' => 'CSP, X-Frame-Options, HSTS 등 6대 핵심 보안 헤더를 분석해 취약점을 진단하고 개선 방법을 안내합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔒 보안 헤더 검사" subtitle="CSP / XFO / X-Content-Type / Referrer / Permissions / HSTS"
        :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

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
                                    <label class="form-label">웹사이트 주소</label>
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
                                                검사
                                            @endif
                                        </button>
                                    </div>
                                    @error('url')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">HEAD → GET 폴백 · 리다이렉트 추적(최대 5)</small>

                                    @if ($hasProOrAgencyPlan)
                                        <div class="mt-2">
                                            <a href="javascript:void(0)" wire:click="toggleScheduleForm"
                                                class="text-primary me-3">검사 예약</a>
                                            <a href="javascript:void(0)" wire:click="toggleRecurringForm"
                                                class="text-primary">스케줄 등록</a>
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

                        {{-- 스케줄 등록 폼 (공통 컴포넌트) --}}
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
                                    <h3>6대 핵심 보안 헤더 종합 검사</h3>
                                    <div class="text-muted small mt-1">
                                        HTTP 응답 헤더를 통해 브라우저의 보안 기능을 활성화하여 웹 애플리케이션을 다양한 공격으로부터 보호합니다.
                                        <br><br>
                                        <strong>측정 도구:</strong> Node.js 기반 자체 개발 스크립터 (axios HTTP 클라이언트 활용)
                                        <br>
                                        <strong>테스트 목적:</strong> XSS, 클릭재킹, MIME 스니핑, 정보 유출 등 주요 웹 취약점 방어 수준 평가
                                        <br><br>
                                        <strong>검사 항목:</strong>
                                        <br>
                                        • <strong>Content-Security-Policy (CSP)</strong> – 리소스 로드 출처를 제한, XSS·서드파티 스크립트
                                        악용 방지
                                        <br>
                                        • <strong>X-Frame-Options / frame-ancestors</strong> – iframe 삽입 차단, 클릭재킹·피싱형
                                        오버레이 방지
                                        <br>
                                        • <strong>X-Content-Type-Options</strong> – MIME 스니핑 차단, 잘못된 실행 취약점 방어
                                        <br>
                                        • <strong>Referrer-Policy</strong> – 외부 전송 시 URL 정보 최소화, 개인정보·내부경로 노출 방지
                                        <br>
                                        • <strong>Permissions-Policy</strong> – 위치·마이크·카메라 등 브라우저 기능 제한, 프라이버시 보호
                                        <br>
                                        • <strong>Strict-Transport-Security (HSTS)</strong> – HTTPS 강제, 중간자 공격·다운그레이드 방지
                                        <br><br>
                                        <strong>설정 위치:</strong> CDN(Cloudflare) · 웹서버(Nginx/Apache) · 앱(Laravel 등)
                                        <br>
                                        모든 헤더가 함께 적용될 때 가장 강력한 보안 효과를 발휘합니다.
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
                                                    <td>95-100</td>
                                                    <td>
                                                        <strong>CSP 강함</strong>(nonce/hash/strict-dynamic, unsafe-*
                                                        미사용)<br>
                                                        XFO: DENY/SAMEORIGIN 또는 frame-ancestors 제한<br>
                                                        X-Content-Type: nosniff<br>
                                                        Referrer-Policy: strict-origin-when-cross-origin 이상<br>
                                                        Permissions-Policy: 불필요 기능 차단<br>
                                                        HSTS: 6개월↑ + 서브도메인
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>85-94</td>
                                                    <td>
                                                        CSP 존재(약함 허용) <strong>또는</strong> 비-CSP 5항목 우수<br>
                                                        XFO 적용(또는 frame-ancestors 제한)<br>
                                                        X-Content-Type: nosniff<br>
                                                        Referrer-Policy: 권장 값 사용<br>
                                                        Permissions-Policy: 기본 제한 적용<br>
                                                        HSTS: 6개월↑
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>70-84</td>
                                                    <td>
                                                        CSP 없음/약함<br>
                                                        XFO 정상 적용<br>
                                                        X-Content-Type: 있음<br>
                                                        Referrer-Policy: 양호/보통<br>
                                                        Permissions-Policy: 일부 제한<br>
                                                        HSTS: 단기 또는 서브도메인 미포함
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>55-69</td>
                                                    <td>
                                                        헤더 일부만 존재<br>
                                                        CSP 없음/약함<br>
                                                        Referrer-Policy 약함<br>
                                                        X-Content-Type 누락<br>
                                                        HSTS 없음 또는 매우 짧음
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>40-54</td>
                                                    <td>
                                                        핵심 헤더 1~2개만<br>
                                                        CSP 없음<br>
                                                        Referrer 약함/없음<br>
                                                        기타 헤더 다수 누락
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0-39</td>
                                                    <td>
                                                        보안 헤더 전무에 가까움<br>
                                                        CSP/XFO/X-Content 없음<br>
                                                        Referrer-Policy 없음<br>
                                                        HSTS 없음
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-info d-block mt-3">
                                        <strong>등급 정책:</strong> A+는 강한 CSP가 필수입니다. CSP가 없어도 비-CSP 5항목(XFO, XCTO,
                                        Referrer, Permissions, HSTS)이 모두 우수하면 A를 부여합니다.
                                    </div>
                                </div>

                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $report = $currentTest->results;
                                            $metrics = $currentTest->metrics;
                                            $grade = $currentTest->overall_grade ?? 'F';
                                            $score = $currentTest->overall_score ?? 0;

                                            $gradeClass = match ($grade) {
                                                'A+' => 'bg-green-lt text-green-lt-fg',
                                                'A' => 'bg-lime-lt text-lime-lt-fg',
                                                'B' => 'bg-blue-lt text-blue-lt-fg',
                                                'C' => 'bg-yellow-lt text-yellow-lt-fg',
                                                'D' => 'bg-orange-lt text-orange-lt-fg',
                                                'F' => 'bg-red-lt text-red-lt-fg',
                                                default => 'bg-secondary',
                                            };

                                            $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);

                                            // CSP 및 HSTS 상태 분석
                                            $csp = $metrics['headers']['csp'] ?? [];
                                            $hsts = $metrics['headers']['hsts'] ?? [];

                                            $cspBadge = 'bg-azure-lt text-azure-lt-fg';
                                            $cspText = 'CSP: 없음';
                                            if ($csp['present'] ?? false) {
                                                if ($csp['strong'] ?? false) {
                                                    $cspBadge = 'bg-green-lt text-green-lt-fg';
                                                    $cspText = 'CSP: 강함';
                                                } else {
                                                    $cspBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                    $cspText = 'CSP: 약함';
                                                }
                                            }

                                            $hstsBadge = 'bg-azure-lt text-azure-lt-fg';
                                            $hstsText = 'HSTS: 없음';
                                            if ($hsts['present'] ?? false) {
                                                $six = 15552000;
                                                $has6m = ($hsts['max_age'] ?? 0) >= $six;
                                                $inc = $hsts['include_sub_domains'] ?? false;
                                                if ($has6m && $inc) {
                                                    $hstsBadge = 'bg-green-lt text-green-lt-fg';
                                                    $hstsText = 'HSTS: 6개월+, 서브도메인 포함';
                                                } elseif ($has6m) {
                                                    $hstsBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                    $hstsText = 'HSTS: 6개월+ (서브도메인 미포함)';
                                                } else {
                                                    $hstsBadge = 'bg-yellow-lt text-yellow-lt-fg';
                                                    $hstsText = 'HSTS: 기간 짧음';
                                                }
                                            }
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 헤더별 점수 상세 -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h3 class="card-title">헤더별 점수 분석</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead>
                                                            <tr>
                                                                <th>헤더</th>
                                                                <th>값</th>
                                                                <th>점수</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($metrics['breakdown'] ?? [] as $item)
                                                                <tr>
                                                                    <td><strong>{{ $item['key'] }}</strong></td>
                                                                    <td class="text-truncate"
                                                                        style="max-width: 400px;"
                                                                        title="{{ $item['value'] ?? '(설정되지 않음)' }}">
                                                                        {{ $item['value'] ?? '(설정되지 않음)' }}
                                                                    </td>
                                                                    <td>{{ round((($item['score'] ?? 0) * 100) / 60, 1) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- URL 정보 -->
                                        @if (isset($report['url']) || isset($report['finalUrl']))
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <strong>테스트 URL:</strong> {{ $report['url'] ?? '' }}
                                                            @if (isset($report['finalUrl']) && $report['finalUrl'] !== $report['url'])
                                                                <br><strong>최종 URL:</strong> {{ $report['finalUrl'] }}
                                                            @endif
                                                            @if (isset($report['status']))
                                                                <br><strong>HTTP 상태:</strong> {{ $report['status'] }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 등급 사유 -->
                                        @if (!empty($report['reasons']))
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <strong>등급 평가 사유:</strong><br>
                                                    {{ implode(' · ', $report['reasons']) }}
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 보안 헤더 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h5>💡 주요 보안 헤더 설명</h5>
                                            <p class="mb-2"><strong>Content-Security-Policy (CSP):</strong> 웹페이지에서 실행
                                                가능한 리소스의 출처를 제한합니다. XSS 공격과 데이터 주입 공격을 방어하는 가장 강력한 보안 메커니즘입니다.</p>
                                            <p class="mb-2"><strong>X-Frame-Options:</strong> 웹페이지가 iframe, frame,
                                                embed, object 태그 내에 표시되는 것을 제어합니다. 클릭재킹 공격을 방지합니다.</p>
                                            <p class="mb-2"><strong>X-Content-Type-Options:</strong> 브라우저의 MIME 타입
                                                스니핑을 방지합니다. 악의적인 스크립트가 다른 MIME 타입으로 실행되는 것을 차단합니다.</p>
                                            <p class="mb-2"><strong>Referrer-Policy:</strong> 다른 도메인으로 이동할 때 전송되는
                                                Referrer 정보를 제어합니다. 민감한 URL 정보 유출을 방지합니다.</p>
                                            <p class="mb-2"><strong>Permissions-Policy:</strong> 웹사이트에서 사용할 수 있는 브라우저
                                                기능과 API를 제한합니다. 카메라, 마이크, 위치 정보 등의 접근을 제어합니다.</p>
                                            <p class="mb-0"><strong>Strict-Transport-Security (HSTS):</strong> 브라우저가
                                                항상 HTTPS로 연결하도록 강제합니다. 중간자 공격과 프로토콜 다운그레이드 공격을 방지합니다.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 보안 헤더 분석 결과를 확인할 수 있습니다.</p>
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
