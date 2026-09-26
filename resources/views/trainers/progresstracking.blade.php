@extends('auth.sidebar')

@section('title', 'Member Progress Tracking')

@section('content_header')

<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-4 border-indigo">

        <div>

            <h4 class="fw-bold text-dark mb-1">

                <i class="fas fa-chart-line text-indigo me-2"></i>

                Member Progress Tracking

            </h4>

            <p class="text-muted small mb-0">

                Visualize member transformations, body measurements, workouts, and goal achievements.

            </p>

        </div>

    </div>

</div>

@endsection


@section('content')


<style>

/* =========================================================
   MAIN COLORS
========================================================= */

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


/* =========================================================
   CONTAINER
========================================================= */

.form-container-wrapper {
    max-width: 1150px;
    margin: 0 auto;
}


/* =========================================================
   CARDS
========================================================= */

.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05) !important;
    border-radius: 0.75rem !important;
}

.card-body {
    padding: 1.5rem !important;
}


/* =========================================================
   PROGRESS
========================================================= */

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.8s ease;
}

.progress-bar-custom {
    background-color: #3f3d99 !important;
}


/* =========================================================
   IMAGES
========================================================= */

.img-preview {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
}


/* =========================================================
   STAT CARDS
========================================================= */

.stat-card {
    min-height: 115px;
}


/* =========================================================
   CHARTS
========================================================= */

.chart-card {
    min-height: 400px;
}

.chart-wrapper {
    position: relative;
    height: 320px;
    width: 100%;
}

.chart-wrapper-small {
    position: relative;
    height: 280px;
    width: 100%;
}


/* =========================================================
   TABLE
========================================================= */

.table th {
    white-space: nowrap;
}

.table td {
    white-space: nowrap;
}


/* =========================================================
   EMPTY CHART MESSAGE
========================================================= */

.no-chart-data {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #6c757d;
}

.no-chart-data i {
    font-size: 2rem;
    margin-bottom: 10px;
}


/* =========================================================
   MEMBER INFO
========================================================= */

.member-info-card {
    border-left: 4px solid #3f3d99;
}


/* =========================================================
   GOAL BOX
========================================================= */

.goal-value-box {
    min-height: 75px;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 767px) {

    .chart-wrapper {
        height: 280px;
    }

    .chart-wrapper-small {
        height: 250px;
    }

    .table {
        font-size: 13px;
    }

}

</style>


