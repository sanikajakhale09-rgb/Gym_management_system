<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquiry;

class WebhookController extends Controller
{
    public function handleSmsWebhook(Request $request)
    {
        $mobileNumber = $request->input('From') ?? $request->input('mobile');
        $messageBody  = $request->input('Body') ?? $request->input('message');

        Enquiry::create([
            'name'          => 'SMS Lead (' . ($mobileNumber ?? 'Unknown') . ')',
            'mobile_number' => $mobileNumber,
            'source'        => 'SMS',
            'message'       => $messageBody,
            'status'        => 'New',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'SMS enquiry saved successfully'
        ], 200);
    }
}