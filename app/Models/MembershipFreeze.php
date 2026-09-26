<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipFreeze extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_subscription_id',
        'member_id',
        'freeze_from',
        'freeze_to',
        'freeze_days',
        'reason',
        'approval_reference',
        'remarks',
    ];

    public function subscription()
    {
        return $this->belongsTo(MemberSubscription::class, 'member_subscription_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}