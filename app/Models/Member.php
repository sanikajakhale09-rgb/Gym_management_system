<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'member_code',
        'name',
        'phone',
        'email',
        'membership_plan_id',
        'trainer_id',
        'paid_amount',
        'pending_amount',    
        'next_due_date',    
        'payment_method',
        'blood_group',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'reference_source',
        'medical_notes',
        'joining_date',
        'start_date',        // <--- हा नवीन ॲड करा
        'end_date',          // <--- हा नवीन ॲड करा
        'expiry_date',      
        'trainer_charges',   // <--- हा नवीन ॲड करा (चार्जेस सेव्ह होण्यासाठी)
        'document_path',
        'status'
    ];

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'member_id');
    }

    public function latestSubscription()
    {
        return $this->hasOne(MemberSubscription::class, 'member_id')->latestOfMany();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    // नवीन ॲड केलेले रिलेशन: Workout Plans साठी
    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'member_id');
    }

    // नवीन ॲड केलेले रिलेशन: Diet Plans साठी
    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class, 'member_id');
    }

    // ---------------------------------------------------------
    // Fitness Goals साठी रिलेशन
    // ---------------------------------------------------------

    public function fitnessGoals()
    {
        return $this->hasMany(FitnessGoal::class, 'member_id');
    }

    // ---------------------------------------------------------
    // Body Measurements साठी रिलेशन
    // Progress Tracking मध्ये वापरले जाईल
    // ---------------------------------------------------------

    public function measurements()
    {
        return $this->hasMany(BodyMeasurement::class, 'member_id');
    }

    // ---------------------------------------------------------
    // Memberships / Active Membership
    // ---------------------------------------------------------

    public function memberships()
    {
        return $this->hasMany(MemberSubscription::class, 'member_id');
    }

    public function activeMembership()
    {
        return $this->hasOne(MemberSubscription::class, 'member_id')->latest();
    }

    // ---------------------------------------------------------
    // Model Boot
    // ---------------------------------------------------------

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_code)) {
                $member->member_code = 'GYM-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
            }
        });
    }
}