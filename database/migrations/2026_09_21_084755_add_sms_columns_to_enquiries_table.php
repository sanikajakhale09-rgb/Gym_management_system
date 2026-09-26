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
    Schema::create('enquiries', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('mobile_number')->nullable();
        $table->string('source')->default('Manual');
        $table->text('message')->nullable();
        $table->string('status')->default('New');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::table('enquiries', function (Blueprint $table) {
        $table->dropColumn(['mobile_number', 'source', 'message', 'status']);
    });
}
};
