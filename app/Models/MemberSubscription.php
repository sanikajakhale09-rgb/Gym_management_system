<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'branch_id',
        'member_id',
        'membership_plan_id',
        'trainer_id',        // ट्रॅनर आयडी
        'start_date',
        'end_date',
        'total_amount',        // एकूण रक्कम
        'amount_paid',         // भरलेली रक्कम
        'balance_amount',      // पेंडिंग / शिल्लक रक्कम
        'price_paid',          // जुन्या सपोर्टसाठी
        'registration_fee',
        'due_date',            // 🟢 Mass assignment sathi due_date add keli ahe
        'status',
        // फ्रीज फिल्ड्स
        'freeze_from',
        'freeze_to',
        'approval_reference',
        'freeze_reason',
    ];

    // 🟢 Accessor function active membership table madhe next_due_date disnyasathi
    public function getNextDueDateAttribute()
    {
        return $this->due_date;
    }

    // Membership Plan सोबत जुने रिलेशन
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    // कंट्रोलरचा एरर घालवण्यासाठी membershipPlan रिलेशन
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    // Member सोबत relation
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    // ट्रॅनर रिलेशन
    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    // फ्रीज हिस्ट्रीसाठी रिलेशन
    public function freezes()
    {
        return $this->hasMany(MembershipFreeze::class, 'member_subscription_id');
    }

    // 🟢 नवीन ॲड केलेले: Payments सोबत रिलेशन (एरर काढण्यासाठी)
    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_subscription_id');
    }
}