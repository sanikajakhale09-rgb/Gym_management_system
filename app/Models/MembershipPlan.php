<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // <-- He import ahe ka check kara

class MembershipPlan extends Model // <-- extends Model asna garjeche ahe
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'branch_id',
        'name',
        'price',
        'registration_fee',
        'discount',
        'tax',
        'duration_days',
        'description',
        'status',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}