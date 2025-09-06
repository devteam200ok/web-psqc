<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestUsage extends Model
{
    protected $fillable = [
        'user_id',
        'domain',
        'test_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 오늘 사용량 조회
    public static function getTodayUsage(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();
    }

    // 월간 사용량 조회
    public static function getMonthlyUsage(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    // 사용 기록 생성
    public static function record(int $userId, string $domain, string $testName): self
    {
        return static::create([
            'user_id' => $userId,
            'domain' => $domain,
            'test_name' => $testName,
        ]);
    }
}