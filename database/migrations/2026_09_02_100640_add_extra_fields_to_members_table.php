<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('members', function (Blueprint $table) {
        if (!Schema::hasColumn('members', 'blood_group')) {
            $table->string('blood_group')->nullable();
        }
        if (!Schema::hasColumn('members', 'occupation')) {
            $table->string('occupation')->nullable();
        }
        if (!Schema::hasColumn('members', 'emergency_contact_name')) {
            $table->string('emergency_contact_name')->nullable();
        }
        if (!Schema::hasColumn('members', 'emergency_contact_phone')) {
            $table->string('emergency_contact_phone')->nullable();
        }
        if (!Schema::hasColumn('members', 'reference_source')) {
            $table->string('reference_source')->nullable();
        }
        if (!Schema::hasColumn('members', 'medical_notes')) {
            $table->text('medical_notes')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            //
        });
    }
};