@extends('auth.sidebar')

@section('content')
<div class="container py-5">
    <!-- Centered Form Wrapper Container -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Header & Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-user-plus text-primary me-2"></i>Add New Staff</h2>
                    <p class="text-muted small mb-0">Fill in the staff details, role, branch, and salary information securely.</p>
                </div>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary px-4 shadow-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-lg me-2 text-danger"></i>
                        <div><strong>Please fix the following validation errors:</strong></div>
                    </div>
                    <ul class="mb-0 mt-2 small">
                        @foreach ($errors->all() as $error)
                            <li class="text-danger fw-medium">{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Add Staff Form Card -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                    <h5 class="mb-0 text-white"><i class="fas fa-id-badge me-2"></i> Staff Registration Form</h5>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data" novalidate id="staffForm">
                        @csrf

                        <!-- SECTION 1: Personal Information -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light bg-gradient border-start border-primary border-4">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle me-2"></i> 1. Personal Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control border-start-0 @error('name') is-invalid @enderror" placeholder="Enter Full Name" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-phone-alt"></i></span>
                                        <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" class="form-control border-start-0 @error('mobile') is-invalid @enderror" placeholder="Enter Mobile No." maxlength="10" required>
                                    </div>
                                    <div id="mobile-live-error" class="text-danger small mt-1 fw-semibold" style="display: none;"><i class="fas fa-exclamation-circle me-1"></i>Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.</div>
                                    @error('mobile')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control border-start-0 @error('email') is-invalid @enderror" placeholder="Enter Email">
                                    </div>
                                    <div id="email-live-error" class="text-danger small mt-1 fw-semibold" style="display: none;"><i class="fas fa-exclamation-circle me-1"></i>Please enter a valid email address.</div>
                                    @error('email')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Profile Photo</label>
                                    <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror">
                                    @error('profile_photo')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary">Address</label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Enter residential address">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Employment Details -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light bg-gradient border-start border-info border-4">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-briefcase me-2"></i> 2. Employment Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Staff ID</label>
                                    <input type="text" name="staff_id" value="{{ $nextStaffId }}" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="Receptionist" {{ old('role') == 'Receptionist' ? 'selected' : '' }}>Receptionist</option>
                                        <option value="Trainer" {{ old('role') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                                        <option value="Accountant" {{ old('role') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="Cleaning Staff" {{ old('role') == 'Cleaning Staff' ? 'selected' : '' }}>Cleaning Staff</option>
                                    </select>
                                    @error('role')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Branch <span class="text-danger">*</span></label>
                                    <select name="branch" class="form-select @error('branch') is-invalid @enderror" required>
                                        <option value="">Select Branch</option>
                                        <option value="AURA Baner" {{ old('branch') == 'AURA Baner' ? 'selected' : '' }}>AURA Baner</option>
                                        <option value="Kothrud" {{ old('branch') == 'Kothrud' ? 'selected' : '' }}>Kothrud</option>
                                        <option value="Wakad" {{ old('branch') == 'Wakad' ? 'selected' : '' }}>Wakad</option>
                                    </select>
                                    @error('branch')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Joining Date</label>
                                    <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Employment Type</label>
                                    <select name="employment_type" class="form-select">
                                        <option value="Full Time" {{ old('employment_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="Part Time" {{ old('employment_type') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="Contract" {{ old('employment_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Salary Details -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light bg-gradient border-start border-success border-4">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-rupee-sign me-2"></i> 3. Salary Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Salary Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted border-end-0">₹</span>
                                        <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" class="form-control border-start-0 @error('salary') is-invalid @enderror" placeholder="Enter Salary Amount" required>
                                    </div>
                                    @error('salary')
                                        <div class="text-danger small mt-1 fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Salary Type</label>
                                    <select name="salary_type" class="form-select">
                                        <option value="Monthly" {{ old('salary_type') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="Daily" {{ old('salary_type') == 'Daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="Hourly" {{ old('salary_type') == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit & Cancel Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('staff.index') }}" class="btn btn-light px-4 py-2 border shadow-sm rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm rounded-pill" style="background-color: #1e293b; border-color: #1e293b;">
                                <i class="fas fa-save me-1"></i> Save Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- jQuery and Real-time validation script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Indian Mobile Number Live Validation (Starts with 6-9, total 10 digits)
        $('#mobile').on('input', function() {
            let mobileVal = $(this).val();
            let mobileRegex = /^[6-9]\d{9}$/;

            if (mobileVal.length > 0 && !mobileRegex.test(mobileVal)) {
                $(this).addClass('is-invalid');
                $('#mobile-live-error').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#mobile-live-error').hide();
            }
        });

        // Email Live Validation
        $('#email').on('input', function() {
            let emailVal = $(this).val();
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailVal.length > 0 && !emailRegex.test(emailVal)) {
                $(this).addClass('is-invalid');
                $('#email-live-error').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#email-live-error').hide();
            }
        });
    });
</script>
@endsection