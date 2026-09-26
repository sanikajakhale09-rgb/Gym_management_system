@extends('auth.sidebar')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-clipboard-user text-primary me-2"></i>Staff Attendance</h2>
                    <p class="text-muted small mb-0">Mark and manage daily attendance status for active gym staff.</p>
                </div>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary px-4 shadow-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back to Staff List
                </a>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card p-3 shadow-sm border-0 border-start border-primary border-4 rounded-4 bg-white">
                        <span class="text-muted small fw-semibold text-uppercase">Total Active Staff</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">{{ $totalStaffCount }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card p-3 shadow-sm border-0 border-start border-success border-4 rounded-4 bg-white">
                        <span class="text-muted small fw-semibold text-uppercase">Present Today</span>
                        <h3 class="fw-bold mb-0 text-success mt-1">{{ $presentCount }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card p-3 shadow-sm border-0 border-start border-danger border-4 rounded-4 bg-white">
                        <span class="text-muted small fw-semibold text-uppercase">Absent Today</span>
                        <h3 class="fw-bold mb-0 text-danger mt-1">{{ $absentCount }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card p-3 shadow-sm border-0 border-start border-warning border-4 rounded-4 bg-white">
                        <span class="text-muted small fw-semibold text-uppercase">Half Day / Leave</span>
                        <h3 class="fw-bold mb-0 text-warning mt-1">{{ $halfDayCount + $leaveCount }}</h3>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Form Card -->
            <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                    <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i> Select Date & Branch</h5>
                </div>
                <div class="card-body bg-light p-4">
                    <form method="GET" action="{{ route('staff.attendance.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-secondary">Attendance Date</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-control shadow-none">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-secondary">Branch</label>
                                <select name="branch" class="form-select shadow-none">
                                    <option value="">All Branches</option>
                                    <option value="AURA Baner" {{ $branch == 'AURA Baner' ? 'selected' : '' }}>AURA Baner</option>
                                    <option value="Kothrud" {{ $branch == 'Kothrud' ? 'selected' : '' }}>Kothrud</option>
                                    <option value="Wakad" {{ $branch == 'Wakad' ? 'selected' : '' }}>Wakad</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-secondary">Search Staff</label>
                                <input type="text" name="search" value="{{ $search }}" class="form-control shadow-none" placeholder="Name or ID...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn text-white w-100 py-2 shadow-sm fw-semibold" style="background-color: #1e293b; border-color: #1e293b;">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Marking Form & Table -->
            <form action="{{ route('staff.attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="text-uppercase fs-7 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                                    <tr>
                                        <th class="py-3 ps-4 text-white">#</th>
                                        <th class="text-white">Staff ID & Name</th>
                                        <th class="text-white">Role</th>
                                        <th class="text-white">Branch</th>
                                        <th class="text-white">Check-in</th>
                                        <th class="text-white">Check-out</th>
                                        <th class="text-center text-white">Status</th>
                                        <th class="pe-4 text-center text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($staffList as $key => $staff)
                                        @php
                                            $record = $attendanceRecords[$staff->id] ?? null;
                                            $checkInVal = $record ? $record->check_in : '';
                                            $checkOutVal = $record ? $record->check_out : '';
                                            
                                            $hasCheckIn = !empty($checkInVal) && $checkInVal !== '00:00:00' && $checkInVal !== '00:00';
                                            $hasCheckOut = !empty($checkOutVal) && $checkOutVal !== '00:00:00' && $checkOutVal !== '00:00';

                                            // Status Priority: Jar database madhe 'Leave' save asel tr ti override honar nahit
                                            $dbStatus = $record->status ?? '';

                                            if ($dbStatus === 'Leave') {
                                                $currentStatus = 'Leave';
                                            } elseif (!$hasCheckIn && !$hasCheckOut) {
                                                $currentStatus = 'Absent';
                                            } elseif ($hasCheckIn && !$hasCheckOut) {
                                                $currentStatus = 'Working';
                                            } elseif ($hasCheckIn && $hasCheckOut) {
                                                $inTime = strtotime($checkInVal);
                                                $outTime = strtotime($checkOutVal);
                                                if ($outTime <= $inTime) {
                                                    $outTime += 24 * 60 * 60; // Next day night shift support
                                                }
                                                $diffHours = ($outTime - $inTime) / 3600;
                                                if ($diffHours >= 7) {
                                                    $currentStatus = 'Present';
                                                } elseif ($diffHours > 0) {
                                                    $currentStatus = 'Half Day';
                                                } else {
                                                    $currentStatus = 'Absent';
                                                }
                                            } else {
                                                $currentStatus = $dbStatus ?: 'Absent';
                                            }

                                            // Dynamic role badge colors
                                            $roleLower = strtolower($staff->role);
                                            if (str_contains($roleLower, 'trainer')) {
                                                $roleBadgeClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                            } elseif (str_contains($roleLower, 'manager') || str_contains($roleLower, 'admin')) {
                                                $roleBadgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                            } elseif (str_contains($roleLower, 'receptionist') || str_contains($roleLower, 'front')) {
                                                $roleBadgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                            } else {
                                                $roleBadgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-semibold text-secondary">{{ $key + 1 }}</td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $staff->staff_id }}</span>
                                                <div class="fw-semibold text-dark">{{ $staff->name }}</div>
                                            </td>
                                            <td><span class="badge {{ $roleBadgeClass }} px-2.5 py-1.5 fw-semibold">{{ $staff->role }}</span></td>
                                            <td class="text-muted">{{ $staff->branch }}</td>
                                            <td>
                                                <input type="time" name="attendance[{{ $staff->id }}][check_in]" value="{{ $checkInVal }}" class="form-control form-control-sm shadow-none check-in-field" {{ $currentStatus == 'Leave' ? 'disabled' : 'disabled' }}>
                                            </td>
                                            <td>
                                                <input type="time" name="attendance[{{ $staff->id }}][check_out]" value="{{ $checkOutVal }}" class="form-control form-control-sm shadow-none check-out-field" {{ $currentStatus == 'Leave' ? 'disabled' : 'disabled' }}>
                                            </td>
                                            <td class="text-center" style="width: 160px;">
                                                <select name="attendance[{{ $staff->id }}][status]" class="form-select form-select-sm shadow-none fw-semibold status-select" disabled>
                                                    <option value="Present" {{ $currentStatus == 'Present' ? 'selected' : '' }} style="color: #198754; font-weight: bold;">🟢 Present</option>
                                                    <option value="Working" {{ $currentStatus == 'Working' ? 'selected' : '' }} style="color: #0d6efd; font-weight: bold;">🔵 Working</option>
                                                    <option value="Absent" {{ $currentStatus == 'Absent' ? 'selected' : '' }} style="color: #dc3545; font-weight: bold;">🔴 Absent</option>
                                                    <option value="Half Day" {{ $currentStatus == 'Half Day' ? 'selected' : '' }} style="color: #ffc107; font-weight: bold;">🟡 Half Day</option>
                                                    <option value="Leave" {{ $currentStatus == 'Leave' ? 'selected' : '' }} style="color: #0dcaf0; font-weight: bold;">🔵 Leave</option>
                                                </select>
                                            </td>
                                            <td class="pe-4 text-center" style="width: 100px;">
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-row-btn rounded-pill px-3">
                                                    <i class="fas fa-edit me-1"></i> 
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                                <p class="mb-0">No active staff members found for the selected filter.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($staffList->count() > 0)
                        <div class="card-footer bg-white py-3 px-4 d-flex justify-content-end border-0">
                            <button type="submit" class="btn text-white px-5 py-2 fw-semibold shadow-sm rounded-pill" style="background-color: #1e293b; border-color: #1e293b;">
                                <i class="fas fa-save me-1"></i> Save Attendance
                            </button>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </div>
</div>

<!-- JavaScript for Edit Toggle, Auto Status Calculation & Leave Handling -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Edit Button Click Logic (Row Enable/Disable Toggle)
        const editButtons = document.querySelectorAll('.edit-row-btn');
        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                let row = this.closest('tr');
                let checkInInput = row.querySelector('.check-in-field');
                let checkOutInput = row.querySelector('.check-out-field');
                let statusSelect = row.querySelector('.status-select');

                let isDisabled = statusSelect.disabled;

                if (isDisabled) {
                    statusSelect.disabled = false;
                    // Jar status 'Leave' nasel tarach check-in/out enable karayche
                    if (statusSelect.value !== 'Leave') {
                        checkInInput.disabled = false;
                        checkOutInput.disabled = false;
                    }
                    this.innerHTML = '<i class="fas fa-lock-open me-1"></i> Done';
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-success');
                } else {
                    checkInInput.disabled = true;
                    checkOutInput.disabled = true;
                    statusSelect.disabled = true;
                    this.innerHTML = '<i class="fas fa-edit me-1"></i> Edit';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-primary');
                }
            });
        });

        // Status Dropdown Change Logic (Manual Leave selection handling)
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(function (select) {
            select.addEventListener('change', function () {
                let row = this.closest('tr');
                let checkInInput = row.querySelector('.check-in-field');
                let checkOutInput = row.querySelector('.check-out-field');

                if (this.value === 'Leave') {
                    // Leave select keli tar check-in/out clear karun disable karun takne
                    checkInInput.value = '';
                    checkOutInput.value = '';
                    checkInInput.disabled = true;
                    checkOutInput.disabled = true;
                } else {
                    // Itar status asel tar enable thevne (jar edit mode madhe asel tar)
                    if (!this.disabled) {
                        checkInInput.disabled = false;
                        checkOutInput.disabled = false;
                    }
                }
            });
        });

        // Time Input & Status Calculation Logic (Leave asel tar calculation bypass karel)
        const timeInputs = document.querySelectorAll('.check-in-field, .check-out-field');
        timeInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                let row = this.closest('tr');
                let statusSelect = row.querySelector('.status-select');
                
                // Jar status already Leave select asel tar auto-calculation trigger honar nahi
                if (statusSelect && statusSelect.value === 'Leave') {
                    return;
                }

                let checkInInput = row.querySelector('.check-in-field');
                let checkOutInput = row.querySelector('.check-out-field');

                let checkInVal = checkInInput ? checkInInput.value.trim() : "";
                let checkOutVal = checkOutInput ? checkOutInput.value.trim() : "";

                let hasCheckIn = checkInVal !== "" && checkInVal !== "00:00" && checkInVal !== "00:00:00";
                let hasCheckOut = checkOutVal !== "" && checkOutVal !== "00:00" && checkOutVal !== "00:00:00";

                if (!hasCheckIn && !hasCheckOut) {
                    if (statusSelect) {
                        statusSelect.value = "Absent";
                    }
                } 
                else if (hasCheckIn && !hasCheckOut) {
                    if (statusSelect) {
                        statusSelect.value = "Working";
                    }
                } 
                else if (hasCheckIn && hasCheckOut) {
                    let [inHour, inMin] = checkInVal.split(':').map(Number);
                    let [outHour, outMin] = checkOutVal.split(':').map(Number);

                    let inTotalMinutes = (inHour * 60) + inMin;
                    let outTotalMinutes = (outHour * 60) + outMin;

                    if (outTotalMinutes < inTotalMinutes) {
                        outTotalMinutes += 12 * 60;
                    }

                    let diffMinutes = outTotalMinutes - inTotalMinutes;
                    let diffHours = diffMinutes / 60;

                    if (diffHours >= 7) {
                        if (statusSelect) {
                            statusSelect.value = "Present";
                        }
                    } else if (diffHours > 0) {
                        if (statusSelect) {
                            statusSelect.value = "Half Day";
                        }
                    } else {
                        if (statusSelect) {
                            statusSelect.value = "Absent";
                        }
                    }
                }
            });
        });
    });
</script>
@endsection