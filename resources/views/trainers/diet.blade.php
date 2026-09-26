@extends('auth.sidebar')

@section('title', 'Add Diet Plan & Fitness Details')

@section('content_header')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-4 border-indigo">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-utensils text-indigo me-2"></i> Add Diet Plan & Fitness Tracking</h4>
            <p class="text-muted small mb-0">Create customized meal plans, set fitness goals, and record initial measurements.</p>
        </div>
        <a href="{{ route('diet-plans.index') }}" class="btn btn-outline-secondary btn-sm px-4 shadow-sm rounded-pill fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> Back to List
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
    .text-indigo {
        color: #3f3d99 !important;
    }
    .border-indigo {
        border-color: #3f3d99 !important;
    }
    .form-container-wrapper {
        max-width: 950px;
        margin: 0 auto;
    }
    .form-control, .form-select {
        border: 1px solid #dee2e6;
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3f3d99;
        box-shadow: 0 0 0 0.25rem rgba(63, 61, 153, 0.12);
    }
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.04) !important;
        border-radius: 0.75rem !important;
    }
    .card-body {
        padding: 1.5rem !important;
    }
    .form-label {
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
    }
    .bg-light-warning {
        background-color: #fff9e6 !important;
        border: 1px solid #ffeeba !important;
    }
</style>

