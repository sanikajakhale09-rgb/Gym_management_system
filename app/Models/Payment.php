<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'member_id',
        'total_amount',      // 🟢 हा कॉलम आधीच आहे
        'amount',
        'remaining_balance', // 🟢 हा कॉलम आधीच आहे
        'membership_fee',    // 🟢 नवीन कॉलम ॲड केला
        'registration_fee',  // 🟢 नवीन कॉलम ॲड केला
        'discount',          // 🟢 नवीन कॉलम ॲड केला
        'tax',               // 🟢 नवीन कॉलम ॲड केला
        'trainer_charges',   // 🟢 नवीन कॉलम ॲड केला
        'payment_method',
        'transaction_id',
        'status',
        'staff_name',
        'notes',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}