<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branch_code',
        'status',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'manager_name',
        'manager_contact',
        'manager_email',
        'primary_contact',
        'alternate_contact',
        'branch_email',
        'address',
        'contact'
    ];

    // Branch शी जोडलेले Members मिळवण्यासाठी
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    // Branch शी जोडलेले Trainers मिळवण्यासाठी
    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }
}