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
        Schema::create('notification_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->constrained('users','id');
            $table->string('token'); // Firebaseトークン
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_tokens', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('notification_tokens');
    }
};
