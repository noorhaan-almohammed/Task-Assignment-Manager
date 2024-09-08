<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('assign_date')->nullable();
            $table->timestamp('complete_date')->nullable();
            $table->integer('execute_time')->nullable(); // Time in hours or minutes, depending on your design
            $table->float('rate')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Foreign keys
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
