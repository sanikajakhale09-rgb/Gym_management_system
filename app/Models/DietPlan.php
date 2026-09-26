<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    protected $fillable = [
        'gym_id',
        'branch_id',
        'trainer_id',
        'member_id',
        'title',
        'measurement_date',
        'start_date',
        'end_date',        // <--- हा कॉलम इथे ॲड करा
        'breakfast',
        'mid_morning',
        'lunch',
        'evening',
        'dinner',
        'goal_type',
        'starting_value',
        'current_value',
        'target_value',
        'weight',
        'height',
        'bmi',
        'chest',
        'waist',
        'biceps',
        'hip',
        'thigh',
        'body_fat',
        'before_photo',
        'after_photo',
    ];
    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function trainer() {
        return $this->belongsTo(Trainer::class);
    }
}