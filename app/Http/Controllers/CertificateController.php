<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\WebTest;
use App\Models\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
   public function paymentSuccess(Request $request)
   {
       if (!Auth::check()) {
           return redirect()->route('home')->with('error', 'Login is required.');
       }

       $paymentType = $request->get('paymentType');
       $orderId = $request->get('orderId');
       $paymentKey = $request->get('paymentKey');
       $amount = $request->get('amount');
       $certificateId = $request->get('certificate_id');

       $certificate = Certificate::where('id', $certificateId)
           ->where('user_id', Auth::id())
           ->where('payment_status', 'pending')
           ->first();

       if (!$certificate) {
           return redirect()->route('home')->with('error', 'Certificate not found.');
       }

       $api = Api::first();
       $toss_secret_key = $api->toss_mode == 'live' 
           ? $api->toss_secret_key 
           : $api->toss_secret_key_test;
       $toss_secret_key = base64_encode($toss_secret_key . ':');

       $apiURL = 'https://api.tosspayments.com/v1/payments/confirm';
       $headers = [
           'Authorization' => 'Basic ' . $toss_secret_key,
           'Content-Type' => 'application/json',
       ];

       $postInput = [
           'paymentKey' => $paymentKey,
           'orderId' => $orderId,
           'amount' => $amount
       ];

       $response = Http::withHeaders($headers)->post($apiURL, $postInput);
       $statusCode = $response->status();

       if ($statusCode == 200) {
           // Payment successful - Update certificate status
           $certificate->markAsPaid([
               'payment_type' => $paymentType,
               'payment_key' => $paymentKey,
               'order_id' => $orderId,
               'amount' => $amount,
               'toss_response' => $response->json()
           ]);

           $webTest = WebTest::find($certificate->web_test_id);
           if ($webTest) {
               $webTest->is_saved_permanently = true;
               $webTest->save();
           }

           // Generate certificate PDF
           $this->generateCertificatePdf($certificate->code);

           return redirect()->route('client.certificate')
               ->with('success', 'Payment completed successfully. Your certificate has been issued.');
       } else {
           // Payment failed
           $certificate->markAsFailed([
               'error_response' => $response->json(),
               'status_code' => $statusCode
           ]);

           return redirect()->route('certificate.checkout', ['certificate' => $certificate->id])
               ->with('error', 'Payment failed. Please try again.');
       }
   }

   public function paymentFail(Request $request)
   {
       $certificateId = $request->get('certificate_id');
       
       if ($certificateId && Auth::check()) {
           $certificate = Certificate::where('id', $certificateId)
               ->where('user_id', Auth::id())
               ->first();
               
           if ($certificate) {
               $certificate->markAsFailed(['reason' => 'user_cancelled']);
           }
       }

       return redirect()->route('home')->with('error', 'Payment has been cancelled.');
   }

   public function generateCertificatePdf($code)
   {
       \Illuminate\Support\Facades\Artisan::call('cert:make-pdf', [
           'code'    => $code,
           '--force' => true,
       ]);
   }
}