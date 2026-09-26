<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Accessors for blade compatibility without changing blade code
    public function getGymNameAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function getPhoneAttribute()
    {
        return $this->attributes['contact_number'] ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->attributes['address_line_1'] ?? null;
    }

    public function operatingHours()
    {
        return $this->hasMany(GymOperatingHour::class);
    }
}