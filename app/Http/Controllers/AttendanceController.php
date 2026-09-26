<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Attendance Main Index Page (Today's Attendance daakhvnyasathi)
    public function index(Request $request)
    {
        $branchId = $request->input('branch_id');
        $branches = Branch::all();

        $attendances = Attendance::with(['member', 'branch'])
            ->whereDate('date', Carbon::today('Asia/Kolkata'))
            ->when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })->latest()
            ->get();

        return view('admin.attendance.Todayattendance', compact('attendances', 'branches', 'branchId'));
    }

    // Mark Attendance Page View
    public function markAttendance(Request $request)
    {
        $branchId = $request->input('branch_id');
        $branches = Branch::all();
        
        $members = Member::when($branchId, function ($query, $branchId) {
            return $query->where('branch_id', $branchId);
        })->get();

        return view('admin.attendance.mark', compact('members', 'branches', 'branchId'));
    }

    // Store Attendance (भारतीय वेळेनुसार चॅक-इन टाईम सेव्ह करण्यासाठी)
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:Present,Absent'
        ]);

        Attendance::create([
            'member_id' => $request->member_id,
            'branch_id' => $request->branch_id,
            'date' => Carbon::today('Asia/Kolkata'),
            'check_in_time' => Carbon::now('Asia/Kolkata')->format('H:i:s'), // भारतीय वेळ (IST)
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Attendance marked successfully!');
    }

    // Today's Attendance List
    public function todayAttendance(Request $request)
    {
        $branchId = $request->input('branch_id');
        $branches = Branch::all();

        $attendances = Attendance::with(['member', 'branch'])
            ->whereDate('date', Carbon::today('Asia/Kolkata'))
            ->when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })->latest()
            ->get();

        return view('admin.attendance.Todayattendance', compact('attendances', 'branches', 'branchId'));
    }

    // ==========================================
    // Attendance Reports Method (दुरुस्ती केलेले)
    // ==========================================
    public function attendanceReports(Request $request)
    {
        $query = Attendance::with(['member', 'branch']);

        // तारखेनुसार फिल्टर (From Date आणि To Date)
        if ($request->has('from_date') && $request->has('to_date') && $request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        } else {
            // डीफॉल्ट अलीकडचा डेटा दाखवण्यासाठी
            $query->whereDate('created_at', '>=', now()->subDays(30));
        }

        // मेंबर आयडीनुसार फिल्टर
        if ($request->has('member_id') && $request->member_id) {
            $query->where('member_id', $request->member_id);
        }

        // ब्रँचनुसार फिल्टर (असेल तर)
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $reports = $query->latest()->paginate(15);
        $members = Member::all();
        $branches = Branch::all();

        return view('admin.attendance.reports', compact('reports', 'members', 'branches'));
    }

    // Check-out Method
    public function checkout($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $attendance->update([
            'check_out_time' => Carbon::now('Asia/Kolkata')->format('H:i:s'),
            'status' => 'Checked-Out'
        ]);

        return redirect()->back()->with('success', 'Member checked out successfully!');
    }

    // Update Attendance Method
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'check_in_time' => 'required',
        ]);

        $attendance->update([
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
        ]);

        return redirect()->back()->with('success', 'Attendance updated successfully!');
    }

    // Delete Attendance Method
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Attendance deleted successfully!');
    }

    // ==========================================
    // QR / ID Check-in Methods
    // ==========================================

    public function qrCheckin()
    {
        return view('admin.attendance.qr-checkin');
    }

    public function storeQrCheckin(Request $request)
    {
        $request->validate([
            'member_identifier' => 'required|string',
        ]);

        $identifier = $request->member_identifier;

        $member = Member::where('id', $identifier)
                        ->orWhere('phone', $identifier)
                        ->first();

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member Not Found!'
            ], 404);
        }

        if (strtolower($member->status) !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership Expired! Please renew.'
            ], 400);
        }

        $todayAttendance = Attendance::where('member_id', $member->id)
                                     ->whereDate('date', Carbon::today('Asia/Kolkata'))
                                     ->first();

        if ($todayAttendance) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Member has already checked in today!'
            ], 200);
        }

        Attendance::create([
            'member_id' => $member->id,
            'branch_id' => $member->branch_id ?? 1,
            'date' => Carbon::today('Asia/Kolkata'),
            'check_in_time' => Carbon::now('Asia/Kolkata')->format('H:i:s'),
            'status' => 'Present',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful for ' . $member->name
        ], 200);
    }

    // ==========================================
    // 🟢 Mobile Self Check-in & Check-out Methods
    // ==========================================

    public function showSelfCheckinForm()
    {
        return view('admin.attendance.self-checkin');
    }

    public function storeSelfCheckin(Request $request)
    {
        $request->validate([
            'member_identifier' => 'required|string',
            'action_type' => 'nullable|in:checkin,checkout'
        ]);

        $identifier = $request->input('member_identifier');
        $actionType = $request->input('action_type', 'checkin');

        $member = Member::where('id', $identifier)
                    ->orWhere('phone', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found! Please check your ID or Phone number.'
            ], 404);
        }

        if (strtolower($member->status) !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership Expired! Please renew your plan.'
            ], 400);
        }

        $today = Carbon::today('Asia/Kolkata');
        $currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

        $existingAttendance = Attendance::where('member_id', $member->id)
                                ->whereDate('date', $today)
                                ->first();

        if ($actionType === 'checkout') {
            if (!$existingAttendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No check-in record found for today to check out!'
                ], 400);
            }

            if (!empty($existingAttendance->check_out_time)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'You have already checked out today at ' . $existingAttendance->check_out_time
                ], 200);
            }

            $existingAttendance->update([
                'check_out_time' => $currentTime,
                'status' => 'Checked-Out'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Goodbye ' . $member->name . '! Check-out successful.'
            ], 200);
        }

        if ($existingAttendance) {
            if (empty($existingAttendance->check_out_time)) {
                return response()->json([
                    'status' => 'already_checked_in',
                    'message' => 'Welcome back ' . $member->name . '! You are already checked in. Do you want to Check-out?'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'info',
                    'message' => 'You have already completed your attendance for today!'
                ], 200);
            }
        }

        Attendance::create([
            'member_id' => $member->id,
            'branch_id' => $member->branch_id ?? 1,
            'date' => $today,
            'check_in_time' => $currentTime,
            'status' => 'Present'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Success! Check-in recorded for ' . $member->name
        ], 200);
    }
}