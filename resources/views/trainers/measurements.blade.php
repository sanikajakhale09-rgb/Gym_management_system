@extends('auth.sidebar')

@section('title', 'Body Measurements & Tracking')

@section('content_header')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-4 border-indigo">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-ruler-combined text-indigo me-2"></i> Body Measurements Tracking</h4>
            <p class="text-muted small mb-0">Record and track date-wise physical measurements and body composition.</p>
        </div>
        <a href="{{ route('admin.panel') }}" class="btn btn-outline-secondary btn-sm px-4 shadow-sm rounded-pill fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection

@section('content')
<style>
    .bg-custom-header {
        background: linear-gradient(135deg, #2b2b40 0%, #3f3d99 100%) !important;
        padding: 1rem 1.5rem !important;
    }
    .text-indigo { color: #3f3d99 !important; }
    .border-indigo { border-color: #3f3d99 !important; }
    .form-container-wrapper { 
        max-width: 950px; 
        margin: 0 auto; 
    }
    .form-control, .form-select {
        border: 1px solid #dee2e6;
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3f3d99;
        box-shadow: 0 0 0 0.25rem rgba(63, 61, 153, 0.12);
    }
    .card { border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.04) !important; border-radius: 0.75rem !important; }
    .card-body { padding: 1.5rem !important; }
    .form-label { margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #495057; }
</style>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="form-container-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4 py-3 small" role="alert">
                        <i class="fas fa-check-circle me-2 fs-5 align-middle"></i> <span class="align-middle">{{ session('success') }}</span>
                        <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4 py-3 small" role="alert">
                        <i class="fas fa-exclamation-circle me-2 fs-5 align-middle"></i> <strong class="align-middle">Please fix the input errors below.</strong>
                        <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Add Measurement Form -->
                <form id="measurementForm" action="{{ route('fitness.measurements.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="gym_id" value="{{ auth()->user()->gym_id ?? 1 }}">
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? 1 }}">

                    <!-- Section 1: Member & Date Selection -->
                    <div class="card overflow-hidden mb-4">
                        <div class="card-header bg-custom-header text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-user-tag me-2"></i> <span id="formTitleText">Select Member & Date</span></h6>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Select Member <span class="text-danger">*</span></label>
                                    <select name="member_id" id="memberSelect" class="form-select rounded-2 @error('member_id') is-invalid @enderror" required>
                                        <option value="">Select Member Name / Member ID</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ (old('member_id') == $member->id || (isset($selectedMemberId) && $selectedMemberId == $member->id)) ? 'selected' : '' }}>
                                                {{ $member->name }} (ID: GYM-{{ $member->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('member_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Measurement Date <span class="text-danger">*</span></label>
                                    <input type="date" name="measurement_date" id="measurement_date" class="form-control rounded-2 @error('measurement_date') is-invalid @enderror" value="{{ old('measurement_date', date('Y-m-d')) }}" required>
                                    @error('measurement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Basic Measurements -->
                    <div class="card overflow-hidden mb-4">
                        <div class="card-header bg-custom-header text-white">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-weight me-2"></i> Basic Measurements</h6>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="weight" id="weight" class="form-control rounded-2" placeholder="Enter weight in kg" value="{{ old('weight') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Height (cm) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="height" id="height" class="form-control rounded-2" placeholder="Enter height in cm" value="{{ old('height') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">BMI <span class="text-muted small">(Auto)</span></label>
                                    <input type="text" name="bmi" id="bmi" class="form-control rounded-2 bg-light" placeholder="Auto calculated" value="{{ old('bmi') }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Body Fat (%)</label>
                                    <input type="number" step="0.1" name="body_fat" id="body_fat" class="form-control rounded-2" placeholder="Enter body fat percentage" value="{{ old('body_fat') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Body Measurements Details -->
                    <div class="card overflow-hidden mb-4">
                        <div class="card-header bg-custom-header text-white">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-child me-2"></i> Body Dimensions</h6>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Chest (cm)</label>
                                    <input type="number" step="0.1" name="chest" id="chest" class="form-control rounded-2" placeholder="Enter chest measurement in cm" value="{{ old('chest') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Waist (cm)</label>
                                    <input type="number" step="0.1" name="waist" id="waist" class="form-control rounded-2" placeholder="Enter waist measurement in cm" value="{{ old('waist') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hip (cm)</label>
                                    <input type="number" step="0.1" name="hip" id="hip" class="form-control rounded-2" placeholder="Enter hip measurement in cm" value="{{ old('hip') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Biceps (cm)</label>
                                    <input type="number" step="0.1" name="biceps" id="biceps" class="form-control rounded-2" placeholder="Enter biceps measurement in cm" value="{{ old('biceps') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thigh (cm)</label>
                                    <input type="number" step="0.1" name="thigh" id="thigh" class="form-control rounded-2" placeholder="Enter thigh measurement in cm" value="{{ old('thigh') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" id="submitBtn" class="btn px-5 py-3 shadow rounded-pill fw-bold text-white" style="background-color: #3f3d99;">
                            <i class="fas fa-save me-2"></i> <span id="submitBtnText">Save Measurement Entry</span>
                        </button>
                    </div>
                </form>

                <!-- Section 4: Date-wise History Table -->
                <div class="card overflow-hidden mt-5">
                    <div class="card-header bg-custom-header text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i> Date-wise Measurement History</h6>
                        <span class="badge bg-light text-dark" id="historyCount">Select member to view history</span>
                    </div>
                    <div class="card-body bg-white p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center small" id="historyTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Weight</th>
                                        <th>BMI</th>
                                        <th>Chest</th>
                                        <th>Waist</th>
                                        <th>Hip</th>
                                        <th>Biceps</th>
                                        <th>Thigh</th>
                                        <th>Body Fat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody">
                                    <tr>
                                        <td colspan="10" class="text-muted py-4">Please select a member above to load historical measurement records.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- View Measurement Modal -->
<div class="modal fade" id="viewMeasurementModal" tabindex="-1" aria-labelledby="viewMeasurementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-custom-header text-white">
                <h5 class="modal-title fw-bold" id="viewMeasurementModalLabel"><i class="fas fa-eye me-2"></i> View Body Measurement Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Measurement Date:</strong> <span id="view_measurement_date" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Weight:</strong> <span id="view_weight" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Height:</strong> <span id="view_height" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>BMI:</strong> <span id="view_bmi" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Body Fat:</strong> <span id="view_body_fat" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Chest:</strong> <span id="view_chest" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Waist:</strong> <span id="view_waist" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Hip:</strong> <span id="view_hip" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Biceps:</strong> <span id="view_biceps" class="text-dark"></span></div>
                    <div class="col-md-6"><strong>Thigh:</strong> <span id="view_thigh" class="text-dark"></span></div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Measurement Modal -->
<div class="modal fade" id="editMeasurementModal" tabindex="-1" aria-labelledby="editMeasurementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="editModalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-custom-header text-white">
                    <h5 class="modal-title fw-bold" id="editMeasurementModalLabel"><i class="fas fa-edit me-2"></i> Edit Body Measurement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <input type="hidden" name="member_id" id="edit_member_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Measurement Date <span class="text-danger">*</span></label>
                            <input type="date" name="measurement_date" id="edit_measurement_date" class="form-control rounded-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Weight (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="weight" id="edit_weight" class="form-control rounded-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Height (cm) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="height" id="edit_height" class="form-control rounded-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Body Fat (%)</label>
                            <input type="number" step="0.1" name="body_fat" id="edit_body_fat" class="form-control rounded-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chest (cm)</label>
                            <input type="number" step="0.1" name="chest" id="edit_chest" class="form-control rounded-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Waist (cm)</label>
                            <input type="number" step="0.1" name="waist" id="edit_waist" class="form-control rounded-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hip (cm)</label>
                            <input type="number" step="0.1" name="hip" id="edit_hip" class="form-control rounded-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Biceps (cm)</label>
                            <input type="number" step="0.1" name="biceps" id="edit_biceps" class="form-control rounded-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thigh (cm)</label>
                            <input type="number" step="0.1" name="thigh" id="edit_thigh" class="form-control rounded-2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #3f3d99;">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMeasurementModal" tabindex="-1" aria-labelledby="deleteMeasurementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="deleteModalForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteMeasurementModalLabel"><i class="fas fa-trash-alt me-2"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                    <p class="mb-0 fw-semibold">Are you sure you want to delete this measurement record? This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const weightInput = document.getElementById('weight');
        const heightInput = document.getElementById('height');
        const bmiInput = document.getElementById('bmi');
        const memberSelect = document.getElementById('memberSelect');
        const historyTableBody = document.getElementById('historyTableBody');
        const historyCount = document.getElementById('historyCount');

        function calculateBMI() {
            const weight = parseFloat(weightInput.value);
            const heightCm = parseFloat(heightInput.value);

            if (weight > 0 && heightCm > 0) {
                const heightM = heightCm / 100;
                const bmi = weight / (heightM * heightM);
                bmiInput.value = bmi.toFixed(1);
            } else {
                bmiInput.value = '';
            }
        }

        weightInput.addEventListener('input', calculateBMI);
        heightInput.addEventListener('input', calculateBMI);

        // पेज लोड करताना आधीच मेम्बर सिलेक्टेड असेल (जसे की डिलीट झाल्यावर), तर त्याची हिस्ट्री आटोमॅटिक लोड करा
        if (memberSelect && memberSelect.value) {
            loadMemberHistory(memberSelect.value);
        }

        // Fetch history when member changes
        if (memberSelect) {
            memberSelect.addEventListener('change', function() {
                loadMemberHistory(this.value);
            });
        }
    });

    function loadMemberHistory(memberId) {
        const historyTableBody = document.getElementById('historyTableBody');
        const historyCount = document.getElementById('historyCount');

        if (!memberId) {
            historyTableBody.innerHTML = `<tr><td colspan="10" class="text-muted py-4">Please select a member above to load historical measurement records.</td></tr>`;
            historyCount.textContent = 'Select member to view history';
            return;
        }

        historyTableBody.innerHTML = `<tr><td colspan="10" class="text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i> Loading history...</td></tr>`;

        fetch(`{{ url('/dashboard/body-measurements/member') }}/${memberId}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    historyCount.textContent = `${data.length} Records Found`;
                    let rows = '';
                    data.forEach(item => {
                        rows += `
                            <tr>
                                <td class="fw-semibold">${item.measurement_date}</td>
                                <td>${item.weight} kg</td>
                                <td><span class="badge bg-secondary">${item.BMI ?? '-'}</span></td>
                                <td>${item.chest ?? '-'}</td>
                                <td>${item.waist ?? '-'}</td>
                                <td>${item.hip ?? '-'}</td>
                                <td>${item.biceps ?? '-'}</td>
                                <td>${item.thigh ?? '-'}</td>
                                <td>${item.body_fat ? item.body_fat + '%' : '-'}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 fw-bold" onclick="viewMeasurement(${item.id})" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fw-bold" onclick="editMeasurement(${item.id})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold" onclick="deleteMeasurement(${item.id})" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    historyTableBody.innerHTML = rows;
                } else {
                    historyCount.textContent = '0 Records Found';
                    historyTableBody.innerHTML = `<tr><td colspan="10" class="text-muted py-4">No measurement history found for this member.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching history:', error);
                historyTableBody.innerHTML = `<tr><td colspan="10" class="text-danger py-4">Failed to load history records.</td></tr>`;
            });
    }

    // View Modal Handler
    function viewMeasurement(id) {
        fetch(`{{ url('/dashboard/body-measurements/edit') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                if(data) {
                    document.getElementById('view_measurement_date').textContent = data.measurement_date ?? '-';
                    document.getElementById('view_weight').textContent = data.weight ? data.weight + ' kg' : '-';
                    document.getElementById('view_height').textContent = data.height ? data.height + ' cm' : '-';
                    document.getElementById('view_bmi').textContent = data.BMI ?? '-';
                    document.getElementById('view_body_fat').textContent = data.body_fat ? data.body_fat + '%' : '-';
                    document.getElementById('view_chest').textContent = data.chest ? data.chest + ' cm' : '-';
                    document.getElementById('view_waist').textContent = data.waist ? data.waist + ' cm' : '-';
                    document.getElementById('view_hip').textContent = data.hip ? data.hip + ' cm' : '-';
                    document.getElementById('view_biceps').textContent = data.biceps ? data.biceps + ' cm' : '-';
                    document.getElementById('view_thigh').textContent = data.thigh ? data.thigh + ' cm' : '-';

                    var viewModal = new bootstrap.Modal(document.getElementById('viewMeasurementModal'));
                    viewModal.show();
                }
            })
            .catch(error => console.error('Error loading measurement for view:', error));
    }

    // Edit button click handler (Opens Modal)
    function editMeasurement(id) {
        fetch(`{{ url('/dashboard/body-measurements/edit') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                if(data) {
                    const editModalForm = document.getElementById('editModalForm');
                    editModalForm.action = `{{ url('/dashboard/body-measurements/update') }}/${id}`;
                    
                    document.getElementById('edit_member_id').value = data.member_id;
                    document.getElementById('edit_measurement_date').value = data.measurement_date;
                    document.getElementById('edit_weight').value = data.weight;
                    document.getElementById('edit_height').value = data.height;
                    document.getElementById('edit_body_fat').value = data.body_fat ?? '';
                    document.getElementById('edit_chest').value = data.chest ?? '';
                    document.getElementById('edit_waist').value = data.waist ?? '';
                    document.getElementById('edit_hip').value = data.hip ?? '';
                    document.getElementById('edit_biceps').value = data.biceps ?? '';
                    document.getElementById('edit_thigh').value = data.thigh ?? '';

                    var myModal = new bootstrap.Modal(document.getElementById('editMeasurementModal'));
                    myModal.show();
                }
            })
            .catch(error => console.error('Error loading measurement for edit:', error));
    }

    // Delete Modal Handler
   function deleteMeasurement(id) {
        const deleteModalForm = document.getElementById('deleteModalForm');
        deleteModalForm.action = `/dashboard/body-measurements/${id}`;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteMeasurementModal'));
        deleteModal.show();
    }
</script>
@endsection