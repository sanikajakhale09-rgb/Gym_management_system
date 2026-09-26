<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Branch;
use App\Models\Trainer;
use App\Models\MembershipPlan;
use App\Models\FitnessGoal;
use App\Models\MemberSubscription;
use App\Models\MembershipFreeze;
use App\Models\Payment; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller {
    
    private function checkAndAutoUnfreezeMembers($members)
    {
        if ($members instanceof \Illuminate\Pagination\LengthAwarePaginator || $members instanceof \Illuminate\Pagination\Paginator) {
            $collection = $members->getCollection();
        } else {
            $collection = $members instanceof \Illuminate\Support\Collection ? $members : collect([$members]);
        }

        foreach ($collection as $member) {
            foreach ($member->subscriptions as $subscription) {
                if (strtolower($subscription->status) === 'frozen') {
                    $latestFreeze = MembershipFreeze::where('member_subscription_id', $subscription->id)->latest()->first();
                    
                    if ($latestFreeze) {
                        $freezeTo = Carbon::parse($latestFreeze->freeze_to)->endOfDay();
                        
                        if (Carbon::now()->greaterThan($freezeTo)) {
                            if (Schema::hasColumn('membership_freezes', 'unfrozen_at') && !$latestFreeze->unfrozen_at) {
                                $latestFreeze->update([
                                    'unfrozen_at' => Carbon::now(),
                                ]);
                            }

                            $subscription->update([
                                'status' => 'active',
                                'freeze_reason' => null,
                            ]);

                            $member->update([
                                'status' => 'Active',
                                'expiry_date' => $subscription->end_date
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function index(Request $request)
    {
        $query = Member::with([
            'branch', 
            'membershipPlan', 
            'trainer', 
            'subscriptions.freezes', 
            'latestSubscription.plan', 
            'latestSubscription.trainer'
        ]);

        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(10);

        foreach ($members as $member) {
            $latestSub = $member->latestSubscription;
            
            if ($latestSub && $latestSub->end_date) {
                $endDate = Carbon::parse($latestSub->end_date)->endOfDay();
                if (Carbon::now()->greaterThan($endDate)) {
                    $latestSub->update(['status' => 'expired']);
                    $member->update(['status' => 'Expired']);
                } else {
                    $latestSub->update(['status' => 'active']);
                    $member->update(['status' => 'Active']);
                }
            } else {
                $member->update(['status' => 'Active']);
            }

            $totalPaid = Payment::where('member_id', $member->id)->sum('amount');
            
            if ($totalPaid == 0) {
                $totalPaid = $latestSub->amount_paid ?? 0;
            }
            
            if ($latestSub) {
                $latestSub->calculated_amount_paid = $totalPaid;
            } else {
                $member->calculated_amount_paid = 0;
            }
        }

        $this->checkAndAutoUnfreezeMembers($members);
        $branches = Branch::all(); 

        $downloadPdfId = session('download_pdf_member_id');
        if($downloadPdfId) {
            session()->forget('download_pdf_member_id');
        }

        return view('admin.members.index', compact('members', 'branches', 'downloadPdfId'));
    }

    public function create() {
        $branches = Branch::all();
        $plans = MembershipPlan::all(); 
        return view('admin.members.create', compact('branches', 'plans'));
    }

    public function store(Request $request) {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => 'required|email:rfc,dns|unique:members,email',
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'reference_source' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => ['nullable', 'regex:/^[6-9]\d{9}$/'],
            'joining_date' => 'required|date',
            'document_path' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048'
        ], [
            'phone.regex' => 'The phone number must be a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'phone.required' => 'The phone number field is mandatory.',
            'email.email' => 'Please enter a valid and active email address format.',
            'email.unique' => 'This email address is already registered with another member.',
            'emergency_contact_phone.regex' => 'The emergency contact number must be a valid 10-digit Indian mobile number.'
        ]);

        $docPath = null;
        if ($request->hasFile('document_path')) {
            $docPath = $request->file('document_path')->store('member_documents', 'public');
        }

        $branch = Branch::find($request->branch_id);
        $branchName = $branch ? $branch->name : 'GYM';
        $cleanBranchName = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $branchName), 0, 3));
        $prefix = $cleanBranchName !== '' ? $cleanBranchName : 'GYM';

        $uniqueNumber = random_int(10000, 99999);
        while (Member::where('member_code', $prefix . '-' . $uniqueNumber)->exists()) {
            $uniqueNumber = random_int(10000, 99999);
        }

        $generatedMemberCode = $prefix . '-' . $uniqueNumber;

        $joiningDate = Carbon::parse($request->joining_date);
        $expiryDate = null; 

        $member = new Member();
        $member->member_code = $generatedMemberCode;
        $member->branch_id = $request->branch_id;
        $member->name = $request->name;
        $member->gender = $request->gender;
        $member->date_of_birth = $request->date_of_birth;
        $member->phone = $request->phone;
        $member->email = $request->email;
        $member->occupation = $request->occupation;
        $member->address = $request->address;
        $member->blood_group = $request->blood_group;
        $member->reference_source = $request->reference_source;
        $member->emergency_contact_name = $request->emergency_contact_name;
        $member->emergency_contact_phone = $request->emergency_contact_phone;
        $member->joining_date = $joiningDate;
        
        if (Schema::hasColumn('members', 'expiry_date')) {
            $member->expiry_date = $expiryDate;
        }

        $member->status = 'Active';

        // Fakt jar form madhun plan select kela asel tarach plan ghenar, anyatha 'N/A' set hoil
        $planId = $request->membership_plan_id ?? $request->membership_plan ?? $request->plan_id;

        if (Schema::hasColumn('members', 'membership_plan')) {
            if ($planId) {
                $planModelForName = MembershipPlan::find($planId);
                $member->membership_plan = $planModelForName ? $planModelForName->name : 'Standard';
            } else {
                $member->membership_plan = 'N/A';
            }
        }

        $member->document_path = $docPath;
        $member->save();

        // Jar plan select kela asel tarach subscription ani payment entry padel, anyatha nahi
        if ($planId) {
            $planModel = MembershipPlan::find($planId);
            $durationMonths = $planModel ? ($planModel->duration_months ?? 1) : 1;
            $endDateCalc = $joiningDate->copy()->addMonths($durationMonths);
            
            $totalAmt = $request->total_amount ?? ($planModel->price ?? ($request->amount_paid ?? 0));
            $amtPaid = $request->amount_paid ?? 0;
            $balAmt = max(0, $totalAmt - $amtPaid);
            $paymentStatus = $balAmt <= 0 ? 'Paid' : 'Partial';

            $subscription = MemberSubscription::create([
                'member_id' => $member->id,
                'membership_plan_id' => $planModel->id ?? $planId,
                'trainer_id' => $request->trainer_id ?? null,
                'start_date' => $joiningDate,
                'end_date' => $request->expiry_date ?? $endDateCalc,
                'total_amount' => $totalAmt,
                'amount_paid' => $amtPaid,
                'price_paid' => $amtPaid,
                'balance_amount' => $balAmt,
                'payment_method' => $request->payment_method ?? 'Cash',
                'payment_status' => $paymentStatus,
                'status' => 'active',
            ]);

            $member->update(['expiry_date' => $endDateCalc]);

            $latestInvoice = Payment::latest()->first();
            $invoiceNo = $latestInvoice ? 'INV-2026-' . str_pad((int)substr($latestInvoice->invoice_no, -3) + 1, 3, '0', STR_PAD_LEFT) : 'INV-2026-001';

            Payment::create([
                'invoice_no' => $invoiceNo,
                'member_id' => $member->id,
                'member_subscription_id' => $subscription->id,
                'total_amount' => $totalAmt,
                'amount' => $amtPaid,
                'remaining_balance' => $balAmt,
                'payment_method' => $request->payment_method ?? 'Cash',
                'status' => $paymentStatus,
                'staff_name' => auth()->user()->name ?? 'Admin',
                'notes' => 'Generated automatically via Member Registration',
            ]);
        }

        return redirect()->route('members.index')->with([
            'success' => 'Member registered successfully!',
            'download_pdf_member_id' => $planId ? $member->id : null
        ]);
    }

    public function show(Member $member) {
        $member->load(['branch', 'subscriptions.freezes', 'latestSubscription.plan', 'latestSubscription.trainer']);
        $this->checkAndAutoUnfreezeMembers($member);

        $today = now()->format('l'); 
        $todaysWorkouts = collect();

        return view('admin.members.show', compact('member', 'today', 'todaysWorkouts'));
    }

    public function edit(int $id) {
        $member = Member::with(['latestSubscription.plan', 'latestSubscription.trainer'])->findOrFail($id);
        $branches = Branch::all();
        $plans = MembershipPlan::all(); 
        $trainers = Trainer::all();    
        
        return view('admin.members.edit', compact('member', 'branches', 'plans', 'trainers'));
    }

    public function update(Request $request, int $id) {
        $member = Member::findOrFail($id);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => 'required|email:rfc,dns|unique:members,email,' . $id,
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'reference_source' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => ['nullable', 'regex:/^[6-9]\d{9}$/'],
            'joining_date' => 'required|date', 
            'status' => 'required',
            'document_path' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048'
        ], [
            'email.unique' => 'This email address is already registered with another member.'
        ]);

        $data = [
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'gender' => $request->filled('gender') ? $request->gender : $member->gender,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'email' => $request->email,
            'occupation' => $request->occupation,
            'address' => $request->address,
            'blood_group' => $request->blood_group,
            'reference_source' => $request->reference_source,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'joining_date' => $request->joining_date, 
            'status' => $request->status,
        ];

        if ($request->hasFile('document_path')) {
            $data['document_path'] = $request->file('document_path')->store('member_documents', 'public');
        }

        $member->update($data);

        $latestSub = $member->latestSubscription;
        if ($latestSub && ($request->has('total_amount') || $request->has('membership_plan_id'))) {
            $latestSub->update([
                'membership_plan_id' => $request->membership_plan_id ?? $latestSub->membership_plan_id,
                'trainer_id' => $request->trainer_id ?? $latestSub->trainer_id,
                'total_amount' => $request->total_amount ?? $latestSub->total_amount,
                'amount_paid' => $request->amount_paid ?? $latestSub->amount_paid,
                'price_paid' => $request->amount_paid ?? $latestSub->price_paid,
                'balance_amount' => $request->balance_amount ?? $latestSub->balance_amount,
                'payment_method' => $request->payment_method ?? $latestSub->payment_method,
            ]);
        }

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    public function destroy(int $id) {
        $member = Member::withTrashed()->findOrFail($id);
        
        foreach($member->subscriptions as $subscription) {
            $subscription->freezes()->delete();
        }
        $member->subscriptions()->delete();
        
        $member->forceDelete(); 

        return redirect()->route('members.index')->with('success', 'Member permanently deleted successfully!');
    }

    public function documents() {
        $members = Member::with(['latestSubscription.plan', 'latestSubscription.trainer', 'subscriptions.freezes'])->latest()->get();
        $this->checkAndAutoUnfreezeMembers($members);
        return view('admin.members.documents', compact('members'));
    }

    public function search(Request $request) {
        $search = $request->input('search');

        $members = Member::with(['branch', 'latestSubscription.plan', 'latestSubscription.trainer', 'subscriptions.freezes'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('member_code', 'like', "%{$search}%");
            })->latest()->paginate(10);

        $this->checkAndAutoUnfreezeMembers($members);

        if ($request->ajax()) {
            return view('admin.members.partials.table-rows', compact('members'))->render();
        }

        return view('admin.members.search', compact('members', 'search'));
    }

    public function receipt(int $id) {
        $member = Member::with(['branch', 'latestSubscription.plan', 'latestSubscription.trainer'])->findOrFail($id);
        $subscription = $member->latestSubscription;
        $plan = $subscription ? $subscription->plan : null;

        $totalAmount = $subscription->total_amount ?? $subscription->total ?? $subscription->amount ?? 0;
        $amountPaid = $subscription->amount_paid ?? $subscription->paid ?? 0;
        $balanceDue = $subscription->balance_amount ?? $subscription->balance ?? 0;
        
        $membershipFee = (!empty($subscription->membership_fee)) 
            ? $subscription->membership_fee 
            : ($subscription->price ?? ($plan->price ?? $plan->amount ?? 0));

        $registrationFee = (!empty($subscription->registration_fee)) 
            ? $subscription->registration_fee 
            : 0;

        $discount = $subscription->discount ?? ($plan->discount ?? 0);

        $discountPercentage = 0;
        if ($membershipFee > 0 && $discount != 0) {
            $discountPercentage = round((abs($discount) / $membershipFee) * 100, 1);
        }

        $tax = $subscription->tax ?? $subscription->gst ?? $subscription->vat ?? 0;
        $trainerCharges = $subscription->trainer_charges ?? $subscription->trainer_fee ?? 0;

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body {
                    font-family: Helvetica, Arial, sans-serif;
                    color: #2d3748;
                    margin: 0;
                    padding: 10px;
                    font-size: 13px;
                    background-color: #f7fafc;
                }
                .receipt-box {
                    max-width: 750px;
                    margin: auto;
                    padding: 20px 25px;
                    border: 1px solid #e2e8f0;
                    background: #fff;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
                    color: white;
                    padding: 18px;
                    border-radius: 6px;
                }
                .header h2 {
                    margin: 0;
                    font-size: 24px;
                    letter-spacing: 1px;
                }
                .header p {
                    margin: 6px 0 0;
                    color: #cbd5e0;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .section-title {
                    font-size: 14px;
                    font-weight: bold;
                    color: #2b6cb0;
                    border-bottom: 2px solid #bee3f8;
                    padding-bottom: 5px;
                    margin-top: 15px;
                    margin-bottom: 12px;
                    text-transform: uppercase;
                }
                .details-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .details-table td {
                    padding: 7px 8px;
                    vertical-align: top;
                    font-size: 13px;
                    line-height: 1.5;
                }
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .items-table th, .items-table td {
                    border: 1px solid #e2e8f0;
                    padding: 8px 12px;
                    text-align: left;
                    font-size: 12px;
                }
                .items-table th {
                    background-color: #ebf8ff;
                    color: #2b6cb0;
                    font-weight: bold;
                }
                .text-right {
                    text-align: right;
                }
                .footer-note {
                    margin-top: 25px;
                    text-align: center;
                    font-size: 11px;
                    color: #718096;
                    border-top: 1px dashed #cbd5e0;
                    padding-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="receipt-box">
                <div class="header">
                    <h2>GYM MANAGEMENT SYSTEM</h2>
                    <p>Official Membership & Payment Receipt</p>
                </div>

                <div class="section-title">Member Information</div>
                <table class="details-table">
                    <tr>
                        <td style="width: 50%;">
                            <strong>Receipt ID:</strong> #GYM-' . $member->id . '<br>
                            <strong>Date:</strong> ' . date('d-m-Y') . '<br>
                            <strong>Member Code:</strong> ' . $member->member_code . '<br>
                            <strong>Branch:</strong> ' . ($member->branch->name ?? 'N/A') . '<br>
                            <strong>Full Name:</strong> ' . $member->name . '<br>
                            <strong>Gender:</strong> ' . ($member->gender ?? 'N/A') . '<br>
                            <strong>Date of Birth:</strong> ' . ($member->date_of_birth ?? 'N/A') . '<br>
                            <strong>Phone Number:</strong> ' . $member->phone . '<br>
                            <strong>Email Address:</strong> ' . ($member->email ?? 'N/A') . '
                        </td>
                        <td style="width: 50%;">
                            <strong>Occupation:</strong> ' . ($member->occupation ?? 'N/A') . '<br>
                            <strong>Blood Group:</strong> ' . ($member->blood_group ?? 'N/A') . '<br>
                            <strong>Address:</strong> ' . ($member->address ?? 'N/A') . '<br>
                            <strong>Reference Source:</strong> ' . ($member->reference_source ?? 'N/A') . '<br>
                            <strong>Emergency Contact Name:</strong> ' . ($member->emergency_contact_name ?? 'N/A') . '<br>
                            <strong>Emergency Contact Phone:</strong> ' . ($member->emergency_contact_phone ?? 'N/A') . '<br>
                            <strong>Joining Date:</strong> ' . $member->joining_date . '<br>
                            <strong>Status:</strong> <span style="color: green; font-weight: bold;">' . $member->status . '</span>
                        </td>
                    </tr>
                </table>

                <div class="section-title">Price Breakdown & Subscription Details</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Validity</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Membership Fee:</strong> ' . ($plan ? $plan->name : 'Monthly Basic') . '</td>
                            <td>' . ($subscription ? $subscription->start_date . ' to ' . $subscription->end_date : 'N/A') . '</td>
                            <td class="text-right">INR ' . number_format($membershipFee, 2) . '</td>
                        </tr>';

        if ($registrationFee > 0) {
            $html .= '<tr>
                <td colspan="2">Registration Fee</td>
                <td class="text-right">INR ' . number_format($registrationFee, 2) . '</td>
            </tr>';
        }

        if ($discount != 0) {
            $html .= '<tr>
                <td colspan="2">Discount' . ($discountPercentage > 0 ? ' (' . $discountPercentage . '%)' : '') . '</td>
                <td class="text-right" style="color: #e53e3e;">- INR ' . number_format(abs($discount), 2) . '</td>
            </tr>';
        }

        if ($tax > 0) {
            $html .= '<tr>
                <td colspan="2">Tax (GST / VAT)</td>
                <td class="text-right">INR ' . number_format($tax, 2) . '</td>
            </tr>';
        }

        if ($trainerCharges > 0) {
            $html .= '<tr>
                <td colspan="2">Trainer Charges (' . ($subscription->trainer->name ?? 'Personal Trainer') . ')</td>
                <td class="text-right">INR ' . number_format($trainerCharges, 2) . '</td>
            </tr>';
        }

        $html .= '<tr>
                            <td colspan="2" class="text-right"><strong>Total Amount Payable:</strong></td>
                            <td class="text-right"><strong>INR ' . number_format($totalAmount, 2) . '</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right" style="color: #4a5568;"><strong>Amount Paid (Via ' . ($subscription->payment_method ?? 'Card') . '):</strong></td>
                            <td class="text-right" style="color: #38a169;"><strong>- INR ' . number_format($amountPaid, 2) . '</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right" style="color: #4a5568;"><strong>Balance Due / Remaining:</strong></td>
                            <td class="text-right" style="color: #e53e3e;"><strong>INR ' . number_format($balanceDue, 2) . '</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer-note">
                    <p>Thank you for choosing our Gym! Keep pushing your limits. This is a computer-generated receipt.</p>
                </div>
            </div>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('Gym_Receipt_' . $member->id . '.pdf');
    }
    
    public function downloadReceipt(int $id) {
        return $this->receipt($id);
    }

    public function exportPDF() {
        $members = Member::with(['branch'])->get();
        $html = '<h2 style="text-align: center;">Gym Members List</h2>';
        $html .= '<table border="1" cellspacing="0" cellpadding="8" style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 10px;">';
        $html .= '<thead style="background-color: #f2f2f2;"><tr><th>ID</th><th>Name</th><th>Gender</th><th>Phone</th><th>Email</th><th>Blood Group</th><th>Joining Date</th><th>Status</th></tr></thead><tbody>';

        foreach ($members as $member) {
            $html .= '<tr><td>' . $member->id . '</td><td>' . $member->name . '</td><td>' . ($member->gender ?? 'N/A') . '</td><td>' . $member->phone . '</td><td>' . $member->email . '</td><td>' . ($member->blood_group ?? 'N/A') . '</td><td>' . $member->joining_date . '</td><td>' . $member->status . '</td></tr>';
        }

        $html .= '</tbody></table>';
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('gym-members.pdf');
    }

    public function exportExcel() {
        return Excel::download(new class implements FromCollection, WithHeadings, WithMapping {
            public function collection() {
                return Member::with(['branch'])->get();
            }

            public function headings(): array {
                return ['ID', 'Member Code', 'Name', 'Gender', 'Date of Birth', 'Phone', 'Email', 'Blood Group', 'Occupation', 'Address', 'Emergency Contact Name', 'Emergency Contact Phone', 'Reference Source', 'Joining Date', 'Status', 'Document Path'];
            }

            public function map($member): array {
                return [
                    $member->id,
                    $member->member_code,
                    $member->name,
                    $member->gender,
                    $member->date_of_birth,
                    $member->phone,
                    $member->email,
                    $member->blood_group,
                    $member->occupation,
                    $member->address,
                    $member->emergency_contact_name,
                    $member->emergency_contact_phone,
                    $member->reference_source,
                    $member->joining_date,
                    $member->status,
                    $member->document_path,
                ];
            }
        }, 'gym-members.xlsx');
    }

    public function storeSubscription(Request $request)
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
            'due_date' => 'nullable|date',
            'membership_fee' => 'nullable|numeric',
            'registration_fee' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'trainer_charges' => 'nullable|numeric',
        ]);

        $subscription = MemberSubscription::create([
            'member_id' => $request->member_id,
            'membership_plan_id' => $request->membership_plan_id,
            'trainer_id' => $request->trainer_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_amount' => $request->total_amount,
            'amount_paid' => $request->amount_paid,
            'price_paid' => $request->amount_paid,
            'balance_amount' => $request->balance_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'due_date' => $request->due_date,
            'status' => 'active',
            'membership_fee' => $request->membership_fee ?? 0,
            'registration_fee' => $request->registration_fee ?? 0,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'trainer_charges' => $request->trainer_charges ?? 0,
        ]);

        $member = Member::findOrFail($request->member_id);
        $member->update([
            'status' => 'Active',
            'expiry_date' => $request->end_date
        ]);

        $latestInvoice = Payment::latest()->first();
        $invoiceNo = $latestInvoice ? 'INV-2026-' . str_pad((int)substr($latestInvoice->invoice_no, -3) + 1, 3, '0', STR_PAD_LEFT) : 'INV-2026-001';

        Payment::create([
            'invoice_no' => $invoiceNo,
            'member_id' => $request->member_id,
            'member_subscription_id' => $subscription->id, 
            'total_amount' => $request->total_amount,
            'amount' => $request->amount_paid,
            'remaining_balance' => $request->balance_amount,
            'payment_method' => $request->payment_method,
            'status' => $request->payment_status,
            'staff_name' => auth()->user()->name ?? 'Admin',
            'notes' => 'Generated automatically via Membership Purchase',
        ]);

        return response()->json([
            'success' => true,
            'member_id' => $request->member_id,
            'download_receipt_id' => $subscription->id, 
            'message' => 'Membership purchased and payment recorded successfully!'
        ]);
    }

    public function idCard($id)
    {
        $member = Member::with(['branch', 'membershipPlan', 'trainer'])->findOrFail($id);
        
        return view('admin.members.id-card', compact('member'));
    }

    public function renew($id)
    {
        $member = Member::with(['latestSubscription.plan', 'latestSubscription.trainer'])->findOrFail($id);
        $plans = MembershipPlan::all();
        $trainers = Trainer::all();
        
        return view('admin.members.renew', compact('member', 'plans', 'trainers'));
    }

    public function storeRenewal(Request $request, $id)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'trainer_charges' => 'nullable|numeric',
        ]);

        $member = Member::findOrFail($id);
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);

        $startDate = Carbon::now();
        $durationMonths = $plan->duration_months ?? 1;
        $endDate = $startDate->copy()->addMonths($durationMonths);

        $planPrice = $plan->price ?? 0;
        $trainerCharges = $request->trainer_charges ?? 0;
        $totalAmount = $planPrice + $trainerCharges;
        $amountPaid = $request->amount_paid;
        $balanceAmount = max(0, $totalAmount - $amountPaid);
        $paymentStatus = $balanceAmount <= 0 ? 'Paid' : 'Partial';

        $subscription = MemberSubscription::create([
            'member_id' => $member->id,
            'membership_plan_id' => $plan->id,
            'trainer_id' => $request->trainer_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'price_paid' => $amountPaid,
            'balance_amount' => $balanceAmount,
            'payment_method' => $request->payment_method,
            'payment_status' => $paymentStatus,
            'status' => 'active',
            'membership_fee' => $planPrice,
            'trainer_charges' => $trainerCharges,
        ]);

        $member->update([
            'status' => 'Active',
            'expiry_date' => $endDate
        ]);

        $latestInvoice = Payment::latest()->first();
        $invoiceNo = $latestInvoice ? 'INV-2026-' . str_pad((int)substr($latestInvoice->invoice_no, -3) + 1, 3, '0', STR_PAD_LEFT) : 'INV-2026-001';

        Payment::create([
            'invoice_no' => $invoiceNo,
            'member_id' => $member->id,
            'member_subscription_id' => $subscription->id,
            'total_amount' => $totalAmount,
            'amount' => $amountPaid,
            'remaining_balance' => $balanceAmount,
            'payment_method' => $request->payment_method,
            'status' => $paymentStatus,
            'staff_name' => auth()->user()->name ?? 'Admin',
            'notes' => 'Generated automatically via Membership Renewal',
        ]);

        return redirect()->route('members.index')->with('success', 'Membership renewed successfully!');
    }
}