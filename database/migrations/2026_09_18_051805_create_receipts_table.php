<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🟢 जर receipts टेबल आधीपासून नसेल तरच तो तयार होईल (एरर येणार नाही)
        if (!Schema::hasTable('receipts')) {
            Schema::create('receipts', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_number')->unique();
                
                // Foreign key constraints vyatirikta direct columns thevle ahet jyamule error yenar nahi
                $table->unsignedBigInteger('invoice_id');
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->unsignedBigInteger('member_id');

                $table->date('receipt_date');
                $table->decimal('amount', 10, 2);
                $table->string('payment_method');
                $table->string('transaction_id')->nullable();
                $table->string('received_by')->default('Admin');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};