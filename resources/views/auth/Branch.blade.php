@extends('auth.sidebar')

@section('content')
<style>
    body { background-color: #f3f5f7 !important; }
    .container-fluid { background-color: #f3f5f7; }
    .card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08); background-color: #ffffff; }
    .branch-header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #274c77 100%) !important; padding: 18px 22px; }
    .branch-header h5 { font-weight: 700; letter-spacing: 0.2px; }
    .add-branch-btn { background-color: #ffffff !important; color: #17324d !important; border: none !important; border-radius: 8px; padding: 8px 15px; transition: all 0.2s ease; }
    .add-branch-btn:hover { background-color: #e9eef3 !important; color: #0f172a !important; transform: translateY(-1px); }
    .table { margin-bottom: 0; }
    .table thead th { background-color: #172b4d !important; color: #ffffff !important; border-color: #263b5a !important; font-weight: 600; padding: 13px 12px; }
    .table tbody td { padding: 13px 12px; vertical-align: middle; color: #334155; }
    .table tbody tr:hover { background-color: #f5f7fa; }
    .branch-name { color: #17324d; font-weight: 700; }
    .status-badge { background-color: #198754; color: #ffffff; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-inactive { background-color: #dc3545; color: #ffffff; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .btn-view { background-color: #3b82a0; border-color: #3b82a0; color: #ffffff; }
    .btn-view:hover { background-color: #2f6f8a; border-color: #2f6f8a; color: #ffffff; }
    .btn-edit { background-color: #d39e00; border-color: #d39e00; color: #ffffff; }
    .btn-edit:hover { background-color: #b88600; border-color: #b88600; color: #ffffff; }
    .btn-delete { background-color: #dc3545; border-color: #dc3545; color: #ffffff; }
    .btn-delete:hover { background-color: #bb2d3b; border-color: #bb2d3b; color: #ffffff; }
    .modal-content { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 15px 40px rgba(15, 23, 42, 0.18); }
    .modal-header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #274c77 100%) !important; border-bottom: none; padding: 17px 20px; }
    .modal-title { font-weight: 700; }
    .modal-body { background-color: #f3f5f7; padding: 25px; max-height: 75vh; overflow-y: auto; }
    .modal-footer { background-color: #f3f5f7; border-top: 1px solid #e2e8f0; }
    .form-label { color: #334155; font-weight: 600; margin-bottom: 7px; }
    .form-control, .form-select { background-color: #ffffff; border: 1px solid #d7dee7; border-radius: 8px; padding: 10px 12px; color: #334155; }
    .form-control:focus, .form-select:focus { border-color: #274c77; box-shadow: 0 0 0 0.2rem rgba(39, 76, 119, 0.12); }
    textarea.form-control { resize: vertical; }
    .save-branch-btn { background-color: #17324d !important; border-color: #17324d !important; color: #ffffff !important; border-radius: 8px; padding: 9px 18px; font-weight: 600; }
    .save-branch-btn:hover { background-color: #0f263c !important; border-color: #0f263c !important; color: #ffffff !important; }
    .close-modal-btn { background-color: #64748b; border-color: #64748b; color: #ffffff; border-radius: 8px; padding: 9px 18px; }
    .close-modal-btn:hover { background-color: #475569; border-color: #475569; color: #ffffff; }
    .empty-state { padding: 35px 20px !important; color: #64748b; background-color: #fafbfc; }
    .detail-label { color: #64748b; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
    .detail-value { color: #1e293b; font-weight: 600; font-size: 15px; }
    .section-title { font-size: 15px; font-weight: 700; color: #1e3a5f; border-bottom: 2px solid #cbd5e1; padding-bottom: 5px; margin-top: 15px; margin-bottom: 15px; }
</style>

<div class="container-fluid px-4">
    <div class="row my-4">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header branch-header d-flex justify-content-between align-items-center text-white">
                    <h5 class="mb-0"><i class="fas fa-code-branch me-2"></i> Gym Branches Management</h5>
                    <button type="button" class="btn btn-light btn-sm fw-bold add-branch-btn" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                        <i class="fas fa-plus me-1"></i> Add New Branch
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Please check the form data (invalid mobile number or missing required fields).
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Branch Name</th>
                                    <th>Branch Code</th>
                                    <th>Manager Name</th>
                                    <th>Location / City</th>
                                    <th>Primary Contact</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($branches)
                                    @forelse($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->id }}</td>
                                            <td class="branch-name">{{ $branch->name }}</td>
                                            <td><span class="badge bg-secondary">{{ $branch->branch_code ?? 'N/A' }}</span></td>
                                            <td>{{ $branch->manager_name ?? 'N/A' }}</td>
                                            <td>{{ $branch->city ?? ($branch->address ?? 'N/A') }}</td>
                                            <td>{{ $branch->primary_contact ?? ($branch->contact ?? 'N/A') }}</td>
                                            <td>
                                                @if(isset($branch->status) && $branch->status == 'Inactive')
                                                    <span class="status-inactive">Inactive</span>
                                                @else
                                                    <span class="status-badge">Active</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-view btn-sm text-white view-branch-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#viewBranchModal"
                                                        data-id="{{ $branch->id }}" 
                                                        data-name="{{ $branch->name }}" 
                                                        data-code="{{ $branch->branch_code ?? 'N/A' }}"
                                                        data-status="{{ $branch->status ?? 'Active' }}"
                                                        data-address1="{{ $branch->address_line_1 ?? $branch->address ?? 'N/A' }}"
                                                        data-address2="{{ $branch->address_line_2 ?? 'N/A' }}"
                                                        data-city="{{ $branch->city ?? 'N/A' }}"
                                                        data-state="{{ $branch->state ?? 'N/A' }}"
                                                        data-pincode="{{ $branch->pincode ?? 'N/A' }}"
                                                        data-managername="{{ $branch->manager_name ?? 'N/A' }}"
                                                        data-managercontact="{{ $branch->manager_contact ?? 'N/A' }}"
                                                        data-manageremail="{{ $branch->manager_email ?? 'N/A' }}"
                                                        data-primarycontact="{{ $branch->primary_contact ?? $branch->contact ?? 'N/A' }}"
                                                        data-alternatecontact="{{ $branch->alternate_contact ?? 'N/A' }}"
                                                        data-branchemail="{{ $branch->branch_email ?? 'N/A' }}"
                                                        title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-edit btn-sm text-white edit-branch-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#editBranchModal"
                                                        data-id="{{ $branch->id }}" 
                                                        data-name="{{ $branch->name }}" 
                                                        data-code="{{ $branch->branch_code ?? '' }}"
                                                        data-status="{{ $branch->status ?? 'Active' }}"
                                                        data-address1="{{ $branch->address_line_1 ?? '' }}"
                                                        data-address2="{{ $branch->address_line_2 ?? '' }}"
                                                        data-city="{{ $branch->city ?? '' }}"
                                                        data-state="{{ $branch->state ?? '' }}"
                                                        data-pincode="{{ $branch->pincode ?? '' }}"
                                                        data-managername="{{ $branch->manager_name ?? '' }}"
                                                        data-managercontact="{{ $branch->manager_contact ?? '' }}"
                                                        data-manageremail="{{ $branch->manager_email ?? '' }}"
                                                        data-primarycontact="{{ $branch->primary_contact ?? $branch->contact ?? '' }}"
                                                        data-alternatecontact="{{ $branch->alternate_contact ?? '' }}"
                                                        data-branchemail="{{ $branch->branch_email ?? '' }}"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form action="{{ route('dashboard.branches.destroy', $branch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this branch?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-delete btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center empty-state">
                                                <i class="fas fa-code-branch fa-2x mb-2"></i>
                                                <div>No branches found. Click on <strong>"Add New Branch"</strong> to create one.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center empty-state">
                                            <i class="fas fa-info-circle me-1"></i> Branch data is not loaded yet.
                                        </td>
                                    </tr>
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Branch Modal --}}
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dashboard.branches.store') }}" method="POST" id="addBranchForm">
                @csrf
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addBranchModalLabel"><i class="fas fa-code-branch me-2"></i> Add New Gym Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 1. Branch Information -->
                    <div class="section-title">1. Branch Information</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter branch name">
                        </div>
                        <div class="col-md-4">
                            <label for="branch_code" class="form-label">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branch_code" name="branch_code" required placeholder="Branch code">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Complete Address -->
                    <div class="section-title">2. Complete Address</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" required placeholder="Enter address">
                        </div>
                        <div class="col-md-6">
                            <label for="address_line_2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address_line_2" name="address_line_2" placeholder="Enter address">
                        </div>
                        <div class="col-md-4">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" required placeholder="Enter city">
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" required placeholder="Enter state">
                        </div>
                        <div class="col-md-4">
                            <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pincode" name="pincode" maxlength="6" required placeholder="Enter pincode">
                        </div>
                    </div>

                    <!-- 3. Manager Details -->
                    <div class="section-title">3. Branch Manager Details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="manager_name" class="form-label">Manager Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="manager_name" name="manager_name" required placeholder="Enter manager name">
                        </div>
                        <div class="col-md-4">
                            <label for="manager_contact" class="form-label">Manager Contact</label>
                            <input type="text" class="form-control" id="manager_contact" name="manager_contact" maxlength="10" placeholder="Enter manager no." oninput="validateIndianMobile(this, 'add-mgr-contact-error')">
                            <div id="add-mgr-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="manager_email" class="form-label">Manager Email</label>
                            <input type="email" class="form-control" id="manager_email" name="manager_email" placeholder="Enter manager email" oninput="validateEmail(this, 'add-mgr-email-error')">
                            <div id="add-mgr-email-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- 4. Contact Details -->
                    <div class="section-title">4. Branch Contact Details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="primary_contact" class="form-label">Primary Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="primary_contact" name="primary_contact" maxlength="10" required placeholder="Primary Number" oninput="validateIndianMobile(this, 'add-primary-contact-error')">
                            <div id="add-primary-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="alternate_contact" class="form-label">Alternate Contact</label>
                            <input type="text" class="form-control" id="alternate_contact" name="alternate_contact" maxlength="10" placeholder="Alternate Number" oninput="validateIndianMobile(this, 'add-alt-contact-error')">
                            <div id="add-alt-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="branch_email" class="form-label">Branch Email</label>
                            <input type="email" class="form-control" id="branch_email" name="branch_email" placeholder="Enter branch email" oninput="validateEmail(this, 'add-branch-email-error')">
                            <div id="add-branch-email-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- 5. Operating Hours Note -->
                    <div class="section-title">5. Operating Hours</div>
                    <p class="text-muted small">Standard operating hours (06:00 AM - 10:00 PM) will be applied by default. You can configure custom schedule via backend settings if required.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal-btn" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn save-branch-btn"><i class="fas fa-save me-1"></i> Save Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Branch Modal --}}
<div class="modal fade" id="editBranchModal" tabindex="-1" aria-labelledby="editBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBranchForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editBranchModalLabel"><i class="fas fa-edit me-2"></i> Edit Gym Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 1. Branch Information -->
                    <div class="section-title">1. Branch Information</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="edit-name" class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-branch-code" class="form-label">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-branch-code" name="branch_code" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Complete Address -->
                    <div class="section-title">2. Complete Address</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit-address-line-1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-address-line-1" name="address_line_1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-address-line-2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="edit-address-line-2" name="address_line_2">
                        </div>
                        <div class="col-md-4">
                            <label for="edit-city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-city" name="city" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-state" name="state" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-pincode" name="pincode" maxlength="6" required>
                        </div>
                    </div>

                    <!-- 3. Manager Details -->
                    <div class="section-title">3. Branch Manager Details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="edit-manager-name" class="form-label">Manager Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-manager-name" name="manager_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-manager-contact" class="form-label">Manager Contact</label>
                            <input type="text" class="form-control" id="edit-manager-contact" name="manager_contact" maxlength="10" oninput="validateIndianMobile(this, 'edit-mgr-contact-error')">
                            <div id="edit-mgr-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-manager-email" class="form-label">Manager Email</label>
                            <input type="email" class="form-control" id="edit-manager-email" name="manager_email" oninput="validateEmail(this, 'edit-mgr-email-error')">
                            <div id="edit-mgr-email-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- 4. Contact Details -->
                    <div class="section-title">4. Branch Contact Details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="edit-primary-contact" class="form-label">Primary Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-primary-contact" name="primary_contact" maxlength="10" required oninput="validateIndianMobile(this, 'edit-primary-contact-error')">
                            <div id="edit-primary-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-alternate-contact" class="form-label">Alternate Contact</label>
                            <input type="text" class="form-control" id="edit-alternate-contact" name="alternate_contact" maxlength="10" oninput="validateIndianMobile(this, 'edit-alt-contact-error')">
                            <div id="edit-alt-contact-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-branch-email" class="form-label">Branch Email</label>
                            <input type="email" class="form-control" id="edit-branch-email" name="branch_email" oninput="validateEmail(this, 'edit-branch-email-error')">
                            <div id="edit-branch-email-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal-btn" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn save-branch-btn"><i class="fas fa-save me-1"></i> Update Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Branch Modal --}}
<div class="modal fade" id="viewBranchModal" tabindex="-1" aria-labelledby="viewBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="viewBranchModalLabel"><i class="fas fa-eye me-2"></i> Branch Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="detail-label">Branch ID</div>
                        <div class="detail-value text-primary" id="modal-branch-id">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Branch Code</div>
                        <div class="detail-value" id="modal-branch-code">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Status</div>
                        <div><span class="status-badge" id="modal-branch-status">Active</span></div>
                    </div>
                    
                    <div class="col-12"><hr class="text-muted opacity-25 my-1"></div>
                    
                    <div class="col-12">
                        <div class="detail-label">Branch Name</div>
                        <div class="detail-value fs-6" id="modal-branch-name">-</div>
                    </div>

                    <div class="section-title col-12 mb-0 mt-2">Address Details</div>
                    <div class="col-12">
                        <div class="detail-label">Complete Address</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-branch-full-address">-</div>
                    </div>

                    <div class="section-title col-12 mb-0 mt-2">Manager Details</div>
                    <div class="col-md-4">
                        <div class="detail-label">Manager Name</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-manager-name">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Manager Contact</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-manager-contact">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Manager Email</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-manager-email">-</div>
                    </div>

                    <div class="section-title col-12 mb-0 mt-2">Contact Details</div>
                    <div class="col-md-4">
                        <div class="detail-label">Primary Contact</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-primary-contact">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Alternate Contact</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-alternate-contact">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Branch Email</div>
                        <div class="detail-value fw-normal text-secondary" id="modal-branch-email">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn close-modal-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function validateIndianMobile(inputElement, errorDivId) {
        inputElement.value = inputElement.value.replace(/[^0-9]/g, '');
        const errorDiv = document.getElementById(errorDivId);
        if (!errorDiv) return;
        const value = inputElement.value;

        if (value.length > 0) {
            const firstDigit = value.charAt(0);
            if (!['6', '7', '8', '9'].includes(firstDigit)) {
                errorDiv.textContent = "Mobile number must start with 6, 7, 8, or 9.";
                errorDiv.style.display = "block";
            } else if (value.length < 10) {
                errorDiv.textContent = "Mobile number must be 10 digits.";
                errorDiv.style.display = "block";
            } else {
                errorDiv.textContent = "";
                errorDiv.style.display = "none";
            }
        } else {
            errorDiv.textContent = "";
            errorDiv.style.display = "none";
        }
    }

    function validateEmail(inputElement, errorDivId) {
        const errorDiv = document.getElementById(errorDivId);
        if (!errorDiv) return;
        const value = inputElement.value.trim();

        if (value.length > 0) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                errorDiv.textContent = "Please enter a valid email address (e.g., mail@example.com).";
                errorDiv.style.display = "block";
            } else {
                errorDiv.textContent = "";
                errorDiv.style.display = "none";
            }
        } else {
            errorDiv.textContent = "";
            errorDiv.style.display = "none";
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Automatic Branch Code Generation Logic
        const branchNameInput = document.getElementById('name');
        const branchCodeInput = document.getElementById('branch_code');

        if (branchNameInput && branchCodeInput) {
            branchNameInput.addEventListener('input', function () {
                let name = this.value.trim();
                if (name.length > 0) {
                    let initials = name.split(' ')
                                       .map(word => word.charAt(0))
                                       .join('')
                                       .toUpperCase();
                    
                    let randomNum = Math.floor(100 + Math.random() * 900);
                    branchCodeInput.value = initials + '-' + randomNum;
                } else {
                    branchCodeInput.value = '';
                }
            });
        }

        // View Modal Data Handler
        const viewButtons = document.querySelectorAll('.view-branch-btn');
        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('modal-branch-id').textContent = '#' + this.getAttribute('data-id');
                document.getElementById('modal-branch-code').textContent = this.getAttribute('data-code');
                document.getElementById('modal-branch-name').textContent = this.getAttribute('data-name');
                
                const status = this.getAttribute('data-status');
                const statusBadge = document.getElementById('modal-branch-status');
                statusBadge.textContent = status;
                statusBadge.className = status === 'Inactive' ? 'status-inactive' : 'status-badge';

                const addr1 = this.getAttribute('data-address1');
                const addr2 = this.getAttribute('data-address2');
                const city = this.getAttribute('data-city');
                const state = this.getAttribute('data-state');
                const pincode = this.getAttribute('data-pincode');
                
                let fullAddr = addr1 !== 'N/A' ? addr1 : '';
                if (addr2 && addr2 !== 'N/A') fullAddr += ', ' + addr2;
                if (city && city !== 'N/A') fullAddr += ', ' + city;
                if (state && state !== 'N/A') fullAddr += ' - ' + state;
                if (pincode && pincode !== 'N/A') fullAddr += ' (' + pincode + ')';
                
                document.getElementById('modal-branch-full-address').textContent = fullAddr || 'N/A';
                document.getElementById('modal-manager-name').textContent = this.getAttribute('data-managername');
                document.getElementById('modal-manager-contact').textContent = this.getAttribute('data-managercontact');
                document.getElementById('modal-manager-email').textContent = this.getAttribute('data-manageremail');
                document.getElementById('modal-primary-contact').textContent = this.getAttribute('data-primarycontact');
                document.getElementById('modal-alternate-contact').textContent = this.getAttribute('data-alternatecontact');
                document.getElementById('modal-branch-email').textContent = this.getAttribute('data-branchemail');
            });
        });

        // Edit Modal Data Handler
        const editButtons = document.querySelectorAll('.edit-branch-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                
                document.querySelectorAll('[id$="-contact-error"], [id$="-email-error"]').forEach(el => el.style.display = 'none');

                const form = document.getElementById('editBranchForm');
                form.action = `/dashboard/branches/${id}`;

                document.getElementById('edit-name').value = this.getAttribute('data-name') || '';
                document.getElementById('edit-branch-code').value = this.getAttribute('data-code') || '';
                document.getElementById('edit-status').value = this.getAttribute('data-status') || 'Active';
                document.getElementById('edit-address-line-1').value = this.getAttribute('data-address1') || '';
                document.getElementById('edit-address-line-2').value = this.getAttribute('data-address2') !== 'N/A' ? this.getAttribute('data-address2') : '';
                document.getElementById('edit-city').value = this.getAttribute('data-city') || '';
                document.getElementById('edit-state').value = this.getAttribute('data-state') || '';
                document.getElementById('edit-pincode').value = this.getAttribute('data-pincode') !== 'N/A' ? this.getAttribute('data-pincode') : '';
                document.getElementById('edit-manager-name').value = this.getAttribute('data-managername') !== 'N/A' ? this.getAttribute('data-managername') : '';
                document.getElementById('edit-manager-contact').value = this.getAttribute('data-managercontact') !== 'N/A' ? this.getAttribute('data-managercontact') : '';
                document.getElementById('edit-manager-email').value = this.getAttribute('data-manageremail') !== 'N/A' ? this.getAttribute('data-manageremail') : '';
                document.getElementById('edit-primary-contact').value = this.getAttribute('data-primarycontact') !== 'N/A' ? this.getAttribute('data-primarycontact') : '';
                document.getElementById('edit-alternate-contact').value = this.getAttribute('data-alternatecontact') !== 'N/A' ? this.getAttribute('data-alternatecontact') : '';
                document.getElementById('edit-branch-email').value = this.getAttribute('data-branchemail') !== 'N/A' ? this.getAttribute('data-branchemail') : '';
            });
        });

        // Add Modal Reset
        const addModalBtn = document.querySelector('[data-bs-target="#addBranchModal"]');
        if(addModalBtn) {
            addModalBtn.addEventListener('click', function() {
                document.querySelectorAll('[id$="-contact-error"], [id$="-email-error"]').forEach(el => el.style.display = 'none');
                document.getElementById('addBranchForm').reset();
            });
        }
    });
</script>
@endsection