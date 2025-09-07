<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPlan;
use App\Models\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanController extends Controller
{
   protected $planTemplates = [
       'starter' => [
           'name' => 'Starter', 'price' => 29, 'is_subscription' => true,
           'monthly_limit' => 600, 'daily_limit' => 60, 'total_limit' => null,
           'validity_days' => null, 'refund_days' => 7,
       ],
       'pro' => [
           'name' => 'Pro', 'price' => 69, 'is_subscription' => true,
           'monthly_limit' => 1500, 'daily_limit' => 150, 'total_limit' => null,
           'validity_days' => null, 'refund_days' => 7,
       ],
       'agency' => [
           'name' => 'Agency', 'price' => 199, 'is_subscription' => true,
           'monthly_limit' => 6000, 'daily_limit' => 600, 'total_limit' => null,
           'validity_days' => null, 'refund_days' => 7,
       ],
       'test1' => [
           'name' => 'Test1', 'price' => 5, 'is_subscription' => false,
           'monthly_limit' => null, 'daily_limit' => 30, 'total_limit' => 30,
           'validity_days' => 1, 'refund_days' => 0,
       ],
       'test7' => [
           'name' => 'Test7', 'price' => 19, 'is_subscription' => false,
           'monthly_limit' => null, 'daily_limit' => null, 'total_limit' => 150,
           'validity_days' => 7, 'refund_days' => 3,
       ],
       'test30' => [
           'name' => 'Test30', 'price' => 39, 'is_subscription' => false,
           'monthly_limit' => null, 'daily_limit' => null, 'total_limit' => 500,
           'validity_days' => 30, 'refund_days' => 7,
       ],
       'test90' => [
           'name' => 'Test90', 'price' => 119, 'is_subscription' => false,
           'monthly_limit' => null, 'daily_limit' => null, 'total_limit' => 1300,
           'validity_days' => 90, 'refund_days' => 30,
       ],
   ];

   public function paymentSuccess(Request $request)
   {
       if (!Auth::check()) {
           return redirect()->route('home')->with('error', 'Login is required.');
       }

       $paymentType = $request->get('paymentType');
       $orderId = $request->get('orderId');
       $paymentKey = $request->get('paymentKey');
       $amount = $request->get('amount');
       $planType = $request->get('plan_type');
       $customerName = $request->get('customerName');
       $customerEmail = $request->get('customerEmail');
       $customerPhone = $request->get('customerMobilePhone');

       if (!isset($this->planTemplates[$planType])) {
           return redirect()->route('home')->with('error', 'Invalid plan selected.');
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
           // Payment successful - Create UserPlan
           $planTemplate = $this->planTemplates[$planType];
           $now = Carbon::now();
           
           $endDate = $planTemplate['is_subscription'] 
               ? $now->copy()->addMonth() 
               : $now->copy()->addDays($planTemplate['validity_days']);
               
           $refundDeadline = $planTemplate['refund_days'] > 0 
               ? $now->copy()->addDays($planTemplate['refund_days'])
               : null;

           UserPlan::create([
               'user_id' => Auth::id(),
               'plan_type' => $planType,
               'customerKey' => 'WP' . Auth::id(),
               'customerName' => $customerName,
               'customerEmail' => $customerEmail,
               'customerPhone' => $customerPhone,
               'is_subscription' => $planTemplate['is_subscription'],
               'price' => $planTemplate['price'],
               'start_date' => $now,
               'end_date' => $endDate,
               'monthly_limit' => $planTemplate['monthly_limit'],
               'daily_limit' => $planTemplate['daily_limit'],
               'total_limit' => $planTemplate['total_limit'],
               'auto_renew' => $planTemplate['is_subscription'],
               'refund_deadline' => $refundDeadline,
           ]);

           return redirect()->route('client.plan')
               ->with('success', $planTemplate['name'] . ' plan purchase completed successfully.');
       } else {
           return redirect()->route('client.purchase', ['plan' => $planType])
               ->with('error', 'Payment failed. Please try again.');
       }
   }

   public function paymentFail(Request $request)
   {
       $planType = $request->get('plan_type');
       
       return redirect()->route('client.purchase', ['plan' => $planType])
           ->with('error', 'Payment has been cancelled.');
   }
}