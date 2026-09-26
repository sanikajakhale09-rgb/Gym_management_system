<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('tagline')->nullable();
            $table->string('owner_name');
            $table->string('owner_contact')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('contact_number');
            $table->string('alternate_contact')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->string('pincode');
            $table->string('gst_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('business_legal_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};