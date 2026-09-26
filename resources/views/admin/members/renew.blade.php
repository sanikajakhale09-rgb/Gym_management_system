@extends('auth.sidebar')

@section('title', 'Renew Membership - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-rotate-right me-2" style="color: #4f46e5;"></i> Renew Membership: {{ $member->name }}
        </h2>
        <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Members
        </a>
    </div>

    <!-- Flash Success & Error Messages Added Here -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white px-4 py-3" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2"></i> Member Code: {{ $member->member_code ?? 'GYM-' . $member->id }}</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <!-- 🟢 सुरक्षितपणे $subscription किंवा $member->id वापरले आहे -->
                    <form action="{{ route('membership.storeRenew', isset($subscription) ? $subscription->id : $member->id) }}" method="POST">
                        @csrf

                        <!-- Select Plan -->
                        <div class="mb-3">
                            <label for="membership_plan_id" class="form-label fw-semibold text-dark">Select New Membership Plan</label>
                            <select name="membership_plan_id" id="membership_plan_id" class="form-select rounded-3 shadow-sm" required>
                                <option value="" selected disabled>Choose a plan...</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" data-price="{{ $plan->price ?? 0 }}" 
                                        {{ (isset($subscription) && $subscription->membership_plan_id == $plan->id) || (!isset($subscription) && $member->membership_plan_id == $plan->id) ? 'selected' : '' }}>
                                        {{ $plan->name }} (₹{{ $plan->price }}) - {{ $plan->duration_months ?? $plan->duration_days ?? 0 }} Months/Days
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Trainer -->
                        <div class="mb-3">
                            <label for="trainer_id" class="form-label fw-semibold text-dark">Assign Trainer (Optional)</label>
                            <select name="trainer_id" id="trainer_id" class="form-select rounded-3 shadow-sm">
                                <option value="">No Trainer</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ $member->trainer_id == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- PT Charges -->
                        <div class="mb-3">
                            <label for="pt_charges" class="form-label fw-semibold text-dark">Personal Training (PT) Charges</label>
                            <input type="number" step="0.01" name="pt_charges" id="pt_charges" class="form-control rounded-3 shadow-sm" value="{{ $member->pt_charges ?? 0 }}">
                        </div>

                        <!-- Paid Amount -->
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label fw-semibold text-dark">Paid Amount (₹)</label>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control rounded-3 shadow-sm" value="{{ isset($subscription) ? ($subscription->price_paid ?? '') : '' }}" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold text-dark">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select rounded-3 shadow-sm" required>
                                <option value="cash">Cash</option>
                                <option value="upi">UPI</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('members.index') }}" class="btn btn-light border px-4 rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%); border: none;">
                                <i class="fa-solid fa-check me-1"></i> Complete Renewal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🟢 Auto Price Selection Script -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const planSelect = document.getElementById('membership_plan_id');
        const paidAmountInput = document.getElementById('paid_amount');

        function updatePrice() {
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                paidAmountInput.value = price;
            }
        }

        planSelect.addEventListener('change', updatePrice);
        
        if(planSelect.value && !paidAmountInput.value) {
            updatePrice();
        }
    });
</script>
@endpush
@endsection