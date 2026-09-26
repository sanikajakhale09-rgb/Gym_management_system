<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_freezes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_subscription_id');
            $table->unsignedBigInteger('member_id');
            $table->date('freeze_from');
            $table->date('freeze_to');
            $table->integer('freeze_days');
            $table->text('reason')->nullable();
            $table->string('approval_reference')->nullable();
            $table->text('remarks')->nullable();
            
            // 👉 नवीन ॲड केलेले कॉलम (Status आणि Unfrozen Time साठी)
            $table->string('status')->default('Frozen'); // Values: Frozen, Unfrozen, Completed
            $table->timestamp('unfrozen_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_freezes');
    }
};