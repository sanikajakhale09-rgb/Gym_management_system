@extends('auth.sidebar')

@section('content')
<style>
    /* Pagination Styling Fix */
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        justify-content: center;
        margin-top: 20px;
    }
    .pagination li {
        margin: 0 3px;
    }
    .pagination li a, .pagination li span {
        position: relative;
        display: block;
        padding: 8px 14px;
        font-size: 14px;
        color: #17324d;
        background-color: #ffffff;
        border: 1px solid #d7dee7;
        border-radius: 8px;
        text-decoration: none;
    }
    .pagination li.active span {
        background-color: #17324d;
        color: #ffffff;
        border-color: #17324d;
    }
    .pagination li.disabled span {
        color: #64748b;
        background-color: #f3f5f7;
        border-color: #d7dee7;
    }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-clock-rotate-left text-primary me-2"></i> Pending & Partial Payments
            </h2>
            <p class="text-muted mb-0">Track and collect due balances from members easily.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark shadow-sm border px-3 py-2 fs-6">
                <i class="fas fa-calendar-alt text-secondary me-1"></i> {{ date('d M, Y') }}
            </span>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card Container -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-file-invoice-dollar me-2 text-info"></i> Dues List
            </h5>
            <span class="badge bg-light text-dark fw-bold px-3 py-2 shadow-sm">
                Total Pending: {{ method_exists($pendingPayments, 'total') ? $pendingPayments->total() : count($pendingPayments) }}
            </span>
        </div>

        <div class="card-body p-4" style="background-color: #fcfcfc;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="py-3">Invoice No</th>
                            <th class="py-3">Member Name</th>
                            <th class="py-3">Total Amount</th>
                            <th class="py-3">Paid Amount</th>
                            <th class="py-3">Balance Due</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $payment)
                            <tr>
                                <td class="fw-bold text-dark">{{ $payment->invoice_no }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #475569 100%, #1e293b 0%);">
                                            {{ substr(optional($payment->member)->name ?? 'N', 0, 1) }}
                                        </div>
                                        <span class="fw-semibold text-dark">{{ optional($payment->member)->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>₹{{ number_format($payment->total_amount, 2) }}</td>
                                <td class="text-success fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1 fs-6">
                                        ₹{{ number_format($payment->remaining_balance, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->status == 'Pending')
                                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill">
                                            <i class="fas fa-exclamation-circle me-1"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-info text-dark px-2 py-1 rounded-pill">
                                            <i class="fas fa-hourglass-half me-1"></i> Partial
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ optional($payment->created_at)->format('d M, Y') }}</td>
                                <td class="text-center">
                                    <!-- Collect Due Trigger Button -->
                                    <button type="button" class="btn btn-sm text-white px-3 rounded-pill fw-semibold shadow-sm" style="background-color: #1e293b;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#collectModal{{ $payment->id }}">
                                        <i class="fas fa-rupee-sign me-1 text-info"></i> Collect Due
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal for Collecting Due Payment -->
                            <div class="modal fade" id="collectModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form action="{{ route('payments.collect', $payment->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-cash-register me-2 text-info"></i> Collect Payment - {{ $payment->invoice_no }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 bg-light">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold">Member Name</label>
                                                    <input type="text" class="form-control bg-white" value="{{ optional($payment->member)->name ?? 'N/A' }}" readonly>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label text-muted small fw-bold">Total Amount</label>
                                                        <input type="text" class="form-control bg-white" value="₹{{ number_format($payment->total_amount, 2) }}" readonly>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label text-muted small fw-bold text-danger">Balance Due</label>
                                                        <input type="text" class="form-control bg-white text-danger fw-bold" value="₹{{ number_format($payment->remaining_balance, 2) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-dark">Enter Amount to Collect (₹) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" max="{{ $payment->remaining_balance }}" name="paying_amount" class="form-control bg-white" placeholder="Enter amount" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-dark">Payment Method</label>
                                                    <select name="payment_method" class="form-select bg-white">
                                                        <option value="Cash">Cash</option>
                                                        <option value="UPI">UPI / QR</option>
                                                        <option value="Card">Credit / Debit Card</option>
                                                        <option value="Online">Bank Transfer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-white rounded-bottom-4">
                                                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn text-white px-4 rounded-pill shadow-sm" style="background-color: #1e293b;">
                                                    <i class="fas fa-check-circle me-1 text-info"></i> Confirm Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal -->
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                                        <p class="fs-5 fw-semibold mb-0">No pending payments found!</p>
                                        <p class="small text-muted">All member accounts are fully cleared.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($pendingPayments, 'links'))
                <div class="mt-4">
                    {{ $pendingPayments->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection