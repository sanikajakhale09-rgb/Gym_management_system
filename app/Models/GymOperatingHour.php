<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymOperatingHour extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}