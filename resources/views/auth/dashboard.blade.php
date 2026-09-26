@extends('auth.sidebar')

@section('title', 'Dashboard - Gym Admin')

@section('content')

@php
    $totalMembers = $totalMembers ?? 0;
    $activeMembers = $activeMembers ?? 0;
    $todayAttendance = $todayAttendance ?? 0;
    $expiringSoon = $expiringSoon ?? 0;
    $monthlyCollection = $monthlyCollection ?? 0;

    $expiredMembers = $expiredMembers ?? 0;
    $totalTrainers = $totalTrainers ?? 0;
    $todayCollection = $todayCollection ?? 0;
    $pendingAmount = $pendingAmount ?? 0;

    $todayNewMembers = $todayNewMembers ?? 0;
    $todayRenewals = $todayRenewals ?? 0;
    $followUpsDue = $followUpsDue ?? 0;
    $alertCount = $alertCount ?? 0;

    $activePercentage = $activePercentage ?? 77;
    $renewalPercentage = $renewalPercentage ?? 29;
    $expiredPercentage = $expiredPercentage ?? 86;

    $recentMembers = $recentMembers ?? collect();
    $paymentDues = $paymentDues ?? collect();
    $expiringMembers = $expiringMembers ?? collect();
    $recentPayments = $recentPayments ?? collect();

    $attendanceLabels = $attendanceLabels ?? ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'];
    $attendanceData = $attendanceData ?? [45, 30, 55, 70, 85, 60];

    $revenueLabels = $revenueLabels ?? ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'];
    $revenueData = $revenueData ?? [35000, 65000, 45000, 80000, 50000, 75000];

    $leadLabels = $leadLabels ?? ['New Leads', 'Contacted', 'Follow-up', 'Converted', 'Lost'];
    $leadData = $leadData ?? [25, 18, 30, 45, 10];

    $expenseLabels = $expenseLabels ?? ['Rent', 'Equipment', 'Trainer Salary', 'Marketing', 'Miscellaneous'];
    $expenseData = $expenseData ?? [28500, 36700, 11400, 68900, 4200];
@endphp


<!-- Toastr CSS & jQuery -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Notification Sound Audio Element -->
<audio id="notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>


<!-- ========================================================= -->
<!-- PAGE HEADER -->
<!-- ========================================================= -->

