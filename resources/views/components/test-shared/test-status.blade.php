@if ($currentTest)
    <div class="alert alert-info d-block mb-4">
        @if ($selectedHistoryTest)
            <h4>선택된 검사: <span
                    class="badge {{ $currentTest->grade_color }}">{{ ucfirst($currentTest->status) }}</span>
            </h4>
            <p><strong>URL:</strong> {{ $currentTest->url }}</p>
            <p><strong>검사일시:</strong> {{ $currentTest->created_at->format('Y-m-d H:i:s') }}</p>
            @if ($currentTest->overall_grade)
                <p><strong>등급:</strong>
                    <span
                        class="badge {{ $currentTest->grade_color }}">{{ $currentTest->overall_grade }}</span>
                    @if ($currentTest->overall_score)
                        ({{ number_format($currentTest->overall_score, 1) }}점)
                    @endif
                </p>
            @endif
        @else
            <h4>진행 상태: <span
                    class="badge {{ $currentTest->grade_color }}">{{ ucfirst($currentTest->status) }}</span>
            </h4>
            <p><strong>URL:</strong> {{ $currentTest->url }}</p>
            <p><strong>시작일시:</strong>
                {{ $currentTest->started_at ? $currentTest->started_at->format('Y-m-d H:i:s') : $currentTest->created_at->format('Y-m-d H:i:s') }}
            </p>
        @endif

        @if ($currentTest->status === 'failed' && $currentTest->error_message)
            <div class="alert alert-danger mt-2">{{ $currentTest->error_message }}</div>
        @endif
    </div>
@endif