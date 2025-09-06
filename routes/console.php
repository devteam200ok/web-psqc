<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\IpUsage;
use App\Models\User;
use App\Models\UserPlan;

// 매월 1일 00:00에 IP 사용량 초기화
Schedule::call(function () {
   try {
       IpUsage::truncate();
       \Log::info('Monthly IP usage reset completed');
   } catch (\Throwable $e) {
       \Log::error('IP usage reset error: ' . $e->getMessage());
   }
})->monthlyOn(1, '00:00');

// 매월 1일 00:00에 모든 User의 사용량을 20으로 설정
Schedule::call(function () {
   try {
       User::query()->update(['usage' => 20]);
       \Log::info('Monthly user usage reset to 20 completed');
   } catch (\Throwable $e) {
       \Log::error('User usage reset error: ' . $e->getMessage());
   }
})->monthlyOn(1, '00:00');

// 매일 00:00에 모든 UserPlan의 daily_used_count를 0으로 초기화
Schedule::call(function () {
   try {
       UserPlan::where('status', 'active')->update(['daily_used_count' => 0]);
       \Log::info('Daily UserPlan usage reset completed');
   } catch (\Throwable $e) {
       \Log::error('UserPlan daily usage reset error: ' . $e->getMessage());
   }
})->dailyAt('00:00');

// 스케줄된 테스트 처리 - 매 분마다 실행
Schedule::command('tests:process-scheduled --limit=100')
    ->everyMinute()
    ->withoutOverlapping(5) // 5분 타임아웃
    ->runInBackground();

// 오래된 스케줄 정리 - 매일 새벽 2시
Schedule::command('tests:process-scheduled --limit=0') // limit=0이면 정리만 수행
    ->dailyAt('02:00')
    ->withoutOverlapping(10);