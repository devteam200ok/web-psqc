<?php

// app/Livewire/ClientPurchase.php

namespace App\Livewire;

use App\Models\UserPlan;
use App\Models\Api;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientPurchase extends Component
{
    public $planType;
    public $planData;
    public $amount;
    public $orderId;

    // PayPal 관련 속성 (클라이언트 ID만 - Secret은 서버에서만 사용)
    public $paypal_mode;
    public $paypal_client_id;

    protected $listeners = [
        'paypal-payment-verified' => 'verifyPaypalPayment',
        'paypal-subscription-verified' => 'verifyPaypalSubscription',
    ];

    // Define plan information
    protected $planTemplates = [
        'starter' => [
            'name' => 'Starter',
            'price' => 29,
            'is_subscription' => true,
            'monthly_limit' => 600,
            'daily_limit' => 60,
            'description' => 'Up to 600/month · 60/day',
            'features' => ['Great for personal projects', 'Basic member benefits', 'Email alerts'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 69,
            'is_subscription' => true,
            'monthly_limit' => 1500,
            'daily_limit' => 150,
            'description' => 'Up to 1,500/month · 150/day',
            'features' => ['Ideal for SMBs/agencies', 'Includes Starter benefits', 'Scheduled runs + recurring scans'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'agency' => [
            'name' => 'Agency',
            'price' => 199,
            'is_subscription' => true,
            'monthly_limit' => 6000,
            'daily_limit' => 600,
            'description' => 'Up to 6,000/month · 600/day',
            'features' => ['Manage multiple domains/clients', 'Includes Pro benefits', 'White-label reports (customizable certificate logo)'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'test1' => [
            'name' => 'Test1',
            'price' => 5,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => 30,
            'total_limit' => 30,
            'description' => 'Up to 30 uses within 1 day',
            'features' => ['Short burst testing', 'Non-refundable'],
            'validity_days' => 1,
            'refund_days' => 0,
        ],
        'test7' => [
            'name' => 'Test7',
            'price' => 19,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 150,
            'description' => 'Up to 150 uses within 7 days',
            'features' => ['Sprint QA', 'Full refund within 3 days if unused'],
            'validity_days' => 7,
            'refund_days' => 3,
        ],
        'test30' => [
            'name' => 'Test30',
            'price' => 39,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 500,
            'description' => 'Up to 500 uses within 30 days',
            'features' => ['Project stabilization', 'Full refund within 7 days if unused'],
            'validity_days' => 30,
            'refund_days' => 7,
        ],
        'test90' => [
            'name' => 'Test90',
            'price' => 119,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 1300,
            'description' => 'Up to 1,300 uses within 90 days',
            'features' => ['Release readiness', 'Full refund within 30 days if unused'],
            'validity_days' => 90,
            'refund_days' => 30,
        ],
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // PayPal 설정 로드 (Client ID만)
        $api = Api::first();
        $this->paypal_mode = $api->paypal_mode;

        if ($this->paypal_mode == 'live') {
            $this->paypal_client_id = $api->paypal_client_id_live;
        } else {
            $this->paypal_client_id = $api->paypal_client_id_sandbox;
        }

        $this->planType = request('plan');
        
        if (!$this->planType || !isset($this->planTemplates[$this->planType])) {
            return redirect()->route('home')->with('error', 'Invalid plan.');
        }

        $this->planData = $this->planTemplates[$this->planType];
        $this->amount = $this->planData['price'];

        // Check for existing subscription
        if ($this->planData['is_subscription']) {
            $existingSubscription = UserPlan::where('user_id', Auth::id())
                ->subscription()
                ->active()
                ->paid()
                ->first();

            if ($existingSubscription) {
                return redirect()->route('home')->with('error', 'You already have an active subscription plan.');
            }
        }

        $this->orderId = 'PLAN_' . strtoupper($this->planType) . '_' . Auth::id() . '_' . time();
    }

    private function getPaypalAccessToken()
    {
        $api = Api::first();
        
        $baseUrl = $this->paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        // Secret은 서버에서만 사용
        $paypal_secret = $this->paypal_mode == 'live' 
            ? $api->paypal_secret_live 
            : $api->paypal_secret_sandbox;

        $tokenResponse = Http::asForm()
            ->withBasicAuth($this->paypal_client_id, $paypal_secret)
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            Log::error('PayPal token request failed', $tokenResponse->json());
            return null;
        }

        return $tokenResponse->json('access_token');
    }

    // 구독 결제 검증
    public function verifyPaypalSubscription($subscription_id)
    {
        $accessToken = $this->getPaypalAccessToken();
        if (!$accessToken) {
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Payment verification failed. Please try again.');
        }

        $baseUrl = $this->paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        // PayPal Subscription 조회
        $subscriptionResponse = Http::withToken($accessToken)
            ->get("{$baseUrl}/v1/billing/subscriptions/{$subscription_id}");

        if (!$subscriptionResponse->ok()) {
            Log::error('PayPal subscription lookup failed', $subscriptionResponse->json());
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Subscription verification failed. Please try again.');
        }

        $data = $subscriptionResponse->json();

        // 구독 상태 확인
        if ($data['status'] !== 'ACTIVE') {
            Log::error('PayPal subscription not active', $data);
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Subscription is not active. Please try again.');
        }

        // UserPlan 생성 (구독용)
        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addMonth();
        $refundDeadline = $startDate->copy()->addDays($this->planData['refund_days']);

        $userPlan = UserPlan::create([
            'user_id' => Auth::id(),
            'plan_type' => $this->planType,
            'customerName' => $data['subscriber']['name']['given_name'] . ' ' . $data['subscriber']['name']['surname'],
            'customerEmail' => $data['subscriber']['email_address'],
            'paypal_subscription_id' => $subscription_id,
            'paypal_payer_id' => $data['subscriber']['payer_id'],
            'paypal_email' => $data['subscriber']['email_address'],
            'paypal_name' => $data['subscriber']['name']['given_name'] . ' ' . $data['subscriber']['name']['surname'],
            'paypal_amount' => $this->amount,
            'paypal_currency' => 'USD',
            'paypal_paid_at' => now(),
            'payment_status' => 'paid',
            'is_subscription' => true,
            'price' => $this->amount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_limit' => $this->planData['monthly_limit'],
            'daily_limit' => $this->planData['daily_limit'],
            'status' => 'active',
            'auto_renew' => true,
            'is_refundable' => true,
            'refund_deadline' => $refundDeadline,
        ]);

        return redirect(url('/') . '/client/plan')->with('success', 'Subscription activated successfully!');
    }

    // 일회성 결제 검증 (쿠폰용)
    public function verifyPaypalPayment($order_id)
    {
        $accessToken = $this->getPaypalAccessToken();
        if (!$accessToken) {
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Payment verification failed. Please try again.');
        }

        $baseUrl = $this->paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        // PayPal Order Lookup
        $orderResponse = Http::withToken($accessToken)
            ->get("{$baseUrl}/v2/checkout/orders/{$order_id}");

        if (!$orderResponse->ok()) {
            Log::error('PayPal order lookup failed', $orderResponse->json());
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Payment verification failed. Please try again.');
        }

        $data = $orderResponse->json();

        // 검증 1: 상태 확인
        if ($data['status'] !== 'COMPLETED') {
            Log::error('PayPal payment not completed', $data);
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Payment was not completed. Please try again.');
        }

        // 검증 2: 금액/통화 확인
        $expectedAmount = number_format($this->amount, 2, '.', '');
        $actualAmount = $data['purchase_units'][0]['amount']['value'];
        $currency = $data['purchase_units'][0]['amount']['currency_code'];

        if ($actualAmount != $expectedAmount || $currency !== 'USD') {
            Log::error('PayPal payment amount mismatch', [
                'expected' => $expectedAmount,
                'actual' => $actualAmount,
                'currency' => $currency,
            ]);
            return redirect(url('/') . '/client/purchase?plan=' . $this->planType)->with('error', 'Payment amount verification failed. Please try again.');
        }

        // UserPlan 생성 (쿠폰용)
        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addDays($this->planData['validity_days']);

        $refundDeadline = null;
        if ($this->planData['refund_days'] > 0) {
            $refundDeadline = $startDate->copy()->addDays($this->planData['refund_days']);
        }

        $userPlan = UserPlan::create([
            'user_id' => Auth::id(),
            'plan_type' => $this->planType,
            'customerName' => $data['payer']['name']['given_name'] . ' ' . $data['payer']['name']['surname'],
            'customerEmail' => $data['payer']['email_address'],
            'paypal_order_id' => $data['id'],
            'paypal_payer_id' => $data['payer']['payer_id'],
            'paypal_email' => $data['payer']['email_address'],
            'paypal_name' => $data['payer']['name']['given_name'] . ' ' . $data['payer']['name']['surname'],
            'paypal_amount' => $actualAmount,
            'paypal_currency' => $currency,
            'paypal_paid_at' => now(),
            'payment_status' => 'paid',
            'is_subscription' => false,
            'price' => $this->amount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily_limit' => $this->planData['daily_limit'],
            'total_limit' => $this->planData['total_limit'],
            'status' => 'active',
            'auto_renew' => false,
            'is_refundable' => $this->planData['refund_days'] > 0,
            'refund_deadline' => $refundDeadline,
        ]);

        return redirect(url('/') . '/client/plan')->with('success', 'Payment completed successfully!');
    }

    public function purchaseForFree()
    {
        if ($this->amount != 0) {
            return;
        }

        // 무료 플랜 처리
        $startDate = Carbon::now();
        $endDate = $this->planData['is_subscription'] 
            ? $startDate->copy()->addMonth() 
            : $startDate->copy()->addDays($this->planData['validity_days']);

        $userPlan = UserPlan::create([
            'user_id' => Auth::id(),
            'plan_type' => $this->planType,
            'customerName' => Auth::user()->name,
            'customerEmail' => Auth::user()->email,
            'payment_status' => 'paid',
            'is_subscription' => $this->planData['is_subscription'],
            'price' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_limit' => $this->planData['monthly_limit'],
            'daily_limit' => $this->planData['daily_limit'],
            'total_limit' => $this->planData['total_limit'],
            'status' => 'active',
            'auto_renew' => $this->planData['is_subscription'],
            'is_refundable' => false,
        ]);

        return redirect(url('/') . '/client/plan')->with('success', 'Free plan activated successfully!');
    }

    public function render()
    {
        $api = Api::first();
        
        return view('livewire.client-purchase', [
            'api' => $api
        ])->layout('layouts.app');
    }
}