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

    // 플랜 정보 정의
    protected $planTemplates = [
        'starter' => [
            'name' => 'Starter',
            'price' => 29000,
            'is_subscription' => true,
            'monthly_limit' => 600,
            'daily_limit' => 60,
            'description' => '월 최대 600회 · 일 최대 60회',
            'features' => ['개인 프로젝트 적합', '기본 회원 혜택', '이메일 알림'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 69000,
            'is_subscription' => true,
            'monthly_limit' => 1500,
            'daily_limit' => 150,
            'description' => '월 최대 1,500회 · 일 최대 150회',
            'features' => ['중소규모 사업장/에이전시 적합', 'Starter 회원 혜택', '검사 예약 + 스케줄러 주기 검사'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'agency' => [
            'name' => 'Agency',
            'price' => 199000,
            'is_subscription' => true,
            'monthly_limit' => 6000,
            'daily_limit' => 600,
            'description' => '월 최대 6,000회 · 일 최대 600회',
            'features' => ['다수 도메인/고객 관리', 'Pro 회원 혜택', '화이트라벨 리포트(인증서를 로고 수정)'],
            'validity_days' => null,
            'refund_days' => 7,
        ],
        'test1' => [
            'name' => 'Test1',
            'price' => 4900,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => 30,
            'total_limit' => 30,
            'description' => '1일 이내 최대 30회',
            'features' => ['단기 급테스트', '환불 불가'],
            'validity_days' => 1,
            'refund_days' => 0,
        ],
        'test7' => [
            'name' => 'Test7',
            'price' => 19000,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 150,
            'description' => '7일 이내 최대 150회',
            'features' => ['스프린트 QA', '사용 전 3일 이내 전액 환불 가능'],
            'validity_days' => 7,
            'refund_days' => 3,
        ],
        'test30' => [
            'name' => 'Test30',
            'price' => 39000,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 500,
            'description' => '30일 이내 최대 500회',
            'features' => ['프로젝트 안정화', '사용 전 7일 이내 전액 환불 가능'],
            'validity_days' => 30,
            'refund_days' => 7,
        ],
        'test90' => [
            'name' => 'Test90',
            'price' => 119000,
            'is_subscription' => false,
            'monthly_limit' => null,
            'daily_limit' => null,
            'total_limit' => 1300,
            'description' => '90일 이내 최대 1,300회',
            'features' => ['릴리즈 대응', '사용 전 30일 이내 전액 환불 가능'],
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
            return redirect()->route('home')->with('error', '잘못된 플랜입니다.');
        }

        $this->planData = $this->planTemplates[$this->planType];
        $this->amount = $this->planData['price'];

        // 구독 중복 체크
        if ($this->planData['is_subscription']) {
            $existingSubscription = UserPlan::where('user_id', Auth::id())
                ->subscription()
                ->active()
                ->first();

            if ($existingSubscription) {
                return redirect()->route('home')->with('error', '이미 구독 중인 플랜이 있습니다.');
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