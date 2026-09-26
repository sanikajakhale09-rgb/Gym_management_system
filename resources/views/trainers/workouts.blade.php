@extends('auth.sidebar')

@section('title', 'Create Workout Plan')

@section('content_header')
<div class="container px-4 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.5rem;"><i class="fas fa-dumbbell text-indigo me-2"></i> Create Workout Plan</h2>
            <p class="text-muted small mb-0">Fill in the details to create a new workout routine for a member.</p>
        </div>
        <a href="{{ route('workouts.index') }}" class="btn btn-dark btn-sm px-3 shadow-sm rounded-pill" style="background-color: #2b2b40;">
            <i class="fas fa-arrow-left me-1"></i> Back to Workouts
        </a>
    </div>
</div>
@endsection

@section('content')
<style>
    .bg-custom-header {
        background: linear-gradient(135deg, #2b2b40 0%, #1a1a2e 100%) !important;
    }
    .text-indigo {
        color: #3f3d99 !important;
    }
    .form-control, .form-select {
        border: 1px solid #cbd5e1;
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 8px;
        background-color: #f8fafc;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2b2b40;
        background-color: #ffffff;
        box-shadow: 0 0 0 0.25rem rgba(43, 43, 64, 0.15);
    }
    .input-group-text {
        background-color: #e2e8f0;
        border: 1px solid #cbd5e1;
        color: #2b2b40;
        font-weight: 600;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    #exercises-table th, #exercises-table td {
        vertical-align: middle;
        padding: 0.5rem;
    }
    #exercises-table th {
        font-size: 0.75rem;
        white-space: nowrap;
        background-color: #f1f5f9 !important;
        color: #2b2b40;
    }
    #exercises-table td .form-control, 
    #exercises-table td .form-select {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }
    .bg-light-warning {
        background-color: #fff3cd !important;
    }
    .card {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
    }
</style>

