<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipPlan;
use App\Models\Branch;
use App\Models\MembershipPurchase;
use App\Models\MemberSubscription;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $gymId = $user->gym_id ?? 1;
        $branchId = $request->input('branch_id');

        $query = MembershipPlan::where('gym_id', $gymId);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $plans = $query->with('branch')->get();
        $branches = Branch::all();

        return view('admin.membership-plans.index', compact('plans', 'branches'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.membership-plans.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'registration_fee' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'duration_days' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $gymId = Auth::user()->gym_id ?? 1;

        MembershipPlan::create([
            'gym_id' => $gymId,
            'branch_id' => $request->input('branch_id') ?? (Auth::user()->branch_id ?? 1),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'registration_fee' => $request->input('registration_fee') ?? 0,
            'discount' => $request->input('discount') ?? 0,
            'tax' => $request->input('tax') ?? 0,
            'duration_days' => $request->input('duration_days'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('membership.plans.index')->with('success', 'Plan added successfully!');
    }

    public function edit(int $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $branches = Branch::all();
        return view('admin.membership-plans.edit', compact('plan', 'branches'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'registration_fee' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'duration_days' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $plan = MembershipPlan::findOrFail($id);
        $plan->update([
            'branch_id' => $request->input('branch_id') ?? $plan->branch_id,
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'registration_fee' => $request->input('registration_fee') ?? 0,
            'discount' => $request->input('discount') ?? 0,
            'tax' => $request->input('tax') ?? 0,
            'duration_days' => $request->input('duration_days'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('membership.plans.index')->with('success', 'Plan updated successfully!');
    }

    public function destroy(int $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->delete();

        return redirect()->route('membership.plans.index')->with('success', 'Plan deleted successfully!');
    }

    // ==========================================
    // नवीन मेंबरशिप खरेदी आणि रिसिप्टचे लॉजिक
    // ==========================================

    public function storePurchase(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_amount' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'balance_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'due_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
        ]);

        $gymId = Auth::user()->gym_id ?? 1;
        $branchId = Auth::user()->branch_id ?? 1;

        // १. डेटाबेसमध्ये मेंबरशिप पर्चेस सेव्ह करणे (रसीद/पेमेंट ट्रॅकिंगसाठी)
        $purchase = MembershipPurchase::create([
            'gym_id' => $gymId,
            'branch_id' => $branchId,
            'member_id' => $request->input('member_id'),
            'membership_plan_id' => $request->input('membership_plan_id'),
            'trainer_id' => $request->input('trainer_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'total_amount' => $request->input('total_amount'),
            'amount_paid' => $request->input('amount_paid'),
            'balance_amount' => $request->input('balance_amount'),
            'payment_method' => $request->input('payment_method'),
            'payment_status' => $request->input('payment_status'),
            'due_date' => $request->input('due_date'),
        ]);

        // २. जर मेंबरची आधी कोणती ॲक्टिव्ह मेंबरशिप असेल तर ती 'expired' करणे
        MemberSubscription::where('member_id', $request->input('member_id'))
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // ३. मुख्य: MemberSubscription टेबलमध्ये नवीन रेकॉर्ड सेव्ह करणे (अमाऊंटसह)
        MemberSubscription::create([
            'gym_id' => $gymId,
            'branch_id' => $branchId,
            'member_id' => $request->input('member_id'),
            'membership_plan_id' => $request->input('membership_plan_id'),
            'trainer_id' => $request->input('trainer_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'amount_paid' => $request->input('amount_paid'),       // 👈 हे ॲड केले आहे
            'balance_amount' => $request->input('balance_amount'), // 👈 हे पेडिंग अमाऊंटसाठी ॲड केले आहे
            'status' => 'active', 
        ]);

        // सेव्ह झाल्यावर थेट रिसिप्ट पेजवर रीडायरेक्ट करणे
        return redirect()->route('membership.receipt', $purchase->id)
                         ->with('success', 'Membership purchased successfully & Receipt generated!');
    }

    public function receipt($id)
    {
        // रिसिप्ट पेजसाठी माहिती फेच करणे
        $purchase = MembershipPurchase::with(['member', 'plan', 'branch'])->findOrFail($id);

        return view('admin.membership-purchase.receipt', compact('purchase'));
    }
}