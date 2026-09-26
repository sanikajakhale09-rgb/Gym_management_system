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
        $table->decimal('total_amount', 10, 2)->after('member_id'); // एकूण रक्कम
        $table->decimal('remaining_balance', 10, 2)->after('amount'); // बाकी रक्कम
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['total_amount', 'remaining_balance']);
    });
}
};
