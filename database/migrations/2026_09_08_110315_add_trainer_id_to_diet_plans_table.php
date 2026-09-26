<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('diet_plans', 'trainer_id')) {
                $table->unsignedBigInteger('trainer_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->dropColumn('trainer_id');
        });
    }
};