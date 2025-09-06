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
                <h2 class="page-title">인증서 결제 및 발급</h2>
                <div class="text-muted">웹 테스트 인증서 (Web Test Certificate) 발급을 위한 결제 페이지입니다.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 offset-lg-3 mb-2">
                <!-- 인증서 정보 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-blue-lt text-blue-lt-fg me-1">{{ $certificate->test_type_name }}</span>
                            <span class="{{ $certificate->grade_color }}">{{ $certificate->overall_grade }}</span>
                        </div>
                        <h3 class="page-title">웹 테스트 인증서 (Web Test Certificate)</h3>
                        <div class="page-pretitle">{{ $certificate->url }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📄 인증서에 포함되는 내용</h6>
                                    <ul class="mb-0">
                                        <li>테스트 결과 등급: {{ $certificate->overall_grade }}</li>
                                        <li>테스트 점수: {{ $certificate->formatted_score }}</li>
                                        <li>테스트 일시: {{ $certificate->webTest->finished_at ? $certificate->webTest->finished_at->format('Y-m-d H:i:s') : $certificate->webTest->updated_at->format('Y-m-d H:i:s') }}</li>
                                        <li>QR 코드를 통한 온라인 검증</li>
                                        <li>PDF 다운로드 및 인쇄 가능</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h4>인증서 발급 비용</h4>
                                <span class="h4">{{ number_format($amount) }} 원</span>
                            </div>
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
    <input type="hidden" id="orderName" value="웹 테스트 인증서({{ $certificate->url }})">
    <input type="hidden" id="totalPrice" value="{{ $amount }}">
    <input type="hidden" id="orderId" value="{{ $orderId }}">
    <input type="hidden" id="certificateId" value="{{ $certificate->id }}">
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
        const certificateId = document.getElementById("certificateId").value;
        
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
                successUrl: window.location.origin + "/certificate/payment/success?certificate_id=" + certificateId,
                failUrl: window.location.origin + "/certificate/payment/fail?certificate_id=" + certificateId,
                customerEmail: customerEmail,
                customerName: customerName,
                customerMobilePhone: customer_phone,
            });
        });
    </script>
@endsection