@extends('auth.sidebar')

@section('title', 'Trainers List - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-sm-6">
            <h2 class="fw-bold text-dark mb-1 d-flex align-items-center">
                <i class="fas fa-chalkboard-teacher me-2" style="color: #2b215e;"></i>Trainers List
            </h2>
            <p class="text-muted small mb-0">Manage and oversee all gym trainers and their details</p>
        </div>
        <div class="col-sm-6 text-end">
            <a href="{{ route('trainers.create') }}" class="btn btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm border-0 text-white" style="background-color: #2b215e;">
                <i class="fa-solid fa-user-plus me-1"></i> Add New Trainer
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap mb-0" style="font-size: 14px;">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="py-3 ps-3">ID</th>
                            <th class="py-3">Branch</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Mobile</th>
                            <th class="py-3">Specialization</th>
                            <th class="py-3">Experience</th>
                            <th class="py-3">Salary</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainers as $trainer)
                        <tr>
                            <td class="ps-3 fw-semibold text-secondary">#{{ $trainer->id }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-code-branch me-1" style="color: #2b215e;"></i> {{ $trainer->branch->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="fw-bold" style="color: #2b215e;">{{ $trainer->name }}</td>
                            <!-- Email आणि Mobile गडद (Dark) केले आहेत -->
                            <td class="fw-semibold text-dark">{{ $trainer->email }}</td>
                            <td class="fw-semibold text-dark">{{ $trainer->mobile }}</td>
                            <td>
                                <span class="badge px-2 py-1 fw-semibold" style="background-color: rgba(43, 33, 94, 0.1); color: #2b215e;">
                                    {{ $trainer->specialization }}
                                </span>
                            </td>
                            <td class="fw-semibold text-secondary">{{ $trainer->experience }} Yrs</td>
                            <td class="fw-bold text-success">Rs. {{ number_format($trainer->salary, 2) }}</td>
                            <td>
                                @php
                                    $status = strtolower($trainer->status ?? 'active');
                                    $badgeClass = match($status) {
                                        'active' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'inactive' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                        default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-1 rounded-pill fw-bold">
                                    {{ ucfirst($trainer->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-2 align-items-center justify-content-end">
                                    <!-- View Button Trigger Modal -->
                                    <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle p-2 d-flex align-items-center justify-content-center view-trainer-btn" 
                                        style="width: 32px; height: 32px; color: #2b215e;" 
                                        title="View Details"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#trainerModal"
                                        data-id="{{ $trainer->id }}"
                                        data-branch="{{ $trainer->branch->name ?? 'N/A' }}"
                                        data-name="{{ $trainer->name }}"
                                        data-email="{{ $trainer->email }}"
                                        data-mobile="{{ $trainer->mobile }}"
                                        data-specialization="{{ $trainer->specialization }}"
                                        data-experience="{{ $trainer->experience }}"
                                        data-salary="{{ number_format($trainer->salary, 2) }}"
                                        data-status="{{ ucfirst($trainer->status ?? 'Active') }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <a href="{{ route('trainers.edit', $trainer->id) }}" class="btn btn-sm btn-light text-warning border shadow-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('trainers.destroy', $trainer->id) }}" method="POST" class="d-inline mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                No trainers found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Trainer Details Modal -->
<div class="modal fade" id="trainerModal" tabindex="-1" aria-labelledby="trainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white rounded-top-4" style="background-color: #2b215e;">
                <h5 class="modal-title fw-bold" id="trainerModalLabel">
                    <i class="fas fa-user-shield me-2"></i>Trainer Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Trainer ID:</span>
                        <span id="modal-id" class="fw-bold text-dark"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Branch:</span>
                        <span id="modal-branch" class="fw-bold text-dark"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Name:</span>
                        <span id="modal-name" class="fw-bold" style="color: #2b215e;"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Email:</span>
                        <span id="modal-email" class="fw-semibold text-dark"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Mobile:</span>
                        <span id="modal-mobile" class="fw-semibold text-dark"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Specialization:</span>
                        <span id="modal-specialization" class="badge px-2 py-1" style="background-color: rgba(43, 33, 94, 0.1); color: #2b215e;"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Experience:</span>
                        <span id="modal-experience" class="text-dark fw-semibold"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Salary:</span>
                        <span id="modal-salary" class="fw-bold text-success"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted fw-semibold">Status:</span>
                        <span id="modal-status" class="badge px-3 py-1 rounded-pill fw-bold"></span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to pass data into Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trainerModal = document.getElementById('trainerModal');
        trainerModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            document.getElementById('modal-id').textContent = '#' + button.getAttribute('data-id');
            document.getElementById('modal-branch').textContent = button.getAttribute('data-branch');
            document.getElementById('modal-name').textContent = button.getAttribute('data-name');
            document.getElementById('modal-email').textContent = button.getAttribute('data-email');
            document.getElementById('modal-mobile').textContent = button.getAttribute('data-mobile');
            document.getElementById('modal-specialization').textContent = button.getAttribute('data-specialization');
            document.getElementById('modal-experience').textContent = button.getAttribute('data-experience') + ' Yrs';
            document.getElementById('modal-salary').textContent = 'Rs. ' + button.getAttribute('data-salary');
            
            // Status Handling Fix
            const statusEl = document.getElementById('modal-status');
            const statusText = button.getAttribute('data-status') || 'Active';
            statusEl.textContent = statusText;
            
            if(statusText.toLowerCase() === 'active') {
                statusEl.className = 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold';
            } else {
                statusEl.className = 'badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill fw-bold';
            }
        });
    });
</script>
@endsection