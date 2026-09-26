<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->date('freeze_from')->nullable()->after('status');
            $table->date('freeze_to')->nullable()->after('freeze_from');
            $table->string('approval_reference')->nullable()->after('freeze_to');
            $table->text('freeze_reason')->nullable()->after('approval_reference');
        });
    }

    public function down(): void
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'freeze_from',
                'freeze_to',
                'approval_reference',
                'freeze_reason'
            ]);
        });
    }
};