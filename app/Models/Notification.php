<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'branch_id',
        'user_id',
        'type',
        'title',
        'message',
        'reference_type',
        'reference_id',
        'read_at',
    ];

    // Relation to get related member or reference if needed
    public function member()
    {
        return $this->belongsTo(Member::class, 'reference_id');
    }
}