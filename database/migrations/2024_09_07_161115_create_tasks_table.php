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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->foreignId('status_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('priority_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('excute_time')->default(5);
            $table->date('due_data')->nullable();
            $table->date('delivired_data')->nullable();
            $table->date('assign_date')->nullable();
            $table->Integer('rate')->max(5)->min(0)->default(0);
            $table->softDeletes();
            $table->timestamps();
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
