@extends('auth.sidebar')

@section('title', 'Assign Members to Trainer - Gym Admin')

@section('title', 'Add Trainer - ZapCodex Gym')

@section('content')
<div class="content-wrapper py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <!-- Content Header -->
    <section class="content-header mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h2 class="fw-bold text-dark m-0 d-flex align-items-center">
                        <i class="fas fa-user-plus me-2" style="color: #2b215e;"></i>Add New Trainer
                    </h2>
                    <p class="text-muted small m-0 mt-1">Register a new trainer profile under gym branches.</p>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('trainers.index') }}" class="btn btn-sm rounded-pill px-4 py-2 shadow-sm fw-semibold text-white border-0" style="background-color: #2b215e;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Trainers
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid px-4">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fs-4 me-2"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header text-white p-4 border-0" style="background-color: #2b215e;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-id-card me-2"></i> Trainer Registration Form</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('trainers.store') }}" method="POST" id="trainerForm">
                        @csrf
                        <div class="row g-4">
                            <!-- Branch Selection -->
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label fw-semibold text-secondary small">Select Branch <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-code-branch"></i></span>
                                    <select name="branch_id" id="branch_id" class="form-select border-start-0 ps-0 rounded-end-3 shadow-none bg-white" required>
                                        <option value="" selected disabled>Select Gym Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Trainer Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold text-secondary small">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
                                </div>
                            </div>

                            <!-- Email with Live Validation -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-secondary small">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address " oninput="validateEmail(this, 'email-error')" required>
                                </div>
                                <div id="email-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                            </div>

                            <!-- Mobile Number with Live Validation -->
                            <div class="col-md-6">
                                <label for="mobile" class="form-label fw-semibold text-secondary small">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="mobile" name="mobile" value="{{ old('mobile') }}" maxlength="10" placeholder="Enter valid mobile number" oninput="validateIndianMobile(this, 'mobile-error')" required>
                                </div>
                                <div id="mobile-error" class="text-danger small mt-1 fw-bold" style="display: none;"></div>
                            </div>

                            <!-- Specialization -->
                            <div class="col-md-6">
                                <label for="specialization" class="form-label fw-semibold text-secondary small">Specialization <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-dumbbell"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="Enter Specialization" required>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div class="col-md-6">
                                <label for="experience" class="form-label fw-semibold text-secondary small">Experience (in Years) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-award"></i></span>
                                    <input type="number" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="experience" name="experience" value="{{ old('experience') }}" placeholder="Enter experience" min="0" required>
                                </div>
                            </div>

                            <!-- Salary -->
                            <div class="col-md-6">
                                <label for="salary" class="form-label fw-semibold text-secondary small">Monthly Salary (Rs.) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0 ps-0 rounded-end-3 shadow-none bg-white" id="salary" name="salary" value="{{ old('salary') }}" placeholder="Enter salary" required>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold text-secondary small">Account Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="fas fa-toggle-on"></i></span>
                                    <select name="status" id="status" class="form-select border-start-0 ps-0 rounded-end-3 shadow-none bg-white" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 mt-5 text-end border-top pt-4">
                                <button type="reset" class="btn btn-light border px-4 py-2 rounded-pill me-2 text-secondary fw-semibold" onclick="resetAllErrors()">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn px-5 py-2 rounded-pill shadow-sm fw-semibold text-white" style="background-color: #2b215e;">
                                    <i class="fas fa-save me-1"></i> Save Trainer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Indian Mobile Number Validation
    function validateIndianMobile(inputElement, errorDivId) {
        inputElement.value = inputElement.value.replace(/[^0-9]/g, '');
        
        const errorDiv = document.getElementById(errorDivId);
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

    // Email Validation (Checks format like user@domain.com, removes spaces, checks for @ and dot)
    function validateEmail(inputElement, errorDivId) {
        // Remove spaces automatically
        inputElement.value = inputElement.value.replace(/\s+/g, '');
        
        const errorDiv = document.getElementById(errorDivId);
        const value = inputElement.value;

        // Standard Email Regex Pattern
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (value.length > 0) {
            if (!emailRegex.test(value)) {
                errorDiv.textContent = "Please enter a valid email address (e.g. name@gmail.com).";
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

    // Reset error messages on form reset
    function resetAllErrors() {
        document.getElementById('mobile-error').style.display = 'none';
        document.getElementById('email-error').style.display = 'none';
    }

    $(document).ready(function() {
        // Form submit final validation check
        $('#trainerForm').on('submit', function(e) {
            const mobileInput = $('#mobile').val();
            const emailInput = $('#email').val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            // Mobile validation check
            if (mobileInput.length > 0) {
                const firstDigit = mobileInput.charAt(0);
                if (!['6', '7', '8', '9'].includes(firstDigit) || mobileInput.length < 10) {
                    e.preventDefault();
                    alert("Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.");
                    $('#mobile').focus();
                    return false;
                }
            }

            // Email validation check
            if (!emailRegex.test(emailInput)) {
                e.preventDefault();
                alert("Please enter a valid email address.");
                $('#email').focus();
                return false;
            }
        });
    });
</script>
@endsection