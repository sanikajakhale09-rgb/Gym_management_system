<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Gym Dashboard')</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>

        body {
            background-color: #f7f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* =========================
            SIDEBAR
        ========================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;

            height: 100vh;

            width: 260px;

            background-color: #ffffff;

            color: #4b5563;

            z-index: 1050;

            border-right: 1px solid #e5e7eb;

            box-shadow: 2px 0 8px rgba(0,0,0,0.02);

            overflow-y: auto;

            transition: all 0.3s ease;
        }


        /* Brand */

        .sidebar .brand-link {

            padding: 1.2rem 1rem;

            font-size: 1.15rem;

            font-weight: 700;

            color: #1f2937;

            background-color: #ffffff;

            text-decoration: none;

            display: block;

            text-align: center;

            border-bottom: 1px solid #f3f4f6;

            letter-spacing: 0.5px;
        }


        .sidebar .brand-link i {

            color: #6366f1;

            margin-right: 6px;
        }


        /* =========================
            ADMIN PROFILE
        ========================== */

        .admin-profile {

            padding: 16px 14px;

            margin: 10px;

            background-color: #f8fafc;

            border: 1px solid #eef0f3;

            border-radius: 12px;
        }


        .admin-avatar {

            width: 42px;

            height: 42px;

            border-radius: 50%;

            background-color: #eef2ff;

            color: #4f46e5;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;
        }


        .admin-name {

            font-size: 14px;

            font-weight: 700;

            color: #1f2937;
        }


        .admin-role {

            font-size: 11px;

            color: #9ca3af;
        }


        /* =========================
            SECTION HEADING
        ========================== */

        .sidebar-heading {

            font-size: 0.7rem;

            text-transform: uppercase;

            letter-spacing: 0.8px;

            font-weight: 700;

            color: #9ca3af;

            padding: 0.75rem 1rem 0.25rem 1rem;

            margin-top: 0.5rem;
        }


        /* =========================
            NAVIGATION
        ========================== */

        .sidebar .nav-link {

            color: #4b5563;

            padding: 0.6rem 1rem;

            margin: 0.15rem 0.8rem;

            border-radius: 0.5rem;

            font-weight: 500;

            font-size: 0.9rem;

            transition: all 0.2s ease;

            display: flex;

            align-items: center;

            text-decoration: none;
        }


        .sidebar .nav-link:hover {

            background-color: #f3f4f6;

            color: #1f2937;
        }


        .sidebar .nav-link.active {

            background-color: #e0e7ff;

            color: #4f46e5;

            font-weight: 600;
        }


        .sidebar .nav-link i {

            margin-right: 10px;

            color: #9ca3af;

            width: 18px;

            text-align: center;

            transition: color 0.2s ease;
        }


        .sidebar .nav-link:hover i,

        .sidebar .nav-link.active i {

            color: #4f46e5;
        }


        /* =========================
            SUB MENU & CHEVRON
        ========================== */

        .sidebar .nav-link .submenu-chevron {
            transition: transform 0.3s ease;
            color: #9ca3af;
            margin-right: 0;
            width: auto;
        }

        .sidebar .nav-link:not(.collapsed) .submenu-chevron {
            transform: rotate(180deg);
            color: #4f46e5;
        }

        .submenu-container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .sidebar .nav-link.active-sub {
            color: #4f46e5 !important;
            font-weight: 600;
            background-color: #f3f4f6 !important;
        }

        .sidebar .nav-link.active-sub:hover {
            color: #4338ca !important;
            background-color: #e5e7eb !important;
        }


        /* =========================
            MAIN CONTENT
        ========================== */

        .main-content {

            margin-left: 260px;

            padding: 25px;

            min-height: calc(100vh - 65px);

            transition: all 0.3s ease;
        }


        /* =========================
            TOP NAVBAR
        ========================== */

        .navbar-custom {

            background-color: #ffffff;

            box-shadow: 0 1px 3px rgba(0,0,0,0.04);

            padding: 0.8rem 1.5rem;

            margin-left: 260px;

            border-bottom: 1px solid #e5e7eb;

            transition: all 0.3s ease;
        }


        .navbar-custom .navbar-brand {

            font-size: 15px;
        }


        /* =========================
            NOTIFICATION
        ========================== */

        .notification-dot {

            width: 8px;

            height: 8px;

            background-color: #ef4444;

            border: 2px solid white;

            border-radius: 50%;
        }


        /* =========================
            SIDEBAR FOOTER
        ========================== */

        .sidebar-footer {

            padding: 12px;

            margin-top: 5px;
        }


        .logout-btn {

            background-color: #fee2e2;

            border-color: #fecaca !important;

            color: #dc2626;
        }


        .logout-btn:hover {

            background-color: #fecaca;

            color: #b91c1c;
        }


        /* =========================
            MOBILE TOGGLE
        ========================== */

        .sidebar-toggle {

            border: none;

            background-color: transparent;

            color: #4b5563;

            font-size: 20px;
        }


        /* =========================
            MOBILE OVERLAY
        ========================== */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background: rgba(0,0,0,0.25);

            z-index: 1040;
        }


        /* =========================
            RESPONSIVE
        ========================== */

        @media (max-width: 992px) {

            .sidebar {

                left: -260px;
            }


            .sidebar.show {

                left: 0;
            }


            .main-content {

                margin-left: 0;

                padding: 15px;
            }


            .navbar-custom {

                margin-left: 0;

                padding: 0.7rem 1rem;
            }


            .sidebar-overlay.show {

                display: block;
            }
        }

    </style>

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar d-flex flex-column" id="sidebar">


    <!-- BRAND -->

    <a href="{{ url('/dashboard') }}" class="brand-link">

        <i class="fa-solid fa-dumbbell"></i>

        GYM ADMIN

    </a>


    <!-- ADMIN PROFILE -->

    <div class="admin-profile">

        <div class="d-flex align-items-center">

            <div class="admin-avatar me-3">

                <i class="fa-solid fa-user-shield"></i>

            </div>

            <div>

                <div class="admin-name">
                    {{ Auth::user()->name ?? 'Admin' }}
                </div>

                <div class="admin-role">
                    {{ Auth::user()->getRoleNames()->implode(', ') ?? 'Gym Administrator' }}
                </div>

            </div>

        </div>

    </div>


    <div class="nav flex-column mt-1 mb-4 px-2" id="adminSidebar">


        <!-- =================================================
             OVERVIEW
        ================================================== -->

        <div class="sidebar-heading">
            Overview
        </div>


        <!-- Dashboard -->
        @can('dashboard')
        <div class="sidebar-dashboard">
        <a href="{{ route('admin.panel') }}"
           class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">

            <i class="fa-solid fa-gauge"></i>

            Dashboard

        </a>
        </div>
        @endcan

        <!-- Added Role-based Direct Links Here -->
       @if(optional(Auth::user())->hasRole('Super Admin') || optional(Auth::user())->can('gym_profile'))
            <li class="nav-item list-unstyled sidebar-branch" data-target-sidebar="sidebar-branch">
                <a class="nav-link" href="{{ url('/dashboard/branches') }}">Branch Management</a>
            </li>
        @endif

        @hasanyrole('Super Admin|Gym Owner|Manager')
            <li class="nav-item list-unstyled sidebar-members-direct" data-target-sidebar="sidebar-members-direct">
                <a class="nav-link" href="{{ route('members.index') }}">Members</a>
            </li>
        @endhasanyrole

        @role('Trainer')
            <li class="nav-item list-unstyled sidebar-assigned-members" data-target-sidebar="sidebar-assigned-members">
                <a class="nav-link" href="{{ route('trainer.assigned-members') }}">Assigned Members</a>
            </li>
        @endrole


        <!-- Gym Profile & Branches (Dropdown) -->
        @can('gym_profile')
        <div class="sidebar-gym-profile" data-target-sidebar="sidebar-gym-profile">
        <a href="#menuGymProfile" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/gym-profile*') || request()->is('dashboard/branches*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/gym-profile*') || request()->is('dashboard/branches*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-building"></i> Gym Profile & Branches</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/gym-profile*') || request()->is('dashboard/branches*') ? 'show' : '' }}" id="menuGymProfile" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/gym-profile') }}" class="nav-link small py-1 {{ request()->is('dashboard/gym-profile*') ? 'active-sub' : '' }}">Gym Profile</a>
                <a href="{{ url('/dashboard/branches') }}" class="nav-link small py-1 {{ request()->is('dashboard/branches*') ? 'active-sub' : '' }}">Branches</a>
            </div>
        </div>
        </div>
        @endcan



        <!-- =================================================
             MANAGEMENT
        ================================================== -->

        <div class="sidebar-heading">
            Management
        </div>


        <!-- Members (Dropdown) -->
        @can('members')
        <div class="sidebar-members" data-target-sidebar="sidebar-members">
        <a href="#menuMembers" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/members*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/members*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-users"></i> Members</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/members*') ? 'show' : '' }}" id="menuMembers" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/members') }}" class="nav-link small py-1 {{ request()->is('dashboard/members') ? 'active-sub' : '' }}">All Members</a>
                <a href="{{ url('/dashboard/members/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/members/create') ? 'active-sub' : '' }}">Add Member</a>
                <a href="{{ url('/dashboard/members/documents') }}" class="nav-link small py-1 {{ request()->is('dashboard/members/documents*') ? 'active-sub' : '' }}">Member Documents</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Leads & Enquiries (Dropdown) -->
        @can('enquiries')
        <div class="sidebar-enquiries" data-target-sidebar="sidebar-enquiries">
        <a href="#menuEnquiries" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/enquiries*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/enquiries*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-user-clock"></i> Leads & Enquiries</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/enquiries*') ? 'show' : '' }}" id="menuEnquiries" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/enquiries') }}" class="nav-link small py-1 {{ request()->is('dashboard/enquiries') ? 'active-sub' : '' }}">All Enquiries</a>
                <a href="{{ url('/dashboard/enquiries/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/enquiries/create') ? 'active-sub' : '' }}">New Enquiry</a>
                <a href="{{ url('/dashboard/enquiries/follow-ups') }}" class="nav-link small py-1 {{ request()->is('dashboard/enquiries/follow-ups*') ? 'active-sub' : '' }}">Follow-ups</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Attendance (Dropdown) -->
        @can('attendance')
        <div class="sidebar-attendance" data-target-sidebar="sidebar-attendance">
        <a href="#menuAttendance" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/attendance*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/attendance*') ? 'true' : 'false' }}">

            <span>
                <i class="fa-solid fa-clipboard-user"></i> Attendance
            </span>

            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>

        <div class="collapse submenu-container {{ request()->is('dashboard/attendance*') ? 'show' : '' }}" 
             id="menuAttendance" 
             data-bs-parent="#adminSidebar">

            <div class="nav flex-column ps-3 py-1">

                <!-- Manual Attendance -->
                <a href="{{ url('/dashboard/attendance/mark') }}" 
                   class="nav-link small py-1 {{ request()->is('dashboard/attendance/mark*') ? 'active-sub' : '' }}">
                    Mark Attendance
                </a>

                <!-- QR / Member ID Check-in -->
                <a href="{{ url('/dashboard/attendance/qr-scan') }}" 
                   class="nav-link small py-1 {{ request()->is('dashboard/attendance/qr-scan*') ? 'active-sub' : '' }}">
                    QR / ID Check-in
                </a>

                <!-- Today's Attendance -->
                <a href="{{ url('/dashboard/attendance/today') }}" 
                   class="nav-link small py-1 {{ request()->is('dashboard/attendance/today*') ? 'active-sub' : '' }}">
                    Today's Attendance
                </a>

                <!-- Attendance Reports -->
                <a href="{{ url('/dashboard/attendance/reports') }}" 
                   class="nav-link small py-1 {{ request()->is('dashboard/attendance/reports*') ? 'active-sub' : '' }}">
                    Attendance Reports
                </a>

            </div>
        </div>
        </div>
        @endcan

        <!-- Trainers & Fitness (Dropdown) -->
        @can('trainers')
        <div class="sidebar-trainers" data-target-sidebar="sidebar-trainers">
        <a href="#menuTrainers" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/trainers*') || request()->is('dashboard/workouts*') || request()->is('dashboard/diets*') || request()->is('dashboard/fitness*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/trainers*') || request()->is('dashboard/workouts*') || request()->is('dashboard/diets*') || request()->is('dashboard/fitness*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-user-tie"></i> Trainers & Fitness</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/trainers*') || request()->is('dashboard/workouts*') || request()->is('dashboard/diets*') || request()->is('dashboard/fitness*') ? 'show' : '' }}" id="menuTrainers" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/trainers') }}" class="nav-link small py-1 {{ request()->is('dashboard/trainers') ? 'active-sub' : '' }}">Trainers</a>
                <a href="{{ url('/dashboard/trainers/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/trainers/create') ? 'active-sub' : '' }}">Add Trainer</a>

                <a href="{{ url('/dashboard/workouts/exercises') }}" class="nav-link small py-1 {{ request()->is('dashboard/workouts/exercises*') ? 'active-sub' : '' }}">Exercise Library</a>
                <a href="{{ route('fitness-goals.index') }}" class="nav-link small py-1 {{ request()->is('dashboard/fitness/goals*') ? 'active-sub' : '' }}">Fitness Goals</a>
                <a href="{{ url('/dashboard/fitness/measurements') }}" class="nav-link small py-1 {{ request()->is('dashboard/fitness/measurements*') ? 'active-sub' : '' }}">Body Measurements</a>
                <a href="{{ url('/dashboard/workouts/plans') }}" class="nav-link small py-1 {{ request()->is('dashboard/workouts/plans*') ? 'active-sub' : '' }}">Workout Plans</a>
                <a href="{{ url('/dashboard/diets/plans') }}" class="nav-link small py-1 {{ request()->is('dashboard/diets*') ? 'active-sub' : '' }}">Diet Plans</a>
                <a href="{{ url('/dashboard/fitness/progress') }}" class="nav-link small py-1 {{ request()->is('dashboard/fitness/progress*') ? 'active-sub' : '' }}">Progress Tracking</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Staff Management (Dropdown) -->
        @can('staff')
        <div class="sidebar-staff" data-target-sidebar="sidebar-staff">
        <a href="#menuStaff" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/staff*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/staff*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-users-gear"></i> Staff Management</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/staff*') ? 'show' : '' }}" id="menuStaff" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/staff') }}" class="nav-link small py-1 {{ request()->is('dashboard/staff') ? 'active-sub' : '' }}">Staff</a>
                <a href="{{ url('/dashboard/staff/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/staff/create') ? 'active-sub' : '' }}">Add Staff</a>
                <a href="{{ url('/dashboard/staff/attendance') }}" class="nav-link small py-1 {{ request()->is('dashboard/staff/attendance*') ? 'active-sub' : '' }}">Staff Attendance</a>
                <a href="{{ url('/dashboard/staff/leaves') }}" class="nav-link small py-1 {{ request()->is('dashboard/staff/leaves*') ? 'active-sub' : '' }}">Leave Management</a>
            </div>
        </div>
        </div>
        @endcan



        <!-- =================================================
             OPERATIONS
        ================================================== -->

        <div class="sidebar-heading">
            Operations
        </div>


        <!-- Memberships (Dropdown) -->
        @can('memberships')
        <div class="sidebar-memberships" data-target-sidebar="sidebar-memberships">
        <a href="#menuMemberships" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/memberships*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/memberships*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-id-card"></i> Memberships</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/memberships*') ? 'show' : '' }}" id="menuMemberships" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/memberships/plans') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/plans*') ? 'active-sub' : '' }}">Membership Plans</a>
                <a href="{{ url('/dashboard/memberships/active') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/active*') ? 'active-sub' : '' }}">Active Memberships</a>
                <a href="{{ url('/dashboard/memberships/purchase') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/purchase*') ? 'active-sub' : '' }}">Membership Purchase</a>
                <a href="{{ url('/dashboard/memberships/renewals') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/renewals*') ? 'active-sub' : '' }}">Renewals</a>
                <a href="{{ url('/dashboard/memberships/freeze') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/freeze*') ? 'active-sub' : '' }}">Freeze Membership</a>
                <a href="{{ url('/dashboard/memberships/expiring') }}" class="nav-link small py-1 {{ request()->is('dashboard/memberships/expiring*') ? 'active-sub' : '' }}">Expiring / Expired</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Payments & Invoices (Dropdown) -->
        @can('payments')
        <div class="sidebar-payments" data-target-sidebar="sidebar-payments">
        <a href="#menuPayments" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/payments*') || request()->is('dashboard/invoices*') || request()->is('dashboard/receipts*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/payments*') || request()->is('dashboard/invoices*') || request()->is('dashboard/receipts*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-credit-card"></i> Payments & Invoices</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/payments*') || request()->is('dashboard/invoices*') || request()->is('dashboard/receipts*') ? 'show' : '' }}" id="menuPayments" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/payments') }}" class="nav-link small py-1 {{ request()->is('dashboard/payments') ? 'active-sub' : '' }}">Payments</a>
                <a href="{{ url('/dashboard/payments/pending') }}" class="nav-link small py-1 {{ request()->is('dashboard/payments/pending*') ? 'active-sub' : '' }}">Pending Payments</a>
                <a href="{{ url('/dashboard/invoices') }}" class="nav-link small py-1 {{ request()->is('dashboard/invoices*') ? 'active-sub' : '' }}">Invoices</a>
                <a href="{{ url('/dashboard/receipts') }}" class="nav-link small py-1 {{ request()->is('dashboard/receipts*') ? 'active-sub' : '' }}">Receipts</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Expenses (Dropdown) -->
        @can('expenses')
        <div class="sidebar-expenses" data-target-sidebar="sidebar-expenses">
        <a href="#menuExpenses" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/expenses*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/expenses*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-wallet"></i> Expenses</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/expenses*') ? 'show' : '' }}" id="menuExpenses" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/expenses') }}" class="nav-link small py-1 {{ request()->is('dashboard/expenses') && !request()->is('dashboard/expenses/create') && !request()->is('dashboard/expenses/categories') ? 'active-sub' : '' }}">All Expenses</a>
                <a href="{{ url('/dashboard/expenses/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/expenses/create') ? 'active-sub' : '' }}">Add Expense</a>
                <a href="{{ url('/dashboard/expenses/categories') }}" class="nav-link small py-1 {{ request()->is('dashboard/expenses/categories*') ? 'active-sub' : '' }}">Expense Categories</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Equipment (Dropdown) -->
        @can('equipment')
        <div class="sidebar-equipment" data-target-sidebar="sidebar-equipment">
        <a href="#menuEquipment" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/equipment*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/equipment*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-screwdriver-wrench"></i> Equipment</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/equipment*') ? 'show' : '' }}" id="menuEquipment" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/equipment') }}" class="nav-link small py-1 {{ request()->is('dashboard/equipment') && !request()->is('dashboard/equipment/create') && !request()->is('dashboard/equipment/maintenance*') ? 'active-sub' : '' }}">Equipment Inventory</a>
                <a href="{{ url('/dashboard/equipment/create') }}" class="nav-link small py-1 {{ request()->is('dashboard/equipment/create') ? 'active-sub' : '' }}">Add Equipment</a>
                <a href="{{ url('/dashboard/equipment/maintenance') }}" class="nav-link small py-1 {{ request()->is('dashboard/equipment/maintenance*') ? 'active-sub' : '' }}">Maintenance</a>
            </div>
        </div>
        </div>
        @endcan



        <!-- =================================================
             REPORTS
        ================================================== -->

        <div class="sidebar-heading">
            Reports
        </div>


        <!-- Reports (Dropdown) -->
        @can('reports')
        <div class="sidebar-reports" data-target-sidebar="sidebar-reports">
        <a href="#menuReports" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/reports*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/reports*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-chart-line"></i> Reports</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/reports*') ? 'show' : '' }}" id="menuReports" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/reports/members') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/members*') ? 'active-sub' : '' }}">Member Reports</a>
                <a href="{{ url('/dashboard/reports/memberships') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/memberships*') ? 'active-sub' : '' }}">Membership Reports</a>
                <a href="{{ url('/dashboard/reports/revenue') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/revenue*') ? 'active-sub' : '' }}">Revenue Reports</a>
                <a href="{{ url('/dashboard/reports/attendance') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/attendance*') ? 'active-sub' : '' }}">Attendance Reports</a>
                <a href="{{ url('/dashboard/reports/trainers') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/trainers*') ? 'active-sub' : '' }}">Trainer Reports</a>
                <a href="{{ url('/dashboard/reports/expenses') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/expenses*') ? 'active-sub' : '' }}">Expense Reports</a>
                <a href="{{ url('/dashboard/reports/leads') }}" class="nav-link small py-1 {{ request()->is('dashboard/reports/leads*') ? 'active-sub' : '' }}">Lead Reports</a>
            </div>
        </div>
        </div>
        @endcan



        <!-- =================================================
             SYSTEM
        ================================================== -->

        <div class="sidebar-heading">
            System
        </div>


        <!-- Roles & Permissions Page Added Here -->
        @can('roles_permissions')
        <div class="sidebar-roles" data-target-sidebar="sidebar-roles">
        <a href="{{ url('/dashboard/system/roles-permissions') }}"
           class="nav-link {{ request()->is('dashboard/system/roles-permissions*') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-keyhole"></i>
            Roles & Permissions
        </a>
        </div>
        @endcan


        <!-- Notifications & Alerts -->
        @can('notifications')
        <div class="sidebar-notifications" data-target-sidebar="sidebar-notifications">
        <a href="{{ url('/dashboard/notifications') }}"
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/notifications*') ? 'active' : '' }}">

            <span>
                <i class="fa-solid fa-bell"></i>
                Notifications & Alerts
            </span>

            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif

        </a>
        </div>
        @endcan


        <!-- Activity Logs -->
        @can('activity_logs')
        <div class="sidebar-activity-logs" data-target-sidebar="sidebar-activity-logs">
        <a href="{{ url('/dashboard/system/activity-logs') }}"
           class="nav-link {{ request()->is('dashboard/system/activity-logs*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
            Activity Logs
        </a> 
        </div>
        @endcan


        <!-- SMS / WhatsApp -->
        @can('sms_whatsapp')
        <div class="sidebar-sms" data-target-sidebar="sidebar-sms">
        <a href="{{ url('/dashboard/sms-whatsapp') }}"
           class="nav-link {{ request()->is('dashboard/sms-whatsapp*') ? 'active' : '' }}">

            <i class="fa-solid fa-comment-sms"></i>

            SMS / WhatsApp

        </a>
        </div>
        @endcan


        <!-- Two Factor -->
        @can('two_factor')
        <div class="sidebar-two-factor" data-target-sidebar="sidebar-two-factor">
        <a href="{{ url('/dashboard/two-factor') }}"
           class="nav-link {{ request()->is('dashboard/two-factor*') ? 'active' : '' }}">

            <i class="fa-solid fa-shield-halved"></i>

            Two-Factor Auth

        </a>
        </div>
        @endcan



        <!-- =================================================
             ADMIN ACCOUNT
        ================================================== -->

        <div class="sidebar-heading">
            Account
        </div>


        <!-- Profile -->
        @can('admin_profile')
        <div class="sidebar-profile">
        <a href="{{ url('/dashboard/profile') }}"
           class="nav-link {{ request()->is('dashboard/profile*') ? 'active' : '' }}">

            <i class="fa-solid fa-user"></i>

            Admin Profile

        </a>
        </div>
        @endcan


        <!-- Settings (Dropdown) -->
        @can('settings')
        <div class="sidebar-settings">
        <a href="#menuSettings" 
           data-bs-toggle="collapse" 
           class="nav-link d-flex justify-content-between align-items-center {{ request()->is('dashboard/settings*') ? 'active' : 'collapsed' }}" 
           aria-expanded="{{ request()->is('dashboard/settings*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-gear"></i> Settings</span>
            <i class="fa-solid fa-chevron-down submenu-chevron small"></i>
        </a>
        <div class="collapse submenu-container {{ request()->is('dashboard/settings*') ? 'show' : '' }}" id="menuSettings" data-bs-parent="#adminSidebar">
            <div class="nav flex-column ps-3 py-1">
                <a href="{{ url('/dashboard/settings/general') }}" class="nav-link small py-1 {{ request()->is('dashboard/settings/general*') ? 'active-sub' : '' }}">General Settings</a>
                <a href="{{ url('/dashboard/settings/invoice') }}" class="nav-link small py-1 {{ request()->is('dashboard/settings/invoice*') ? 'active-sub' : '' }}">Invoice Settings</a>
                <a href="{{ url('/dashboard/settings/tax') }}" class="nav-link small py-1 {{ request()->is('dashboard/settings/tax*') ? 'active-sub' : '' }}">Tax Settings</a>
                <a href="{{ url('/dashboard/settings/notifications') }}" class="nav-link small py-1 {{ request()->is('dashboard/settings/notifications*') ? 'active-sub' : '' }}">Notification Settings</a>
            </div>
        </div>
        </div>
        @endcan


        <!-- Help -->
        @can('help')
        <div class="sidebar-help">
        <a href="{{ url('/dashboard/help') }}"
           class="nav-link {{ request()->is('dashboard/help*') ? 'active' : '' }}">

            <i class="fa-solid fa-circle-question"></i>

            Help & Documentation

        </a>
        </div>
        @endcan


        <!-- =================================================
             LOGOUT
        ================================================== -->

        <div class="sidebar-footer">

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit"
                        class="btn logout-btn border w-100 btn-sm fw-semibold">

                    <i class="fa-solid fa-right-from-bracket me-1"></i>

                    Logout

                </button>

            </form>

        </div>


    </div>

