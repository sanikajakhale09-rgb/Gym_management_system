<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('membership_plan')->default('Standard');
                $table->date('joining_date');
                $table->string('status')->default('Active'); // Active, Inactive, Expired
                $table->string('document_path')->nullable(); // Member Document upload
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('members');
    }
};