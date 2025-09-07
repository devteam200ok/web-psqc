<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PsqcCertification;
use App\Models\WebTest;
use App\Models\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PsqcController extends Controller
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
       $certificationId = $request->get('certification_id');

       $certification = PsqcCertification::where('id', $certificationId)
           ->where('user_id', Auth::id())
           ->where('payment_status', 'pending')
           ->first();

       if (!$certification) {
           return redirect()->route('home')->with('error', 'Certification not found.');
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
           // Payment successful - Update certification status
           $certification->markAsPaid([
               'payment_type' => $paymentType,
               'payment_key' => $paymentKey,
               'order_id' => $orderId,
               'amount' => $amount,
               'toss_response' => $response->json()
           ]);
           // WebTest connection is automatically handled within the markAsPaid method

           // Generate certification PDF
            $this->generatePsqcPdf($certification->code);

           return redirect()->route('client.psqc')
               ->with('success', 'Payment completed successfully. Your certification has been issued.');
       } else {
           // Payment failed
           $certification->markAsFailed([
               'error_response' => $response->json(),
               'status_code' => $statusCode
           ]);

           return redirect()->route('psqc.checkout', ['certificate' => $certification->id])
               ->with('error', 'Payment failed. Please try again.');
       }
   }

   public function paymentFail(Request $request)
   {
       $certificationId = $request->get('certification_id');

       if ($certificationId && Auth::check()) {
           $certification = PsqcCertification::where('id', $certificationId)
               ->where('user_id', Auth::id())
               ->first();

           if ($certification) {
               $certification->markAsFailed(['reason' => 'user_cancelled']);
           }
       }

       return redirect()->route('home')->with('error', 'Payment has been cancelled.');
   }

   public function generatePsqcPdf($code)
   {
       \Illuminate\Support\Facades\Artisan::call('cert:make-psqc-pdf', [
           'code'    => $code,
           '--force' => true,
       ]);
   }
}