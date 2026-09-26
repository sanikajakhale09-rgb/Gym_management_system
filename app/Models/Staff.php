<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StaffAttendance;

class Staff extends Model {
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'mobile',
        'role',
        'branch',
        'salary',
        'joining_date',
        'status'
    ];

    /**
     * Get the attendance records for the staff member.
     */
    public function attendances() {
        return $this->hasMany(StaffAttendance::class, 'staff_id', 'id');
    }
}