<div class="row mb-4 align-items-center">

    <div class="col-lg-3 col-md-5">
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-gauge-high text-primary me-2"></i>
            Dashboard
        </h2>

        <p class="text-muted small mb-0">
            Welcome back, Admin!
        </p>
    </div>


    <!-- Branch -->
    <div class="col-lg-3 col-md-4 mt-3 mt-md-0">
        <select class="form-select rounded-4 shadow-sm border-light text-muted small fw-semibold">
            <option selected>All Branches (HQ)</option>
            <option>Downtown Fitness Branch</option>
            <option>Westside CrossFit Studio</option>
        </select>
    </div>


    <!-- Clock + Calendar + Notification Bell -->
    <div class="col-lg-6 mt-3 mt-lg-0">
        <div class="d-flex justify-content-lg-end align-items-center gap-2 flex-wrap">

    
        
        <!-- Yithe aapan expiring members cha count red badge madhe dakhvat ahot -->
        @if(isset($expiringMembers) && $expiringMembers->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                {{ $expiringMembers->count() }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg p-2" aria-labelledby="dropdownMenuButton" style="width: 320px; border-radius: 12px;">
        <h6 class="dropdown-header fw-bold text-primary pb-2 border-bottom">
            <i class="fa-solid fa-bell me-1"></i> Expiry Notifications
        </h6>
        
        @forelse($expiringMembers ?? [] as $member)
            @php
                $expiry = $member->expiry_date ?? optional($member->latestSubscription)->end_date;
                $formattedExpiry = $expiry ? \Carbon\Carbon::parse($expiry)->format('d M Y') : 'N/A';
            @endphp
            <div class="dropdown-item py-2 border-bottom text-wrap" style="font-size: 13px;">
                <div class="fw-bold text-dark">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> {{ $member->name }}
                </div>
                <div class="text-muted small">
                    Membership expiring on <span class="fw-semibold text-danger">{{ $formattedExpiry }}</span>
                </div>
            </div>
        @empty
            <div class="dropdown-item text-center text-muted py-3" style="font-size: 13px;">
                No upcoming expirations in next 3 days.
            </div>
        @endforelse
    </div>
</div>
            <!-- IST CLOCK -->
            <div class="bg-white px-3 py-2 rounded-4 shadow-sm border border-light d-inline-flex align-items-center">
                <i class="fa-solid fa-clock text-success me-2 fs-5"></i>
                <div>
                    <span class="d-block text-muted fw-bold text-uppercase" style="font-size:0.65rem; letter-spacing:0.5px;">
                        IST India
                    </span>
                    <span id="live-time-ist" class="d-block fw-bold text-dark" style="font-size:0.85rem;">
                        --:--:--
                    </span>
                </div>
            </div>

            <!-- CALENDAR -->
            <div class="dropdown">
                <button class="btn bg-white px-3 py-2 rounded-4 shadow-sm border border-light d-inline-flex align-items-center text-start dropdown-toggle" type="button" id="calendarDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="min-width:170px;">
                    <i class="fa-regular fa-calendar-days text-primary me-2 fs-5"></i>
                    <div>
                        <span id="live-day" class="d-block text-muted fw-bold text-uppercase" style="font-size:0.65rem;"></span>
                        <span id="live-date" class="d-block fw-bold text-dark" style="font-size:0.85rem;"></span>
                    </div>
                </button>

                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 rounded-4 mt-2" style="width:290px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 id="cal-month-year" class="fw-bold text-dark mb-0"></h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-light rounded-circle me-1" id="prev-month">
                                <i class="fa-solid fa-chevron-left fa-xs"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light rounded-circle" id="next-month">
                                <i class="fa-solid fa-chevron-right fa-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid text-center text-muted fw-bold small mb-2" style="grid-template-columns:repeat(7,1fr);">
                        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                    </div>

                    <div id="cal-days-grid" class="d-grid text-center small" style="grid-template-columns:repeat(7,1fr); row-gap:6px;"></div>

                    <div class="mt-3 pt-2 border-top">
                        <button type="button" class="btn btn-primary btn-sm w-100 rounded-pill" id="today-btn">
                            Today
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>



<!-- ========================================================= -->
<!-- STAT CARDS -->
<!-- ========================================================= -->

<div class="row g-3 mb-4">

    <!-- ACTIVE MEMBERS -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div>
                    <h2 class="fw-bold mb-0">{{ $activeMembers }}</h2>
                    <p class="small mb-0 text-white-50 fw-semibold">Active Members</p>
                </div>
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="fa-solid fa-users fa-3x"></i>
                </div>
                <div class="mt-3 pt-2 border-top border-white border-opacity-10 text-center">
                    <a href="{{ url('/dashboard/members') }}" class="text-white text-decoration-none small fw-semibold">
                        More info <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background:linear-gradient(135deg,#10b981,#047857);">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div>
                    <h2 class="fw-bold mb-0">
                        {{ $todayAttendance }} <span class="fs-4">%</span>
                    </h2>
                    <p class="small mb-0 text-white-50 fw-semibold">Today's Attendance Rate</p>
                </div>
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="fa-solid fa-clipboard-user fa-3x"></i>
                </div>
                <div class="mt-3 pt-2 border-top border-white border-opacity-10 text-center">
                    <a href="{{ url('/dashboard/attendance') }}" class="text-white text-decoration-none small fw-semibold">
                        More info <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- EXPIRING -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 text-dark h-100" style="background:linear-gradient(135deg,#fbbf24,#d97706);">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div>
                    <h2 class="fw-bold mb-0">{{ $expiringSoon }}</h2>
                    <p class="small mb-0 fw-semibold">Expiring Soon (7 Days)</p>
                </div>
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                </div>
                <div class="mt-3 pt-2 border-top border-dark border-opacity-10 text-center">
                    <a href="{{ url('/dashboard/membership') }}" class="text-dark text-decoration-none small fw-semibold">
                        More info <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- COLLECTION -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div>
                    <h2 class="fw-bold mb-0">₹{{ number_format((float)$monthlyCollection) }}</h2>
                    <p class="small mb-0 text-white-50 fw-semibold">Monthly Collection</p>
                </div>
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="fa-solid fa-wallet fa-3x"></i>
                </div>
                <div class="mt-3 pt-2 border-top border-white border-opacity-10 text-center">
                    <a href="{{ url('/dashboard/payment') }}" class="text-white text-decoration-none small fw-semibold">
                        More info <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- ========================================================= -->
<!-- QUICK ACTIONS -->
<!-- ========================================================= -->

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="fa-solid fa-bolt text-warning me-2"></i> Quick Actions
                </h5>
                <small class="text-muted">Frequently used gym operations</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ route('members.create') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-user-plus text-primary d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Add Member</span>
                </a>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ url('/dashboard/attendance') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-calendar-check text-success d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Attendance</span>
                </a>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ url('/dashboard/payment/create') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-money-bill-wave text-danger d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Add Payment</span>
                </a>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ route('members.index') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-id-card text-warning d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Membership</span>
                </a>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ url('/dashboard/plans') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-dumbbell text-info d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Workout Plan</span>
                </a>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6">
                <a href="{{ url('/dashboard/trainers/create') }}" class="btn btn-light border w-100 rounded-4 py-3">
                    <i class="fa-solid fa-user-tie text-secondary d-block fs-4 mb-2"></i>
                    <span class="small fw-semibold">Add Trainer</span>
                </a>
            </div>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- CHARTS ROW 1 -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">
    <!-- MEMBERSHIP METRICS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-chart-pie text-primary me-2"></i> Membership Status Metrics
                </h5>
            </div>
            <div class="card-body px-4">
                <div class="row text-center g-3">
                    <div class="col-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="circularChart1" width="100" height="100"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle fw-bold text-dark">{{ $activePercentage }}%</div>
                        </div>
                        <p class="small fw-bold text-muted mt-2 mb-0">ACTIVE</p>
                    </div>
                    <div class="col-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="circularChart2" width="100" height="100"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle fw-bold text-dark">{{ $renewalPercentage }}%</div>
                        </div>
                        <p class="small fw-bold text-muted mt-2 mb-0">RENEWALS</p>
                    </div>
                    <div class="col-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="circularChart3" width="100" height="100"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle fw-bold text-dark">{{ $expiredPercentage }}%</div>
                        </div>
                        <p class="small fw-bold text-muted mt-2 mb-0">EXPIRED</p>
                    </div>
                </div>
                <div class="row mt-4 pt-3 border-top text-muted small">
                    <div class="col-4 text-center">Standard: Active</div>
                    <div class="col-4 text-center">Premium: Stable</div>
                    <div class="col-4 text-center">Basic: Peak</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-chart-column text-success me-2"></i> Attendance Trends
                    </h5>
                    <span class="small text-muted">Monthly</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="height:250px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- CHARTS ROW 2 -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">
    <!-- REVENUE -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-chart-area text-warning me-2"></i> Revenue Analytics
                </h5>
            </div>
            <div class="card-body px-4">
                <div style="height:250px;">
                    <canvas id="areaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- LEADS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-chart-pie text-info me-2"></i> Lead / Enquiry Conversion
                </h5>
            </div>
            <div class="card-body">
                <div style="height:250px;">
                    <canvas id="polarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- EXPENSE CHART & TODAY SUMMARY -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-chart-line text-danger me-2"></i> Expense & Operational Cost
                </h5>
            </div>
            <div class="card-body">
                <div style="height:280px;">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- TODAY SUMMARY -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-calendar-day text-primary me-2"></i> Today's Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <span class="text-muted small">New Members</span>
                    <span class="badge bg-primary rounded-pill">{{ $todayNewMembers }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <span class="text-muted small">Renewals</span>
                    <span class="badge bg-success rounded-pill">{{ $todayRenewals }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <span class="text-muted small">Follow-ups Due</span>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $followUpsDue }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted small">Payment Due</span>
                    <span class="badge bg-danger rounded-pill">₹{{ number_format((float)$pendingAmount) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- EXPENSE BREAKDOWN -->
<!-- ========================================================= -->

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-bars-progress text-danger me-2"></i> Expense & Operational Cost Breakdown
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">RENT & UTILITIES</span>
                        <span class="small text-muted">₹28,500</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-primary" style="width:75%;"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">EQUIPMENT MAINTENANCE</span>
                        <span class="small text-muted">₹36,700</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:85%;"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">TRAINER SALARIES</span>
                        <span class="small text-muted">₹11,400</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:40%;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">MARKETING & LEADS</span>
                        <span class="small text-muted">₹68,900</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-danger" style="width:90%;"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">MISCELLANEOUS</span>
                        <span class="small text-muted">₹4,200</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-info" style="width:55%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- TABLES SECTION -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">
    <!-- RECENT MEMBERS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-user-group text-primary me-2"></i> Recent Members
                    </h5>
                    <a href="{{ url('/dashboard/members') }}" class="small text-primary text-decoration-none">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentMembers as $member)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark">{{ data_get($member, 'name', 'Member') }}</div>
                                    <small class="text-muted">{{ data_get($member, 'email', '') }}</small>
                                </td>
                                <td>
                                    <small>{{ optional(data_get($member, 'created_at'))->format('d M Y') ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No recent members found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT DUE -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-money-bill-transfer text-danger me-2"></i> Payment Due
                    </h5>
                    <a href="{{ url('/dashboard/payment') }}" class="small text-primary text-decoration-none">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($paymentDues as $payment)
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    {{ data_get($payment, 'member.name', data_get($payment, 'name', 'Member')) }}
                                </td>
                                <td class="fw-bold">
                                    ₹{{ number_format((float)data_get($payment, 'amount', 0)) }}
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger">Due</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No payment dues found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- EXPIRING + RECENT PAYMENTS -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">
    <!-- EXPIRING MEMBERS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-hourglass-half text-warning me-2"></i> Memberships Expiring Soon
                    </h5>
                    <a href="{{ url('/dashboard/membership') }}" class="small text-primary text-decoration-none">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Expiry</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($expiringMembers as $member)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ data_get($member, 'name', 'Member') }}</td>
                                <td><small>{{ optional(data_get($member, 'end_date'))->format('d M Y') ?? '-' }}</small></td>
                                <td>
                                    <a href="{{ url('/dashboard/membership') }}" class="btn btn-sm btn-warning rounded-pill">Renew</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No memberships expiring soon.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT PAYMENTS -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-receipt text-success me-2"></i> Recent Payments
                    </h5>
                    <a href="{{ url('/dashboard/payment') }}" class="small text-primary text-decoration-none">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    {{ data_get($payment, 'member.name', data_get($payment, 'name', 'Member')) }}
                                </td>
                                <td class="fw-bold">
                                    ₹{{ number_format((float)data_get($payment, 'amount', 0)) }}
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success">Paid</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No recent payments found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- IMPORTANT ALERTS -->
<!-- ========================================================= -->

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-bell text-danger me-2"></i> Important Alerts
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="alert alert-danger border-0 rounded-4 mb-0 h-100">
                    <div class="d-flex">
                        <i class="fa-solid fa-money-bill-wave fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold">Payment Due</h6>
                            <p class="small mb-2">Members have pending payments.</p>
                            <a href="{{ url('/dashboard/payment') }}" class="btn btn-sm btn-danger rounded-pill">Check Payments</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-warning border-0 rounded-4 mb-0 h-100">
                    <div class="d-flex">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold">Membership Expiring</h6>
                            <p class="small mb-2">{{ $expiringSoon }} memberships expire within 7 days.</p>
                            <a href="{{ url('/dashboard/membership') }}" class="btn btn-sm btn-warning rounded-pill">View Members</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="alert alert-info border-0 rounded-4 mb-0 h-100">
                    <div class="d-flex">
                        <i class="fa-solid fa-phone-volume fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold">Follow-ups</h6>
                            <p class="small mb-2">{{ $followUpsDue }} customer follow-ups are due.</p>
                            <a href="{{ url('/dashboard/enquiries') }}" class="btn btn-sm btn-info rounded-pill text-white">View Enquiries</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- ========================================================= -->
<!-- CHART DATA BRIDGE -->
<!-- ========================================================= -->

<div
    id="dashboardChartData"
    data-attendance-labels="{{ json_encode($attendanceLabels) }}"
    data-attendance-data="{{ json_encode($attendanceData) }}"
    data-revenue-labels="{{ json_encode($revenueLabels) }}"
    data-revenue-data="{{ json_encode($revenueData) }}"
    data-lead-labels="{{ json_encode($leadLabels) }}"
    data-lead-data="{{ json_encode($leadData) }}"
    data-expense-labels="{{ json_encode($expenseLabels) }}"
    data-expense-data="{{ json_encode($expenseData) }}"
    data-active-percentage="{{ $activePercentage }}"
    data-renewal-percentage="{{ $renewalPercentage }}"
    data-expired-percentage="{{ $expiredPercentage }}"
    style="display:none;">
</div>



<!-- ========================================================= -->
<!-- SCRIPTS (Chart.js, Toastr & Dashboard Logic) -->
<!-- ========================================================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Toastr Setup
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "timeOut": "5000"
    };

    window.showNotificationPopup = function(title, message) {
        var audio = document.getElementById('notification-sound');
        if (audio) {
            audio.play().catch(function(error) {
                console.log("Audio autoplay prevented by browser.");
            });
        }
        toastr.info(message, title);
    }

    // Live India Clock
    function updateIndiaTime() {
        const now = new Date();
        const options = {
            timeZone: "Asia/Kolkata",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true
        };
        const clock = document.getElementById("live-time-ist");
        if (clock) {
            clock.textContent = new Intl.DateTimeFormat("en-US", options).format(now);
        }
    }
    updateIndiaTime();
    setInterval(updateIndiaTime, 1000);

    // Top Date
    function updateTopDate() {
        const now = new Date();
        const day = document.getElementById("live-day");
        const date = document.getElementById("live-date");
        if (day) day.textContent = now.toLocaleDateString("en-US", { weekday: "long" });
        if (date) date.textContent = now.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
    }
    updateTopDate();

    // Calendar
    const calendarGrid = document.getElementById("cal-days-grid");
    const calendarTitle = document.getElementById("cal-month-year");
    const previousButton = document.getElementById("prev-month");
    const nextButton = document.getElementById("next-month");
    const todayButton = document.getElementById("today-btn");

    const realToday = new Date();
    let currentMonth = realToday.getMonth();
    let currentYear = realToday.getFullYear();
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    function renderCalendar(month, year) {
        if (!calendarGrid || !calendarTitle) return;
        calendarTitle.textContent = monthNames[month] + " " + year;
        calendarGrid.innerHTML = "";
        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            calendarGrid.appendChild(document.createElement("span"));
        }

        for (let day = 1; day <= totalDays; day++) {
            const button = document.createElement("button");
            button.type = "button";
            button.textContent = day;
            button.className = "btn btn-sm p-1 rounded-circle fw-semibold mx-auto";
            button.style.width = "30px";
            button.style.height = "30px";
            button.style.fontSize = "0.8rem";

            const isToday = day === realToday.getDate() && month === realToday.getMonth() && year === realToday.getFullYear();
            if (isToday) {
                button.classList.add("btn-primary", "text-white");
            } else {
                button.classList.add("btn-light", "text-dark");
            }
            calendarGrid.appendChild(button);
        }
    }

    renderCalendar(currentMonth, currentYear);

    if (previousButton) {
        previousButton.addEventListener("click", function (event) {
            event.stopPropagation();
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar(currentMonth, currentYear);
        });
    }

    if (nextButton) {
        nextButton.addEventListener("click", function (event) {
            event.stopPropagation();
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            renderCalendar(currentMonth, currentYear);
        });
    }

    if (todayButton) {
        todayButton.addEventListener("click", function (event) {
            event.stopPropagation();
            currentMonth = realToday.getMonth();
            currentYear = realToday.getFullYear();
            renderCalendar(currentMonth, currentYear);
        });
    }

    // Chart Data Reader
    const dataElement = document.getElementById("dashboardChartData");
    let attendanceLabels = [], attendanceData = [];
    let revenueLabels = [], revenueData = [];
    let leadLabels = [], leadData = [];
    let expenseLabels = [], expenseData = [];
    let activePercentage = 77, renewalPercentage = 29, expiredPercentage = 86;

    if (dataElement) {
        try {
            attendanceLabels = JSON.parse(dataElement.dataset.attendanceLabels);
            attendanceData = JSON.parse(dataElement.dataset.attendanceData);
            revenueLabels = JSON.parse(dataElement.dataset.revenueLabels);
            revenueData = JSON.parse(dataElement.dataset.revenueData);
            leadLabels = JSON.parse(dataElement.dataset.leadLabels);
            leadData = JSON.parse(dataElement.dataset.leadData);
            expenseLabels = JSON.parse(dataElement.dataset.expenseLabels);
            expenseData = JSON.parse(dataElement.dataset.expenseData);

            activePercentage = parseInt(dataElement.dataset.activePercentage) || 77;
            renewalPercentage = parseInt(dataElement.dataset.renewalPercentage) || 29;
            expiredPercentage = parseInt(dataElement.dataset.expiredPercentage) || 86;
        } catch (error) {
            console.error("Dashboard chart data error:", error);
        }
    }

    // Circular Chart Function
    function createCircularChart(canvasId, value, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        new Chart(canvas.getContext("2d"), {
            type: "doughnut",
            data: { datasets: [{ data: [value, 100 - value], backgroundColor: [color, "#f1f5f9"], borderWidth: 0 }] },
            options: { responsive: false, cutout: "75%", plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    createCircularChart("circularChart1", activePercentage, "#f59e0b");
    createCircularChart("circularChart2", renewalPercentage, "#3b82f6");
    createCircularChart("circularChart3", expiredPercentage, "#10b981");

    // Bar Chart
    const barCanvas = document.getElementById("barChart");
    if (barCanvas) {
        new Chart(barCanvas.getContext("2d"), {
            type: "bar",
            data: { labels: attendanceLabels, datasets: [{ label: "Attendance", data: attendanceData, backgroundColor: "#059669", borderRadius: 8, borderSkipped: false }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: "#f1f5f9" } }, x: { grid: { display: false } } } }
        });
    }

    // Area Chart
    const areaCanvas = document.getElementById("areaChart");
    if (areaCanvas) {
        new Chart(areaCanvas.getContext("2d"), {
            type: "line",
            data: { labels: revenueLabels, datasets: [{ label: "Revenue", data: revenueData, borderColor: "#f59e0b", backgroundColor: "rgba(245,158,11,0.12)", borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: "#f1f5f9" } }, x: { grid: { display: false } } } }
        });
    }

    // Polar Chart
    const polarCanvas = document.getElementById("polarChart");
    if (polarCanvas) {
        new Chart(polarCanvas.getContext("2d"), {
            type: "polarArea",
            data: { labels: leadLabels, datasets: [{ data: leadData, backgroundColor: ["#3b82f6", "#10b981", "#f59e0b", "#8b5cf6", "#ef4444"], borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "right", labels: { boxWidth: 12, font: { size: 10 } } } } }
        });
    }

    // Expense Chart
    const expenseCanvas = document.getElementById("expenseChart");
    if (expenseCanvas) {
        new Chart(expenseCanvas.getContext("2d"), {
            type: "line",
            data: { labels: expenseLabels, datasets: [{ label: "Expenses", data: expenseData, borderColor: "#ef4444", backgroundColor: "rgba(239,68,68,0.10)", fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: "#f1f5f9" } }, x: { grid: { display: false } } } }
        });
    }

});
</script>

@endsection