<div class="page-header d-print-none">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row g-2 align-items-center">
            <div class="col d-flex">
                <div class="flex-grow-1">
                    <div class="d-xl-flex align-items-xl-baseline">
                        <h2 class="page-title mb-0 me-xl-3">{{ $title }}</h2>
                        <div class="page-subtitle mb-0">{{ $subtitle }}</div>
                    </div>
                </div>

                {{-- 비회원 IP 사용량 표시 --}}
                @if (!Auth::check() && isset($ipUsage) && $ipUsage)
                    <div class="ms-auto text-end">
                        <div class="mb-1 mb-xl-0 d-xl-inline-block me-xl-2">
                            <span class="badge bg-info text-white fw-bold fs-5 px-2 py-1">
                                🌐 {{ $ipAddress }}
                            </span>
                        </div>
                        <div class="d-xl-inline-block">
                            <span class="badge bg-dark text-white fw-bold fs-5 px-2 py-1">
                                🔥 {{ $ipUsage->usage }}회 남음
                            </span>
                        </div>
                    </div>
                @endif

                {{-- 회원 플랜 사용량 표시 --}}
                @if (Auth::check() && $userPlanUsage)
                    <div class="ms-auto text-end">
                        @if ($userPlanUsage['type'] === 'basic')
                            {{-- 플랜 없는 회원 - 월간 사용량만 표시 --}}
                            <div class="d-xl-inline-block">
                                <span class="badge bg-secondary text-white fw-bold fs-5 px-2 py-1">
                                    📅 월간 {{ $userPlanUsage['monthly_remaining'] }}회 남음
                                </span>
                            </div>
                        @elseif ($userPlanUsage['type'] === 'subscription')
                            {{-- 구독 플랜 - 월간, 일간 표시 --}}
                            @if ($userPlanUsage['monthly_remaining'] !== null)
                                <div class="mb-1 mb-xl-0 d-xl-inline-block me-xl-2">
                                    <span class="badge bg-primary text-white fw-bold fs-5 px-2 py-1">
                                        📊 월간 {{ $userPlanUsage['monthly_remaining'] }}회 남음
                                    </span>
                                </div>
                            @endif
                            @if ($userPlanUsage['daily_remaining'] !== null)
                                <div class="d-xl-inline-block">
                                    <span class="badge bg-success text-white fw-bold fs-5 px-2 py-1">
                                        📅 일간 {{ $userPlanUsage['daily_remaining'] }}회 남음
                                    </span>
                                </div>
                            @endif
                        @else
                            {{-- 쿠폰만 있는 경우 - 일간 표시 --}}
                            <div class="d-xl-inline-block">
                                <span class="badge bg-warning text-white fw-bold fs-5 px-2 py-1">
                                    🎫 {{ $userPlanUsage['daily_remaining'] }}회 남음
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>