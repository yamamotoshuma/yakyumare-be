<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary(); // FM:USR0000000 7桁
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Googleログイン用にnullableに
            $table->string('avatar')->nullable(); // 画像用のカラムを追加
            $table->string('provider_id')->nullable(); // プロバイダーID用のカラムを追加
            $table->string('provider')->nullable(); // プロバイダー名（例: google, facebookなど）
            $table->string('bio')->nullable(); // プロフィールの一言用のカラムを追加
            $table->unsignedBigInteger('role_id')->default(2); // 1: Admin, 2: User
            $table->foreign('role_id')->references('id')->on('roles');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
