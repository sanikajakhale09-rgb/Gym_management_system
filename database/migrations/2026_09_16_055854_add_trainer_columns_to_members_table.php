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
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->decimal('trainer_charges', 8, 2)->default(1500);
    });
}

public function down(): void
{
    Schema::table('members', function (Blueprint $table) {
        $table->dropColumn(['start_date', 'end_date', 'trainer_charges']);
    });
}
};
