@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Staff Management</h2>
            <p class="text-muted small mb-0">Manage gym staff, roles, branches, and statuses smoothly.</p>
        </div>
        <a href="{{ route('staff.create') }}" class="btn btn-primary px-4 shadow-sm">+ Add Staff</a>
    </div>

    <!-- Success Alert Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg me-2 text-success"></i>
                <div><strong>{{ session('success') }}</strong></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 border-start border-primary border-4 rounded-3">
                <h6 class="text-muted mb-1 font-monospace">Total Staff</h6>
                <h3 class="fw-bold mb-0">{{ $totalStaff }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 border-start border-success border-4 rounded-3">
                <h6 class="text-muted mb-1 font-monospace">Active Staff</h6>
                <h3 class="text-success fw-bold mb-0">{{ $activeStaff }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 border-start border-danger border-4 rounded-3">
                <h6 class="text-muted mb-1 font-monospace">Inactive Staff</h6>
                <h3 class="text-danger fw-bold mb-0">{{ $inactiveStaff }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 border-start border-info border-4 rounded-3">
                <h6 class="text-muted mb-1 font-monospace">Trainers</h6>
                <h3 class="text-primary fw-bold mb-0">{{ $totalTrainers }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filters Form -->
    <div class="card shadow-lg border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient bg-dark text-white py-3 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
            <h5 class="mb-0 text-white"><i class="fas fa-filter me-2"></i> Filter & Search Staff</h5>
            
        </div>
        <div class="card-body p-4 bg-light">
            <form method="GET" action="{{ route('staff.index') }}">
                <div class="row g-3 justify-content-center align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-secondary">Search Keyword</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm shadow-none" placeholder="Name, Mobile, ID...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Role</label>
                        <select name="role" class="form-select form-select-sm shadow-none">
                            <option value="">All Roles</option>
                            <option value="Manager" {{ request('role') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Receptionist" {{ request('role') == 'Receptionist' ? 'selected' : '' }}>Receptionist</option>
                            <option value="Trainer" {{ request('role') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                            <option value="Accountant" {{ request('role') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="Cleaning Staff" {{ request('role') == 'Cleaning Staff' ? 'selected' : '' }}>Cleaning Staff</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Branch</label>
                        <select name="branch" class="form-select form-select-sm shadow-none">
                            <option value="">All Branches</option>
                            <option value="AURA Baner" {{ request('branch') == 'AURA Baner' ? 'selected' : '' }}>AURA Baner</option>
                            <option value="Kothrud" {{ request('branch') == 'Kothrud' ? 'selected' : '' }}>Kothrud</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-secondary">Status</label>
                        <select name="status" class="form-select form-select-sm shadow-none">
                            <option value="">All Status</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2 mt-4">
                        <button type="submit" class="btn btn-dark btn-sm w-100 py-2 fw-semibold shadow-sm" style="background-color: #1e293b; border-color: #1e293b;">
                            <i class="fas fa-search me-1"></i> Apply Filter
                        </button>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 fw-semibold">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="py-3 ps-3">#</th>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Role</th>
                            <th>Mobile Number</th>
                            <th>Branch</th>
                            <th>Salary</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                            <th class="pe-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffList as $key => $staff)
                        <tr>
                            <td class="ps-3">{{ $staffList->firstItem() + $key }}</td>
                            <td><span class="fw-bold text-primary">{{ $staff->staff_id }}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $staff->name }}</div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-75">{{ $staff->role }}</span></td>
                            <td>{{ $staff->mobile }}</td>
                            <td>{{ $staff->branch }}</td>
                            <td class="fw-semibold text-success">₹{{ number_format($staff->salary, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($staff->joining_date)->format('d M Y') }}</td>
                            <td>
                                @if($staff->status == 'Active')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Button (Triggers Modal) -->
                                    <button type="button" class="btn btn-sm btn-info text-white shadow-sm view-btn" data-id="{{ $staff->id }}" title="View"><i class="fas fa-eye"></i></button>
                                    
                                    <!-- Edit Button (Triggers Modal) -->
                                    <button type="button" class="btn btn-sm btn-warning text-white shadow-sm edit-btn" data-id="{{ $staff->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger shadow-sm delete-btn" data-id="{{ $staff->id }}" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-secondary mb-3 opacity-50"></i>
                                    <p class="mb-0">No staff records found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-0">
            {{ $staffList->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- ================= MODALS SECTION ================= -->

<!-- 1. View Modal -->
<div class="modal fade" id="viewStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <h5 class="modal-title"><i class="fas fa-user-circle me-2 text-info"></i>Staff Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><strong>Staff ID:</strong> <span id="view-staff_id"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Name:</strong> <span id="view-name"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Role:</strong> <span id="view-role"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Mobile:</strong> <span id="view-mobile"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Branch:</strong> <span id="view-branch"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Salary:</strong> <span id="view-salary"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Joining Date:</strong> <span id="view-joining_date"></span></li>
                    <li class="list-group-item d-flex justify-content-between"><strong>Status:</strong> <span id="view-status"></span></li>
                </ul>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Edit Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="editStaffForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2 text-info"></i>Edit Staff Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="role" id="edit-role" class="form-select" required>
                            <option value="Manager">Manager</option>
                            <option value="Receptionist">Receptionist</option>
                            <option value="Trainer">Trainer</option>
                            <option value="Accountant">Accountant</option>
                            <option value="Cleaning Staff">Cleaning Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mobile Number</label>
                        <input type="text" name="mobile" id="edit-mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Branch</label>
                        <select name="branch" id="edit-branch" class="form-select" required>
                            <option value="AURA Baner">AURA Baner</option>
                            <option value="Kothrud">Kothrud</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Salary</label>
                        <input type="number" step="0.01" name="salary" id="edit-salary" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit-status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-semibold btn-sm" style="background-color: #1e293b; border-color: #1e293b;">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Delete Form (Hidden Form for submission) -->
<form id="deleteStaffForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- jQuery & AJAX Scripts directly loaded here -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // View Button Click
        $('.view-btn').click(function () {
            let id = $(this).data('id');
            $.get('/staff/' + id + '/json', function (data) {
                $('#view-staff_id').text(data.staff_id);
                $('#view-name').text(data.name);
                $('#view-role').text(data.role);
                $('#view-mobile').text(data.mobile);
                $('#view-branch').text(data.branch);
                $('#view-salary').text('₹' + parseFloat(data.salary).toFixed(2));
                $('#view-joining_date').text(data.joining_date);
                $('#view-status').text(data.status);
                $('#viewStaffModal').modal('show');
            });
        });

        // Edit Button Click
        $('.edit-btn').click(function () {
            let id = $(this).data('id');
            $.get('/staff/' + id + '/json', function (data) {
                $('#edit-name').val(data.name);
                $('#edit-role').val(data.role);
                $('#edit-mobile').val(data.mobile);
                $('#edit-branch').val(data.branch);
                $('#edit-salary').val(data.salary);
                $('#edit-status').val(data.status);
                
                $('#editStaffForm').attr('action', '/staff/' + id);
                $('#editStaffModal').modal('show');
            });
        });

        // Delete Button Click with Confirmation
        $('.delete-btn').click(function () {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this staff record?')) {
                let form = $('#deleteStaffForm');
                form.attr('action', '/staff/' + id);
                form.submit();
            }
        });
    });
</script>

@endsection