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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            
            // 기본 정보
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('web_test_id')->constrained()->onDelete('cascade'); // 원본 테스트
            $table->string('test_type'); // p-speed, p-load, s-ssl 등
            $table->text('url'); // 테스트 대상 URL
            $table->string('domain'); // 도메인 (인덱싱용)
            
            // 인증서 고유 정보
            $table->string('code', 12)->unique(); // QR 코드용 고유 식별자
            
            // 테스트 결과 (스냅샷)
            $table->string('overall_grade', 2); // A+, A, B, C, D, F
            $table->decimal('overall_score', 5, 2)->nullable(); // 0.00 ~ 100.00
            
            // 결제 관련 (PayPal 지원)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->json('payment_data')->nullable(); // PayPal 결제 정보 저장용
            
            // 인증서 상태 관리
            $table->timestamp('issued_at'); // 발급일시
            $table->timestamp('expires_at')->nullable(); // 만료일시 (결제 완료 시 설정)
            $table->boolean('is_valid')->default(false); // 유효 여부 (결제 완료 시 true)
            
            $table->timestamps();
            
            // 인덱스
            $table->index('code'); // QR 코드 조회용
            $table->index(['user_id', 'test_type']);
            $table->index(['domain', 'test_type']);
            $table->index(['overall_grade', 'test_type']);
            $table->index(['payment_status', 'created_at']); // 결제 상태별 조회
            $table->index(['is_valid', 'expires_at']); // 유효한 인증서 조회용
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};