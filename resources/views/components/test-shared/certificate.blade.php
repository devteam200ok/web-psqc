@php
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
<div class="card mb-4">
    <div class="card-body text-center py-4">
        <div class="mb-3">
            <div class="h1 mb-2">
                <span class="{{ $gradeClass }}">{{ $grade }}</span>
            </div>
            @if ($currentTest->overall_score)
                <div class="text-muted h4">
                    {{ number_format($currentTest->overall_score, 1) }} points
                </div>
            @endif
        </div>

        <div class="mb-3">
            <small class="text-muted">
                Test Completed:
                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
            </small>
        </div>
        @if (Auth::check())

            @php
                $certificate = App\Models\Certificate::where('web_test_id', $currentTest->id)->where('payment_status', 'paid')->first();
            @endphp

            @if ($certificate)
                <a href="{{ url('/') }}/{{ $certificate->code }}/certified" class="btn btn-dark" target="_blank">
                    View Certificate
                </a>
            @else
                @if ($canIssueCertificate)
                    <button class="btn btn-primary" wire:click="issueCertificate">
                        Issue Certificate
                    </button>
                @else
                    <button class="btn btn-secondary" disabled>
                        Issue Certificate
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">Certificate issuance requires grade B or higher</small>
                    </div>
                @endif
            @endif
        @else
            <button class="btn btn-secondary" disabled>
                Issue Certificate
            </button>
            <div class="mt-2">
                <small class="text-muted">Login required for certificate issuance</small>
            </div>
        @endif
    </div>
</div>