<div class="container pb-5">
    <div class="form-container-wrapper">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4 py-3 small" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5 align-middle"></i> <strong class="align-middle">Please fix the input errors below.</strong>
                <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('diet-plans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Hidden Multi-Tenant Security Fields -->
            <input type="hidden" name="gym_id" value="{{ auth()->user()->gym_id }}">
            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">

            <!-- Section 1: Basic Assignment & Plan Details -->
            <div class="card overflow-hidden mb-4">
                <div class="card-header bg-custom-header text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-user-tag me-2"></i> Assignment & Plan Info</h6>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Select Member <span class="text-danger">*</span></label>
                            <select name="member_id" id="memberSelect" class="form-select rounded-2 @error('member_id') is-invalid @enderror" required>
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    @php
                                        $latestGoal = optional($member->fitnessGoals)->last();
                                    @endphp
                                    <option value="{{ $member->id }}" 
                                        data-trainer-id="{{ $member->trainer_id ?? '' }}" 
                                        data-goal-type="{{ $latestGoal->goal_type ?? ($member->goal ?? '') }}"
                                        data-starting-value="{{ $latestGoal->starting_value ?? '' }}"
                                        data-current-value="{{ $latestGoal->current_value ?? '' }}"
                                        data-target-value="{{ $latestGoal->target_value ?? '' }}"
                                        {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} (ID: {{ $member->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Assigned Trainer <span class="text-danger">*</span></label>
                            <select name="trainer_id" id="trainerSelect" class="form-select rounded-2 @error('trainer_id') is-invalid @enderror" required>
                                <option value="">Select Trainer</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('trainer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Plan Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control rounded-2 @error('title') is-invalid @enderror" placeholder="Enter plan title" value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control rounded-2 @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Diet Plan Preview/Warning Box -->
            <div id="active-plan-container" class="mb-4" style="display: none;">
                <div class="alert shadow-sm rounded-3 p-4 bg-light-warning">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i> Active Diet Plan Found for <span id="active-member-name" class="text-indigo"></span>
                        </h6>
                        <span class="badge bg-danger px-3 py-2 rounded-pill">Already Assigned</span>
                    </div>
                    <p class="mb-2 text-secondary small">
                        <strong>Plan Title:</strong> <span id="active-plan-title" class="fw-bold text-dark"></span> &nbsp;|&nbsp; 
                        <strong>Start Date:</strong> <span id="active-plan-date" class="fw-bold text-dark"></span>
                    </p>
                    <div class="small text-muted bg-white p-3 rounded-3 border" id="active-plan-meals">
                        <!-- Meals summary will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Section 2: Diet Plan (Meal-wise structure) -->
            <div class="card overflow-hidden mb-4">
                <div class="card-header bg-custom-header text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-utensils me-2"></i> Structured Meal-wise Diet Plan</h6>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Breakfast</label>
                            <textarea name="breakfast" id="breakfast" class="form-control rounded-2" rows="2" placeholder="Enter breakfast details">{{ old('breakfast') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mid-Morning</label>
                            <textarea name="mid_morning" id="mid_morning" class="form-control rounded-2" rows="2" placeholder="Enter mid-morning meal details">{{ old('mid_morning') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lunch</label>
                            <textarea name="lunch" id="lunch" class="form-control rounded-2" rows="2" placeholder="Enter lunch details">{{ old('lunch') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Evening Snack</label>
                            <textarea name="evening" id="evening" class="form-control rounded-2" rows="2" placeholder="Enter evening snack details">{{ old('evening') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dinner</label>
                            <textarea name="dinner" id="dinner" class="form-control rounded-2" rows="2" placeholder="Enter dinner details">{{ old('dinner') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Fitness Goals & Body Measurements (Two Columns) -->
            <div class="row g-4 mb-4">
                <!-- Fitness Goals -->
                <div class="col-md-6">
                    <div class="card h-100 overflow-hidden">
                        <div class="card-header bg-custom-header text-white">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-bullseye me-2"></i> Fitness Goals</h6>
                        </div>
                        <div class="card-body bg-white d-flex flex-column justify-content-between">
                            <div class="mb-3">
                                <label class="form-label">Goal Type</label>
                                <select name="goal_type" id="goal_type" class="form-select rounded-2">
                                    <option value="Weight Loss">Weight Loss</option>
                                    <option value="Weight Gain">Weight Gain</option>
                                    <option value="Muscle Gain">Muscle Gain</option>
                                    <option value="Strength">Strength</option>
                                    <option value="General Fitness">General Fitness</option>
                                    <option value="Bodybuilding">Bodybuilding</option>
                                </select>
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label">Start Value</label>
                                    <input type="text" name="starting_value" id="starting_value" class="form-control rounded-2" placeholder="Enter starting value" value="{{ old('starting_value') }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Current Value</label>
                                    <input type="text" name="current_value" id="current_value" class="form-control rounded-2" placeholder="Enter current value" value="{{ old('current_value') }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Target Value</label>
                                    <input type="text" name="target_value" id="target_value" class="form-control rounded-2" placeholder="Enter target value" value="{{ old('target_value') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body Measurements -->
                <div class="col-md-6">
                    <div class="card h-100 overflow-hidden">
                        <div class="card-header bg-custom-header text-white">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-ruler-combined me-2"></i> Body Measurements</h6>
                        </div>
                        <div class="card-body bg-white">
                            <div class="mb-3">
                                <label class="form-label">Measurement Date</label>
                                <input type="date" name="measurement_date" id="measurementDate" class="form-control rounded-2" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label">Weight</label>
                                    <input type="number" step="0.1" name="weight" id="weight" class="form-control rounded-2" placeholder="Enter weight in kg">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Height</label>
                                    <input type="number" step="0.1" name="height" id="height" class="form-control rounded-2" placeholder="Enter height in cm">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">BMI</label>
                                    <input type="number" step="0.01" name="bmi" id="bmi" class="form-control rounded-2" placeholder="Auto calculated">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Chest</label>
                                    <input type="text" name="chest" id="chest" class="form-control rounded-2" placeholder="Enter chest measurement">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Waist</label>
                                    <input type="text" name="waist" id="waist" class="form-control rounded-2" placeholder="Enter waist measurement">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Biceps</label>
                                    <input type="text" name="biceps" id="biceps" class="form-control rounded-2" placeholder="Enter biceps measurement">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Progress Photos -->
            <div class="card overflow-hidden mb-4">
                <div class="card-header bg-custom-header text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-camera me-2"></i> Progress Photos</h6>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Before Photo</label>
                            <input type="file" name="before_photo" class="form-control rounded-2" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">After Photo</label>
                            <input type="file" name="after_photo" class="form-control rounded-2" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn px-5 py-3 shadow rounded-pill fw-bold text-white" style="background-color: #3f3d99; font-size: 1rem;">
                    <i class="fas fa-save me-2"></i> Save Diet Plan & Tracking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const memberSelect = document.getElementById('memberSelect');
        const trainerSelect = document.getElementById('trainerSelect');
        const activePlanContainer = document.getElementById('active-plan-container');
        const dateInput = document.getElementById('measurementDate');

        // Function to fetch past body measurements by member and date
        function fetchMeasurements() {
            const memberId = memberSelect ? memberSelect.value : null;
            const date = dateInput ? dateInput.value : null;

            if (!memberId || !date) return;

            fetch(`/dashboard/body-measurements/fetch-by-date/${memberId}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        if(document.getElementById('weight')) document.getElementById('weight').value = data.weight || '';
                        if(document.getElementById('height')) document.getElementById('height').value = data.height || '';
                        if(document.getElementById('bmi')) document.getElementById('bmi').value = data.BMI || data.bmi || '';
                        if(document.getElementById('chest')) document.getElementById('chest').value = data.chest || '';
                        if(document.getElementById('waist')) document.getElementById('waist').value = data.waist || '';
                        if(document.getElementById('biceps')) document.getElementById('biceps').value = data.biceps || '';
                        if(document.getElementById('hip')) document.getElementById('hip').value = data.hip || '';
                        if(document.getElementById('thigh')) document.getElementById('thigh').value = data.thigh || '';
                        if(document.getElementById('body_fat')) document.getElementById('body_fat').value = data.body_fat || '';
                    } else {
                        ['weight', 'height', 'bmi', 'chest', 'waist', 'biceps', 'hip', 'thigh', 'body_fat'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.value = '';
                        });
                    }
                })
                .catch(error => console.error('Error fetching measurements:', error));
        }

        if (memberSelect) {
            memberSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const assignedTrainerId = selectedOption.getAttribute('data-trainer-id');
                const goalType = selectedOption.getAttribute('data-goal-type');
                const startingValue = selectedOption.getAttribute('data-starting-value');
                const currentValue = selectedOption.getAttribute('data-current-value');
                const targetValue = selectedOption.getAttribute('data-target-value');
                const memberId = this.value;
                const memberName = selectedOption.textContent.split(' (ID:')[0];

                // Auto-bind trainer
                if (assignedTrainerId) {
                    trainerSelect.value = assignedTrainerId;
                } else {
                    trainerSelect.value = ""; 
                }

                // Auto-fill Fitness Goal details & Plan Title
                if (goalType) {
                    document.getElementById('goal_type').value = goalType;
                    document.getElementById('title').value = goalType + ' Plan'; 
                }

                if (startingValue) document.getElementById('starting_value').value = startingValue;
                if (currentValue) document.getElementById('current_value').value = currentValue;
                if (targetValue) document.getElementById('target_value').value = targetValue;

                // Trigger measurements fetch for selected date and member
                fetchMeasurements();

                if (!memberId) {
                    if (activePlanContainer) activePlanContainer.style.display = 'none';
                    return;
                }

                // Fetch member existing/active diet plans via AJAX
                fetch(`/dashboard/diets/member/${memberId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            let activePlan = data[0]; // Latest plan

                            document.getElementById('active-member-name').textContent = memberName;
                            document.getElementById('active-plan-title').textContent = activePlan.title || 'Diet Plan';
                            document.getElementById('active-plan-date').textContent = activePlan.start_date || '-';

                            let mealsHtml = '<strong>Existing Meal Details:</strong> <ul class="mb-0 ps-3">';
                            if (activePlan.breakfast) mealsHtml += `<li><strong>Breakfast:</strong> ${activePlan.breakfast}</li>`;
                            if (activePlan.lunch) mealsHtml += `<li><strong>Lunch:</strong> ${activePlan.lunch}</li>`;
                            if (activePlan.dinner) mealsHtml += `<li><strong>Dinner:</strong> ${activePlan.dinner}</li>`;
                            mealsHtml += '</ul>';

                            document.getElementById('active-plan-meals').innerHTML = mealsHtml;
                            if (activePlanContainer) activePlanContainer.style.display = 'block';

                            if(activePlan.title) document.getElementById('title').value = activePlan.title;
                            if(document.getElementById('start_date')) document.getElementById('start_date').value = activePlan.start_date || '';
                            if(document.getElementById('breakfast')) document.getElementById('breakfast').value = activePlan.breakfast || '';
                            if(document.getElementById('mid_morning')) document.getElementById('mid_morning').value = activePlan.mid_morning || '';
                            if(document.getElementById('lunch')) document.getElementById('lunch').value = activePlan.lunch || '';
                            if(document.getElementById('evening')) document.getElementById('evening').value = activePlan.evening || '';
                            if(document.getElementById('dinner')) document.getElementById('dinner').value = activePlan.dinner || '';
                        } else {
                            if (activePlanContainer) activePlanContainer.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading diet plans:', error);
                        if (activePlanContainer) activePlanContainer.style.display = 'none';
                    });
            });
        }

        if (dateInput) {
            dateInput.addEventListener('change', fetchMeasurements);
        }
    });
</script>
@endsection