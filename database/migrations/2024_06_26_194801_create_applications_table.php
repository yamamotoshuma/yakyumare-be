<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->string('id')->primary(); //APL000000000 9桁
            $table->string('recruitment_id')->constrained('recruitments','id');
            $table->string('recruited_user_id')->constrained('users','id'); // 募集者
            $table->string('apply_user_id')->constrained('users','id'); // 応募者
            $table->boolean('approval')->nullable()->default(value: null); // true:承認
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}

