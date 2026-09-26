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
    Schema::table('workout_plan_items', function (Blueprint $table) {
        // Jar muscle_group nasel tarach ha thev
        if (!Schema::hasColumn('workout_plan_items', 'muscle_group')) {
            $table->string('muscle_group')->nullable()->after('day_of_week');
        }
        // Jar weight nasel tarach ha thev
        if (!Schema::hasColumn('workout_plan_items', 'weight')) {
            $table->string('weight')->nullable()->after('reps');
        }
        // Jar instructions nasel tarach ha thev
        if (!Schema::hasColumn('workout_plan_items', 'instructions')) {
            $table->text('instructions')->nullable();
        }
    });
}

public function down()
{
    Schema::table('workout_plan_items', function (Blueprint $table) {
        $table->dropColumn(['muscle_group', 'weight', 'rest_time', 'instructions']);
    });
}
};
