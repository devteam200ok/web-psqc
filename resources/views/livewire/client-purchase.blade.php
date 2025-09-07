@section('title')
    @include('inc.component.seo')
@endsection
@section('css')
@endsection

<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="page-title">{{ $planData['name'] }} Plan Payment</h2>
                <div class="text-muted">This is the payment page for purchasing {{ $planData['is_subscription'] ? 'subscription' : 'coupon' }} plan.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 offset-lg-3 mb-2">
                <!-- Plan Information -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge {{ $planData['is_subscription'] ? 'bg-blue-lt text-blue' : 'bg-green-lt text-green' }} me-1">
                                {{ $planData['is_subscription'] ? 'Subscription' : 'Coupon' }}
                            </span>
                        </div>
                        <h3 class="page-title">{{ $planData['name'] }} Plan</h3>
                        <div class="page-pretitle">{{ $planData['description'] }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📋 Plan Benefits</h6>
                                    <ul class="mb-0">
                                        @foreach($planData['features'] as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            @if($planData['is_subscription'])
                                <div class="alert alert-warning">
                                    <div>
                                        <h6>⚠️ Subscription Information</h6>
                                        <ul class="mb-0">
                                            <li>Automatically charged monthly via PayPal</li>
                                            <li>Full refund available within {{ $planData['refund_days'] }} days before use</li>
                                            <li>No refund once used</li>
                                            <li>Can cancel subscription anytime through PayPal</li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <div>
                                        <h6>⚠️ Coupon Information</h6>
                                        <ul class="mb-0">
                                            <li>Available for {{ $planData['validity_days'] }} days</li>
                                            @if($planData['refund_days'] > 0)
                                                <li>Full refund available within {{ $planData['refund_days'] }} days before use</li>
                                            @else
                                                <li>No refund available</li>
                                            @endif
                                            <li>No refund once used</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h4>{{ $planData['name'] }} Plan</h4>
                                <span class="h4">{{ number_format($amount) }} USD{{ $planData['is_subscription'] ? '/month' : '' }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div>
                            <h4 class="mt-3">Total Payment Amount</h4>
                            <span class="h4">{{ number_format($amount) }} USD</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="mt-3">
                    @if ($amount != 0)
                        <div id="paypal-button-container"></div>
                    @else
                        <button wire:loading.attr="disabled" wire:click="purchaseForFree" class="btn btn-primary w-100">
                            Purchase for Free
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden inputs for PayPal -->
    <input type="hidden" id="totalPrice" value="{{ $amount }}">
    <input type="hidden" id="planType" value="{{ $planType }}">
    <input type="hidden" id="orderId" value="{{ $orderId }}">
    <input type="hidden" id="isSubscription" value="{{ $planData['is_subscription'] ? 'true' : 'false' }}">
</div>

@section('js')
    @if ($amount != 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let sdkLoaded = false;
                const totalPrice = parseFloat(document.getElementById('totalPrice').value || 0);
                const isSubscription = document.getElementById('isSubscription').value === 'true';

                if (!sdkLoaded) {
                    sdkLoaded = true;
                    const script = document.createElement('script');
                    
                    // 구독과 일회성 결제에 따라 다른 components 로드
                    if (isSubscription) {
                        script.src = 'https://www.paypal.com/sdk/js?client-id={{ $paypal_client_id }}&vault=true&intent=subscription&currency=USD';
                    } else {
                        script.src = 'https://www.paypal.com/sdk/js?client-id={{ $paypal_client_id }}&components=buttons&currency=USD';
                    }
                    
                    script.onload = () => renderPayPalButtons(totalPrice, isSubscription);
                    document.body.appendChild(script);
                } else {
                    renderPayPalButtons(totalPrice, isSubscription);
                }

                function renderPayPalButtons(totalPrice, isSubscription) {
                    if (isSubscription) {
                        // 구독 결제
                        paypal.Buttons({
                            style: {
                                layout: 'vertical',
                                color: 'blue',
                                shape: 'rect',
                                label: 'subscribe'
                            },
                            createSubscription: function(data, actions) {
                                return actions.subscription.create({
                                    'plan_id': 'P-{{ strtoupper($planType) }}_MONTHLY', // 미리 생성된 플랜 ID 사용
                                    'custom_id': '{{ $orderId }}',
                                    'application_context': {
                                        'brand_name': '{{ config("app.name") }}',
                                        'locale': 'en-US',
                                        'shipping_preference': 'NO_SHIPPING',
                                        'user_action': 'SUBSCRIBE_NOW',
                                        'payment_method': {
                                            'payer_selected': 'PAYPAL',
                                            'payee_preferred': 'IMMEDIATE_PAYMENT_REQUIRED'
                                        },
                                        'return_url': '{{ url("/") }}/plan/payment/success?plan_type={{ $planType }}',
                                        'cancel_url': '{{ url("/") }}/plan/payment/cancel?plan_type={{ $planType }}'
                                    }
                                });
                            },
                            onApprove: function(data, actions) {
                                // 구독 승인 후 검증
                                Livewire.dispatch('paypal-subscription-verified', {
                                    subscription_id: data.subscriptionID
                                });
                            },
                            onError: function(err) {
                                console.error('PayPal Subscription Error:', err);
                                alert('An error occurred while processing your subscription. Please try again later.');
                                window.location.href = '/plan/payment/fail?plan_type={{ $planType }}';
                            },
                            onCancel: function(data) {
                                console.log('Subscription cancelled by user');
                                window.location.href = '/plan/payment/cancel?plan_type={{ $planType }}';
                            }
                        }).render('#paypal-button-container');
                    } else {
                        // 일회성 결제 (쿠폰)
                        paypal.Buttons({
                            style: {
                                layout: 'vertical',
                                color: 'blue',
                                shape: 'rect',
                                label: 'paypal'
                            },
                            createOrder: function(data, actions) {
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            currency_code: "USD",
                                            value: totalPrice.toFixed(2)
                                        },
                                        description: "{{ $planData['name'] }} Plan",
                                        custom_id: '{{ $orderId }}'
                                    }],
                                    application_context: {
                                        brand_name: '{{ config("app.name") }}',
                                        locale: 'en-US',
                                        landing_page: 'BILLING',
                                        shipping_preference: 'NO_SHIPPING',
                                        user_action: 'PAY_NOW'
                                    }
                                });
                            },
                            onApprove: function(data, actions) {
                                return actions.order.capture().then(function(details) {
                                    // 일회성 결제 검증
                                    Livewire.dispatch('paypal-payment-verified', {
                                        order_id: details.id
                                    });
                                });
                            },
                            onError: function(err) {
                                console.error('PayPal Error:', err);
                                alert('An error occurred while processing your payment. Please try again later.');
                                window.location.href = '/plan/payment/fail?plan_type={{ $planType }}';
                            },
                            onCancel: function(data) {
                                console.log('Payment cancelled by user');
                                window.location.href = '/plan/payment/cancel?plan_type={{ $planType }}';
                            }
                        }).render('#paypal-button-container');
                    }
                }
            });
        </script>
    @endif
@endsection