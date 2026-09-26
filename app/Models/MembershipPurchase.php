<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'branch_id',
        'member_id',
        'membership_plan_id',
        'start_date',
        'end_date',
        'total_amount',
        'amount_paid',
        'balance_amount',
        'payment_method',
        'payment_status',
        'due_date',
    ];

    // मेंबर्स सोबतचे नाते (Relationship)
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // प्लॅन सोबतचे नाते
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    // ब्रांच सोबतचे नाते
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}