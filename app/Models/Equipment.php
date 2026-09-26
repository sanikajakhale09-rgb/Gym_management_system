<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'branch_id',
        'name',
        'category',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'vendor',
        'invoice_number',
        'warranty_end_date',
        'location',
        'condition',
        'status',
        'description',
    ];

    // Branch relation (जर Branch model असेल तर)
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}