<div class="container pb-5">

    <div class="form-container-wrapper">


        {{-- =========================================================
             SUCCESS MESSAGE
        ========================================================== --}}

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3"
                 role="alert">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        @endif


        {{-- =========================================================
             ERROR MESSAGE
        ========================================================== --}}

        @if($errors->any())

            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3"
                 role="alert">

                <i class="fas fa-exclamation-triangle me-2"></i>

                <strong>Please fix the errors below:</strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        @endif



        {{-- =========================================================
             STEP 1: SELECT MEMBER
        ========================================================== --}}

        <div class="card mb-4">

            <div class="card-body bg-white">

                <form method="GET"
                      action="{{ route('progress.index') }}"
                      class="row g-3 align-items-end">

                    <div class="col-md-9">

                        <label class="form-label fw-bold">

                            Select Member

                            <span class="text-danger">*</span>

                        </label>


                        <select name="member_id"
                                class="form-select rounded-2"
                                required
                                onchange="this.form.submit()">

                            <option value="">
                                -- Choose Member ID / Name --
                            </option>


                            @foreach($members as $member)

                                <option value="{{ $member->id }}"
                                    {{ optional($selectedMember)->id == $member->id ? 'selected' : '' }}>

                                    {{ $member->name }}
                                    (ID: {{ $member->id }})

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <button type="submit"
                                class="btn text-white w-100 fw-semibold rounded-2"
                                style="background-color:#3f3d99;">

                            <i class="fas fa-search me-1"></i>

                            View Progress

                        </button>

                    </div>

                </form>

            </div>

        </div>



        @if($selectedMember)


        {{-- =========================================================
             PREPARE DATA
        ========================================================== --}}

        @php

            /*
            |--------------------------------------------------------------------------
            | Measurement Collection
            |--------------------------------------------------------------------------
            */

            $measurementCollection = collect($measurements ?? []);

            /*
            |--------------------------------------------------------------------------
            | Sort Measurements By Date
            |--------------------------------------------------------------------------
            */

            $measurementCollection = $measurementCollection
                ->sortBy('measurement_date')
                ->values();

            $firstMeasurement = $measurementCollection->first();

            $latestMeasurement = $measurementCollection->last();


            /*
            |--------------------------------------------------------------------------
            | Goal Safe Values
            |--------------------------------------------------------------------------
            */

            $goalExists = !empty($goal);


            /*
            |--------------------------------------------------------------------------
            | Weight
            |--------------------------------------------------------------------------
            */

            $startWeight = $firstMeasurement->weight
                ?? ($goal->starting_value ?? 0);

            $currentWeight = $latestMeasurement->weight
                ?? ($goal->current_value ?? $startWeight);

            $targetWeight = $goal->target_value ?? 0;


            $weightChange = round(
                floatval($currentWeight) -
                floatval($startWeight),
                1
            );


            /*
            |--------------------------------------------------------------------------
            | BMI
            |--------------------------------------------------------------------------
            */

            $startBmi = $firstMeasurement->bmi ?? 0;

            $currentBmi = $latestMeasurement->bmi ?? 0;

            $bmiChange = round(
                floatval($currentBmi) -
                floatval($startBmi),
                1
            );


            /*
            |--------------------------------------------------------------------------
            | Body Fat
            |--------------------------------------------------------------------------
            */

            $startFat = $firstMeasurement->body_fat ?? 0;

            $currentFat = $latestMeasurement->body_fat ?? 0;

            $fatChange = round(
                floatval($currentFat) -
                floatval($startFat),
                1
            );


            /*
            |--------------------------------------------------------------------------
            | Workout
            |--------------------------------------------------------------------------
            */

            $workoutTotal = intval(
                $workouts['total_sessions'] ?? 0
            );

            $workoutCompleted = intval(
                $workouts['completed'] ?? 0
            );

            $workoutMissed = intval(
                $workouts['missed'] ?? 0
            );


            $workoutPercentage = min(
                max(
                    floatval($workouts['percentage'] ?? 0),
                    0
                ),
                100
            );


            /*
            |--------------------------------------------------------------------------
            | Fitness Goal
            |--------------------------------------------------------------------------
            */

            $startVal = floatval(
                $goal->starting_value ?? 0
            );

            $currVal = floatval(
                $goal->current_value ?? $startVal
            );

            $targetVal = floatval(
                $goal->target_value ?? 0
            );


            $totalDiff = abs(
                $targetVal - $startVal
            );

            $achievedDiff = abs(
                $currVal - $startVal
            );


            $goalPercentage = $totalDiff > 0
                ? min(
                    round(
                        ($achievedDiff / $totalDiff) * 100
                    ),
                    100
                )
                : 0;


            $goalPercentage = max(
                0,
                $goalPercentage
            );


            $goalRemaining = max(
                100 - $goalPercentage,
                0
            );


            /*
            |--------------------------------------------------------------------------
            | Chart Data
            |--------------------------------------------------------------------------
            */

            $measurementDates = $measurementCollection
                ->pluck('measurement_date')
                ->map(function ($date) {

                    return $date
                        ? date('d M Y', strtotime($date))
                        : '';

                })
                ->values()
                ->toArray();


            $weightData = $measurementCollection
                ->pluck('weight')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $bmiData = $measurementCollection
                ->pluck('bmi')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $bodyFatData = $measurementCollection
                ->pluck('body_fat')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $chestData = $measurementCollection
                ->pluck('chest')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $waistData = $measurementCollection
                ->pluck('waist')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $hipData = $measurementCollection
                ->pluck('hip')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $bicepsData = $measurementCollection
                ->pluck('biceps')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();


            $thighData = $measurementCollection
                ->pluck('thigh')
                ->map(function ($value) {

                    return $value !== null
                        ? floatval($value)
                        : null;

                })
                ->values()
                ->toArray();

        @endphp



        {{-- =========================================================
             DYNAMIC DATA STORAGE
             IMPORTANT:
             Data is stored in HTML instead of directly inside JS.
        ========================================================== --}}

        <div id="progressData"

             data-measurement-dates='{{ json_encode($measurementDates) }}'

             data-weight-data='{{ json_encode($weightData) }}'

             data-bmi-data='{{ json_encode($bmiData) }}'

             data-body-fat-data='{{ json_encode($bodyFatData) }}'

             data-chest-data='{{ json_encode($chestData) }}'

             data-waist-data='{{ json_encode($waistData) }}'

             data-hip-data='{{ json_encode($hipData) }}'

             data-biceps-data='{{ json_encode($bicepsData) }}'

             data-thigh-data='{{ json_encode($thighData) }}'

             data-workout-total="{{ $workoutTotal }}"

             data-workout-completed="{{ $workoutCompleted }}"

             data-workout-missed="{{ $workoutMissed }}"

             data-workout-percentage="{{ $workoutPercentage }}"

             data-goal-percentage="{{ $goalPercentage }}"

             data-goal-remaining="{{ $goalRemaining }}"

             data-goal-exists="{{ $goalExists ? 1 : 0 }}">

        </div>



        {{-- =========================================================
             MEMBER INFORMATION
        ========================================================== --}}

        <div class="card member-info-card mb-4">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h5 class="fw-bold text-dark mb-1">

                            {{ $selectedMember->name }}

                        </h5>

                        <p class="text-muted mb-0">

                            <i class="fas fa-id-card me-1"></i>

                            Member ID: {{ $selectedMember->id }}

                        </p>

                    </div>


                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <span class="badge bg-success px-3 py-2">

                            {{ $selectedMember->status ?? 'Active' }}

                        </span>

                    </div>

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 2: SUMMARY CARDS
        ========================================================== --}}

        <div class="row g-3 mb-4">


            {{-- STARTING WEIGHT --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3 border-start border-4 border-primary">

                    <span class="text-muted small fw-bold">

                        STARTING WEIGHT

                    </span>

                    <h3 class="fw-bold text-dark mb-0">

                        {{ number_format((float)$startWeight, 1) }}

                        <small class="fs-6 text-muted">
                            kg
                        </small>

                    </h3>

                </div>

            </div>



            {{-- CURRENT WEIGHT --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3 border-start border-4 border-success">

                    <span class="text-muted small fw-bold">

                        CURRENT WEIGHT

                    </span>

                    <h3 class="fw-bold text-dark mb-0">

                        {{ number_format((float)$currentWeight, 1) }}

                        <small class="fs-6 text-muted">
                            kg
                        </small>

                    </h3>

                </div>

            </div>



            {{-- TARGET WEIGHT --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3 border-start border-4 border-warning">

                    <span class="text-muted small fw-bold">

                        TARGET WEIGHT

                    </span>

                    <h3 class="fw-bold text-dark mb-0">

                        {{ number_format((float)$targetWeight, 1) }}

                        <small class="fs-6 text-muted">
                            kg
                        </small>

                    </h3>

                </div>

            </div>



            {{-- WEIGHT CHANGE --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3">

                    <span class="text-muted small fw-bold">

                        WEIGHT CHANGE

                    </span>

                    <h4 class="fw-bold mb-0
                        {{ $weightChange <= 0 ? 'text-success' : 'text-danger' }}">

                        {{ $weightChange > 0 ? '+' : '' }}

                        {{ number_format($weightChange, 1) }}

                        kg

                    </h4>

                </div>

            </div>



            {{-- BMI CHANGE --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3">

                    <span class="text-muted small fw-bold">

                        BMI CHANGE

                    </span>

                    <h4 class="fw-bold text-indigo mb-0">

                        {{ $bmiChange > 0 ? '+' : '' }}

                        {{ number_format($bmiChange, 1) }}

                    </h4>

                </div>

            </div>



            {{-- BODY FAT CHANGE --}}

            <div class="col-md-4">

                <div class="card stat-card bg-white p-3">

                    <span class="text-muted small fw-bold">

                        BODY FAT CHANGE

                    </span>

                    <h4 class="fw-bold text-info mb-0">

                        {{ $fatChange > 0 ? '+' : '' }}

                        {{ number_format($fatChange, 1) }}%

                    </h4>

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 3: BODY TRANSFORMATION GRAPH
        ========================================================== --}}

        <div class="card mb-4 chart-card">

            <div class="card-header bg-custom-header text-white">

                <h6 class="mb-0 fw-bold">

                    <i class="fas fa-chart-line me-2"></i>

                    Body Transformation Progress

                </h6>

            </div>


            <div class="card-body bg-white">

                <div class="chart-wrapper">

                    @if($measurementCollection->count() > 0)

                        <canvas id="bodyProgressChart"></canvas>

                    @else

                        <div class="no-chart-data">

                            <i class="fas fa-chart-line"></i>

                            <span>No measurement data available</span>

                        </div>

                    @endif

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 4: BODY MEASUREMENTS HISTORY
        ========================================================== --}}

        <div class="card mb-4 overflow-hidden">

            <div class="card-header bg-custom-header text-white">

                <h6 class="mb-0 fw-bold">

                    <i class="fas fa-ruler-combined me-2"></i>

                    Date-Wise Body Measurements History

                </h6>

            </div>


            <div class="card-body bg-white p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0 align-middle text-center">

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

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($measurementCollection as $m)

                                <tr>

                                    <td class="fw-semibold text-indigo">

                                        {{ $m->measurement_date }}

                                    </td>


                                    <td>

                                        {{ $m->weight !== null ? number_format((float)$m->weight, 2) . ' kg' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->bmi !== null ? number_format((float)$m->bmi, 2) : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->chest !== null ? number_format((float)$m->chest, 2) . ' cm' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->waist !== null ? number_format((float)$m->waist, 2) . ' cm' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->hip !== null ? number_format((float)$m->hip, 2) . ' cm' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->biceps !== null ? number_format((float)$m->biceps, 2) . ' cm' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->thigh !== null ? number_format((float)$m->thigh, 2) . ' cm' : '-' }}

                                    </td>


                                    <td>

                                        {{ $m->body_fat !== null ? number_format((float)$m->body_fat, 2) . '%' : '-' }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="9"
                                        class="text-muted py-4">

                                        <i class="fas fa-ruler-combined me-2"></i>

                                        No body measurement records found
                                        for this member yet.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 5: BODY MEASUREMENTS CHART
        ========================================================== --}}

        <div class="card mb-4 chart-card">

            <div class="card-header bg-custom-header text-white">

                <h6 class="mb-0 fw-bold">

                    <i class="fas fa-ruler-horizontal me-2"></i>

                    Body Measurement Transformation

                </h6>

            </div>


            <div class="card-body bg-white">

                <div class="chart-wrapper">

                    @if($measurementCollection->count() > 0)

                        <canvas id="measurementChart"></canvas>

                    @else

                        <div class="no-chart-data">

                            <i class="fas fa-ruler-horizontal"></i>

                            <span>No measurement data available</span>

                        </div>

                    @endif

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 6: WORKOUT PROGRESS
        ========================================================== --}}

        <div class="row g-4 mb-4">


            {{-- WORKOUT STATISTICS --}}

            <div class="col-md-6">

                <div class="card h-100">

                    <div class="card-header bg-custom-header text-white">

                        <h6 class="mb-0 fw-bold">

                            <i class="fas fa-dumbbell me-2"></i>

                            Workout Progress

                        </h6>

                    </div>


                    <div class="card-body bg-white">

                        <div class="row text-center g-3">


                            {{-- TOTAL --}}

                            <div class="col-4">

                                <div class="bg-light p-3 rounded">

                                    <span class="d-block text-muted small">

                                        Total Sessions

                                    </span>

                                    <h4 class="fw-bold mb-0">

                                        {{ $workoutTotal }}

                                    </h4>

                                </div>

                            </div>


                            {{-- COMPLETED --}}

                            <div class="col-4">

                                <div class="bg-light p-3 rounded">

                                    <span class="d-block text-muted small">

                                        Completed

                                    </span>

                                    <h4 class="fw-bold text-success mb-0">

                                        {{ $workoutCompleted }}

                                    </h4>

                                </div>

                            </div>


                            {{-- MISSED --}}

                            <div class="col-4">

                                <div class="bg-light p-3 rounded">

                                    <span class="d-block text-muted small">

                                        Missed

                                    </span>

                                    <h4 class="fw-bold text-danger mb-0">

                                        {{ $workoutMissed }}

                                    </h4>

                                </div>

                            </div>

                        </div>


                        <div class="mt-4">

                            <div class="d-flex justify-content-between mb-2">

                                <span class="fw-semibold">

                                    Completion Rate

                                </span>

                                <span class="fw-bold">

                                    {{ number_format($workoutPercentage, 1) }}%

                                </span>

                            </div>


                            <div class="progress"
                                 style="height:12px;">

                                <div class="progress-bar bg-success workout-progress-bar"
                                     role="progressbar"
                                     data-progress="{{ $workoutPercentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            {{-- WORKOUT GRAPH --}}

            <div class="col-md-6">

                <div class="card h-100">

                    <div class="card-header bg-custom-header text-white">

                        <h6 class="mb-0 fw-bold">

                            <i class="fas fa-chart-pie me-2"></i>

                            Workout Session Summary

                        </h6>

                    </div>


                    <div class="card-body bg-white">

                        <div class="chart-wrapper-small">

                            <canvas id="workoutChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =========================================================
             STEP 7: FITNESS GOAL
        ========================================================== --}}

        <div class="card mb-4">

            <div class="card-header bg-custom-header text-white">

                <h6 class="mb-0 fw-bold">

                    <i class="fas fa-bullseye me-2"></i>

                    Fitness Goal Status

                </h6>

            </div>


            <div class="card-body bg-white">

                @if($goal)

                    <div class="row align-items-center">


                        {{-- GOAL DETAILS --}}

                        <div class="col-md-5">

                            <span class="text-muted small d-block">

                                Goal Type

                            </span>


                            <h4 class="fw-bold text-indigo mb-3">

                                {{ $goal->goal_type ?? 'General Fitness' }}

                            </h4>


                            <div class="row g-2">


                                {{-- START --}}

                                <div class="col-4">

                                    <div class="bg-light p-3 rounded text-center goal-value-box">

                                        <small class="text-muted">
                                            Start
                                        </small>

                                        <div class="fw-bold">

                                            {{ $goal->starting_value ?? 0 }}

                                        </div>

                                    </div>

                                </div>


                                {{-- CURRENT --}}

                                <div class="col-4">

                                    <div class="bg-light p-3 rounded text-center goal-value-box">

                                        <small class="text-muted">
                                            Current
                                        </small>

                                        <div class="fw-bold text-primary">

                                            {{ $goal->current_value ?? 0 }}

                                        </div>

                                    </div>

                                </div>


                                {{-- TARGET --}}

                                <div class="col-4">

                                    <div class="bg-light p-3 rounded text-center goal-value-box">

                                        <small class="text-muted">
                                            Target
                                        </small>

                                        <div class="fw-bold text-success">

                                            {{ $goal->target_value ?? 0 }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>



                        {{-- GOAL CHART --}}

                        <div class="col-md-7">

                            <div class="chart-wrapper-small">

                                <canvas id="goalChart"></canvas>

                            </div>

                        </div>

                    </div>



                    {{-- GOAL PROGRESS BAR --}}

                    <div class="mt-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span class="fw-semibold">

                                Goal Completion Progress

                            </span>

                            <span class="fw-bold text-indigo">

                                {{ $goalPercentage }}%

                            </span>

                        </div>


                        <div class="progress"
                             style="height:12px;">

                            <div class="progress-bar progress-bar-custom goal-progress-bar"
                                 role="progressbar"
                                 data-progress="{{ $goalPercentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">

                            </div>

                        </div>

                    </div>

                @else

                    <div class="text-center py-4">

                        <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>

                        <p class="text-muted mb-0">

                            No active fitness goal assigned for this member.

                        </p>

                    </div>

                @endif

            </div>

        </div>



        {{-- =========================================================
             STEP 8: BEFORE / AFTER PHOTOS
        ========================================================== --}}

        <div class="card mb-4">

            <div class="card-header bg-custom-header text-white">

                <h6 class="mb-0 fw-bold">

                    <i class="fas fa-camera me-2"></i>

                    Before & After Photos Comparison

                </h6>

            </div>


            <div class="card-body bg-white">

                <div class="row text-center g-4">


                    {{-- BEFORE PHOTO --}}

                    <div class="col-md-6">

                        <div class="border rounded-3 p-3 h-100">

                            <span class="fw-bold text-muted d-block mb-3">

                                Before Photo

                            </span>


                            @if($firstMeasurement && $firstMeasurement->before_photo)

                                <img src="{{ asset('storage/' . $firstMeasurement->before_photo) }}"
                                     class="img-fluid rounded shadow-sm img-preview"
                                     alt="Before Photo">

                            @else

                                <div class="bg-light p-5 rounded text-muted">

                                    <i class="fas fa-image fa-2x mb-2"></i>

                                    <div>
                                        No Before Photo Uploaded
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>



                    {{-- AFTER PHOTO --}}

                    <div class="col-md-6">

                        <div class="border rounded-3 p-3 h-100">

                            <span class="fw-bold text-muted d-block mb-3">

                                Current / After Photo

                            </span>


                            @if($latestMeasurement && $latestMeasurement->after_photo)

                                <img src="{{ asset('storage/' . $latestMeasurement->after_photo) }}"
                                     class="img-fluid rounded shadow-sm img-preview"
                                     alt="After Photo">

                            @else

                                <div class="bg-light p-5 rounded text-muted">

                                    <i class="fas fa-image fa-2x mb-2"></i>

                                    <div>
                                        No After Photo Uploaded
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        @endif

    </div>

</div>



{{-- =========================================================
     CHART.JS
========================================================== --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       GET DATA FROM HTML
    ========================================================== */

    const progressData =
        document.getElementById('progressData');


    if (!progressData) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Safe JSON Parser
    |--------------------------------------------------------------------------
    */

    function getJsonData(attributeName) {

        try {

            const value =
                progressData.getAttribute(attributeName);

            return value
                ? JSON.parse(value)
                : [];

        } catch (error) {

            console.error(
                'Invalid JSON:',
                attributeName,
                error
            );

            return [];

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Measurement Data
    |--------------------------------------------------------------------------
    */

    const measurementDates =
        getJsonData('data-measurement-dates');


    const weightData =
        getJsonData('data-weight-data');


    const bmiData =
        getJsonData('data-bmi-data');


    const bodyFatData =
        getJsonData('data-body-fat-data');


    const chestData =
        getJsonData('data-chest-data');


    const waistData =
        getJsonData('data-waist-data');


    const hipData =
        getJsonData('data-hip-data');


    const bicepsData =
        getJsonData('data-biceps-data');


    const thighData =
        getJsonData('data-thigh-data');



    /* =========================================================
       WORKOUT DATA
    ========================================================== */

    const workoutTotal =
        Number(
            progressData.getAttribute(
                'data-workout-total'
            )
        ) || 0;


    const workoutCompleted =
        Number(
            progressData.getAttribute(
                'data-workout-completed'
            )
        ) || 0;


    const workoutMissed =
        Number(
            progressData.getAttribute(
                'data-workout-missed'
            )
        ) || 0;


    const workoutPercentage =
        Number(
            progressData.getAttribute(
                'data-workout-percentage'
            )
        ) || 0;



    /* =========================================================
       GOAL DATA
    ========================================================== */

    const goalPercentage =
        Number(
            progressData.getAttribute(
                'data-goal-percentage'
            )
        ) || 0;


    const goalRemaining =
        Number(
            progressData.getAttribute(
                'data-goal-remaining'
            )
        ) || 0;


    const goalExists =
        Number(
            progressData.getAttribute(
                'data-goal-exists'
            )
        ) === 1;



    /* =========================================================
       PROGRESS BARS
    ========================================================== */

    document
        .querySelectorAll('.workout-progress-bar')
        .forEach(function (bar) {

            const progress =
                Number(
                    bar.getAttribute('data-progress')
                ) || 0;

            bar.style.width =
                Math.min(
                    Math.max(progress, 0),
                    100
                ) + '%';

        });


    document
        .querySelectorAll('.goal-progress-bar')
        .forEach(function (bar) {

            const progress =
                Number(
                    bar.getAttribute('data-progress')
                ) || 0;

            bar.style.width =
                Math.min(
                    Math.max(progress, 0),
                    100
                ) + '%';

        });



    /* =========================================================
       CHART.JS CHECK
    ========================================================== */

    if (typeof Chart === 'undefined') {

        console.error(
            'Chart.js could not be loaded.'
        );

        return;

    }



    /* =========================================================
       COMMON CHART OPTIONS
    ========================================================== */

    const commonOptions = {

        responsive: true,

        maintainAspectRatio: false,

        interaction: {
            mode: 'index',
            intersect: false
        },

        plugins: {

            legend: {
                position: 'top'
            }

        }

    };



    /* =========================================================
       1. BODY TRANSFORMATION LINE CHART
    ========================================================== */

    const bodyProgressCanvas =
        document.getElementById(
            'bodyProgressChart'
        );


    if (
        bodyProgressCanvas &&
        measurementDates.length > 0
    ) {

        new Chart(
            bodyProgressCanvas,
            {

                type: 'line',

                data: {

                    labels: measurementDates,

                    datasets: [

                        {
                            label: 'Weight (kg)',

                            data: weightData,

                            borderWidth: 3,

                            tension: 0.4,

                            fill: false,

                            spanGaps: true
                        },

                        {
                            label: 'BMI',

                            data: bmiData,

                            borderWidth: 3,

                            tension: 0.4,

                            fill: false,

                            spanGaps: true
                        },

                        {
                            label: 'Body Fat (%)',

                            data: bodyFatData,

                            borderWidth: 3,

                            tension: 0.4,

                            fill: false,

                            spanGaps: true
                        }

                    ]

                },

                options: {

                    ...commonOptions,

                    scales: {

                        x: {

                            title: {

                                display: true,

                                text: 'Measurement Date'

                            }

                        },

                        y: {

                            beginAtZero: false,

                            title: {

                                display: true,

                                text: 'Value'

                            }

                        }

                    }

                }

            }
        );

    }



    /* =========================================================
       2. BODY MEASUREMENT BAR CHART
    ========================================================== */

    const measurementCanvas =
        document.getElementById(
            'measurementChart'
        );


    if (
        measurementCanvas &&
        measurementDates.length > 0
    ) {

        new Chart(
            measurementCanvas,
            {

                type: 'bar',

                data: {

                    labels: measurementDates,

                    datasets: [

                        {
                            label: 'Chest (cm)',

                            data: chestData,

                            borderWidth: 1,

                            borderRadius: 5
                        },

                        {
                            label: 'Waist (cm)',

                            data: waistData,

                            borderWidth: 1,

                            borderRadius: 5
                        },

                        {
                            label: 'Hip (cm)',

                            data: hipData,

                            borderWidth: 1,

                            borderRadius: 5
                        },

                        {
                            label: 'Biceps (cm)',

                            data: bicepsData,

                            borderWidth: 1,

                            borderRadius: 5
                        },

                        {
                            label: 'Thigh (cm)',

                            data: thighData,

                            borderWidth: 1,

                            borderRadius: 5
                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {
                            position: 'top'
                        }

                    },

                    scales: {

                        x: {

                            title: {

                                display: true,

                                text: 'Measurement Date'

                            }

                        },

                        y: {

                            beginAtZero: true,

                            title: {

                                display: true,

                                text: 'Measurement (cm)'

                            }

                        }

                    }

                }

            }
        );

    }



    /* =========================================================
       3. WORKOUT DOUGHNUT CHART
    ========================================================== */

    const workoutCanvas =
        document.getElementById(
            'workoutChart'
        );


    if (workoutCanvas) {


        const workoutRemaining =
            Math.max(
                workoutTotal -
                workoutCompleted -
                workoutMissed,
                0
            );


        new Chart(
            workoutCanvas,
            {

                type: 'doughnut',

                data: {

                    labels: [

                        'Completed',

                        'Missed',

                        'Remaining'

                    ],

                    datasets: [

                        {

                            data: [

                                workoutCompleted,

                                workoutMissed,

                                workoutRemaining

                            ],

                            borderWidth: 2

                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    cutout: '65%',

                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            }
        );

    }



    /* =========================================================
       4. FITNESS GOAL DOUGHNUT CHART
    ========================================================== */

    const goalCanvas =
        document.getElementById(
            'goalChart'
        );


    if (
        goalCanvas &&
        goalExists
    ) {

        new Chart(
            goalCanvas,
            {

                type: 'doughnut',

                data: {

                    labels: [

                        'Completed',

                        'Remaining'

                    ],

                    datasets: [

                        {

                            data: [

                                goalPercentage,

                                goalRemaining

                            ],

                            borderWidth: 2

                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    cutout: '70%',

                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            }
        );

    }


});

</script>

@endsection