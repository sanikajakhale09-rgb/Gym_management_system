<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('upi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('upi_id');
            $table->string('payee_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upi_settings');
    }
};