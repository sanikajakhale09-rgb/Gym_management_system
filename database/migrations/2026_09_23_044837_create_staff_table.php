<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique(); // STF001
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->unique();
            $table->enum('role', ['Manager', 'Receptionist', 'Trainer', 'Accountant', 'Cleaning Staff']);
            $table->string('branch');
            $table->decimal('salary', 10, 2);
            $table->date('joining_date');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('staff');
    }
};