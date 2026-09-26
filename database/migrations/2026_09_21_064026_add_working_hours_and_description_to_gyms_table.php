<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            if (!Schema::hasColumn('gyms', 'working_hours')) {
                $table->text('working_hours')->nullable();
            }
            if (!Schema::hasColumn('gyms', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn(['working_hours', 'description']);
        });
    }
};