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
        Schema::create('psqc_certifications', function (Blueprint $table) {
            $table->id();

            // 소유자
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // 대상 정보
            $table->text('url');             // 테스트 대상 URL
            $table->string('domain');        // 도메인 (인덱싱용)

            // 결과 요약
            $table->string('overall_grade', 2);          // A+, A, B, C, D, F
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->json('metrics')->nullable();         // 종합 메트릭 요약

            // 결제 관련 (PayPal 지원)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                ->default('pending');
            $table->json('payment_data')->nullable();    // PayPal 결제 정보 저장용

            // 인증서 상태
            $table->string('code', 12)->unique();        // QR 코드용 고유 식별자
            $table->timestamp('issued_at')->nullable();  // 발급일시
            $table->timestamp('expires_at')->nullable(); // 만료일시
            $table->boolean('is_valid')->default(false); // 유효 여부
            $table->boolean('is_revoked')->default(false); // 강제 무효 여부

            // 화이트라벨 (Agency)
            $table->string('agency_name')->nullable();       // 대행사/회사명
            $table->string('agency_representative')->nullable(); // 대표자 이름

            $table->timestamps();

            // 인덱스
            $table->index(['user_id', 'domain']);
            $table->index('code');
            $table->index(['payment_status', 'created_at']); // 결제 상태별 조회
            $table->index(['is_valid', 'expires_at']); // 유효한 인증서 조회용
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psqc_certifications');
    }
};