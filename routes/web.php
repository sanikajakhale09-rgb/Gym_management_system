<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MembershipPlanController;

use App\Http\Controllers\MemberSubscriptionController; 
use App\Http\Controllers\MembershipFreezeController; 
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\BranchController; 

use App\Http\Controllers\ExerciseController; 
use App\Http\Controllers\WorkoutPlanController; 
use App\Http\Controllers\DietPlanController; 
use App\Http\Controllers\FitnessTrackingController; 
use App\Http\Controllers\FitnessGoalController; 
use App\Http\Controllers\BodyMeasurementController;
use App\Http\Controllers\GymProfileController; 
use App\Http\Controllers\EnquiryController; 
use App\Http\Controllers\FollowupController; 

// ==========================================
// Payment, Receipt & PDF Controllers
// ==========================================
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController; 
use App\Http\Controllers\ExpenseController; 
use App\Http\Controllers\EquipmentController; 
use Barryvdh\DomPDF\Facade\Pdf;

// ==========================================
// PROGRESS TRACKING & ATTENDANCE CONTROLLERS
// ==========================================
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MemberPortalController; 
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RolePermissionController; // Added here

use App\Models\Member;
use App\Models\Notification;
use Carbon\Carbon;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/login/2fa', [LoginController::class, 'show2faForm'])->name('login.2fa');
Route::post('/login/2fa', [LoginController::class, 'verify2fa'])->name('login.2fa.verify');

Route::get('/member/self-checkin', [AttendanceController::class, 'showSelfCheckinForm'])->name('member.self-checkin');
Route::post('/member/self-checkin/store', [AttendanceController::class, 'storeSelfCheckin'])->name('member.self-checkin.store');

Route::post('/webhook/sms-enquiry', [EnquiryController::class, 'storeFromSms']);

Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
    Route::get('/portal', [MemberPortalController::class, 'index'])->name('portal.index');
    Route::get('/profile', [MemberPortalController::class, 'profile'])->name('portal.profile');
    Route::get('/workouts', [MemberPortalController::class, 'workouts'])->name('portal.workouts');
    Route::get('/diets', [MemberPortalController::class, 'diets'])->name('portal.diets');
    Route::get('/attendance', [MemberPortalController::class, 'attendance'])->name('portal.attendance');
    Route::get('/payments', [MemberPortalController::class, 'payments'])->name('portal.payments');
});


