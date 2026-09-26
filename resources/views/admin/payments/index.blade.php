@extends('auth.sidebar')

@section('title', 'Payments - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm text-white border-0" role="alert" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-3">
        <h2 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-credit-card me-2" style="color: #7c3aed;"></i> Payments Management
        </h2>
        
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search Form -->
            <form action="{{ route('payments.index') }}" method="GET" class="input-group input-group-sm shadow-sm" style="width: 250px;">
                <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search invoice or member..." value="{{ request('search') }}">
                <button class="btn btn-primary px-3 rounded-end-pill" type="submit" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- Record Payment Button -->
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">
                <i class="fa-solid fa-plus me-1"></i> Record Payment
            </button>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" style="font-size: 15px;">
                    <thead class="text-white text-uppercase" style="background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%); font-size: 13px; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 ps-4">Invoice No</th>
                            <th class="py-3">Member Name</th>
                            <th class="py-3">Total / Paid / Due</th>
                            <th class="py-3">Method</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3">Staff</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="border-bottom">
                            <td class="ps-4 py-3 fw-bold" style="color: #7c3aed;">{{ $payment->invoice_no }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ optional($payment->member)->name ?? 'N/A' }}</span>
                            </td>
                            <!-- 🟢 Total, Paid & Remaining Balance Display -->
                            <td>
                                <span class="d-block text-dark fw-bold" style="font-size: 13px;">Total: ₹{{ number_format($payment->total_amount, 2) }}</span>
                                <span class="d-block text-success" style="font-size: 13px;">Paid: ₹{{ number_format($payment->amount, 2) }}</span>
                                <span class="d-block text-danger" style="font-size: 13px;">Due: ₹{{ number_format($payment->remaining_balance, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">{{ $payment->payment_method }}</span>
                            </td>
                            <td class="text-muted">{{ $payment->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                @if($payment->status == 'Paid')
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #d1fae5; color: #065f46;">Paid</span>
                                @elseif($payment->status == 'Partial')
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #fef3c7; color: #b45309;">Partial</span>
                                @elseif($payment->status == 'Pending')
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #fee2e2; color: #dc2626;">Pending</span>
                                @else
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #f3f4f6; color: #374151;">Refunded</span>
                                @endif
                            </td>
                            <td><span class="text-muted">{{ $payment->staff_name ?? 'Admin' }}</span></td>
                            
                            <!-- 🟢 Actions Column: View, Edit (Popup), Delete -->
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Receipt Button (Triggers View Modal) -->
                                    <button type="button" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2 text-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewPaymentModal{{ $payment->id }}" 
                                        title="View Receipt">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit Button (Triggers Edit Modal) -->
                                    <button type="button" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2 text-warning edit-payment-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPaymentModal"
                                        data-id="{{ $payment->id }}"
                                        data-member_id="{{ $payment->member_id }}"
                                        data-total_amount="{{ $payment->total_amount }}"
                                        data-amount="{{ $payment->amount }}"
                                        data-payment_method="{{ $payment->payment_method }}"
                                        data-status="{{ $payment->status }}"
                                        data-notes="{{ $payment->notes }}"
                                        title="Edit Payment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2 text-danger" title="Delete Payment">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No payment records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Record Payment (Add) -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Record New Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Select Member</label>
                        <select name="member_id" class="form-select rounded-pill px-3" required>
                            <option value="">Choose Member...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Total Amount Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Total Amount (₹)</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control rounded-pill px-3" placeholder="Enter total amount" required>
                    </div>
                    <!-- Paid Amount Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Paid Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" class="form-control rounded-pill px-3" placeholder="Enter paid amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Payment Method</label>
                        <select name="payment_method" class="form-select rounded-pill px-3" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Payment Status</label>
                        <select name="status" class="form-select rounded-pill px-3" required>
                            <option value="Paid">Paid</option>
                            <option value="Partial">Partial</option>
                            <option value="Pending">Pending</option>
                            <option value="Refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea name="notes" class="form-control rounded-3 px-3" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🟢 Modals: View Payment Details Loop Popups -->
@foreach($payments as $payment)
<div class="modal fade" id="viewPaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-invoice me-2"></i> Payment Invoice Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-2"><strong>Invoice No:</strong> <span class="text-purple fw-bold">{{ $payment->invoice_no }}</span></div>
                <div class="mb-2"><strong>Member Name:</strong> {{ optional($payment->member)->name ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Total Amount:</strong> ₹{{ number_format($payment->total_amount, 2) }}</div>
                <div class="mb-2"><strong>Amount Paid:</strong> ₹{{ number_format($payment->amount, 2) }}</div>
                <div class="mb-2 text-danger"><strong>Remaining Balance (Due):</strong> ₹{{ number_format($payment->remaining_balance, 2) }}</div>
                <div class="mb-2"><strong>Payment Method:</strong> {{ $payment->payment_method }}</div>
                <div class="mb-2"><strong>Status:</strong> {{ $payment->status }}</div>
                <div class="mb-2"><strong>Date:</strong> {{ $payment->created_at->format('d M Y, h:i A') }}</div>
                <div class="mb-2"><strong>Staff / Handler:</strong> {{ $payment->staff_name ?? 'Admin' }}</div>
                <div class="mb-2"><strong>Notes:</strong> {{ $payment->notes ?? 'No notes available.' }}</div>
            </div>
            <div class="modal-footer border-0 bg-white px-4 py-3">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- 🟢 Modal: Edit Payment Popup -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="editPaymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Payment Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Select Member</label>
                        <select name="member_id" id="edit_member_id" class="form-select rounded-pill px-3" required>
                            <option value="">Choose Member...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Edit Total Amount Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Total Amount (₹)</label>
                        <input type="number" step="0.01" name="total_amount" id="edit_total_amount" class="form-control rounded-pill px-3" placeholder="Enter total amount" required>
                    </div>
                    <!-- Edit Paid Amount Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Paid Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control rounded-pill px-3" placeholder="Enter paid amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Payment Method</label>
                        <select name="payment_method" id="edit_payment_method" class="form-select rounded-pill px-3" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Payment Status</label>
                        <select name="status" id="edit_status" class="form-select rounded-pill px-3" required>
                            <option value="Paid">Paid</option>
                            <option value="Partial">Partial</option>
                            <option value="Pending">Pending</option>
                            <option value="Refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea name="notes" id="edit_notes" class="form-control rounded-3 px-3" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🟢 JavaScript: Edit Modal Data Feeder -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-payment-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const memberId = this.getAttribute('data-member_id');
                const totalAmount = this.getAttribute('data-total_amount'); // Added
                const amount = this.getAttribute('data-amount');
                const paymentMethod = this.getAttribute('data-payment_method');
                const status = this.getAttribute('data-status');
                const notes = this.getAttribute('data-notes');

                // Form action URL अपडेट करणे
                const form = document.getElementById('editPaymentForm');
                form.action = `/dashboard/payments/${id}`;

                // Fields मध्ये डेटा भरणे
                document.getElementById('edit_member_id').value = memberId;
                document.getElementById('edit_total_amount').value = totalAmount; // Added
                document.getElementById('edit_amount').value = amount;
                document.getElementById('edit_payment_method').value = paymentMethod;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_notes').value = notes || '';
            });
        });
    });
</script>
@endpush

@endsection