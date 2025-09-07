<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 플랜 정보
            $table->enum('plan_type', ['starter', 'pro', 'agency', 'test1', 'test7', 'test30', 'test90']);
            $table->string('customerName')->nullable(); // 고객 이름
            $table->string('customerEmail')->nullable(); // 고객 이메일
            $table->boolean('is_subscription');
            $table->integer('price');
            
            // PayPal 관련 컬럼
            $table->string('paypal_order_id')->nullable(); // 일회성 결제용
            $table->string('paypal_subscription_id')->nullable(); // 구독용
            $table->string('paypal_plan_id')->nullable(); // PayPal 구독 플랜 ID
            $table->string('paypal_payer_id')->nullable();
            $table->string('paypal_email')->nullable();
            $table->string('paypal_name')->nullable();
            $table->decimal('paypal_amount', 10, 2)->nullable();
            $table->string('paypal_currency', 3)->nullable();
            $table->datetime('paypal_paid_at')->nullable();
            
            // 기간
            $table->datetime('start_date');
            $table->datetime('end_date'); // 구독: 갱신일, 쿠폰: 만료일
            
            // 한도
            $table->integer('monthly_limit')->nullable();
            $table->integer('daily_limit')->nullable();
            $table->integer('total_limit')->nullable();
            
            // 사용량
            $table->integer('used_count')->default(0); // 총 사용량
            $table->integer('daily_used_count')->default(0); // 일일 사용량
            
            // 상태
            $table->enum('status', ['active', 'expired', 'cancelled', 'refunded', 'suspended'])->default('active');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->integer('payment_failure_count')->default(0); // 결제 실패 횟수
            $table->boolean('auto_renew')->default(true); // 구독 자동갱신 여부
            $table->boolean('is_refundable')->default(true);
            $table->datetime('refund_deadline')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'is_subscription', 'status']);
            $table->index(['paypal_order_id']);
            $table->index(['paypal_subscription_id']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};