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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->string('category'); // Rent, Salary, Electricity, etc.
        $table->decimal('amount', 10, 2);
        $table->string('payment_method'); // Cash, UPI, Bank Transfer
        $table->text('description')->nullable();
        $table->string('attachment')->nullable();
        $table->date('expense_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
