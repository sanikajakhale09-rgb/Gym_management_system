<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'mobile_number',
        'source',
        'message',
        'status',
        'email',
        'address',
        'follow_up_date',     // 🟢 हा कॉलम ॲड करणे बंधनकारक आहे
        'follow_up_response', // 🟢 हा कॉलम ॲड करणे बंधनकारक आहे
    ];
}