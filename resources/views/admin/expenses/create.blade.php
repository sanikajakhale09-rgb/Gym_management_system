@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-plus-circle text-primary me-2"></i> Add New Expense
            </h2>
            <p class="text-muted small mb-0">Record and manage gym operational expenses securely.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Expenses
        </a>
    </div>

    <!-- Error Messages Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Please fix the following errors:</h5>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Add Expense Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold text-secondary mb-0"><i class="fas fa-receipt text-primary me-2"></i> Expense Information</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Expense Category -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Expense Category <span class="text-danger">*</span></label>
                                <div class="input-group bg-light rounded-3 border overflow-hidden">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-tags"></i></span>
                                    <select name="category_id" class="form-select bg-transparent border-0 shadow-none ps-2" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Amount (₹) <span class="text-danger">*</span></label>
                                <div class="input-group bg-light rounded-3 border overflow-hidden">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="number" step="0.01" name="amount" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Enter amount" value="{{ old('amount') }}" required min="0.01">
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Payment Method <span class="text-danger">*</span></label>
                                <div class="input-group bg-light rounded-3 border overflow-hidden">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-wallet"></i></span>
                                    <select name="payment_method" class="form-select bg-transparent border-0 shadow-none ps-2" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="UPI" {{ old('payment_method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="Card" {{ old('payment_method') == 'Card' ? 'selected' : '' }}>Card</option>
                                        <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Expense Date <span class="text-danger">*</span></label>
                                <div class="input-group bg-light rounded-3 border overflow-hidden">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="expense_date" class="form-control bg-transparent border-0 shadow-none ps-2" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">Description <span class="text-muted">(Optional)</span></label>
                                <textarea name="description" class="form-control bg-light rounded-3 border shadow-none p-3" rows="3" placeholder="Enter additional details about this expense (e.g. September month gym rent payment)">{{ old('description') }}</textarea>
                            </div>

                            <!-- Attachment -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">Attachment <span class="text-muted">(Bill / Receipt / Invoice)</span></label>
                                <input type="file" name="attachment" class="form-control bg-light rounded-3 border shadow-none py-2">
                                <div class="form-text text-muted small mt-1"><i class="fas fa-info-circle me-1"></i> Supported formats: PDF, JPG, PNG (Max size: 2MB)</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 text-end mt-4 pt-2 border-top">
                                <a href="{{ route('expenses.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-semibold me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                                    <i class="fas fa-save me-1"></i> Save Expense
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection