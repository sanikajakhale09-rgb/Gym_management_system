@extends('auth.sidebar')

@section('title', 'Edit Membership Plan - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-edit text-warning me-2"></i>Edit Membership Plan
            </h1>
            <p class="text-muted small mb-0">Modify subscription details, pricing, and active duration</p>
        </div>
        <a href="{{ route('membership.plans.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm bg-white">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <!-- Card Header -->
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="m-0 fw-bold text-dark"><i class="fas id-card me-2 text-warning"></i>Plan Edit Form</h5>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('membership.plans.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Plan Name Dropdown with Auto-Duration Mapping -->
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label fw-semibold text-secondary small text-uppercase">Plan Name *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-tag"></i></span>
                                    <select class="form-select border-0 bg-white shadow-none ps-2" id="name" name="name" required>
                                        <option value="">Select Plan Name</option>
                                        <option value="Monthly Basic" data-duration="30" {{ $plan->name == 'Monthly Basic' ? 'selected' : '' }}>Monthly Basic (30 Days)</option>
                                        <option value="Quarterly Plan" data-duration="90" {{ $plan->name == 'Quarterly Plan' ? 'selected' : '' }}>Quarterly Plan (90 Days)</option>
                                        <option value="Half Yearly Plan" data-duration="180" {{ $plan->name == 'Half Yearly Plan' ? 'selected' : '' }}>Half Yearly Plan (180 Days)</option>
                                        <option value="Yearly Plan" data-duration="365" {{ $plan->name == 'Yearly Plan' ? 'selected' : '' }}>Yearly Plan (365 Days)</option>
                                        <option value="Personal Training" data-duration="30" {{ $plan->name == 'Personal Training' ? 'selected' : '' }}>Personal Training (30 Days)</option>
                                        @if(!empty($plans))
                                            @foreach($plans as $p)
                                                <option value="{{ is_object($p) ? $p->name : $p['name'] }}" data-duration="{{ is_object($p) ? $p->duration_days : $p['duration_days'] }}" {{ $plan->name == (is_object($p) ? $p->name : $p['name']) ? 'selected' : '' }}>
                                                    {{ is_object($p) ? $p->name : $p['name'] }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="duration_days" class="form-label fw-semibold text-secondary small text-uppercase">Duration (Days) *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-info"><i class="far fa-clock"></i></span>
                                    <input type="number" class="form-control border-0 bg-white shadow-none ps-2" id="duration_days" name="duration_days" value="{{ $plan->duration_days }}" required placeholder="e.g. 30">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label fw-semibold text-secondary small text-uppercase">Price (₹) *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-success"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="price" name="price" value="{{ $plan->price }}" required placeholder="e.g. 1500">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="registration_fee" class="form-label fw-semibold text-secondary small text-uppercase">Registration Fee (₹)</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="registration_fee" name="registration_fee" value="{{ $plan->registration_fee }}" placeholder="e.g. 500">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="status" class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-success"><i class="fas fa-toggle-on"></i></span>
                                    <select class="form-select border-0 bg-white shadow-none ps-2" id="status" name="status">
                                        <option value="active" {{ $plan->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $plan->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-secondary small text-uppercase">Description</label>
                            <textarea class="form-control shadow-sm rounded-4 border p-3 bg-white" id="description" name="description" rows="3" placeholder="Plan details...">{{ $plan->description }}</textarea>
                        </div>

                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-semibold shadow-sm border-0 text-white">
                                <i class="fas fa-sync-alt me-1"></i> Update Plan
                            </button>
                            <a href="{{ route('membership.plans.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary border shadow-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script for Auto Duration Mapping -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const nameSelect = document.getElementById('name');
        const durationInput = document.getElementById('duration_days');

        nameSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const duration = selectedOption.getAttribute('data-duration');
            if (duration) {
                durationInput.value = duration;
            }
        });
    });
</script>
@endsection