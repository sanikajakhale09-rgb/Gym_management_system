@extends('auth.sidebar')

@section('title', 'Member Complete Profile - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- Header & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-user-gear me-2 text-primary"></i> Profile & Fitness Overview: {{ $member->name }}
        </h2>
        <div class="d-flex gap-2">
            <!-- Download PDF Receipt Button -->
            <a href="{{ route('members.receipt', $member->id) }}" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-file-pdf me-1"></i> Download Receipt
            </a>
            <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Members
            </a>
        </div>
    </div>

    <!-- Member Quick Info Card -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow" style="width: 50px; height: 50px; font-size: 20px; background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $member->name }}</h5>
                        <span class="text-muted small">ID: {{ $member->member_code ?? 'GYM-'.$member->id }}</span>
                    </div>
                </div>
                <p class="mb-1 text-secondary"><strong>Phone:</strong> {{ $member->phone }}</p>
                <p class="mb-1 text-secondary"><strong>Email:</strong> {{ $member->email ?? 'N/A' }}</p>
                <p class="mb-0 text-secondary"><strong>Assigned Trainer:</strong> <span class="badge bg-purple text-dark fw-bold">{{ $member->trainer->name ?? 'No Trainer Assigned' }}</span></p>
            </div>
        </div>

        <!-- Today's Workout Card -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-start border-primary border-4">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fa-solid fa-fire me-2"></i> Today's Workout ({{ $today ?? now()->format('l') }})
                </h5>
                @if(isset($todaysWorkouts) && $todaysWorkouts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Exercise</th>
                                    <th>Muscle Group</th>
                                    <th>Sets x Reps</th>
                                    <th>Weight</th>
                                    <th>Rest</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaysWorkouts as $item)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $item->exercise->name ?? 'Exercise' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->muscle_group ?? 'N/A' }}</span></td>
                                    <td>{{ $item->sets }} Sets / {{ $item->reps }} Reps</td>
                                    <td>{{ $item->weight ? $item->weight : '-' }}</td>
                                    <td>{{ $item->rest_time ? $item->rest_time : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted py-3 text-center">No specific workout scheduled for today ({{ $today ?? now()->format('l') }}). Enjoy your rest day!</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Diet Plan & Full Workout Plans Section -->
    <div class="row g-4">
        <!-- Diet Plan Section -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="fa-solid fa-utensils me-2"></i> Assigned Diet Plans & Fitness History
                    </h5>
                    <a href="{{ route('diet-plans.create', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="fa-solid fa-plus me-1"></i> Add Diet Plan
                    </a>
                </div>

                @if(isset($member->dietPlans) && $member->dietPlans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Plan Title</th>
                                    <th>Goal / Target</th>
                                    <th>Weight / BMI</th>
                                    <th>Breakfast</th>
                                    <th>Lunch</th>
                                    <th>Dinner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($member->dietPlans as $plan)
                                    <tr>
                                        <td class="fw-semibold">{{ $plan->title }}</td>
                                        <td>{{ $plan->goal_type ?? 'N/A' }} (Target: {{ $plan->target_value ?? 'N/A' }})</td>
                                        <td>{{ $plan->weight ?? '-' }} kg / BMI: {{ $plan->bmi ?? '-' }}</td>
                                        <td><small>{{ Str::limit($plan->breakfast, 25) }}</small></td>
                                        <td><small>{{ Str::limit($plan->lunch, 25) }}</small></td>
                                        <td><small>{{ Str::limit($plan->dinner, 25) }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No diet plans or fitness details recorded for this member yet.</p>
                @endif
            </div>
        </div>

        <!-- Active Workout Plan Section -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #4f46e5;">
                        <i class="fa-solid fa-dumbbell me-2"></i> Current Active Workout Plan
                    </h5>
                    <a href="{{ route('workouts.create', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fa-solid fa-plus me-1"></i> Add / Change Plan
                    </a>
                </div>

                @php
                    $allPlans = $member->workoutPlans ?? collect();
                    $activePlan = $allPlans->sortByDesc('created_at')->first();
                @endphp

                @if($activePlan)
                    @php
                        $planItems = $activePlan->items ?? $activePlan->workouts ?? $activePlan->details ?? collect();
                        $totalExercisesCount = $planItems->count();
                    @endphp
                    <div class="card border shadow-sm rounded-3 overflow-hidden bg-light">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">
                                    {{ $activePlan->title }} 
                                    <span class="badge bg-primary ms-2 small">Goal: {{ $activePlan->goal ?? 'General' }}</span>
                                </h6>
                                <small class="text-muted">
                                    📅 <strong>Duration:</strong> {{ \Carbon\Carbon::parse($activePlan->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($activePlan->end_date)->format('d M Y') }}
                                </small>
                            </div>
                            <span class="badge bg-success text-white">Active</span>
                        </div>
                        <div class="card-body">
                            <p class="mb-2 text-secondary small">
                                <strong>Level:</strong> <span class="text-dark fw-bold">{{ $activePlan->level ?? 'Beginner' }}</span> | 
                                <strong>Total Exercises:</strong> {{ $totalExercisesCount }}
                            </p>

                            @if($planItems->count() > 0)
                                <div class="table-responsive bg-white rounded border p-2">
                                    <table class="table table-sm align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date / Day</th>
                                                <th>Exercise</th>
                                                <th>Sets x Reps</th>
                                                <th>Weight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($planItems as $wItem)
                                                @php
                                                    $displayDateLabel = '';
                                                    if (!empty($wItem->workout_date)) {
                                                        $displayDateLabel = \Carbon\Carbon::parse($wItem->workout_date)->format('d M Y') . (!empty($wItem->day_of_week) ? ' (' . $wItem->day_of_week . ')' : '');
                                                    } elseif (!empty($wItem->date)) {
                                                        $displayDateLabel = $wItem->date;
                                                    } else {
                                                        $displayDateLabel = $wItem->day_of_week ?? 'Schedule';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold text-secondary">{{ $displayDateLabel }}</td>
                                                    <td class="fw-bold text-dark">{{ $wItem->exercise->name ?? $wItem->exercise_name ?? 'Exercise' }}</td>
                                                    <td>{{ $wItem->sets ?? 0 }} Sets / {{ $wItem->reps ?? 0 }} Reps</td>
                                                    <td>{{ $wItem->weight ? $wItem->weight : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0 small italic">No exercises found for this active plan.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-2">No active workout plans assigned for this member yet.</p>
                        <a href="{{ route('workouts.create', ['member_id' => $member->id]) }}" class="btn btn-sm btn-primary">Assign Plan Now</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto Download PDF Script via Session -->
@if(session('auto_download') && session('download_member_id'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.location.href = "{{ route('members.receipt', session('download_member_id')) }}";
        });
    </script>
@endif

@endsection