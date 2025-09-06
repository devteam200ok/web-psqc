@section('title')
    @include('inc.component.seo')
@endsection

@section('css')
    <style>
        /* PSQC 전용 도메인 관리 사이드바 개선 */
        .psqc-domain-card .domain-list {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
            overflow-x: hidden;
            /* 가로 스크롤 제거 */
        }

        .psqc-domain-card .list-group-item small {
            word-break: break-all;
            /* 긴 URL 줄바꿈 */
        }

        .psqc-domain-card .list-group-item {
            white-space: normal;
            /* 내용이 자연스럽게 줄바꿈되도록 */
        }
    </style>
@endsection

<div>
    <section class="pt-4">
        <div class="container-xl px-3">
            <div class="row g-2 align-items-center">
                <div class="col d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="page-title">PSQC 종합 인증서</h2>
                        <div class="page-pretitle">소유권 인증 도메인 기반 종합 인증 관리</div>
                    </div>
                    <div>
                        <div class="btn-list">
                            <button class="btn btn-outline-dark {{ $page == 'history' ? 'active' : '' }}"
                                wire:click="$set('page', 'history')">발급 내역</button>
                            <button class="btn btn-outline-dark {{ $page == 'issue' ? 'active' : '' }}"
                                wire:click="$set('page', 'issue')">인증서 발행</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            @if ($page == 'history')
                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">시작일</label>
                                <input type="date" class="form-control" wire:model.change="dateFrom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">종료일</label>
                                <input type="date" class="form-control" wire:model.change="dateTo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">상태</label>
                                <select class="form-select" wire:model.change="status">
                                    <option value="all">전체</option>
                                    <option value="valid">유효</option>
                                    <option value="expired">만료</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button class="btn btn-sm btn-secondary" wire:click="clearFilters">필터 초기화</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row mb-2">
                    <div class="col">
                        <div class="text-muted">검색 결과: {{ $certifications->total() }}건</div>
                    </div>
                </div>

                <!-- Certificate Grid -->
                <div class="row">
                    @forelse($certifications as $certification)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <div>
                                            <div>
                                                <span
                                                    class="badge {{ $certification->grade_color }} me-1">{{ $certification->overall_grade }}</span>
                                                <span
                                                    class="{{ $certification->status_badge_class }}">{{ $certification->status }}</span>
                                            </div>
                                            <h3 class="card-title mb-0 mt-2">PSQC 종합 인증서</h3>
                                            <div class="text-muted">{{ $certification->url }}</div>
                                            <div class="text-start text-muted small">
                                                <div class="mt-2">발급일:
                                                    {{ $certification->issued_at?->format('Y-m-d') }}
                                                </div>
                                                <div>
                                                    만료일: {{ $certification->expires_at?->format('Y-m-d') ?? '없음' }}
                                                    @if (!empty($certification->days_until_expiration))
                                                        <span>({{ '만료까지 ' . $certification->days_until_expiration . '일' }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-auto pt-3 d-flex justify-content-start gap-2">
                                                <a href="{{ route('psqc.certified', ['code' => $certification->code]) }}"
                                                    target="_blank" rel="noopener" class="btn btn-sm btn-dark">
                                                    🔎 상세 보기
                                                </a>
                                                @php
                                                    $pdfRel = "psqc-certification/{$certification->code}.pdf";
                                                @endphp
                                                @if (Storage::disk('local')->exists($pdfRel))
                                                    <a href="{{ route('cert.psqc.download', ['code' => $certification->code]) }}"
                                                        target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                        📥 다운로드
                                                    </a>
                                                @else
                                                    <button wire:click="generatePsqcCertificatePdf('{{ $certification->code }}')" wire:loading.attr="disabled"
                                                        class="btn btn-sm btn-primary">
                                                        ⏳ 인증서 생성
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center text-muted">
                                    표시할 인증서가 없습니다.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="row my-3">
                    {{ $certifications->onEachSide(0)->links() }}
                </div>
            @endif
            @if ($page == 'issue')
                <div class="row g-3">
                    <!-- Main content: Verified domains -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="alert alert-info d-block" role="alert">
                            <div class="mb-1">🔒 <strong>인증완료 도메인</strong>에 한해 PSQC 종합 인증서 발행이 가능합니다.</div>
                            <div class="mb-1">📊 <strong>점수 산정</strong>: 최근 <strong>3일</strong> 기준 각 개별 시험의
                                <strong>최고
                                    점수</strong>를 합산하여 총점과 등급이 결정됩니다.
                            </div>
                            <div class="mb-1">✅ <strong>발행 조건</strong>: 총 <strong>16개 시험</strong>을 모두 완료해야 발행됩니다.
                            </div>
                            <div>🗓️ <strong>유효기간</strong>: 발행일로부터 <strong>1년</strong>.</div>
                        </div>
                        <div class="row">
                            @php
                                $testTypes = App\Models\WebTest::getTestTypes();
                            @endphp

                            @forelse($psqcCards as $card)
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h3 class="card-title mb-0">{{ $card['display'] }}</h3>
                                                    <div class="text-muted small">{{ $card['url'] }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-teal-lt text-teal-lt-fg">최근 3일 내 기록
                                                        기준</span>
                                                    <div class="mt-1 text-muted small">진행 현황:
                                                        {{ $card['completed'] }} /
                                                        {{ $card['total'] }}</div>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <!-- 16 test rows grouped by PSQC -->
                                            @php
                                                $groups = [
                                                    '성능 (P)' => ['p-speed', 'p-load', 'p-mobile'],
                                                    '보안 (S)' => [
                                                        's-ssl',
                                                        's-sslyze',
                                                        's-header',
                                                        's-scan',
                                                        's-nuclei',
                                                    ],
                                                    '품질 (Q)' => [
                                                        'q-lighthouse',
                                                        'q-accessibility',
                                                        'q-compatibility',
                                                        'q-visual',
                                                    ],
                                                    '콘텐츠 (C)' => ['c-links', 'c-structure', 'c-crawl', 'c-meta'],
                                                ];
                                            @endphp
                                            <div class="row g-2">
                                                @foreach ($groups as $groupLabel => $keys)
                                                    <div class="col-12 col-md-6 col-xl-3">
                                                        <div class="card">
                                                            <div class="card-header py-2">
                                                                <h4 class="card-title mb-0 small">
                                                                    {{ $groupLabel }}
                                                                </h4>
                                                            </div>
                                                            <div class="card-body py-2">
                                                                @foreach ($keys as $key)
                                                                    @php $label = $testTypes[$key] ?? $key; @endphp
                                                                    @php $test = $card['tests'][$key] ?? null; @endphp
                                                                    @php
                                                                        $groupMap = [
                                                                            'p' => 'performance',
                                                                            's' => 'security',
                                                                            'q' => 'quality',
                                                                            'c' => 'content',
                                                                        ];
                                                                        $prefix = substr($key, 0, 1);
                                                                        $group = $groupMap[$prefix] ?? 'performance';
                                                                        $typeSlug = substr($key, 2); // after "x-"
                                                                        $runUrl =
                                                                            url('/') .
                                                                            '/' .
                                                                            $group .
                                                                            '/' .
                                                                            $typeSlug .
                                                                            '?url=' .
                                                                            urlencode($card['url']);
                                                                    @endphp
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center py-1">
                                                                        <div class="text-muted">{{ $label }}
                                                                        </div>
                                                                        <div>
                                                                            @if ($test)
                                                                                <a href="{{ $runUrl }}"
                                                                                    target="_blank" rel="noopener"
                                                                                    class="badge {{ $test->grade_color }}"
                                                                                    style="cursor: pointer;">
                                                                                    {{ $test->overall_grade }}
                                                                                    @if ($test->overall_score)
                                                                                        ({{ number_format($test->overall_score, 1) }}점)
                                                                                    @endif
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ $runUrl }}"
                                                                                    target="_blank" rel="noopener"
                                                                                    class="btn btn-sm btn-secondary">검사하기</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <hr class="my-3">

                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    @if (!is_null($card['final_score']))
                                                        @php
                                                            $gradeToClass = [
                                                                'A+' => 'badge bg-green-lt text-green-lt-fg',
                                                                'A' => 'badge bg-lime-lt text-lime-lt-fg',
                                                                'B' => 'badge bg-blue-lt text-blue-lt-fg',
                                                                'C' => 'badge bg-yellow-lt text-yellow-lt-fg',
                                                                'D' => 'badge bg-orange-lt text-orange-lt-fg',
                                                                'F' => 'badge bg-red-lt text-red-lt-fg',
                                                            ];
                                                            $finalGradeClass =
                                                                $gradeToClass[$card['final_grade']] ??
                                                                'badge bg-azure-lt text-azure-lt-fg';
                                                        @endphp
                                                        <div class="h3 mb-0">
                                                            <span class="{{ $finalGradeClass }}">
                                                                {{ $card['final_grade'] }}
                                                                ({{ number_format($card['final_score'], 1) }}점)
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="text-muted">모든 항목 테스트 완료 시 종합 점수가 표시됩니다.</div>
                                                    @endif
                                                </div>
                                                <!-- 인증서 발급 버튼 부분만 수정 -->
                                                <div>
                                                    @if ($card['completed'] === $card['total'])
                                                        <button class="btn btn-primary"
                                                            wire:click="issueCertificate({{ $card['domain_id'] }})"
                                                            wire:loading.attr="disabled">
                                                            인증서 발급
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary" disabled>
                                                            인증서 발급
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center text-muted">
                                            소유권 인증이 완료된 도메인이 없습니다. 우측에서 도메인을 등록/인증하세요.
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Sidebar: Domain management -->
                    <div class="col-lg-4 col-xl-3">
                        <div class="card psqc-domain-card">
                            <div class="card-header">
                                <h3 class="card-title mb-0">도메인 관리</h3>
                            </div>
                            <div class="card-body">
                                @if (Auth::check())
                                    <div class="mb-3">
                                        <label class="form-label">도메인 추가</label>
                                        <div class="input-group">
                                            <input type="url" wire:keydown.enter="addDomain"
                                                class="form-control @error('newDomainUrl') is-invalid @enderror"
                                                placeholder="https://www.example.com" wire:model.defer="newDomainUrl">
                                            <button class="btn btn-primary" wire:click="addDomain">추가</button>
                                        </div>
                                        @error('newDomainUrl')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="domain-list">
                                        @if ($userDomains && count($userDomains) > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach ($userDomains as $domain)
                                                    <div
                                                        class="list-group-item px-2 d-flex justify-content-between align-items-center">
                                                        <div class="flex-grow-1 me-2">
                                                            <div class="fw-bold">{{ $domain['display_name'] }}
                                                            </div>
                                                            <small class="text-muted">{{ $domain['url'] }}</small>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 text-nowrap">
                                                            <span class="{{ $domain['verification_status_class'] }}"
                                                                style="cursor: pointer;"
                                                                wire:click="openVerificationModal({{ $domain['id'] }})">
                                                                {{ $domain['verification_status'] }}
                                                            </span>
                                                            <span style="cursor: pointer;"
                                                                wire:click="deleteDomain({{ $domain['id'] }})"
                                                                wire:confirm="이 도메인을 삭제하시겠습니까?">
                                                                🗑️
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted p-4">
                                                <p class="mb-0">등록된 도메인이 없습니다.</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center text-muted p-4">
                                        <p>도메인 관리는 로그인이 필요합니다.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <x-test-shared.domain-verification-modal :show-verification-modal="$showVerificationModal" :current-verification-domain="$currentVerificationDomain" :verification-message="$verificationMessage"
                            :verification-message-type="$verificationMessageType" />
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>

@section('js')
    @include('components.test-shared.js')
@endsection
