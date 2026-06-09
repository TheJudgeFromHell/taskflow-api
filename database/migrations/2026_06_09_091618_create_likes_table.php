<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikesTable extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Один пользователь может оценить задачу только один раз
            $table->unique(['task_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
