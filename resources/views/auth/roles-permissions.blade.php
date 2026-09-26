@extends('auth.sidebar')

@section('title', 'Roles & Permissions Management')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Page Header & BRD Stakeholders Info -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-10">
            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-primary border-4 mb-3">
                <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-shield-keyhole text-primary me-2"></i> Roles & Permissions</h4>
                <p class="text-muted small mb-0">Manage access control and page permissions for different gym stakeholders.</p>
            </div>

            <!-- BRD Primary System Roles Reference Box -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-users text-primary me-2"></i> System Stakeholder Roles (BRD Overview)</h6>
                <p class="text-muted small mb-3">The Business Requirements Document (BRD) clearly defines 5 primary system roles with specific access levels:</p>
                <div class="row g-3">
                    <div class="col-md">
                        <div class="p-2 bg-white rounded-3 border text-center">
                            <span class="d-block fw-bold text-dark small">Super Admin</span>
                            <span class="text-muted x-small" style="font-size: 11px;">Full platform access</span>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="p-2 bg-white rounded-3 border text-center">
                            <span class="d-block fw-bold text-dark small">Gym Owner</span>
                            <span class="text-muted x-small" style="font-size: 11px;">Full gym access</span>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="p-2 bg-white rounded-3 border text-center">
                            <span class="d-block fw-bold text-dark small">Manager</span>
                            <span class="text-muted x-small" style="font-size: 11px;">Operational modules</span>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="p-2 bg-white rounded-3 border text-center">
                            <span class="d-block fw-bold text-dark small">Receptionist</span>
                            <span class="text-muted x-small" style="font-size: 11px;">Front desk modules</span>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="p-2 bg-white rounded-3 border text-center">
                            <span class="d-block fw-bold text-dark small">Trainer</span>
                            <span class="text-muted x-small" style="font-size: 11px;">Assigned members/fitness</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Roles Status Overview Header with View/Edit Popup Triggers -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-bar text-primary me-2"></i> Stakeholder Status & Access Summary</h6>
                <div class="row g-3 text-center justify-content-center">
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="d-block small text-muted fw-semibold">Super Admin</span>
                                <span class="badge bg-success mt-2 px-2 py-1 rounded-pill" style="font-size: 10px;">Full Access (Active)</span>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 view-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Super Admin" data-mode="view">View</button>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 edit-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Super Admin" data-mode="edit">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="d-block small text-muted fw-semibold">Gym Owner</span>
                                <span class="badge bg-primary mt-2 px-2 py-1 rounded-pill" style="font-size: 10px;">Custom Access</span>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 view-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Gym Owner" data-mode="view">View</button>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 edit-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Gym Owner" data-mode="edit">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="d-block small text-muted fw-semibold">Manager</span>
                                <span class="badge bg-secondary mt-2 px-2 py-1 rounded-pill" style="font-size: 10px;">Custom Access</span>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 view-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Manager" data-mode="view">View</button>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 edit-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Manager" data-mode="edit">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="d-block small text-muted fw-semibold">Receptionist</span>
                                <span class="badge bg-warning text-dark mt-2 px-2 py-1 rounded-pill" style="font-size: 10px;">Custom Access</span>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 view-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Receptionist" data-mode="view">View</button>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 edit-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Receptionist" data-mode="edit">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="d-block small text-muted fw-semibold">Trainer</span>
                                <span class="badge bg-info mt-2 px-2 py-1 rounded-pill" style="font-size: 10px;">Custom Access</span>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 view-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Trainer" data-mode="view">View</button>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 edit-btn" data-bs-toggle="modal" data-bs-target="#permissionModal" data-role="Trainer" data-mode="edit">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="row justify-content-center mb-3">
            <div class="col-xl-10">
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="row g-4">
                <!-- Left Side: Select Role -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-gradient border-top border-primary border-3">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-gear text-primary me-2"></i> Select Role</h5>
                        <form action="{{ route('roles-permissions.index') }}" method="GET">
                            <div class="mb-3">
                                <label for="roleSelect" class="form-label small fw-semibold text-secondary">Choose Stakeholder</label>
                                <select name="role" id="roleSelect" class="form-select rounded-pill shadow-sm border-0 bg-light py-2" onchange="this.form.submit()">
                                    <option value="">-- Select Role --</option>
                                    <option value="Gym Owner" {{ request('role') == 'Gym Owner' ? 'selected' : '' }}>Gym Owner</option>
                                    <option value="Manager" {{ request('role') == 'Manager' ? 'selected' : '' }}>Gym Manager</option>
                                    <option value="Trainer" {{ request('role') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                                    <option value="Receptionist" {{ request('role') == 'Receptionist' ? 'selected' : '' }}>Receptionist</option>
                                </select>
                            </div>
                        </form>

                        <div class="alert alert-primary bg-opacity-10 border-0 text-primary small rounded-3 mt-3 mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i> Select a role above to view and update its corresponding page permissions.
                        </div>
                    </div>
                </div>

                <!-- Right Side: Permissions Checkboxes Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <form action="{{ route('roles-permissions.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="{{ request('role') }}">

                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark mb-0">Permissions for: <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ request('role', 'None Selected') }}</span></h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" id="selectAll">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="deselectAll">Deselect All</button>
                                </div>
                            </div>

                            @if(request('role'))
                                <div class="row g-3">
                                    <!-- OVERVIEW -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-0 rounded-4 shadow-sm bg-info bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-info text-uppercase small mb-3"><i class="fa-solid fa-chart-pie me-1"></i> Overview</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="dashboard" 
                                                       id="perm_dashboard"
                                                       data-target-sidebar=".sidebar-dashboard"
                                                       {{ in_array('dashboard', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_dashboard">Dashboard</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="gym_profile" id="perm_gym_profile" data-target-sidebar=".sidebar-gym-profile" {{ in_array('gym_profile', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_gym_profile">Gym Profile & Branches</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MANAGEMENT -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-0 rounded-4 shadow-sm bg-warning bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-warning text-dark text-uppercase small mb-3"><i class="fa-solid fa-users-gear me-1"></i> Management</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="members" id="perm_members" data-target-sidebar=".sidebar-members" {{ in_array('members', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_members">Members Management</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="enquiries" id="perm_enquiries" data-target-sidebar=".sidebar-enquiries" {{ in_array('enquiries', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_enquiries">Leads & Enquiries</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="attendance" id="perm_attendance" data-target-sidebar=".sidebar-attendance" {{ in_array('attendance', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_attendance">Attendance</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="trainers_fitness" id="perm_trainers" data-target-sidebar=".sidebar-trainers" {{ in_array('trainers_fitness', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_trainers">Trainers & Fitness</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="staff" id="perm_staff" data-target-sidebar=".sidebar-staff" {{ in_array('staff', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_staff">Staff Management</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OPERATIONS -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-0 rounded-4 shadow-sm bg-success bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-success text-uppercase small mb-3"><i class="fa-solid fa-cogs me-1"></i> Operations</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="memberships" id="perm_memberships" data-target-sidebar=".sidebar-memberships" {{ in_array('memberships', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_memberships">Memberships Plans</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="payments" id="perm_payments" data-target-sidebar=".sidebar-payments" {{ in_array('payments', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_payments">Payments & Invoices</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="expenses" id="perm_expenses" data-target-sidebar=".sidebar-expenses" {{ in_array('expenses', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_expenses">Expenses</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="equipment" id="perm_equipment" data-target-sidebar=".sidebar-equipment" {{ in_array('equipment', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_equipment">Equipment Inventory</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- REPORTS & SYSTEM -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-0 rounded-4 shadow-sm bg-danger bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-danger text-uppercase small mb-3"><i class="fa-solid fa-chart-line me-1"></i> Reports & System</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="reports" id="perm_reports" data-target-sidebar=".sidebar-reports" {{ in_array('reports', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_reports">Reports Section</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="activity_logs" id="perm_logs" data-target-sidebar=".sidebar-activity-logs" {{ in_array('activity_logs', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_logs">Activity Logs</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="sms_whatsapp" id="perm_sms" data-target-sidebar=".sidebar-sms" {{ in_array('sms_whatsapp', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_sms">SMS / WhatsApp</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="notifications" id="perm_notifications" data-target-sidebar=".sidebar-notifications" {{ in_array('notifications', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_notifications">Notifications & Alerts</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="two_factor" id="perm_two_factor" data-target-sidebar=".sidebar-two-factor" {{ in_array('two_factor', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-medium text-dark" for="perm_two_factor">Two-Factor Auth</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ACCOUNT -->
                                    <div class="col-12">
                                        <div class="p-3 border-0 rounded-4 shadow-sm bg-secondary bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="fa-solid fa-user-shield me-1"></i> Account & Settings</h6>
                                            <div class="row">
                                                <div class="col-md-4 form-check mb-2">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="profile" id="perm_profile" data-target-sidebar=".sidebar-profile" {{ in_array('profile', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-medium text-dark" for="perm_profile">Admin Profile</label>
                                                </div>
                                                <div class="col-md-4 form-check mb-2">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="settings" id="perm_settings" data-target-sidebar=".sidebar-settings" {{ in_array('settings', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-medium text-dark" for="perm_settings">Settings</label>
                                                </div>
                                                <div class="col-md-4 form-check mb-2">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="help" id="perm_help" data-target-sidebar=".sidebar-help" {{ in_array('help', $assignedPermissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-medium text-dark" for="perm_help">Help & Documentation</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-semibold shadow-sm">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Permissions
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-hand-pointer fa-3x mb-3 text-primary opacity-50"></i>
                                    <h6 class="fw-bold text-dark">No Role Selected</h6>
                                    <p class="mb-0 small">Please select a role from the left panel to configure permissions.</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Modal for View/Edit Popup -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="editRoleForm" action="{{ route('roles-permissions.update') }}" method="POST">
                @csrf
                <input type="hidden" name="role" id="modalRoleInput">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="permissionModalLabel">Role Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Managing access details for: <strong id="modalRoleName" class="text-primary"></strong></p>
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <span class="d-block small text-dark fw-semibold mb-1">Status: <span class="badge bg-success">Active</span></span>
                        <span class="d-block small text-muted" id="modalModeDescription">You can review or quick-update settings for this stakeholder category.</span>
                    </div>
                    <div class="mb-3" id="modalInputContainer">
                        <label for="roleNameInput" class="form-label small fw-semibold text-secondary">Stakeholder Name</label>
                        <input type="text" class="form-control rounded-pill bg-white" id="roleNameInput" name="role_name_display">
                    </div>
                    
                    <!-- New Assigned Permissions Badges Container -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Assigned Permissions / Access</label>
                        <div id="modalPermissionsList" class="p-2 bg-white border rounded-3 d-flex flex-wrap gap-1">
                            <!-- Badges dynamic yetil -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" id="saveChangesBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Select All / Deselect All & View/Edit Mode Handling -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAllBtn = document.getElementById("selectAll");
        const deselectAllBtn = document.getElementById("deselectAll");
        const checkboxes = document.querySelectorAll(".permission-checkbox");

        if (selectAllBtn) {
            selectAllBtn.addEventListener("click", function () {
                checkboxes.forEach(cb => {
                    cb.checked = true;
                });
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener("click", function () {
                checkboxes.forEach(cb => {
                    cb.checked = false;
                });
            });
        }

        // Modal dynamic role name loader & View/Edit handling
        const permissionModal = document.getElementById('permissionModal');
        if (permissionModal) {
            permissionModal.addEventListener('show.bs.modal', function (event) {
                let button = event.relatedTarget;
                let role = button.getAttribute('data-role');
                let mode = button.getAttribute('data-mode'); // 'view' or 'edit'

                let modalTitle = permissionModal.querySelector('#permissionModalLabel');
                let modalRoleName = permissionModal.querySelector('#modalRoleName');
                let modalRoleInput = permissionModal.querySelector('#modalRoleInput');
                let roleNameInput = permissionModal.querySelector('#roleNameInput');
                let saveChangesBtn = permissionModal.querySelector('#saveChangesBtn');
                let modeDesc = permissionModal.querySelector('#modalModeDescription');
                let permissionsContainer = permissionModal.querySelector('#modalPermissionsList');

                modalRoleName.textContent = role;
                modalRoleInput.value = role;
                roleNameInput.value = role;
                permissionsContainer.innerHTML = '<span class="text-muted small">Loading permissions...</span>';

                // AJAX call to fetch assigned permissions
                $.ajax({
                    url: "/dashboard/system/roles-permissions/get-permissions/" + encodeURIComponent(role),
                    type: "GET",
                    success: function(response) {
                        permissionsContainer.innerHTML = '';
                        if(response.permissions && response.permissions.length > 0) {
                            response.permissions.forEach(function(perm) {
                                permissionsContainer.innerHTML += `<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 me-1 mb-1">${perm}</span>`;
                            });
                        } else {
                            permissionsContainer.innerHTML = '<span class="text-danger small">No access assigned yet.</span>';
                        }
                    },
                    error: function() {
                        permissionsContainer.innerHTML = '<span class="text-muted small">No access permissions configured yet.</span>';
                    }
                });

                if (mode === 'view') {
                    modalTitle.textContent = 'View Role Details';
                    roleNameInput.setAttribute('disabled', 'true');
                    saveChangesBtn.style.display = 'none'; // Hide save button in view mode
                    modeDesc.textContent = 'You are currently viewing details in read-only mode.';
                } else {
                    modalTitle.textContent = 'Edit Role Details';
                    roleNameInput.removeAttribute('disabled');
                    saveChangesBtn.style.display = 'inline-block'; // Show save button in edit mode
                    modeDesc.textContent = 'You can modify settings for this stakeholder category.';
                }
            });
        }

        // AJAX handling for Save Changes inside modal
        $('#editRoleForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            let actionUrl = $(this).attr('action');$.ajax({
                url: actionUrl,
                type: "POST",
                data: formData,
                success: function(response) {
                    $('#permissionModal').modal('hide');
                    alert('Changes saved successfully!');
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred while saving changes. Please try again.');
                }
            });
        });
    });
</script>
@endsection