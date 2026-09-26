<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WorkoutPlanItem;

class WorkoutPlan extends Model
{
    protected $guarded = [];

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function trainer() {
        return $this->belongsTo(Trainer::class);
    }

    public function items() {
        return $this->hasMany(WorkoutPlanItem::class);
    }
}