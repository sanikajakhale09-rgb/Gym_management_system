<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('diet_plans', 'hip')) {
                $table->string('hip', 50)->nullable();
            }
            if (!Schema::hasColumn('diet_plans', 'thigh')) {
                $table->string('thigh', 50)->nullable();
            }
            if (!Schema::hasColumn('diet_plans', 'measurement_date')) {
                $table->date('measurement_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->dropColumn(['hip', 'thigh', 'measurement_date']);
        });
    }
};