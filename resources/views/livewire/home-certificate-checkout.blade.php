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
                                <span class="h4">${{ number_format($amount) }} USD</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div>
                            <h4 class="mt-3">Total Payment Amount</h4>
                            <span class="h4">${{ number_format($amount) }} USD</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="mt-3">
                    <div id="paypal-button-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden inputs for PayPal -->
    <input type="hidden" id="totalPrice" value="{{ $amount }}">
    <input type="hidden" id="orderId" value="{{ $orderId }}">
    <input type="hidden" id="certificateId" value="{{ $certificate->id }}">
</div>

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sdkLoaded = false;
            const totalPrice = parseFloat(document.getElementById('totalPrice').value || 0);
            const orderId = document.getElementById('orderId').value;
            const certificateId = document.getElementById('certificateId').value;

            if (!sdkLoaded) {
                sdkLoaded = true;
                const script = document.createElement('script');
                script.src = 'https://www.paypal.com/sdk/js?client-id={{ $paypal_client_id }}&components=buttons&currency=USD';
                script.onload = () => renderPayPalButtons(totalPrice, orderId, certificateId);
                document.body.appendChild(script);
            } else {
                renderPayPalButtons(totalPrice, orderId, certificateId);
            }

            function renderPayPalButtons(totalPrice, orderId, certificateId) {
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
                                description: "Web Test Certificate ({{ $certificate->url }})",
                                custom_id: orderId
                            }],
                            application_context: {
                                brand_name: '{{ config('app.name') }}',
                                locale: 'en-US',
                                landing_page: 'BILLING',
                                shipping_preference: 'NO_SHIPPING',
                                user_action: 'PAY_NOW'
                            }
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            // Livewire로 결제 검증 요청
                            Livewire.dispatch('paypal-certificate-payment-verified', {
                                order_id: details.id
                            });
                        });
                    },
                    onError: function(err) {
                        console.error('PayPal Error:', err);
                        alert('An error occurred while processing your payment. Please try again later.');
                        window.location.href = '{{ route('certificate.checkout', ['certificate' => $certificate->id]) }}';
                    },
                    onCancel: function(data) {
                        console.log('Payment cancelled by user');
                        // 취소시 홈으로 리다이렉트하거나 현재 페이지에 남을 수 있습니다
                    }
                }).render('#paypal-button-container');
            }
        });
    </script>
@endsection