<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('mobile_number');
            $table->string('interested_plan')->nullable();
            $table->string('assigned_staff')->nullable();
            $table->date('followup_date');
            $table->string('status')->default('Pending'); // Pending, Rescheduled, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};