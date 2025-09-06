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
                <div class="text-muted">PSQC 종합 인증서 (PSQC Master Certificate) 발급을 위한 결제 페이지입니다.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2 mb-2">
                <!-- 인증서 정보 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-blue-lt text-blue-lt-fg me-1">PSQC Master Certificate</span>
                            <span class="{{ $certification->grade_color }}">{{ $certification->overall_grade }}</span>
                        </div>
                        <h3 class="page-title">PSQC 종합 인증서</h3>
                        <div class="page-pretitle">{{ $certification->url }}</div>
                        
                        <div class="my-3">
                            <div class="alert alert-info">
                                <div>
                                    <h6>📄 인증서에 포함되는 내용</h6>
                                    <ul class="mb-0">
                                        <li>테스트 결과 등급: {{ $certification->overall_grade }}</li>
                                        <li>테스트 점수: {{ $certification->formatted_score }}</li>
                                        <li>QR 코드를 통한 온라인 검증</li>
                                        <li>PDF 다운로드 및 인쇄 가능</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        
                        <!-- 16개 테스트 상세 정보 -->
                        <h5 class="mb-3">테스트 구성 상세</h5>
                        <div class="row g-2">
                            @foreach ($testDetails as $groupLabel => $tests)
                                <div class="col-12 col-md-6 col-xl-3">
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
                                <h4>종합 점수</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="{{ $certification->grade_color }} h4">
                                        {{ $certification->overall_grade }} 등급
                                    </span>
                                    <span class="h4">
                                        ({{ number_format($certification->overall_score, 1) }} / 1000점)
                                    </span>
                                </div>
                            </div>
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
    <input type="hidden" id="orderName" value="PSQC 종합 인증서({{ $certification->url }})">
    <input type="hidden" id="totalPrice" value="{{ $amount }}">
    <input type="hidden" id="orderId" value="{{ $orderId }}">
    <input type="hidden" id="certificationId" value="{{ $certification->id }}">
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
        const certificationId = document.getElementById("certificationId").value;
        
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
                successUrl: window.location.origin + "/psqc/payment/success?certification_id=" + certificationId,
                failUrl: window.location.origin + "/psqc/payment/fail?certification_id=" + certificationId,
                customerEmail: customerEmail,
                customerName: customerName,
                customerMobilePhone: customer_phone,
            });
        });
    </script>
@endsection