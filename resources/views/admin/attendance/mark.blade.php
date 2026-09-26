@extends('auth.sidebar')



@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <i class="fas fa-user-check text-primary me-2"></i> Mark Attendance
            </h2>
            <p class="text-muted mb-0">Record daily attendance for gym members safely and quickly.</p>
        </div>
        <div>
            <a href="{{ route('attendance.today') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm fw-bold">
                <i class="fas fa-list-alt me-1"></i> View Today's Attendance
            </a>
        </div>
    </div>

    <!-- Success Message Alert -->
    @if(session('success'))
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Centered & Attractive Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient text-white py-3 px-4" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <h5 class="mb-0 fw-semibold text-white"><i class="fas fa-edit me-2"></i> Attendance Details Form</h5>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('attendance.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Branch Selection -->
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label fw-bold text-secondary">Select Branch <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3 text-secondary rounded-start-pill">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <select name="branch_id" id="branch_id" class="form-select border-0 bg-light shadow-none rounded-end-pill py-2" required onchange="filterMembersByBranch(this.value)">
                                        <option value="">-- Choose Branch --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (isset($branchId) && $branchId == $branch->id) ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Member Selection -->
                            <div class="col-md-6">
                                <label for="member_id" class="form-label fw-bold text-secondary">Select Member <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3 text-secondary rounded-start-pill">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <select name="member_id" id="member_id" class="form-select border-0 bg-light shadow-none rounded-end-pill py-2" required>
                                        <option value="">-- Choose Member --</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" data-branch="{{ $member->branch_id }}">
                                                {{ $member->name }} ({{ $member->member_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Selection -->
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-bold text-secondary">Attendance Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3 text-secondary rounded-start-pill">
                                        <i class="fas fa-clipboard-check"></i>
                                    </span>
                                    <select name="status" id="status" class="form-select border-0 bg-light shadow-none rounded-end-pill py-2" required>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button Center Aligned -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
                                <i class="fas fa-check-circle me-2"></i> Mark Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to filter members based on selected branch dynamically -->
<script>
    function filterMembersByBranch(branchId) {
        let memberSelect = document.getElementById('member_id');
        let options = memberSelect.options;
        
        memberSelect.value = ""; // Reset selection

        for (let i = 0; i < options.length; i++) {
            let option = options[i];
            let optionBranch = option.getAttribute('data-branch');

            if (i === 0) {
                option.style.display = 'block'; // Keep "-- Choose Member --" visible
                continue;
            }

            if (!branchId || optionBranch === branchId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    }
</script>
@endsection