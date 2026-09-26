@forelse($members as $member)
@php
    $joiningDate = \Carbon\Carbon::parse($member->joining_date);
    $expiryDate = clone $joiningDate;

    if (stripos($member->membership_plan, 'Month') !== false) {
        $months = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
        $expiryDate->addMonths($months > 0 ? $months : 1);
    } elseif (stripos($member->membership_plan, 'Year') !== false) {
        $years = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
        $expiryDate->addYears($years > 0 ? $years : 1);
    } elseif (stripos($member->membership_plan, 'Day') !== false) {
        $days = (int) filter_var($member->membership_plan, FILTER_SANITIZE_NUMBER_INT);
        $expiryDate->addDays($days > 0 ? $days : 30);
    } else {
        $expiryDate->addMonth();
    }

    $isExpired = \Carbon\Carbon::now()->greaterThan($expiryDate);
@endphp
<tr>
    <td class="ps-4 fw-bold text-secondary">#{{ $member->id }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2 shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); font-size: 13px;">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <span class="fw-semibold text-dark">{{ $member->name }}</span>
        </div>
    </td>
    <td><span class="text-muted"><i class="fa-solid fa-phone small me-1 text-primary"></i>{{ $member->phone }}</span></td>
    <td><span class="text-muted"><i class="fa-solid fa-envelope small me-1 text-secondary"></i>{{ $member->email }}</span></td>
    <td><span class="badge rounded-pill bg-indigo-subtle text-indigo px-3 py-2 fw-semibold" style="background-color: #e0e7ff; color: #4338ca;">{{ $member->membership_plan }}</span></td>
    <td><span class="text-secondary small">{{ $member->joining_date }}</span></td>
    <td><span class="text-secondary small">{{ $expiryDate->format('Y-m-d') }}</span></td>
    <td class="pe-4">
        @if($isExpired)
            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2 fw-semibold"><i class="fa-solid fa-circle-xmark me-1"></i> Expired</span>
        @else
            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2 fw-semibold"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <div class="py-4">
            <div class="mb-3 text-muted opacity-50" style="font-size: 45px;">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No Members Found</h5>
            <p class="text-muted small mb-0">Try searching with a different name, phone, or email.</p>
        </div>
    </td>
</tr>
@endforelse