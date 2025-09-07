@section('title')
    @include('inc.component.seo')
@endsection

@section('css')
@endsection

<div>
    <section class="pt-4">
        <div class="container-xl px-3">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Web Test Certificates</h2>
                    <div class="page-pretitle">View all your issued certificates at a glance</div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" wire:model.change="dateFrom">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" wire:model.change="dateTo">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.change="status">
                                <option value="all">All</option>
                                <option value="valid">Valid</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" wire:model.change="type">
                                <option value="all">All</option>
                                @foreach ($testTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
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
                    <div class="text-muted">Search Results: {{ $certificates->total() }} certificates</div>
                </div>
            </div>

            <!-- Certificate Grid -->
            <div class="row">
                @forelse($certificates as $certificate)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                @php
                                    $prefix = substr($certificate->test_type, 0, 1);
                                    $groupMap = [
                                        'p' => ['label' => 'Performance', 'class' => 'badge bg-teal-lt text-teal-lt-fg'],
                                        's' => ['label' => 'Security', 'class' => 'badge bg-red-lt text-red-lt-fg'],
                                        'q' => ['label' => 'Quality', 'class' => 'badge bg-indigo-lt text-indigo-lt-fg'],
                                        'c' => ['label' => 'Content', 'class' => 'badge bg-cyan-lt text-cyan-lt-fg'],
                                    ];
                                    $groupInfo = $groupMap[$prefix] ?? [
                                        'label' => 'Other',
                                        'class' => 'badge bg-azure-lt text-azure-lt-fg',
                                    ];
                                @endphp
                                <div class="mb-2">
                                    <div>
                                        <div>
                                            <span
                                                class="{{ $groupInfo['class'] }} me-1">{{ $groupInfo['label'] }}</span>
                                            <span
                                                class="badge bg-blue-lt text-blue-lt-fg me-1">{{ $certificate->test_type_name }}</span>
                                            <span
                                                class="badge {{ $certificate->grade_color }} me-1">{{ $certificate->overall_grade }}</span>
                                            <span
                                                class="{{ $certificate->status_badge_class }}">{{ $certificate->status }}</span>
                                        </div>
                                        <h3 class="card-title mb-0 mt-2">Web Test Certificate</h3>
                                        <div class="text-muted">{{ $certificate->url }}</div>
                                        <div class="text-start text-muted small">
                                            <div class="mt-2">Issued: {{ $certificate->issued_at?->format('Y-m-d') }}
                                            </div>
                                            <div>
                                                Expires: {{ $certificate->expires_at?->format('Y-m-d') ?? 'Never' }}
                                                @if (!empty($certificate->days_until_expiration))
                                                    <span>({{ $certificate->days_until_expiration . ' days until expiration' }})
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-auto pt-3 d-flex justify-content-start gap-2">
                                            <a href="{{ route('certified', ['code' => $certificate->code]) }}"
                                                target="_blank" rel="noopener" class="btn btn-sm btn-dark">
                                                🔎 View Details
                                            </a>
                                            @php
                                                $pdfRel = "certification/{$certificate->code}.pdf";
                                            @endphp
                                            @if (Storage::disk('local')->exists($pdfRel))
                                                <a href="{{ route('cert.pdf.download', ['code' => $certificate->code]) }}"
                                                    target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                    📥 Download
                                                </a>
                                            @else
                                                <a href="{{ route('certified', ['code' => $certificate->code]) }}"
                                                    target="_blank" rel="noopener"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    ⏳ PDF Preparing
                                                </a>
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
                {{ $certificates->onEachSide(0)->links() }}
            </div>
        </div>
    </div>

</div>

@section('js')
@endsection
