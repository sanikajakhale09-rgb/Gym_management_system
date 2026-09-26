<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Jila notification pathvaychi ahe
            $table->string('type'); // membership_expiry, pending_payment, new_enquiry, etc.
            $table->string('title');
            $table->text('message');
            $table->string('reference_type')->nullable(); // e.g., member, invoice, enquiry
            $table->unsignedBigInteger('reference_id')->nullable(); // related record id
            $table->timestamp('read_at')->nullable(); // Null mhanje Unread
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
    }

};
