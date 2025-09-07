<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
            <li class="nav-item">
                <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'history')"
                    class="nav-link {{ $sideTabActive == 'history' ? 'active' : '' }}" data-bs-toggle="tab">Test History</a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'domain')"
                    class="nav-link {{ $sideTabActive == 'domain' ? 'active' : '' }}" data-bs-toggle="tab">Domains</a>
            </li>
            @if ($hasProOrAgencyPlan)
                <li class="nav-item">
                    <a href="javascript:void(0);" wire:click="$set('sideTabActive', 'scheduled')"
                        class="nav-link {{ $sideTabActive == 'scheduled' ? 'active' : '' }}" data-bs-toggle="tab">Scheduled Tests</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Test History -->
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
                                                    <small class="text-muted">{{ number_format($test->overall_score, 1) }} pts</small>
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

                                    @if (!$test->is_saved_permanently && $test->psqc_certification_id === null)
                                        <div class="d-flex align-items-center">
                                                <span style="cursor: pointer;"
                                                wire:click.stop="deleteTestHistory({{ $test->id }})"
                                                wire:confirm="Delete this test history?">
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
                            🗂️ Test history is retained for <strong>30 days, up to 100 items</strong>.<br>
                            🧾 Results with issued certificates are retained for the <strong>certificate validity period</strong>.
                        </div>
                    @endif
                @elseif(Auth::check())
                    <div class="text-center text-muted p-4">
                        <p>No recent test history.</p>
                        <p>Run your first test to see it here.</p>
                    </div>
                @else
                    <div class="text-center text-muted p-4">
                        <p>Sign in to view test history.</p>
                    </div>
                @endif
            </div>

            <!-- Domains -->
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
                                Add
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
                                            wire:confirm="Delete this domain?">
                                            🗑️
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted p-4">
                            <p>No domains added.</p>
                            <p>Save your frequently used URLs.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted p-4">
                        <p>Sign in to manage domains.</p>
                    </div>
                @endif
            </div>

            <!-- Scheduled Tests -->
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
                                                    <span class="badge bg-danger-lt text-danger-lt-fg ms-1">Overdue</span>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <span class="{{ $scheduled['status_badge_class'] }}">
                                                    {{ $scheduled['status_text'] }}
                                                </span>
                                                <small class="text-muted ms-2">{{ $scheduled['test_type_name'] }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @if ($scheduled['can_be_cancelled'])
                                                <span style="cursor: pointer;"
                                                    wire:click="cancelScheduledTest({{ $scheduled['id'] }})"
                                                    wire:confirm="Cancel this scheduled test?">
                                                    🗑️
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted p-4">
                                <p>No scheduled tests.</p>
                                <p>Schedule a test or add a schedule above.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-4">
                            <p>Sign in to view scheduled tests.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
