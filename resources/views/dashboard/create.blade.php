@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                <div>
                    <h1 class="h3 text-dark fw-bold mb-0">Add New Enquiry</h1>
                    <p class="text-muted small mb-0">Fill in the details below to register a new lead/enquiry.</p>
                </div>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}" class="text-decoration-none">Enquiries</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold">Add</li>
                </ol>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <div class="d-flex align-items-center">
                        <div class="bg-light-primary text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(13, 110, 253, 0.1);">
                            <i class="fas fa-plus-circle text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Enquiry Details Form</h5>
                            <span class="text-muted small">Please enter accurate contact and status information</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span class="fw-bold">Please fix the following errors:</span>
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('enquiries.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold text-secondary small">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0 shadow-none @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold text-secondary small">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control bg-light border-start-0 ps-0 shadow-none @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter mobile number" maxlength="10" pattern="[6-9][0-9]{9}" title="Please enter a valid 10-digit Indian mobile number starting with 6-9" required>
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold text-secondary small">Email Address (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 ps-0 shadow-none @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter Email">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="source" class="form-label fw-semibold text-secondary small">Source</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-bullhorn"></i></span>
                                    <select class="form-select bg-light border-start-0 ps-0 shadow-none" id="source" name="source">
                                        <option value="Walking">Walking</option>
                                        <option value="Phone">Phone</option>
                                        <option value="SMS">SMS</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold text-secondary small">Message / Notes</label>
                            <textarea class="form-control bg-light shadow-none" id="message" name="message" rows="3" placeholder="Write any additional details or notes here...">{{ old('message') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold text-secondary small">Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-info-circle"></i></span>
                                <select class="form-select bg-light border-start-0 ps-0 shadow-none" id="status" name="status" required>
                                    <option value="New">New</option>
                                    <option value="Contacted">Contacted</option>
                                    <option value="Joined">Joined</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top border-light">
                            <a href="{{ route('enquiries.index') }}" class="btn btn-light px-4 py-2 border text-secondary fw-semibold">Cancel</a>
                            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold shadow-sm">
                                <i class="fas fa-save me-1"></i> Save Enquiry
                            </button>
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
        const emailInput = document.getElementById('email');

        // Phone validation on typing
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                let phoneVal = this.value;
                let phoneError = document.getElementById('phone-error');
                
                if (!phoneError) {
                    phoneError = document.createElement('div');
                    phoneError.id = 'phone-error';
                    phoneError.className = 'text-danger small mt-1';
                    phoneInput.closest('.col-md-6').appendChild(phoneError);
                }

                let regex = /^[6-9]\d{9}$/;
                if (phoneVal.length > 0 && !regex.test(phoneVal)) {
                    phoneError.textContent = 'Please enter a valid 10-digit Indian mobile number starting with 6-9.';
                    phoneInput.classList.add('is-invalid');
                } else {
                    phoneError.textContent = '';
                    phoneInput.classList.remove('is-invalid');
                }
            });
        }

        // Email validation on typing
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                let emailVal = this.value;
                let emailError = document.getElementById('email-error');

                if (!emailError) {
                    emailError = document.createElement('div');
                    emailError.id = 'email-error';
                    emailError.className = 'text-danger small mt-1';
                    emailInput.closest('.col-md-6').appendChild(emailError);
                }

                let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailVal.length > 0 && !emailRegex.test(emailVal)) {
                    emailError.textContent = 'Please enter a valid email address.';
                    emailInput.classList.add('is-invalid');
                } else {
                    emailError.textContent = '';
                    emailInput.classList.remove('is-invalid');
                }
            });
        }
    });
</script>
@endsection