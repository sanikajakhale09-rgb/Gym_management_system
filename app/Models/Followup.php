<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'mobile_number',
        'interested_plan',
        'assigned_staff',
        'followup_date',
        'status',
        'notes',
    ];
}