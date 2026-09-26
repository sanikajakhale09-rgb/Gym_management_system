@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <!-- Unique Hero Banner Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4 text-white shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="position-relative z-index-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-1 rounded-pill fw-normal small">
                            <i class="fas fa-chart-pie me-1"></i> Financial Overview
                        </span>
                        <h2 class="fw-bold mb-1">Expenses Management</h2>
                        <p class="text-white-50 mb-0">Track, filter, and analyze all gym operational costs in one place.</p>
                    </div>
                    <div>
                        <a href="{{ route('expenses.create') }}" class="btn btn-light text-primary shadow-sm px-4 py-2 rounded-pill fw-bold hover-scale">
                            <i class="fas fa-plus-circle me-2 text-primary"></i> Add New Expense
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success / Error Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Total Expenses Summary Card -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white border-start border-primary border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-muted small mb-1 tracking-wider">Total Expenses Outflow</p>
                            <h2 class="fw-extrabold text-dark mb-0">₹{{ number_format($totalExpenses, 2) }}</h2>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar (Floating Modern Card) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3 align-items-center">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label small fw-bold text-muted">Search Keyword</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Search by details..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <label class="form-label small fw-bold text-muted">Filter By Category</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-layer-group"></i></span>
                        <select name="category" class="form-select bg-transparent border-0 shadow-none ps-2">
                            <option value="">All Categories</option>
                            <option value="Rent" {{ request('category') == 'Rent' ? 'selected' : '' }}>Rent</option>
                            <option value="Electricity" {{ request('category') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                            <option value="Salary" {{ request('category') == 'Salary' ? 'selected' : '' }}>Salary</option>
                            <option value="Equipment" {{ request('category') == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="Cleaning" {{ request('category') == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 d-flex align-items-end gap-2 pt-2 pt-md-0">
                    <div class="w-100">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="submit" class="btn btn-dark w-100 rounded-3 shadow-sm py-2 fw-semibold">
                            <i class="fas fa-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                    @if(request('search') || request('category'))
                        <div>
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger rounded-3 py-2 px-3" title="Clear Filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Modern Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list text-primary me-2"></i> Expense Records List
            </h5>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                Showing entries
            </span>
        </div>
        <div class="card-body px-0 pb-0 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fs-7 text-muted border-top border-bottom">
                        <tr>
                            <th class="py-3 ps-4">#</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Payment Method</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $expense)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $expenses->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-soft-primary p-2 text-primary me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(42, 82, 152, 0.1);">
                                        <i class="fas fa-tag fa-xs"></i>
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $expense->category }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">₹{{ number_format($expense->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                    <i class="fas fa-wallet text-secondary me-1"></i> {{ $expense->payment_method }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> {{ $expense->expense_date }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-sm btn-light text-primary border shadow-sm px-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#viewExpenseModal{{ $expense->id }}" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- Edit Button (Triggers Edit Modal) -->
                                    <button type="button" class="btn btn-sm btn-light text-warning border shadow-sm px-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $expense->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm px-2 rounded-pill fw-semibold" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="my-4">
                                    <div class="text-muted mb-2">
                                        <i class="fas fa-receipt fa-3x opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">No expenses found</h6>
                                    <p class="text-muted small mb-0">Try adjusting your search query or filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            <div class="px-4 py-3 bg-white border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================= -->
<!-- 🟢 View Modals for each Expense Record -->
<!-- ================================================= -->
@foreach($expenses as $expenseModal)
<div class="modal fade" id="viewExpenseModal{{ $expenseModal->id }}" tabindex="-1" aria-labelledby="viewExpenseModalLabel{{ $expenseModal->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h5 class="modal-title fw-bold" id="viewExpenseModalLabel{{ $expenseModal->id }}">
                    <i class="fas fa-receipt me-2"></i> Expense Details
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <span class="text-muted small d-block fw-semibold mb-1">Expense Category</span>
                            <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">
                                <i class="fas fa-tags me-1"></i> {{ $expenseModal->category }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block fw-semibold mb-1">Amount</span>
                            <h5 class="fw-bold text-success mb-0">₹{{ number_format($expenseModal->amount, 2) }}</h5>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block fw-semibold mb-1">Payment Method</span>
                            <span class="fw-semibold text-secondary">
                                <i class="fas fa-wallet me-1 text-muted"></i> {{ $expenseModal->payment_method }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block fw-semibold mb-1">Expense Date</span>
                            <span class="fw-semibold text-secondary">
                                <i class="far fa-calendar-alt me-1 text-muted"></i> {{ $expenseModal->expense_date }}
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block fw-semibold mb-1">Description / Notes</span>
                            <p class="bg-light p-3 rounded-3 text-secondary mb-0 border">
                                {{ $expenseModal->description ?? 'No description provided for this expense.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 py-3 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach


<!-- ================================================= -->
<!-- 🟡 Edit Modals for each Expense Record -->
<!-- ================================================= -->
@foreach($expenses as $expenseModal)
<div class="modal fade" id="editExpenseModal{{ $expenseModal->id }}" tabindex="-1" aria-labelledby="editExpenseModalLabel{{ $expenseModal->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-warning text-dark py-3 px-4">
                <h5 class="modal-title fw-bold" id="editExpenseModalLabel{{ $expenseModal->id }}">
                    <i class="fas fa-edit me-2"></i> Edit Expense Record
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('expenses.update', $expenseModal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <div class="row g-3">
                            
                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Category</label>
                                <select name="category" class="form-select rounded-3" required>
                                    <option value="Rent" {{ $expenseModal->category == 'Rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="Electricity" {{ $expenseModal->category == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                    <option value="Salary" {{ $expenseModal->category == 'Salary' ? 'selected' : '' }}>Salary</option>
                                    <option value="Equipment" {{ $expenseModal->category == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="Cleaning" {{ $expenseModal->category == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Amount (₹)</label>
                                <input type="number" step="0.01" name="amount" class="form-control rounded-3" value="{{ $expenseModal->amount }}" required>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Payment Method</label>
                                <select name="payment_method" class="form-select rounded-3" required>
                                    <option value="Cash" {{ $expenseModal->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Online" {{ $expenseModal->payment_method == 'Online' ? 'selected' : '' }}>Online</option>
                                    <option value="Bank Transfer" {{ $expenseModal->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>

                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Expense Date</label>
                                <input type="date" name="expense_date" class="form-control rounded-3" value="{{ $expenseModal->expense_date }}" required>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Description / Notes</label>
                                <textarea name="description" class="form-control rounded-3" rows="3">{{ $expenseModal->description }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold shadow-sm text-dark">Update Expense</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach

@endsection