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
    Schema::table('diet_plans', function (Blueprint $table) {
        if (!Schema::hasColumn('diet_plans', 'breakfast')) {
            $table->text('breakfast')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'mid_morning')) {
            $table->text('mid_morning')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'lunch')) {
            $table->text('lunch')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'evening')) {
            $table->text('evening')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'dinner')) {
            $table->text('dinner')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'goal_type')) {
            $table->string('goal_type')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'starting_value')) {
            $table->string('starting_value')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'current_value')) {
            $table->string('current_value')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'target_value')) {
            $table->string('target_value')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'weight')) {
            $table->decimal('weight', 5, 2)->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'height')) {
            $table->decimal('height', 5, 2)->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'bmi')) {
            $table->decimal('bmi', 5, 2)->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'chest')) {
            $table->string('chest')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'waist')) {
            $table->string('waist')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'biceps')) {
            $table->string('biceps')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'hip')) {
            $table->string('hip')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'thigh')) {
            $table->string('thigh')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'body_fat')) {
            $table->decimal('body_fat', 5, 2)->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'before_photo')) {
            $table->string('before_photo')->nullable();
        }
        if (!Schema::hasColumn('diet_plans', 'after_photo')) {
            $table->string('after_photo')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            //
        });
    }
};
