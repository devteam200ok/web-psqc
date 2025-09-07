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
                <h2 class="page-title">Certificate Payment & Issuance</h2>
                <div class="text-muted">Payment page for issuing the PSQC Comprehensive Certificate (PSQC Master Certificate).</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 offset-lg-3 mb-2">
                <!-- Certificate information -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-blue-lt text-blue-lt-fg me-1">PSQC Master Certificate</span>
                            <span class="{{ $certification->grade_color }}">{{ $certification->overall_grade }}</span>
                        </div>
                        <h3 class="page-title">PSQC Comprehensive Certificate</h3>
                        <div class="page-pretitle">{{ $certification->url }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📄 Certificate Contents</h6>
                                    <ul class="mb-0">
                                        <li>Test result grade: {{ $certification->overall_grade }}</li>
                                        <li>Test score: {{ $certification->formatted_score }}</li>
                                        <li>Online verification via QR code</li>
                                        <li>PDF download and print available</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        
                        <!-- 16 test detailed information -->
                        <h5 class="mb-3">Test Composition Details</h5>
                        <div class="row g-2">
                            @foreach ($testDetails as $groupLabel => $tests)
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0 small">{{ $groupLabel }}</h6>
                                        </div>
                                        <div class="card-body py-2">
                                            @foreach ($tests as $test)
                                                <div class="d-flex justify-content-between align-items-center py-1">
                                                    <div class="text-muted small">{{ $test['label'] }}</div>
                                                    <div>
                                                        @if ($test['grade'])
                                                            <span class="badge {{ $test['grade_color'] }}">
                                                                {{ $test['grade'] }}
                                                                @if ($test['score'])
                                                                    ({{ number_format($test['score'], 1) }})
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h4>Overall Score</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="{{ $certification->grade_color }} h4">
                                        Grade {{ $certification->overall_grade }}
                                    </span>
                                    <span class="h4">
                                        ({{ number_format($certification->overall_score, 1) }} / 1000 points)
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h4>Certificate Issuance Cost</h4>
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
    <input type="hidden" id="certificationId" value="{{ $certification->id }}">
</div>

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sdkLoaded = false;
            const totalPrice = parseFloat(document.getElementById('totalPrice').value || 0);
            const orderId = document.getElementById('orderId').value;
            const certificationId = document.getElementById('certificationId').value;

            if (!sdkLoaded) {
                sdkLoaded = true;
                const script = document.createElement('script');
                script.src = 'https://www.paypal.com/sdk/js?client-id={{ $paypal_client_id }}&components=buttons&currency=USD';
                script.onload = () => renderPayPalButtons(totalPrice, orderId, certificationId);
                document.body.appendChild(script);
            } else {
                renderPayPalButtons(totalPrice, orderId, certificationId);
            }

            function renderPayPalButtons(totalPrice, orderId, certificationId) {
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
                                description: "PSQC Comprehensive Certificate ({{ $certification->url }})",
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
                            Livewire.dispatch('paypal-psqc-payment-verified', {
                                order_id: details.id
                            });
                        });
                    },
                    onError: function(err) {
                        console.error('PayPal Error:', err);
                        alert('An error occurred while processing your payment. Please try again later.');
                        window.location.href = '{{ route('psqc.checkout', ['certificate' => $certification->id]) }}';
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