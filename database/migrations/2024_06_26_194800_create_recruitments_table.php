<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentsTable extends Migration
{
    public function up()
    {
        Schema::create('recruitments', function (Blueprint $table) {
            $table->string('id')->primary(); //YKM000000000 9桁
            $table->foreignId('type_id')->constrained('recruitment_types'); // recruitment_typesテーブルへの外部キー
            $table->string('user_id')->constrained('users', 'id'); // usersテーブルへの外部キーを追加
            $table->string('title');
            $table->text('content');
            $table->dateTime('event_date');
            $table->dateTime('deadline');
            $table->string('prefecture');
            $table->string('city');
            $table->string('place');
            $table->integer('capacity');
            $table->boolean('active')->default(true); // 募集中フラグを追加
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recruitments');
    }
}