</div>



<!-- =========================================================
     MOBILE OVERLAY
========================================================= -->

<div class="sidebar-overlay" id="sidebarOverlay"></div>



<!-- =========================================================
     TOP NAVBAR
========================================================= -->

<nav class="navbar navbar-expand navbar-custom">

    <div class="container-fluid">


        <!-- Mobile Sidebar Button -->

        <button
            class="sidebar-toggle d-lg-none me-3"
            id="sidebarToggle">

            <i class="fa-solid fa-bars"></i>

        </button>


        <!-- Page Name -->

        <span class="navbar-brand mb-0 text-secondary fw-semibold">

            Admin Panel

        </span>


        <!-- Right Side -->

        <ul class="navbar-nav ms-auto align-items-center">


            <!-- Notification (Bell Icon directly linking to Notifications Page) -->

            <li class="nav-item me-3 position-relative">

                <a href="{{ url('/dashboard/notifications') }}"
                   id="notificationBell"
                   class="text-secondary position-relative text-text-decoration-none">

                    <i class="fa-regular fa-bell fs-5"></i>

                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            {{ $unreadCount }}
                        </span>
                    @else
                        <span id="notificationDot"
                            class="position-absolute top-0 start-100 translate-middle notification-dot" style="display: none;">
                        </span>
                    @endif

                </a>

            </li>


            <!-- Admin -->

            <li class="nav-item">

                <a href="{{ url('/dashboard/profile') }}"
                   class="nav-link text-dark fw-bold small">

                    <i class="fa-solid fa-user-shield text-primary me-1"></i>

                    {{ Auth::user()->name ?? 'Admin' }}

                </a>

            </li>

        </ul>


    </div>

