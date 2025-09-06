<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 기존 테이블 삭제
        Schema::dropIfExists('domains');
        
        // 새 테이블 생성
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('url'); // 전체 URL 저장
            $table->string('url_hash', 64); // URL의 해시값
            
            // 도메인 인증 관련 필드
            $table->string('verification_token', 32)->unique();
            $table->enum('verification_method', ['txt_record', 'file_upload', 'manual', 'auto_hostname'])->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            
            // 인덱스
            $table->index('user_id');
            $table->index(['user_id', 'is_verified']);
            $table->index('verification_token');
            $table->unique(['user_id', 'url_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};