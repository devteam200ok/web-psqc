@section('title')
    @include('inc.component.seo')
@endsection
@section('css')
    <script src="https://js.tosspayments.com/v1/payment-widget"></script>
@endsection

<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <!-- 헤더 -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="page-title">{{ $planData['name'] }} 플랜 결제</h2>
                <div class="text-muted">{{ $planData['is_subscription'] ? '구독' : '쿠폰' }} 플랜 구매를 위한 결제 페이지입니다.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 offset-lg-3 mb-2">
                <!-- 플랜 정보 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge {{ $planData['is_subscription'] ? 'bg-blue-lt text-blue' : 'bg-green-lt text-green' }} me-1">
                                {{ $planData['is_subscription'] ? '구독' : '쿠폰' }}
                            </span>
                        </div>
                        <h3 class="page-title">{{ $planData['name'] }} 플랜</h3>
                        <div class="page-pretitle">{{ $planData['description'] }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📋 플랜에 포함되는 혜택</h6>
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
                                        <h6>⚠️ 구독 안내</h6>
                                        <ul class="mb-0">
                                            <li>매월 자동 결제됩니다</li>
                                            <li>사용 전 7일 이내 전액 환불 가능</li>
                                            <li>1회라도 사용 시 환불 불가</li>
                                            <li>언제든지 구독 취소 가능</li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <div>
                                        <h6>⚠️ 쿠폰 안내</h6>
                                        <ul class="mb-0">
                                            <li>{{ $planData['validity_days'] }}일 이내 사용 가능</li>
                                            @if($planData['refund_days'] > 0)
                                                <li>사용 전 {{ $planData['refund_days'] }}일 이내 전액 환불 가능</li>
                                            @else
                                                <li>환불 불가</li>
                                            @endif
                                            <li>1회라도 사용 시 환불 불가</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h4>{{ $planData['name'] }} 플랜</h4>
                                <span class="h4">{{ number_format($amount) }} 원{{ $planData['is_subscription'] ? '/월' : '' }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div>
                            <h4 class="mt-3">총 결제 금액</h4>
                            <span class="h4">{{ number_format($amount) }} 원</span>
                        </div>
                    </div>
                </div>

                <!-- 토스 결제 위젯 -->
                <div id="payment-method"></div>
                <div id="agreement"></div>
                
                <div class="mt-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">이름</label>
                            <input type="text" class="form-control mb-3" id="customerName"
                                placeholder="이름을 입력하세요" style="font-size:16px" 
                                value="{{ Auth::user()->name }}">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">전화번호</label>
                            <input type="text" class="form-control mb-3" id="customerPhone"
                                placeholder="전화번호를 입력하세요" style="font-size:16px">
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 btn-lg" id="tossPaymentButton">
                        {{ number_format($amount) }}원 결제하기
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 토스 결제 설정 -->
    @if ($api->toss_mode == 'live')
        <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key }}">
    @else
        <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key_test }}">
    @endif
    <input type="hidden" id="customerKey" value="DT{{ Auth::user()->id }}">
    <input type="hidden" id="customerEmail" value="{{ Auth::user()->email }}">
    <input type="hidden" id="orderName" value="{{ $planData['name'] }} 플랜">
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
                alert("이름과 전화번호를 입력해주세요.");
                return;
            }
            
            if (!/^\d{10,13}$/.test(customer_phone)) {
                alert("전화번호는 10자리에서 13자리 사이의 숫자여야 합니다.");
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