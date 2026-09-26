@extends('auth.sidebar')

@section('title', 'Member Fitness Goals')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f6f9; min-height: 100vh;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Please fix the errors below:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form to Add/Edit Fitness Goal -->
        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white p-3 rounded-top-4 d-flex justify-content-between align-items-center" style="background-color: #0d233a;">
                    <h5 class="mb-0 fw-bold" id="form-title"><i class="fas fa-bullseye me-2"></i> Set Fitness Goal</h5>
                    <button type="button" id="reset-form-btn" class="btn btn-sm btn-light text-dark" style="display: none;">Cancel Edit</button>
                </div>
                <div class="card-body p-4">
                    <form id="goal-form" action="{{ route('fitness-goals.store') }}" method="POST">
                        @csrf
                        <div id="method-field"></div>
                        
                        <!-- Select Member -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Select Member <span class="text-danger">*</span></label>
                            <select name="member_id" id="memberSelect" class="form-select rounded-3 shadow-sm" required>
                                <option value="">Choose Member</option>
                                @if(isset($members))
                                  @foreach($members as $member)
                                        @php
                                            $activeMembership = $member->activeMembership ?? ($member->memberships ? $member->memberships()->latest()->first() : null);$latestGoal = $member->fitnessGoals ? $member->fitnessGoals->last() : null;
                                        @endphp
                                        <option value="{{ $member->id }}" 
                                            data-trainer-id="{{ $member->trainer_id ?? '' }}" 
                                            data-trainer-name="{{ $member->trainer->name ?? 'No Trainer Assigned' }}"
                                            data-goal-type="{{ $latestGoal->goal_type ?? ($member->goal ?? '') }}"
                                            data-starting-value="{{ $latestGoal->starting_value ?? '' }}"
                                            data-current-value="{{ $latestGoal->current_value ?? '' }}"
                                            data-target-value="{{ $latestGoal->target_value ?? '' }}"
                                            data-target-date="{{ $latestGoal->target_date ?? '' }}"
                                            data-membership-start="{{ $activeMembership->start_date ?? '' }}"
                                            data-membership-end="{{ $activeMembership->end_date ?? '' }}">
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Membership Duration Info Box -->
                        <div id="membership-info-box" class="alert alert-info py-2 px-3 small rounded-3 mb-3" style="display: none;">
                            <i class="fas fa-id-card me-1"></i> <strong>Membership Duration:</strong> <span id="membershipDurationText">N/A</span>
                        </div>

                        <!-- Auto-loaded Assigned Trainer -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Assigned Trainer</label>
                            <input type="text" id="assignedTrainerDisplay" class="form-control bg-light rounded-3 shadow-sm text-muted" value="Auto-selected from member" readonly>
                            <input type="hidden" name="trainer_id" id="trainerIdInput" value="{{ old('trainer_id') }}">
                        </div>

                        <!-- Goal Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Goal Type <span class="text-danger">*</span></label>
                            <select name="goal_type" id="goal_type" class="form-select rounded-3 shadow-sm" required>
                                <option value="Weight Loss">Weight Loss</option>
                                <option value="Weight Gain">Weight Gain</option>
                                <option value="Muscle Gain">Muscle Gain</option>
                                <option value="Strength">Strength</option>
                                <option value="General Fitness">General Fitness</option>
                                <option value="Bodybuilding">Bodybuilding</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Starting Value <span class="text-danger">*</span></label>
                                <input type="text" name="starting_value" id="starting_value" class="form-control rounded-3 shadow-sm" placeholder="Starting weight" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Current Value <span class="text-danger">*</span></label>
                                <input type="text" name="current_value" id="current_value" class="form-control rounded-3 shadow-sm" placeholder="Current weight" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Target Value <span class="text-danger">*</span></label>
                                <input type="text" name="target_value" id="target_value" class="form-control rounded-3 shadow-sm" placeholder="Target weight" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-secondary">Target Date</label>
                                <input type="date" name="target_date" id="target_date" class="form-control rounded-3 shadow-sm">
                            </div>
                        </div>

                        <!-- Start Date Field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control rounded-3 shadow-sm" value="{{ date('Y-m-d') }}" required>
                            <!-- Warning text if target date exceeds membership end date or start date is invalid -->
                            <small id="membership-warning" class="text-danger fw-semibold mt-1" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i> Warning: Target date exceeds the member's current membership expiry date!
                            </small>
                        </div>

                        <!-- Goal Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Goal Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select rounded-3 shadow-sm" required>
                                <option value="Active">Active</option>
                                <option value="Achieved">Achieved</option>
                                <option value="Dropped">Dropped</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Notes</label>
                            <textarea name="notes" id="notes" class="form-control rounded-3 shadow-sm" rows="2" placeholder="Optional notes..."></textarea>
                        </div>

                        <button type="submit" id="submit-btn" class="btn w-100 fw-bold py-2 rounded-pill text-white shadow-sm" style="background-color: #0d233a;">
                            <i class="fas fa-save me-2"></i> Save Goal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List of Fitness Goals -->
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #0d233a;"><i class="fas fa-list me-2"></i> Members Fitness Goals List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-white text-uppercase fs-7" style="background-color: #0d233a;">
                                <tr>
                                    <th class="ps-4">Member</th>
                                    <th>Trainer</th>
                                    <th>Goal Type</th>
                                    <th>Start / Current / Target & Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($goals))
                                    @forelse($goals as $goal)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-dark">{{ $goal->member->name ?? 'N/A' }}</td>
                                        <td class="text-muted">{{ $goal->trainer->name ?? ($goal->member->trainer->name ?? 'Not Assigned') }}</td>
                                        <td><span class="badge bg-info text-dark px-2 py-1">{{ $goal->goal_type ?? '' }}</span></td>
                                        <td>
                                            <span class="text-muted small">S: {{ $goal->starting_value }}</span> | 
                                            <span class="text-dark fw-bold">C: {{ $goal->current_value }}</span> | 
                                            <span class="text-success fw-bold">T: {{ $goal->target_value }}</span>
                                            
                                            @if(!empty($goal->start_date))
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-calendar-plus me-1 text-primary"></i> Start: {{ \Carbon\Carbon::parse($goal->start_date)->format('d M, Y') }}
                                                </div>
                                            @endif

                                            @if(!empty($goal->target_date))
                                                <div class="small text-muted">
                                                    <i class="fas fa-calendar-check me-1 text-success"></i> Target: {{ \Carbon\Carbon::parse($goal->target_date)->format('d M, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'secondary';
                                                if(($goal->status ?? '') == 'Active') $statusClass = 'success';
                                                elseif(($goal->status ?? '') == 'Achieved') $statusClass = 'primary';
                                                elseif(($goal->status ?? '') == 'Dropped') $statusClass = 'danger';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} px-2 py-1">
                                                {{ $goal->status ?? 'Active' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-start-pill px-2 edit-goal-btn" 
                                                    data-id="{{ $goal->id }}"
                                                    data-member-id="{{ $goal->member_id }}"
                                                    data-trainer-id="{{ $goal->trainer_id }}"
                                                    data-trainer-name="{{ $goal->trainer->name ?? ($goal->member->trainer->name ?? 'No Trainer Assigned') }}"
                                                    data-goal-type="{{ $goal->goal_type }}"
                                                    data-starting-value="{{ $goal->starting_value }}"
                                                    data-current-value="{{ $goal->current_value }}"
                                                    data-target-value="{{ $goal->target_value }}"
                                                    data-target-date="{{ $goal->target_date }}"
                                                    data-start-date="{{ $goal->start_date ?? date('Y-m-d') }}"
                                                    data-status="{{ $goal->status }}"
                                                    data-notes="{{ $goal->notes }}"
                                                    title="Edit Goal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form action="{{ route('fitness-goals.destroy', $goal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this goal?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-end-pill px-2" title="Delete Goal">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fas fa-folder-open fa-2x mb-2 text-black-50"></i>
                                            <p class="mb-0">No fitness goals found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Membership Date Tracking & Warnings -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const memberSelect = document.getElementById('memberSelect');
        const goalForm = document.getElementById('goal-form');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('submit-btn');
        const resetFormBtn = document.getElementById('reset-form-btn');
        const methodField = document.getElementById('method-field');
        const trainerDisplayInput = document.getElementById('assignedTrainerDisplay');
        const trainerIdInput = document.getElementById('trainerIdInput');
        const startDateInput = document.getElementById('start_date');
        const targetDateInput = document.getElementById('target_date');
        const membershipInfoBox = document.getElementById('membership-info-box');
        const membershipDurationText = document.getElementById('membershipDurationText');
        const membershipWarning = document.getElementById('membership-warning');

        let currentMembershipEndDate = null;

        // Function to check date logic and membership expiry
        function validateMembershipExpiry() {
            const startDate = new Date(startDateInput.value);
            const targetDate = new Date(targetDateInput.value);

            // 1. Check if Start Date is after Target Date
            if (startDateInput.value && targetDateInput.value && startDate > targetDate) {
                membershipWarning.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: Start Date cannot be after Target Date!';
                membershipWarning.style.display = 'block';
            } 
            // 2. Check if Target Date exceeds membership expiry
            else if (currentMembershipEndDate && targetDateInput.value) {
                if (new Date(targetDateInput.value) > new Date(currentMembershipEndDate)) {
                    membershipWarning.innerHTML = '<i class="fas fa-exclamation-circle"></i> Warning: Target date exceeds the member\'s current membership expiry date!';
                    membershipWarning.style.display = 'block';
                } else {
                    membershipWarning.style.display = 'none';
                }
            } else {
                membershipWarning.style.display = 'none';
            }
        }

        // Add event listeners for both fields
        targetDateInput.addEventListener('change', validateMembershipExpiry);
        startDateInput.addEventListener('change', validateMembershipExpiry);

        // Member Selection Change Event
        memberSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var trainerName = selectedOption.getAttribute('data-trainer-name');
            var trainerId = selectedOption.getAttribute('data-trainer-id');
            var goalType = selectedOption.getAttribute('data-goal-type');
            var startingValue = selectedOption.getAttribute('data-starting-value');
            var currentValue = selectedOption.getAttribute('data-current-value');
            var targetValue = selectedOption.getAttribute('data-target-value');
            var targetDate = selectedOption.getAttribute('data-target-date');
            var memStart = selectedOption.getAttribute('data-membership-start');
            var memEnd = selectedOption.getAttribute('data-membership-end');

            if (this.value === "") {
                trainerDisplayInput.value = "Auto-selected from member";
                trainerIdInput.value = "";
                membershipInfoBox.style.display = 'none';
                currentMembershipEndDate = null;
                membershipWarning.style.display = 'none';
                document.getElementById('goal_type').value = "Weight Loss";
                document.getElementById('starting_value').value = "";
                document.getElementById('current_value').value = "";
                document.getElementById('target_value').value = "";
                targetDateInput.value = "";
            } else {
                trainerDisplayInput.value = trainerName ? trainerName : "No Trainer Assigned";
                trainerIdInput.value = trainerId ? trainerId : "";

                // Show Membership Info
                if (memStart && memEnd) {
                    membershipDurationText.innerText = `${memStart} to ${memEnd} (Active Membership)`;
                    membershipInfoBox.style.display = 'block';
                    currentMembershipEndDate = memEnd;
                } else {
                    membershipDurationText.innerText = "No active membership found for this member.";
                    membershipInfoBox.style.display = 'block';
                    currentMembershipEndDate = null;
                }

                if (goalType) document.getElementById('goal_type').value = goalType;
                if (startingValue) document.getElementById('starting_value').value = startingValue;
                if (currentValue) document.getElementById('current_value').value = currentValue;
                if (targetValue) document.getElementById('target_value').value = targetValue;
                if (targetDate) targetDateInput.value = targetDate;

                validateMembershipExpiry();
            }
        });

        // Edit Button Click Event
        document.querySelectorAll('.edit-goal-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const goalId = this.getAttribute('data-id');
                const memberId = this.getAttribute('data-member-id');
                const trainerId = this.getAttribute('data-trainer-id');
                const trainerName = this.getAttribute('data-trainer-name');
                const goalType = this.getAttribute('data-goal-type');
                const startingValue = this.getAttribute('data-starting-value');
                const currentValue = this.getAttribute('data-current-value');
                const targetValue = this.getAttribute('data-target-value');
                const targetDate = this.getAttribute('data-target-date');
                const startDate = this.getAttribute('data-start-date');
                const status = this.getAttribute('data-status');
                const notes = this.getAttribute('data-notes');

                // Fill form fields
                memberSelect.value = memberId;
                
                // Trigger change event to load membership details for this member automatically
                const event = new Event('change');
                memberSelect.dispatchEvent(event);

                trainerIdInput.value = trainerId || '';
                trainerDisplayInput.value = trainerName || 'No Trainer Assigned';
                document.getElementById('goal_type').value = goalType || 'Weight Loss';
                document.getElementById('starting_value').value = startingValue || '';
                document.getElementById('current_value').value = currentValue || '';
                document.getElementById('target_value').value = targetValue || '';
                targetDateInput.value = targetDate || '';
                if(startDateInput) startDateInput.value = startDate || '';
                document.getElementById('status').value = status || 'Active';
                document.getElementById('notes').value = notes || '';

                // Change Form Action
                goalForm.action = `/dashboard/fitness/goals/${goalId}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                // Update UI elements for edit mode
                formTitle.innerHTML = '<i class="fas fa-edit me-2"></i> Edit Fitness Goal';
                submitBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i> Update Goal';
                resetFormBtn.style.display = 'block';

                goalForm.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Reset/Cancel Edit Mode
        resetFormBtn.addEventListener('click', function() {
            goalForm.reset();
            goalForm.action = "{{ route('fitness-goals.store') }}";
            methodField.innerHTML = '';
            formTitle.innerHTML = '<i class="fas fa-bullseye me-2"></i> Set Fitness Goal';
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Save Goal';
            trainerDisplayInput.value = "Auto-selected from member";
            trainerIdInput.value = '';
            membershipInfoBox.style.display = 'none';
            membershipWarning.style.display = 'none';
            currentMembershipEndDate = null;
            resetFormBtn.style.display = 'none';
        });
    });
</script>
@endsection