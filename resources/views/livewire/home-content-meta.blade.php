@section('title')
    <title>📋 메타데이터 검사 - SEO 메타태그 품질 및 최적화 분석 - DevTeam Test</title>
    <meta name="description"
        content="웹페이지의 Title, Description, Canonical, Open Graph, Twitter Cards 등 핵심 메타데이터를 종합적으로 분석합니다. SEO 완성도를 평가하고 A+ 등급까지 품질 인증서를 발급받으세요.">
    <meta name="keywords"
        content="메타데이터 검사, SEO 메타태그 분석, Title 최적화, Meta Description, Open Graph 태그, Twitter Cards, Canonical URL, Hreflang 설정, SEO 품질 인증, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow" />

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="📋 메타데이터 검사 - SEO 메타태그 품질 및 최적화 분석 - DevTeam Test" />
    <meta property="og:description"
        content="웹페이지의 Title, Description, OG, Twitter Cards 등 메타데이터를 분석하여 SEO 최적화 수준을 진단합니다. 개선 포인트를 제안하고 품질 인증서를 발급받으세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 메타데이터 검사 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="📋 메타데이터 검사 - SEO 메타태그 품질 및 최적화 분석" />
    <meta name="twitter:description"
        content="SEO 핵심 메타데이터 완성도를 검사하여 검색 최적화 상태를 평가합니다. Title, Description, Canonical, OG, Twitter Cards 분석 결과를 확인하세요." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '메타데이터 검사 - SEO 메타태그 품질 및 최적화 분석',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => '웹페이지의 Title, Description, Canonical, OG, Twitter Cards 등 핵심 메타데이터를 종합적으로 분석합니다. SEO 완성도를 평가하고 A+ 등급까지 품질 인증서를 발급받으세요.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="📋 메타데이터 검사" subtitle="SEO 메타태그 완성도 분석" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                    <label class="form-label">페이지 URL</label>
                                    <div class="input-group">
                                        <input type="url" wire:model="url" wire:keydown.enter="runTest"
                                            class="form-control @error('url') is-invalid @enderror"
                                            placeholder="https://www.example.com/page"
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

                    {{-- 메인 탭 --}}
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
                                    <h3>메타데이터 완성도 검사 도구</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>Meta Inspector CLI</strong>를 활용하여 웹페이지의 메타데이터 완성도를 분석합니다.
                                        <br><br>
                                        <strong>📊 측정 도구 및 방식:</strong><br>
                                        • Node.js 기반 헤드리스 브라우저 엔진으로 실제 페이지 렌더링<br>
                                        • HTML 파싱을 통한 메타태그 추출 및 분석<br>
                                        • SEO 모범 사례 기준으로 점수 산정 (100점 만점)<br><br>
                                        
                                        <strong>🎯 테스트 목적:</strong><br>
                                        • 검색엔진 최적화(SEO)를 위한 메타데이터 품질 평가<br>
                                        • 소셜 미디어 공유 시 미리보기 품질 확인<br>
                                        • 중복 콘텐츠 방지를 위한 Canonical 설정 검증<br>
                                        • 다국어 지원을 위한 Hreflang 설정 확인<br><br>
                                        
                                        <strong>📋 검사 항목:</strong><br>
                                        • <strong>Title Tag:</strong> 페이지 제목의 길이와 품질<br>
                                        • <strong>Meta Description:</strong> 페이지 설명의 길이와 품질<br>
                                        • <strong>Open Graph:</strong> Facebook, LinkedIn 등 소셜 미디어 공유 최적화<br>
                                        • <strong>Twitter Cards:</strong> Twitter 공유 시 카드 형태 최적화<br>
                                        • <strong>Canonical URL:</strong> 중복 콘텐츠 방지를 위한 대표 URL 설정<br>
                                        • <strong>Hreflang Tags:</strong> 다국어 페이지 연결 설정<br><br>
                                        
                                        <strong>DevTeam Test</strong>는 단일 페이지의 메타데이터를 심층 분석하여
                                        SEO 성과를 극대화할 수 있는 구체적인 개선 방안을 제시합니다.
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
                                                    <td>95~100</td>
                                                    <td>Title 최적 길이(50~60자), Description 최적 길이(120~160자)<br>
                                                        Open Graph 완벽 구현, Twitter Cards 완벽 구현<br>
                                                        Canonical URL 정확, 모든 메타데이터 최적화</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-lime-lt text-lime-lt-fg">A</span></td>
                                                    <td>85~94</td>
                                                    <td>Title/Description 허용 범위(30~80자/80~200자)<br>
                                                        Open Graph 완벽 구현, Canonical URL 정확 설정<br>
                                                        Twitter Cards는 선택사항</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-blue-lt text-blue-lt-fg">B</span></td>
                                                    <td>75~84</td>
                                                    <td>Title/Description 기본 작성<br>
                                                        Open Graph 기본 태그 적용<br>
                                                        일부 메타데이터 누락 허용</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-yellow-lt text-yellow-lt-fg">C</span></td>
                                                    <td>65~74</td>
                                                    <td>Title/Description 길이 부적절<br>
                                                        Open Graph 불완전 (주요 태그 누락)<br>
                                                        Canonical URL 부정확 또는 누락</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-orange-lt text-orange-lt-fg">D</span></td>
                                                    <td>50~64</td>
                                                    <td>Title/Description 심각한 길이 문제<br>
                                                        Open Graph 기본 태그 부족<br>
                                                        기본 메타데이터 부족</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-red-lt text-red-lt-fg">F</span></td>
                                                    <td>0~49</td>
                                                    <td>Title/Description 미작성<br>
                                                        Open Graph 부재<br>
                                                        메타데이터 전반 미구현</td>
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
                                            $metadata = $results['metadata'] ?? [];
                                            $analysis = $results['analysis'] ?? [];
                                            $summary = $results['summary'] ?? [];
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

                                        {{-- 종합 현황 --}}
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">종합 현황</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $summary['titleLength'] ?? 0 }}</div>
                                                            <div class="small text-muted">제목 길이</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $summary['descriptionLength'] ?? 0 }}</div>
                                                            <div class="small text-muted">설명 길이</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $summary['openGraphFields'] ?? 0 }}</div>
                                                            <div class="small text-muted">OG 태그</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $summary['twitterCardFields'] ?? 0 }}</div>
                                                            <div class="small text-muted">Twitter 태그</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">{{ $summary['hreflangCount'] ?? 0 }}</div>
                                                            <div class="small text-muted">Hreflang</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                        <div class="text-center">
                                                            <div class="h4 mb-0">
                                                                @if ($summary['hasCanonical'] ?? false)
                                                                    ✅
                                                                @else
                                                                    ❌
                                                                @endif
                                                            </div>
                                                            <div class="small text-muted">Canonical</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="small text-muted mb-2">
                                                        <strong>판정 사유:</strong> {{ $results['grade']['reason'] ?? '' }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        <strong>최종 URL:</strong> {{ $results['finalUrl'] ?? $results['url'] ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 발견된 문제점 --}}
                                        @if (!empty($results['issues']))
                                            <div class="card mb-4">
                                                <div class="card-header bg-warning-lt">
                                                    <h5 class="card-title mb-0">발견된 문제점</h5>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="mb-0">
                                                        @foreach ($results['issues'] as $issue)
                                                            <li class="mb-1">{{ $issue }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 메타데이터 미리보기 --}}
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">메타데이터 미리보기</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="fw-bold mb-1">제목 ({{ $summary['titleLength'] ?? 0 }}자)</div>
                                                    <div class="text-muted">{{ $metadata['title'] ?: '제목 없음' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold mb-1">설명 ({{ $summary['descriptionLength'] ?? 0 }}자)</div>
                                                    <div class="text-muted">{{ $metadata['description'] ?: '설명 없음' }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold mb-1">Canonical URL</div>
                                                    <div class="text-muted">{{ $metadata['canonical'] ?: 'Canonical URL 없음' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 상세 분석 --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Title & Description</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="fw-bold">Title</span>
                                                                <span class="small">
                                                                    @if ($analysis['title']['isEmpty'] ?? true)
                                                                        <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                    @elseif ($analysis['title']['isOptimal'] ?? false)
                                                                        <span class="badge bg-green-lt text-green-lt-fg">최적</span>
                                                                    @elseif ($analysis['title']['isAcceptable'] ?? false)
                                                                        <span class="badge bg-yellow-lt text-yellow-lt-fg">허용</span>
                                                                    @else
                                                                        <span class="badge bg-red-lt text-red-lt-fg">부적절</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div class="small text-muted">
                                                                길이: {{ $analysis['title']['length'] ?? 0 }}자 (허용: 30~80자, 최적: 50~60자)
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-0">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="fw-bold">Description</span>
                                                                <span class="small">
                                                                    @if ($analysis['description']['isEmpty'] ?? true)
                                                                        <span class="badge bg-red-lt text-red-lt-fg">없음</span>
                                                                    @elseif ($analysis['description']['isOptimal'] ?? false)
                                                                        <span class="badge bg-green-lt text-green-lt-fg">최적</span>
                                                                    @elseif ($analysis['description']['isAcceptable'] ?? false)
                                                                        <span class="badge bg-yellow-lt text-yellow-lt-fg">허용</span>
                                                                    @else
                                                                        <span class="badge bg-red-lt text-red-lt-fg">부적절</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div class="small text-muted">
                                                                길이: {{ $analysis['description']['length'] ?? 0 }}자 (허용: 80~200자, 최적: 120~160자)
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Open Graph</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-bold">상태</span>
                                                            <span>
                                                                @if ($analysis['openGraph']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">완벽</span>
                                                                @elseif ($analysis['openGraph']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">기본</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부족</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="small text-muted mb-2">
                                                            설정된 태그: {{ $summary['openGraphFields'] ?? 0 }}개
                                                        </div>
                                                        @if (!empty($analysis['openGraph']['missing']))
                                                            <div class="small text-danger">
                                                                누락: {{ implode(', ', $analysis['openGraph']['missing']) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Twitter Cards</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-bold">상태</span>
                                                            <span>
                                                                @if ($analysis['twitterCards']['isPerfect'] ?? false)
                                                                    <span class="badge bg-green-lt text-green-lt-fg">완벽</span>
                                                                @elseif ($analysis['twitterCards']['hasBasic'] ?? false)
                                                                    <span class="badge bg-yellow-lt text-yellow-lt-fg">기본</span>
                                                                @else
                                                                    <span class="badge bg-red-lt text-red-lt-fg">부족</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="small text-muted mb-2">
                                                            설정된 태그: {{ $summary['twitterCardFields'] ?? 0 }}개
                                                        </div>
                                                        @if (!empty($analysis['twitterCards']['missing']))
                                                            <div class="small text-danger">
                                                                누락: {{ implode(', ', $analysis['twitterCards']['missing']) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">기타 설정</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="text-center">
                                                                    <div class="mb-1">
                                                                        @if ($summary['hasCanonical'] ?? false)
                                                                            <span class="badge bg-green-lt text-green-lt-fg">✓</span>
                                                                        @else
                                                                            <span class="badge bg-red-lt text-red-lt-fg">✗</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted">Canonical</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="text-center">
                                                                    <div class="mb-1">
                                                                        @if (($summary['hreflangCount'] ?? 0) > 0)
                                                                            <span class="badge bg-green-lt text-green-lt-fg">{{ $summary['hreflangCount'] }}</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">0</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted">Hreflang</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Open Graph 상세 --}}
                                        @if (!empty($metadata['openGraph']))
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Open Graph 상세</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Property</th>
                                                                    <th>Content</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($metadata['openGraph'] as $prop => $content)
                                                                    <tr>
                                                                        <td><code>og:{{ $prop }}</code></td>
                                                                        <td class="text-break">{{ $content }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Twitter Cards 상세 --}}
                                        @if (!empty($metadata['twitterCards']))
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Twitter Cards 상세</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Content</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($metadata['twitterCards'] as $name => $content)
                                                                    <tr>
                                                                        <td><code>twitter:{{ $name }}</code></td>
                                                                        <td class="text-break">{{ $content }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Hreflang 상세 --}}
                                        @if (!empty($metadata['hreflangs']))
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Hreflang 설정</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>언어</th>
                                                                    <th>URL</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($metadata['hreflangs'] as $hreflang)
                                                                    <tr>
                                                                        <td>
                                                                            <code>{{ $hreflang['lang'] }}</code>
                                                                            @if ($hreflang['lang'] === 'x-default')
                                                                                <span class="badge bg-primary-lt ms-1">default</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-break">{{ $hreflang['href'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 개선 제안 --}}
                                        @if (!empty($improvementSuggestions))
                                            <div class="alert alert-info d-block">
                                                <h5>💡 개선 제안</h5>
                                                <ul class="mb-0">
                                                    @foreach ($improvementSuggestions as $suggestion)
                                                        <li>{{ $suggestion }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- 메타데이터 설명 --}}
                                        <div class="alert alert-info d-block">
                                            <h5>📚 메타데이터 지표 설명</h5>
                                            <p class="mb-2"><strong>Title Tag:</strong> 검색 결과와 브라우저 탭에 표시되는 페이지 제목. 50-60자가 최적이며, 핵심 키워드를 포함해야 합니다.</p>
                                            <p class="mb-2"><strong>Meta Description:</strong> 검색 결과에 표시되는 페이지 설명. 120-160자가 최적이며, 사용자의 클릭을 유도하는 내용이어야 합니다.</p>
                                            <p class="mb-2"><strong>Open Graph:</strong> Facebook, LinkedIn 등 소셜 미디어에서 링크 공유 시 표시되는 정보. title, description, image, url은 필수입니다.</p>
                                            <p class="mb-2"><strong>Twitter Cards:</strong> Twitter에서 링크 공유 시 표시되는 카드 형태의 정보. card, title, description이 기본입니다.</p>
                                            <p class="mb-2"><strong>Canonical URL:</strong> 중복 콘텐츠 문제를 방지하기 위한 대표 URL 지정. 동일한 콘텐츠가 여러 URL에 존재할 때 필수입니다.</p>
                                            <p class="mb-0"><strong>Hreflang Tags:</strong> 다국어 페이지 연결 설정. 같은 콘텐츠의 다른 언어 버전을 검색엔진에 알려줍니다.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 메타데이터 분석 결과를 확인할 수 있습니다.</p>
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