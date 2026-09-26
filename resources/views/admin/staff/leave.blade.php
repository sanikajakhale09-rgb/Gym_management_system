@extends('auth.sidebar')

@section('content')
<div class="container py-4 bg-light min-vh-100">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #1e3a8a;">
                        <i class="fas fa-calendar-alt me-2" style="color: #1e3a8a;"></i>Staff Leave Management
                    </h2>
                    <p class="text-muted mb-0">Manage and track staff leave requests easily and efficiently.</p>
                </div>
                <button class="btn shadow-sm px-4 py-2 rounded-pill fw-semibold text-white" style="background-color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                    <i class="fas fa-plus me-2"></i> Add Leave Request
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-lg me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filters Section -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('staff.leave.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Search Staff</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Name or Staff ID..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Filter Status</label>
                            <select name="status" class="form-select bg-light">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Filter Branch</label>
                            <input type="text" name="branch" class="form-control bg-light" placeholder="Branch name..." value="{{ request('branch') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="w-100 mt-1">
                                <button type="submit" class="btn w-100 shadow-sm py-2 fw-semibold rounded-3 text-white" style="background-color: #1e3a8a;">
                                    <i class="fas fa-filter me-1"></i> Apply Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leave Requests Table Card -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #1e3a8a;"><i class="fas fa-list me-2" style="color: #1e3a8a;"></i>Leave Requests List</h5>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th class="py-3 px-4">Staff Name</th>
                                <th class="py-3">Role & Branch</th>
                                <th class="py-3">Leave Type</th>
                                <th class="py-3">Date Range</th>
                                <th class="py-3">Reason</th>
                                <th class="py-3">Status</th>
                                <th class="text-end py-3 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $leave->staff->name ?? 'N/A' }}</div>
                                    <small class="text-muted font-monospace bg-light px-1 rounded">{{ $leave->staff->staff_id ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-secondary">{{ $leave->staff->role ?? 'N/A' }}</div>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $leave->staff->branch ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ $leave->leave_type }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</span> <br>
                                    <small class="text-muted">to {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 150px;" title="{{ $leave->reason }}">{{ $leave->reason }}</span>
                                </td>
                                <td>
                                    @if($leave->status == 'Pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold shadow-xs">Pending</span>
                                    @elseif($leave->status == 'Approved')
                                        <span class="badge bg-success px-3 py-2 rounded-pill fw-semibold shadow-xs">Approved</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill fw-semibold shadow-xs">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm rounded-circle p-2 me-1" title="View Details" data-bs-toggle="modal" data-bs-target="#viewLeaveModal{{ $leave->id }}">
                                        <i class="fas fa-eye fa-fw"></i>
                                    </button>

                                    @if($leave->status == 'Pending')
                                        <!-- Approve Button -->
                                        <form action="{{ route('staff.leave.updateStatus', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm rounded-circle p-2 me-1" title="Approve">
                                                <i class="fas fa-check fa-fw"></i>
                                            </button>
                                        </form>

                                        <!-- Reject Button (Triggers Reject Modal) -->
                                        <button type="button" class="btn btn-sm btn-danger shadow-sm rounded-circle p-2 me-1" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectLeaveModal{{ $leave->id }}">
                                            <i class="fas fa-times fa-fw"></i>
                                        </button>
                                    @endif

                                    <form action="{{ route('staff.leave.destroy', $leave->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm rounded-circle p-2" title="Delete">
                                            <i class="fas fa-trash fa-fw"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- View Leave Details Modal -->
                            <div class="modal fade" id="viewLeaveModal{{ $leave->id }}" tabindex="-1" aria-labelledby="viewLeaveModalLabel{{ $leave->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header text-white py-3 px-4" style="background-color: #1e3a8a;">
                                            <h5 class="modal-title fw-bold" id="viewLeaveModalLabel{{ $leave->id }}">
                                                <i class="fas fa-info-circle me-2 text-white-50"></i>Leave Request Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-0">
                                                <div class="mb-3 border-bottom pb-2">
                                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Staff Information</small>
                                                    <h6 class="fw-bold text-dark mb-1">{{ $leave->staff->name ?? 'N/A' }} <span class="text-muted font-monospace small">({{ $leave->staff->staff_id ?? 'N/A' }})</span></h6>
                                                    <p class="text-muted small mb-0">{{ $leave->staff->role ?? 'N/A' }} | <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $leave->staff->branch ?? 'N/A' }}</p>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Leave Type</small>
                                                        <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">{{ $leave->leave_type }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Status</small>
                                                        @if($leave->status == 'Pending')
                                                            <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                                                        @elseif($leave->status == 'Approved')
                                                            <span class="badge bg-success px-2 py-1">Approved</span>
                                                        @else
                                                            <span class="badge bg-danger px-2 py-1">Rejected</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Start Date</small>
                                                        <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted text-uppercase fw-bold d-block mb-1">End Date</small>
                                                        <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Reason</small>
                                                    <p class="text-dark bg-light p-3 rounded-3 mb-0 small">{{ $leave->reason }}</p>
                                                </div>
                                                @if($leave->status == 'Rejected' && !empty($leave->rejection_reason))
                                                    <div class="mb-0">
                                                        <small class="text-danger text-uppercase fw-bold d-block mb-1">Rejection Reason</small>
                                                        <p class="text-danger bg-light p-3 rounded-3 mb-0 small">{{ $leave->rejection_reason }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white border-top py-3 px-4">
                                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Leave Modal with Reason Field -->
                            <div class="modal fade" id="rejectLeaveModal{{ $leave->id }}" tabindex="-1" aria-labelledby="rejectLeaveModalLabel{{ $leave->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <form action="{{ route('staff.leave.updateStatus', $leave->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Rejected">
                                            
                                            <div class="modal-header bg-danger text-white py-3 px-4">
                                                <h5 class="modal-title fw-bold" id="rejectLeaveModalLabel{{ $leave->id }}">
                                                    <i class="fas fa-times-circle me-2 text-white-50"></i>Reject Leave Request
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 bg-light">
                                                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-0">
                                                    <p class="text-muted small mb-3">You are rejecting the leave request for <strong>{{ $leave->staff->name ?? 'Staff' }}</strong>. Please provide a reason below:</p>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-bold small text-secondary">Rejection Reason *</label>
                                                        <textarea name="rejection_reason" rows="3" class="form-control bg-light" placeholder="e.g. Staff shortage during requested dates..." required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-white border-top py-3 px-4">
                                                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm fw-semibold">Confirm Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-times fa-3x text-secondary opacity-50"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark">No leave requests found</h6>
                                    <p class="text-muted small mb-0">Try changing your filters or add a new request.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($leaves->hasPages())
                    <div class="card-footer bg-white py-3 px-4 border-top">
                        {{ $leaves->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Add Leave Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('staff.leave.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white py-3 px-4" style="background-color: #1e3a8a;">
                    <h5 class="modal-title fw-bold" id="addLeaveModalLabel">
                        <i class="fas fa-plus-circle me-2 text-white-50"></i>Add Leave Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-0 bg-white">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Select Staff *</label>
                            <select name="staff_id" class="form-select bg-light" required>
                                <option value="">-- Choose Staff --</option>
                                @foreach($staffList as $stf)
                                    <option value="{{ $stf->id }}">{{ $stf->name }} ({{ $stf->staff_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Leave Type *</label>
                            <select name="leave_type" class="form-select bg-light" required>
                                <option value="Casual Leave">Casual Leave</option>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Paid Leave">Paid Leave</option>
                                <option value="Unpaid Leave">Unpaid Leave</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Start Date *</label>
                                <input type="date" name="start_date" class="form-control bg-light" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">End Date *</label>
                                <input type="date" name="end_date" class="form-control bg-light" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-secondary">Reason *</label>
                            <textarea name="reason" rows="3" class="form-control bg-light" placeholder="Enter reason here..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn px-4 rounded-pill shadow-sm fw-semibold text-white" style="background-color: #1e3a8a;">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection