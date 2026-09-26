@extends('auth.sidebar')

@section('title', 'Membership Purchase - Gym Admin')

@section('content')
@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\MembershipPlan[] $plans */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Member[] $members */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Trainer[] $trainers */
@endphp

<div class="container-fluid px-4 py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%); min-height: 100vh;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-shopping-cart text-primary me-2"></i>Membership Purchase (Assign Plan)
            </h1>
            <p class="text-muted small mb-0">Assign a membership plan, select trainer based on member's branch, calculate expiry, and handle payments</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center align-items-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <h5 class="m-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i> Assign Plan & Process Payment</h5>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="{{ route('members.store.subscription') }}" method="POST" id="membershipPurchaseForm">
                        @csrf

                        <!-- Select Member -->
                        <div class="mb-4">
                            <label for="member_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Member <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-user"></i></span>
                                <select name="member_id" id="member_id" class="form-select border-0 bg-white shadow-none ps-2" required>
                                    <option value="">-- Choose Member --</option>
                                    @foreach($members as $member)
                                        @php
                                            $memberName = $member->name ?? ($member->first_name ?? '');
                                            $branchId = $member->branch_id ?? ($member->gym_branch_id ?? '');
                                        @endphp
                                        <option value="{{ $member->id }}" data-branch-id="{{ $branchId }}">
                                            {{ $memberName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Select Trainer (Branch-wise Filtered) -->
                        <div class="mb-4">
                            <label for="trainer_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Trainer (Optional)</label>
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-warning"><i class="fas fa-user-tie"></i></span>
                                <select name="trainer_id" id="trainer_id" class="form-select border-0 bg-white shadow-none ps-2">
                                    <option value="" data-trainer-charges="0" data-trainer-name="">-- Without Trainer --</option>
                                    @foreach($trainers ?? [] as $trainer)
                                        @php
                                            $tBranchId = $trainer->branch_id ?? ($trainer->gym_branch_id ?? '');
                                        @endphp
                                        <option value="{{ $trainer->id }}" 
                                                data-branch-id="{{ $tBranchId }}"
                                                data-trainer-charges="{{ $trainer->charges ?? 1500 }}"
                                                data-trainer-name="{{ $trainer->name }}">
                                            {{ $trainer->name }} (₹{{ number_format($trainer->charges ?? 1500, 2) }}/month)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small id="trainer_help_text" class="text-muted mt-1 ms-3 d-block" style="font-size: 11px;">Please select a member first to view branch-specific trainers.</small>
                        </div>

                        <!-- Select Membership Plan -->
                        <div class="mb-4">
                            <label for="membership_plan_id" class="form-label fw-semibold text-secondary small text-uppercase">Select Membership Plan <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-success"><i class="fas fa-tags"></i></span>
                                <select name="membership_plan_id" id="membership_plan_id" class="form-select border-0 bg-white shadow-none ps-2" required>
                                    <option value="">-- Choose Plan --</option>
                                    @foreach($plans as $plan)
                                        @php
                                            $basePrice = $plan->price ?? 0;
                                            $regFee = $plan->registration_fee ?? 0;
                                            $discount = $plan->discount ?? 0;
                                            $taxRate = $plan->tax ?? 0;
                                            
                                            $subtotal = $basePrice + $regFee;
                                            $taxable = max(0, $subtotal - $discount);
                                            $taxAmount = ($taxable * $taxRate) / 100;
                                            $planTotal = $taxable + $taxAmount;
                                        @endphp
                                        <option value="{{ $plan->id }}" 
                                                data-base-price="{{ $basePrice }}"
                                                data-reg-fee="{{ $regFee }}"
                                                data-discount="{{ $discount }}"
                                                data-tax-amount="{{ number_format($taxAmount, 2, '.', '') }}"
                                                data-plan-total="{{ number_format($planTotal, 2, '.', '') }}"
                                                data-duration="{{ $plan->duration_days }}">
                                            {{ $plan->name }} (Duration: {{ $plan->duration_days }} Days - ₹{{ number_format($planTotal, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Start Date & End Date Row -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label fw-semibold text-secondary small text-uppercase">Start Date <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-3 text-info"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="start_date" id="start_date" class="form-control border-0 bg-white shadow-none ps-2" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label fw-semibold text-secondary small text-uppercase">End Date (Auto-Calculated)</label>
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-light">
                                    <span class="input-group-text bg-transparent border-0 ps-3 text-warning"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="end_date" id="end_date" class="form-control border-0 bg-light shadow-none ps-2" readonly required>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown Section -->
                        <div class="card border rounded-4 p-4 mb-4 bg-light shadow-sm" id="price_breakdown_card">
                            <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2">
                                <i class="fas fa-receipt text-primary me-2"></i> Price Breakdown
                            </h6>
                            <div class="d-flex justify-content-between mb-2 small text-secondary">
                                <span>Membership Fee:</span>
                                <span class="fw-semibold text-dark">₹<span id="display_base_price">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-secondary">
                                <span>Registration Fee:</span>
                                <span class="fw-semibold text-dark">₹<span id="display_reg_fee">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-secondary">
                                <span>Discount:</span>
                                <span class="fw-semibold text-danger">-₹<span id="display_discount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-secondary">
                                <span>Tax (GST/VAT):</span>
                                <span class="fw-semibold text-dark">₹<span id="display_tax">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-secondary">
                                <span>Trainer Charges <span id="trainer_name_label" class="text-muted fst-italic"></span>:</span>
                                <span class="fw-semibold text-dark">₹<span id="display_trainer_charges">0.00</span></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark text-uppercase">TOTAL AMOUNT PAYABLE:</span>
                                <h4 class="fw-bold text-success mb-0">₹<span id="display_total_amount">0.00</span></h4>
                            </div>
                            <input type="hidden" name="total_amount" id="input_total_amount" value="0">
                            <input type="hidden" name="balance_amount" id="input_balance_amount" value="0">
                        </div>

                        <!-- Payment Information Section -->
                        <div class="card border rounded-4 p-4 mb-4 shadow-sm bg-white">
                            <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2">
                                <i class="fas fa-wallet text-success me-2"></i> Payment Details
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="amount_paid" class="form-label fw-semibold text-secondary small text-uppercase mb-0">Amount Paid <span class="text-danger">*</span></label>
                                        <span class="small text-danger fw-bold">Balance Due: ₹<span id="display_balance_amount">0.00</span></span>
                                    </div>
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0 ps-3 text-success">₹</span>
                                        <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control border-0 bg-white shadow-none ps-2" placeholder="0.00" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label fw-semibold text-secondary small text-uppercase mb-1">Payment Method <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-credit-card"></i></span>
                                        <select name="payment_method" id="payment_method" class="form-select border-0 bg-white shadow-none ps-2" required>
                                            <option value="">-- Choose Method --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Card">Card</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Online">Online</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="payment_status" class="form-label fw-semibold text-secondary small text-uppercase mb-1">Payment Status <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                        <span class="input-group-text bg-white border-0 ps-3 text-info"><i class="fas fa-check-circle"></i></span>
                                        <select name="payment_status" id="payment_status" class="form-select border-0 bg-white shadow-none ps-2" required>
                                            <option value="Paid" selected>Paid</option>
                                            <option value="Partial">Partial</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Refunded">Refunded</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" id="due_date_container" style="display: none;">
                                    <label for="due_date" class="form-label fw-semibold text-danger small text-uppercase mb-1">Balance Due Date <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden border border-danger">
                                        <span class="input-group-text bg-white border-0 ps-3 text-danger"><i class="fas fa-calendar-times"></i></span>
                                        <input type="date" name="due_date" id="due_date" class="form-control border-0 bg-white shadow-none ps-2">
                                    </div>
                                    <small id="due_date_error_msg" class="text-danger mt-1 d-block fw-semibold" style="font-size: 11px;"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow border-0" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i> COMPLETE PURCHASE 
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Branch-wise Trainer Filtering & Calculations -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const memberSelect = document.getElementById('member_id');
        const trainerSelect = document.getElementById('trainer_id');
        const planSelect = document.getElementById('membership_plan_id');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        const displayBasePrice = document.getElementById('display_base_price');
        const displayRegFee = document.getElementById('display_reg_fee');
        const displayDiscount = document.getElementById('display_discount');
        const displayTax = document.getElementById('display_tax');
        const displayTrainerCharges = document.getElementById('display_trainer_charges');
        const trainerNameLabel = document.getElementById('trainer_name_label');
        const displayTotalAmount = document.getElementById('display_total_amount');
        const displayBalanceAmount = document.getElementById('display_balance_amount');
        const inputTotalAmount = document.getElementById('input_total_amount');
        const inputBalanceAmount = document.getElementById('input_balance_amount');
        
        const amountPaidInput = document.getElementById('amount_paid');
        const paymentStatusSelect = document.getElementById('payment_status');
        const dueDateContainer = document.getElementById('due_date_container');
        const dueDateInput = document.getElementById('due_date');
        const dueDateErrorMsg = document.getElementById('due_date_error_msg');
        const trainerHelpText = document.getElementById('trainer_help_text');

        const allTrainerOptions = Array.from(trainerSelect.options);

        memberSelect.addEventListener('change', function () {
            const selectedMemberOption = memberSelect.options[memberSelect.selectedIndex];
            const memberBranchId = selectedMemberOption.getAttribute('data-branch-id');

            trainerSelect.innerHTML = '';
            
            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.setAttribute('data-trainer-charges', '0');
            defaultOption.setAttribute('data-trainer-name', '');
            defaultOption.innerText = "-- Without Trainer --";
            trainerSelect.appendChild(defaultOption);

            if (!memberBranchId) {
                trainerHelpText.innerText = "⚠️ Please select a member with a valid branch.";
                trainerSelect.disabled = true;
            } else {
                trainerSelect.disabled = false;
                trainerHelpText.innerText = "Only trainers belonging to the selected branch are available.";

                allTrainerOptions.forEach(option => {
                    if (option.value !== "") {
                        const trainerBranchId = option.getAttribute('data-branch-id');
                        if (trainerBranchId === memberBranchId) {
                            trainerSelect.appendChild(option.cloneNode(true));
                        }
                    }
                });
            }

            trainerSelect.value = "";
            calculateTotals();
        });

        function calculateTotals() {
            const selectedPlanOption = planSelect.options[planSelect.selectedIndex];
            const selectedTrainerOption = trainerSelect.options[trainerSelect.selectedIndex];

            let trainerCharges = 0;
            let trainerName = '';
            
            if (selectedTrainerOption && selectedTrainerOption.value) {
                trainerCharges = parseFloat(selectedTrainerOption.getAttribute('data-trainer-charges')) || 0;
                trainerName = selectedTrainerOption.getAttribute('data-trainer-name') || '';
            }

            if (trainerName && trainerName.trim() !== '') {
                trainerNameLabel.innerText = `(${trainerName})`;
            } else {
                trainerNameLabel.innerText = '';
            }
            displayTrainerCharges.innerText = trainerCharges.toFixed(2);

            if (!selectedPlanOption.value) {
                displayBasePrice.innerText = '0.00';
                displayRegFee.innerText = '0.00';
                displayDiscount.innerText = '0.00';
                displayTax.innerText = '0.00';
                
                let grandTotal = trainerCharges;
                displayTotalAmount.innerText = grandTotal.toFixed(2);
                inputTotalAmount.value = grandTotal.toFixed(2);
                
                endDateInput.value = '';
                updateDueDateBounds();
                updateBalanceAndStatus(grandTotal);
                return;
            }

            const basePrice = parseFloat(selectedPlanOption.getAttribute('data-base-price')) || 0;
            const regFee = parseFloat(selectedPlanOption.getAttribute('data-reg-fee')) || 0;
            const discount = parseFloat(selectedPlanOption.getAttribute('data-discount')) || 0;
            const taxAmount = parseFloat(selectedPlanOption.getAttribute('data-tax-amount')) || 0;
            const durationDays = parseInt(selectedPlanOption.getAttribute('data-duration')) || 0;

            const subtotal = basePrice + regFee;
            const taxable = Math.max(0, subtotal - discount);
            const grandTotal = taxable + taxAmount + trainerCharges;

            displayBasePrice.innerText = basePrice.toFixed(2);
            displayRegFee.innerText = regFee.toFixed(2);
            displayDiscount.innerText = discount.toFixed(2);
            displayTax.innerText = taxAmount.toFixed(2);
            displayTotalAmount.innerText = grandTotal.toFixed(2);
            inputTotalAmount.value = grandTotal.toFixed(2);
            
            if (!amountPaidInput.value || amountPaidInput.dataset.initialized !== "true") {
                amountPaidInput.value = grandTotal.toFixed(2);
                amountPaidInput.dataset.initialized = "true";
            }

            if (startDateInput.value && durationDays > 0) {
                let startDate = new Date(startDateInput.value);
                startDate.setDate(startDate.getDate() + durationDays);
                let year = startDate.getFullYear();
                let month = String(startDate.getMonth() + 1).padStart(2, '0');
                let day = String(startDate.getDate()).padStart(2, '0');
                endDateInput.value = `${year}-${month}-${day}`;
            }

            updateDueDateBounds();
            updateBalanceAndStatus(grandTotal);
        }

        function updateDueDateBounds() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (startDate) {
                dueDateInput.setAttribute('min', startDate);
            }
            if (endDate) {
                dueDateInput.setAttribute('max', endDate);
            }

           

            if (dueDateInput.value) {
                if ((startDate && dueDateInput.value < startDate) || (endDate && dueDateInput.value > endDate)) {
                    dueDateInput.value = '';
                }
            }
        }

        function updateBalanceAndStatus(grandTotal) {
            const amountPaid = parseFloat(amountPaidInput.value) || 0;
            let balance = grandTotal - amountPaid;
            if (balance < 0) balance = 0;

            displayBalanceAmount.innerText = balance.toFixed(2);
            inputBalanceAmount.value = balance.toFixed(2);

            if (balance > 0) {
                if (amountPaid > 0) {
                    paymentStatusSelect.value = "Partial";
                } else {
                    paymentStatusSelect.value = "Pending";
                }
                dueDateContainer.style.display = "block";
                dueDateInput.setAttribute("required", "required");
                
                if (!dueDateInput.value && endDateInput.value) {
                    dueDateInput.value = endDateInput.value;
                }
            } else {
                paymentStatusSelect.value = "Paid";
                dueDateContainer.style.display = "none";
                dueDateInput.removeAttribute("required");
                dueDateInput.value = "";
                dueDateErrorMsg.innerText = "";
            }
        }

        planSelect.addEventListener('change', function() {
            amountPaidInput.dataset.initialized = "false";
            calculateTotals();
        });
        
        trainerSelect.addEventListener('change', function() {
            amountPaidInput.dataset.initialized = "false";
            calculateTotals();
        });

        startDateInput.addEventListener('change', calculateTotals);
        
        amountPaidInput.addEventListener('input', function() {
            const grandTotal = parseFloat(inputTotalAmount.value) || 0;
            updateBalanceAndStatus(grandTotal);
        });

        dueDateInput.addEventListener('input', function() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            if (dueDateInput.value && (dueDateInput.value < startDate || dueDateInput.value > endDate)) {
                dueDateErrorMsg.innerText = "⚠️ Please select a date within the membership validity period!";
                dueDateInput.value = '';
            } else {
                updateDueDateBounds();
            }
        });

        trainerSelect.disabled = true;
    });
</script>

<!-- परचेस झाल्यावर ऑटोमॅटिक रिसीट PDF नवीन टॅबमध्ये उघडेल आणि युजर active members वर जाईल -->
@if(session('download_receipt_id'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        // १. नवीन टॅबमध्ये PDF रिसीट ओपन होईल
        window.open("{{ route('members.receipt', session('download_receipt_id')) }}", '_blank');
        
        // २. लगेचच मुख्य पेज/ॲक्टिव्ह मेंबरशिप लिस्टवर (index) रीडायरेक्ट होईल
        window.location.href = "{{ route('members.index') }}";
    });
</script>
@endif

@endsection