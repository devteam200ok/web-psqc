<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
            <li class="nav-item">
                <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'history')"
                    class="nav-link {{ $sideTabActive == 'history' ? 'active' : '' }}" data-bs-toggle="tab">검사 내역</a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'domain')"
                    class="nav-link {{ $sideTabActive == 'domain' ? 'active' : '' }}" data-bs-toggle="tab">도메인</a>
            </li>
            @if ($hasProOrAgencyPlan)
                <li class="nav-item">
                    <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'scheduled')"
                        class="nav-link {{ $sideTabActive == 'scheduled' ? 'active' : '' }}" data-bs-toggle="tab">예약된
                        검사</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- 검사 내역 -->
            <div class="tab-pane {{ $sideTabActive == 'history' ? 'active show' : '' }}" id="tabs-history"
                style="max-height: calc(100vh - 300px); overflow-y: auto;">
                @if (Auth::check() && count($testHistory) > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($testHistory as $test)
                            <div class="list-group-item list-group-item-action {{ $selectedHistoryTest && $selectedHistoryTest->id === $test->id ? 'active' : '' }}"
                                wire:click="selectHistoryTest({{ $test->id }})" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $test->short_domain }}</div>
                                        <small class="text-muted">{{ $test->url }}</small>

                                        @if ($test->overall_grade || $test->overall_score)
                                            <div class="mt-1 d-flex align-items-center gap-2">
                                                @if ($test->overall_grade)
                                                    <span
                                                        class="badge {{ $test->grade_color }}">{{ $test->overall_grade }}</span>
                                                @endif
                                                @if ($test->overall_score)
                                                    <small
                                                        class="text-muted">{{ number_format($test->overall_score, 1) }}점</small>
                                                @endif
                                                <small class="text-muted"> -
                                                    {{ $test->created_at->format('m/d H:i') }}</small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <span class="badge bg-azure-lt text-azure-lt-fg">
                                                    {{ ucfirst($test->status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if (!$test->is_saved_permanently)
                                        <div class="d-flex align-items-center">
                                            <span style="cursor: pointer;"
                                                wire:click.stop="deleteTestHistory({{ $test->id }})"
                                                wire:confirm="이 검사 내역을 삭제하시겠습니까?">
                                                🗑️
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($testHistory->count() >= 10)
                        <div class="position-sticky bottom-0 start-0 end-0 border-top bg-white p-2 small text-muted text-center"
                            style="z-index: 1;">
                            🗂️ 검사 내역은 <strong>최근 30일, 최대 100개</strong>까지만 보관됩니다.<br>
                            🧾 인증서를 발행받은 시험 성적은 <strong>인증서 유효기간</strong> 동안 보관됩니다.
                        </div>
                    @endif
                @elseif(Auth::check())
                    <div class="text-center text-muted p-4">
                        <p>아직 검사 내역이 없습니다.</p>
                        <p>첫 번째 테스트를 실행해보세요!</p>
                    </div>
                @else
                    <div class="text-center text-muted p-4">
                        <p>검사 내역을 보려면 로그인이 필요합니다.</p>
                    </div>
                @endif
            </div>

            <!-- 도메인 -->
            <div class="tab-pane {{ $sideTabActive == 'domain' ? 'active show' : '' }}" id="tabs-domain"
                style="max-height: calc(100vh - 300px); overflow-y: auto;">
                @if (Auth::check())
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="url" wire:model="newDomainUrl" wire:keydown.enter="addDomain"
                                class="form-control @error('newDomainUrl') is-invalid @enderror"
                                placeholder="https://example.com/path" wire:keydown.enter="addDomain">
                            <button wire:click="addDomain" wire:loading.attr="disabled" wire:target="addDomain"
                                class="btn btn-primary" type="button">
                                추가
                            </button>
                        </div>
                        @error('newDomainUrl')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($userDomains && count($userDomains) > 0)
                        <div class="list-group">
                            @foreach ($userDomains as $domain)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1" style="cursor: pointer;"
                                        wire:click="selectDomain('{{ $domain['url'] }}')">
                                        <div class="fw-bold">{{ $domain['display_name'] }}</div>
                                        <small class="text-muted">{{ $domain['url'] }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="{{ $domain['verification_status_class'] }}"
                                            style="cursor: pointer;"
                                            wire:click="openVerificationModal({{ $domain['id'] }})">
                                            {{ $domain['verification_status'] }}
                                        </span>
                                        <span style="cursor: pointer;" wire:click="deleteDomain({{ $domain['id'] }})"
                                            wire:confirm="이 도메인을 삭제하시겠습니까?">
                                            🗑️
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted p-4">
                            <p>등록된 도메인이 없습니다.</p>
                            <p>자주 사용하는 URL을 등록해보세요!</p>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted p-4">
                        <p>도메인 관리는 로그인이 필요합니다.</p>
                    </div>
                @endif
            </div>

            <!-- 예약된 검사 -->
            @if ($hasProOrAgencyPlan)
                <div class="tab-pane {{ $sideTabActive == 'scheduled' ? 'active show' : '' }}" id="tabs-scheduled"
                    style="max-height: calc(100vh - 300px); overflow-y: auto;">
                    @if (Auth::check())
                        @if ($scheduledTests && count($scheduledTests) > 0)
                            <div class="list-group">
                                @foreach ($scheduledTests as $scheduled)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $scheduled['short_domain'] }}</div>
                                            <small class="text-muted">{{ $scheduled['url'] }}</small>
                                            <div class="mt-1">
                                                <small class="text-primary">
                                                    {{ $scheduled['scheduled_at_formatted'] }}
                                                </small>
                                                @if ($scheduled['is_overdue'])
                                                    <span class="badge bg-danger-lt text-danger-lt-fg ms-1">지연됨</span>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <span class="{{ $scheduled['status_badge_class'] }}">
                                                    {{ $scheduled['status_text'] }}
                                                </span>
                                                <small
                                                    class="text-muted ms-2">{{ $scheduled['test_type_name'] }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @if ($scheduled['can_be_cancelled'])
                                                <span style="cursor: pointer;"
                                                    wire:click="cancelScheduledTest({{ $scheduled['id'] }})"
                                                    wire:confirm="이 예약된 검사를 취소하시겠습니까?">
                                                    🗑️
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted p-4">
                                <p>예약된 검사가 없습니다.</p>
                                <p>상단에서 검사를 예약하거나 스케쥴을 등록해보세요.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-4">
                            <p>예약된 검사를 보려면 로그인이 필요합니다.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