<div class="container px-4 pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-12">
            <div class="card overflow-hidden">
                <div class="card-header bg-custom-header text-white p-3 px-4 d-flex align-items-center">
                    <i class="fas fa-clipboard-list me-2 fs-5"></i>
                    <h5 class="mb-0 fw-semibold" style="font-size: 1.1rem;">Workout Plan Registration Form</h5>
                </div>
                
                <div class="card-body p-4 p-md-5" style="background-color: #ffffff;">
                    <form action="{{ route('workouts.store') }}" method="POST">
                        @csrf
                        
                        <!-- Row 1: Member, Trainer & Title -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small">Select Member <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <select name="member_id" id="member_select" class="form-select @error('member_id') is-invalid @enderror" required>
                                        <option value="">Select Member</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" data-trainer-id="{{ $member->trainer_id ?? '' }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('member_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small">Assign Trainer <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    <select name="trainer_id" id="trainer_select" class="form-select @error('trainer_id') is-invalid @enderror" required>
                                        <option value="">Select Trainer</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('trainer_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small">Plan Title <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Enter plan title (e.g. Fat Loss / Muscle Gain)" required>
                                </div>
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Active Workout Plan Preview/Warning Box -->
                        <div id="active-plan-container" class="mb-4" style="display: none;">
                            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 bg-light-warning">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i> Active Plan Found for <span id="active-member-name" class="text-primary"></span>
                                    </h6>
                                    <span class="badge bg-danger">Already Assigned</span>
                                </div>
                                <p class="mb-1 small text-secondary">
                                    <strong>Plan Title:</strong> <span id="active-plan-title" class="fw-bold text-dark"></span> | 
                                    <strong>Duration:</strong> <span id="active-plan-dates"></span>
                                </p>
                                <div class="small text-muted" id="active-plan-exercises">
                                    <!-- Exercises list will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Goal & Level -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Fitness Goal</label>
                                <select name="goal" id="goal" class="form-select">
                                    <option value="">Select Fitness Goal</option>
                                    <option value="Muscle Gain" {{ old('goal') == 'Muscle Gain' ? 'selected' : '' }}>Muscle Gain</option>
                                    <option value="Fat Loss" {{ old('goal') == 'Fat Loss' ? 'selected' : '' }}>Fat Loss</option>
                                    <option value="Weight Loss" {{ old('goal') == 'Weight Loss' ? 'selected' : '' }}>Weight Loss</option>
                                    <option value="Strength" {{ old('goal') == 'Strength' ? 'selected' : '' }}>Strength</option>
                                    <option value="General Fitness" {{ old('goal') == 'General Fitness' ? 'selected' : '' }}>General Fitness</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Difficulty Level</label>
                                <select name="level" id="level" class="form-select">
                                    <option value="">Select Difficulty Level</option>
                                    <option value="Beginner" {{ old('level') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Dates -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Start Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    <input type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" placeholder="dd-mm-yyyy" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" required>
                                </div>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">End Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    <input type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" placeholder="dd-mm-yyyy" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" required>
                                </div>
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 4: Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">Notes (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Write any extra instructions, diet tips, or notes here...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <hr class="text-muted my-4">

                        <!-- Exercises Section -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-list-ul text-indigo me-1"></i> Add Exercises Routine</h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm text-white px-3 shadow-sm" id="add-exercise" style="background-color: #2b2b40;">
                                    <i class="fas fa-plus me-1"></i> Add More Exercise
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle shadow-sm rounded overflow-hidden" id="exercises-table" data-exercises='@json($exercises ?? [])' data-old-exercises='@json(old('exercises') ?? [])'>
                                <thead class="table-light text-uppercase text-secondary">
                                    <tr>
                                        <th style="width: 11%;">Day</th>
                                        <th style="width: 14%;">Date</th>
                                        <th style="width: 12%;">Muscle Group</th>
                                        <th style="width: 18%;">Exercise</th>
                                        <th style="width: 6%;">Sets</th>
                                        <th style="width: 6%;">Reps</th>
                                        <th style="width: 9%;">Weight</th>
                                        <th style="width: 8%;">Rest</th>
                                        <th style="width: 11%;">Instructions</th>
                                        <th style="width: 5%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows will populate via JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('workouts.index') }}" class="btn btn-light px-4 border shadow-sm">Cancel</a>
                            <button type="submit" class="btn text-white px-4 shadow-sm" style="background-color: #2b2b40;">Save Workout Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const memberSelect = document.getElementById('member_select');
        const trainerSelect = document.getElementById('trainer_select');
        const goalSelect = document.getElementById('goal');
        const levelSelect = document.getElementById('level');
        const notesInput = document.getElementById('notes');
        const titleInput = document.getElementById('title'); 
        const table = document.getElementById('exercises-table');
        
        if (!table) return;

        let exercisesData = [];
        let oldExercises = [];
        let latestFetchedExercises = []; 

        try {
            exercisesData = JSON.parse(table.getAttribute('data-exercises') || '[]');
        } catch (e) {
            console.error("Invalid exercises JSON data", e);
        }

        try {
            oldExercises = JSON.parse(table.getAttribute('data-old-exercises') || '[]');
        } catch (e) {
            console.error("Invalid old exercises JSON data", e);
        }

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const tableBody = table.querySelector('tbody');
        const addBtn = document.getElementById('add-exercise');
        const copyWorkoutBtn = document.getElementById('copy-previous-workout');

        function getDayName(dateString) {
            if (!dateString) return 'Monday';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Monday';
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[date.getDay()];
        }

        if (oldExercises && oldExercises.length > 0) {
            oldExercises.forEach((item, index) => {
                if(item.workout_date && !item.day_of_week) {
                    item.day_of_week = getDayName(item.workout_date);
                }
                appendRow(index, item);
            });
        } else {
            addNewRow(0);
        }

        if (memberSelect) {
            memberSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const trainerId = selectedOption.getAttribute('data-trainer-id');
                const memberId = this.value;
                const memberName = selectedOption.textContent;
                
                if (trainerSelect) {
                    trainerSelect.value = trainerId || "";
                }

                const activePlanContainer = document.getElementById('active-plan-container');

                if (!memberId) {
                    if (activePlanContainer) activePlanContainer.style.display = 'none';
                    if (goalSelect) goalSelect.value = '';
                    if (levelSelect) levelSelect.value = '';
                    if (notesInput) notesInput.value = '';
                    if (titleInput) titleInput.value = '';
                    latestFetchedExercises = [];
                    return;
                }

                // 1. Fetch member details (Goal, Level, Title, Dates)
                fetch(`/dashboard/workouts/member-details/${memberId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Member Details Data:", data);
                        if (data && data.status) {
                            let goalVal = data.goal || data.latest_goal || '';
                            if (goalSelect && goalVal) {
                                goalSelect.value = goalVal;
                            }
                            if (levelSelect && data.level) {
                                levelSelect.value = data.level;
                            }
                            if (titleInput) {
                                titleInput.value = data.title || (goalVal ? goalVal + ' Plan' : '');
                            }
                            if (startDateInput && data.start_date) {
                                startDateInput.value = data.start_date;
                                startDateInput.type = 'date';
                            }
                            if (endDateInput && data.target_date) {
                                endDateInput.value = data.target_date;
                                endDateInput.type = 'date';
                            }
                        }
                    })
                    .catch(error => console.error('Error loading member details:', error));

                // 2. Fetch member workout plans list for existing routines/preview
                fetch(`/dashboard/workouts/member/${memberId}`)
                    .then(response => response.json())
                    .then(data => {
                        let plansList = Array.isArray(data) ? data : (data.workout_plans || data.plans || []);
                        
                        if (data.start_date && startDateInput && !startDateInput.value) {
                            startDateInput.value = data.start_date;
                            startDateInput.type = 'date';
                        }
                        if (data.target_date && endDateInput && !endDateInput.value) {
                            endDateInput.value = data.target_date;
                            endDateInput.type = 'date';
                        }

                        if (plansList.length > 0) {
                            let activePlan = plansList[0];
                            latestFetchedExercises = activePlan.items || activePlan.workouts || [];

                            document.getElementById('active-member-name').textContent = memberName;
                            document.getElementById('active-plan-title').textContent = activePlan.title || 'Workout Plan';
                            document.getElementById('active-plan-dates').textContent = `${activePlan.start_date} to ${activePlan.end_date}`;
                            
                            if (titleInput && !titleInput.value) {
                                titleInput.value = activePlan.title || '';
                            }
                            
                            if (document.getElementById('start_date') && activePlan.start_date && !startDateInput.value) {
                                document.getElementById('start_date').value = activePlan.start_date;
                                document.getElementById('start_date').type = 'date';
                            }
                            if (document.getElementById('end_date') && activePlan.end_date && !endDateInput.value) {
                                document.getElementById('end_date').value = activePlan.end_date;
                                document.getElementById('end_date').type = 'date';
                            }

                            let itemsList = latestFetchedExercises;
                            let exercisesHtml = '<strong>Assigned Exercises:</strong> <ul class="mb-0 ps-3">';
                            
                            if (itemsList.length > 0) {
                                itemsList.forEach(ex => {
                                    let exName = ex.exercise ? ex.exercise.name : (ex.exercise_name || 'Exercise');
                                    let planDay = ex.workout_date ? getDayName(ex.workout_date) : (ex.day_of_week || '');
                                    exercisesHtml += `<li>${planDay} (${ex.workout_date || ''}): ${exName} (${ex.sets || '-'} Sets x ${ex.reps || '-'} Reps)</li>`;
                                });
                            } else {
                                exercisesHtml += '<li>No exercises listed in this active plan.</li>';
                            }
                            exercisesHtml += '</ul>';

                            document.getElementById('active-plan-exercises').innerHTML = exercisesHtml;
                            if (activePlanContainer) activePlanContainer.style.display = 'block';
                        } else {
                            latestFetchedExercises = [];
                            if (activePlanContainer) activePlanContainer.style.display = 'none';
                        }

                        if (!oldExercises || oldExercises.length === 0) {
                            tableBody.innerHTML = '';
                            let rowIndex = 0;

                            if (plansList.length > 0) {
                                plansList.forEach(plan => {
                                    let items = plan.items || plan.exercises || [];
                                    if (items.length > 0) {
                                        items.forEach(item => {
                                            if(item.workout_date) {
                                                item.day_of_week = getDayName(item.workout_date);
                                            }
                                            appendRow(rowIndex, item);
                                            rowIndex++;
                                        });
                                    }
                                });
                            }

                            if (tableBody.children.length === 0) {
                                addNewRow(0);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading workouts:', error);
                        if (activePlanContainer) activePlanContainer.style.display = 'none';
                        if (!oldExercises || oldExercises.length === 0) {
                            if (tableBody.children.length === 0) {
                                addNewRow(0);
                            }
                        }
                    });
            });
        }

        if (copyWorkoutBtn) {
            copyWorkoutBtn.addEventListener('click', function() {
                if (!latestFetchedExercises || latestFetchedExercises.length === 0) {
                    alert('No previous workout routines found for this member to copy.');
                    return;
                }

                if (confirm('Are you sure you want to copy exercises from the member\'s previous workout plan into this table? This will overwrite existing rows.')) {
                    tableBody.innerHTML = '';
                    latestFetchedExercises.forEach((item, index) => {
                        let clonedItem = {
                            day_of_week: item.day_of_week || (item.workout_date ? getDayName(item.workout_date) : ''),
                            workout_date: '',
                            muscle_group: item.muscle_group || '',
                            exercise_id: item.exercise_id || (item.exercise ? item.exercise.id : ''),
                            sets: item.sets || '',
                            reps: item.reps || '',
                            weight: item.weight || '',
                            rest_time: item.rest_time || '',
                            instructions: item.instructions || ''
                        };
                        appendRow(index, clonedItem);
                    });
                }
            });
        }

        if (startDateInput) {
            startDateInput.addEventListener('change', function() {
                const firstDateInput = tableBody.querySelector('.workout-date-input');
                if (firstDateInput && !firstDateInput.value) {
                    firstDateInput.value = this.value;
                    if(firstDateInput.value) firstDateInput.type = 'date';
                    let row = firstDateInput.closest('tr');
                    let daySelect = row.querySelector('.workout-day-select');
                    if(daySelect) {
                        daySelect.value = getDayName(this.value);
                    }
                }
            });
        }

        function appendRow(index, item = {}) {
            let newRow = document.createElement('tr');
            
            if (item.workout_date) {
                item.day_of_week = getDayName(item.workout_date);
            }

            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            let dayOptions = days.map(day => `<option value="${day}" ${item.day_of_week == day ? 'selected' : ''}>${day}</option>`).join('');
            
            let dropdownOptions = '<option value="">Select Exercise</option>';
            exercisesData.forEach(ex => {
                dropdownOptions += `<option value="${ex.id}" ${ex.id == item.exercise_id ? 'selected' : ''}>${ex.name}</option>`;
            });

            let dateInputType = item.workout_date ? 'date' : 'text';

            newRow.innerHTML = `
                <td>
                    <select name="exercises[${index}][day_of_week]" class="form-select form-select-sm workout-day-select">
                        ${dayOptions}
                    </select>
                </td>
                <td>
                    <input type="${dateInputType}" name="exercises[${index}][workout_date]" value="${item.workout_date || ''}" class="form-control form-control-sm workout-date-input" placeholder="dd-mm-yyyy" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                </td>
                <td><input type="text" name="exercises[${index}][muscle_group]" value="${item.muscle_group || ''}" class="form-control form-control-sm" placeholder="e.g. Chest"></td>
                <td>
                    <select name="exercises[${index}][exercise_id]" class="form-select form-select-sm" required>
                        ${dropdownOptions}
                    </select>
                </td>
                <td><input type="text" name="exercises[${index}][sets]" value="${item.sets || ''}" class="form-control form-control-sm text-center" placeholder="e.g. 3"></td>
                <td><input type="text" name="exercises[${index}][reps]" value="${item.reps || ''}" class="form-control form-control-sm text-center" placeholder="e.g. 12"></td>
                <td><input type="text" name="exercises[${index}][weight]" value="${item.weight || ''}" class="form-control form-control-sm" placeholder="e.g. 20kg"></td>
                <td><input type="text" name="exercises[${index}][rest_time]" value="${item.rest_time || ''}" class="form-control form-control-sm" placeholder="e.g. 60s"></td>
                <td><input type="text" name="exercises[${index}][instructions]" value="${item.instructions || ''}" class="form-control form-control-sm" placeholder="e.g. Keep back straight"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row rounded-circle"><i class="fas fa-times"></i></button>
                </td>
            `;
            tableBody.appendChild(newRow);

            let dateInput = newRow.querySelector('.workout-date-input');
            let daySelect = newRow.querySelector('.workout-day-select');
            if(dateInput && daySelect) {
                dateInput.addEventListener('change', function() {
                    if(this.value) {
                        daySelect.value = getDayName(this.value);
                    }
                });
            }
        }

        function addNewRow(index) {
            let lastDateInput = tableBody.querySelector('tr:last-child .workout-date-input');
            let nextDate = '';
            
            if (lastDateInput && lastDateInput.value) {
                let d = new Date(lastDateInput.value);
                if (!isNaN(d.getTime())) {
                    d.setDate(d.getDate() + 1);
                    nextDate = d.toISOString().split('T')[0];
                }
            } else if (startDateInput && startDateInput.value) {
                nextDate = startDateInput.value;
            }

            appendRow(index, { workout_date: nextDate });
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                let currentRowsCount = tableBody.querySelectorAll('tr').length;
                addNewRow(currentRowsCount);
            });
        }

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                let row = e.target.closest('tr');
                if (tableBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                } else {
                    alert('At least one exercise row is required.');
                }
            }
        });
    });
</script>
@endsection