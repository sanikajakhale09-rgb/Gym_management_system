<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id')->index();
            $table->unsignedBigInteger('branch_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile', 15);
            $table->string('specialization');
            $table->unsignedInteger('experience')->comment('In years');
            $table->decimal('salary', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Branch sobat foreign key proper ahe (Karan branches table astitvat ahe)
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};