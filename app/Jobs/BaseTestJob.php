<?php

namespace App\Jobs;

use App\Models\WebTest;
use App\Models\UserPlan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class BaseTestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $testId;
    
    public $timeout = 120;
    public $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct($url, $testId)
    {
        $this->url = $url;
        $this->testId = $testId;
    }

    /**
     * Get the service class name for this test type
     */
    abstract protected function getServiceClass(): string;

    /**
     * Get the job name for logging
     */
    abstract protected function getJobName(): string;

    /**
     * Execute the job.
     */
    public function handle()
    {
        $serviceClass = $this->getServiceClass();
        $service = app($serviceClass);
        $service->runTest($this->url, $this->testId);
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception)
    {
        Log::error($this->getJobName() . ' job failed', [
            'url' => $this->url,
            'test_id' => $this->testId,
            'exception' => $exception->getMessage(),
        ]);

        $test = WebTest::find($this->testId);
        if ($test) {
            // 테스트 상태를 failed로 업데이트
            $test->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => $exception->getMessage()
            ]);

            // 사용량 복원
            $this->restoreUsage($test);
        }
    }

    /**
     * 실패한 테스트의 사용량 복원
     */
    protected function restoreUsage(WebTest $test)
    {
        // 사용자가 없으면 복원 불필요
        if (!$test->user_id) {
            return;
        }

        $user = User::find($test->user_id);
        if (!$user) {
            return;
        }

        // 사용자의 현재 플랜 정보 가져오기
        $subscription = UserPlan::where('user_id', $user->id)
            ->subscription()
            ->active()
            ->where('end_date', '>', now())
            ->first();

        $coupons = UserPlan::where('user_id', $user->id)
            ->coupon()
            ->active()
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc') // 만료일이 먼 것부터
            ->get();

        // 플랜이 없는 경우 - 기본 사용자 usage 복원
        if (!$subscription && $coupons->isEmpty()) {
            $user->increment('usage', 1);
            Log::info('Usage restored for basic user', [
                'user_id' => $user->id,
                'test_id' => $test->id
            ]);
            return;
        }

        // 복원 우선순위: 쿠폰(만료일 먼 순서) -> 구독
        $restored = false;

        // 1. 쿠폰에 먼저 복원 시도
        foreach ($coupons as $coupon) {
            if ($coupon->used_count > 0) {
                $coupon->decrement('used_count', 1);
                
                // 당일 사용량도 복원 (테스트가 오늘 생성된 경우)
                if ($coupon->daily_used_count > 0 && $test->created_at->isToday()) {
                    $coupon->decrement('daily_used_count', 1);
                }
                
                Log::info('Usage restored to coupon', [
                    'user_id' => $user->id,
                    'test_id' => $test->id,
                    'coupon_id' => $coupon->id
                ]);
                
                $restored = true;
                break;
            }
        }

        // 2. 쿠폰 복원이 안 됐으면 구독에 복원
        if (!$restored && $subscription) {
            if ($subscription->used_count > 0) {
                $subscription->decrement('used_count', 1);
                
                // 당일 사용량도 복원 (테스트가 오늘 생성된 경우)
                if ($subscription->daily_used_count > 0 && $test->created_at->isToday()) {
                    $subscription->decrement('daily_used_count', 1);
                }
                
                Log::info('Usage restored to subscription', [
                    'user_id' => $user->id,
                    'test_id' => $test->id,
                    'subscription_id' => $subscription->id
                ]);
            }
        }
    }
}