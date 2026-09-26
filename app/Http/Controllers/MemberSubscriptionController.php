<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberSubscription;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\MembershipFreeze;
use App\Models\Branch;
use App\Models\Trainer;
use App\Models\Payment; 
use App\Models\Receipt; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class MemberSubscriptionController extends Controller
{
    // १. मेंबरशिप खरेदी किंवा असाइन करण्याचा फॉर्म दाखवणे
    public function create()
    {
        $members = Member::all();
        $plans = MembershipPlan::all();
        $trainers = Trainer::all(); 
        
        return view('admin.membership-purchase.create', compact('members', 'plans', 'trainers'));
    }

    // २. नवीन मेंबरशिप सेव्ह करणे आणि ऑटोमॅटिक PDF डाऊनलोड करणे
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'start_date' => 'required|date',
            'total_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'amount_paid' => 'nullable|numeric',
            'payment_method' => 'nullable|string', 
            'due_date' => 'nullable|date', 
        ]);

        $plan = MembershipPlan::findOrFail($request->membership_plan_id);
        $startDate = Carbon::parse($request->start_date);
        
        $durationDays = ($plan->duration_months ?? 1) * 30; // kinwa plan madhye direct days aslyas ti field wapura
        $endDate = (clone $startDate)->addDays($durationDays - 1);
        
        $totalAmount = $request->input('total_amount', $plan->price ?? 0);
        $paidAmount = $request->input('paid_amount', $request->input('amount_paid', $totalAmount));
        $balanceAmount = max(0, $totalAmount - $paidAmount); 

        $subscription = MemberSubscription::create([
            'member_id' => $request->member_id,
            'membership_plan_id' => $request->membership_plan_id,
            'trainer_id' => $request->input('trainer_id'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active', 
            'total_amount' => $totalAmount,
            'amount_paid' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'price_paid' => $paidAmount,
            'payment_method' => $request->input('payment_method', 'Cash'), 
            'due_date' => $balanceAmount > 0 ? $request->input('due_date') : null, 
        ]);

        $member = Member::find($request->member_id);
        if ($member) {
            $member->update([
                'membership_plan' => $plan->name, 
                'paid_amount' => $paidAmount,    
                'trainer_id' => $request->input('trainer_id') ?? $member->trainer_id, 
                'payment_method' => $request->input('payment_method', 'Cash'), 
                'expiry_date' => $endDate, // 🟢 Hi line add keli aahe yithe expiry date save hoil
                'status' => 'Active'            
            ]);
        }

        $latestInvoice = Payment::latest()->first();
        $invoiceNo = $latestInvoice ? 'INV-2026-' . str_pad((int)substr($latestInvoice->invoice_no, -3) + 1, 3, '0', STR_PAD_LEFT) : 'INV-2026-001';

        $paymentStatus = ($balanceAmount <= 0) ? 'Paid' : 'Partial';

        $membershipFee = $request->input('membership_fee', $plan->price ?? $totalAmount);

        $payment = Payment::create([
            'invoice_no' => $invoiceNo,
            'member_id' => $request->member_id,
            'total_amount' => $totalAmount,
            'amount' => $paidAmount,
            'remaining_balance' => $balanceAmount,
            'membership_fee' => $membershipFee, 
            'registration_fee' => $request->input('registration_fee', 0),
            'discount' => $request->input('discount', 0),
            'tax' => $request->input('tax', 0),
            'trainer_charges' => $request->input('trainer_charges', 0),
            'payment_method' => $request->input('payment_method', 'Cash'),
            'status' => $paymentStatus,
            'staff_name' => auth()->user()->name ?? 'Admin',
            'notes' => 'Generated automatically via Membership Purchase',
        ]);

        $latestReceipt = Receipt::latest()->first();
        $receiptNumber = $latestReceipt ? 'RCP-' . (intval(str_replace('RCP-', '', $latestReceipt->receipt_number)) + 1) : 'RCP-1001';

        Receipt::create([
            'receipt_number' => $receiptNumber,
            'invoice_id' => $payment->id, 
            'member_id' => $request->member_id,
            'payment_id' => $payment->id,
            'amount' => $paidAmount,
            'payment_method' => $request->input('payment_method', 'Cash'),
            'receipt_date' => now(),
            'received_by' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->route('members.index')->with([
            'success' => 'Member registered successfully!',
            'downloadPdfId' => $subscription->id
        ]);
    }

    // रिसीट PDF डाऊनलोड करण्यासाठी मेथड
    public function downloadReceiptPdf($id)
    {
        $subscription = MemberSubscription::with(['member', 'membershipPlan', 'trainer'])->findOrFail($id);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Gym Membership Receipt</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.5; }
                .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
                .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .table th, .table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
            </style>
        </head>
        <body>
            <div class="invoice-box">
                <h2>🏋️‍♂️ GYM MANAGEMENT SYSTEM</h2>
                <p><strong>Receipt ID:</strong> #GYM-' . $subscription->id . '</p>
                <p><strong>Date:</strong> ' . optional($subscription->created_at)->format('d-m-Y') . '</p>
                <hr>
                <h4>Member Details:</h4>
                <p><strong>Name:</strong> ' . optional($subscription->member)->name . '</p>
                <p><strong>Phone:</strong> ' . optional($subscription->member)->phone . '</p>

                <table class="table">
                    <tr>
                        <th>Plan / Item</th>
                        <th>Amount</th>
                    </tr>
                    <tr>
                        <td>' . optional($subscription->membershipPlan)->name . '</td>
                        <td>Rs. ' . number_format($subscription->total_amount, 2) . '</td>
                    </tr>
                </table>

                <h3 style="text-align: right; margin-top: 20px;">Total Paid: Rs. ' . number_format($subscription->amount_paid, 2) . '</h3>
                <h4 style="text-align: right; color: red;">Balance Due: Rs. ' . number_format($subscription->balance_amount, 2) . '</h4>
            </div>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->download('membership-receipt-' . $subscription->id . '.pdf');
    }

    // ४. ॲक्टिव्ह मेंबरशिप्सची यादी दाखवणे
    public function activeMemberships()
    {
        $today = Carbon::today();

        $activeSubscriptions = MemberSubscription::with(['member', 'membershipPlan'])
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today) 
            ->latest()
            ->get();

        $plans = MembershipPlan::all(); 
        $trainers = Trainer::all(); 

        return view('admin.membership-purchase.active', compact('activeSubscriptions', 'plans', 'trainers')); 
    }

    // नवीन: मेंबरशिप अपडेट करण्यासाठी method (Due date fix केली)
    public function update(Request $request, $id)
    {
        $request->validate([
            'membership_plan_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount_paid' => 'required|numeric',
        ]);

        $subscription = MemberSubscription::findOrFail($id);
        
        $subscription->update([
            'membership_plan_id' => $request->membership_plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'due_date' => $request->input('due_date', $request->input('next_due_date')), 
            'amount_paid' => $request->amount_paid,
            'balance_amount' => $request->balance_amount ?? 0,
        ]);

        return redirect()->back()->with('success', 'Membership updated successfully!');
    }

    // ५. मेंबरशिप रिन्यू करण्यासाठी फॉर्म दाखवणे
    public function renewMembership($id)
    {
        $subscription = MemberSubscription::with(['member', 'membershipPlan'])->findOrFail($id);
        $member = $subscription->member; 
        $plans = MembershipPlan::all(); 
        $trainers = Trainer::all(); 

        return view('admin.members.renew', compact('subscription', 'member', 'plans', 'trainers')); 
    }

    // ६. रिन्यूअल डेटा सेव्ह करणे
    public function storeRenewMembership(Request $request, $id)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'start_date' => 'required|date',
            'total_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'amount_paid' => 'nullable|numeric',
            'due_date' => 'nullable|date', 
        ]);

        $oldSubscription = MemberSubscription::findOrFail($id);
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);
        
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::parse($oldSubscription->start_date);
        $endDate = (clone $startDate)->addMonths($plan->duration_months ?? 1);

        $totalAmount = $request->input('total_amount', $plan->price ?? 0);
        $paidAmount = $request->input('paid_amount', $request->input('amount_paid', $totalAmount));
        $balanceAmount = max(0, $totalAmount - $paidAmount);

        if ($oldSubscription->member) {
            $oldSubscription->member->update([
                'membership_plan' => $plan->name, 
                'paid_amount' => $paidAmount, 
                'trainer_id' => $request->trainer_id ?? $oldSubscription->member->trainer_id,
                'pt_charges' => $request->pt_charges ?? $oldSubscription->member->pt_charges,
                'expiry_date' => $endDate, // Yithe pan renew nantar expiry date update hoil
                'status' => 'Active' 
            ]);
        }

        $oldSubscription->update([
            'membership_plan_id' => $request->membership_plan_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $totalAmount,
            'amount_paid' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'price_paid' => $paidAmount, 
            'status' => 'active',
            'due_date' => $balanceAmount > 0 ? $request->input('due_date') : null, 
        ]);

        return redirect()->route('membership.active')->with('success', 'Membership renewed successfully!');
    }

    // ७. मेंबरशिप फ्रीझ फॉर्म दाखवणे
    public function showFreezeForm($id)
    {
        $subscription = MemberSubscription::with('member')->findOrFail($id);
        return view('memberships.freeze-form', compact('subscription'));
    }

    // ८. मेंबरशिप फ्रीझ करणे
    public function freezeMembership(Request $request, $id)
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
            'freeze_reason' => $request->freeze_reason,
        ]);

        return redirect()->back()->with('success', 'Membership frozen successfully!');
    }

    // ९. मेंबरशिप अनफ्रीझ करणे
    public function unfreezeMembership($id)
    {
        $subscription = MemberSubscription::findOrFail($id);

        $freeze = MembershipFreeze::where('member_subscription_id', $subscription->id)
            ->latest()
            ->first();

        $today = Carbon::today();

        if ($freeze) {
            $freezeFrom = Carbon::parse($freeze->freeze_from);
            $freezeTo = Carbon::parse($freeze->freeze_to);

            if ($today->lt($freezeTo)) {
                $remainingDays = $today->max($freezeFrom)->diffInDays($freezeTo) + 1;
                $newEndDate = Carbon::parse($subscription->end_date)->subDays($remainingDays);
            } else {
                $newEndDate = Carbon::parse($subscription->end_date)->subDays($freeze->freeze_days);
            }

            $subscription->update([
                'status' => 'active',
                'end_date' => $newEndDate,
                'freeze_reason' => null,
            ]);

            $freeze->delete();
        } else {
            $subscription->update([
                'status' => 'active',
                'freeze_reason' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Membership unfreezed successfully and end date recalculated!');
    }

    // १०. सर्व रिन्यूअल्सची यादी
    public function renewalsList()
    {
        $renewals = MemberSubscription::with(['member', 'membershipPlan'])->latest()->get();
        return view('admin.membership-purchase.renewals', compact('renewals'));
    }

    // ११. विशिष्ट रिन्यूअल पाहणे
    public function viewRenewal($id)
    {
        $renewal = MemberSubscription::with(['member', 'membershipPlan'])->findOrFail($id);
        return view('admin.members.view-renewal', compact('renewal'));
    }

    // १२. रिन्यूअल अपडेट करणे
    public function updateRenewal(Request $request, $id)
    {
        $renewal = MemberSubscription::findOrFail($id);
        return redirect()->back()->with('success', 'Renewal updated successfully!');
    }

    // १३. रिन्यूअल रेकॉर्ड डिलीट करणे
    public function destroyRenewal($id)
    {
        $renewal = MemberSubscription::findOrFail($id);
        $renewal->delete();

        return redirect()->back()->with('success', 'Renewal record deleted successfully!');
    }

    // १५. मेंबरशिप डिलीट करण्यासाठी नवी मेथड
    public function destroy($id)
    {
        $subscription = MemberSubscription::find($id);
        
        if ($subscription) {
            $subscription->delete();
            return redirect()->back()->with('success', 'Membership deleted successfully!');
        }

        return redirect()->back()->with('error', 'Membership record not found.');
    }

    // १४. Expiring / Expired Memberships List 
    public function expiringExpiredList(Request $request)
    {
        $today = Carbon::today();
        
        $query = MemberSubscription::with(['member.branch', 'member.trainer', 'membershipPlan']);

        $filterTab = $request->input('tab'); 
        
        if ($filterTab == 'expired') {
            $query->whereDate('end_date', '<', $today);
        } elseif ($filterTab == 'today') {
            $query->whereDate('end_date', '=', $today);
        } elseif ($filterTab == '3days') {
            $query->whereBetween('end_date', [$today->copy()->addDay(), $today->copy()->addDays(3)]);
        } elseif ($filterTab == '7days') {
            $query->whereBetween('end_date', [$today->copy()->addDay(), $today->copy()->addDays(7)]);
        }

        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($request->filled('trainer_id')) {
            $trainerId = $request->trainer_id;
            $query->whereHas('member', function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            });
        }

        if ($request->filled('plan_name')) {
            $planId = $request->input('plan_name');
            $query->where('membership_plan_id', $planId);
        } elseif ($request->filled('membership_plan_id')) {
            $planId = $request->input('membership_plan_id');
            $query->where('membership_plan_id', $planId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $members = $query->paginate(10)->withQueryString();

        $branches = Branch::all();
        $trainers = Trainer::all();
        $plans = MembershipPlan::all();

        return view('admin.membership-purchase.expiring-expired', compact('members', 'branches', 'trainers', 'plans', 'filterTab'));
    }
}