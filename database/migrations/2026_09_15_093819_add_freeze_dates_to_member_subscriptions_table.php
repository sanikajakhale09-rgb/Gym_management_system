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
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->date('freeze_from')->nullable();
$table->date('freeze_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            //
        });
    }
};
