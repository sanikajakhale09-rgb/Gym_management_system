@extends('auth.sidebar')

@section('title', 'Add New Membership Plan - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-plus-circle text-primary me-2"></i>Add New Membership Plan
            </h1>
            <p class="text-muted small mb-0">Create a new customized subscription or membership tier</p>
        </div>
        <a href="{{ route('membership.plans.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm bg-white">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <!-- Card Header -->
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="m-0 fw-bold text-dark"><i class="fas fa-clipboard-list me-2 text-primary"></i>Plan Details Form</h5>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('membership.plans.store') }}" method="POST">
                        @csrf

                        <!-- Branch Selection -->
                        <div class="mb-4">
                            <label for="branch_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Branch</label>
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-code-branch"></i></span>
                                <select class="form-select border-0 bg-white shadow-none ps-2" id="branch_id" name="branch_id">
                                    <option value="">All Branches / Global</option>
                                    @if(!empty($branches))
                                        @foreach($branches as $branch)
                                            <option value="{{ is_object($branch) ? $branch->id : $branch['id'] }}">
                                                {{ is_object($branch) ? $branch->name : $branch['name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Plan Name Input Box -->
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label fw-semibold text-secondary small text-uppercase">Plan Name *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-tag"></i></span>
                                    <input type="text" class="form-control border-0 bg-white shadow-none ps-2" id="name" name="name" required placeholder="e.g. Monthly Basic, VIP Plan">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="duration_days" class="form-label fw-semibold text-secondary small text-uppercase">Duration (Days) *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-info"><i class="far fa-clock"></i></span>
                                    <input type="number" class="form-control border-0 bg-white shadow-none ps-2" id="duration_days" name="duration_days" required placeholder="e.g. 30">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="price" class="form-label fw-semibold text-secondary small text-uppercase">Price (₹) *</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-success"><i class="fas fa-rupee-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="price" name="price" required placeholder="e.g. 1500">
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="registration_fee" class="form-label fw-semibold text-secondary small text-uppercase">Registration Fee (₹)</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="registration_fee" name="registration_fee" placeholder="e.g. 500">
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="discount" class="form-label fw-semibold text-secondary small text-uppercase">Discount (₹ or %)</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-danger"><i class="fas fa-percent"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="discount" name="discount" placeholder="e.g. 100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="tax" class="form-label fw-semibold text-secondary small text-uppercase">Tax / GST (%)</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-secondary"><i class="fas fa-file-invoice"></i></span>
                                    <input type="number" step="0.01" class="form-control border-0 bg-white shadow-none ps-2" id="tax" name="tax" placeholder="e.g. 18">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="status" class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-success"><i class="fas fa-toggle-on"></i></span>
                                    <select class="form-select border-0 bg-white shadow-none ps-2" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Live Total Payable Box -->
                        <div class="alert py-3 px-4 mb-4 rounded-4 border bg-light d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small fw-bold text-uppercase d-block text-muted mb-1">Calculation Summary</span>
                                <h5 class="mb-0 fw-bold text-dark">Final Payable Amount: ₹ <span id="final_total" class="text-success">0.00</span></h5>
                            </div>
                            <i class="fas fa-calculator fs-2 text-muted opacity-50"></i>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-secondary small text-uppercase">Description</label>
                            <textarea class="form-control shadow-sm rounded-4 border p-3 bg-white" id="description" name="description" rows="3" placeholder="Plan details..."></textarea>
                        </div>

                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm border-0">
                                <i class="fas fa-save me-1"></i> Save Plan
                            </button>
                            <a href="{{ route('membership.plans.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary border shadow-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script for Live Calculation -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const priceInput = document.getElementById('price');
        const regFeeInput = document.getElementById('registration_fee');
        const discountInput = document.getElementById('discount');
        const taxInput = document.getElementById('tax');
        const finalTotalSpan = document.getElementById('final_total');

        // Live calculation summary
        function calculateTotal() {
            let price = parseFloat(priceInput.value) || 0;
            let regFee = parseFloat(regFeeInput.value) || 0;
            let discount = parseFloat(discountInput.value) || 0;
            let taxPercent = parseFloat(taxInput.value) || 0;

            let subtotal = price + regFee;
            let taxableAmount = subtotal - discount;
            if (taxableAmount < 0) taxableAmount = 0;

            let taxAmount = (taxableAmount * taxPercent) / 100;
            let finalTotal = taxableAmount + taxAmount;

            finalTotalSpan.innerText = finalTotal.toFixed(2);
        }

        [priceInput, regFeeInput, discountInput, taxInput].forEach(element => {
            element.addEventListener('input', calculateTotal);
        });
    });
</script>
@endsection