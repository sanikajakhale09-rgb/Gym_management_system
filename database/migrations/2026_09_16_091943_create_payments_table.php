<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // जसे की INV-2026-001
            $table->unsignedBigInteger('member_id');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['Cash', 'UPI', 'Card', 'Bank Transfer', 'Online']);
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['Paid', 'Partial', 'Pending', 'Refunded'])->default('Pending');
            $table->string('staff_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};