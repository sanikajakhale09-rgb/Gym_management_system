<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipFreeze;
use App\Models\MemberSubscription;
use Carbon\Carbon;

class MembershipFreezeController extends Controller
{
    public function index(Request $request)
    {
        $frozenSubscriptions = MemberSubscription::with(['member', 'membershipPlan', 'freezes'])
            ->where(function($query) {
                $query->where('status', 'frozen')
                      ->orWhereHas('freezes');
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('admin.membership-purchase.freeze-list', compact('frozenSubscriptions'));
    }

    public function create($id)
    {
        $subscription = MemberSubscription::with(['member', 'membershipPlan'])->findOrFail($id);
        return view('admin.membership-purchase.freeze-form', compact('subscription'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'freeze_from' => 'required|date',
            'freeze_to' => 'required|date|after_or_equal:freeze_from',
            'freeze_reason' => 'required|string|max:255',
            'approval_reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $subscription = MemberSubscription::findOrFail($id);

        $fromDate = Carbon::parse($request->freeze_from)->startOfDay();
        $toDate = Carbon::parse($request->freeze_to)->startOfDay();
        $freezeDays = $fromDate->diffInDays($toDate) + 1;

        // फ्रीझ टेबलमध्ये रेकॉर्ड सेव्ह करणे
        MembershipFreeze::create([
            'member_subscription_id' => $subscription->id,
            'member_id' => $subscription->member_id,
            'freeze_from' => $fromDate->toDateString(),
            'freeze_to' => $toDate->toDateString(),
            'freeze_days' => $freezeDays,
            'reason' => $request->freeze_reason,
            'approval_reference' => $request->approval_reference ?? null,
            'remarks' => $request->remarks ?? null,
            'status' => 'Frozen',
        ]);

        $currentEndDate = Carbon::parse($subscription->end_date);
        $newEndDate = $currentEndDate->addDays($freezeDays);

        $subscription->update([
            'status' => 'frozen',
            'end_date' => $newEndDate,
            'freeze_reason' => $request->freeze_reason
        ]);

        return redirect()->route('membership.freeze.list')->with('success', 'Membership frozen successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'freeze_from' => 'required|date',
            'freeze_to' => 'required|date|after_or_equal:freeze_from',
            'reason' => 'required|string|max:255',
        ]);

        $freeze = MembershipFreeze::findOrFail($id);
        $subscription = $freeze->subscription;

        if ($subscription) {
            $currentEndDate = Carbon::parse($subscription->end_date);
            $currentEndDate->subDays($freeze->freeze_days); 
            
            $fromDate = Carbon::parse($request->freeze_from)->startOfDay();
            $toDate = Carbon::parse($request->freeze_to)->startOfDay();
            $newFreezeDays = $fromDate->diffInDays($toDate) + 1;

            $updatedEndDate = $currentEndDate->addDays($newFreezeDays);

            $freeze->update([
                'freeze_from' => $fromDate->toDateString(),
                'freeze_to' => $toDate->toDateString(),
                'freeze_days' => $newFreezeDays,
                'reason' => $request->reason,
            ]);

            $subscription->update([
                'end_date' => $updatedEndDate,
                'freeze_reason' => $request->reason
            ]);
        }

        return redirect()->back()->with('success', 'Freeze details updated successfully!');
    }

    public function destroy($id)
    {
        $freeze = MembershipFreeze::find($id);
        
        if ($freeze) {
            $subscription = $freeze->subscription;
            if ($subscription) {
                $newEndDate = Carbon::parse($subscription->end_date)->subDays($freeze->freeze_days);
                $subscription->update([
                    'status' => 'active',
                    'end_date' => $newEndDate,
                    'freeze_reason' => null
                ]);
            }
            $freeze->delete();
        } else {
            $subscription = MemberSubscription::findOrFail($id);
            $subscription->update([
                'status' => 'active',
                'freeze_reason' => null
            ]);
            $subscription->freezes()->delete();
        }

        return redirect()->back()->with('success', 'Freeze record deleted successfully!');
    }

    public function unfreeze($id)
    {
        $subscription = MemberSubscription::findOrFail($id);
        
        $freeze = MembershipFreeze::where('member_subscription_id', $subscription->id)
            ->latest()
            ->first();

        if ($freeze) {
            $newEndDate = Carbon::parse($subscription->end_date)->subDays($freeze->freeze_days);

            $subscription->update([
                'status' => 'active',
                'end_date' => $newEndDate,
                'freeze_reason' => null
            ]);

            $freeze->delete();
        } else {
            $subscription->update([
                'status' => 'active',
                'freeze_reason' => null
            ]);
        }

        return redirect()->back()->with('success', 'Membership successfully unfreezed!');
    }
}