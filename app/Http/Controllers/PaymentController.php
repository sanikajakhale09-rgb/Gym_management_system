<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Receipt; 
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // सर्व पेमेंट्सची लिस्ट दाखवणे
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $payments = Payment::with(['member.latestSubscription.plan', 'member.latestSubscription.trainer'])
            ->when($search, function ($query, $search) {
                return $query->where('invoice_no', 'like', "%{$search}%")
                             ->orWhereHas('member', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        $members = Member::all();

        return view('admin.payments.index', compact('payments', 'members'));
    }

    // पेंडिंग आणि पार्शियल पेमेंट्सची यादी दाखवण्यासाठी फंक्शन
    public function pendingPayments(Request $request)
    {
        $search = $request->input('search');

        $pendingPayments = Payment::with(['member.latestSubscription.plan', 'member.latestSubscription.trainer'])
            ->where(function($query) {
                $query->where('status', 'Pending')
                      ->orWhere('status', 'Partial')
                      ->orWhere('remaining_balance', '>', 0);
            })
            ->when($search, function ($query, $search) {
                return $query->where('invoice_no', 'like', "%{$search}%")
                             ->orWhereHas('member', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        return view('admin.payments.pending', compact('pendingPayments'));
    }

    // इन्व्हॉइसची लिस्ट आणि समरी कार्ड्स दाखवण्यासाठी फंक्शन
    public function invoicesList(Request $request)
    {
        $search = $request->input('search');
        
        $invoices = Payment::with(['member.latestSubscription.plan', 'member.latestSubscription.trainer'])
            ->when($search, function ($query, $search) {
                return $query->where('invoice_no', 'like', "%{$search}%")
                             ->orWhereHas('member', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        $totalInvoicesAmount = Payment::sum('total_amount');
        $paidInvoicesAmount = Payment::where('status', 'Paid')->sum('amount');
        $partialInvoicesAmount = Payment::where('status', 'Partial')->sum('amount');
        $pendingInvoicesAmount = Payment::sum('remaining_balance');

        return view('admin.payments.invoices', compact('invoices', 'totalInvoicesAmount', 'paidInvoicesAmount', 'partialInvoicesAmount', 'pendingInvoicesAmount'));
    }

    // सिंगल इन्व्हॉइस डिटेल, व्ह्यू किंवा प्रिंट करण्यासाठी फंक्शन
    public function showInvoice($id)
    {
        $invoice = Payment::with(['member.latestSubscription.plan', 'member.latestSubscription.trainer'])->findOrFail($id);
        
        return view('admin.payments.index', compact('invoice'))->with('success', 'Viewing via modal.');
    }

    // **नवीन जोडलेली मेथड:** इमेजमधील मोडलसाठी रिसिप्ट डेटा लोड करण्यासाठी (AJAX सपोर्ट)
    public function showReceipt($id)
    {
        try {
            $receipt = Receipt::with(['payment', 'member.branch', 'member.latestSubscription.plan'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'receipt' => $receipt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found!'
            ], 404);
        }
    }

    // इन्व्हॉइसची PDF डाऊनलोड किंवा प्रिन्ट करण्यासाठी सुधारित फंक्शन
    public function downloadPdf($id)
    {
        $payment = Payment::with(['member.branch', 'subscription.plan', 'subscription.trainer'])->findOrFail($id);
        $member = $payment->member;
        $subscription = $payment->subscription ?? ($member ? $member->latestSubscription : null);
        $plan = $subscription ? $subscription->plan : null;

        $totalAmount = $payment->total_amount ?? ($subscription->total_amount ?? 0);
        $amountPaid = $payment->amount ?? ($subscription->amount_paid ?? 0);
        $balanceDue = $payment->remaining_balance ?? ($subscription->balance_amount ?? 0);
        
        // डेटाबेसमधून अचूक रेकॉर्ड्सवरून व्हॅल्यूज घेणे
        $membershipFee = $payment->membership_fee ?? ($plan->price ?? ($plan->amount ?? $totalAmount));
        $registrationFee = $payment->registration_fee ?? 0;
        $discount = $payment->discount ?? 0;
        $tax = $payment->tax ?? 0;
        $trainerCharges = $payment->trainer_charges ?? (($subscription && $subscription->trainer) ? 0 : 0);

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                body {
                    font-family: "DejaVu Sans", sans-serif;
                    color: #2d3748;
                    margin: 0;
                    padding: 15px;
                    font-size: 13px;
                    background-color: #edf2f7;
                }
                .receipt-box {
                    max-width: 750px;
                    margin: auto;
                    padding: 30px;
                    border: none;
                    background: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                }
                .header {
                    text-align: center;
                    margin-bottom: 25px;
                    background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
                    color: white;
                    padding: 22px;
                    border-radius: 8px;
                }
                .header h2 {
                    margin: 0;
                    font-size: 26px;
                    letter-spacing: 1.2px;
                }
                .header p {
                    margin: 8px 0 0;
                    color: #e2e8f0;
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .info-container {
                    background: #f7fafc;
                    border-left: 4px solid #3182ce;
                    padding: 15px;
                    margin-bottom: 20px;
                    border-radius: 0 8px 8px 0;
                }
                .section-title {
                    font-size: 13px;
                    font-weight: bold;
                    color: #2b6cb0;
                    border-bottom: 2px solid #ebf8ff;
                    padding-bottom: 6px;
                    margin-top: 20px;
                    margin-bottom: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .details-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .details-table td {
                    padding: 6px 8px;
                    vertical-align: top;
                    font-size: 13px;
                    line-height: 1.6;
                }
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .items-table th, .items-table td {
                    border: 1px solid #e2e8f0;
                    padding: 10px 14px;
                    text-align: left;
                    font-size: 12px;
                }
                .items-table th {
                    background-color: #ebf8ff;
                    color: #2b6cb0;
                    font-weight: bold;
                    text-transform: uppercase;
                    font-size: 11px;
                    letter-spacing: 0.5px;
                }
                .text-right {
                    text-align: right;
                }
                .footer-note {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 11px;
                    color: #718096;
                    border-top: 1px dashed #cbd5e0;
                    padding-top: 15px;
                }
            </style>
        </head>
        <body>
            <div class="receipt-box">
                <div class="header">
                    <h2>GYM MANAGEMENT SYSTEM</h2>
                    <p>Official Payment & Invoice Receipt</p>
                </div>

                <div class="info-container">
                    <table class="details-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 50%;">
                                <strong>Invoice No:</strong> ' . $payment->invoice_no . '<br>
                                <strong>Receipt Date:</strong> ' . (optional($payment->created_at)->format('d-m-Y') ?? 'N/A') . '<br>
                                <strong>Payment Method:</strong> ' . $payment->payment_method . '<br>
                                <strong>Status:</strong> <span style="color: #38a169; font-weight: bold;">' . $payment->status . '</span>
                            </td>
                            <td style="width: 50%;">
                                <strong>Member Name:</strong> ' . ($member->name ?? 'N/A') . '<br>
                                <strong>Member Code:</strong> ' . ($member->member_code ?? 'N/A') . '<br>
                                <strong>Phone:</strong> ' . ($member->phone ?? 'N/A') . '<br>
                                <strong>Branch:</strong> ' . ($member->branch->name ?? 'Main Branch') . '
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Price Breakdown</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Details</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Membership Fee</strong></td>
                            <td>' . ($plan ? $plan->name : 'Standard Plan') . '</td>
                            <td class="text-right">Rs. ' . number_format($membershipFee, 2) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Fee</strong></td>
                            <td>-</td>
                            <td class="text-right">Rs. ' . number_format($registrationFee, 2) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Discount</strong></td>
                            <td>-</td>
                            <td class="text-right" style="color: #e53e3e;">-Rs. ' . number_format($discount, 2) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Tax (GST/VAT)</strong></td>
                            <td>-</td>
                            <td class="text-right">Rs. ' . number_format($tax, 2) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Trainer Charges</strong></td>
                            <td>' . (($subscription && $subscription->trainer) ? 'Personal Trainer' : '-') . '</td>
                            <td class="text-right">Rs. ' . number_format($trainerCharges, 2) . '</td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td colspan="2" class="text-right"><strong>TOTAL AMOUNT PAYABLE:</strong></td>
                            <td class="text-right"><strong>Rs. ' . number_format($totalAmount, 2) . '</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-bottom: 20px; font-size: 13px;">
                    <p style="margin: 5px 0;"><strong>Amount Paid Now:</strong> <span style="color: #38a169; font-weight: bold;">Rs. ' . number_format($amountPaid, 2) . '</span></p>
                    <p style="margin: 5px 0;"><strong>Received By:</strong> ' . ($payment->staff_name ?? 'Gym Admin') . '</p>
                </div>

                <div style="background-color: #f7fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="text-align: center; width: 33%;">
                                <span style="font-size: 11px; color: #718096; text-transform: uppercase; font-weight: bold;">Invoice Total</span><br>
                                <strong style="font-size: 13px; color: #2d3748;">Rs. ' . number_format($totalAmount, 2) . '</strong>
                            </td>
                            <td style="text-align: center; width: 33%;">
                                <span style="font-size: 11px; color: #718096; text-transform: uppercase; font-weight: bold;">Already Paid</span><br>
                                <strong style="font-size: 13px; color: #38a169;">Rs. ' . number_format($amountPaid, 2) . '</strong>
                            </td>
                            <td style="text-align: center; width: 33%;">
                                <span style="font-size: 11px; color: #718096; text-transform: uppercase; font-weight: bold;">Balance Due</span><br>
                                <strong style="font-size: 13px; color: #e53e3e;">Rs. ' . number_format($balanceDue, 2) . '</strong>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footer-note">
                    <p>Thank you for your payment!</p>
                </div>
            </div>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('payment-receipt-' . $payment->invoice_no . '.pdf');
    }

    // पेंडिंग किंवा पार्शियल रकमेची वसुली (Collect Due Payment)
    public function collect(Request $request, $id)
    {
        $request->validate([
            'paying_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $payment = Payment::findOrFail($id);
            $payingAmount = $request->paying_amount;

            $payment->amount += $payingAmount;
            $payment->remaining_balance -= $payingAmount;

            if ($payment->remaining_balance <= 0) {
                $payment->remaining_balance = 0;
                $payment->status = 'Paid';
            } else {
                $payment->status = 'Partial';
            }

            $payment->payment_method = $request->payment_method;
            $payment->save();

            if ($payment->member && $payment->member->latestSubscription) {
                $subscription = $payment->member->latestSubscription;
                
                $subscription->amount_paid += $payingAmount;
                $subscription->balance_amount = $subscription->total_amount - $subscription->amount_paid;
                
                if ($subscription->balance_amount < 0) {
                    $subscription->balance_amount = 0;
                }
                
                $subscription->save();
            }

            $latestReceipt = Receipt::latest()->first();
            $receiptNumber = $latestReceipt ? 'REC-2026-' . str_pad((int)substr($latestReceipt->receipt_number, -3) + 1, 3, '0', STR_PAD_LEFT) : 'REC-2026-001';

            Receipt::create([
                'receipt_number'   => $receiptNumber,
                'invoice_id'       => $payment->id,
                'payment_id'       => $payment->id,
                'member_id'        => $payment->member_id,
                'receipt_date'     => now(),
                'amount'           => $payingAmount,
                'payment_method'   => $request->payment_method,
                'transaction_id'   => $request->transaction_id ?? null,
                'received_by'      => auth()->user()->name ?? 'Admin',
                
                // ब्रेकडाऊन डेटा पास केला (जेणेकरून रिसिप्ट मोडलमध्ये झिरो दिसणार नाही)
                'membership_fee'   => $payment->membership_fee ?? 0,
                'registration_fee' => $payment->registration_fee ?? 0,
                'discount'         => $payment->discount ?? 0,
                'tax'              => $payment->tax ?? 0,
                'trainer_charges'  => $payment->trainer_charges ?? 0,
            ]);

            return redirect()->back()->with('success', 'Payment collected and receipt generated successfully!');
        });
    }

    // नवीन पेमेंट रेकॉर्ड करणे (Store)
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'total_amount' => 'required|numeric|min:0', 
            'amount' => 'required|numeric|min:0',      
            'payment_method' => 'required|string',
            'status' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = $request->total_amount;
            $paidAmount = $request->amount;
            $remainingBalance = $totalAmount - $paidAmount;

            $latestInvoice = Payment::latest()->first();
            $invoiceNo = $latestInvoice ? 'INV-2026-' . str_pad((int)substr($latestInvoice->invoice_no, -3) + 1, 3, '0', STR_PAD_LEFT) : 'INV-2026-001';

            // जर फॉर्ममधून ब्रेकडाऊन व्हॅल्यूज येत असतील तर त्या घ्या, नसतर डिफॉल्ट 0 ठेवा
            $membershipFee = $request->membership_fee ?? $totalAmount;
            $registrationFee = $request->registration_fee ?? 0;
            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $trainerCharges = $request->trainer_charges ?? 0;

            $payment = Payment::create([
                'invoice_no'        => $invoiceNo,
                'member_id'         => $request->member_id,
                'total_amount'      => $totalAmount,
                'amount'            => $paidAmount, 
                'remaining_balance' => $remainingBalance, 
                'payment_method'    => $request->payment_method,
                'transaction_id'    => $request->transaction_id ?? null,
                'status'            => $request->status,
                'staff_name'        => auth()->user()->name ?? 'Admin',
                'notes'             => $request->notes,
                
                // इथे मुख्य पेमेंट टेबलमध्ये ब्रेकडाऊन सेव्ह होईल
                'membership_fee'    => $membershipFee,
                'registration_fee'  => $registrationFee,
                'discount'          => $discount,
                'tax'               => $tax,
                'trainer_charges'   => $trainerCharges,
            ]);

            $member = Member::with('latestSubscription')->find($request->member_id);
            if ($member && $member->latestSubscription) {
                $sub = $member->latestSubscription;
                $sub->amount_paid = $paidAmount;
                $sub->balance_amount = $remainingBalance;
                $sub->save();
            }

            if ($paidAmount > 0) {
                $latestReceipt = Receipt::latest()->first();
                $receiptNumber = $latestReceipt ? 'REC-2026-' . str_pad((int)substr($latestReceipt->receipt_number, -3) + 1, 3, '0', STR_PAD_LEFT) : 'REC-2026-001';

                Receipt::create([
                    'receipt_number'   => $receiptNumber,
                    'invoice_id'       => $payment->id,
                    'payment_id'       => $payment->id,
                    'member_id'        => $request->member_id,
                    'receipt_date'     => now(),
                    'amount'           => $paidAmount,
                    'payment_method'   => $request->payment_method,
                    'transaction_id'   => $request->transaction_id ?? null,
                    'received_by'      => auth()->user()->name ?? 'Admin',
                    
                    // इथे रिसिप्ट टेबलमध्ये ब्रेकडाऊन सेव्ह होईल
                    'membership_fee'   => $membershipFee,
                    'registration_fee' => $registrationFee,
                    'discount'         => $discount,
                    'tax'              => $tax,
                    'trainer_charges'  => $trainerCharges,
                ]);
            }

            return redirect()->route('payments.index')->with('success', 'Payment recorded and receipt generated successfully!');
        });
    }

    // पेमेंट अपडेट करणे (Update)
    public function update(Request $request, $id)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'total_amount' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'status' => 'required|string',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $payment = Payment::findOrFail($id);

            $totalAmount = $request->total_amount;
            $paidAmount = $request->amount;
            $remainingBalance = $totalAmount - $paidAmount;

            $payment->update([
                'member_id' => $request->member_id,
                'total_amount' => $totalAmount,
                'amount' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
        });
    }

    // पेमेंट रेकॉर्ड डिलीट करणे
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully!');
    }
}