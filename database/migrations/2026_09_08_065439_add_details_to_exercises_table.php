<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            // Check and add columns if they don't exist yet
            if (!Schema::hasColumn('exercises', 'difficulty_level')) {
                $table->string('difficulty_level')->nullable()->after('muscle_group');
            }
            if (!Schema::hasColumn('exercises', 'equipment')) {
                $table->string('equipment')->nullable()->after('difficulty_level');
            }
            if (!Schema::hasColumn('exercises', 'description')) {
                $table->text('description')->nullable()->after('equipment');
            }
            if (!Schema::hasColumn('exercises', 'media_reference')) {
                $table->string('media_reference')->nullable()->after('description');
            }
            if (!Schema::hasColumn('exercises', 'gym_id')) {
                $table->unsignedBigInteger('gym_id')->nullable();
            }
            if (!Schema::hasColumn('exercises', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn([
                'difficulty_level', 
                'equipment', 
                'description', 
                'media_reference', 
                'gym_id', 
                'branch_id'
            ]);
        });
    }
};