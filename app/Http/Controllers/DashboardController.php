<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Notification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. AUTOMATIC EXPIRY NOTIFICATION GENERATOR (Next 3 Days)
        // ==========================================
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays(2);

        // FIXED: Using whereDate to safely compare only dates, ignoring time parts
        $expiringSoonMembers = Member::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate('expiry_date', '<=', $endDate)
            ->get();

        foreach ($expiringSoonMembers as $mem) {
            $exists = Notification::where('type', 'expiry')
                ->where('data', 'like', '%' . $mem->id . '%')
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                $daysLeft = Carbon::parse($mem->expiry_date)->startOfDay()->diffInDays(Carbon::today());
                
                $message = "";
                if ($daysLeft == 0) {
                    $message = 'Member ' . ($mem->name ?? 'User') . ' membership expires today!';
                } elseif ($daysLeft == 1) {
                    $message = 'Member ' . ($mem->name ?? 'User') . ' membership expires tomorrow!';
                } else {
                    $message = 'Member ' . ($mem->name ?? 'User') . ' membership expires in ' . $daysLeft . ' days!';
                }

                Notification::firstOrCreate([
                    'type' => 'expiry',
                    'data' => json_encode(['member_id' => $mem->id]),
                    'created_at' => today()
                ], [
                    'title' => 'Membership Expiring Soon',
                    'message' => $message,
                    'target_url' => route('members.show', $mem->id),
                    'read_at' => null,
                ]);
            }
        }

        // ==========================================
        // 2. AUTO UPDATE MEMBER STATUS (Date-wise Only)
        // ==========================================
        $allMembers = Member::all();

        foreach ($allMembers as $member) {
            if ($member->expiry_date) {
                $expiryDate = Carbon::parse($member->expiry_date)->startOfDay();
                $todayDate = Carbon::today()->startOfDay();

                if ($todayDate->greaterThan($expiryDate)) {
                    if ($member->status !== 'Expired' && $member->status !== 'expired') {
                        $member->update(['status' => 'Expired']);
                    }
                } else {
                    if (!in_array(strtolower($member->status), ['active'])) {
                        $member->update(['status' => 'Active']);
                    }
                }
            } else {
                // Jar expiry date nsel, tari default Active set karu
                if (empty($member->status) || strtolower($member->status) !== 'active') {
                    $member->update(['status' => 'Active']);
                }
            }
        }

        // ==========================================
        // 3. DASHBOARD METRICS & DATA COLLECTION
        // ==========================================
        $totalMembers = Member::count();
        
        // FIXED: Case-insensitive Active Members check
        $activeMembers = Member::where(function($query) {
            $query->whereIn('status', ['Active', 'active', 'ACTIVE'])
                  ->orWhereNull('status');
        })->count();

        $expiredMembers = Member::whereIn('status', ['Expired', 'expired', 'EXPIRED'])->count();
        $todayNewMembers = Member::whereDate('created_at', today())->count();
        $recentMembers = Member::latest()->take(5)->get();

        $todayAttendance = $totalMembers > 0
            ? round(($activeMembers / $totalMembers) * 100)
            : 0;

        $expiringSoon = Member::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', Carbon::today())
            ->whereDate('expiry_date', '<=', Carbon::today()->addDays(7))
            ->count();

        $monthlyCollection = Member::count() * 1000;
        $pendingAmount = Member::whereIn('status', ['Expired', 'expired'])->count() * 500;
        $totalTrainers = \App\Models\Trainer::count();
        $todayRenewals = Member::whereDate('updated_at', today())->count();
        $followUpsDue = Member::whereIn('status', ['Expired', 'expired'])->count();

        $recentPayments = collect();
        $paymentDues = collect();

        // Expiring members for the dashboard table (Next 3 days)
        $expiringMembers = Member::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [Carbon::today(), Carbon::today()->addDays(3)])
            ->latest()
            ->get();

        // Unread Notification Count & Header Notifications
        $unreadCount = Notification::whereNull('read_at')->count();
        $headerNotifications = Notification::where('type', 'expiry')->latest()->take(5)->get();
        
        // Added: Unread notifications list for the popup toast display
        $unreadNotifications = Notification::whereNull('read_at')->latest()->get();

        return view(
            'auth.dashboard',
            compact(
                'totalMembers',
                'activeMembers',
                'expiredMembers',
                'todayAttendance',
                'todayNewMembers',
                'expiringSoon',
                'monthlyCollection',
                'pendingAmount',
                'totalTrainers',
                'todayRenewals',
                'followUpsDue',
                'recentMembers',
                'recentPayments',
                'paymentDues',
                'expiringMembers',
                'unreadCount',
                'headerNotifications',
                'unreadNotifications'
            )
        );
    }
}
