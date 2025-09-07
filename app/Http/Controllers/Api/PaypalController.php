<?php

// app/Http/Controllers/Api/PaypalController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Api;

class PaypalController extends Controller
{
    protected $planTemplates = [
        'starter' => [
            'name' => 'Starter',
            'price' => 29,
            'description' => 'Up to 600/month · 60/day',
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 69,
            'description' => 'Up to 1,500/month · 150/day',
        ],
        'agency' => [
            'name' => 'Agency',
            'price' => 199,
            'description' => 'Up to 6,000/month · 600/day',
        ],
    ];

    public function getPlanId(Request $request)
    {
        $planType = $request->input('plan_type');
        
        if (!isset($this->planTemplates[$planType])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid plan type'
            ], 400);
        }

        $planData = $this->planTemplates[$planType];
        $planId = $this->getOrCreatePaypalPlan($planType, $planData);

        if (!$planId) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create PayPal plan'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'plan_id' => $planId
        ]);
    }

    private function getOrCreatePaypalPlan($planType, $planData)
    {
        $api = Api::first();
        $paypal_mode = $api->paypal_mode;
        
        $baseUrl = $paypal_mode == 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        $paypal_client_id = $paypal_mode == 'live' 
            ? $api->paypal_client_id_live 
            : $api->paypal_client_id_sandbox;

        $paypal_secret = $paypal_mode == 'live' 
            ? $api->paypal_secret_live 
            : $api->paypal_secret_sandbox;

        // Get access token
        $tokenResponse = Http::asForm()
            ->withBasicAuth($paypal_client_id, $paypal_secret)
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            Log::error('PayPal token request failed', $tokenResponse->json());
            return null;
        }

        $accessToken = $tokenResponse->json('access_token');
        $productId = 'PLAN_' . strtoupper($planType);

        // 1. Product 생성 (이미 존재해도 무시)
        Http::withToken($accessToken)
            ->post("{$baseUrl}/v1/catalogs/products", [
                'id' => $productId,
                'name' => $planData['name'] . ' Plan',
                'description' => $planData['description'],
                'type' => 'SERVICE',
                'category' => 'SOFTWARE'
            ]);

        // 2. 기존 플랜 조회
        $existingPlansResponse = Http::withToken($accessToken)
            ->get("{$baseUrl}/v1/billing/plans", [
                'product_id' => $productId,
                'page_size' => 20
            ]);

        if ($existingPlansResponse->ok()) {
            $plans = $existingPlansResponse->json('plans', []);
            foreach ($plans as $plan) {
                if ($plan['status'] === 'ACTIVE') {
                    Log::info('Using existing PayPal plan', ['plan_id' => $plan['id'], 'plan_type' => $planType]);
                    return $plan['id'];
                }
            }
        }

        // 3. 새 플랜 생성
        $planResponse = Http::withToken($accessToken)
            ->post("{$baseUrl}/v1/billing/plans", [
                'product_id' => $productId,
                'name' => $planData['name'] . ' Monthly Plan',
                'description' => $planData['description'],
                'status' => 'ACTIVE',
                'billing_cycles' => [
                    [
                        'frequency' => [
                            'interval_unit' => 'MONTH',
                            'interval_count' => 1
                        ],
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => 0,
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => number_format($planData['price'], 2, '.', ''),
                                'currency_code' => 'USD'
                            ]
                        ]
                    ]
                ],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee' => [
                        'value' => '0',
                        'currency_code' => 'USD'
                    ],
                    'setup_fee_failure_action' => 'CONTINUE',
                    'payment_failure_threshold' => 3
                ]
            ]);

        if (!$planResponse->ok()) {
            Log::error('PayPal plan creation failed', [
                'plan_type' => $planType,
                'response' => $planResponse->json()
            ]);
            return null;
        }

        $planId = $planResponse->json('id');
        Log::info('Created new PayPal plan', ['plan_id' => $planId, 'plan_type' => $planType]);
        
        return $planId;
    }
}