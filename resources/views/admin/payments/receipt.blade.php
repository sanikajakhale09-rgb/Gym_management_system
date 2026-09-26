@extends('auth.sidebar')

@push('styles')
<style>
/* Custom Dark-Blue Theme & Styling */
.bg-navy {
    background-color: #1e3a8a !important;
}
.text-navy {
    color: #1e3a8a !important;
}
.btn-navy {
    background-color: #1e3a8a;
    color: #fff;
    border-color: #1e3a8a;
    transition: all 0.3s ease;
}
.btn-navy:hover {
    background-color: #1d4ed8;
    color: #fff;
}
.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.04);
}
.table-hover tbody tr:hover {
    background-color: rgba(30, 58, 138, 0.03);
}
.pagination {
    margin-bottom: 0;
}
.page-item .page-link {
    border-radius: 50px !important;
    margin: 0 3px;
    color: #1e3a8a;
    border: 1px solid #e5e7eb;
}
.page-item.active .page-link {
    background-color: #1e3a8a;
    border-color: #1e3a8a;
    color: #fff;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-receipt text-navy me-2"></i> Payment Receipts</h2>
                    <p class="text-muted mb-0">Manage and view all generated payment receipts seamlessly.</p>
                </div>
                <div>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill fs-6">
                        <i class="fas fa-calendar-alt text-navy me-1"></i> {{ date('d M, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-10">
            <div class="card shadow-sm border-0 rounded-4 bg-white p-3">
                <form action="{{ route('receipts.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="search" class="form-control rounded-3 py-2" placeholder="Receipt / Invoice / Member..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Date From</label>
                        <input type="date" name="date_from" class="form-control rounded-3 py-2" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Date To</label>
                        <input type="date" name="date_to" class="form-control rounded-3 py-2" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Payment Method</label>
                        <select name="payment_method" class="form-select rounded-3 py-2">
                            <option value="All Methods" {{ request('payment_method') == 'All Methods' ? 'selected' : '' }}>All Methods</option>
                            <option value="UPI" {{ request('payment_method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                            <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Card" {{ request('payment_method') == 'Card' ? 'selected' : '' }}>Card</option>
                            <option value="NetBanking" {{ request('payment_method') == 'NetBanking' ? 'selected' : '' }}>NetBanking</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2 mt-4">
                        <button type="submit" class="btn btn-navy w-50 rounded-pill py-2 fw-semibold shadow-sm">
                            <i class="fas fa-filter me-1"></i> Search
                        </button>
                        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary w-50 rounded-pill py-2 fw-semibold">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receipts Table Container -->
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-list text-navy me-2"></i> Generated Receipts List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-navy text-white text-uppercase fs-7 tracking-wider">
                                <tr>
                                    <th class="ps-4">Receipt No.</th>
                                    <th>Invoice No.</th>
                                    <th>Member Name</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Balance</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $rec)
                                <tr>
                                    <td class="ps-4 fw-bold text-navy">
                                        {{ $rec->receipt_number }}
                                    </td>
                                    <td class="fw-semibold text-secondary">{{ optional($rec->payment)->invoice_no ?? 'N/A' }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ optional($rec->member)->name ?? 'N/A' }}</div>
                                        <small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i> {{ optional($rec->member)->phone ?? 'No Phone' }}</small>
                                    </td>
                                    <!-- Total Amount -->
                                    <td class="fw-bold text-dark">
                                        ₹{{ number_format(optional($rec->payment)->total_amount ?? $rec->amount, 2) }}
                                    </td>
                                    <!-- Paid Amount -->
                                    <td class="text-success fw-bold">
                                        ₹{{ number_format($rec->amount, 2) }}
                                    </td>
                                    <!-- Due Balance -->
                                    <td class="text-danger fw-bold">
                                        ₹{{ number_format(optional($rec->payment)->remaining_balance ?? 0, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $rec->payment_method }}</span>
                                    </td>
                                    <td><span class="text-secondary"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($rec->receipt_date)->format('d M Y') }}</span></td>
                                    <td>
                                        @if(optional($rec->payment)->remaining_balance <= 0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-semibold">Paid</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill fw-semibold text-dark">Partial</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- View Modal Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm view-receipt-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewReceiptModal{{ $rec->id }}" 
                                                    data-id="{{ $rec->id }}"
                                                    title="View Receipt">
                                                <i class="fas fa-eye"></i> View
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('receipts.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this receipt?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" title="Delete Receipt">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <div class="my-4">
                                            <i class="fas fa-folder-open fa-3x text-secondary mb-3 opacity-50"></i>
                                            <p class="fs-5 fw-semibold mb-0">No Payment Receipts Found.</p>
                                            <small class="text-muted">Receipts will be generated automatically once payments are recorded.</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Correct Centered Pagination Links -->
            <div class="d-flex justify-content-center mt-3 mb-5">
                {{ $receipts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modals Rendered Outside Table Body -->
@foreach($receipts as $rec)
<div class="modal fade" id="viewReceiptModal{{ $rec->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-navy text-white border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-receipt text-white me-2"></i> Payment Receipt: <span class="receiptNo">{{ $rec->receipt_number }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start printable-receipt" id="printableArea{{ $rec->id }}">
                <div class="p-4 border rounded-3 bg-white shadow-sm">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark mb-1">{{ optional(optional($rec->member)->branch)->name ?? 'GYM NAME' }}</h3>
                        <hr class="w-25 mx-auto">
                        <h5 class="fw-bold text-uppercase text-navy tracking-wide">Payment Receipt</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="mb-1"><strong>Receipt No:</strong> <span class="receiptNo">{{ $rec->receipt_number }}</span></p>
                            <p class="mb-1"><strong>Invoice No:</strong> <span class="invoiceNo">{{ optional($rec->payment)->invoice_no ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1"><strong>Receipt Date:</strong> <span class="receiptDate">{{ \Carbon\Carbon::parse($rec->receipt_date)->format('d-m-Y') }}</span></p>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <p class="mb-1"><strong>Member Name:</strong> <span class="memberName">{{ optional($rec->member)->name }}</span></p>
                        <p class="mb-1"><strong>Member ID:</strong> <span class="memberId">GYM-2026-{{ str_pad(optional($rec->member)->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span></p>
                        <p class="mb-0"><strong>Mobile:</strong> <span class="memberMobile">{{ optional($rec->member)->phone ?? 'N/A' }}</span></p>
                    </div>

                    <!-- Price Breakdown Section -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar text-navy me-1"></i> Price Breakdown</h6>
                    
                    <div class="mb-3">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Membership Fee:</td>
                                <td class="text-end fw-semibold membershipFeeText">₹{{ number_format($rec->membership_fee ?? optional($rec->invoice)->membership_fee ?? optional($rec->payment)->base_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Registration Fee:</td>
                                <td class="text-end fw-semibold registrationFeeText">₹{{ number_format($rec->registration_fee ?? optional($rec->payment)->registration_fee ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount:</td>
                                <td class="text-end text-danger fw-semibold discountText">-₹{{ number_format($rec->discount ?? optional($rec->payment)->discount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax (GST/VAT):</td>
                                <td class="text-end fw-semibold taxAmountText">₹{{ number_format($rec->tax ?? optional($rec->payment)->gst_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trainer Charges:</td>
                                <td class="text-end fw-semibold trainerChargesText">₹{{ number_format($rec->trainer_charges ?? optional($rec->payment)->trainer_fee ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold text-dark pt-2">TOTAL AMOUNT PAYABLE:</td>
                                <td class="text-end fw-bold text-dark pt-2 fs-5 invoiceTotalText">₹{{ number_format($rec->total_amount ?? optional($rec->invoice)->total_amount ?? optional($rec->payment)->total_amount ?? $rec->amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Amount Paid Now:</strong> <span class="amountPaidNow text-success fw-bold">₹{{ number_format($rec->amount, 2) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Method:</strong> <span class="paymentMethod">{{ $rec->payment_method }}</span></p>
                            <p class="mb-0"><strong>Received By:</strong> <span class="receivedBy">{{ $rec->received_by }}</span></p>
                        </div>
                    </div>

                    <div class="p-3 bg-secondary bg-opacity-10 rounded-3 border">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Invoice Total</small>
                                <span class="invoiceTotalText fw-bold text-dark">₹{{ number_format(optional($rec->payment)->total_amount ?? 0, 2) }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Already Paid</small>
                                <span class="alreadyPaid fw-bold text-success">₹{{ number_format($rec->amount, 2) }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Balance Due</small>
                                <span class="balanceDueText fw-bold text-danger">₹{{ number_format(optional($rec->payment)->remaining_balance ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted small fst-italic mb-0">Thank you for your payment!</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('receipts.download', $rec->id) }}" class="btn btn-navy rounded-pill px-4 shadow-sm printReceiptBtn">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
$(document).on('click', '.view-receipt-btn', function() {
    var receiptId = $(this).data('id');
    var modalTarget = $('#viewReceiptModal' + receiptId);

    $.ajax({
        url: '/dashboard/receipts/' + receiptId,
        type: 'GET',
        success: function(response) {
            modalTarget.find('.receiptNo').text(response.receipt_number);
            modalTarget.find('.receiptDate').text(response.receipt_date);
            
            var invoiceNo = (response.payment && response.payment.invoice_no) ? response.payment.invoice_no : 'N/A';
            modalTarget.find('.invoiceNo').text(invoiceNo);

            if(response.member) {
                modalTarget.find('.memberName').text(response.member.name);
                modalTarget.find('.memberId').text('GYM-2026-' + String(response.member.id).padStart(5, '0'));
                modalTarget.find('.memberMobile').text(response.member.phone);
            }

            // 🟢 FIXED: Fetching exact price breakdown values from payment relationship or fallback properties
            var payment = response.payment || {};
            
            var membershipFee = parseFloat(response.membership_fee || payment.base_amount || 0);
            var registrationFee = parseFloat(response.registration_fee || payment.registration_fee || 0);
            var discount = parseFloat(response.discount || payment.discount || 0);
            var taxAmount = parseFloat(response.tax || payment.gst_amount || 0);
            var trainerCharges = parseFloat(response.trainer_charges || payment.trainer_fee || 0);
            var totalAmount = parseFloat(response.total_amount || payment.total_amount || response.amount || 0);

            modalTarget.find('.membershipFeeText').text('₹' + membershipFee.toFixed(2));
            modalTarget.find('.registrationFeeText').text('₹' + registrationFee.toFixed(2));
            modalTarget.find('.discountText').text('-₹' + discount.toFixed(2));
            modalTarget.find('.taxAmountText').text('₹' + taxAmount.toFixed(2));
            modalTarget.find('.trainerChargesText').text('₹' + trainerCharges.toFixed(2));
            modalTarget.find('.invoiceTotalText').text('₹' + totalAmount.toFixed(2));

            modalTarget.find('.amountPaidNow').text('₹' + parseFloat(response.amount).toFixed(2));
            modalTarget.find('.paymentMethod').text(response.payment_method);
            modalTarget.find('.receivedBy').text(response.received_by || 'Admin');

            var alreadyPaid = parseFloat(response.amount);
            var balanceDue = parseFloat(payment.remaining_balance || 0);

            modalTarget.find('.alreadyPaid').text('₹' + alreadyPaid.toFixed(2));
            modalTarget.find('.balanceDueText').text('₹' + balanceDue.toFixed(2));

            modalTarget.find('.printReceiptBtn').attr('href', '/dashboard/receipts/download/' + response.id);
        }
    });
});

$(document).on('click', '.printReceiptBtn', function() {
    var modalElement = $(this).closest('.modal');
    var modalInstance = bootstrap.Modal.getInstance(modalElement[0]);
    if (modalInstance) {
        modalInstance.hide();
    }
    
    setTimeout(function() {
        location.reload();
    }, 1000);
});
</script>
@endpush
@endsection