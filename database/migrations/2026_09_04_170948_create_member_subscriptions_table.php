<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('membership_plan_id');
            $table->date('start_date');
            $table->date('end_date'); // Auto-calculated expiry date
            $table->decimal('price_paid', 10, 2);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_subscriptions');
    }
};