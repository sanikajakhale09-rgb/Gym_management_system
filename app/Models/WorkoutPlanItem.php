<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutPlanItem extends Model
{
    protected $guarded = [];

    public function exercise() {
        return $this->belongsTo(Exercise::class);
    }

    public function workoutPlan() {
        return $this->belongsTo(WorkoutPlan::class);
    }
}