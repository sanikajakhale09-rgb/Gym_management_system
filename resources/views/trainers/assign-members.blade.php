@extends('auth.sidebar')

@section('title', 'Assign Members to Trainer - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 100vh;">
    
    <!-- Page Header -->
    <div class="text-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-user-plus text-primary me-2"></i>Assign Members to Trainer
        </h1>
        <p class="text-muted small mb-0">Assign gym members to their respective trainers</p>
    </div>

    <!-- Centered Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <!-- Success Alert Notification -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert" style="background-color: #d1fae5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
                    <h5 class="m-0 fw-bold"><i class="fas fa-link me-2"></i>Assignment Form</h5>
                </div>

                <div class="card-body px-4 py-4">
                    <form action="{{ route('trainers.assign.store') }}" method="POST">
                        @csrf
                        
                        <!-- Automatic Trainer Charges -->
                        <input type="hidden" name="trainer_charges" value="1500">

                        <!-- Branch Selection -->
                        <div class="mb-3">
                            <label for="branch_id" class="form-label fw-semibold text-secondary small text-uppercase">Branch <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                    <i class="fas fa-code-branch"></i>
                                </span>
                                <select name="branch_id" id="branch_id" class="form-select border-start-0 rounded-end-pill py-2 shadow-none" required>
                                    <option value="">Select Gym Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Trainer Selection -->
                        <div class="mb-3">
                            <label for="trainer_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Trainer <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                    <i class="fas fa-user-tie"></i>
                                </span>
                                <select name="trainer_id" id="trainer_id" class="form-select border-start-0 rounded-end-pill py-2 shadow-none" required>
                                    <option value="">Choose Gym Trainer...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Member Selection -->
                        <div class="mb-3">
                            <label for="member_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Member <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                    <i class="fas fa-user"></i>
                                </span>
                                <select name="member_id" id="member_id" class="form-select border-start-0 rounded-end-pill py-2 shadow-none" required>
                                    <option value="">Choose Gym Member...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Start Date & End Date -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label fw-semibold text-secondary small text-uppercase">Assignment Start Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="date" name="start_date" id="start_date" class="form-control border-start-0 rounded-end-pill py-2 shadow-none" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label fw-semibold text-secondary small text-uppercase">Assignment End Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>
                                    <!-- Default value set to 1 month ahead to avoid empty N/A values -->
                                    <input type="date" name="end_date" id="end_date" class="form-control border-start-0 rounded-end-pill py-2 shadow-none" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background-color: #4f46e5; border: none;">
                                <i class="fas fa-check-circle me-1"></i> Assign Member (₹1500 Charges)
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assigned Members Listing Table -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mt-4">
                <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <h5 class="m-0 fw-bold"><i class="fas fa-list me-2"></i>Assigned Members List</h5>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th>Member Name</th>
                                    <th>Assigned Trainer</th>
                                    <th>Branch</th>
                                    <th>Charges</th>
                                    <th>Duration</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($assignedMembers) && count($assignedMembers) > 0)
                                    @foreach($assignedMembers as $assignment)
                                        <tr class="assigned-row" data-branch-id="{{ is_object($assignment) && isset($assignment->branch_id) ? $assignment->branch_id : (is_object($assignment->branch) ? $assignment->branch->id : '') }}" style="display: none;">
                                            <td class="fw-semibold">{{ is_object($assignment) ? ($assignment->name ?? ($assignment->member->name ?? 'N/A')) : 'N/A' }}</td>
                                            <td>{{ (is_object($assignment) && isset($assignment->trainer) && is_object($assignment->trainer)) ? $assignment->trainer->name : 'N/A' }}</td>
                                            <td>{{ (is_object($assignment) && isset($assignment->branch) && is_object($assignment->branch)) ? $assignment->branch->name : 'N/A' }}</td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">₹{{ number_format($assignment->trainer_charges ?? 1500, 2) }}</span></td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $assignment->start_date ?? 'N/A' }} to {{ $assignment->end_date ?? 'N/A' }}
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                @if(is_object($assignment) && isset($assignment->id))
                                                    <form action="{{ route('trainers.unassign', $assignment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" onclick="return confirm('Are you sure you want to unassign this member?')">
                                                            <i class="fas fa-unlink me-1"></i> Unassign
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr id="no-members-row">
                                        <td colspan="6" class="text-center text-muted py-3">No members assigned to trainers yet.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#branch_id').on('change', function() {
        var branchId = $(this).val();
        
        $('#trainer_id').html('<option value="">Choose Gym Trainer...</option>');
        $('#member_id').html('<option value="">Choose Gym Member...</option>');

        if(branchId === "") {
            $('.assigned-row').hide();
        } else {
            $('.assigned-row').each(function() {
                var rowBranchId = $(this).data('branch-id');
                if(rowBranchId == branchId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        if(branchId) {
            // Fetch Trainers via AJAX
            $.ajax({
                url: "{{ url('/dashboard/get-trainers') }}/" + branchId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.length > 0) {
                        $.each(data, function(key, trainer) {
                            $('#trainer_id').append('<option value="'+ trainer.id +'">'+ trainer.name +'</option>');
                        });
                    } else {
                        $('#trainer_id').append('<option value="" disabled>No trainers found in this branch</option>');
                    }
                }
            });

            // Fetch Members via AJAX
            $.ajax({
                url: "{{ url('/dashboard/get-members') }}/" + branchId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.length > 0) {
                        $.each(data, function(key, member) {
                            $('#member_id').append('<option value="'+ member.id +'">'+ member.name +'</option>');
                        });
                    } else {
                        $('#member_id').append('<option value="" disabled>No members found in this branch</option>');
                    }
                }
            });
        }
    });
});
</script>
@endsection