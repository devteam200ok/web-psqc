@section('title')
    <title>🔍 종합 품질 테스트 - Lighthouse 성능 · SEO · 접근성 통합 분석 - DevTeam Test</title>
    <meta name="description"
        content="Google Lighthouse 기반으로 Performance, Accessibility, Best Practices, SEO 4대 품질 지표를 통합 분석합니다. Core Web Vitals(FCP, LCP, CLS)까지 반영해 웹사이트의 전반적인 품질과 사용자 경험을 평가하고, A+부터 F 등급까지 인증서를 발급받을 수 있습니다.">
    <meta name="keywords"
        content="Lighthouse 종합 테스트, 웹사이트 품질 진단, 성능 최적화, SEO 검사, 접근성 평가, Best Practices, Core Web Vitals, FCP, LCP, CLS, 웹 표준, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="🔍 종합 품질 테스트 - Lighthouse 성능 · SEO · 접근성 통합 분석 - DevTeam Test" />
    <meta property="og:description"
        content="Google Lighthouse를 활용한 웹사이트 종합 품질 분석. 성능, 접근성, SEO, Best Practices 4대 영역을 통합 점검하고 A+ 등급까지 인증서를 발급받을 수 있습니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test Lighthouse 종합 품질 테스트 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="🔍 종합 품질 테스트 - Lighthouse 성능 · SEO · 접근성 통합 분석" />
    <meta name="twitter:description"
        content="Google Lighthouse 기반 웹사이트 종합 품질 테스트. Performance, Accessibility, SEO, Best Practices를 통합 평가하고 Core Web Vitals까지 반영된 인증서를 발급받을 수 있습니다." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '종합 품질 테스트 - Lighthouse 성능 · SEO · 접근성 통합 분석',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => 'Google Lighthouse 기반으로 웹사이트의 성능, 접근성, SEO, Best Practices를 통합 측정하여 웹 품질 인증서를 발급합니다. Core Web Vitals(FCP, LCP, CLS) 포함.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔍 종합 품질 테스트" subtitle="Lighthouse 성능+SEO+접근성 통합 분석" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                    <h3>Google Lighthouse - 웹사이트 종합 품질 측정 도구</h3>
                                    <div class="text-muted small mt-1">
                                        Google Lighthouse는 구글이 개발한 오픈소스 웹 품질 측정 도구로, Chrome DevTools에 내장되어 있으며
                                        웹사이트의 성능, 접근성, SEO, 모범 사례 준수 여부를 종합적으로 분석합니다.
                                        <br><br>
                                        <strong>측정 도구 및 환경</strong><br>
                                        • Lighthouse 최신 버전 (Chrome 브라우저 엔진 기반)<br>
                                        • Headless Chrome으로 실제 브라우저 환경 시뮬레이션<br>
                                        • 모바일 3G/4G 네트워크 및 중급 성능 디바이스 기준 측정<br>
                                        • 실제 사용자 경험을 반영한 Core Web Vitals 측정
                                        <br><br>
                                        <strong>테스트 목적</strong><br>
                                        • 웹사이트의 전반적인 품질 수준 파악<br>
                                        • 사용자 경험에 영향을 미치는 성능 병목 지점 발견<br>
                                        • 검색엔진 최적화(SEO) 준수 사항 점검<br>
                                        • 장애인 접근성 표준(WCAG) 준수 여부 확인<br>
                                        • 웹 표준 및 보안 모범 사례 적용 상태 평가
                                        <br><br>
                                        <strong>4대 평가 영역</strong><br>
                                        1. <strong>Performance (성능)</strong>: 페이지 로딩 속도, Core Web Vitals, 리소스 최적화<br>
                                        2. <strong>Accessibility (접근성)</strong>: ARIA 레이블, 색상 대비, 키보드 탐색 지원<br>
                                        3. <strong>Best Practices (모범 사례)</strong>: HTTPS 사용, 콘솔 오류, 이미지 비율<br>
                                        4. <strong>SEO (검색 최적화)</strong>: 메타 태그, 구조화된 데이터, 모바일 친화성
                                        <br><br>
                                        테스트는 약 <strong>30초~2분</strong> 정도 소요되며, 네트워크 상태와 웹사이트 복잡도에 따라 달라질 수 있습니다.
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
                                                    <td>95~100</td>
                                                    <td>Performance: 90점+<br>Accessibility: 90점+<br>Best Practices: 90점+<br>SEO: 90점+<br>전체 평균: 95점+</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>90~94</td>
                                                    <td>Performance: 85점+<br>Accessibility: 85점+<br>Best Practices: 85점+<br>SEO: 85점+<br>전체 평균: 90점+</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>80~89</td>
                                                    <td>Performance: 75점+<br>Accessibility: 75점+<br>Best Practices: 75점+<br>SEO: 75점+<br>전체 평균: 80점+</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>70~79</td>
                                                    <td>Performance: 65점+<br>Accessibility: 65점+<br>Best Practices: 65점+<br>SEO: 65점+<br>전체 평균: 70점+</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>60~69</td>
                                                    <td>Performance: 55점+<br>Accessibility: 55점+<br>Best Practices: 55점+<br>SEO: 55점+<br>전체 평균: 60점+</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~59</td>
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
                                            $results = $currentTest->results;
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
                                            $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 4대 영역 점수 -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3>{{ $metrics['performance_score'] ?? 'N/A' }}</h3>
                                                        <p>Performance</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3>{{ $metrics['accessibility_score'] ?? 'N/A' }}</h3>
                                                        <p>Accessibility</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3>{{ $metrics['best_practices_score'] ?? 'N/A' }}</h3>
                                                        <p>Best Practices</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card text-center">
                                                    <div class="card-body">
                                                        <h3>{{ $metrics['seo_score'] ?? 'N/A' }}</h3>
                                                        <p>SEO</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Core Web Vitals -->
                                        @if(isset($results['audits']))
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Core Web Vitals</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            @if(isset($results['audits']['first-contentful-paint']))
                                                                <tr>
                                                                    <th>First Contentful Paint (FCP)</th>
                                                                    <td>{{ $results['audits']['first-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['largest-contentful-paint']))
                                                                <tr>
                                                                    <th>Largest Contentful Paint (LCP)</th>
                                                                    <td>{{ $results['audits']['largest-contentful-paint']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['cumulative-layout-shift']))
                                                                <tr>
                                                                    <th>Cumulative Layout Shift (CLS)</th>
                                                                    <td>{{ $results['audits']['cumulative-layout-shift']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['speed-index']))
                                                                <tr>
                                                                    <th>Speed Index</th>
                                                                    <td>{{ $results['audits']['speed-index']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['total-blocking-time']))
                                                                <tr>
                                                                    <th>Total Blocking Time (TBT)</th>
                                                                    <td>{{ $results['audits']['total-blocking-time']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                            @if(isset($results['audits']['interactive']))
                                                                <tr>
                                                                    <th>Time to Interactive (TTI)</th>
                                                                    <td>{{ $results['audits']['interactive']['displayValue'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 개선 기회 -->
                                            @php
                                                $opportunities = collect($results['audits'])->filter(function($audit) {
                                                    return isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 0;
                                                })->sortByDesc('details.overallSavingsMs');
                                            @endphp
                                            @if($opportunities->count() > 0)
                                                <div class="card mb-4">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">개선 기회</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                @foreach($opportunities->take(10) as $key => $opportunity)
                                                                    <tr>
                                                                        <td>{{ $opportunity['title'] ?? $key }}</td>
                                                                        <td>{{ $opportunity['displayValue'] ?? '' }}</td>
                                                                        <td class="text-end">{{ round($opportunity['details']['overallSavingsMs'] ?? 0) }}ms 개선 가능</td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- 진단 결과 -->
                                            @php
                                                $diagnostics = collect($results['audits'])->filter(function($audit) {
                                                    return isset($audit['details']['type']) && $audit['details']['type'] === 'table' && isset($audit['score']) && $audit['score'] < 1;
                                                });
                                            @endphp
                                            @if($diagnostics->count() > 0)
                                                <div class="card mb-4">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">진단 결과</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                @foreach($diagnostics->take(10) as $key => $diagnostic)
                                                                    <tr>
                                                                        <td>{{ $diagnostic['title'] ?? $key }}</td>
                                                                        <td>{{ $diagnostic['displayValue'] ?? $diagnostic['description'] ?? '' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h5>Core Web Vitals 지표 설명</h5>
                                            <p class="mb-2"><strong>FCP (First Contentful Paint):</strong> 페이지 로드가 시작된 시점부터 콘텐츠의 일부가 화면에 처음 렌더링되는 시점까지의 시간</p>
                                            <p class="mb-2"><strong>LCP (Largest Contentful Paint):</strong> 뷰포트에서 가장 큰 콘텐츠 요소가 화면에 렌더링되는 시점. 2.5초 이내가 권장됨</p>
                                            <p class="mb-2"><strong>CLS (Cumulative Layout Shift):</strong> 페이지 로드 중 발생하는 예상치 못한 레이아웃 이동의 누적 점수. 0.1 이하가 권장됨</p>
                                            <p class="mb-2"><strong>TBT (Total Blocking Time):</strong> FCP와 TTI 사이에 메인 스레드가 차단된 총 시간. 200ms 이내가 권장됨</p>
                                            <p class="mb-0"><strong>TTI (Time to Interactive):</strong> 페이지가 완전히 상호작용 가능하게 되는 시점. 3.8초 이내가 권장됨</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h5>성능 개선 방안</h5>
                                            <p class="mb-2">📌 <strong>이미지 최적화:</strong> WebP 포맷 사용, 적절한 크기로 리사이징, lazy loading 적용</p>
                                            <p class="mb-2">📌 <strong>JavaScript 최적화:</strong> 불필요한 스크립트 제거, 코드 스플리팅, 비동기 로드 적용</p>
                                            <p class="mb-2">📌 <strong>CSS 최적화:</strong> 사용하지 않는 CSS 제거, Critical CSS 인라인화, 파일 압축</p>
                                            <p class="mb-2">📌 <strong>캐싱 전략:</strong> 브라우저 캐싱 헤더 설정, CDN 활용, Service Worker 구현</p>
                                            <p class="mb-2">📌 <strong>서버 응답 개선:</strong> TTFB 최적화, Gzip/Brotli 압축, HTTP/2 활용</p>
                                            <p class="mb-0">📌 <strong>렌더링 최적화:</strong> 렌더 블로킹 리소스 제거, 폰트 최적화, Critical Path 최소화</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 종합 품질 분석 결과를 확인할 수 있습니다.</p>
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
                                        <pre class="json-dump" id="json-data">{{ $currentTest->raw_json_pretty ?? '미리보기를 생성할 수 없습니다.' }}</pre>
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