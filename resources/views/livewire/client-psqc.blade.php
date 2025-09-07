@section('title')
    @include('inc.component.seo')
@endsection

@section('css')
    <style>
        /* PSQC dedicated domain management sidebar improvements */
        .psqc-domain-card .domain-list {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
            overflow-x: hidden;
            /* Remove horizontal scrolling */
        }

        .psqc-domain-card .list-group-item small {
            word-break: break-all;
            /* Long URL line break */
        }

        .psqc-domain-card .list-group-item {
            white-space: normal;
            /* Allow content to wrap naturally */
        }
    </style>
@endsection

<div>
    <section class="pt-4">
        <div class="container-xl px-3">
            <div class="row g-2 align-items-center">
                <div class="col d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="page-title">PSQC Comprehensive Certificate</h2>
                        <div class="page-pretitle">Comprehensive certification management based on domain ownership verification</div>
                    </div>
                    <div>
                        <div class="btn-list">
                            <button class="btn btn-outline-dark {{ $page == 'history' ? 'active' : '' }}"
                                wire:click="$set('page', 'history')">Issue History</button>
                            <button class="btn btn-outline-dark {{ $page == 'issue' ? 'active' : '' }}"
                                wire:click="$set('page', 'issue')">Issue Certificate</button>
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
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" wire:model.change="dateFrom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" wire:model.change="dateTo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model.change="status">
                                    <option value="all">All</option>
                                    <option value="valid">Valid</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button class="btn btn-sm btn-secondary" wire:click="clearFilters">Clear Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row mb-2">
                    <div class="col">
                        <div class="text-muted">Search Results: {{ $certifications->total() }} certificates</div>
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
                                            <h3 class="card-title mb-0 mt-2">PSQC Comprehensive Certificate</h3>
                                            <div class="text-muted">{{ $certification->url }}</div>
                                            <div class="text-start text-muted small">
                                                <div class="mt-2">Issued:
                                                    {{ $certification->issued_at?->format('Y-m-d') }}
                                                </div>
                                                <div>
                                                    Expires: {{ $certification->expires_at?->format('Y-m-d') ?? 'Never' }}
                                                    @if (!empty($certification->days_until_expiration))
                                                        <span>({{ $certification->days_until_expiration . ' days until expiration' }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-auto pt-3 d-flex justify-content-start gap-2">
                                                <a href="{{ route('psqc.certified', ['code' => $certification->code]) }}"
                                                    target="_blank" rel="noopener" class="btn btn-sm btn-dark">
                                                    🔎 View Details
                                                </a>
                                                @php
                                                    $pdfRel = "psqc-certification/{$certification->code}.pdf";
                                                @endphp
                                                @if (Storage::disk('local')->exists($pdfRel))
                                                    <a href="{{ route('cert.psqc.download', ['code' => $certification->code]) }}"
                                                        target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                        📥 Download
                                                    </a>
                                                @else
                                                    <button wire:click="generatePsqcCertificatePdf('{{ $certification->code }}')" wire:loading.attr="disabled"
                                                        class="btn btn-sm btn-primary">
                                                        ⏳ Generate Certificate
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
                                    No certificates to display.
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
                            <div class="mb-1">🔒 PSQC Comprehensive Certificates can only be issued for <strong>verified domains</strong>.</div>
                            <div class="mb-1">📊 <strong>Score Calculation</strong>: Total score and grade are determined by combining the
                                <strong>highest scores</strong> from each individual test within the last <strong>3 days</strong>.
                            </div>
                            <div class="mb-1">✅ <strong>Issue Requirements</strong>: All <strong>16 tests</strong> must be completed for issuance.
                            </div>
                            <div>🗓️ <strong>Validity Period</strong>: <strong>1 year</strong> from the date of issuance.</div>
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
                                                    <span class="badge bg-teal-lt text-teal-lt-fg">Based on records
                                                        within last 3 days</span>
                                                    <div class="mt-1 text-muted small">Progress:
                                                        {{ $card['completed'] }} /
                                                        {{ $card['total'] }}</div>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <!-- 16 test rows grouped by PSQC -->
                                            @php
                                                $groups = [
                                                    'Performance (P)' => ['p-speed', 'p-load', 'p-mobile'],
                                                    'Security (S)' => [
                                                        's-ssl',
                                                        's-sslyze',
                                                        's-header',
                                                        's-scan',
                                                        's-nuclei',
                                                    ],
                                                    'Quality (Q)' => [
                                                        'q-lighthouse',
                                                        'q-accessibility',
                                                        'q-compatibility',
                                                        'q-visual',
                                                    ],
                                                    'Content (C)' => ['c-links', 'c-structure', 'c-crawl', 'c-meta'],
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
                                                                                        ({{ number_format($test->overall_score, 1) }} pts)
                                                                                    @endif
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ $runUrl }}"
                                                                                    target="_blank" rel="noopener"
                                                                                    class="btn btn-sm btn-secondary">Test Now</a>
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
                                                                ({{ number_format($card['final_score'], 1) }} pts)
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="text-muted">Comprehensive score will be displayed when all tests are completed.</div>
                                                    @endif
                                                </div>
                                                <!-- 인증서 발급 버튼 부분만 수정 -->
                                                <div>
                                                    @if ($card['completed'] === $card['total'])
                                                        <button class="btn btn-primary"
                                                            wire:click="issueCertificate({{ $card['domain_id'] }})"
                                                            wire:loading.attr="disabled">
                                                            Issue Certificate
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary" disabled>
                                                            Issue Certificate
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