</nav>



<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="main-content">

    <div class="container-fluid">

        @yield('content')

    </div>

</div>


<!-- =========================================================
     BOOTSTRAP TOAST CONTAINER (Positioned near the Bell Icon on Top-Right)
========================================================= -->
<div class="toast-container position-fixed p-3" style="top: 60px; right: 70px; z-index: 1080;">
    <div id="liveToast" class="toast align-items-center text-bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                <i class="fa-solid fa-bell text-warning me-2"></i> Membership expiring soon!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Notification Sound (Audio element fixed with working source) -->
<audio id="notificationSound" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
</audio>


<!-- =========================================================
     BOOTSTRAP JS
========================================================= -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- =========================================================
     SCRIPTS (Sidebar Toggle, Sound, Toast, Dynamic Member Names)
========================================================= -->

<script>
document.addEventListener("DOMContentLoaded", function () {

    // 1. Sidebar Mobile Toggle
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const overlay = document.getElementById("sidebarOverlay");

    if (toggle) {
        toggle.addEventListener("click", function () {
            sidebar.classList.toggle("show");
            overlay.classList.toggle("show");
        });
    }

    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
        });
    }

    // 2. Notification Audio & Toast Popup Logic
    const notificationBell = document.getElementById("notificationBell");
    const notificationBadge = document.getElementById("notificationBadge");
    const notificationDot = document.getElementById("notificationDot");
    const notificationSound = document.getElementById("notificationSound");
    const liveToastEl = document.getElementById("liveToast");
    const toastMessage = document.getElementById("toastMessage");
    
    const toast = new bootstrap.Toast(liveToastEl, { delay: 6000 });

    function playNotificationSound() {
        if (notificationSound) {
            notificationSound.currentTime = 0;
            let playPromise = notificationSound.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Audio autoplay prevented by browser. Waiting for user interaction.");
                    document.body.addEventListener('click', function unlockAudio() {
                        notificationSound.play().catch(e => console.log(e));
                        document.body.removeEventListener('click', unlockAudio);
                    }, { once: true });
                });
            }
        }
    }

    window.triggerMemberNotification = function(messageText) {
        if (messageText) {
            toastMessage.innerHTML = `<i class="fa-solid fa-bell text-warning me-2"></i> ${messageText}`;
        }
        
        playNotificationSound();
        toast.show();
    };

    @if(isset($unreadNotifications) && count($unreadNotifications) > 0)
        const unreadList = @json($unreadNotifications);
        
        let shownNotifications = JSON.parse(localStorage.getItem("shown_notification_ids") || "[]");

        let latestUnread = unreadList.find(n => !shownNotifications.includes(n.id.toString()));

        if (latestUnread) {
            setTimeout(() => {
                triggerMemberNotification(latestUnread.message);
            }, 1000);

            shownNotifications.push(latestUnread.id.toString());
            localStorage.setItem("shown_notification_ids", JSON.stringify(shownNotifications));
        }

        if (notificationBell) {
            notificationBell.addEventListener("click", function (e) {
                let allIds = unreadList.map(n => n.id.toString());
                localStorage.setItem("shown_notification_ids", JSON.stringify(allIds));
                
                if (notificationBadge) notificationBadge.style.display = 'none';
                if (notificationDot) notificationDot.style.display = 'none';
            });
        }
    @endif

});
</script>


</body>

</html>