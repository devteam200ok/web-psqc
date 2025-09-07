<?php

// ===== LIVEWIRE COMPONENT =====
// app/Livewire/ClientPurchase.php

namespace App\Livewire;

use App\Models\UserPlan;
use App\Models\Api;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientPurchase extends Component
{
    public $planType;
    public $planData;
    public $amount;
    public $orderId;

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
                ->first();

            if ($existingSubscription) {
                return redirect()->route('home')->with('error', 'You already have an active subscription plan.');
            }
        }

        $this->orderId = 'PLAN_' . strtoupper($this->planType) . '_' . Auth::id() . '_' . time();
    }

    public function render()
    {
        $api = Api::first();
        
        return view('livewire.client-purchase', [
            'api' => $api
        ])->layout('layouts.app');
    }
}