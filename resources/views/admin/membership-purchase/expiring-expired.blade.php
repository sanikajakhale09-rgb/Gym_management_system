@extends('auth.sidebar')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10 text-center">
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Expiring / Expired Memberships
            </h2>
            <p class="text-muted">View and manage memberships expiring today, within 3 days, 7 days, or already expired efficiently.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Expiring / Expired Tabs Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                <a href="{{ route('membership.expiring-expired', array_merge(request()->all(), ['tab' => 'expired'])) }}" class="btn {{ request('tab') == 'expired' ? 'btn-danger shadow' : 'btn-outline-danger' }} px-3 py-2 rounded-pill fw-semibold">
                    🔴 Expired
                </a>
                <a href="{{ route('membership.expiring-expired', array_merge(request()->all(), ['tab' => 'today'])) }}" class="btn {{ request('tab') == 'today' ? 'btn-warning text-dark shadow' : 'btn-outline-warning' }} px-3 py-2 rounded-pill fw-semibold">
                    🟠 Expiring Today
                </a>
                <a href="{{ route('membership.expiring-expired', array_merge(request()->all(), ['tab' => '3days'])) }}" class="btn {{ request('tab') == '3days' ? 'btn-info text-white shadow' : 'btn-outline-info' }} px-3 py-2 rounded-pill fw-semibold">
                    🟡 Within 3 Days
                </a>
                <a href="{{ route('membership.expiring-expired', array_merge(request()->all(), ['tab' => '7days'])) }}" class="btn {{ request('tab') == '7days' ? 'btn-success shadow' : 'btn-outline-success' }} px-3 py-2 rounded-pill fw-semibold">
                    🟢 Within 7 Days
                </a>
                <a href="{{ route('membership.expiring-expired') }}" class="btn btn-secondary px-3 py-2 rounded-pill fw-semibold shadow-sm">
                    🔄 Reset Filters
                </a>
            </div>

            <!-- Filters Section -->
            <div class="card border-0 shadow-lg mb-4 rounded-4 bg-white">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('membership.expiring-expired') }}" class="row g-3 align-items-center">
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                        
                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Search Member</label>
                            <input type="text" name="search" class="form-control rounded-3" placeholder="Name or phone..." value="{{ request('search') }}">
                        </div>

                        <!-- Branch Filter -->
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Branch</label>
                            <select name="branch_id" class="form-select rounded-3">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Trainer Filter -->
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Trainer</label>
                            <select name="trainer_id" class="form-select rounded-3">
                                <option value="">All Trainers</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ request('trainer_id') == $trainer->id ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Plan Filter -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Plan</label>
                            <select name="plan_name" class="form-select rounded-3">
                                <option value="">All Plans</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ request('plan_name') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <div class="col-md-1 d-flex align-items-end mt-4">
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 shadow-sm" title="Apply Filter">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Members Table Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient bg-dark text-white py-3 px-4">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-list-alt me-2"></i> Membership Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light text-uppercase fs-7">
                                <tr>
                                    <th class="py-3">Member ID</th>
                                    <th class="py-3 text-start">Member Name</th>
                                    <th class="py-3">Plan</th>
                                    <th class="py-3">Branch</th>
                                    <th class="py-3">Trainer</th>
                                    <th class="py-3">Start Date</th>
                                    <th class="py-3">Expiry Date</th>
                                    <th class="py-3">Days Left</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $subscription)
                                    @php
                                        $expiry = \Carbon\Carbon::parse($subscription->end_date);
                                        $daysLeft = \Carbon\Carbon::today()->diffInDays($expiry, false);
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-secondary">GYM-2026-{{ str_pad($subscription->member->id ?? 0, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td class="text-start">
                                            <div class="fw-bold text-dark">{{ $subscription->member->name ?? 'N/A' }}</div>
                                            <small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i>{{ $subscription->member->phone ?? '' }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $subscription->membershipPlan->name ?? 'N/A' }}</span></td>
                                        <td>{{ $subscription->member->branch->name ?? 'Main Branch' }}</td>
                                        <td>{{ $subscription->member->trainer->name ?? 'Not Assigned' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</td>
                                        <td class="fw-semibold">{{ $expiry->format('d M Y') }}</td>
                                        <td>
                                            @if($daysLeft < 0)
                                                <span class="text-danger fw-bold">{{ $daysLeft }} Days</span>
                                            @elseif($daysLeft == 0)
                                                <span class="text-warning fw-bold">Today</span>
                                            @else
                                                <span class="text-success fw-bold">+{{ $daysLeft }} Days</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($daysLeft < 0)
                                                <span class="badge bg-danger px-2 py-1">Expired</span>
                                            @elseif($daysLeft == 0)
                                                <span class="badge bg-warning text-dark px-2 py-1">Expiring Today</span>
                                            @else
                                                <span class="badge bg-info text-dark px-2 py-1">Expiring Soon</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('membership.renew', $subscription->id) }}" class="btn btn-sm btn-success px-3 rounded-pill shadow-sm">
                                                <i class="fas fa-sync-alt me-1"></i> Renew
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-5">
                                            <div class="py-4">
                                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                <p class="fs-5 mb-0">No expiring or expired memberships found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Footer -->
                @if($members->hasPages())
                    <div class="card-footer bg-white py-3">
                        <div class="d-flex justify-content-center">
                            {{ $members->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection