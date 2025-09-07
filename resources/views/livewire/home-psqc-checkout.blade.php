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
               <h2 class="page-title">Certificate Payment & Issuance</h2>
               <div class="text-muted">Payment page for issuing the PSQC Comprehensive Certificate (PSQC Master Certificate).</div>
           </div>
       </div>

       <div class="row">
           <div class="col-lg-8 offset-lg-2 mb-2">
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
                               <span class="h4">{{ number_format($amount) }} KRW</span>
                           </div>
                       </div>
                   </div>
               </div>

               <!-- Toss payment widget -->
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

   <!-- Toss payment configuration -->
   @if ($api->toss_mode == 'live')
       <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key }}">
   @else
       <input type="hidden" id="widgetClientKey" value="{{ $api->toss_client_key_test }}">
   @endif
   <input type="hidden" id="customerKey" value="DT{{ Auth::user()->id }}">
   <input type="hidden" id="customerEmail" value="{{ Auth::user()->email }}">
   <input type="hidden" id="orderName" value="PSQC Comprehensive Certificate({{ $certification->url }})">
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
               successUrl: window.location.origin + "/psqc/payment/success?certification_id=" + certificationId,
               failUrl: window.location.origin + "/psqc/payment/fail?certification_id=" + certificationId,
               customerEmail: customerEmail,
               customerName: customerName,
               customerMobilePhone: customer_phone,
           });
       });
   </script>
@endsection