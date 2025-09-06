@section('title')
    <title>🔗 링크 검증 테스트 - 깨진 링크 · 리다이렉트 체인 · 앵커 유효성 분석 - DevTeam Test</title>
    <meta name="description"
        content="웹사이트의 모든 내부·외부·이미지 링크를 크롤링하여 깨진 링크와 오류를 탐지합니다. 404/500 상태 코드, 리다이렉트 체인, 앵커 유효성을 분석해 웹 품질과 사용자 경험을 평가합니다.">
    <meta name="keywords"
        content="링크 검증, Broken Link Checker, 깨진 링크 탐지, 404 오류 검사, 앵커 링크 유효성, 리다이렉트 체인 분석, HTTP 상태 코드 점검, 웹사이트 품질 평가, DevTeam Test">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph -->
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="🔗 링크 검증 테스트 - 깨진 링크 · 리다이렉트 체인 · 앵커 유효성 분석 - DevTeam Test" />
    <meta property="og:description"
        content="내부/외부/이미지 링크 상태를 점검하여 깨진 링크를 찾고, 리다이렉트 체인과 앵커 유효성을 분석해 사이트 품질을 평가합니다. 오류율 기반 등급과 A+ 인증서 발급 지원." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 링크 검증 결과" />
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="🔗 링크 검증 테스트 - 깨진 링크 · 리다이렉트 체인 · 앵커 유효성 분석" />
    <meta name="twitter:description"
        content="모든 링크 상태를 검사하여 깨진 링크와 오류를 탐지하고, 리다이렉트 체인과 앵커 유효성을 분석해 웹사이트 품질을 평가합니다. DevTeam Test로 A+ 인증서를 발급받으세요." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '링크 검증 테스트 - 깨진 링크 · 리다이렉트 체인 · 앵커 유효성 분석',
    'url' => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url' => url('/'),
    ],
    'description' => '웹사이트의 모든 내부·외부·이미지 링크를 크롤링하여 깨진 링크와 오류를 탐지합니다. 404/500 상태 코드, 리다이렉트 체인, 앵커 유효성을 분석해 웹 품질과 사용자 경험을 평가합니다.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
    @include('components.test-shared.css')
@endsection

