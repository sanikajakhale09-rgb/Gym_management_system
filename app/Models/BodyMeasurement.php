<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyMeasurement extends Model
{
    use HasFactory;

    protected $table = 'body_measurements';

    protected $fillable = [
        'gym_id',
        'branch_id',
        'member_id',
        'measurement_date',
        'weight',
        'height',
        'BMI',
        'body_fat',
        'chest',
        'waist',
        'hip',
        'biceps',
        'thigh',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
