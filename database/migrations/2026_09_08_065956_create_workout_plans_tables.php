<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('title'); // e.g., "Hypertrophy Phase 1"
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workout_plan_id');
            $table->string('day_of_week'); // e.g., "Monday", "Chest Day"
            $table->unsignedBigInteger('exercise_id');
            $table->string('sets')->nullable();
            $table->string('reps')->nullable();
            $table->string('rest_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_plan_items');
        Schema::dropIfExists('workout_plans');
    }
};