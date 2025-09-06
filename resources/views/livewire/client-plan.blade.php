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
                    <h2 class="page-title">구독 및 결제 관리</h2>
                    <div class="page-pretitle">현재 구독과 쿠폰을 관리하고 사용 내역을 확인하세요</div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            <!-- 구독 플랜 섹션 -->
            @if ($subscriptions->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">📋 현재 구독 플랜</h4>
                            </div>
                            <div class="card-body">
                                @foreach ($subscriptions as $subscription)
                                    <div class="row mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-blue-lt text-blue-lt-fg me-2">구독</span>
                                                <h5 class="mb-0">{{ ucfirst($subscription->plan_type) }} 플랜</h5>
                                                @if (!$subscription->auto_renew)
                                                    <span class="badge bg-orange-lt text-orange-lt-fg ms-2">취소됨</span>
                                                @endif
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted mb-1">사용 현황</div>
                                                @php
                                                    $usage = $subscription->getRemainingUsage();
                                                @endphp
                                                <div class="row">
                                                    @if ($usage['monthly']['limit'])
                                                        <div class="col-4">
                                                            <div class="small text-muted">월간 사용량</div>
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
                                                            <div class="small text-muted">일간 사용량</div>
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
                                                    <div class="text-muted">시작일</div>
                                                    <div>{{ $subscription->start_date->format('Y-m-d') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted">만료일</div>
                                                    <div>{{ $subscription->end_date->format('Y-m-d') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted">월 요금</div>
                                                    <div>{{ number_format($subscription->price) }}원</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                @if ($subscription->auto_renew)
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary btn-sm dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            플랜 변경
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @foreach ($planTemplates as $key => $template)
                                                                @if ($key != $subscription->plan_type && $template['is_subscription'])
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            wire:click="changePlan({{ $subscription->id }}, '{{ $key }}')"
                                                                            onclick="confirm('{{ $template['name'] }} 플랜으로 변경하시겠습니까? 다음 결제일부터 적용됩니다.') || event.stopImmediatePropagation()">
                                                                            {{ $template['name'] }}
                                                                            ({{ number_format($template['price']) }}원/월)
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <button class="btn btn-danger btn-sm"
                                                        wire:click="cancelSubscription({{ $subscription->id }})"
                                                        onclick="confirm('정말로 구독을 취소하시겠습니까? 현재 구독 기간 종료 후 자동 갱신되지 않습니다.') || event.stopImmediatePropagation()">
                                                        구독 취소
                                                    </button>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <small>구독이
                                                            취소되었습니다.<br>{{ $subscription->end_date->format('Y-m-d') }}에
                                                            만료됩니다.</small>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($subscription->next_plan_type)
                                                <div class="mt-2">
                                                    <div class="alert alert-info mb-0">
                                                        <small>
                                                            <i class="ti ti-info-circle me-1"></i>
                                                            {{ $subscription->end_date->format('m월 d일') }}부터
                                                            {{ $planTemplates[$subscription->next_plan_type]['name'] }}
                                                            플랜({{ number_format($planTemplates[$subscription->next_plan_type]['price']) }}원/월)으로
                                                            변경됩니다.
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 쿠폰 섹션 -->
            @if ($coupons->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">🎫 보유 쿠폰</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($coupons as $coupon)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-green-lt text-green-lt-fg me-2">쿠폰</span>
                                                        <h6 class="mb-0">{{ ucfirst($coupon->plan_type) }}</h6>
                                                    </div>

                                                    @php
                                                        $usage = $coupon->getRemainingUsage();
                                                    @endphp

                                                    @if ($usage['total']['limit'])
                                                        <div class="mb-2">
                                                            <div class="small text-muted">사용 가능 횟수</div>
                                                            <div class="progress mb-1" style="height: 6px;">
                                                                <div class="progress-bar"
                                                                    style="width: {{ ($usage['total']['used'] / $usage['total']['limit']) * 100 }}%">
                                                                </div>
                                                            </div>
                                                            <div class="small">
                                                                {{ number_format($usage['total']['remaining']) }}회 남음
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row text-sm">
                                                        <div class="col-6">
                                                            <div class="text-muted">만료일시</div>
                                                            <div class="small">
                                                                {{ $coupon->end_date->format('Y-m-d H:i') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted">상태</div>
                                                            <div class="small">
                                                                @if ($coupon->isActive())
                                                                    <span
                                                                        class="badge bg-teal-lt text-teal-lt-fg">활성</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-red-lt text-red-lt-fg">만료</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($coupon->canRefund())
                                                        <div class="mt-2">
                                                            <button class="btn btn-outline-warning btn-sm w-100">
                                                                환불 가능 ({{ $coupon->refund_deadline->format('m-d') }}까지)
                                                            </button>
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
                </div>
            @endif

            <!-- 최근 사용 내역 -->
            @if ($recentUsage->count() > 0)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">📊 최근 24시간 테스트 사용 내역</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-vcenter table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>시간</th>
                                                <th>도메인</th>
                                                <th>테스트명</th>
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
                                        <small>최근 20건만 표시됩니다. (전체 {{ $recentUsage->count() }}건)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 플랜이 없을 때 -->
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
                                <h3>구독 플랜이 없습니다</h3>
                                <div class="text-muted mb-3">테스트를 사용하려면 구독 플랜이나 쿠폰이 필요합니다.</div>
                                <a href="/pricing" class="btn btn-primary">플랜 구매하기</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 구독 관련 안내사항 -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-blue-lt">
                        <div class="card-body">
                            <h5>📝 구독 관리 안내사항</h5>
                            <ul class="mb-0">
                                <li><strong>구독 취소:</strong> 즉시 취소되지 않으며, 현재 구독 기간이 끝난 후 자동 갱신이 중단됩니다.</li>
                                <li><strong>플랜 변경:</strong> 다음 결제일부터 새로운 플랜이 적용됩니다. 변경 전까지는 현재 플랜이 유지됩니다.</li>
                                <li><strong>환불 정책:</strong> 사용하지 않은 구독의 경우, 구매 후 7일 이내에 전액 환불 가능합니다.</li>
                                <li><strong>사용량 초기화:</strong> 구독 플랜의 월간 사용량은 매월 결제일에 초기화되며, 일간 사용량은 매일 자정에 초기화됩니다.</li>
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
