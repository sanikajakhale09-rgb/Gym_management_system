<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        // 🟢 'payment' आणि 'member' रिलेशन लोड केले आहे
        $query = Receipt::with(['member', 'payment']);

        // Search Filter (Receipt Number, Invoice No, Member Name, Phone - Flexible multi-word search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('payment', function($payQuery) use ($search) {
                      $payQuery->where('invoice_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('member', function($memQuery) use ($search) {
                      $memQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");

                      // जर नावात स्पेस असेल (उदा. Mayuri Pawar), तर दोन्ही शब्द वेगवेगळे शोधेल
                      $terms = explode(' ', $search);
                      if (count($terms) > 1) {
                          foreach ($terms as $term) {
                              if (!empty(trim($term))) {
                                  $memQuery->orWhere('name', 'like', "%{$term}%");
                              }
                          }
                      }
                  });
            });
        }

        // 🟢 Date From (कुठून ते) फिल्टर
        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }

        // 🟢 Date To (कुठपर्यंतचे) फिल्टर
        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        // 🟢 Payment Method (Cash, Online, etc.) फिल्टर
        if ($request->filled('payment_method') && $request->payment_method !== 'All' && $request->payment_method !== 'All Methods') {
            $query->where('payment_method', $request->payment_method);
        }

        $receipts = $query->latest()->paginate(10);

        return view('admin.payments.receipt', compact('receipts'));
    }

    // 🟢 मॉडेल किंवा व्ह्यूसाठी रिसिप्टचा डेटा JSON स्वरूपात मिळवण्यासाठी (Updated with price breakdown mapping)
    public function show($id)
    {
        $receipt = Receipt::with(['member.branch', 'payment'])->findOrFail($id);
        
        $data = $receipt->toArray();
        if ($receipt->payment) {
            $data['membership_fee'] = $receipt->membership_fee ?? $receipt->payment->base_amount ?? 0;
            $data['registration_fee'] = $receipt->registration_fee ?? $receipt->payment->registration_fee ?? 0;
            $data['discount'] = $receipt->discount ?? $receipt->payment->discount ?? 0;
            $data['tax'] = $receipt->tax ?? $receipt->payment->gst_amount ?? 0;
            $data['trainer_charges'] = $receipt->trainer_charges ?? $receipt->payment->trainer_fee ?? 0;
            $data['total_amount'] = $receipt->total_amount ?? $receipt->payment->total_amount ?? $receipt->amount;
        }

        return response()->json($data);
    }

    // 🟢 नवीन ॲड केलेली showReceipt मेथड
    public function showReceipt($id)
    {
        $receipt = Receipt::with(['invoice', 'member', 'membership'])->findOrFail($id);
        return view('receipts.show', compact('receipt'));
    }

    // 🟢 एरर दूर करण्यासाठी मेथडचे नाव 'downloadPDF' केले आहे
    public function downloadPDF($id)
    {
        $receipt = Receipt::with(['member.branch', 'payment'])->findOrFail($id);
        
        $invoiceNo = optional($receipt->payment)->invoice_no ?? 'INV-N/A';
        $invoiceTotal = optional($receipt->payment)->total_amount ?? $receipt->amount;
        $alreadyPaid = $receipt->amount;
        $balanceDue = optional($receipt->payment)->remaining_balance ?? 0;

        // Dynamic Branch Name for PDF header
        $branchName = optional(optional($receipt->member)->branch)->name ?? 'GYM NAME';

        // Price Breakdown variables
        $membershipFee = optional($receipt->payment)->base_amount ?? ($receipt->amount * 0.7);
        $registrationFee = optional($receipt->payment)->registration_fee ?? 0;
        $discount = optional($receipt->payment)->discount ?? 0;
        $taxAmount = optional($receipt->payment)->gst_amount ?? ($receipt->amount * 0.18);
        $trainerCharges = optional($receipt->payment)->trainer_fee ?? 0;

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Payment Receipt</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.6; }
                .receipt-box { max-width: 700px; margin: auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; }
                .header { text-align: center; color: #007bff; margin-bottom: 20px; }
                .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .table th, .table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                .summary-box { background: #f9f9f9; padding: 15px; margin-top: 20px; border-radius: 5px; text-align: center; }
                .summary-table { width: 100%; text-align: center; }
            </style>
        </head>
        <body>
            <div class="receipt-box">
                <h2 class="header">' . $branchName . '</h2>
                <h3 style="text-align: center; margin-top: -10px;">PAYMENT RECEIPT</h3>
                <hr>
                <table style="width: 100%; margin-bottom: 20px; border: none;">
                    <tr>
                        <td style="border: none;"><strong>Receipt No:</strong> ' . $receipt->receipt_number . '</td>
                        <td style="border: none; text-align: right;"><strong>Receipt Date:</strong> ' . \Carbon\Carbon::parse($receipt->receipt_date)->format('d-m-Y') . '</td>
                    </tr>
                    <tr>
                        <td style="border: none;"><strong>Invoice No:</strong> ' . $invoiceNo . '</td>
                        <td style="border: none; text-align: right;"><strong>Payment Method:</strong> ' . $receipt->payment_method . '</td>
                    </tr>
                </table>

                <div style="background: #fdfdfd; padding: 12px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 20px;">
                    <p style="margin: 4px 0;"><strong>Member Name:</strong> ' . optional($receipt->member)->name . '</p>
                    <p style="margin: 4px 0;"><strong>Member ID:</strong> GYM-2026-' . str_pad(optional($receipt->member)->id, 5, '0', STR_PAD_LEFT) . '</p>
                    <p style="margin: 4px 0;"><strong>Mobile:</strong> ' . optional($receipt->member)->phone . '</p>
                </div>

                <h4 style="margin-bottom: 5px; border-bottom: 2px solid #007bff; padding-bottom: 5px;">Price Breakdown</h4>
                <table class="table">
                    <tr>
                        <td>Membership Fee</td>
                        <td style="text-align: right;">Rs. ' . number_format($membershipFee, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Registration Fee</td>
                        <td style="text-align: right;">Rs. ' . number_format($registrationFee, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td style="text-align: right; color: red;">-Rs. ' . number_format($discount, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Tax (GST/VAT)</td>
                        <td style="text-align: right;">Rs. ' . number_format($taxAmount, 2) . '</td>
                    </tr>
                    <tr>
                        <td>Trainer Charges</td>
                        <td style="text-align: right;">Rs. ' . number_format($trainerCharges, 2) . '</td>
                    </tr>
                    <tr style="background-color: #f1f1f1; font-weight: bold;">
                        <td>TOTAL AMOUNT PAYABLE</td>
                        <td style="text-align: right;">Rs. ' . number_format($invoiceTotal, 2) . '</td>
                    </tr>
                </table>

                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <p style="margin: 4px 0;"><strong>Amount Paid Now:</strong> <span style="color: green;">Rs. ' . number_format($receipt->amount, 2) . '</span></p>
                    <p style="margin: 4px 0;"><strong>Received By:</strong> ' . ($receipt->received_by ?? 'Admin') . '</p>
                </div>

                <div class="summary-box">
                    <table class="summary-table">
                        <tr>
                            <td><strong>Invoice Total</strong><br><span style="font-size: 16px;">Rs. ' . number_format($invoiceTotal, 2) . '</span></td>
                            <td><strong>Already Paid</strong><br><span style="font-size: 16px; color: green;">Rs. ' . number_format($alreadyPaid, 2) . '</span></td>
                            <td><strong>Balance Due</strong><br><span style="font-size: 16px; color: red;">Rs. ' . number_format($balanceDue, 2) . '</span></td>
                        </tr>
                    </table>
                </div>

                <p style="text-align: center; margin-top: 30px; font-style: italic; color: #666;">Thank you for your payment!</p>
            </div>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->download('payment-receipt-' . $receipt->receipt_number . '.pdf');
    }

    // 🟢 नवीन ॲड केलेली destroy मेथड (एरर सॉल्व्ह करण्यासाठी)
    public function destroy($id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->delete();

        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully!');
    }
}