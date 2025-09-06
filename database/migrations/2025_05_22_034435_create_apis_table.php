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
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('sendgrid_key')->nullable();
            $table->string('openai_key')->nullable();
            $table->string('paypal_mode')->nullable();
            $table->string('paypal_client_id_live')->nullable();
            $table->string('paypal_secret_live')->nullable();
            $table->string('paypal_client_id_sandbox')->nullable();
            $table->string('paypal_secret_sandbox')->nullable();
            $table->string('toss_mode')->nullable();
            $table->string('toss_client_key_test')->nullable();
            $table->string('toss_secret_key_test')->nullable();
            $table->string('toss_client_key')->nullable();
            $table->string('toss_secret_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apis');
    }
};
