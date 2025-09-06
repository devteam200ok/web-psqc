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
                    {{ number_format($currentTest->overall_score, 1) }}점
                </div>
            @endif
        </div>

        <div class="mb-3">
            <small class="text-muted">
                테스트 완료:
                {{ $currentTest->finished_at ? $currentTest->finished_at->format('Y-m-d H:i:s') : $currentTest->updated_at->format('Y-m-d H:i:s') }}
            </small>
        </div>
        @if (Auth::check())

            @php
                $certificate = App\Models\Certificate::where('web_test_id', $currentTest->id)->where('payment_status', 'paid')->first();
            @endphp

            @if ($certificate)
                <a href="{{ url('/') }}/{{ $certificate->code }}/certified" class="btn btn-dark" target="_blank">
                    인증서 보기
                </a>
            @else
                @if ($canIssueCertificate)
                    <button class="btn btn-primary" wire:click="issueCertificate">
                        인증서 발급
                    </button>
                @else
                    <button class="btn btn-secondary" disabled>
                        인증서 발급
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">B등급 이상부터 인증서 발급이 가능합니다</small>
                    </div>
                @endif
            @endif
        @else
            <button class="btn btn-secondary" disabled>
                인증서 발급
            </button>
            <div class="mt-2">
                <small class="text-muted">인증서 발급은 로그인이 필요합니다</small>
            </div>
        @endif
    </div>
</div>
