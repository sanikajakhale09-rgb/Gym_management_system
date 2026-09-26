<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\User; // Import User model for role assignment
use Illuminate\Support\Facades\Hash; // Import Hash facade for password generation
use Carbon\Carbon;

class StaffController extends Controller {
    
    public function index(Request $request) {
        $query = Staff::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staffList = $query->latest()->paginate(10);

        // Summary Cards Data
        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'Active')->count();
        $inactiveStaff = Staff::where('status', 'Inactive')->count();
        $totalTrainers = Staff::where('role', 'Trainer')->count();

        return view('admin.staff.index', compact(
            'staffList', 'totalStaff', 'activeStaff', 'inactiveStaff', 'totalTrainers'
        ));
    }

    public function create() {
        // Find the latest staff ID and generate the next sequential ID
        $lastStaff = Staff::latest('id')->first();
        $nextIdNumber = $lastStaff ? intval(substr($lastStaff->staff_id, 3)) + 1 : 1;
        $nextStaffId = 'STF' . str_pad($nextIdNumber, 3, '0', STR_PAD_LEFT);

        return view('admin.staff.create', compact('nextStaffId'));
    }

    public function store(Request $request) {
        $request->validate([
            'staff_id' => 'required|unique:staff,staff_id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|digits:10|unique:staff,mobile', 
            'email' => 'nullable|email|max:255|unique:staff,email', 
            'role' => 'required|string',
            'branch' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'status' => 'required|string',
            'employment_type' => 'nullable|string',
            'salary_type' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('profile_photo');

        // Handle Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/staff'), $filename);
            $data['profile_photo'] = 'uploads/staff/' . $filename;
        }

        $staff = Staff::create($data);

        // Receptionist kinva staff sathi aaptop-aap user account create karne
        if ($request->filled('email')) {
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'mobile' => $request->mobile,
                    'password' => Hash::make('password123'), // Default password
                ]
            );
            
            // Spatie role assign karne
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles($request->role);
            }
        }

        return redirect()->route('staff.index')->with('success', 'Staff saved successfully.');
    }

    public function show($id) {
        $staff = Staff::findOrFail($id);
        return view('admin.staff.show', compact('staff'));
    }

    // Fetch staff details for AJAX
    public function showJson($id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json($staff);
    }

    public function edit($id) {
        $staff = Staff::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id) {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|digits:10|unique:staff,mobile,' . $id, 
            'email' => 'nullable|email|max:255|unique:staff,email,' . $id, 
            'role' => 'required|string',
            'branch' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'employment_type' => 'nullable|string',
            'salary_type' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('profile_photo');

        if ($request->hasFile('profile_photo')) {
            if ($staff->profile_photo && file_exists(public_path($staff->profile_photo))) {
                @unlink(public_path($staff->profile_photo));
            }

            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/staff'), $filename);
            $data['profile_photo'] = 'uploads/staff/' . $filename;
        }

        $staff->update($data);

        // Find the corresponding User after update and sync the role
        $user = User::where('email', $staff->email)->orWhere('mobile', $staff->mobile)->first();
        if ($user) {
            $user->syncRoles($request->role);
        }

        return redirect()->route('staff.index')->with('success', 'Staff details updated successfully!');
    }

    public function destroy($id) {
        $staff = Staff::findOrFail($id);
        
        if ($staff->profile_photo && file_exists(public_path($staff->profile_photo))) {
            @unlink(public_path($staff->profile_photo));
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully!');
    }

    public function deactivate($id) {
        $staff = Staff::findOrFail($id);
        $staff->update(['status' => 'Inactive']);

        return redirect()->back()->with('success', 'Staff member deactivated successfully!');
    }

    // ==========================================
    // STAFF ATTENDANCE METHODS
    // ==========================================

    public function attendanceIndex(Request $request) {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $branch = $request->input('branch');
        $search = $request->input('search');

        $query = Staff::where('status', 'Active');

        if (!empty($branch)) {
            $query->where('branch', $branch);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $staffList = $query->get();

        $totalStaffCount = $staffList->count();

        $staffIds = $staffList->pluck('id');
        $attendanceRecords = StaffAttendance::where('date', $date)
            ->whereIn('staff_id', $staffIds)
            ->get()
            ->keyBy('staff_id')
            ->all();

        foreach ($staffList as $staff) {
            $approvedLeave = StaffLeave::where('staff_id', $staff->id)
                ->where('status', 'Approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            if ($approvedLeave) {
                if (!isset($attendanceRecords[$staff->id])) {
                    $attendance = new StaffAttendance();
                    $attendance->staff_id = $staff->id;
                    $attendance->date = $date;
                    $attendance->status = 'Leave';
                    $attendance->check_in = null;
                    $attendance->check_out = null;
                    $attendanceRecords[$staff->id] = $attendance;
                } else {
                    $attendanceRecords[$staff->id]->status = 'Leave';
                    $attendanceRecords[$staff->id]->check_in = null;
                    $attendanceRecords[$staff->id]->check_out = null;
                }
            }
        }

        $presentCount = StaffAttendance::where('date', $date)->whereIn('staff_id', $staffIds)->where('status', 'Present')->count();
        $absentCount = StaffAttendance::where('date', $date)->whereIn('staff_id', $staffIds)->where('status', 'Absent')->count();
        $halfDayCount = StaffAttendance::where('date', $date)->whereIn('staff_id', $staffIds)->where('status', 'Half Day')->count();
        $leaveCount = StaffAttendance::where('date', $date)->whereIn('staff_id', $staffIds)->where('status', 'Leave')->count();

        return view('admin.staff.attendance', compact(
            'staffList', 'attendanceRecords', 'date', 'branch', 'search',
            'totalStaffCount', 'presentCount', 'absentCount', 'halfDayCount', 'leaveCount'
        ));
    }

    public function attendanceStore(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $date = $request->date;

        foreach ($request->attendance as $staffId => $data) {
            $status = $data['status'] ?? 'Present';

            if ($status === 'Working') {
                $status = 'Present'; 
            }

            $checkIn = ($status === 'Leave') ? null : ($data['check_in'] ?? null);
            $checkOut = ($status === 'Leave') ? null : ($data['check_out'] ?? null);

            StaffAttendance::updateOrCreate(
                ['staff_id' => $staffId, 'date' => $date],
                [
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ]
            );
        }

        return redirect()->route('staff.attendance.index', ['date' => $date])
                       ->with('success', 'Staff attendance marked successfully!');
    }

    // ==========================================
    // STAFF LEAVE MANAGEMENT METHODS
    // ==========================================

    public function leaveIndex(Request $request) {
        $query = StaffLeave::with('staff');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch')) {
            $branch = $request->branch;
            $query->whereHas('staff', function($q) use ($branch) {
                $q->where('branch', $branch);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staff', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $leaves = $query->latest()->paginate(10);
        
        $staffList = Staff::all();

        return view('admin.staff.leave', compact('leaves', 'staffList'));
    }

    public function leaveStore(Request $request) {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        StaffLeave::create([
            'staff_id' => $request->staff_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return redirect()->route('staff.leave.index')->with('success', 'Leave request submitted successfully!');
    }

    public function leaveUpdateStatus(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'rejection_reason' => 'nullable|required_if:status,Rejected|string|max:500',
        ]);

        $leave = StaffLeave::findOrFail($id);
        
        $leave->status = $request->status;

        if ($request->status == 'Rejected') {
            $leave->rejection_reason = $request->rejection_reason;
        } else {
            $leave->rejection_reason = null;
        }

        $leave->save();

        return redirect()->back()->with('success', 'Leave request status updated to ' . $request->status);
    }

    public function leaveDestroy($id) {
        $leave = StaffLeave::findOrFail($id);
        $leave->delete();

        return redirect()->back()->with('success', 'Leave request deleted successfully!');
    }
}