<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReminderAtToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dateTime('reminder_at')->nullable()->after('due_date');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('reminder_at');
        });
    }
}