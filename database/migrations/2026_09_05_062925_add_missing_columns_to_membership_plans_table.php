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
    Schema::table('membership_plans', function (Blueprint $table) {
        $table->unsignedBigInteger('gym_id')->nullable()->after('id');
        $table->decimal('discount', 10, 2)->default(0)->after('registration_fee');
        $table->decimal('tax', 5, 2)->default(0)->after('discount');
    });
}

public function down(): void
{
    Schema::table('membership_plans', function (Blueprint $table) {
        $table->dropColumn(['gym_id', 'discount', 'tax']);
    });
}
};
