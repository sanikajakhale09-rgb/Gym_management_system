<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name'); // Equipment Name
            $table->string('serial_number')->nullable()->unique(); // Serial Number
            $table->date('purchase_date')->nullable(); // Purchase Date
            $table->decimal('purchase_price', 10, 2)->nullable(); // Price
            $table->string('vendor')->nullable(); // Supplier
            $table->string('invoice_number')->nullable(); // Invoice No
            $table->date('warranty_end_date')->nullable(); // Warranty Expiry
            $table->string('location')->nullable(); // Cardio, Strength, etc.
            $table->string('condition')->default('Good'); // New, Good, Fair, Needs Repair, etc.
            $table->string('status')->default('Active'); // Active, Under Maintenance, Inactive, Retired
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};