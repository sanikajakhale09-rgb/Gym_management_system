<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'diet_plan_id',
        'day_of_week',
        'meal_type',
        'food_items',
        'notes',
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
}