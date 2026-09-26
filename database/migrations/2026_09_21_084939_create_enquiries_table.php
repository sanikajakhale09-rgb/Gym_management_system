<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // जर enquiries टेबल आधीच नसेल तरच तयार होईल (जुना डेटा सेफ राहील)
        if (!Schema::hasTable('enquiries')) {
            Schema::create('enquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('mobile_number')->nullable();
                $table->string('email')->nullable();
                $table->string('source')->default('Manual');
                $table->text('message')->nullable();
                $table->string('status')->default('New');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};