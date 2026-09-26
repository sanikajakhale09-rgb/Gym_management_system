@extends('auth.sidebar')

@section('title', 'Membership Plans - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-tags text-primary me-2"></i>Membership Plans
            </h1>
            <p class="text-muted small mb-0">Manage and oversee all gym membership packages and pricing</p>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
        <!-- Card Header with Gradient -->
        <div class="card-header text-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center border-0" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);">
            <h5 class="m-0 fw-bold"><i class="fas fa-table me-2"></i>Plans List</h5>
            
            <div class="d-flex align-items-center gap-3 mt-2 mt-md-0">
                <!-- Branch Filter Dropdown -->
                <form method="GET" action="{{ route('membership.plans.index') }}" class="d-flex align-items-center m-0">
                    <select name="branch_id" class="form-select form-select-sm rounded-pill px-3 shadow-none border-0 fw-semibold text-secondary" onchange="this.form.submit()" style="background-color: rgba(255, 255, 255, 0.9);">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('membership.plans.create') }}" class="btn btn-light btn-sm rounded-pill px-4 text-nowrap fw-bold text-primary shadow-sm hover-scale">
                    <i class="fas fa-plus-circle me-1"></i> Add New Plan
                </a>
            </div>
        </div>

        <div class="card-body px-4 py-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-uppercase fs-7 text-secondary bg-light text-nowrap">
                        <tr>
                            <th class="py-3 ps-3 rounded-start">ID</th>
                            <th class="py-3">Branch</th>
                            <th class="py-3">Plan Name</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Reg. Fee</th>
                            <th class="py-3">Discount</th>
                            <th class="py-3">Tax (%)</th>
                            <th class="py-3">Total Payable</th>
                            <th class="py-3">Duration (Days)</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Description</th>
                            <th class="py-3 text-center rounded-end" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        @php
                            $subtotal = $plan->price + ($plan->registration_fee ?? 0);
                            $taxable = $subtotal - ($plan->discount ?? 0);
                            if($taxable < 0) $taxable = 0;
                            $taxAmount = ($taxable * ($plan->tax ?? 0)) / 100;
                            $finalTotal = $taxable + $taxAmount;
                        @endphp
                        <tr class="border-bottom">
                            <td class="py-3 ps-3 fw-semibold text-secondary">#{{ $plan->id }}</td>
                            <td class="py-3 fw-semibold text-dark text-nowrap">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-code-branch text-info me-1"></i> {{ $plan->branch->name ?? 'Main Branch' }}
                                </span>
                            </td>
                            <td class="py-3 fw-bold text-primary text-nowrap">{{ $plan->name }}</td>
                            <td class="py-3 text-nowrap text-secondary">₹{{ number_format($plan->price, 2) }}</td>
                            <td class="py-3 text-nowrap text-muted">₹{{ number_format($plan->registration_fee ?? 0, 2) }}</td>
                            <td class="py-3 text-nowrap text-danger">-₹{{ number_format($plan->discount ?? 0, 2) }}</td>
                            <td class="py-3 text-nowrap text-warning fw-semibold">{{ $plan->tax ?? 0 }}%</td>
                            <td class="py-3 text-nowrap fw-bold text-success fs-6">₹{{ number_format($finalTotal, 2) }}</td>
                            <td class="py-3 text-nowrap">
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 fw-semibold">
                                    <i class="far fa-clock me-1"></i> {{ $plan->duration_days }} Days
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge {{ strtolower($plan->status) == 'active' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25' }} px-3 py-1 rounded-pill fw-bold">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-muted small" style="min-width: 250px;">{{ $plan->description ?? '-' }}</td>
                            <td class="py-3 text-center pe-3">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="{{ route('membership.plans.edit', $plan->id) }}" class="btn btn-sm btn-light text-warning border shadow-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('membership.plans.destroy', $plan->id) }}" method="POST" class="mb-0" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection