@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <i class="fas fa-calendar-check text-primary me-2"></i> Today's Attendance
            </h2>
            <p class="text-muted mb-0">Live attendance tracking for <span class="badge bg-primary text-white">{{ \Carbon\Carbon::now('Asia/Kolkata')->format('d M, Y') }}</span></p>
        </div>
    </div>

    <!-- Center Aligned Filter Box -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <form method="GET" action="{{ route('attendance.today') }}" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill ps-3 text-secondary">
                            <i class="fas fa-filter"></i>
                        </span>
                        <select name="branch_id" class="form-select border-0 bg-light shadow-none" onchange="this.form.submit()">
                            <option value="">-- All Branches --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request('branch_id'))
                        <a href="{{ route('attendance.today') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Centered & Attractive Attendance Table Card -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <h5 class="mb-0 fw-semibold text-white"><i class="fas fa-list-alt me-2"></i> Attendance Records</h5>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill shadow-sm fw-bold">
                        Total Records: {{ count($attendances) }}
                    </span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light text-uppercase fs-7 text-secondary fw-bold" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-3 align-middle">Sr No.</th>
                                    <th class="py-3 align-middle text-start">Member Name</th>
                                    <th class="py-3 align-middle">Member Code</th>
                                    <th class="py-3 align-middle text-start">Branch</th>
                                    <th class="py-3 align-middle">Check-in Time</th>
                                    <th class="py-3 align-middle">Check-out Time</th>
                                    <th class="py-3 align-middle">Status</th>
                                    <th class="py-3 align-middle">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $index => $attendance)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start fw-semibold text-dark">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light-primary text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; background: #e8f0fe;">
                                                    {{ substr($attendance->member->name ?? 'M', 0, 1) }}
                                                </div>
                                                {{ $attendance->member->name ?? 'Unknown Member' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                                {{ $attendance->member->member_code ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-start text-muted">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $attendance->branch->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                <i class="far fa-clock me-1"></i> 
                                                {{ $attendance->check_in_time ? date('h:i:s A', strtotime($attendance->check_in_time)) : '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-secondary">
                                                <i class="far fa-clock me-1"></i> 
                                                {{ $attendance->check_out_time ? date('h:i:s A', strtotime($attendance->check_out_time)) : '-- : --' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($attendance->check_out_time)
                                                <span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold">
                                                    <i class="fas fa-check-circle me-1"></i> Checked-Out
                                                </span>
                                            @else
                                                <span class="badge bg-info bg-opacity-15 text-info px-3 py-2 rounded-pill fw-bold" style="background-color: #cff4fc !important; color: #055160 !important;">
                                                    <i class="fas fa-user-clock me-1"></i> Checked-In
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- Check-out Action Button -->
                                                @if(!$attendance->check_out_time)
                                                    <form action="{{ route('attendance.checkout', $attendance->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 shadow-sm fw-bold text-nowrap" title="Check Out">
                                                            <i class="fas fa-sign-out-alt me-1"></i> Check Out
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-light text-success border px-2 py-1 rounded-pill fw-semibold">
                                                        <i class="fas fa-check-circle text-success me-1"></i> Checked Out
                                                    </span>
                                                @endif

                                                <!-- 1. VIEW BUTTON -->
                                                <button type="button" class="btn btn-sm btn-light text-info rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#viewModal{{ $attendance->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- 2. EDIT BUTTON -->
                                                <button type="button" class="btn btn-sm btn-light text-warning rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" data-bs-toggle="modal" data-bs-target="#editModal{{ $attendance->id }}" title="Edit Attendance">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- 3. DELETE BUTTON -->
                                                <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- VIEW MODAL FOR EACH ROW -->
                                    <div class="modal fade" id="viewModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header bg-primary text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i> Attendance Details</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <p><strong>Member Name:</strong> {{ $attendance->member->name ?? 'N/A' }}</p>
                                                    <p><strong>Member Code:</strong> {{ $attendance->member->member_code ?? 'N/A' }}</p>
                                                    <p><strong>Branch:</strong> {{ $attendance->branch->name ?? 'N/A' }}</p>
                                                    <p><strong>Check-in Time:</strong> {{ $attendance->check_in_time }}</p>
                                                    <p><strong>Check-out Time:</strong> {{ $attendance->check_out_time ?? 'Not Checked Out Yet' }}</p>
                                                    <p><strong>Status:</strong> {{ $attendance->check_out_time ? 'Checked-Out' : 'Checked-In' }}</p>
                                                    <p><strong>Date:</strong> {{ $attendance->created_at->format('d M, Y') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EDIT MODAL FOR EACH ROW -->
                                    <div class="modal fade" id="editModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-dark rounded-top-4">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Attendance</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Member Name</label>
                                                            <input type="text" class="form-control" value="{{ $attendance->member->name ?? 'N/A' }}" disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Check-in Time</label>
                                                            <input type="time" name="check_in_time" class="form-control" value="{{ $attendance->check_in_time ? date('H:i', strtotime($attendance->check_in_time)) : '' }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Check-out Time</label>
                                                            <input type="time" name="check_out_time" class="form-control" value="{{ $attendance->check_out_time ? date('H:i', strtotime($attendance->check_out_time)) : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                                <p class="fs-5 mb-0">No attendance records found for today.</p>
                                            </div>
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