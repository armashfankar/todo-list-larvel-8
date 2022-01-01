<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_dos', function (Blueprint $table) {
            $table->id();
            $table->string('todo_reference_number', 20)->unique();
            $table->string('user_reference_number', 20);
            $table->string('title',100);
            $table->mediumText('body');
            $table->date('due_date')->nullable();
            $table->string('attachment',255)->nullable();
            $table->enum('status',['complete','incomplete']);
            $table->boolean('is_archived')->default(0);
            $table->boolean('is_reminder_set')->default(0);
            $table->timestamps();

            $table->foreign('user_reference_number')->references('user_reference_number')->on('users');
            $table->index('todo_reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_dos');
    }
}
