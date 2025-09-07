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
                    <h2 class="page-title">Subscription & Payment Management</h2>
                    <div class="page-pretitle">Manage your current subscriptions and coupons, and check usage history</div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            <!-- Subscription Plan Section -->
            @if ($subscriptions->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">📋 Current Subscription Plans</h4>
                            </div>
                            <div class="card-body">
                                @foreach ($subscriptions as $subscription)
                                    <div class="row mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-blue-lt text-blue-lt-fg me-2">Subscription</span>
                                                <h5 class="mb-0">{{ ucfirst($subscription->plan_type) }} Plan</h5>
                                                @if (!$subscription->auto_renew || $subscription->status === 'cancelled')
                                                    <span class="badge bg-orange-lt text-orange-lt-fg ms-2">Cancelled</span>
                                                @endif
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted mb-1">Usage Status</div>
                                                @php
                                                    $usage = $subscription->getRemainingUsage();
                                                @endphp
                                                <div class="row">
                                                    @if ($usage['monthly']['limit'])
                                                        <div class="col-4">
                                                            <div class="small text-muted">Monthly Usage</div>
                                                            <div class="progress mb-1" style="height: 6px;">
                                                                <div class="progress-bar"
                                                                    style="width: {{ ($usage['monthly']['used'] / $usage['monthly']['limit']) * 100 }}%">
                                                                </div>
                                                            </div>
                                                            <div class="small">
                                                                {{ number_format($usage['monthly']['used']) }} /
                                                                {{ number_format($usage['monthly']['limit']) }}</div>
                                                        </div>
                                                    @endif
                                                    @if ($usage['daily']['limit'])
                                                        <div class="col-4">
                                                            <div class="small text-muted">Daily Usage</div>
                                                            <div class="progress mb-1" style="height: 6px;">
                                                                <div class="progress-bar"
                                                                    style="width: {{ ($usage['daily']['used'] / $usage['daily']['limit']) * 100 }}%">
                                                                </div>
                                                            </div>
                                                            <div class="small">
                                                                {{ number_format($usage['daily']['used']) }} /
                                                                {{ number_format($usage['daily']['limit']) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row text-sm mb-3">
                                                <div class="col-4">
                                                    <div class="text-muted">Start Date</div>
                                                    <div>{{ $subscription->start_date->format('Y-m-d') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted">End Date</div>
                                                    <div>{{ $subscription->end_date->format('Y-m-d') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted">Monthly Fee</div>
                                                    <div>${{ number_format($subscription->price) }} USD</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                @if ($subscription->auto_renew && $subscription->status !== 'cancelled')
                                                    <button class="btn btn-danger btn-sm"
                                                        wire:click="cancelSubscription({{ $subscription->id }})"
                                                        onclick="confirm('Are you sure you want to cancel your subscription? It will not auto-renew after the current subscription period ends.') || event.stopImmediatePropagation()">
                                                        Cancel Subscription
                                                    </button>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <small>Subscription has been cancelled.<br>It will expire on {{ $subscription->end_date->format('Y-m-d') }}.</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Coupon Section -->
            @if ($coupons->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">🎫 Available Coupons</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($coupons as $coupon)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-green-lt text-green-lt-fg me-2">Coupon</span>
                                                        <h6 class="mb-0">{{ ucfirst($coupon->plan_type) }}</h6>
                                                    </div>

                                                    @php
                                                        $usage = $coupon->getRemainingUsage();
                                                    @endphp

                                                    @if ($usage['total']['limit'])
                                                        <div class="mb-2">
                                                            <div class="small text-muted">Available Uses</div>
                                                            <div class="progress mb-1" style="height: 6px;">
                                                                <div class="progress-bar"
                                                                    style="width: {{ ($usage['total']['used'] / $usage['total']['limit']) * 100 }}%">
                                                                </div>
                                                            </div>
                                                            <div class="small">
                                                                {{ number_format($usage['total']['remaining']) }} remaining
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row text-sm">
                                                        <div class="col-6">
                                                            <div class="text-muted">Expires</div>
                                                            <div class="small">
                                                                {{ $coupon->end_date->format('Y-m-d H:i') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted">Status</div>
                                                            <div class="small">
                                                                @if ($coupon->isActive())
                                                                    <span
                                                                        class="badge bg-teal-lt text-teal-lt-fg">Active</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-red-lt text-red-lt-fg">Expired</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Usage History -->
            @if ($recentUsage->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">📊 Recent 24-Hour Test Usage History</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Domain</th>
                                                <th>Test Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentUsage->take(20) as $usage)
                                                <tr>
                                                    <td class="text-muted">{{ $usage->created_at->format('m-d H:i') }}
                                                    </td>
                                                    <td>{{ $usage->domain }}</td>
                                                    <td>{{ $usage->test_name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($recentUsage->count() > 20)
                                    <div class="text-center text-muted mt-2">
                                        <small>Only the recent 20 entries are displayed. (Total {{ $recentUsage->count() }} entries)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- When no plans available -->
            @if ($subscriptions->count() == 0 && $coupons->count() == 0)
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3>No Subscription Plans</h3>
                                <div class="text-muted mb-3">You need a subscription plan or coupon to use tests.</div>
                                <a href="/pricing" class="btn btn-primary">Purchase Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Subscription Management Guidelines -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-blue-lt">
                        <div class="card-body">
                            <h5>📝 Subscription Management Guidelines</h5>
                            <ul class="mb-0">
                                <li><strong>Subscription Cancellation:</strong> Not cancelled immediately; auto-renewal stops after the current subscription period ends.</li>
                                <li><strong>Usage Reset:</strong> Monthly usage for subscription plans resets on the monthly billing date, and daily usage resets at midnight each day.</li>
                                <li><strong>Plan Management:</strong> You can cancel your subscription anytime. Service continues until the end of the current billing period.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
@endsection