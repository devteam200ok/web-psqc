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
            $table->string('next_plan_type')->nullable(); // 다음 플랜 (구독 변경 시)
            $table->string('customerKey')->nullable(); // 결제 키
            $table->string('customerName')->nullable(); // 고객 이름
            $table->string('customerEmail')->nullable(); // 고객 이메일
            $table->string('customerPhone')->nullable(); // 고객 전화번호
            $table->boolean('is_subscription');
            $table->integer('price');
            
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
            $table->enum('status', ['active', 'expired', 'cancelled', 'refunded'])->default('active');
            $table->boolean('auto_renew')->default(true); // 구독 자동갱신 여부
            $table->boolean('is_refundable')->default(true);
            $table->datetime('refund_deadline')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'is_subscription', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};