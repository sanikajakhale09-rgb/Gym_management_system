<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fitness_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('trainer_id')->nullable();
            
            $table->enum('goal_type', [
                'Weight Loss', 
                'Weight Gain', 
                'Muscle Gain', 
                'Strength', 
                'General Fitness', 
                'Bodybuilding'
            ]);
            
            $table->string('starting_value');
            $table->string('current_value');
            $table->string('target_value');
            
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ['Active', 'Achieved', 'Discontinued'])->default('Active');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_goals');
    }
};