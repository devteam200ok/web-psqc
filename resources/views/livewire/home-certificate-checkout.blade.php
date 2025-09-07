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
                <h2 class="page-title">Certificate Payment and Issuance</h2>
                <div class="text-muted">This is the payment page for issuing Web Test Certificate.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 offset-lg-3 mb-2">
                <!-- Certificate Information -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-blue-lt text-blue-lt-fg me-1">{{ $certificate->test_type_name }}</span>
                            <span class="{{ $certificate->grade_color }}">{{ $certificate->overall_grade }}</span>
                        </div>
                        <h3 class="page-title">Web Test Certificate</h3>
                        <div class="page-pretitle">{{ $certificate->url }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📄 Certificate Contents</h6>
                                    <ul class="mb-0">
                                        <li>Test result grade: {{ $certificate->overall_grade }}</li>
                                        <li>Test score: {{ $certificate->formatted_score }}</li>
                                        <li>Test date: {{ $certificate->webTest->finished_at ? $certificate->webTest->finished_at->format('Y-m-d H:i:s') : $certificate->webTest->updated_at->format('Y-m-d H:i:s') }}</li>
                                        <li>Online verification via QR code</li>
                                        <li>PDF download and printable</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h4>Certificate Issuance Fee</h4>
                                <span class="h4">{{ number_format($amount) }} KRW</span>
                            </div>
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
    <input type="hidden" id="orderName" value="Web Test Certificate ({{ $certificate->url }})">
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
                successUrl: window.location.origin + "/certificate/payment/success?certificate_id=" + certificateId,
                failUrl: window.location.origin + "/certificate/payment/fail?certificate_id=" + certificateId,
                customerEmail: customerEmail,
                customerName: customerName,
                customerMobilePhone: customer_phone,
            });
        });
    </script>
@endsection