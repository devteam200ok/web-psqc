<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserPlan;
use App\Models\TestUsage;
use App\Models\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientPlan extends Component
{
    protected $planTemplates = [
        'starter' => [
            'name' => 'Starter',
            'price' => 29,
            'is_subscription' => true,
            'description' => 'Basic plan for individuals and small teams',
            'features' => [
                '600 monthly tests',
                '60 daily test limit',
                'Basic analysis reports',
                'Email support'
            ]
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 69,
            'is_subscription' => true,
            'description' => 'Professional plan for medium-sized teams',
            'features' => [
                '1,500 monthly tests',
                '150 daily test limit',
                'Advanced analysis reports',
                'Priority email support',
                'API access'
            ]
        ],
        'agency' => [
            'name' => 'Agency',
            'price' => 199,
            'is_subscription' => true,
            'description' => 'Premium plan for large agencies',
            'features' => [
                '6,000 monthly tests',
                '600 daily test limit',
                'Professional analysis reports',
                '24/7 priority support',
                'Dedicated API key',
                'Custom integrations'
            ]
        ]
    ];

    public function render()
    {
        $user = Auth::user();
        
        // Query current active plans
        $currentPlans = UserPlan::where('user_id', $user->id)
            ->active()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Subscription plans
        $subscriptions = $currentPlans->where('is_subscription', true);
        
        // Coupons
        $coupons = $currentPlans->where('is_subscription', false);
        
        // Recent 24-hour test usage history
        $recentUsage = TestUsage::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('livewire.client-plan', [
            'subscriptions' => $subscriptions,
            'coupons' => $coupons,
            'recentUsage' => $recentUsage,
            'planTemplates' => $this->planTemplates
        ])->layout('layouts.app');
    }

    public function cancelSubscription($planId)
    {
        $plan = UserPlan::where('id', $planId)
            ->where('user_id', Auth::id())
            ->where('is_subscription', true)
            ->first();
            
        if (!$plan) {
            session()->flash('error', 'Subscription not found.');
            return;
        }

        if (!$plan->paypal_subscription_id) {
            session()->flash('error', 'PayPal subscription ID not found.');
            return;
        }

        // Cancel subscription on PayPal
        $cancelled = $this->cancelPaypalSubscription($plan->paypal_subscription_id);
        
        if ($cancelled) {
            $plan->update([
                'auto_renew' => false,
                'status' => 'cancelled'
            ]);
            
            session()->flash('success', 'Subscription cancelled successfully. You can continue using the service until ' . $plan->end_date->format('Y-m-d') . '.');
        } else {
            session()->flash('error', 'Failed to cancel subscription. Please try again or contact support.');
        }
    }

    private function cancelPaypalSubscription($subscriptionId)
    {
        try {
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
                Log::error('PayPal token request failed for cancellation', $tokenResponse->json());
                return false;
            }

            $accessToken = $tokenResponse->json('access_token');

            // Cancel subscription
            $cancelResponse = Http::withToken($accessToken)
                ->post("{$baseUrl}/v1/billing/subscriptions/{$subscriptionId}/cancel", [
                    'reason' => 'User requested cancellation'
                ]);

            if ($cancelResponse->successful()) {
                Log::info('PayPal subscription cancelled successfully', ['subscription_id' => $subscriptionId]);
                return true;
            } else {
                Log::error('PayPal subscription cancellation failed', [
                    'subscription_id' => $subscriptionId,
                    'response' => $cancelResponse->json()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('PayPal subscription cancellation exception', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}