@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <!-- Centering Main Wrapper using Flex Row -->
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            <!-- Success Alert Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm text-white border-0" role="alert" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-4 align-items-center">
                <div class="col-md-12 text-center text-md-start">
                    <h2 class="mt-4 fw-bold text-dark"><i class="fas fa-snowflake text-info me-2"></i>Freeze Memberships</h2>
                    <p class="text-muted mb-0">View and manage all frozen gym member subscriptions</p>
                </div>
            </div>

            <!-- Search Card -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 bg-white">
                <div class="card-body p-4">
                    <form action="{{ route('membership.freeze.list') }}" method="GET" class="row g-3 align-items-center justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-0 bg-white shadow-none ps-2" placeholder="Search by member name or phone..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm fw-semibold" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">Filter</button>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('membership.freeze.list') }}" class="btn btn-secondary w-100 rounded-pill shadow-sm fw-semibold">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Freeze Members List Table -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2d84 100%);">
                    <h5 class="mb-0 fw-bold text-white"><i class="fas fa-snowflake me-2"></i> Frozen Members List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center text-md-start">
                            <thead class="table-light text-uppercase fs-7 text-secondary">
                                <tr>
                                    <th class="ps-4">Member Name</th>
                                    <th>Plan Name</th>
                                    <th>Freeze Duration</th>
                                    <th>Expiry Date</th>
                                    <th>Freeze Reason</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($frozenSubscriptions ?? [] as $subscription)
                                    @php
                                        /** @var \App\Models\MemberSubscription $subscription */
                                        $member = $subscription->member ?? null;
                                        $memberName = $member ? $member->name : 'Unknown Member';
                                        $memberPhone = $member ? $member->phone : '';
                                        
                                        $plan = $subscription->membershipPlan ?? ($subscription->plan ?? null);
                                        $planName = $plan ? $plan->name : 'N/A';
                                        
                                        $startDateFormatted = $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('d M, Y') : '-';
                                        $endDateFormatted = $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M, Y') : '-';
                                        
                                        // Fetching latest freeze details from freezes table relationship
                                        $latestFreeze = $subscription->freezes()->latest()->first();
                                        
                                        // Carbon calculation directly in Blade for accuracy
                                        $fromDate = ($latestFreeze && $latestFreeze->freeze_from) ? \Carbon\Carbon::parse($latestFreeze->freeze_from) : null;
                                        $toDate = ($latestFreeze && $latestFreeze->freeze_to) ? \Carbon\Carbon::parse($latestFreeze->freeze_to) : null;
                                        $freezeDays = ($fromDate && $toDate) ? $fromDate->diffInDays($toDate) + 1 : ($latestFreeze ? $latestFreeze->freeze_days : 0);

                                        $freezeFromFormatted = $fromDate ? $fromDate->format('d M, Y') : '-';
                                        $freezeToFormatted = $toDate ? $toDate->format('d M, Y') : '-';

                                        $approvalRef = $latestFreeze ? $latestFreeze->approval_reference : 'N/A';
                                        $remarks = $latestFreeze ? $latestFreeze->remarks : 'N/A';
                                        
                                        $freezeReason = $latestFreeze->reason ?? ($subscription->freeze_reason ?? ($subscription->reason ?? 'N/A'));
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark">{{ $memberName }}</strong>
                                            <br><small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i>{{ $memberPhone }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $planName }}</span></td>
                                        <td>
                                            <small class="fw-bold text-dark">From:</small> {{ $freezeFromFormatted }}<br>
                                            <small class="fw-bold text-dark">To:</small> {{ $freezeToFormatted }}<br>
                                            <span class="badge bg-info text-dark mt-1">{{ $freezeDays }} Days</span>
                                        </td>
                                        <td>{{ $endDateFormatted }} <br><small class="text-success">(Extended)</small></td>
                                        <td><span class="text-muted fst-italic">{{ $freezeReason }}</span></td>
                                        <td>
                                            <span class="badge fw-bold px-3 py-2 rounded-pill" style="background-color: #f3e8ff !important; color: #6f42c1 !important;">Frozen</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <!-- View Details Modal Trigger Button -->
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $subscription->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Edit Freeze Modal Trigger Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $subscription->id }}" title="Edit Freeze">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Unfreeze Action Form -->
                                                <form action="{{ route('membership.unfreeze', $subscription->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to unfreeze this membership?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" title="Unfreeze Membership">
                                                        <i class="fas fa-play me-1"></i> Unfreeze
                                                    </button>
                                                </form>

                                                <!-- Delete Action Form -->
                                                <form action="{{ route('membership.freeze.destroy', $latestFreeze ? $latestFreeze->id : $subscription->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this freeze record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="Delete Freeze">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- 1. View Modal for each subscription -->
                                            <div class="modal fade text-start" id="viewModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $subscription->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 shadow">
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-dark" id="viewModalLabel{{ $subscription->id }}">
                                                                <i class="fas fa-info-circle me-2" style="color: #6f42c1;"></i> Freeze Details (#{{ $subscription->id }})
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Member Name</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $memberName }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Phone</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $memberPhone ?: '-' }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Plan Name</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $planName }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Status</label>
                                                                    <p class="mb-2">
                                                                        <span class="badge fw-bold px-3 py-1 rounded-pill" style="background-color: #f3e8ff !important; color: #6f42c1 !important;">
                                                                            Frozen
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Freeze From</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $freezeFromFormatted }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Freeze To</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $freezeToFormatted }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Total Freeze Days</label>
                                                                    <p class="fw-bold text-info mb-2">{{ $freezeDays }} Days</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">New Extended Expiry Date</label>
                                                                    <p class="fw-bold text-success mb-2">{{ $endDateFormatted }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Approval Reference</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $approvalRef }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Remarks</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $remarks }}</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="text-muted small fw-semibold">Freeze Reason</label>
                                                                    <p class="fw-bold text-danger mb-2">{{ $freezeReason }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Edit Modal for each subscription -->
                                            <div class="modal fade text-start" id="editModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $subscription->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 shadow">
                                                        <form action="{{ route('membership.freeze.update', $latestFreeze ? $latestFreeze->id : $subscription->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-dark" id="editModalLabel{{ $subscription->id }}">
                                                                    <i class="fas fa-edit me-2" style="color: #4e73df;"></i> Edit Freeze Details (#{{ $subscription->id }})
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-semibold">Freeze From</label>
                                                                        <input type="date" name="freeze_from" class="form-control" value="{{ $fromDate ? $fromDate->format('Y-m-d') : '' }}" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small fw-semibold">Freeze To</label>
                                                                        <input type="date" name="freeze_to" class="form-control" value="{{ $toDate ? $toDate->format('Y-m-d') : '' }}" required>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label small fw-semibold">Freeze Reason</label>
                                                                        <textarea name="reason" class="form-control" rows="3" required>{{ $freezeReason !== 'N/A' ? $freezeReason : '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                            No frozen memberships found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection