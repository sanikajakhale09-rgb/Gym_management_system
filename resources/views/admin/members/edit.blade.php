@extends('auth.sidebar')

@section('title', 'Edit Member - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-10">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-user-pen me-2" style="color: #4f46e5;"></i> Edit Member Details
                    </h2>
                    <p class="text-muted small mb-0">Update registration details for {{ $member->name }}</p>
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
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2"></i> Update Member Form</h5>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data" id="memberForm">
                        @csrf
                        @method('PUT')

                        <!-- Branch Selection -->
                        <div class="mb-4">
                            <label for="branch_id" class="form-label fw-semibold text-secondary">Select Branch <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-code-branch"></i></span>
                                <select class="form-control border-start-0 bg-light" id="branch_id" name="branch_id" required style="box-shadow: none;">
                                    <option value="" disabled>Select Gym Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $member->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label fw-semibold text-secondary">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="name" name="name" value="{{ old('name', $member->name) }}" required placeholder="Enter full name" style="box-shadow: none;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="phone" class="form-label fw-semibold text-secondary">Phone Number (Indian) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="phone" name="phone" value="{{ old('phone', $member->phone) }}" required maxlength="10" placeholder="Enter 10-digit mobile number" style="box-shadow: none;">
                                </div>
                                <div id="phoneError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 4px;">
                                    Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label fw-semibold text-secondary">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 bg-light" id="email" name="email" value="{{ old('email', $member->email) }}" required placeholder="Enter email address" style="box-shadow: none;">
                                </div>
                                <div id="emailError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 4px;">
                                    Please enter a valid email address containing '@' and a domain (e.g., name@gmail.com).
                                </div>
                            </div>
                            
                            <!-- Membership Plan Dropdown -->
                            <div class="col-md-6 mb-4">
                                <label for="membership_plan_id" class="form-label fw-semibold text-secondary">Membership Plan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-tag"></i></span>
                                    <select class="form-control border-start-0 bg-light" id="membership_plan_id" name="membership_plan_id" required style="box-shadow: none;">
                                        <option value="" disabled data-price="0" data-tax="0" data-discount="0" data-days="0">Select Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('membership_plan_id', optional($member->latestSubscription)->membership_plan_id) == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} (₹{{ $plan->price }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Assign Trainer Dropdown -->
                        <div class="mb-4">
                            <label for="trainer_id" class="form-label fw-semibold text-secondary">Assign Trainer <span class="text-muted fw-normal">(Optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-dumbbell"></i></span>
                                <select class="form-control border-start-0 bg-light" id="trainer_id" name="trainer_id" style="box-shadow: none;">
                                    <option value="">Select Gym Trainer (Optional)</option>
                                    @foreach($trainers as $trainer)
                                        <option value="{{ $trainer->id }}" {{ old('trainer_id', optional($member->latestSubscription)->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                            {{ $trainer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Status Option for Edit -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold text-secondary">Membership Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-circle-check"></i></span>
                                <select class="form-control border-start-0 bg-light" id="status" name="status" required style="box-shadow: none;">
                                    <option value="Active" {{ old('status', $member->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Expired" {{ old('status', $member->status) == 'Expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="Suspended" {{ old('status', $member->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>

                        <!-- Payment Fields (Updated with Paid Amount & Fixed Payment Method) -->
                        <div class="row">
                            <!-- Total Package Amount -->
                            <div class="col-md-4 mb-4">
                                <label for="total_amount" class="form-label fw-semibold text-secondary">Total Package Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary"><i class="fa-solid fa-indian-rupee-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0 bg-light fw-bold text-primary" id="total_amount" name="total_amount" value="{{ old('total_amount', optional($member->latestSubscription)->total_amount ?? $member->total_amount) }}" required style="box-shadow: none;">
                                </div>
                            </div>

                            <!-- 🟢 Paid Amount (Total Amount च्या बाजूला ॲड केला आहे) -->
                            <div class="col-md-4 mb-4">
                                <label for="amount_paid" class="form-label fw-semibold text-secondary">Paid Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-success"><i class="fa-solid fa-circle-check"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0 bg-light fw-bold text-success" id="amount_paid" name="amount_paid" value="{{ old('amount_paid', optional($member->latestSubscription)->amount_paid ?? $member->paid_amount ?? 0) }}" style="box-shadow: none;">
                                </div>
                            </div>

                            <!-- Pending Amount -->
                            <div class="col-md-4 mb-4">
                                <label for="balance_amount" class="form-label fw-semibold text-secondary">Pending Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-danger"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                    <input type="number" step="0.01" class="form-control border-start-0 bg-white fw-bold text-danger" id="balance_amount" name="balance_amount" value="{{ old('balance_amount', optional($member->latestSubscription)->balance_amount ?? 0) }}" style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method (Dynamic selection instead of fixed Cash) -->
                        <div class="mb-4">
                            <label for="payment_method" class="form-label fw-semibold text-secondary">Payment Method <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-wallet"></i></span>
                                @php
                                    $currentPaymentMethod = old('payment_method', optional($member->latestSubscription)->payment_method ?? $member->payment_method ?? 'Cash');
                                @endphp
                                <select class="form-control border-start-0 bg-light" id="payment_method" name="payment_method" required style="box-shadow: none;">
                                    <option value="Cash" {{ $currentPaymentMethod == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="UPI" {{ $currentPaymentMethod == 'UPI' ? 'selected' : '' }}>UPI</option>
                                    <option value="Card" {{ $currentPaymentMethod == 'Card' ? 'selected' : '' }}>Card</option>
                                    <option value="Bank Transfer" {{ $currentPaymentMethod == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <!-- Blood Group & Occupation -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="blood_group" class="form-label fw-semibold text-secondary">Blood Group</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-droplet"></i></span>
                                    <select class="form-control border-start-0 bg-light" id="blood_group" name="blood_group" style="box-shadow: none;">
                                        <option value="">Select Blood Group</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                            <option value="{{ $bg }}" {{ old('blood_group', $member->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="occupation" class="form-label fw-semibold text-secondary">Occupation</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-briefcase"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="occupation" name="occupation" value="{{ old('occupation', $member->occupation) }}" placeholder="e.g. Engineer, Student" style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Details -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="emergency_contact_name" class="form-label fw-semibold text-secondary">Emergency Contact Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user-shield"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}" placeholder="Enter contact person name" style="box-shadow: none;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="emergency_contact_phone" class="form-label fw-semibold text-secondary">Emergency Contact Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone-flip"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}" maxlength="10" placeholder="Enter emergency phone number" style="box-shadow: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Reference & Medical Notes -->
                        <div class="mb-4">
                            <label for="reference_source" class="form-label fw-semibold text-secondary">Reference / Source</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-bullhorn"></i></span>
                                <input type="text" class="form-control border-start-0 bg-light" id="reference_source" name="reference_source" value="{{ old('reference_source', $member->reference_source) }}" placeholder="e.g. Friend, Instagram" style="box-shadow: none;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="medical_notes" class="form-label fw-semibold text-secondary">Medical Notes / Health Issues</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-start text-muted"><i class="fa-solid fa-notes-medical"></i></span>
                                <textarea class="form-control bg-light" id="medical_notes" name="medical_notes" rows="2" placeholder="Any injuries or medical conditions..." style="box-shadow: none;">{{ old('medical_notes', $member->medical_notes) }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="joining_date" class="form-label fw-semibold text-secondary">Joining Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-calendar"></i></span>
                                    <input type="date" class="form-control border-start-0 bg-light" id="joining_date" name="joining_date" value="{{ old('joining_date', $member->joining_date) }}" required style="box-shadow: none;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="document_path" class="form-label fw-semibold text-secondary">Update Document (ID/Photo)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-file-arrow-up"></i></span>
                                    <input type="file" class="form-control border-start-0 bg-light" id="document_path" name="document_path" style="box-shadow: none;">
                                </div>
                                <small class="text-muted d-block mt-1">Leave blank to keep existing document.</small>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 d-flex gap-2">
                            <button type="submit" class="btn text-white rounded-pill px-4 py-2 shadow-sm fw-semibold" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%); border: none;">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Member
                            </button>
                            <a href="{{ route('members.index') }}" class="btn btn-light rounded-pill px-4 py-2 shadow-sm text-secondary fw-semibold border">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phoneError');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');

        const phoneRegex = /^[6-9]\d{9}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.trim() === '') {
                phoneError.style.display = 'none';
                this.style.borderColor = '#ced4da';
            } else if (!phoneRegex.test(this.value)) {
                phoneError.style.display = 'block';
                this.style.borderColor = '#dc2626';
            } else {
                phoneError.style.display = 'none';
                this.style.borderColor = '#198754';
            }
        });

        emailInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                emailError.style.display = 'none';
                this.style.borderColor = '#ced4da';
            } else if (!emailRegex.test(this.value)) {
                emailError.style.display = 'block';
                this.style.borderColor = '#dc2626';
            } else {
                emailError.style.display = 'none';
                this.style.borderColor = '#198754';
            }
        });

        document.getElementById('memberForm').addEventListener('submit', function(event) {
            let isValid = true;
            if (!phoneRegex.test(phoneInput.value)) {
                event.preventDefault();
                phoneError.style.display = 'block';
                phoneInput.focus();
                isValid = false;
            }
            if (!emailRegex.test(emailInput.value)) {
                event.preventDefault();
                emailError.style.display = 'block';
                if (isValid) emailInput.focus();
                isValid = false;
            }
        });
    });
</script>
@endsection