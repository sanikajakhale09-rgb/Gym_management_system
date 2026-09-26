@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4">
    <!-- Success Alert Message -->
    @if(session('success'))
        <div class="row justify-content-center mt-3">
            <div class="col-md-10">
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-success text-white" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-3 text-center">
        <div class="col-md-12">
            <h2 class="mt-4 fw-bold" style="color: #1e3a8a;">Active Memberships</h2>
            <p class="text-muted">View and manage all currently active gym member subscriptions</p>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-4 bg-white" style="border-top: 4px solid #1e3a8a !important;">
                <div class="card-body p-4">
                    <form action="{{ route('membership.active') }}" method="GET" class="row g-3 align-items-center justify-content-center">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control rounded-pill px-3 shadow-none border" placeholder="Search by name or phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="branch_id" class="form-select rounded-pill px-3 shadow-none border">
                                <option value="">All Branches</option>
                                @if(isset($branches) && count($branches) > 0)
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2 text-center">
                            <button type="submit" class="btn rounded-pill w-100 fw-semibold text-white" style="background-color: #1e3a8a;"><i class="fas fa-filter me-1"></i> Filter</button>
                        </div>
                        <div class="col-md-2 text-center">
                            <a href="{{ route('membership.active') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-semibold"><i class="fas fa-redo me-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Members List Table -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i> Active Members List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="text-uppercase fs-7 text-white" style="background-color: #1e3a8a;">
                                <tr>
                                    <th class="py-3 ps-4 text-start">Member Name</th>
                                    <th>Plan Name</th>
                                    <th>Start Date</th>
                                    <th>Expiry Date</th>
                                    <th>Next Due Date</th>
                                    <th>Amount Paid</th>
                                    <th>Pending Amount</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeSubscriptions ?? [] as $subscription)
                                    @php
                                        /** @var \App\Models\MemberSubscription $subscription */
                                        $member = $subscription->member ?? null;
                                        $memberName = $member ? $member->name : 'Unknown Member';
                                        $memberPhone = $member ? $member->phone : '';
                                        
                                        $plan = $subscription->membershipPlan ?? ($subscription->plan ?? null);
                                        $planName = $plan ? $plan->name : 'N/A';
                                        $planId = $plan ? $plan->id : '';
                                        
                                        $startDate = $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d') : '';
                                        $endDate = $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') : '';
                                        $nextDueDate = $subscription->next_due_date ? \Carbon\Carbon::parse($subscription->next_due_date)->format('Y-m-d') : '';
                                        
                                        $startDateFormatted = $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('d M, Y') : '-';
                                        $endDateFormatted = $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M, Y') : '-';
                                        $nextDueDateFormatted = $subscription->next_due_date ? \Carbon\Carbon::parse($subscription->next_due_date)->format('d M, Y') : '-';
                                        
                                        $amountPaid = $subscription->amount_paid ?? $subscription->price_paid ?? 0;
                                        $pricePaidFormatted = number_format($amountPaid, 2);
                                        
                                        $balanceAmount = $subscription->balance_amount ?? 0;
                                        if ($balanceAmount == 0 && isset($subscription->total_amount)) {
                                            $balanceAmount = max(0, $subscription->total_amount - $amountPaid);
                                        }
                                        $pendingAmountFormatted = number_format($balanceAmount, 2);
                                    @endphp
                                    <tr>
                                        <td class="text-start ps-4">
                                            <strong class="text-dark">{{ $memberName }}</strong>
                                            <br><small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i>{{ $memberPhone }}</small>
                                        </td>
                                        <td><span class="badge px-2 py-1" style="background-color: #e0e7ff; color: #1e3a8a;">{{ $planName }}</span></td>
                                        <td>{{ $startDateFormatted }}</td>
                                        <td><span class="text-muted">{{ $endDateFormatted }}</span></td>
                                        <td><span class="fw-semibold text-danger">{{ $nextDueDateFormatted }}</span></td>
                                        <td class="fw-bold" style="color: #1e3a8a;">₹{{ $pricePaidFormatted }}</td>
                                        <td>
                                            @if($balanceAmount > 0)
                                                <span class="fw-bold text-danger">₹{{ $pendingAmountFormatted }}</span>
                                            @else
                                                <span class="text-success fw-semibold">₹0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-secondary fw-semibold">{{ ucfirst($subscription->status ?? 'Active') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill" role="group">
                                                <!-- View Profile -->
                                                <button type="button" class="btn btn-sm btn-light border text-primary" title="View Profile" 
                                                    data-bs-toggle="modal" data-bs-target="#viewProfileModal"
                                                    data-name="{{ $memberName }}"
                                                    data-phone="{{ $memberPhone }}"
                                                    data-plan="{{ $planName }}"
                                                    data-start="{{ $startDateFormatted }}"
                                                    data-end="{{ $endDateFormatted }}"
                                                    data-duedate="{{ $nextDueDateFormatted }}"
                                                    data-amount="{{ $pricePaidFormatted }}"
                                                    data-pending="{{ $pendingAmountFormatted }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Edit Subscription -->
                                                <button type="button" class="btn btn-sm btn-light border text-info" title="Edit Membership"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $subscription->id }}"
                                                    data-name="{{ $memberName }}"
                                                    data-planid="{{ $planId }}"
                                                    data-start="{{ $startDate }}"
                                                    data-end="{{ $endDate }}"
                                                    data-duedate="{{ $nextDueDate }}"
                                                    data-amount="{{ $amountPaid }}"
                                                    data-balance="{{ $balanceAmount }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Renew -->
                                                <button type="button" class="btn btn-sm btn-light border text-success" title="Renew"
                                                    data-bs-toggle="modal" data-bs-target="#renewModal"
                                                    data-id="{{ $subscription->id }}"
                                                    data-name="{{ $memberName }}">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>

                                                <!-- Freeze -->
                                                <button type="button" class="btn btn-sm btn-light border text-warning" title="Freeze Membership"
                                                    data-bs-toggle="modal" data-bs-target="#freezeModal"
                                                    data-id="{{ $subscription->id }}"
                                                    data-name="{{ $memberName }}">
                                                    <i class="fas fa-snowflake"></i>
                                                </button>

                                                <!-- Delete -->
                                                <button type="button" class="btn btn-sm btn-light border text-danger" title="Delete Membership"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $subscription->id }}"
                                                    data-name="{{ $memberName }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-2 text-secondary"></i>
                                            <p class="mb-0">No active memberships found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1. View Profile Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <h6 class="modal-title fw-bold" id="viewProfileModalLabel"><i class="fas fa-user-circle me-2"></i> Member Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="card border shadow-sm rounded-3 p-3 bg-white fs-7">
                    <p class="mb-2"><strong>Member Name:</strong> <span id="modalMemberName" class="text-dark"></span></p>
                    <p class="mb-2"><strong>Phone Number:</strong> <span id="modalMemberPhone" class="text-dark"></span></p>
                    <p class="mb-2"><strong>Plan Name:</strong> <span id="modalPlanName" class="text-dark"></span></p>
                    <p class="mb-2"><strong>Start Date:</strong> <span id="modalStartDate" class="text-dark"></span></p>
                    <p class="mb-2"><strong>Expiry Date:</strong> <span id="modalEndDate" class="text-dark"></span></p>
                    <p class="mb-2"><strong>Next Due Date:</strong> <span id="modalDueDate" class="text-danger fw-semibold"></span></p>
                    <p class="mb-2"><strong>Amount Paid:</strong> ₹<span id="modalAmountPaid" class="fw-bold" style="color: #1e3a8a;"></span></p>
                    <p class="mb-0"><strong>Pending Amount:</strong> ₹<span id="modalPendingAmount" class="fw-bold text-danger"></span></p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Edit Membership Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <form id="editForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h6 class="modal-title fw-bold" id="editModalLabel"><i class="fas fa-edit me-2"></i> Edit Membership</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="text-center mb-3 text-muted fs-7">Edit membership for <br><strong id="editMemberName" class="text-primary"></strong></p>
                    
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Membership Plan</label>
                        <select name="membership_plan_id" id="editPlanId" class="form-select form-select-sm rounded-pill px-3 shadow-none border" required>
                            <option value="">Select Plan</option>
                            @if(isset($plans))
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} (₹{{ $plan->price }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Start Date</label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control form-control-sm rounded-pill px-3 shadow-none border" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Expiry Date</label>
                        <input type="date" name="end_date" id="editEndDate" class="form-control form-control-sm rounded-pill px-3 shadow-none border" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Next Due Date</label>
                        <input type="date" name="next_due_date" id="editNextDueDate" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                        <!-- Error Message Container -->
                        <small id="dueDateError" class="text-danger fw-semibold d-none mt-1 px-2" style="font-size: 0.75rem;"></small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Amount Paid (₹)</label>
                        <input type="number" step="0.01" name="amount_paid" id="editAmountPaid" class="form-control form-control-sm rounded-pill px-3 shadow-none border" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Balance / Pending Amount (₹)</label>
                        <input type="number" step="0.01" name="balance_amount" id="editBalanceAmount" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="updateBtn" class="btn btn-sm rounded-pill px-3 fw-semibold text-white" style="background-color: #1e3a8a;">Update Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. Renew Membership Modal -->
<div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="renewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <form id="renewForm" action="" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h6 class="modal-title fw-bold" id="renewModalLabel"><i class="fas fa-sync-alt me-2"></i> Renew Membership</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="text-center mb-3 text-muted fs-7">Renew membership for <br><strong id="renewMemberName" class="text-primary"></strong>?</p>
                    
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Select New Plan / Extension</label>
                        <select name="membership_plan_id" class="form-select form-select-sm rounded-pill px-3 shadow-none border" required>
                            <option value="">Select Plan</option>
                            @if(isset($plans))
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} (₹{{ $plan->price }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Paid Amount (₹)</label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control form-control-sm rounded-pill px-3 shadow-none border" placeholder="Enter amount paid" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Select Trainer (Optional)</label>
                        <select name="trainer_id" class="form-select form-select-sm rounded-pill px-3 shadow-none border">
                            <option value="">Select Trainer</option>
                            @if(isset($trainers))
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">PT Charges (₹) (Optional)</label>
                        <input type="number" step="0.01" name="pt_charges" class="form-control form-control-sm rounded-pill px-3 shadow-none border" placeholder="Enter PT charges">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm rounded-pill px-3 shadow-none border" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Next Due Date</label>
                        <input type="date" name="next_due_date" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm rounded-pill px-3 fw-semibold text-white" style="background-color: #1e3a8a;">Confirm Renewal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 4. Freeze Membership Modal -->
<div class="modal fade" id="freezeModal" tabindex="-1" aria-labelledby="freezeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <form id="freezeForm" action="" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <h6 class="modal-title fw-bold" id="freezeModalLabel"><i class="fas fa-snowflake me-2"></i> Freeze Membership</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="text-center mb-3 text-muted fs-7">Freeze membership for <br><strong id="freezeMemberName" class="text-primary"></strong>?</p>
                    
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Freeze From Date</label>
                        <input type="date" name="freeze_from" class="form-control form-control-sm rounded-pill px-3 shadow-none border" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Freeze To Date</label>
                        <input type="date" name="freeze_to" class="form-control form-control-sm rounded-pill px-3 shadow-none border" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary fs-7">Reason / Notes</label>
                        <textarea name="freeze_reason" class="form-control form-control-sm rounded-4 p-2 shadow-none border" rows="2" placeholder="Enter reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm rounded-pill px-3 fw-semibold text-white" style="background-color: #1e3a8a;">Freeze</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 5. Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <form id="deleteForm" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-danger text-white py-3">
                    <h6 class="modal-title fw-bold" id="deleteModalLabel"><i class="fas fa-trash-alt me-2"></i> Delete Membership</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p class="text-muted mb-1">Are you sure you want to delete the subscription for</p>
                    <strong id="deleteMemberName" class="text-dark fs-6"></strong>?
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light border-0 py-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Modals & Due Date Validation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('download_pdf_id'))
            const subscriptionId = "{{ session('download_pdf_id') }}";
            const pdfUrl = "{{ url('/dashboard/memberships') }}/" + subscriptionId + "/receipt-pdf";
            
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = pdfUrl;
            document.body.appendChild(iframe);
        @endif

        // View Profile Modal Handler
        const viewModal = document.getElementById('viewProfileModal');
        if (viewModal) {
            viewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('modalMemberName').textContent = button.getAttribute('data-name');
                document.getElementById('modalMemberPhone').textContent = button.getAttribute('data-phone');
                document.getElementById('modalPlanName').textContent = button.getAttribute('data-plan');
                document.getElementById('modalStartDate').textContent = button.getAttribute('data-start');
                document.getElementById('modalEndDate').textContent = button.getAttribute('data-end');
                document.getElementById('modalDueDate').textContent = button.getAttribute('data-duedate');
                document.getElementById('modalAmountPaid').textContent = button.getAttribute('data-amount');
                document.getElementById('modalPendingAmount').textContent = button.getAttribute('data-pending');
            });
        }

        // Edit Modal Handler & Due Date Validation Logic
        const editModal = document.getElementById('editModal');
        if (editModal) {
            const editStartDate = document.getElementById('editStartDate');
            const editEndDate = document.getElementById('editEndDate');
            const editNextDueDate = document.getElementById('editNextDueDate');
            const dueDateError = document.getElementById('dueDateError');
            const updateBtn = document.getElementById('updateBtn');
            const editForm = document.getElementById('editForm');

            function validateDueDate() {
                if (!editNextDueDate.value) {
                    dueDateError.classList.add('d-none');
                    editNextDueDate.classList.remove('is-invalid');
                    updateBtn.disabled = false;
                    return true;
                }

                const start = new Date(editStartDate.value);
                const end = new Date(editEndDate.value);
                const due = new Date(editNextDueDate.value);

                // Set min and max limits on HTML5 input dynamically for convenience
                if (editStartDate.value) editNextDueDate.min = editStartDate.value;
                if (editEndDate.value) editNextDueDate.max = editEndDate.value;

                if (due < start || due > end) {
                    dueDateError.textContent = "Next due date must be between the start date and expiry date.";
                    dueDateError.classList.remove('d-none');
                    editNextDueDate.classList.add('is-invalid');
                    updateBtn.disabled = true;
                    return false;
                } else {
                    dueDateError.classList.add('d-none');
                    editNextDueDate.classList.remove('is-invalid');
                    updateBtn.disabled = false;
                    return true;
                }
            }

            editStartDate.addEventListener('change', validateDueDate);
            editEndDate.addEventListener('change', validateDueDate);
            editNextDueDate.addEventListener('input', validateDueDate);
            editNextDueDate.addEventListener('change', validateDueDate);

            editForm.addEventListener('submit', function (e) {
                if (!validateDueDate()) {
                    e.preventDefault();
                }
            });

            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const subscriptionId = button.getAttribute('data-id');
                
                document.getElementById('editMemberName').textContent = button.getAttribute('data-name');
                document.getElementById('editPlanId').value = button.getAttribute('data-planid');
                editStartDate.value = button.getAttribute('data-start');
                editEndDate.value = button.getAttribute('data-end');
                editNextDueDate.value = button.getAttribute('data-duedate');
                document.getElementById('editAmountPaid').value = button.getAttribute('data-amount');
                document.getElementById('editBalanceAmount').value = button.getAttribute('data-balance');
                
                // Set min/max and validate initially
                if(editStartDate.value) editNextDueDate.min = editStartDate.value;
                if(editEndDate.value) editNextDueDate.max = editEndDate.value;
                
                dueDateError.classList.add('d-none');
                editNextDueDate.classList.remove('is-invalid');
                updateBtn.disabled = false;

                editForm.action = "{{ url('/dashboard/memberships') }}/" + subscriptionId;
            });
        }

        // Renew Modal Handler
        const renewModal = document.getElementById('renewModal');
        if (renewModal) {
            renewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const subscriptionId = button.getAttribute('data-id');
                document.getElementById('renewMemberName').textContent = button.getAttribute('data-name');
                
                const form = document.getElementById('renewForm');
                form.action = "{{ url('/dashboard/memberships') }}/" + subscriptionId + "/renew";
            });
        }

        // Freeze Modal Handler
        const freezeModal = document.getElementById('freezeModal');
        if (freezeModal) {
            freezeModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const subscriptionId = button.getAttribute('data-id');
                document.getElementById('freezeMemberName').textContent = button.getAttribute('data-name');
                
                const form = document.getElementById('freezeForm');
                form.action = "{{ url('/dashboard/memberships') }}/" + subscriptionId + "/freeze";
            });
        }

        // Delete Modal Handler
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const subscriptionId = button.getAttribute('data-id');
                document.getElementById('deleteMemberName').textContent = button.getAttribute('data-name');
                
                const form = document.getElementById('deleteForm');
                form.action = "{{ url('/dashboard/memberships') }}/" + subscriptionId;
            });
        }
    });
</script>
@endsection