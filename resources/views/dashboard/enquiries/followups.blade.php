@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-calendar-alt text-primary me-2"></i>Follow-ups Management
            </h2>
            <p class="text-muted mb-0">Manage all customer follow-ups and enquiries efficiently</p>
        </div>
        <div>
            <a href="{{ route('enquiries.index') }}" class="btn btn-outline-primary px-3 py-2 rounded-pill shadow-sm transition-all">
                <i class="fas fa-arrow-left me-1"></i> Back to Enquiries
            </a>
        </div>
    </div>

    <!-- 🟢 Filter Navigation Tabs / Buttons -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
        <div class="card-body p-3 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <!-- Status Filters -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('enquiries.follow-ups') }}" class="btn btn-sm px-3 rounded-pill {{ !request('status') && !request('filter') ? 'btn-dark shadow-sm' : 'btn-outline-secondary' }}">
                    <i class="fas fa-list me-1"></i> All Records
                </a>
                <a href="{{ route('enquiries.follow-ups', ['status' => 'Pending']) }}" class="btn btn-sm px-3 rounded-pill {{ request('status') == 'Pending' ? 'btn-warning text-dark fw-semibold shadow-sm' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock me-1"></i> Pending
                </a>
                <a href="{{ route('enquiries.follow-ups', ['status' => 'Overdue']) }}" class="btn btn-sm px-3 rounded-pill {{ request('status') == 'Overdue' ? 'btn-danger shadow-sm' : 'btn-outline-danger' }}">
                    <i class="fas fa-exclamation-circle me-1"></i> Overdue
                </a>
                <a href="{{ route('enquiries.follow-ups', ['status' => 'Contacted']) }}" class="btn btn-sm px-3 rounded-pill {{ request('status') == 'Contacted' ? 'btn-info text-white shadow-sm' : 'btn-outline-info' }}">
                    <i class="fas fa-phone-alt me-1"></i> Contacted
                </a>
                <a href="{{ route('enquiries.follow-ups', ['status' => 'Completed']) }}" class="btn btn-sm px-3 rounded-pill {{ request('status') == 'Completed' ? 'btn-success shadow-sm' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle me-1"></i> Completed
                </a>
            </div>

            <!-- Date Filters -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('enquiries.follow-ups', ['filter' => 'yesterday']) }}" class="btn btn-sm px-3 rounded-pill {{ request('filter') == 'yesterday' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">
                    Yesterday
                </a>
                <a href="{{ route('enquiries.follow-ups', ['filter' => 'today']) }}" class="btn btn-sm px-3 rounded-pill {{ request('filter') == 'today' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">
                    Today
                </a>
                <a href="{{ route('enquiries.follow-ups', ['filter' => 'tomorrow']) }}" class="btn btn-sm px-3 rounded-pill {{ request('filter') == 'tomorrow' ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">
                    Tomorrow
                </a>
            </div>
        </div>
    </div>

    <!-- Main Card Section -->
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden bg-white">
                
                <!-- Card Header -->
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-calendar-check me-2"></i> Follow-ups List & Details
                    </h5>
                    <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill shadow-sm">
                        {{ isset($todayFollowups) ? $todayFollowups->count() : 0 }} Records Found
                    </span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary text-uppercase fs-7 border-bottom">
                                <tr>
                                    <th class="py-3 ps-3">#</th>
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Phone</th>
                                    <th class="py-3">Enquiry Date</th>
                                    <th class="py-3">Follow-up Date</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Response / Remarks</th>
                                    <th class="py-3 text-center pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($todayFollowups) && $todayFollowups->count() > 0)
                                    @foreach($todayFollowups as $index => $followup)
                                        <tr class="border-bottom">
                                            <td class="fw-bold text-muted ps-3">{{ $index + 1 }}</td>
                                            <td class="fw-bold text-dark">{{ $followup->name }}</td>
                                            <td>
                                                <a href="tel:{{ $followup->phone }}" class="text-decoration-none text-secondary">
                                                    <i class="fas fa-phone-alt text-muted small me-1"></i> {{ $followup->phone }}
                                                </a>
                                            </td>
                                            <td class="text-muted small">{{ $followup->created_at ? $followup->created_at->format('d M Y') : '-' }}</td>
                                            <td>
                                                <span class="badge bg-light text-danger border border-danger-subtle px-2 py-1 fw-semibold">
                                                    <i class="far fa-clock me-1"></i> {{ $followup->follow_up_date ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeBg = 'warning text-dark';
                                                    if($followup->status == 'Completed') $badgeBg = 'success';
                                                    elseif($followup->status == 'Overdue') $badgeBg = 'danger';
                                                    elseif($followup->status == 'Contacted') $badgeBg = 'info text-white';
                                                @endphp
                                                <span class="badge bg-{{ $badgeBg }} px-3 py-2 rounded-pill shadow-sm">{{ $followup->status ?? 'Pending' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary small d-inline-block text-truncate" style="max-width: 180px;" title="{{ $followup->follow_up_response }}">
                                                    {{ $followup->follow_up_response ?? 'No remarks yet' }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- WhatsApp Direct Chat Button -->
                                                    @php
                                                        $waMessage = "Hello " . $followup->name . ", thank you for your interest in AURA Strength Club! Following up on your enquiry.";
                                                    @endphp
                                                    <a href="https://api.whatsapp.com/send?phone=91{{ $followup->phone }}&text={{ urlencode($waMessage) }}" target="_blank" class="btn btn-sm btn-success rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Send WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>

                                                    <!-- View Details Button -->
                                                    <button class="btn btn-sm btn-outline-info rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#viewModal{{ $followup->id }}" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <!-- Update Follow-up Button -->
                                                    <button class="btn btn-sm btn-outline-primary rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#updateModal{{ $followup->id }}" title="Update Follow-up">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $followup->id }}" title="Delete Enquiry">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- 🟢 1. View Modal for each enquiry -->
                                        <div class="modal fade" id="viewModal{{ $followup->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <div class="modal-header bg-info text-white px-4 py-3">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i> Enquiry Details: {{ $followup->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start p-4">
                                                        <ul class="list-group list-group-flush rounded-3 border">
                                                            <li class="list-group-item d-flex justify-content-between py-3">
                                                                <span class="fw-semibold text-secondary">Name:</span> <span class="fw-bold text-dark">{{ $followup->name }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between py-3">
                                                                <span class="fw-semibold text-secondary">Phone:</span> <span class="text-dark">{{ $followup->phone ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between py-3">
                                                                <span class="fw-semibold text-secondary">Email:</span> <span class="text-dark">{{ $followup->email ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between py-3">
                                                                <span class="fw-semibold text-secondary">Status:</span> <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $followup->status }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between py-3">
                                                                <span class="fw-semibold text-secondary">Follow-up Date:</span> <span class="text-danger fw-semibold">{{ $followup->follow_up_date ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item py-3 border-0">
                                                                <span class="fw-semibold text-secondary d-block mb-1">Response / Remarks:</span>
                                                                <p class="text-dark bg-light p-3 rounded-3 mb-0 border">{{ $followup->follow_up_response ?? 'No remarks available.' }}</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer bg-light px-4 py-3">
                                                        <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 🟢 2. Update Modal for each enquiry -->
                                        <div class="modal fade" id="updateModal{{ $followup->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <form action="{{ route('enquiries.update', $followup->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header bg-primary text-white px-4 py-3">
                                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Update Follow-up: {{ $followup->name }}</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start p-4">
                                                            <input type="hidden" name="name" value="{{ $followup->name }}">
                                                            <input type="hidden" name="phone" value="{{ $followup->phone }}">
                                                            <input type="hidden" name="email" value="{{ $followup->email }}">

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold text-secondary">Follow-up Status</label>
                                                                <select name="status" class="form-select rounded-3 py-2 shadow-none border-secondary-subtle" required>
                                                                    <option value="Pending" {{ $followup->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                    <option value="Completed" {{ $followup->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                    <option value="Contacted" {{ $followup->status == 'Contacted' ? 'selected' : '' }}>Contacted</option>
                                                                    <option value="Overdue" {{ $followup->status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                                                    <option value="Rescheduled" {{ $followup->status == 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                                    <option value="Cancelled" {{ $followup->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold text-secondary">Follow-up Response / Remarks</label>
                                                                <textarea name="follow_up_response" class="form-control rounded-3 shadow-none border-secondary-subtle" rows="3" placeholder="Enter customer response or discussion notes...">{{ $followup->follow_up_response ?? '' }}</textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold text-secondary">Next Follow-up Date</label>
                                                                <input type="date" name="follow_up_date" class="form-control rounded-3 py-2 shadow-none border-secondary-subtle" value="{{ $followup->follow_up_date }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light px-4 py-3">
                                                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">Save Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 🟢 3. Delete Modal for each enquiry -->
                                        <div class="modal fade" id="deleteModal{{ $followup->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <form action="{{ route('enquiries.destroy', $followup->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-header bg-danger text-white px-4 py-3">
                                                            <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Delete</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center py-5 px-4">
                                                            <div class="mb-3 text-danger bg-danger bg-opacity-10 d-inline-flex p-3 rounded-circle">
                                                                <i class="fas fa-trash-alt fa-2x"></i>
                                                            </div>
                                                            <p class="mb-1 fs-5 fw-bold text-dark">Are you sure you want to delete?</p>
                                                            <p class="text-muted small mb-0">Enquiry record for <strong>{{ $followup->name }}</strong> will be permanently removed from the system.</p>
                                                        </div>
                                                        <div class="modal-footer bg-light justify-content-center px-4 py-3">
                                                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm">Yes, Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted py-5">
                                                <div class="mb-3 bg-light d-inline-flex p-4 rounded-circle text-secondary">
                                                    <i class="fas fa-calendar-times fa-3x opacity-50"></i>
                                                </div>
                                                <p class="mb-0 fs-5 fw-semibold text-dark">No follow-ups or enquiries found.</p>
                                                <p class="text-muted small">Try checking back later or clearing your filters.</p>
                                            </div>
                                        </td>
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
@endsection