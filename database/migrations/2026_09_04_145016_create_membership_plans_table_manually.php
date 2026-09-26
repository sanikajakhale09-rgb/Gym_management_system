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
    Schema::create('membership_plans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('gym_id')->nullable(); // Multi-tenancy support
        $table->unsignedBigInteger('branch_id')->nullable(); // Branch support
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->decimal('registration_fee', 10, 2)->default(0);
        $table->integer('duration_days');
        $table->text('description')->nullable();
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
        $table->decimal('price', 10, 2);
$table->decimal('registration_fee', 10, 2)->default(0);
$table->decimal('discount', 10, 2)->default(0);
$table->decimal('tax', 5, 2)->default(0); // GST %
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans_table_manually');
    }
};
