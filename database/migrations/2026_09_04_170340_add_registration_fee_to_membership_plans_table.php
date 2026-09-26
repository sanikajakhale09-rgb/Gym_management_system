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
    Schema::table('membership_plans', function (Blueprint $table) {
        $table->decimal('registration_fee', 10, 2)->default(0)->after('price');
        $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
    });
}

public function down()
{
    Schema::table('membership_plans', function (Blueprint $table) {
        $table->dropColumn(['registration_fee', 'status']);
    });
}
};
