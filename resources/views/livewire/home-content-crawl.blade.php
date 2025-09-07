@section('title')
    <title>🕷️ 사이트 크롤링 검사 - robots.txt · sitemap.xml SEO 기술진단 - DevTeam Test</title>
    <meta name="description"
        content="robots.txt와 sitemap.xml 설정을 점검하여 검색엔진 크롤링 최적화 여부를 분석합니다. 전체 페이지 접근성, 중복 콘텐츠, SEO 기술 요소를 종합 평가하여 사이트 검색 노출 품질을 개선할 수 있습니다.">
    <meta name="keywords"
        content="사이트 크롤링 검사, robots.txt 분석, sitemap.xml 검증, SEO 기술 진단, 크롤링 최적화, 중복 콘텐츠 분석, 페이지 품질 평가, 검색엔진 최적화, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="🕷️ 사이트 크롤링 검사 - robots.txt · sitemap.xml SEO 기술진단 - DevTeam Test" />
    <meta property="og:description"
        content="robots.txt와 sitemap.xml 파일을 분석하여 검색엔진 크롤링 준수 여부를 검증하고, 전체 페이지 품질과 SEO 최적화 상태를 종합 진단합니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 사이트 크롤링 검사 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="🕷️ 사이트 크롤링 검사 - robots.txt · sitemap.xml SEO 기술진단" />
    <meta name="twitter:description"
        content="robots.txt와 sitemap.xml 설정을 검증하여 사이트 크롤링 최적화 여부와 SEO 품질을 평가합니다. DevTeam Test로 A+ 등급까지 인증서를 발급받으세요." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '사이트 크롤링 검사 - robots.txt · sitemap.xml SEO 기술진단',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => 'robots.txt와 sitemap.xml 설정을 점검하여 크롤링 최적화 여부와 SEO 품질을 분석하고 개선 방안을 제공합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🕷️ 사이트 크롤링 검사" subtitle="robots.txt/sitemap.xml 기반 SEO 기술검사" :user-plan-usage="$userPlanUsage"
        :ip-usage="$ipUsage ?? null" :ip-address="$ipAddress ?? null" />

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')
            <div class="row">
                <div class="col-xl-8 d-block mb-2">
                    {{-- URL 폼 (개별 컴포넌트) --}}
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
                                                검사
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
                                    <h3>검색엔진 크롤링 준수 및 페이지 품질 종합 분석</h3>
                                    <div class="text-muted small mt-1">
                                        웹사이트의 robots.txt와 sitemap.xml을 분석하여 SEO 준수 여부를 검증하고,
                                        sitemap에 등록된 페이지들의 접근성과 품질을 종합적으로 평가합니다.
                                        <br><br>
                                        <strong>📋 검사 프로세스:</strong><br>
                                        1. robots.txt 파일 존재 여부 및 규칙 확인<br>
                                        2. sitemap.xml 파일 검색 및 URL 수집<br>
                                        3. robots.txt 규칙에 따른 크롤링 허용 URL 필터링<br>
                                        4. 최대 50개 페이지 샘플링 및 순차 검사<br>
                                        5. 각 페이지의 HTTP 상태, 메타데이터, 품질 점수 측정<br>
                                        6. 중복 콘텐츠(title/description) 비율 분석<br><br>

                                        <strong>🎯 측정 도구:</strong><br>
                                        • Node.js 기반 자체 크롤러 (robots.txt 준수)<br>
                                        • sitemap.xml 파서 (index 파일 재귀 처리 지원)<br>
                                        • HTML 파서를 통한 메타데이터 추출<br>
                                        • 품질 점수 알고리즘 (100점 만점)<br><br>

                                        <strong>💯 품질 점수 산정 기준:</strong><br>
                                        • Title 태그 길이 (5자 미만: -15점)<br>
                                        • Description 메타 태그 (20자 미만: -10점)<br>
                                        • Canonical URL 누락 (-5점)<br>
                                        • H1 태그 부재 (-10점) / 과다 사용 (-5점)<br>
                                        • 콘텐츠 부족 (1000자 미만: -10점)<br><br>

                                        <strong>🚀 테스트 목적:</strong><br>
                                        • 검색엔진이 사이트를 올바르게 크롤링할 수 있는지 확인<br>
                                        • sitemap에 등록된 모든 페이지가 정상 접근 가능한지 검증<br>
                                        • 중복 콘텐츠로 인한 SEO 패널티 위험 진단<br>
                                        • 페이지별 품질 점수를 통한 개선점 도출<br><br>

                                        이 검사는 약 <strong>30초~2분</strong> 정도 소요됩니다.
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
                                                    <td>robots.txt 정상 적용<br>
                                                        sitemap.xml 존재 및 누락/404 없음<br>
                                                        검사 대상 페이지 전부 2xx<br>
                                                        전체 페이지 품질 평균 ≥ 85점<br>
                                                        중복 콘텐츠 ≤ 30%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>80~89</td>
                                                    <td>robots.txt 정상 적용<br>
                                                        sitemap.xml 존재 및 정합성 확보<br>
                                                        검사 대상 페이지 전부 2xx<br>
                                                        전체 페이지 품질 평균 ≥ 85점</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>70~79</td>
                                                    <td>robots.txt 및 sitemap.xml 존재<br>
                                                        검사 대상 페이지 전부 2xx<br>
                                                        전체 페이지 품질 평균 무관</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>55~69</td>
                                                    <td>robots.txt 및 sitemap.xml 존재<br>
                                                        검사 리스트 일부 4xx/5xx 오류 포함</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>35~54</td>
                                                    <td>robots.txt 및 sitemap.xml 존재<br>
                                                        검사 대상 URL 생성 가능<br>
                                                        단, 정상 접근률 낮거나 품질 점검 불가</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~34</td>
                                                    <td>robots.txt 부재 또는 sitemap.xml 부재<br>
                                                        검사 리스트 자체 생성 불가</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane {{ $mainTabActive == 'results' ? 'active show' : '' }}"
                                    id="tabs-results">
                                    @if ($currentTest && $currentTest->status === 'completed' && $currentTest->results)
                                        @php
                                            $report = $currentTest->results;
                                            $grade = $report['overall']['grade'] ?? 'F';
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

                                            $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                        @endphp
                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 종합 현황 -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title mb-3">종합 현황</h5>
                                                        <div class="row g-3">
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div class="h4 mb-0">{{ $pages['count'] ?? 0 }}
                                                                    </div>
                                                                    <div class="small text-muted">검사 페이지</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div class="h4 mb-0">
                                                                        {{ number_format($pages['qualityAvg'] ?? 0, 1) }}
                                                                    </div>
                                                                    <div class="small text-muted">평균 품질점수</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="h4 mb-0 {{ ($pages['errorRate4xx5xx'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                                        {{ number_format($pages['errorRate4xx5xx'] ?? 0, 1) }}%
                                                                    </div>
                                                                    <div class="small text-muted">오류율</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-lg-3">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="h4 mb-0 {{ ($pages['duplicateRate'] ?? 0) > 30 ? 'text-warning' : '' }}">
                                                                        {{ number_format($pages['duplicateRate'] ?? 0, 1) }}%
                                                                    </div>
                                                                    <div class="small text-muted">중복률</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>판정 사유:</strong>
                                                            {{ $report['overall']['reason'] ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- robots.txt & sitemap.xml -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">robots.txt</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>상태:</span>
                                                            <span
                                                                class="{{ $robots['exists'] ?? false ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                {{ $robots['exists'] ?? false ? '존재' : '없음' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>HTTP 상태:</span>
                                                            <span>{{ $robots['status'] ?? 0 }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">sitemap.xml</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>상태:</span>
                                                            <span
                                                                class="{{ $sitemap['hasSitemap'] ?? false ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                {{ $sitemap['hasSitemap'] ?? false ? '존재' : '없음' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>총 URL 수:</span>
                                                            <span>{{ $sitemap['sitemapUrlCount'] ?? 0 }}개</span>
                                                        </div>

                                                        @if (!empty($sitemap['sitemaps']))
                                                            <div class="mt-3">
                                                                <div class="small text-muted mb-2">sitemap 파일들:</div>
                                                                @foreach ($sitemap['sitemaps'] as $s)
                                                                    <div class="small d-flex justify-content-between">
                                                                        <span class="text-truncate me-2"
                                                                            style="max-width:70%">
                                                                            {{ basename($s['url']) }}
                                                                        </span>
                                                                        <span
                                                                            class="{{ $s['ok'] ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                                            {{ $s['ok'] ? 'OK' : 'NG' }}
                                                                            ({{ $s['count'] ?? 0 }})
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 크롤링 계획 -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">크롤링 계획</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="small text-muted mb-2">검사 대상 URL (총
                                                            {{ $crawlPlan['candidateCount'] ?? 0 }}개)</div>
                                                        @if (!empty($crawlPlan['sample']))
                                                            <div
                                                                style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem; background: #f8f9fa;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($crawlPlan['sample'] as $url)
                                                                        <li class="text-break mb-1">•
                                                                            {{ $url }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="small text-muted mb-2">제외된 URL
                                                            ({{ count($crawlPlan['skipped'] ?? []) }}개)</div>
                                                        @if (!empty($crawlPlan['skipped']))
                                                            <div
                                                                style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem; background: #f8f9fa;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach (array_slice($crawlPlan['skipped'], 0, 10) as $skip)
                                                                        <li class="mb-2">
                                                                            <div class="fw-bold text-danger">
                                                                                {{ $skip['reason'] }}</div>
                                                                            <div class="text-break small">
                                                                                {{ $skip['url'] }}</div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <div class="text-muted small">제외된 URL 없음 ✓</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 문제점 샘플 -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">오류 페이지 (4xx/5xx)</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @php $errorPages = $report['samples']['errorPages'] ?? []; @endphp
                                                        @if (empty($errorPages))
                                                            <div class="text-muted">오류 페이지 없음 ✓</div>
                                                        @else
                                                            <div style="max-height: 200px; overflow-y: auto;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($errorPages as $page)
                                                                        <li class="mb-2">
                                                                            <span
                                                                                class="badge bg-red-lt text-red-lt-fg me-1">{{ $page['status'] }}</span>
                                                                            <span
                                                                                class="text-break">{{ $page['url'] }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">낮은 품질 페이지 (50점 미만)</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @php
                                                            $lowQuality = collect(
                                                                $report['samples']['lowQuality'] ?? [],
                                                            )
                                                                ->filter(function ($page) {
                                                                    return ($page['score'] ?? 100) < 50;
                                                                })
                                                                ->take(10)
                                                                ->values()
                                                                ->toArray();
                                                        @endphp
                                                        @if (empty($lowQuality))
                                                            <div class="text-muted">50점 미만 페이지 없음 ✓</div>
                                                        @else
                                                            <div style="max-height: 200px; overflow-y: auto;">
                                                                <ul class="small mb-0 list-unstyled">
                                                                    @foreach ($lowQuality as $page)
                                                                        <li class="mb-2">
                                                                            <span
                                                                                class="badge bg-orange-lt text-orange-lt-fg me-1">{{ $page['score'] ?? 0 }}점</span>
                                                                            <span
                                                                                class="text-break">{{ $page['url'] }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 중복 콘텐츠 정보 -->
                                        @if (($pages['dupTitleCount'] ?? 0) > 0 || ($pages['dupDescCount'] ?? 0) > 0)
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">중복 콘텐츠</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0 text-warning">
                                                                    {{ $pages['dupTitleCount'] ?? 0 }}</div>
                                                                <div class="small text-muted">중복 제목 페이지</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="text-center">
                                                                <div class="h4 mb-0 text-warning">
                                                                    {{ $pages['dupDescCount'] ?? 0 }}</div>
                                                                <div class="small text-muted">중복 설명 페이지</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>📊 측정 지표 설명</h6>
                                            <p class="mb-2"><strong>검사 페이지:</strong> sitemap.xml에서 수집되고 robots.txt에서
                                                허용된 URL 중 실제로 검사한 페이지 수</p>
                                            <p class="mb-2"><strong>평균 품질점수:</strong> 각 페이지의 SEO 품질 요소(title,
                                                description, canonical, H1, 콘텐츠량)를 종합 평가한 점수의 평균</p>
                                            <p class="mb-2"><strong>오류율:</strong> 검사한 페이지 중 4xx, 5xx 에러가 발생한 페이지의 비율
                                            </p>
                                            <p class="mb-0"><strong>중복률:</strong> 동일한 title 또는 description을 사용하는
                                                페이지들의 비율</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 개선 방안</h6>
                                            @if (!($robots['exists'] ?? false))
                                                <p class="mb-2">⚠️ <strong>robots.txt 파일 생성:</strong> 루트 디렉토리에
                                                    robots.txt 파일을 생성하여 크롤링 규칙을 명시하세요.</p>
                                            @endif
                                            @if (!($sitemap['hasSitemap'] ?? false))
                                                <p class="mb-2">⚠️ <strong>sitemap.xml 파일 생성:</strong> 사이트의 모든 중요
                                                    페이지를 포함한 sitemap.xml을 생성하세요.</p>
                                            @endif
                                            @if (($pages['errorRate4xx5xx'] ?? 0) > 0)
                                                <p class="mb-2">⚠️ <strong>오류 페이지 수정:</strong> 404, 500 에러가 발생하는 페이지를
                                                    수정하거나 sitemap에서 제거하세요.</p>
                                            @endif
                                            @if (($pages['duplicateRate'] ?? 0) > 30)
                                                <p class="mb-2">⚠️ <strong>중복 콘텐츠 개선:</strong> 각 페이지마다 고유한 title과
                                                    description을 작성하여 SEO 효과를 높이세요.</p>
                                            @endif
                                            @if (($pages['qualityAvg'] ?? 0) < 70)
                                                <p class="mb-2">⚠️ <strong>페이지 품질 개선:</strong> 메타 태그 최적화, H1 태그 사용,
                                                    충분한 콘텐츠 작성으로 품질 점수를 향상시키세요.</p>
                                            @endif
                                            @if ($grade === 'A+')
                                                <p class="mb-0">✅ <strong>최적화 완료:</strong> 현재 크롤링 최적화 상태가 매우 우수합니다.
                                                    지속적인 모니터링으로 품질을 유지하세요.</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 크롤링 검사 결과를 확인할 수 있습니다.</p>
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
