<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeave extends Model {
    use HasFactory;

    protected $table = 'staff_leaves';

    protected $fillable = [
        'staff_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    public function staff() {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }
}