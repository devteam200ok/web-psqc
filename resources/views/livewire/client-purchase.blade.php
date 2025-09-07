@section('title')
    @include('inc.component.seo')
@endsection
@section('css')
    <script src="https://js.tosspayments.com/v1/payment-widget"></script>
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
                                            <li>Automatically charged monthly</li>
                                            <li>Full refund available within 7 days before use</li>
                                            <li>No refund once used</li>
                                            <li>Can cancel subscription anytime</li>
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
                                <span class="h4">{{ number_format($amount) }} KRW{{ $planData['is_subscription'] ? '/month' : '' }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div>
                            <h4 class="mt-3">Total Payment Amount</h4>
                            <span class="h4">{{ number_format($amount) }} KRW</span>
                        </div>
                    </div>
                </div>

                <!-- Toss Payment Widget -->
                <div id="payment-method"></div>
                <div id="agreement"></div>
                
                <div class="mt-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control mb-3" id="customerName"
                                placeholder="Enter your name" style="font-size:16px" 
                                value="{{ Auth::user()->name }}">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control mb-3" id="customerPhone"
                                placeholder="Enter your phone number" style="font-size:16px">
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 btn-lg" id="tossPaymentButton">
                        Pay {{ number_format($amount) }} KRW
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toss Payment Configuration -->
    @if ($api->toss_mode == 'live')
        <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key }}">
    @else
        <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key_test }}">
    @endif
    <input type="hidden" id="customerKey" value="WP{{ Auth::user()->id }}">
    <input type="hidden" id="customerEmail" value="{{ Auth::user()->email }}">
    <input type="hidden" id="orderName" value="{{ $planData['name'] }} Plan">
    <input type="hidden" id="totalPrice" value="{{ $amount }}">
    <input type="hidden" id="orderId" value="{{ $orderId }}">
    <input type="hidden" id="planType" value="{{ $planType }}">
</div>

@section('js')
    <script>
        const button = document.getElementById("tossPaymentButton");
        
        const widgetClientKey = document.getElementById("widgetClientKey").value;
        const customerKey = document.getElementById("customerKey").value;
        const customerEmail = document.getElementById("customerEmail").value;
        
        const totalPrice = document.getElementById("totalPrice").value;
        const orderName = document.getElementById("orderName").value;
        const orderId = document.getElementById("orderId").value;
        const planType = document.getElementById("planType").value;
        
        const paymentWidget = PaymentWidget(widgetClientKey, customerKey);
        
        const paymentMethodWidget = paymentWidget.renderPaymentMethods(
            "#payment-method", {
                value: totalPrice,
            }, {
                variantKey: "DEFAULT"
            }
        );
        
        paymentWidget.renderAgreement(
            "#agreement", {
                variantKey: "AGREEMENT"
            }
        );
        
        button.addEventListener("click", function() {
            var customerName = document.getElementById("customerName").value;
            var customer_phone = document.getElementById("customerPhone").value;
            customer_phone = customer_phone.replace(/-/g, '');
            
            if (!customerName || !customer_phone) {
                alert("Please enter your name and phone number.");
                return;
            }
            
            if (!/^\d{10,13}$/.test(customer_phone)) {
                alert("Phone number must be between 10 and 13 digits.");
                return;
            }
            
            paymentWidget.requestPayment({
                orderId: orderId,
                orderName: orderName,
                successUrl: window.location.origin + "/plan/payment/success?plan_type=" + planType + "&customerName=" + encodeURIComponent(customerName) + "&customerEmail=" + encodeURIComponent(customerEmail) + "&customerMobilePhone=" + encodeURIComponent(customer_phone),
                failUrl: window.location.origin + "/plan/payment/fail?plan_type=" + planType,
                customerEmail: customerEmail,
                customerName: customerName,
                customerMobilePhone: customer_phone,
            });
        });
    </script>
@endsection