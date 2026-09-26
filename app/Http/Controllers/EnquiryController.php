<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Http\Controllers\Controller;
use Carbon\Carbon; // 🟢 Carbon class import kela ahe

class EnquiryController extends Controller
{
    // Sarv enquiry list dakhवण्यासाठी
    public function index()
    {
        $enquiries = Enquiry::latest()->get();
        return view('dashboard.enquiries.index', compact('enquiries'));
    }

    // Navin enquiry form dakhवण्यासाठी
    public function create()
    {
        return view('dashboard.create');
    }

    // Navin enquiry database madhe save karnyasathi
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => 'nullable|email|max:255',
            'status' => 'required|string',
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        Enquiry::create($request->all());

        return redirect()->route('enquiries.index')->with('success', 'Enquiry successfully added!');
    }

    // SMS Gateway kadun yenara data receive karnyasathi (Webhook)
    public function storeFromSms(Request $request)
    {
        $phone =$request->input('From', $request->input('phone', 'Unknown'));$message = $request->input('Body',$request->input('message', 'No Message'));

        Enquiry::create([
            'name' => 'SMS User / Unknown',
            'phone' => $phone,
            'email' => null,
            'message' => $message, 
            'source' => 'SMS',    
            'status' => 'New',    
        ]);

        return response()->json(['status' => 'success', 'message' => 'Enquiry recorded successfully']);
    }

    // 🟢 Sarv follow-ups ani leads dakhवण्यासाठी update kelele function (Date ani Status filters sobat)
    public function followUps(Request $request)
    {
        // 🟢 Jar tarikh nighun geli asel ani status 'Pending' asel, tr automatic 'Overdue' hoil
        Enquiry::where('follow_up_date', '<', Carbon::today())
            ->where('status', 'Pending')
            ->update(['status' => 'Overdue']);

        $query = Enquiry::query();

        // 🟢 Status nusar filter (Pending, Completed, Contacted, Overdue)
        if ($request->has('status') &&$request->status != '') {
            $query->where('status',$request->status);
        }

        // 🟢 Date nusar filter (Yesterday, Today, Tomorrow) - अचूक तारीख मॅच करण्यासाठी सुधारणा केली आहे
        if ($request->has('filter') &&$request->filter != '') {
            $filter =$request->filter;
            if ($filter == 'yesterday') {$query->whereDate('follow_up_date', Carbon::yesterday()->toDateString());
            } elseif ($filter == 'today') {$query->whereDate('follow_up_date', Carbon::today()->toDateString());
            } elseif ($filter == 'tomorrow') {$query->whereDate('follow_up_date', Carbon::tomorrow()->toDateString());
            }
        }

        $todayFollowups =$query->latest()->get();

        return view('dashboard.enquiries.follow-ups', compact('todayFollowups'));
    }

    // 🟢 Add kelele update function (Remark madhe 'visited' alyas 'Completed' hoil)
    public function update(Request $request, Enquiry$enquiry)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => 'nullable|email|max:255',
            'status' => 'required|string',
            'follow_up_response' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $data =$request->all();

        if ($request->has('follow_up_response') && stripos($request->follow_up_response, 'visited') !== false) {$data['status'] = 'Completed';
        }

        $enquiry->update($data);

        return redirect()->back()->with('success', 'Follow-up successfully updated!');
    }

    // 🟢 Add kelele destroy function
    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('enquiries.index')->with('success', 'Enquiry successfully deleted!');
    }

    // 🟢 Navin add kelele updateStatus function
    public function updateStatus(Request $request,$id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->status =$request->status;
        $enquiry->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    // 🟢 Navin WhatsApp Reply function
    public function whatsappReply(Request $request,$id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        $enquiry->status = 'Contacted';$enquiry->save();

        $phone = preg_replace('/[^0-9]/', '', (string) ($enquiry->phone ?? $enquiry->mobile_number));
        if (strlen($phone) === 10) {
            $phone = '91' .$phone;
        }

        $text = urlencode($request->input('text', ''));$whatsappUrl = "https://wa.me/{$phone}?text={$text}";

        return redirect()->away($whatsappUrl);
    }

    // 🟢 Update kelele WhatsApp Reply function
    public function replyWhatsApp(Request $request,$id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->status = 'Contacted';$enquiry->save();

        $phone = $enquiry->phone ?? $enquiry->mobile_number;
        $message =$request->input('text', '');

        $whatsappPhone = preg_replace('/[^0-9]/', '', (string)$phone);
        if (strlen($whatsappPhone) === 10) {
            $whatsappPhone = '91' .$whatsappPhone;
        }

        $whatsappUrl = "https://wa.me/{$whatsappPhone}?text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'whatsapp_url' => $whatsappUrl,
            'message' => 'Status updated successfully!'
        ]);
    }

    // 🟢 Navin `sendSms` function
    public function sendSms(Request $request,$id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'SMS sent successfully!'
        ]);
    }

    // 🟢 Navin `sendWhatsAppAndUpdateStatus` function
    public function sendWhatsAppAndUpdateStatus(Request $request,$id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        if ($request->has('status')) {$enquiry->status = $request->input('status');$enquiry->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp status updated successfully!'
        ]);
    }
}