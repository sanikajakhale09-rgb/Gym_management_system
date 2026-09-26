<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('diet_plans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('gym_id');
        $table->unsignedBigInteger('branch_id');
        $table->unsignedBigInteger('member_id');
        $table->unsignedBigInteger('trainer_id');
        $table->string('title');
        $table->text('breakfast')->nullable();
        $table->text('mid_morning')->nullable();
        $table->text('lunch')->nullable();
        $table->text('evening')->nullable();
        $table->text('dinner')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};