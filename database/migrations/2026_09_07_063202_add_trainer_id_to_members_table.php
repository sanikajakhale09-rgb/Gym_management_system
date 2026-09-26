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
    Schema::table('members', function (Blueprint $table) {
        $table->unsignedBigInteger('trainer_id')->nullable()->after('membership_plan_id');
        
        // Optional: If you want a foreign key constraint linking to trainers table
        // $table->foreign('trainer_id')->references('id')->on('trainers')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('members', function (Blueprint $table) {
        $table->dropColumn('trainer_id');
    });
}
};
