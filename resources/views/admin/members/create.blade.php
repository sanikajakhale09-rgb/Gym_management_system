@extends('auth.sidebar')

@section('title', 'Add New Member - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-10">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-user-plus me-2" style="color: #4f46e5;"></i> Add New Member
                    </h2>
                    <p class="text-muted small mb-0">Fill in the details to register a new gym member</p>
                </div>
                <a href="{{ route('members.index') }}" class="btn btn-sm rounded-pill px-3 text-white shadow-sm" style="background: linear-gradient(135deg, #475569 0%, #334155 100%); border: none;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Members
                </a>
            </div>

            {{-- Show Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 bg-danger text-white mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white px-4 py-3" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2"></i> Member Registration Form</h5>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" id="memberForm">
                        @csrf

                        <!-- QR Code Hidden Field -->
                        <input type="hidden" name="qr_code" id="qr_code">

                        <!-- Branch Selection -->
                        <div class="mb-4">
                            <label for="branch_id" class="form-label fw-semibold text-secondary">Branch <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-code-branch"></i></span>
                                <select class="form-control border-start-0 bg-light" id="branch_id" name="branch_id" required style="box-shadow: none;">
                                    <option value="" disabled {{ old('branch_id') ? '' : 'selected' }}>Select Gym Branch</option>
                                    @php
                                        $allBranches = class_exists(\App\Models\Branch::class) ? \App\Models\Branch::all() : [];
                                    @endphp
                                    @foreach($allBranches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Full Name & Gender -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label fw-semibold text-secondary">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter full name" style="box-shadow: none;">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-secondary d-block">Gender <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center bg-light border rounded-3 px-3 py-2" style="height: 38px;">
                                    <div class="form-check form-check-inline me-4">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" {{ old('gender', 'Male') == 'Male' ? 'checked' : '' }} required>
                                        <label class="form-check-label text-secondary small" for="gender_male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline me-4">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                        <label class="form-check-label text-secondary small" for="gender_female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_other" value="Other" {{ old('gender') == 'Other' ? 'checked' : '' }}>
                                        <label class="form-check-label text-secondary small" for="gender_other">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date of Birth & Phone -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="date_of_birth" class="form-label fw-semibold text-secondary">Date of Birth</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-cake-candles"></i></span>
                                    <input type="date" class="form-control border-start-0 bg-light" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" style="box-shadow: none;">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="phone" class="form-label fw-semibold text-secondary">Phone <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="phone" name="phone" value="{{ old('phone') }}" required maxlength="10" placeholder="Enter 10-digit mobile number" style="box-shadow: none;">
                                </div>
                                <div id="phoneError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 4px;">
                                    Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.
                                </div>
                            </div>
                        </div>

                        <!-- Email & Occupation -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address" style="box-shadow: none;">
                                </div>
                                <div id="emailError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 4px;">
                                    Email must contain only lowercase letters, at least one number, an @ symbol, and no capital letters or invalid special characters.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="occupation" class="form-label fw-semibold text-secondary">Occupation</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-briefcase"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="occupation" name="occupation" value="{{ old('occupation') }}" placeholder="e.g. Engineer, Student" style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="form-label fw-semibold text-secondary">Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-start text-muted"><i class="fa-solid fa-location-dot"></i></span>
                                <textarea class="form-control bg-light" id="address" name="address" rows="2" placeholder="Enter residential address..." style="box-shadow: none;">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Blood Group & Reference / Source -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="blood_group" class="form-label fw-semibold text-secondary">Blood Group</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-droplet"></i></span>
                                    <select class="form-control border-start-0 bg-light" id="blood_group" name="blood_group" style="box-shadow: none;">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="reference_source" class="form-label fw-semibold text-secondary">Reference / Source</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-bullhorn"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="reference_source" name="reference_source" value="{{ old('reference_source') }}" placeholder="e.g. Friend, Instagram, Pamphlet" style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Details -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="emergency_contact_name" class="form-label fw-semibold text-secondary">Emergency Contact Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user-shield"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Enter contact person name" style="box-shadow: none;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="emergency_contact_phone" class="form-label fw-semibold text-secondary">Emergency Contact Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone-flip"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" maxlength="10" placeholder="Enter emergency phone number" style="box-shadow: none;">
                                </div>
                                <div id="emergencyPhoneError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 4px;">
                                    Please enter a valid 10-digit Indian emergency mobile number starting with 6, 7, 8, or 9.
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold text-secondary">Notes</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-start text-muted"><i class="fa-solid fa-notes-medical"></i></span>
                                <textarea class="form-control bg-light" id="notes" name="notes" rows="2" placeholder="Any medical issues, injuries or special remarks..." style="box-shadow: none;">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Joining Date & Document / Photo -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="joining_date" class="form-label fw-semibold text-secondary">Joining Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-calendar"></i></span>
                                    <input type="date" class="form-control border-start-0 bg-light" id="joining_date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required style="box-shadow: none;">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="document_path" class="form-label fw-semibold text-secondary">Document / Photo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-file-arrow-up"></i></span>
                                    <input type="file" class="form-control border-start-0 bg-light" id="document_path" name="document_path" required style="box-shadow: none;">
                                </div>
                                <small class="text-muted d-block mt-1">Upload ID proof or photograph.</small>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-4 pt-2 d-flex gap-2">
                            <button type="submit" class="btn text-white rounded-pill px-4 py-2 shadow-sm fw-semibold" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%); border: none;">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Member
                            </button>
                            <a href="{{ route('members.index') }}" class="btn btn-light rounded-pill px-4 py-2 shadow-sm text-secondary fw-semibold border">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Automatic PDF Download Script (Index/Redirect Handle) --}}
@if(session('downloadPdfId'))
    <script>
        window.open("{{ route('members.receipt', session('downloadPdfId')) }}", "_blank");
    </script>
@endif

<!-- Validation & Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const memberForm = document.getElementById('memberForm');
        const phoneInput = document.getElementById('phone');
        const emailInput = document.getElementById('email');
        const emergencyPhoneInput = document.getElementById('emergency_contact_phone');
        
        const phoneError = document.getElementById('phoneError');
        const emailError = document.getElementById('emailError');
        const emergencyPhoneError = document.getElementById('emergencyPhoneError');

        const phoneRegex = /^[6-9]\d{9}$/;
        // Email validation: Only lowercase letters, digits, @, and allowed symbols (., _, -, +, %)
        const emailRegex = /^(?=.*\d)[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;

        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
                if (phoneRegex.test(this.value)) {
                    phoneError.style.display = 'none';
                    this.style.borderColor = '#cbd5e1';
                } else {
                    phoneError.style.display = 'block';
                    this.style.borderColor = '#dc2626';
                }
            });
        }

        if (emergencyPhoneInput) {
            emergencyPhoneInput.addEventListener('input', function() {
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
                if (this.value === '' || phoneRegex.test(this.value)) {
                    emergencyPhoneError.style.display = 'none';
                    this.style.borderColor = '#cbd5e1';
                } else {
                    emergencyPhoneError.style.display = 'block';
                    this.style.borderColor = '#dc2626';
                }
            });
        }

        if (emailInput) {
            emailInput.addEventListener('input', function() {
                if (emailRegex.test(this.value)) {
                    emailError.style.display = 'none';
                    this.style.borderColor = '#cbd5e1';
                } else {
                    emailError.style.display = 'block';
                    this.style.borderColor = '#dc2626';
                }
            });
        }

        memberForm.addEventListener('submit', function(event) {
            if (!phoneRegex.test(phoneInput.value)) {
                event.preventDefault();
                phoneError.style.display = 'block';
                phoneInput.focus();
                alert('Please enter a valid 10-digit Indian mobile number.');
                return;
            }

            if (emergencyPhoneInput.value !== '' && !phoneRegex.test(emergencyPhoneInput.value)) {
                event.preventDefault();
                emergencyPhoneError.style.display = 'block';
                emergencyPhoneInput.focus();
                alert('Please enter a valid 10-digit Indian emergency contact number.');
                return;
            }

            if (!emailRegex.test(emailInput.value)) {
                event.preventDefault();
                emailError.style.display = 'block';
                emailInput.focus();
                alert('Please enter a valid email address using only lowercase letters, at least one number, and no capital letters.');
                return;
            }
        });
    });
</script>
@endsection