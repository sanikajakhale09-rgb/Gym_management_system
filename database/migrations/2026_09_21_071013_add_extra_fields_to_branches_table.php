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
            $table->string('branch_code')->nullable()->after('name');
            $table->string('status')->default('Active')->after('branch_code');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('manager_name')->nullable();
            $table->string('manager_contact', 15)->nullable();
            $table->string('manager_email')->nullable();
            $table->string('alternate_contact', 15)->nullable();
            $table->string('branch_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'branch_code',
                'status',
                'address_line_1',
                'address_line_2',
                'state',
                'pincode',
                'manager_name',
                'manager_contact',
                'manager_email',
                'alternate_contact',
                'branch_email'
            ]);
        });
    }
};