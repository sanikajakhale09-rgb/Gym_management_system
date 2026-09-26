@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded shadow-sm border-start border-4" style="border-color: #1e3a8a !important;">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1">Gym Profile Management</h1>
            <p class="text-muted mb-0">Manage your gym details, branding, and contact information seamlessly.</p>
        </div>
        <div>
            <!-- Edit Profile Button triggering the Modal -->
            <button type="button" class="btn text-white px-4 py-2 shadow-sm rounded-pill fw-semibold" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);"
                    data-bs-toggle="modal" 
                    data-bs-target="#actionModal"
                    data-id="{{ $gymProfile->id ?? '' }}"
                    data-name="{{ $gymProfile->gym_name ?? '' }}"
                    data-email="{{ $gymProfile->email ?? '' }}"
                    data-phone="{{ $gymProfile->phone ?? '' }}"
                    data-owner="{{ $gymProfile->owner_name ?? '' }}"
                    data-gst="{{ $gymProfile->gst_number ?? '' }}"
                    data-website="{{ $gymProfile->website ?? '' }}"
                    data-hours="{{ $gymProfile->working_hours ?? '' }}"
                    data-address="{{ $gymProfile->address ?? '' }}"
                    data-desc="{{ $gymProfile->description ?? '' }}">
                <i class="fas fa-edit me-1"></i> Manage Profile
            </button>
        </div>
    </div>

    <!-- Success Message Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <!-- Gym Logo & Basic Info Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-lg border-0 text-center p-4 rounded-4 h-100 bg-gradient">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-3 position-relative">
                        @if(isset($gymProfile) && $gymProfile->logo)
                            <img src="{{ asset('storage/' . $gymProfile->logo) }}" alt="Gym Logo" class="rounded-circle img-thumbnail shadow border-3" style="width: 140px; height: 140px; object-fit: cover; border-color: #1e3a8a !important;">
                        @else
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center mx-auto shadow border-3 border-light" style="width: 140px; height: 140px; font-size: 55px; background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $gymProfile->gym_name ?? 'My Fitness Gym' }}</h4>
                    <p class="text-muted small mb-3 fst-italic">{{ $gymProfile->tagline ?? 'Transform Your Body & Mind' }}</p>
                    
                    <hr class="w-100 my-3 text-muted opacity-50">

                    <div class="text-start w-100 px-2">
                        <p class="mb-2 text-secondary small"><i class="fas fa-envelope me-2 fa-fw" style="color: #1e3a8a;"></i> {{ $gymProfile->email ?? 'N/A' }}</p>
                        <p class="mb-2 text-secondary small"><i class="fas fa-phone text-success me-2 fa-fw"></i> {{ $gymProfile->phone ?? 'N/A' }}</p>
                        <p class="mb-0 text-secondary small"><i class="fas fa-map-marker-alt text-danger me-2 fa-fw"></i> {{ $gymProfile->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information Card -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-0 rounded-top-4">
                    <h5 class="fw-bold mb-0" style="color: #1e3a8a;"><i class="fas fa-info-circle me-2"></i>Gym Information Details</h5>
                </div>
                <div class="card-body px-4">
                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 fw-bold text-muted"><i class="fas fa-user-tie text-secondary me-2"></i>Owner Name:</div>
                        <div class="col-sm-8 text-dark fw-semibold">{{ $gymProfile->owner_name ?? 'N/A' }}</div>
                    </div>
                    <hr class="text-muted opacity-25">

                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 fw-bold text-muted"><i class="fas fa-file-invoice-dollar text-secondary me-2"></i>GST / Reg Number:</div>
                        <div class="col-sm-8 text-dark fw-semibold">{{ $gymProfile->gst_number ?? 'N/A' }}</div>
                    </div>
                    <hr class="text-muted opacity-25">

                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 fw-bold text-muted"><i class="fas fa-globe text-secondary me-2"></i>Website:</div>
                        <div class="col-sm-8 text-dark">
                            @if(isset($gymProfile->website) && $gymProfile->website)
                                <a href="{{ $gymProfile->website }}" target="_blank" class="text-decoration-none fw-semibold" style="color: #1e3a8a;">{{ $gymProfile->website }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <hr class="text-muted opacity-25">

                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 fw-bold text-muted"><i class="fas fa-clock text-secondary me-2"></i>Working Hours:</div>
                        <div class="col-sm-8 text-dark fw-semibold">{{ $gymProfile->working_hours ?? 'N/A' }}</div>
                    </div>
                    <hr class="text-muted opacity-25">

                    <div class="row mb-0">
                        <div class="col-sm-4 fw-bold text-muted"><i class="fas fa-align-left text-secondary me-2"></i>Description:</div>
                        <div class="col-sm-8 text-dark">{{ $gymProfile->description ?? 'No description provided.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 🟢 Modal for View, Edit, and Delete Options -->
<!-- ========================================== -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Dark Blue Gradient Header -->
            <div class="modal-header text-white rounded-top-4 py-3 px-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <h5 class="modal-title fw-bold" id="actionModalLabel"><i class="fas fa-sliders-h me-2"></i>Manage Gym Profile</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <!-- Navigation Tabs (View / Edit) with Dark Blue Active State -->
                <ul class="nav nav-pills nav-fill mb-4 bg-white p-2 rounded-pill shadow-sm border" id="modalTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-semibold py-2 text-white" id="view-tab" data-bs-toggle="tab" data-bs-target="#view-pane" type="button" role="tab" style="background: #1e3a8a;"><i class="fas fa-eye me-1"></i> View Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-semibold py-2 text-dark" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit-pane" type="button" role="tab"><i class="fas fa-user-edit me-1"></i> Edit Profile</button>
                    </li>
                </ul>

                <div class="tab-content" id="modalTabContent">
                    <!-- 1. View Pane -->
                    <div class="tab-pane fade show active p-4 bg-white rounded-4 shadow-sm border" id="view-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Gym Name:</strong> <span id="view-name" class="text-muted"></span></div>
                            <div class="col-md-6"><strong>Email:</strong> <span id="view-email" class="text-muted"></span></div>
                            <div class="col-md-6"><strong>Phone:</strong> <span id="view-phone" class="text-muted"></span></div>
                            <div class="col-md-6"><strong>Owner Name:</strong> <span id="view-owner" class="text-muted"></span></div>
                            <div class="col-md-6"><strong>GST Number:</strong> <span id="view-gst" class="text-muted"></span></div>
                            <div class="col-md-6"><strong>Website:</strong> <span id="view-website" class="text-muted"></span></div>
                            <div class="col-md-12"><strong>Registered Address:</strong> <span id="view-address" class="text-muted"></span></div>
                            <div class="col-md-12"><strong>Working Hours:</strong> <span id="view-hours" class="text-muted"></span></div>
                            <div class="col-md-12"><strong>Description:</strong> <span id="view-desc" class="text-muted"></span></div>
                        </div>
                    </div>

                    <!-- 2. Edit Pane -->
                    <div class="tab-pane fade" id="edit-pane" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <form id="editForm" method="POST" action="{{ route('gym.profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Gym Name</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-name" name="gym_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Email</label>
                                        <input type="email" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-email" name="email" placeholder="example@gmail.com" required>
                                        <small id="emailError" class="text-danger d-none mt-1">Please enter a valid email address ending with @gmail.com.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Phone (Indian Mobile Number)</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-phone" name="phone" maxlength="10" placeholder="9876543210" required>
                                        <small id="phoneError" class="text-danger d-none mt-1">Please enter a valid 10-digit Indian mobile number starting with 6-9.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Owner Name</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-owner" name="owner_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">GST / Reg Number</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-gst" name="gst_number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Website</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-website" name="website">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Registered Address</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-address" name="address">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Working Hours</label>
                                        <input type="text" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" id="edit-hours" name="working_hours">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Description</label>
                                        <textarea class="form-control rounded-4 p-3 shadow-none border-primary-subtle" id="edit-desc" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold text-secondary">Gym Logo</label>
                                        <input type="file" class="form-control rounded-pill px-3 shadow-none border-primary-subtle" name="logo">
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" id="updateBtn" class="btn text-white px-5 py-2 rounded-pill fw-semibold shadow-sm" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);"><i class="fas fa-save me-1"></i> Update Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">

                <!-- 3. Delete Section -->
                <div class="mt-2 text-center">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2 fw-semibold" onclick="return confirm('Are you sure you want to delete this profile record?')">
                            <i class="fas fa-trash me-1"></i> Delete Profile Record
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var actionModal = document.getElementById('actionModal');
        actionModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var phone = button.getAttribute('data-phone');
            var owner = button.getAttribute('data-owner');
            var gst = button.getAttribute('data-gst');
            var website = button.getAttribute('data-website');
            var hours = button.getAttribute('data-hours');
            var address = button.getAttribute('data-address');
            var desc = button.getAttribute('data-desc');

            // View data set karne
            document.getElementById('view-name').textContent = name || 'N/A';
            document.getElementById('view-email').textContent = email || 'N/A';
            document.getElementById('view-phone').textContent = phone || 'N/A';
            document.getElementById('view-owner').textContent = owner || 'N/A';
            document.getElementById('view-gst').textContent = gst || 'N/A';
            document.getElementById('view-website').textContent = website || 'N/A';
            document.getElementById('view-address').textContent = address || 'N/A';
            document.getElementById('view-hours').textContent = hours || 'N/A';
            document.getElementById('view-desc').textContent = desc || 'No description provided.';

            // Edit form input values set karne
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-owner').value = owner;
            document.getElementById('edit-gst').value = gst;
            document.getElementById('edit-website').value = website;
            if(document.getElementById('edit-address')) document.getElementById('edit-address').value = address;
            document.getElementById('edit-hours').value = hours;
            document.getElementById('edit-desc').value = desc;

            // 🟢 Delete form action dynamically set karne
            var deleteForm = document.getElementById('deleteForm');
            deleteForm.action = "{{ route('gym.profile.destroy') }}";
        });

        // Tab switching style adjustments for dark blue theme
        const viewTab = document.getElementById('view-tab');
        const editTab = document.getElementById('edit-tab');

        viewTab.addEventListener('click', function() {
            this.style.background = '#1e3a8a';
            this.classList.add('text-white');
            this.classList.remove('text-dark');
            editTab.style.background = 'transparent';
            editTab.classList.remove('text-white');
            editTab.classList.add('text-dark');
        });

        editTab.addEventListener('click', function() {
            this.style.background = '#1e3a8a';
            this.classList.add('text-white');
            this.classList.remove('text-dark');
            viewTab.style.background = 'transparent';
            viewTab.classList.remove('text-white');
            viewTab.classList.add('text-dark');
        });

        // Live validation for Gmail and Phone
        const emailInput = document.getElementById('edit-email');
        const emailError = document.getElementById('emailError');
        
        const phoneInput = document.getElementById('edit-phone');
        const phoneError = document.getElementById('phoneError');
        const updateBtn = document.getElementById('updateBtn');

        function validateInputs() {
            let isValid = true;

            // Gmail validation regex ensuring it ends with @gmail.com
            const emailRegex = /^[^\s@]+@gmail\.com$/i;
            if (emailInput.value && !emailRegex.test(emailInput.value)) {
                emailError.classList.remove('d-none');
                emailInput.classList.add('is-invalid');
                isValid = false;
            } else {
                emailError.classList.add('d-none');
                emailInput.classList.remove('is-invalid');
            }

            // Indian phone validation regex (10 digits starting with 6-9)
            const phoneRegex = /^[6-9]\d{9}$/;
            if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
                phoneError.classList.remove('d-none');
                phoneInput.classList.add('is-invalid');
                isValid = false;
            } else {
                phoneError.classList.add('d-none');
                phoneInput.classList.remove('is-invalid');
            }

            return isValid;
        }

        emailInput.addEventListener('input', validateInputs);
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            validateInputs();
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!validateInputs()) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection