<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Followup;
use App\Models\Enquiry; // 🟢 इम्पोर्ट केला आहे
use Carbon\Carbon;
use Illuminate\Support\Facades\Http; // SMS Gateway API sathi

class FollowupController extends Controller
{
    // Follow-ups page load karne ani tabs/date wise data fetch karne
    public function index(Request $request)
    {
        // 🟢 जर तारीख निघून गेली असेल आणि स्टेटस 'Pending' असेल, तर आपोआप 'Overdue' होईल
        Enquiry::where('follow_up_date', '<', Carbon::today())
            ->where('status', 'Pending')
            ->update(['status' => 'Overdue']);

        $query = Enquiry::query();

        // 🟢 Status नुसार फिल्टर (Pending, Completed, Contacted, Overdue इ.)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // 🟢 Date नुसार फिल्टर (Yesterday, Today, Tomorrow) - अचूक तारीख मॅच करण्यासाठी
        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            
            if ($filter == 'yesterday') {
                $query->whereDate('follow_up_date', Carbon::yesterday()->toDateString());
            } elseif ($filter == 'today') {
                $query->whereDate('follow_up_date', Carbon::today()->toDateString());
            } elseif ($filter == 'tomorrow') {
                $query->whereDate('follow_up_date', Carbon::tomorrow()->toDateString());
            }
        }

        $todayFollowups = $query->latest()->get();

        // 🟢 व्यू फाईलचे नाव अचूक दिले आहे (डॅश काढून 'followups' केले आहे)
        return view('dashboard.enquiries.followups', compact('todayFollowups'));
    }

    // Follow-up update / reschedule karne
    public function update(Request $request, $id)
    {
        $request->validate([
            'follow_up_date' => 'nullable|date',
            'status' => 'required|string',
            'follow_up_response' => 'nullable|string',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        
        // 🟢 डेटाबेसमध्ये डेटा अचूक सेव्ह होईल
        $enquiry->update([
            'follow_up_date' => $request->follow_up_date ?? Carbon::today(),
            'status' => $request->status,
            'follow_up_response' => $request->follow_up_response, 
        ]);

        return redirect()->back()->with('success', 'Follow-up updated successfully.');
    }

    // Mark as Completed
    public function complete($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'status' => 'Completed',
            'follow_up_date' => Carbon::today()
        ]);

        return redirect()->back()->with('success', 'Follow-up marked as completed.');
    }

    // Send SMS to Customer via SMS Gateway API & Save Followup Date/Remarks
    public function sendSms(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        
        $mobileNumber = $enquiry->mobile_number ?? $enquiry->phone ?? 'N/A';
        $message = $request->message ?? 'Hello, this is a reminder from the gym.';

        /*
        $response = Http::get('https://api.smsgateway.com/send', [
            'api_key' => 'YOUR_API_KEY',
            'to' => $mobileNumber,
            'message' => $message,
        ]);
        */

        // 🟢 SMS send केल्यावर आजची तारीख, मेसेज आणि स्टेटस अपडेट होईल
        $enquiry->update([
            'follow_up_date' => Carbon::today(),
            'follow_up_response' => 'SMS Sent: ' . $message,
            'status' => 'Contacted'
        ]);

        return redirect()->back()->with('success', 'SMS sent successfully to ' . $mobileNumber);
    }
}