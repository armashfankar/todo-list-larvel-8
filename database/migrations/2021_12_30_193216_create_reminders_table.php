<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->string('reminder_reference_number', 20)->unique();
            $table->string('todo_reference_number', 20);
            $table->string('user_reference_number', 20);
            $table->integer('remind_in')->default(0);
            $table->enum('type',['day','week']);
            $table->enum('status',['created','reminded']);
            $table->boolean('is_email_sent')->default(0);
            $table->timestamps();

            $table->foreign('todo_reference_number')->references('todo_reference_number')->on('to_dos');
            $table->foreign('user_reference_number')->references('user_reference_number')->on('users');
            $table->index('reminder_reference_number');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
}
