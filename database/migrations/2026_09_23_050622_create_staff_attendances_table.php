<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['Present', 'Absent', 'Half Day', 'Leave'])->default('Present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Unique constraint taaki ek staff chi eka divshi double entry honar nahi
            $table->unique(['staff_id', 'date']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('staff_attendances');
    }
};