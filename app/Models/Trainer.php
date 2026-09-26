<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'branch_id',
        'name',
        'email',
        'mobile',
        'specialization',
        'experience',
        'salary',
        'status',
    ];


    protected $casts = [
        'experience' => 'integer',
        'salary' => 'decimal:2',
    ];

    // Branch relation proper ahe (Karan Branch model already ahe)
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}