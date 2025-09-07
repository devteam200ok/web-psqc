<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserPlan;
use App\Models\TestUsage;
use Illuminate\Support\Facades\Auth;
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
        
        $plan->update(['auto_renew' => false]);
        
        session()->flash('success', 'Subscription cancelled. It will not auto-renew after the current subscription period ends.');
    }
    
    public function changePlan($currentPlanId, $newPlanType)
    {
        $currentPlan = UserPlan::where('id', $currentPlanId)
            ->where('user_id', Auth::id())
            ->where('is_subscription', true)
            ->first();
            
        if (!$currentPlan) {
            session()->flash('error', 'Current subscription not found.');
            return;
        }
        
        if (!isset($this->planTemplates[$newPlanType])) {
            session()->flash('error', 'Invalid plan.');
            return;
        }
        
        // Save plan change request (in practice, store in separate table or add field)
        $currentPlan->update([
            'next_plan_type' => $newPlanType
        ]);
        
        $newPlanName = $this->planTemplates[$newPlanType]['name'];
        session()->flash('success', "Starting from the next billing date ({$currentPlan->end_date->format('Y-m-d')}), it will change to {$newPlanName} plan.");
    }
}
