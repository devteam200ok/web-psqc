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

                {{-- Guest IP usage display --}}
                @if (!Auth::check() && isset($ipUsage) && $ipUsage)
                    <div class="ms-auto text-end">
                        <div class="mb-1 mb-xl-0 d-xl-inline-block me-xl-2">
                            <span class="badge bg-info text-white fw-bold fs-5 px-2 py-1">
                                🌐 {{ $ipAddress }}
                            </span>
                        </div>
                        <div class="d-xl-inline-block">
                            <span class="badge bg-dark text-white fw-bold fs-5 px-2 py-1">
                                🔥 {{ $ipUsage->usage }} left
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Member plan usage display --}}
                @if (Auth::check() && $userPlanUsage)
                    <div class="ms-auto text-end">
                        @if ($userPlanUsage['type'] === 'basic')
                            <div class="d-xl-inline-block">
                                <span class="badge bg-secondary text-white fw-bold fs-5 px-2 py-1">
                                    📅 M: {{ $userPlanUsage['monthly_remaining'] }}
                                </span>
                            </div>
                        @elseif ($userPlanUsage['type'] === 'subscription')
                            @if ($userPlanUsage['monthly_remaining'] !== null)
                                <div class="mb-1 mb-xl-0 d-xl-inline-block me-xl-2">
                                    <span class="badge bg-primary text-white fw-bold fs-5 px-2 py-1">
                                        📊 M: {{ $userPlanUsage['monthly_remaining'] }}
                                    </span>
                                </div>
                            @endif
                            @if ($userPlanUsage['daily_remaining'] !== null)
                                <div class="d-xl-inline-block">
                                    <span class="badge bg-success text-white fw-bold fs-5 px-2 py-1">
                                        📅 D: {{ $userPlanUsage['daily_remaining'] }}
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="d-xl-inline-block">
                                <span class="badge bg-warning text-white fw-bold fs-5 px-2 py-1">
                                    🎫 C: {{ $userPlanUsage['daily_remaining'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