Route::middleware(['auth'])->group(function () {
    
    // ==========================================
    // ROLE-BASED ACCESS CONTROL ROUTES (ADDED HERE)
    // ==========================================
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/super-admin/dashboard', [DashboardController::class, 'index']); // (Or your SuperAdminController)
    });

    Route::middleware(['role:Super Admin|Gym Owner|Manager'])->group(function () {
        Route::get('/members-secure-check', [MemberController::class, 'index']);
    });

    // ==========================================
    // ROLE & PERMISSIONS SYSTEM ROUTES (UPDATED WITH VIEW/EDIT/DELETE)
    // ==========================================
    Route::prefix('dashboard/system')->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
        Route::post('/roles-permissions/update', [RolePermissionController::class, 'update'])->name('roles-permissions.update');
        
        // Added View, Edit & Delete Routes for Roles & Permissions Modal Actions
        Route::get('/roles-permissions/{role}/view', [RolePermissionController::class, 'show'])->name('roles-permissions.show');
        Route::get('/roles-permissions/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles-permissions.edit');
        Route::delete('/roles-permissions/{role}/delete', [RolePermissionController::class, 'destroy'])->name('roles-permissions.destroy');
        
        // Added AJAX Route to fetch permissions for modal view
        Route::get('/roles-permissions/get-permissions/{role}', [RolePermissionController::class, 'getPermissions'])->name('roles-permissions.get-permissions');
    });

    // ==========================================
    // NOTIFICATION ROUTES ADDED HERE
    // ==========================================
    Route::get('/dashboard/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/dashboard/notifications/{id}/redirect', [NotificationController::class, 'redirectNotification'])->name('notifications.redirect');
    Route::post('/dashboard/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/dashboard/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    
    // Add & Send Notification Routes Added Here
    Route::get('/dashboard/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/dashboard/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');

    Route::get('/dashboard', function () {
        
        // ==========================================
        // AUTOMATIC EXPIRY NOTIFICATION GENERATOR (Next 3 Days)
        // ==========================================
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(3);
        
        // Updated with whereDate to correctly match database expiry dates
        $expiringSoonMembers = Member::whereNull('deleted_at')
            ->whereDate('expiry_date', '>=', $startDate)
            ->whereDate('expiry_date', '<=', $endDate)
            ->get();

        foreach ($expiringSoonMembers as $mem) {
            $exists = Notification::where('message', 'like', '%'.$mem->name.'%')
                ->whereDate('created_at', today())
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => auth()->id() ?? 1,
                    'title' => 'Membership Expiring Soon',
                    'message' => 'Member ' . ($mem->name ?? 'User') . ' membership is expiring soon!', 
                    'type' => 'expiry',
                    'target_url' => route('membership.expiring'),
                    'read_at' => null,
                ]);
            }
        }

        $activeMembersList = Member::whereNull('deleted_at')
            ->whereIn('status', ['Active', 'active'])
            ->get();

        foreach ($activeMembersList as $member) {

            $joiningDate = Carbon::parse($member->joining_date);
            $expiryDate = $joiningDate;

            if (stripos($member->membership_plan, 'Month') !== false) {

                $months = (int) filter_var($member->membership_plan,
                    FILTER_SANITIZE_NUMBER_INT
                );

                $expiryDate = (clone $joiningDate)->addMonths(
                    $months > 0 ? $months : 1
                );

            } elseif (stripos($member->membership_plan, 'Year') !== false) {

                $years = (int) filter_var($member->membership_plan,
                    FILTER_SANITIZE_NUMBER_INT
                );

                $expiryDate = (clone $joiningDate)->addYears(
                    $years > 0 ? $years : 1
                );

            } elseif (stripos($member->membership_plan, 'Day') !== false) {

                $days = (int) filter_var($member->membership_plan,
                    FILTER_SANITIZE_NUMBER_INT
                );

                $expiryDate = (clone $joiningDate)->addDays(
                    $days > 0 ? $days : 30
                );

            } else {

                $expiryDate = (clone $joiningDate)->addMonth();
            }

            if (Carbon::today()->greaterThan($expiryDate)) {
                $member->update([
                    'status' => 'Expired'
                ]);
            }
        }

        // Active & Total live members excluding deleted ones
        $totalMembers = Member::whereNull('deleted_at')->count();

        // FIXED: Jar status 'Active' nasel tari count milnyasathi ha badal kela ahe
        $activeMembers = Member::whereNull('deleted_at')
            ->where(function($query) {
                $query->whereIn('status', ['Active', 'active', 'ACTIVE'])
                      ->orWhereNull('status');
            })
            ->count();

        $expiredMembers = Member::whereNull('deleted_at')
            ->whereIn('status', ['Expired', 'expired'])
            ->count();

        $todayNewMembers = Member::whereNull('deleted_at')
            ->whereDate('created_at', today())
            ->count();

        $recentMembers = Member::whereNull('deleted_at')
            ->latest()
            ->take(5)
            ->get();

        $todayAttendance = $totalMembers > 0
            ? round(($activeMembers / $totalMembers) * 100)
            : 0;

        // FIXED: Expiring Soon Count for Next 7 Days
        $expiringStartDate = now();
        $expiringEndDate = now()->addDays(7);
        $expiringSoon = Member::whereNull('deleted_at')
            ->whereDate('expiry_date', '>=', $expiringStartDate)
            ->whereDate('expiry_date', '<=', $expiringEndDate)
            ->count();

        // Dynamic Monthly Collection from Payment Model
        $monthlyCollection = \App\Models\Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Dynamic Pending Amount from Payment Model
        $pendingAmount = \App\Models\Payment::where('status', 'Pending')
            ->sum('amount');

        // Dynamic Total Trainers count from Trainer Model
        $totalTrainers = \App\Models\Trainer::count();

        $todayRenewals = Member::whereNull('deleted_at')
            ->whereDate('updated_at', today())
            ->count();

        $followUpsDue = Member::whereNull('deleted_at')
            ->whereIn('status', ['Expired', 'expired'])
            ->count();

        $recentPayments = collect();

        $paymentDues = collect();

        // Expiring Soon List Filter
        $expiringMembers = Member::whereNull('deleted_at')
            ->whereIn('status', ['Active', 'active'])
            ->latest()
            ->take(5)
            ->get();

        // Unread Notification Count added here:
        $unreadCount = Notification::whereNull('read_at')->count();

        // Header Notifications fetch
        $headerNotifications = Notification::latest()->take(5)->get();

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
                'headerNotifications'
            )
        );

    })->name('dashboard');

    Route::get('/admin/panel', function () {
        return redirect()->route('dashboard');
    })->name('admin.panel');

    Route::get('/dashboard/gym-profile', [GymProfileController::class, 'index'])->name('gym.profile.index');
    Route::post('/dashboard/gym-profile', [GymProfileController::class, 'update'])->name('gym.profile.update');
    Route::put('/dashboard/gym-profile', [GymProfileController::class, 'update'])->name('gym.profile.update.put');
    Route::delete('/dashboard/gym-profile/destroy', [GymProfileController::class, 'destroy'])->name('gym.profile.destroy');

    Route::get('/dashboard/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/dashboard/enquiries/create', [EnquiryController::class, 'create'])->name('enquiries.create');
    Route::post('/dashboard/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
    Route::put('/dashboard/enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
    Route::delete('/dashboard/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    
    Route::post('/dashboard/enquiries/{id}/whatsapp-reply', [EnquiryController::class, 'whatsappReply'])->name('enquiries.whatsapp-reply');

    Route::get('/enquiries/{id}/whatsapp-reply', [EnquiryController::class, 'replyWhatsApp'])->name('enquiries.whatsapp');
    Route::post('/dashboard/enquiries/{id}/whatsapp-update', [EnquiryController::class, 'sendWhatsAppAndUpdateStatus'])->name('enquiries.whatsapp.update');

    Route::post('/dashboard/enquiries/{id}/send-sms', [EnquiryController::class, 'sendSms'])->name('enquiries.sendSms');
    Route::put('/enquiries/{id}/update-status', [EnquiryController::class, 'updateStatus'])->name('enquiries.updateStatus');

    Route::get('/dashboard/enquiries/follow-ups', [FollowupController::class, 'index'])->name('enquiries.follow-ups'); 
    Route::get('/dashboard/follow-ups', [FollowupController::class, 'index'])->name('followups.index');
    Route::put('/dashboard/follow-ups/{id}', [FollowupController::class, 'update'])->name('followups.update');
    Route::patch('/dashboard/follow-ups/{id}/complete', [FollowupController::class, 'complete'])->name('followups.complete');
    Route::post('/dashboard/follow-ups/{id}/send-sms', [FollowupController::class, 'sendSms'])->name('followups.send-sms');

    Route::get(
        '/dashboard/branches', 
        [BranchController::class, 'index']
    )->name('dashboard.branches.index')->name('branch.index'); // Added name('branch.index') to fix RouteNotFoundException

    Route::get(
        '/dashboard/branches/create', 
        [BranchController::class, 'create']
    )->name('dashboard.branches.create');

    Route::post(
        '/dashboard/branches', 
        [BranchController::class, 'store']
    )->name('dashboard.branches.store');

    Route::get(
        '/dashboard/branches/{id}', 
        [BranchController::class, 'show']
    )->name('dashboard.branches.show')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/branches/{id}/edit', 
        [BranchController::class, 'edit']
    )->name('dashboard.branches.edit')->where('id', '[0-9]+');

    Route::put(
        '/dashboard/branches/{id}', 
        [BranchController::class, 'update']
    )->name('dashboard.branches.update')->where('id', '[0-9]+');

    Route::delete(
        '/dashboard/branches/{id}', 
        [BranchController::class, 'destroy']
    )->name('dashboard.branches.destroy')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/two-factor',
        [TwoFactorController::class, 'index']
    )->name('dashboard.two-factor');

    Route::post(
        '/dashboard/two-factor/enable',
        [TwoFactorController::class, 'enable']
    )->name('dashboard.two-factor.enable');

    Route::post(
        '/dashboard/two-factor/disable',
        [TwoFactorController::class, 'disable']
    )->name('dashboard.two-factor.disable');

    Route::get(
        '/dashboard/memberships/plans',
        [MembershipPlanController::class, 'index']
    )->name('membership.plans.index');

    Route::get(
        '/dashboard/memberships/plans/create',
        [MembershipPlanController::class, 'create']
    )->name('membership.plans.create');

    Route::post(
        '/dashboard/memberships/plans',
        [MembershipPlanController::class, 'store']
    )->name('membership.plans.store');

    Route::get(
        '/dashboard/memberships/purchase',
        [MemberSubscriptionController::class, 'create']
    )->name('membership.purchase.create');

    Route::post(
        '/dashboard/memberships/purchase',
        [MemberSubscriptionController::class, 'store']
    )->name('membership.purchase.store');

    Route::post(
        '/dashboard/memberships/store-purchase',
        [MemberSubscriptionController::class, 'store']
    )->name('members.store.subscription');

    Route::post(
        '/dashboard/memberships/store-purchase-alt',
        [MemberSubscriptionController::class, 'store']
    )->name('membership.storePurchase');

    Route::get(
        '/dashboard/memberships/active',
        [MemberSubscriptionController::class, 'activeMemberships']
    )->name('membership.active');

    Route::get(
        '/dashboard/memberships/expiring',
        [MemberSubscriptionController::class, 'expiringExpiredList']
    )->name('membership.expiring');

    Route::put(
        '/dashboard/memberships/{id}',
        [MemberSubscriptionController::class, 'update']
    )->name('memberships.update')->where('id', '[0-9]+');

    Route::delete(
        '/dashboard/memberships/{id}',
        [MemberSubscriptionController::class, 'destroy']
    )->name('memberships.destroy')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/{id}/renew',
        [MemberSubscriptionController::class, 'renewMembership']
    )->name('membership.renew')->where('id', '[0-9]+'); 
    
    Route::post(
        '/dashboard/memberships/{id}/renew',
        [MemberSubscriptionController::class, 'storeRenewMembership']
    )->name('membership.renew.store')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/store-renew',
        [MemberSubscriptionController::class, 'storeRenewMembership']
    )->name('membership.storeRenew')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/renew-alias',
        [MemberSubscriptionController::class, 'storeRenewMembership']
    )->name('members.storeRenewal')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/{id}/freeze',
        [MemberSubscriptionController::class, 'showFreezeForm']
    )->name('membership.freeze.form')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/freeze',
        [MemberSubscriptionController::class, 'freezeMembership']
    )->name('membership.freeze')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/unfreeze',
        [MemberSubscriptionController::class, 'unfreezeMembership']
    )->name('membership.unfreeze')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/freeze',
        [MembershipFreezeController::class, 'index']
    )->name('membership.freeze.list');

    Route::get(
        '/dashboard/memberships/{id}/freeze-form',
        [MembershipFreezeController::class, 'create']
    )->name('membership.freeze.create')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/freeze-store',
        [MembershipFreezeController::class, 'store']
    )->name('membership.freeze.store')->where('id', '[0-9]+');

    Route::put(
        '/dashboard/memberships/freeze/{id}',
        [MembershipFreezeController::class, 'update']
    )->name('membership.freeze.update')->where('id', '[0-9]+');

    Route::delete(
        '/dashboard/memberships/freeze/{id}',
        [MembershipFreezeController::class, 'destroy']
    )->name('membership.freeze.destroy')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/memberships/{id}/unfreeze-record',
        [MembershipFreezeController::class, 'unfreeze']
    )->name('membership.freeze.unfreeze')->where('id', '[0-9]+');

    Route::get('/dashboard/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/dashboard/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/dashboard/payments/pending', [PaymentController::class, 'pendingPayments'])->name('payments.pending');
    Route::post('/dashboard/payments/collect/{id}', [PaymentController::class, 'collect'])->name('payments.collect');
    Route::get('/dashboard/payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit')->where('id', '[0-9]+');
    Route::put('/dashboard/payments/{id}', [PaymentController::class, 'update'])->name('payments.update')->where('id', '[0-9]+');
    Route::delete('/dashboard/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy')->where('id', '[0-9]+');

    Route::get('/dashboard/invoices', [PaymentController::class, 'invoicesList'])->name('invoices.index');
    Route::get('/dashboard/invoices/{id}', [PaymentController::class, 'showInvoice'])->name('invoices.show')->where('id', '[0-9]+');
    Route::put('/dashboard/invoices/{id}', [PaymentController::class, 'update'])->name('invoices.update')->where('id', '[0-9]+');
    Route::delete('/dashboard/invoices/{id}', [PaymentController::class, 'destroy'])->name('invoices.destroy')->where('id', '[0-9]+');

    // Receipt Routes
    Route::get('/dashboard/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/dashboard/receipts/download/{id}', [ReceiptController::class, 'downloadPDF'])->name('receipts.download')->where('id', '[0-9]+');
    Route::delete('/dashboard/receipts/{id}', [ReceiptController::class, 'destroy'])->name('receipts.destroy')->where('id', '[0-9]+');

    Route::get('/dashboard/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/dashboard/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/dashboard/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/dashboard/expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories.index');
    Route::post('/dashboard/expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
    Route::put('/dashboard/expenses/categories/{id}', [ExpenseController::class, 'updateCategory'])->name('expenses.categories.update')->where('id', '[0-9]+');
    Route::delete('/dashboard/expenses/categories/{id}', [ExpenseController::class, 'destroyCategory'])->name('expenses.categories.destroy')->where('id', '[0-9]+');
    Route::get('/dashboard/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit')->where('id', '[0-9]+');
    Route::put('/dashboard/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update')->where('id', '[0-9]+');
    Route::delete('/dashboard/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/renewals',
        [MemberSubscriptionController::class, 'renewalsList']
    )->name('admin.membership-purchase.renewals');

    Route::get(
        '/dashboard/memberships/renewals/{id}/view',
        [MemberSubscriptionController::class, 'viewRenewal']
    )->name('admin.membership-purchase.view')->where('id', '[0-9]+');

    Route::put(
        '/dashboard/memberships/renewals/{id}/update',
        [MemberSubscriptionController::class, 'updateRenewal']
    )->name('admin.membership-purchase.update')->where('id', '[0-9]+');

    Route::delete(
        '/dashboard/memberships/renewals/{id}/delete',
        [MemberSubscriptionController::class, 'destroyRenewal']
    )->name('admin.membership-purchase.delete')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/trainers',
        [TrainerController::class, 'index']
    )->name('trainers.index');

    Route::get(
        '/dashboard/trainers/create',
        [TrainerController::class, 'create']
    )->name('trainers.create');

    Route::get(
        '/dashboard/trainers/assign-members',
        [TrainerController::class, 'assignMembers']
    )->name('trainers.assign');

    Route::post(
        '/dashboard/trainers/assign-members',
        [TrainerController::class, 'storeAssignedMember']
    )->name('trainers.assign.store');

    Route::delete(
        '/dashboard/trainers/unassign/{id}',
        [TrainerController::class, 'unassignMember']
    )->name('trainers.unassign');

    Route::get(
        '/dashboard/get-trainers/{branchId}',
        [TrainerController::class, 'getTrainersByBranch']
    );

    Route::get(
        '/dashboard/get-members/{branchId}',
        [TrainerController::class, 'getMembersByBranch']
    );

    Route::post(
        '/dashboard/trainers',
        [TrainerController::class, 'store']
    )->name('trainers.store');

    Route::get(
        '/dashboard/trainers/{id}',
        [TrainerController::class, 'show']
    )->name('trainers.show')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/trainers/{id}/edit',
        [TrainerController::class, 'edit']
    )->name('trainers.edit')->where('id', '[0-9]+');

    Route::put(
        '/dashboard/trainers/{id}',
        [TrainerController::class, 'update']
    )->name('trainers.update')->where('id', '[0-9]+');

    Route::delete(
        '/dashboard/trainers/{id}',
        [TrainerController::class, 'destroy']
    )->name('trainers.destroy')->where('id', '[0-9]+');

    Route::get(
        'dashboard/members/create',
        [MemberController::class, 'create']
    )->name('members.create');

    Route::get(
        'dashboard/members/search',
        [MemberController::class, 'search']
    )->name('members.search');

    Route::get(
        'dashboard/members/documents',
        [MemberController::class, 'documents']
    )->name('members.documents');

    Route::get(
        'dashboard/members/export-pdf',
        [MemberController::class, 'exportPDF']
    )->name('members.pdf');

    Route::get(
        'dashboard/members/export-excel',
        [MemberController::class, 'exportExcel']
    )->name('members.excel');

    Route::get(
        'dashboard/members/{id}/id-card',
        [MemberController::class, 'idCard']
    )->name('members.id-card')->where('id', '[0-9]+');

    Route::get(
        'dashboard/members/{id}/renew',
        [MemberController::class, 'renew']
    )->name('members.renew')->where('id', '[0-9]+');

    Route::post(
        '/dashboard/members/{id}/renew',
        [MemberController::class, 'storeRenewal']
    )->name('members.storeRenewal')->where('id', '[0-9]+');

    Route::get(
        'dashboard/members/{id}/receipt',
        [MemberController::class, 'receipt']
    )->name('members.receipt')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/{id}/receipt',
        [MemberSubscriptionController::class, 'receipt']
    )->name('membership.receipt')->where('id', '[0-9]+');

    Route::get(
        '/dashboard/memberships/plans/{id}/edit',
        [MembershipPlanController::class, 'edit']
    )->name('membership.plans.edit');

    Route::put(
        '/dashboard/memberships/plans/{id}',
        [MembershipPlanController::class, 'update']
    )->name('membership.plans.update');

    Route::delete(
        '/dashboard/memberships/plans/{id}',
        [MembershipPlanController::class, 'destroy']
    )->name('membership.plans.destroy');

    Route::get(
        '/dashboard/workouts/exercises',
        [TrainerController::class, 'exerciseIndex']
    )->name('exercises.index');

    Route::post(
        '/dashboard/workouts/exercises/store',
        [TrainerController::class, 'exerciseStore']
    )->name('exercises.store');

    Route::get(
        '/dashboard/workouts/exercises/{id}/edit',
        [TrainerController::class, 'exerciseEdit']
    )->name('exercises.edit');

    Route::put(
        '/dashboard/workouts/exercises/{id}',
        [TrainerController::class, 'exerciseUpdate']
    )->name('exercises.update');

    Route::delete(
        '/dashboard/workouts/exercises/{id}',
        [TrainerController::class, 'exerciseDestroy']
    )->name('exercises.destroy');

    Route::get(
        '/dashboard/workouts/plans',
        [WorkoutPlanController::class, 'index']
    )->name('workouts.index');

    Route::get(
        '/dashboard/workouts/plans/create',
        [WorkoutPlanController::class, 'create']
    )->name('workouts.create');

    Route::post(
        '/dashboard/workouts/plans',
        [WorkoutPlanController::class, 'store']
    )->name('workouts.store');

    Route::get(
        '/dashboard/workouts/member/{memberId}',
        [WorkoutPlanController::class, 'getWorkoutsByMember']
    )->name('workouts.byMember');

    Route::get(
        '/workouts/member-details/{id}',
        [WorkoutPlanController::class, 'getMemberDetails']
    )->name('workouts.member-details');

    Route::get(
        '/dashboard/workouts/member-details/{id}',
        [WorkoutPlanController::class, 'getMemberDetails']
    );

    Route::get(
        '/dashboard/workouts/plans/{id}',
        [WorkoutPlanController::class, 'show']
    )->name('workouts.show');

    Route::get(
        '/dashboard/workouts/plans/{id}/edit',
        [WorkoutPlanController::class, 'edit']
    )->name('workouts.edit');

    Route::put(
        '/dashboard/workouts/plans/{id}',
        [WorkoutPlanController::class, 'update']
    )->name('workouts.update');

    Route::delete(
        '/dashboard/workouts/plans/{id}',
        [WorkoutPlanController::class, 'destroy']
    )->name('workouts.destroy');

    Route::get(
        '/dashboard/diets/plans',
        [DietPlanController::class, 'create']
    )->name('diet-plans.index');

    Route::get(
        '/dashboard/diets/plans/create',
        [DietPlanController::class, 'create']
    )->name('diet-plans.create');

    Route::get(
        '/dashboard/diets/member/{id}',
        [DietPlanController::class, 'getMemberDietPlans']
    )->name('diet-plans.byMember');

    Route::post(
        '/dashboard/diets/plans',
        [DietPlanController::class, 'store']
    )->name('diet-plans.store');

    Route::get(
        '/dashboard/diets/plans/{id}',
        [DietPlanController::class, 'show']
    )->name('diet-plans.show');

    Route::get(
        '/dashboard/diets/plans/{id}/edit',
        [DietPlanController::class, 'edit']
    )->name('diet-plans.edit');

    Route::put(
        '/dashboard/diets/plans/{id}',
        [DietPlanController::class, 'update']
    )->name('diet-plans.update');

    Route::delete(
        '/dashboard/diets/plans/{id}',
        [DietPlanController::class, 'destroy']
    )->name('diet-plans.destroy');

    Route::get(
        '/dashboard/fitness/goals',
        [FitnessGoalController::class, 'index']
    )->name('fitness-goals.index');

    Route::get(
        '/dashboard/fitness/member-details/{id}',
        [FitnessGoalController::class, 'getMemberDetails']
    );

    Route::post(
        '/dashboard/fitness/goals',
        [FitnessGoalController::class, 'store']
    )->name('fitness-goals.store');

    Route::get(
        '/dashboard/fitness/goals/{id}/edit',
        [FitnessGoalController::class, 'edit']
    )->name('fitness-goals.edit');

    Route::put(
        '/dashboard/fitness/goals/{id}',
        [FitnessGoalController::class, 'update']
    )->name('fitness-goals.update');

    Route::delete(
        '/dashboard/fitness/goals/{id}',
        [FitnessGoalController::class, 'destroy']
    )->name('fitness-goals.destroy');

    Route::get(
        '/dashboard/fitness/measurements',
        [BodyMeasurementController::class, 'index']
    )->name('fitness.measurements.index');

    Route::post(
        '/dashboard/fitness/measurements',
        [BodyMeasurementController::class, 'store']
    )->name('fitness.measurements.store');

    Route::get(
        '/dashboard/body-measurements',
        [BodyMeasurementController::class, 'index']
    )->name('body-measurements.index');

    Route::post(
        '/dashboard/body-measurements',
        [BodyMeasurementController::class, 'store']
    )->name('body-measurements.store');

    Route::get(
        '/dashboard/body-measurements/member/{id}',
        [BodyMeasurementController::class, 'getMemberHistory']
    )->name('body-measurements.history');

    Route::get(
        '/dashboard/fitness/measurements/member/{id}',
        [BodyMeasurementController::class, 'getMemberHistory']
    );

    Route::get(
        '/dashboard/body-measurements/fetch-by-date/{memberId}',
        [BodyMeasurementController::class, 'getMeasurementByDate']
    );

    Route::get(
        '/dashboard/body-measurements/edit/{id}',
        [BodyMeasurementController::class, 'edit']
    )->name('body-measurements.edit');

    Route::put(
        '/dashboard/body-measurements/update/{id}',
        [BodyMeasurementController::class, 'update']
    )->name('body-measurements.update');

    Route::delete(
        '/dashboard/body-measurements/{id}',
        [BodyMeasurementController::class, 'destroy']
    )->name('fitness.measurements.destroy');

    Route::get(
        '/dashboard/fitness/progress',
        [ProgressController::class, 'index']
    )->name('progress.index');

    Route::get(
        '/dashboard/attendance',
        [AttendanceController::class, 'index']
    )->name('attendance.index');

    Route::get(
        '/dashboard/attendance/mark',
        [AttendanceController::class, 'markAttendance']
    )->name('attendance.mark');

    Route::post(
        '/dashboard/attendance/store',
        [AttendanceController::class, 'storeAttendance']
    )->name('attendance.store');

    Route::get(
        '/dashboard/attendance/today',
        [AttendanceController::class, 'todayAttendance']
    )->name('attendance.today');

    Route::patch(
        '/dashboard/attendance/checkout/{id}',
        [AttendanceController::class, 'checkout']
    )->name('attendance.checkout');

    Route::put(
        '/dashboard/attendance/update/{id}',
        [AttendanceController::class, 'AttendanceUpdate']
    )->name('attendance.update');

    Route::delete(
        '/dashboard/attendance/destroy/{id}',
        [AttendanceController::class, 'destroy']
    )->name('attendance.destroy');

    Route::get(
        '/dashboard/attendance/qr-scan',
        [AttendanceController::class, 'qrCheckin']
    )->name('attendance.qr-scan');

    Route::post(
        '/dashboard/attendance/qr-scan',
        [AttendanceController::class, 'storeQrCheckin']
    )->name('attendance.qr-scan.store');

    Route::get('/dashboard/attendance/reports', [AttendanceController::class, 'attendanceReports'])->name('attendance.reports');

    Route::get(
        '/dashboard/memberships/expiring-expired',
        [MemberSubscriptionController::class, 'expiringExpiredList']
    )->name('membership.expiring-expired');

    Route::get('/dashboard/equipment', [EquipmentController::class, 'index'])->name('equipments.index');
    Route::get('/dashboard/equipment/create', [EquipmentController::class, 'create'])->name('equipments.create');
    Route::post('/dashboard/equipment', [EquipmentController::class, 'store'])->name('equipments.store');
    Route::put('/dashboard/equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipments.update');
    Route::delete('/dashboard/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipments.destroy');

   // ==========================================
    // ADMIN-ONLY ROUTES (RECEPTIONIST BLOCKED HERE)
    // ==========================================
    Route::group(['middleware' => ['role:Admin|Super Admin']], function () {
        
        // ==========================================
        // STAFF MANAGEMENT & ATTENDANCE ROUTES
        // ==========================================
        Route::get('/dashboard/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/dashboard/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/dashboard/staff', [StaffController::class, 'store'])->name('staff.store');
        
        // Staff Attendance Routes Added Here
        Route::get('/dashboard/staff/attendance', [StaffController::class, 'attendanceIndex'])->name('staff.attendance.index');
        Route::post('/dashboard/staff/attendance/store', [StaffController::class, 'attendanceStore'])->name('staff.attendance.store');

        // ==========================================
        // STAFF LEAVE MANAGEMENT ROUTES
        // ==========================================
        Route::get('/dashboard/staff/leaves', [StaffController::class, 'leaveIndex'])->name('staff.leave.index');
        Route::post('/dashboard/staff/leaves', [StaffController::class, 'leaveStore'])->name('staff.leave.store');
        Route::patch('/dashboard/staff/leaves/{id}/update-status', [StaffController::class, 'leaveUpdateStatus'])->name('staff.leave.updateStatus');
        Route::delete('/dashboard/staff/leaves/{id}', [StaffController::class, 'leaveDestroy'])->name('staff.leave.destroy');

        Route::get('/dashboard/staff/{id}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/dashboard/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/dashboard/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('/dashboard/staff/{id}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');

        // Added New Routes for Modal & Resource Operations
        Route::resource('staff', StaffController::class);
        Route::patch('staff/{id}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');

        // AJAX routes sathi (Modal data fetch & update sathi)
        Route::get('staff/{id}/json', [StaffController::class, 'showJson'])->name('staff.json');

    });
    // Ha resource route sarvat shevati thevla ahe
    Route::resource(
        'dashboard/members',
        MemberController::class
    );

});