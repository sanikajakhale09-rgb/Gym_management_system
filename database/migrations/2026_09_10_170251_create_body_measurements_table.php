<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id')->default(1);
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->unsignedBigInteger('member_id');
            $table->date('measurement_date');
            $table->decimal('weight', 5, 2);
            $table->decimal('height', 5, 2);
            $table->decimal('BMI', 5, 2)->nullable();
            $table->decimal('body_fat', 5, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('hip', 5, 2)->nullable();
            $table->decimal('biceps', 5, 2)->nullable();
            $table->decimal('thigh', 5, 2)->nullable();
            $table->timestamps();

            // Foreign key relation (optional, depending on members table)
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};