@extends('auth.sidebar')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Invoices Management</h2>
                    <p class="text-muted mb-0">Manage, search, and view all generated gym invoices seamlessly.</p>
                </div>
                <div>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill fs-6">
                        <i class="fas fa-calendar-alt text-primary me-1"></i> {{ date('d M, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="row justify-content-center">
            <div class="col-xl-11">
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4 py-3" role="alert">
                    <i class="fas fa-check-circle me-2 fs-5"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="row justify-content-center">
            <div class="col-xl-11">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4 py-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Summary Cards (Centered & Styled) -->
    <div class="row justify-content-center g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow border-0 rounded-4 text-white h-100" style="background: linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold small mb-1">Total Invoices</h6>
                            <h3 class="fw-bold mb-0">₹{{ number_format($totalInvoicesAmount ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-shape bg-white text-primary rounded-circle p-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow border-0 rounded-4 text-white h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold small mb-1">Paid Invoices</h6>
                            <h3 class="fw-bold mb-0">₹{{ number_format($paidInvoicesAmount ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-shape bg-white text-success rounded-circle p-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow border-0 rounded-4 text-white h-100" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-opacity-75 text-uppercase fw-bold small mb-1">Partial Invoices</h6>
                            <h3 class="fw-bold mb-0 text-white">₹{{ number_format($partialInvoicesAmount ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-shape bg-white text-warning rounded-circle p-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-adjust fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card shadow border-0 rounded-4 text-white h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold small mb-1">Pending Invoices</h6>
                            <h3 class="fw-bold mb-0">₹{{ number_format($pendingInvoicesAmount ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-shape bg-white text-danger rounded-circle p-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-11">
            <div class="card shadow-sm border-0 rounded-4 bg-white p-2">
                <div class="card-body p-2">
                    <form action="{{ route('invoices.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 ps-3 text-muted fs-5">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-0 shadow-none py-2 fs-6" placeholder="Search by Invoice No, Member Name or Phone..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fas fa-filter me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Table Container -->
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="card shadow border-0 rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-white py-3 border-0 px-4">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-list text-primary me-2"></i> All Invoices List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-dark text-uppercase fs-7 tracking-wider">
                                <tr>
                                    <th class="ps-4">Invoice No.</th>
                                    <th>Member Details</th>
                                    <th>Invoice Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6 rounded-pill">
                                            {{ $inv->invoice_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $inv->member->name ?? 'N/A' }}</div>
                                        <small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i> {{ $inv->member->phone ?? 'No Phone' }}</small>
                                    </td>
                                    <td>
                                        <span class="text-secondary"><i class="far fa-calendar-alt me-1"></i> {{ $inv->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">₹{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="text-success fw-bold">₹{{ number_format($inv->amount, 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($inv->remaining_balance, 2) }}</td>
                                    <td>
                                        @if($inv->status == 'Paid')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-semibold">Paid</span>
                                        @elseif($inv->status == 'Partial')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill fw-semibold text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill fw-semibold">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- View Button -->
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewInvoiceModal{{ $inv->id }}" 
                                                    title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 shadow-sm text-dark" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editInvoiceModal{{ $inv->id }}" 
                                                    title="Edit Invoice">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Delete Form -->
                                            <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" title="Delete Invoice">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div class="modal fade" id="viewInvoiceModal{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header bg-primary bg-opacity-10 border-0 px-4 pt-4">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="fas fa-file-invoice text-primary me-2"></i> Invoice Details: {{ $inv->invoice_no }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="p-3 bg-light rounded-3 border h-100">
                                                            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-user me-1"></i> Member Information</h6>
                                                            <p class="mb-1"><strong>Name:</strong> {{ $inv->member->name ?? 'N/A' }}</p>
                                                            <p class="mb-1"><strong>Phone:</strong> {{ $inv->member->phone ?? 'N/A' }}</p>
                                                            <p class="mb-0"><strong>Email:</strong> {{ $inv->member->email ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-3 bg-light rounded-3 border h-100">
                                                            <h6 class="fw-bold text-success mb-2"><i class="fas fa-rupee-sign me-1"></i> Payment Summary</h6>
                                                            <p class="mb-1"><strong>Total Amount:</strong> ₹{{ number_format($inv->total_amount, 2) }}</p>
                                                            <p class="mb-1"><strong>Paid Amount:</strong> ₹{{ number_format($inv->amount, 2) }}</p>
                                                            <p class="mb-1"><strong>Remaining Due:</strong> <span class="text-danger fw-bold">₹{{ number_format($inv->remaining_balance, 2) }}</span></p>
                                                            <p class="mb-0"><strong>Status:</strong> 
                                                                <span class="badge {{ $inv->status == 'Paid' ? 'bg-success' : ($inv->status == 'Partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                                    {{ $inv->status }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0 py-3 px-4">
                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="window.print()">
                                                    <i class="fas fa-print me-1"></i> Print Invoice
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editInvoiceModal{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <form action="{{ route('invoices.update', $inv->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-warning bg-opacity-10 border-0 px-4 pt-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="fas fa-edit text-warning me-2"></i> Edit Invoice: {{ $inv->invoice_no }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted">Total Amount (₹)</label>
                                                        <input type="number" step="0.01" name="total_amount" class="form-control rounded-3 total-input p-2" data-id="{{ $inv->id }}" value="{{ $inv->total_amount }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted">Paid Amount (₹)</label>
                                                        <input type="number" step="0.01" name="amount" class="form-control rounded-3 paid-input p-2" data-id="{{ $inv->id }}" value="{{ $inv->amount }}" required>
                                                    </div>
                                                    
                                                    <!-- Remaining Balance Info Box -->
                                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                                        <small class="text-muted fw-semibold d-block">Remaining Balance (Due):</small>
                                                        <span id="remaining_display_{{ $inv->id }}" class="fw-bold fs-5 text-danger">₹{{ number_format($inv->remaining_balance, 2) }}</span>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted">Payment Status</label>
                                                        <select name="status" id="status_{{ $inv->id }}" class="form-select rounded-3 p-2" required>
                                                            <option value="Paid" {{ $inv->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                                            <option value="Partial" {{ $inv->status == 'Partial' ? 'selected' : '' }}>Partial</option>
                                                            <option value="Pending" {{ $inv->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0 py-3 px-4">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark fw-bold shadow-sm">Update Invoice</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="my-4">
                                            <i class="fas fa-folder-open fa-3x text-secondary mb-3 opacity-50"></i>
                                            <p class="fs-5 fw-semibold mb-0">No Invoices Found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-0 d-flex justify-content-end px-4">
                    @if(method_exists($invoices, 'links'))
                        <div class="w-100">
                            {{ $invoices->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Pagination Styling -->
<style>
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

<!-- Direct Script (Not pushed to stack to avoid layout caching issues) -->
<script>
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('total-input') || e.target.classList.contains('paid-input')) {
        const id = e.target.getAttribute('data-id');
        const modal = e.target.closest('.modal');
        
        const totalInput = modal.querySelector('.total-input');
        const paidInput = modal.querySelector('.paid-input');
        const remainingDisplay = modal.querySelector('#remaining_display_' + id);
        const statusSelect = modal.querySelector('#status_' + id);

        const total = parseFloat(totalInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const remaining = total - paid;

        // Update remaining balance view properly
        remainingDisplay.textContent = '₹' + remaining.toFixed(2);

        // Auto change status dropdown based on calculation
        if (paid <= 0) {
            statusSelect.value = 'Pending';
        } else if (remaining <= 0) {
            statusSelect.value = 'Paid';
        } else {
            statusSelect.value = 'Partial';
        }
    }
});
</script>
@endsection