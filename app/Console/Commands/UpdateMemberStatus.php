<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Carbon\Carbon;

class UpdateMemberStatus extends Command
{
    protected $signature = 'members:update-status';
    protected $description = 'Automatically update member status to Expired if membership plan date is passed';

    public function handle()
    {
        $members = Member::whereRaw('LOWER(status) = ?', ['active'])->get();

        foreach ($members as $member) {
            $joiningDate = Carbon::parse($member->joining_date);
            $expiryDate = $joiningDate;

            if (stripos($member->membership_plan, 'Month') !== false) {
                $months = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
                $expiryDate = (clone $joiningDate)->addMonths($months > 0 ? $months : 1);
            } elseif (stripos($member->membership_plan, 'Year') !== false) {
                $years = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
                $expiryDate = (clone $joiningDate)->addYears($years > 0 ? $years : 1);
            } elseif (stripos($member->membership_plan, 'Day') !== false) {
                $days = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
                $expiryDate = (clone $joiningDate)->addDays($days > 0 ? $days : 30);
            } else {
                $expiryDate = (clone $joiningDate)->addMonth();
            }

            // जर आजची तारीख एक्सपायरी डेटच्या पुढे गेली असेल तर डेटाबेसमध्ये 'Expired' करा
            if (Carbon::now()->greaterThan($expiryDate)) {
                $member->update(['status' => 'Expired']);
            }
        }

        $this->info('Expired members status updated successfully in database.');
    }
}