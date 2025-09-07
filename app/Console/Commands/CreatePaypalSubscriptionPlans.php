<?php

// app/Console/Commands/CreatePaypalSubscriptionPlans.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Api;

class CreatePaypalSubscriptionPlans extends Command
{
    protected $signature = 'paypal:create-subscription-plans';
    protected $description = 'Create PayPal subscription plans for all subscription-based plans';

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

    public function handle()
    {
        $api = Api::first();
        
        if (!$api) {
            $this->error('API configuration not found!');
            return 1;
        }

        $paypal_mode = $api->paypal_mode;
        
        if ($paypal_mode == 'live') {
            $paypal_client_id = $api->paypal_client_id_live;
            $paypal_secret = $api->paypal_secret_live;
            $baseUrl = 'https://api-m.paypal.com';
        } else {
            $paypal_client_id = $api->paypal_client_id_sandbox;
            $paypal_secret = $api->paypal_secret_sandbox;
            $baseUrl = 'https://api-m.sandbox.paypal.com';
        }

        // Get PayPal Access Token
        $tokenResponse = Http::asForm()
            ->withBasicAuth($paypal_client_id, $paypal_secret)
            ->post("{$baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->ok()) {
            $this->error('Failed to get PayPal access token: ' . $tokenResponse->body());
            return 1;
        }

        $accessToken = $tokenResponse->json('access_token');
        $this->info('PayPal access token obtained successfully.');

        $planResults = [];

        foreach ($this->planTemplates as $planType => $planData) {
            $this->info("Creating plan for: {$planData['name']}");
            
            // 1. Create Product
            $productId = 'PLAN_' . strtoupper($planType);
            $productResponse = Http::withToken($accessToken)
                ->post("{$baseUrl}/v1/catalogs/products", [
                    'id' => $productId,
                    'name' => $planData['name'] . ' Plan',
                    'description' => $planData['description'],
                    'type' => 'SERVICE',
                    'category' => 'SOFTWARE'
                ]);

            // Product creation can return 200 (created) or 422 (already exists)
            if ($productResponse->successful() || $productResponse->status() === 422) {
                $this->info("✅ Product ready: {$productId}");
            } else {
                $this->error("❌ Product creation failed: " . $productResponse->body());
                continue;
            }

            // 2. Create Subscription Plan
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
                            'total_cycles' => 0, // 무제한
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

            if ($planResponse->successful()) {
                $planId = $planResponse->json('id');
                $this->info("✅ Subscription plan created: {$planId}");
                
                $planResults[] = [$planType, $planId];
            } else {
                $this->error("❌ Failed to create plan for {$planType}: " . $planResponse->body());
            }

            $this->info('');
        }

        if (!empty($planResults)) {
            $this->info('Created PayPal Plan IDs:');
            $this->table(['Plan Type', 'PayPal Plan ID'], $planResults);
            
            $this->info('');
            $this->warn('Update your JavaScript with these plan IDs:');
            
            $jsCode = "const planIdMap = {\n";
            foreach ($planResults as [$planType, $planId]) {
                $jsCode .= "    '{$planType}': '{$planId}',\n";
            }
            $jsCode .= "};";
            
            $this->line($jsCode);
        }

        $this->info('PayPal subscription plans creation completed!');
        
        return 0;
    }
}