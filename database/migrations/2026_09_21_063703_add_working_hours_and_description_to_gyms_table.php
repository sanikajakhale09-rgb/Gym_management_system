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
    Schema::table('gyms', function (Blueprint $table) {
        $table->text('working_hours')->nullable();
        $table->text('description')->nullable();
    });
}

public function down(): void
{
    Schema::table('gyms', function (Blueprint $table) {
        $table->dropColumn(['working_hours', 'description']);
    });
}
};
