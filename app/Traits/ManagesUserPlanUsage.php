<?php

namespace App\Traits;

use App\Models\UserPlan;
use App\Models\TestUsage;
use Illuminate\Support\Facades\Auth;

trait ManagesUserPlanUsage
{
    /**
     * 사용자의 현재 활성 플랜들 정보를 가져옴
     */
    public function getUserPlanUsage()
    {
        if (!Auth::check()) {
            return null;
        }

        $userId = Auth::id();
        
        // 활성 구독 플랜 (최대 1개)
        $subscription = UserPlan::where('user_id', $userId)
            ->subscription()
            ->active()
            ->where('end_date', '>', now())
            ->first();

        // 활성 쿠폰들 (복수 가능)
        $coupons = UserPlan::where('user_id', $userId)
            ->coupon()
            ->active()
            ->where('end_date', '>', now())
            ->get();

        return [
            'subscription' => $subscription,
            'coupons' => $coupons,
        ];
    }

    /**
     * 사용자의 사용 가능한 횟수 계산 (차감 우선순위 적용)
     */
    public function calculateAvailableUsage()
    {
        if (!Auth::check()) {
            return null;
        }

        $planUsage = $this->getUserPlanUsage();
        $user = Auth::user();
        
        // 플랜이 없는 경우 - user->usage는 월간 남은 사용량 (일간 한도 없음)
        if (!$planUsage['subscription'] && $planUsage['coupons']->isEmpty()) {
            return [
                'type' => 'basic',
                'monthly_remaining' => max(0, $user->usage),
                'daily_remaining' => null,
                'total_remaining' => null,
            ];
        }

        $dailyRemaining = 0;
        $monthlyRemaining = null;
        $totalRemaining = 0;

        // 1순위: 구독 플랜 사용 가능량
        if ($planUsage['subscription']) {
            $sub = $planUsage['subscription'];
            
            // 구독 일간 남은 횟수
            $subDailyRemaining = 0;
            if ($sub->daily_limit) {
                $subDailyRemaining = max(0, $sub->daily_limit - $sub->daily_used_count);
            }
            
            // 구독 월간 남은 횟수 (월간 한도가 있는 경우만)
            if ($sub->monthly_limit) {
                $monthlyRemaining = max(0, $sub->monthly_limit - $sub->used_count);
                // 월간 한도와 일간 한도 중 작은 값
                $subDailyRemaining = min($subDailyRemaining, $monthlyRemaining);
            }
            
            $dailyRemaining += $subDailyRemaining;
        }

        // 2순위: 쿠폰들 (만료일 가까운 순서대로)
        $sortedCoupons = $planUsage['coupons']->sortBy('end_date');
        foreach ($sortedCoupons as $coupon) {
            if ($coupon->total_limit) {
                $couponRemaining = max(0, $coupon->total_limit - $coupon->used_count);
                
                // 쿠폰에 일일 한도가 있는 경우
                if ($coupon->daily_limit) {
                    $couponDailyRemaining = max(0, $coupon->daily_limit - $coupon->daily_used_count);
                    $availableCouponUsage = min($couponRemaining, $couponDailyRemaining);
                } else {
                    $availableCouponUsage = $couponRemaining;
                }
                
                $dailyRemaining += $availableCouponUsage;
                $totalRemaining += $couponRemaining;
            }
        }

        return [
            'type' => $planUsage['subscription'] ? 'subscription' : 'coupon',
            'daily_remaining' => $dailyRemaining,
            'monthly_remaining' => $monthlyRemaining,
            'total_remaining' => $totalRemaining,
            'subscription' => $planUsage['subscription'],
            'coupons' => $sortedCoupons,
        ];
    }

    /**
     * 사용 가능 여부 체크
     */
    public function canUseService(int $count = 1): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $usage = $this->calculateAvailableUsage();
        
        if (!$usage) {
            return false;
        }

        // 기본 사용자인 경우 - 월간 한도만 체크 (일간 한도 없음)
        if ($usage['type'] === 'basic') {
            return $usage['monthly_remaining'] >= $count;
        }

        // 플랜 사용자인 경우 - 일일 한도 체크
        if ($usage['daily_remaining'] < $count) {
            return false;
        }

        // 구독자인 경우 월간 한도도 체크
        if ($usage['type'] === 'subscription' && $usage['monthly_remaining'] !== null) {
            return $usage['monthly_remaining'] >= $count;
        }

        return true;
    }

    /**
     * 서비스 사용량 차감 (우선순위: 1.구독 2.만료일 가까운 쿠폰 3.기본사용자)
     */
    public function consumeService(string $domain, string $testName, int $count = 1): bool
    {
        if (!Auth::check() || !$this->canUseService($count)) {
            return false;
        }

        $userId = Auth::id();
        $planUsage = $this->getUserPlanUsage();
        $user = Auth::user();

        // TestUsage 증빙 기록 생성
        TestUsage::record($userId, $domain, $testName);

        // 플랜이 없는 경우 - user->usage에서 차감
        if (!$planUsage['subscription'] && $planUsage['coupons']->isEmpty()) {
            $user->decrement('usage', $count);
            return true;
        }

        $remainingCount = $count;

        // 1순위: 구독 플랜에서 먼저 차감
        if ($planUsage['subscription'] && $remainingCount > 0) {
            $sub = $planUsage['subscription'];
            
            // 구독에서 사용 가능한 양 계산
            $subAvailable = 0;
            if ($sub->daily_limit) {
                $subAvailable = max(0, $sub->daily_limit - $sub->daily_used_count);
                
                // 월간 한도도 있는 경우 더 작은 값으로 제한
                if ($sub->monthly_limit) {
                    $monthlyAvailable = max(0, $sub->monthly_limit - $sub->used_count);
                    $subAvailable = min($subAvailable, $monthlyAvailable);
                }
            }
            
            $useFromSub = min($remainingCount, $subAvailable);
            if ($useFromSub > 0) {
                $sub->increment('daily_used_count', $useFromSub);
                $sub->increment('used_count', $useFromSub); // 월간 누적 사용량도 증가
                $remainingCount -= $useFromSub;
            }
        }

        // 2순위: 쿠폰에서 차감 (만료일 가까운 순서)
        if ($remainingCount > 0) {
            $sortedCoupons = $planUsage['coupons']->sortBy('end_date');
            
            foreach ($sortedCoupons as $coupon) {
                if ($remainingCount <= 0) break;

                $couponTotalAvailable = max(0, $coupon->total_limit - $coupon->used_count);
                if ($couponTotalAvailable <= 0) continue;
                
                // 쿠폰에 일일 한도가 있는 경우
                $couponAvailable = $couponTotalAvailable;
                if ($coupon->daily_limit) {
                    $couponDailyAvailable = max(0, $coupon->daily_limit - $coupon->daily_used_count);
                    $couponAvailable = min($couponTotalAvailable, $couponDailyAvailable);
                }

                $useFromCoupon = min($remainingCount, $couponAvailable);
                if ($useFromCoupon > 0) {
                    $coupon->increment('used_count', $useFromCoupon);
                    $coupon->increment('daily_used_count', $useFromCoupon);
                    $remainingCount -= $useFromCoupon;
                }
            }
        }

        // 모든 사용량을 차감했는지 확인
        return $remainingCount === 0;
    }
}