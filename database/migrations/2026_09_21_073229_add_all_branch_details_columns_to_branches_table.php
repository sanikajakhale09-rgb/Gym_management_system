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
    Schema::table('branches', function (Blueprint $table) {
        // जर हे कॉलम्स आधीपासून नसतील तरच खालीलप्रमाणे एक-एक करून टाका
        if (!Schema::hasColumn('branches', 'branch_code')) {
            $table->string('branch_code')->nullable();
        }
        if (!Schema::hasColumn('branches', 'status')) {
            $table->string('status')->default('Active');
        }
        if (!Schema::hasColumn('branches', 'address_line_1')) {
            $table->string('address_line_1')->nullable();
        }
        if (!Schema::hasColumn('branches', 'address_line_2')) {
            $table->string('address_line_2')->nullable();
        }
        if (!Schema::hasColumn('branches', 'city')) {
            $table->string('city')->nullable();
        }
        if (!Schema::hasColumn('branches', 'state')) {
            $table->string('state')->nullable();
        }
        if (!Schema::hasColumn('branches', 'pincode')) {
            $table->string('pincode')->nullable();
        }
        if (!Schema::hasColumn('branches', 'manager_name')) {
            $table->string('manager_name')->nullable();
        }
        if (!Schema::hasColumn('branches', 'manager_contact')) {
            $table->string('manager_contact')->nullable();
        }
        if (!Schema::hasColumn('branches', 'manager_email')) {
            $table->string('manager_email')->nullable();
        }
        if (!Schema::hasColumn('branches', 'primary_contact')) {
            $table->string('primary_contact')->nullable();
        }
        if (!Schema::hasColumn('branches', 'alternate_contact')) {
            $table->string('alternate_contact')->nullable();
        }
        if (!Schema::hasColumn('branches', 'branch_email')) {
            $table->string('branch_email')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('branches', function (Blueprint $table) {
        $table->dropColumn([
            'branch_code', 'status', 'address_line_1', 'address_line_2', 
            'city', 'state', 'pincode', 'manager_name', 'manager_contact', 
            'manager_email', 'primary_contact', 'alternate_contact', 'branch_email'
        ]);
    });
}
};
