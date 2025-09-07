<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    protected $fillable = [
        'user_id',
        'plan_type',
        'next_plan_type',
        'customerKey',
        'customerName',
        'customerEmail',
        'customerPhone',
        'paypal_order_id',
        'paypal_payer_id',
        'paypal_email',
        'paypal_name',
        'paypal_amount',
        'paypal_currency',
        'paypal_paid_at',
        'paypal_subscription_id',
        'payment_status',
        'is_subscription',
        'price',
        'start_date',
        'end_date',
        'monthly_limit',
        'daily_limit',
        'total_limit',
        'used_count',
        'daily_used_count',
        'status',
        'auto_renew',
        'is_refundable',
        'refund_deadline',
    ];

    protected $casts = [
        'is_subscription' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'refund_deadline' => 'datetime',
        'paypal_paid_at' => 'datetime',
        'auto_renew' => 'boolean',
        'is_refundable' => 'boolean',
        'price' => 'integer',
        'paypal_amount' => 'decimal:2',
        'monthly_limit' => 'integer',
        'daily_limit' => 'integer',
        'total_limit' => 'integer',
        'used_count' => 'integer',
        'daily_used_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSubscription($query)
    {
        return $query->where('is_subscription', true);
    }

    public function scopeCoupon($query)
    {
        return $query->where('is_subscription', false);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // 활성 상태 체크 (결제 완료 조건 추가)
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->payment_status === 'paid' &&
               $this->end_date->isFuture();
    }

    // 환불 가능 여부
    public function canRefund(): bool
    {
        return $this->is_refundable && 
               $this->payment_status === 'paid' &&
               $this->used_count === 0 &&
               $this->refund_deadline &&
               $this->refund_deadline->isFuture();
    }

    // 사용 가능 여부 체크
    public function canUse(int $count = 1): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // 일일 한도 체크
        if ($this->daily_limit && ($this->daily_used_count + $count) > $this->daily_limit) {
            return false;
        }

        // 월간 한도 체크 (구독용)
        if ($this->monthly_limit && ($this->used_count + $count) > $this->monthly_limit) {
            return false;
        }

        // 총 한도 체크 (쿠폰용)
        if ($this->total_limit && ($this->used_count + $count) > $this->total_limit) {
            return false;
        }

        return true;
    }

    // 사용량 증가
    public function use(int $count = 1): bool
    {
        if (!$this->canUse($count)) {
            return false;
        }

        $this->increment('used_count', $count);
        $this->increment('daily_used_count', $count);

        return true;
    }

    // 남은 사용량 정보
    public function getRemainingUsage(): array
    {
        return [
            'daily' => [
                'limit' => $this->daily_limit,
                'used' => $this->daily_used_count,
                'remaining' => $this->daily_limit ? max(0, $this->daily_limit - $this->daily_used_count) : null,
            ],
            'monthly' => [
                'limit' => $this->monthly_limit,
                'used' => $this->used_count,
                'remaining' => $this->monthly_limit ? max(0, $this->monthly_limit - $this->used_count) : null,
            ],
            'total' => [
                'limit' => $this->total_limit,
                'used' => $this->used_count,
                'remaining' => $this->total_limit ? max(0, $this->total_limit - $this->used_count) : null,
            ],
        ];
    }

    // PayPal 관련 헬퍼 메서드
    public function isPaidViaPaypal(): bool
    {
        return !empty($this->paypal_order_id) && $this->payment_status === 'paid';
    }

    public function hasPaypalSubscription(): bool
    {
        return $this->is_subscription && !empty($this->paypal_subscription_id);
    }

    // 결제 실패 처리
    public function incrementPaymentFailure(): void
    {
        $this->increment('payment_failure_count');
        
        // 3회 실패 시 구독 중단
        if ($this->payment_failure_count >= 3) {
            $this->update([
                'status' => 'suspended',
                'payment_status' => 'failed'
            ]);
        }
    }

    // 월간 사용량 리셋 (구독 갱신 시)
    public function resetMonthlyUsage(): void
    {
        if ($this->is_subscription) {
            $this->update([
                'used_count' => 0,
                'daily_used_count' => 0,
                'payment_failure_count' => 0, // 결제 성공 시 실패 카운트 리셋
            ]);
        }
    }
}