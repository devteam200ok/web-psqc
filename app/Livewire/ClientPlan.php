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
            'price' => 29000,
            'is_subscription' => true,
            'description' => '개인 및 소규모 팀을 위한 기본 플랜',
            'features' => [
                '월 600회 테스트',
                '일 60회 테스트 제한',
                '기본 분석 리포트',
                '이메일 지원'
            ]
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 69000,
            'is_subscription' => true,
            'description' => '중간 규모 팀을 위한 전문 플랜',
            'features' => [
                '월 1,500회 테스트',
                '일 150회 테스트 제한',
                '고급 분석 리포트',
                '우선 이메일 지원',
                'API 접근'
            ]
        ],
        'agency' => [
            'name' => 'Agency',
            'price' => 199000,
            'is_subscription' => true,
            'description' => '대규모 에이전시를 위한 프리미엄 플랜',
            'features' => [
                '월 6,000회 테스트',
                '일 600회 테스트 제한',
                '전문 분석 리포트',
                '24/7 우선 지원',
                '전용 API 키',
                '맞춤 통합'
            ]
        ]
    ];

    public function render()
    {
        $user = Auth::user();
        
        // 현재 활성 플랜들 조회
        $currentPlans = UserPlan::where('user_id', $user->id)
            ->active()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // 구독 플랜들
        $subscriptions = $currentPlans->where('is_subscription', true);
        
        // 쿠폰들
        $coupons = $currentPlans->where('is_subscription', false);
        
        // 최근 24시간 테스트 사용 내역
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
            session()->flash('error', '구독을 찾을 수 없습니다.');
            return;
        }
        
        $plan->update(['auto_renew' => false]);
        
        session()->flash('success', '구독이 취소되었습니다. 현재 구독 기간 종료 후 자동 갱신되지 않습니다.');
    }
    
    public function changePlan($currentPlanId, $newPlanType)
    {
        $currentPlan = UserPlan::where('id', $currentPlanId)
            ->where('user_id', Auth::id())
            ->where('is_subscription', true)
            ->first();
            
        if (!$currentPlan) {
            session()->flash('error', '현재 구독을 찾을 수 없습니다.');
            return;
        }
        
        if (!isset($this->planTemplates[$newPlanType])) {
            session()->flash('error', '잘못된 플랜입니다.');
            return;
        }
        
        // 플랜 변경 요청 저장 (실제로는 별도 테이블에 저장하거나 필드 추가)
        $currentPlan->update([
            'next_plan_type' => $newPlanType
        ]);
        
        $newPlanName = $this->planTemplates[$newPlanType]['name'];
        session()->flash('success', "다음 결제일({$currentPlan->end_date->format('Y-m-d')})부터 {$newPlanName} 플랜으로 변경됩니다.");
    }
}