<div class="page-wrapper">
    {{-- 헤더 (공통 컴포넌트) --}}
    <x-test-shared.header title="🔗 링크 검증" subtitle="내부/외부/이미지 링크 + 앵커 상태 검사" :user-plan-usage="$userPlanUsage" :ip-usage="$ipUsage ?? null"
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
                                    <h3>Playwright 기반 링크 검증 도구</h3>
                                    <div class="text-muted small mt-1">
                                        <strong>측정 도구:</strong> Playwright + Node.js 기반 커스텀 크롤러<br>
                                        <strong>테스트 목적:</strong> 웹사이트의 모든 링크 상태를 검사하여 사용자 경험을 해치는 깨진 링크, 잘못된 리다이렉트, 존재하지 않는 앵커 등을 찾아냅니다.
                                        <br><br>
                                        <strong>검사 항목:</strong><br>
                                        • 내부 링크: 동일 도메인 내 모든 페이지 링크의 HTTP 상태<br>
                                        • 외부 링크: 외부 도메인으로 연결되는 링크의 유효성<br>
                                        • 이미지 링크: img 태그의 src 속성에 있는 이미지 리소스 상태<br>
                                        • 앵커 링크: 동일 페이지 내 #id 형태의 앵커 존재 여부<br>
                                        • 리다이렉트 체인: 각 링크의 리다이렉트 단계 수와 최종 도착지<br>
                                        <br>
                                        <strong>DevTeam Test</strong>는 Playwright로 실제 브라우저를 구동하여 JavaScript로 생성되는 동적 콘텐츠의 링크까지 
                                        완벽하게 검사합니다. OAuth/SSO 관련 리다이렉트는 정상으로 간주하여 등급 산정에서 제외합니다.
                                        <br><br>
                                        테스트는 약 <strong>30초~4분</strong> 정도 소요되며, 페이지의 링크 수에 따라 달라집니다.
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
                                                    <td>• 내부/외부/이미지 링크 오류율: 0%<br>
                                                        • 리다이렉트 체인 ≤1단계<br>
                                                        • 앵커 링크 100% 정상</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-a">A</span></td>
                                                    <td>80~89</td>
                                                    <td>• 전체 오류율 ≤1%<br>
                                                        • 리다이렉트 체인 ≤2단계<br>
                                                        • 앵커 링크 대부분 정상</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-b">B</span></td>
                                                    <td>70~79</td>
                                                    <td>• 전체 오류율 ≤3%<br>
                                                        • 리다이렉트 체인 ≤3단계<br>
                                                        • 일부 앵커 링크 불량</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-c">C</span></td>
                                                    <td>60~69</td>
                                                    <td>• 전체 오류율 ≤5%<br>
                                                        • 다수 링크 경고 (타임아웃/SSL 문제)<br>
                                                        • 앵커 링크 오류 빈번</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-d">D</span></td>
                                                    <td>50~59</td>
                                                    <td>• 전체 오류율 ≤10%<br>
                                                        • 리다이렉트 루프 또는 긴 체인<br>
                                                        • 이미지 링크 다수 깨짐</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge badge-f">F</span></td>
                                                    <td>0~49</td>
                                                    <td>• 전체 오류율 10% 이상<br>
                                                        • 주요 내부 링크 다수 깨짐<br>
                                                        • 앵커/이미지 전반 불량</td>
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
                                            $totals = $results['totals'] ?? [];
                                            $rates = $results['rates'] ?? [];
                                            $overall = $results['overall'] ?? [];
                                            $samples = $results['samples'] ?? [];
                                            
                                            $grade = $currentTest->overall_grade ?? 'F';
                                            $canIssueCertificate = in_array($grade, ['A+', 'A', 'B']);
                                        @endphp

                                        <x-test-shared.certificate :current-test="$currentTest" />

                                        <!-- 종합 결과 -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="mb-3">종합 결과</h5>
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">전체 오류율</div>
                                                                <div class="h3 {{ $this->getErrorRateBadgeClass($rates['overallErrorRate'] ?? 0) }}">
                                                                    {{ $rates['overallErrorRate'] ?? 0 }}%
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">최대 리다이렉트 체인</div>
                                                                <div class="h3">
                                                                    {{ $totals['maxRedirectChainEffective'] ?? 0 }}단계
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="text-muted small">검사한 링크 수</div>
                                                                <div class="h3">
                                                                    {{ $totals['httpChecked'] ?? 0 }}개
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 text-muted small">
                                                            평가 사유: {{ $overall['reason'] ?? '' }}
                                                        </div>
                                                        @if (!empty($totals['navError']))
                                                            <div class="mt-2 text-danger small">
                                                                네비게이션 오류: {{ $totals['navError'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 카테고리별 상세 결과 -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <h5 class="mb-3">카테고리별 상세</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-vcenter">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>카테고리</th>
                                                                <th>검사 수</th>
                                                                <th>오류 수</th>
                                                                <th>오류율</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>내부 링크</strong></td>
                                                                <td>{{ $totals['internalChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['internalErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['internalErrorRate'] ?? 0) }}">
                                                                        {{ $rates['internalErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>외부 링크</strong></td>
                                                                <td>{{ $totals['externalChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['externalErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['externalErrorRate'] ?? 0) }}">
                                                                        {{ $rates['externalErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>이미지 링크</strong></td>
                                                                <td>{{ $totals['imageChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['imageErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['imageErrorRate'] ?? 0) }}">
                                                                        {{ $rates['imageErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>앵커 링크</strong></td>
                                                                <td>{{ $totals['anchorChecked'] ?? 0 }}</td>
                                                                <td>{{ $totals['anchorErrors'] ?? 0 }}</td>
                                                                <td>
                                                                    <span class="{{ $this->getErrorRateBadgeClass($rates['anchorErrorRate'] ?? 0) }}">
                                                                        {{ $rates['anchorErrorRate'] ?? 0 }}%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 오류 샘플 -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">링크 오류 샘플</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $linkSamples = $samples['links'] ?? []; @endphp
                                                        @if (empty($linkSamples))
                                                            <div class="text-muted">오류 없음</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($linkSamples, 0, 10) as $sample)
                                                                    <li class="mb-2">
                                                                        <div class="text-truncate" style="max-width: 100%;">
                                                                            <code>{{ $sample['url'] ?? '' }}</code>
                                                                        </div>
                                                                        <div class="text-muted">
                                                                            상태: {{ $sample['status'] ?? 0 }} • 
                                                                            체인: {{ $sample['chain'] ?? 0 }} • 
                                                                            {{ $sample['error'] ?? '' }}
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">이미지 오류 샘플</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $imgSamples = $samples['images'] ?? []; @endphp
                                                        @if (empty($imgSamples))
                                                            <div class="text-muted">오류 없음</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($imgSamples, 0, 10) as $sample)
                                                                    <li class="mb-2">
                                                                        <div class="text-truncate" style="max-width: 100%;">
                                                                            <code>{{ $sample['url'] ?? '' }}</code>
                                                                        </div>
                                                                        <div class="text-muted">
                                                                            상태: {{ $sample['status'] ?? 0 }} • 
                                                                            체인: {{ $sample['chain'] ?? 0 }} • 
                                                                            {{ $sample['error'] ?? '' }}
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">앵커 오류 샘플 (동일 페이지 #id)</h5>
                                                    </div>
                                                    <div class="card-body small">
                                                        @php $anchorSamples = $samples['anchors'] ?? []; @endphp
                                                        @if (empty($anchorSamples))
                                                            <div class="text-muted">오류 없음</div>
                                                        @else
                                                            <ul class="mb-0">
                                                                @foreach (array_slice($anchorSamples, 0, 10) as $sample)
                                                                    <li>
                                                                        <code>{{ $sample['href'] ?? '' }}</code>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 측정 지표 설명 -->
                                        <div class="alert alert-info d-block">
                                            <h6>📊 측정 지표 설명</h6>
                                            <p class="mb-2"><strong>오류율:</strong> (오류 링크 수 ÷ 전체 링크 수) × 100으로 계산된 백분율입니다.</p>
                                            <p class="mb-2"><strong>리다이렉트 체인:</strong> 최종 목적지에 도달하기까지 거치는 리다이렉트 횟수입니다. 짧을수록 좋습니다.</p>
                                            <p class="mb-2"><strong>HTTP 상태 코드:</strong> 200번대(정상), 300번대(리다이렉트), 400번대(클라이언트 오류), 500번대(서버 오류)</p>
                                            <p class="mb-0"><strong>앵커 링크:</strong> 페이지 내 특정 위치로 이동하는 #id 형태의 링크입니다.</p>
                                        </div>

                                        <!-- 개선 방안 -->
                                        <div class="alert alert-info d-block">
                                            <h6>💡 개선 방안</h6>
                                            @if ($rates['overallErrorRate'] > 0)
                                                <p class="mb-2">• <strong>깨진 링크 수정:</strong> 404 오류를 반환하는 링크들을 올바른 URL로 수정하거나 제거하세요.</p>
                                            @endif
                                            @if ($totals['maxRedirectChainEffective'] > 2)
                                                <p class="mb-2">• <strong>리다이렉트 체인 단축:</strong> 여러 단계의 리다이렉트를 최종 목적지로 직접 연결하세요.</p>
                                            @endif
                                            @if ($rates['imageErrorRate'] > 0)
                                                <p class="mb-2">• <strong>이미지 경로 확인:</strong> 존재하지 않는 이미지 파일의 경로를 수정하거나 대체 이미지를 제공하세요.</p>
                                            @endif
                                            @if ($rates['anchorErrorRate'] > 0)
                                                <p class="mb-2">• <strong>앵커 ID 매칭:</strong> href="#section"에 대응하는 id="section" 요소가 페이지에 존재하는지 확인하세요.</p>
                                            @endif
                                            @if ($rates['externalErrorRate'] > 5)
                                                <p class="mb-2">• <strong>외부 링크 모니터링:</strong> 외부 사이트가 변경되거나 삭제될 수 있으므로 정기적으로 확인하세요.</p>
                                            @endif
                                            <p class="mb-0">• <strong>정기적인 검사:</strong> 웹사이트의 링크 상태는 시간이 지나면서 변할 수 있으므로 주기적으로 검사하세요.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-block">
                                            <h5>아직 결과가 없습니다</h5>
                                            <p class="mb-0">테스트를 실행하면 링크 검증 결과를 확인할 수 있습니다.</p>
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