<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        // Sarv notifications fetch karne (pagination sobat)
        $notifications = Notification::latest()->paginate(10);

        return view('auth.notification', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read_at' => now()]);

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->back();
    }

    public function redirectNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read_at' => now()]);

        // Type nusar yogya route var redirect karne
        switch ($notification->type) {
            case 'expiry': 
            case 'membership_expiring':
            case 'membership_expired':
                return redirect()->route('members.show', $notification->reference_id ?? 1);

            case 'pending_payment':
                return redirect()->route('payments.pending');

            case 'new_enquiry':
                return redirect()->route('enquiries.index');

            case 'follow_up':
                return redirect()->route('followups.index');

            case 'birthday':
                return redirect()->route('members.show', $notification->reference_id ?? 1);

            case 'attendance_anomaly':
                return redirect()->route('attendance.index');

            case 'equipment_maintenance':
            case 'low_stock':
                return redirect()->route('equipments.index');

            default:
                return redirect()->route('dashboard');
        }
    }
}