<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->decimal('membership_fee', 10, 2)->default(0);
        $table->decimal('registration_fee', 10, 2)->default(0);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('tax', 10, 2)->default(0);
        $table->decimal('trainer_charges', 10, 2)->default(0);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
