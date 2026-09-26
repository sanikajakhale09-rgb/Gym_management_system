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
        $table->unsignedBigInteger('gym_id')->nullable()->after('id');
        $table->unsignedBigInteger('branch_id')->nullable()->after('gym_id');
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
