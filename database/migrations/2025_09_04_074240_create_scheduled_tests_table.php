<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_tests', function (Blueprint $table) {
            $table->id();
            
            // 기본 정보
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('test_type'); // p-speed, p-load, s-ssl 등
            $table->text('url'); // 테스트 대상 URL
            $table->string('domain'); // 도메인 (인덱싱용)
            
            // 스케줄링 정보
            $table->timestamp('scheduled_at'); // 실행 예정 시간
            $table->enum('status', ['pending', 'executed', 'failed', 'cancelled'])->default('pending');
            
            // 테스트 설정
            $table->json('test_config')->nullable(); // 테스트별 설정 (VUs, duration 등)
            
            // 실행 결과
            $table->timestamp('executed_at')->nullable(); // 실제 실행 시간
            $table->foreignId('executed_test_id')->nullable()->constrained('web_tests')->nullOnDelete(); // 실행된 WebTest ID
            $table->text('error_message')->nullable(); // 실행 실패 시 에러 메시지
            
            $table->timestamps();
            
            // 인덱스
            $table->index(['scheduled_at', 'status']); // 스케줄러에서 자주 조회
            $table->index(['user_id', 'status']);
            $table->index(['test_type', 'status']);
            $table->index('domain');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tests');
    }
};