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
        Schema::create('web_tests', function (Blueprint $table) {
            $table->id();
            
            // 기본 정보
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('psqc_certification_id')->nullable();
            $table->string('test_type'); // 'lighthouse', 'ssl', 'speed', 'k6', 'security', 'accessibility' 등
            $table->string('url'); // 테스트 대상 URL
            $table->string('domain'); // 도메인 추출용 (인덱싱)
            
            // 상태 관리
            $table->enum('status', ['pending', 'queued', 'running', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            
            // 테스트별 설정
            $table->json('test_config')->nullable(); // VUs, duration, 테스트 옵션 등
            
            // 주요 결과
            $table->string('overall_grade', 2)->nullable(); // A+, A, B, C, F
            $table->decimal('overall_score', 5, 2)->nullable(); // 0.00 ~ 100.00
            
            // 상세 결과
            $table->json('metrics')->nullable(); // 주요 지표 (TTFB, Load Time, RPS 등)
            $table->json('results')->nullable(); // 전체 결과 (raw json)
            $table->text('error_message')->nullable(); // 에러 메시지 (에러 발생시)
            
            // 메타데이터
            $table->boolean('is_certified')->default(false); // 인증서 발급 가능 여부
            $table->boolean('is_saved_permanently')->default(false); // 영구 보관 (보고서 구매시)
            
            $table->timestamps();
            
            // 인덱스
            $table->index(['user_id', 'test_type', 'created_at']);
            $table->index(['domain', 'test_type']);
            $table->index(['status', 'test_type']);
            $table->index(['created_at', 'test_type']);
            $table->index('overall_grade');
            $table->index('is_certified');
        });

        // 사용자별 테스트 히스토리 제한을 위한 인덱스
        Schema::table('web_tests', function (Blueprint $table) {
            $table->index(['user_id', 'created_at', 'is_saved_permanently']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_tests');
    }
};
