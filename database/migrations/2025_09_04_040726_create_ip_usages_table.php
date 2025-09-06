<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ip_usages', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->integer('usage')->default(5);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ip_usages');
    }
};