@extends('auth.sidebar')

@section('title', 'All Members - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Success Alert Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm text-white border-0" role="alert" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-3">
        <h2 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-users-line me-2" style="color: #7c3aed;"></i> All Members
        </h2>
        
        <!-- Search Bar and Export Buttons container -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search Form -->
            <form action="{{ route('members.index') }}" method="GET" class="input-group input-group-sm shadow-sm" style="width: 250px;">
                @if(request('branch_id'))
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                @endif
                <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search members..." value="{{ request('search') }}">
                <button class="btn btn-primary px-3 rounded-end-pill" type="submit" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- PDF & Excel Export Buttons -->
            <a href="{{ route('members.pdf', ['branch_id' => request('branch_id'), 'search' => request('search')]) }}" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border: none;">
                <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('members.excel', ['branch_id' => request('branch_id'), 'search' => request('search')]) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                <i class="fa-solid fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none;">
                <i class="fa-solid fa-user-plus me-1"></i> Add Member
            </a>
        </div>
    </div>

    <!-- Branch Filter Dropdown -->
    <div class="mb-4 d-flex align-items-center gap-2">
        <span class="fw-bold text-secondary"><i class="fa-solid fa-filter me-1"></i> Filter By Branch:</span>
        <select class="form-select form-select-sm shadow-sm rounded-pill px-3 py-2 fw-semibold text-dark bg-white" style="width: 220px; border-color: #ddd6fe;" onchange="window.location.href=this.value">
            <option value="{{ route('members.index', ['search' => request('search')]) }}">All Branches</option>
            @isset($branches)
                @foreach($branches as $branch)
                    <option value="{{ route('members.index', ['branch_id' => $branch->id, 'search' => request('search')]) }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            @endisset
        </select>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" style="font-size: 15px;">
                    <thead class="text-white text-uppercase" style="background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%); font-size: 13px; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 ps-4">Member ID</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Phone</th>
                            <th class="py-3">Plan</th>
                            <th class="py-3">Trainer & PT Charges</th>
                            <th class="py-3">Joining Date</th>
                            <th class="py-3">Paid Amount</th>
                            <th class="py-3">Pending Amount</th>
                            <th class="py-3">Next Due Date</th>
                            <th class="py-3">Expiry Date</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3">Branch</th>
                            <th class="py-3 text-center">ID Card / QR</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        @php
                            $latestSub = $member->latestSubscription;
                            $latestFreeze = $latestSub ? $latestSub->freezes()->latest()->first() : null;
                            
                            // फ्रीझ तारीख चालू आहे का ते तपासणे
                            $isFrozen = !empty($latestFreeze) && empty($latestFreeze->unfrozen_at) && \Carbon\Carbon::parse($latestFreeze->freeze_to)->endOfDay()->isFuture();
                            
                            // ओरिजिनल एक्स्पायरी तारीख
                            $rawExpiry = $member->expiry_date ?? optional($latestSub)->end_date;
                            $expiryFormatted = $rawExpiry ? \Carbon\Carbon::parse($rawExpiry)->format('d M Y') : 'N/A';
                            
                            // एकूण फ्रीझचे दिवस
                            $totalFreezeDays = $latestFreeze ? (int) $latestFreeze->freeze_days : 0;
                            
                            // फायनल एक्स्पायरी तारीख काढणे
                            if ($rawExpiry) {
                                $finalExpiryDateCarbon = \Carbon\Carbon::parse($rawExpiry)->addDays($totalFreezeDays);
                                $finalExpiryDate = $finalExpiryDateCarbon->format('d M Y');
                            } else {
                                $finalExpiryDateCarbon = null;
                                $finalExpiryDate = 'N/A';
                            }

                            // अचूक एक्स्पायरी चेक: आजची तारीख फायनल एक्स्पायरीच्या पुढे निघून गेली असेल तरच Expired दाखवेल
                            $today = \Carbon\Carbon::today();
                            $isExpired = $finalExpiryDateCarbon ? $today->gt($finalExpiryDateCarbon->copy()->endOfDay()) : false;
                        @endphp
                        <tr class="border-bottom">
                            <!-- 1. Member ID -->
                            <td class="ps-4 py-3">
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold shadow-sm" style="font-size: 13px;">
                                    <i class="fa-solid fa-id-card me-1" style="color: #7c3aed;"></i> {{ $member->member_code ?? 'GYM-' . $member->id }}
                                </span>
                            </td>

                            <!-- 2. Name -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('workouts.show', $member->id) }}" title="View Assigned Workout Plan" class="text-decoration-none">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 38px; height: 38px; font-size: 14px; background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    </a>
                                    <span class="fw-semibold text-dark" style="font-size: 15px;">{{ $member->name }}</span>
                                </div>
                            </td>

                            <!-- 3. Email -->
                            <td><span class="fw-medium text-dark">{{ $member->email ?? 'N/A' }}</span></td>

                            <!-- 4. Phone -->
                            <td><span class="fw-medium text-dark">{{ $member->phone }}</span></td>

                            <!-- 5. Plan -->
                            <td>
                                <span class="badge px-3 py-2 fw-semibold shadow-sm" style="font-size: 14px; background-color: #fae8ff; color: #86198f; border: 1px solid #f5d0fe;">
                                    <i class="fa-solid fa-tag me-1"></i> {{ optional($member->membershipPlan)->name ?? optional($latestSub)->plan->name ?? 'No Plan' }}
                                </span>
                            </td>

                            <!-- 6. Trainer & PT Charges -->
                            <td>
                                <span class="badge px-3 py-2 fw-semibold shadow-sm" style="font-size: 14px; background-color: #ccfbf1; color: #115e59; border: 1px solid #99f6e4;">
                                    <i class="fa-solid fa-dumbbell me-1"></i> 
                                    {{ optional($member->trainer)->name ?? optional($latestSub)->trainer->name ?? 'No Trainer' }}
                                    @if($member->pt_charges ?? optional($latestSub)->pt_charges)
                                        (₹{{ $member->pt_charges ?? optional($latestSub)->pt_charges }})
                                    @endif
                                </span>
                            </td>

                            <!-- 7. Joining Date (New Column) -->
                            <td>
                                <span class="fw-semibold text-dark" style="font-size: 14px;">
                                    <i class="fa-solid fa-calendar-plus me-1 text-primary"></i> 
                                    {{ $member->joining_date ? \Carbon\Carbon::parse($member->joining_date)->format('d M Y') : 'N/A' }}
                                </span>
                            </td>

                            <!-- 8. Paid Amount -->
                            <td class="fw-bold fs-6" style="color: #059669;">
                                ₹{{ number_format($member->amount_paid ?? optional($latestSub)->amount_paid ?? 0, 2) }}
                            </td>

                            <!-- 9. Pending Amount -->
                            <td class="fw-bold fs-6" style="color: #d97706;">
                                ₹{{ number_format($member->balance_amount ?? optional($latestSub)->balance_amount ?? 0, 2) }}
                            </td>
                            
                            <!-- 10. Next Due Date -->
                            <td class="align-middle">
                                @php
                                    $nextDueDate = $member->balance_due_date 
                                        ?? $member->due_date 
                                        ?? $member->next_due_date 
                                        ?? $member->installment_date 
                                        ?? optional($latestSub)->balance_due_date 
                                        ?? optional($latestSub)->due_date 
                                        ?? optional($latestSub)->next_due_date 
                                        ?? optional($latestSub)->installment_date;
                                @endphp

                                @if(!empty($nextDueDate))
                                    <span class="fw-semibold" style="color: #e11d48;">
                                        <i class="fa-solid fa-calendar-days me-1"></i> 
                                        {{ \Carbon\Carbon::parse($nextDueDate)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            
                            <!-- 11. Expiry Date -->
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-dark" style="font-size: 14px;">
                                        <i class="fa-regular fa-calendar me-1"></i> {{ $expiryFormatted }}
                                    </span>
                                    
                                    @if($totalFreezeDays > 0)
                                        <span class="fw-bold text-danger mt-1" style="font-size: 12px;">
                                            <i class="fa-solid fa-snowflake me-1 text-primary"></i> Extended: {{ $totalFreezeDays }} Days
                                        </span>
                                        <span class="fw-bold text-success mt-1" style="font-size: 13px;" title="Final Expiry after freeze extension">
                                            <i class="fa-solid fa-calendar-check me-1"></i> New: {{ $finalExpiryDate }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 12. Status -->
                            <td class="text-center">
                                <span class="badge {{ strtolower($member->status) == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $member->status }}
                                </span>
                            </td>

                            <!-- 13. Branch -->
                            <td>
                                <span class="fw-medium text-dark">{{ optional($member->branch)->name ?? 'Main Branch' }}</span>
                            </td>

                            <!-- 14. ID Card / QR Button -->
                            <td class="text-center">
                                <a href="{{ route('members.id-card', $member->id) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1 shadow-sm rounded-2 text-primary" title="Generate ID Card / QR">
                                    <i class="fa-solid fa-qrcode fs-6"></i>
                                </a>
                            </td>

                            <!-- 15. Actions -->
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2" style="color: #0284c7;" data-bs-toggle="modal" data-bs-target="#viewMemberModal{{ $member->id }}" title="View Info">
                                        <i class="fa-solid fa-eye fs-6"></i>
                                    </button>
                                    <a href="{{ route('members.renew', $member->id) }}" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2" style="color: #0d9488;" title="Renew Membership">
                                        <i class="fa-solid fa-rotate-right fs-6"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2" style="color: #7c3aed;" title="Edit">
                                        <i class="fa-solid fa-pen-to-square fs-6"></i>
                                    </a>
                                    <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border px-2.5 py-1.5 shadow-sm rounded-2" style="color: #e11d48;" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash fs-6"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal for Viewing Member Info -->
                        <div class="modal fade" id="viewMemberModal{{ $member->id }}" tabindex="-1" aria-labelledby="viewMemberLabel{{ $member->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%);">
                                        <h5 class="modal-title fw-bold" id="viewMemberLabel{{ $member->id }}">
                                            <i class="fa-solid fa-user-shield me-2"></i> Member Details
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 text-start bg-light" style="font-size: 15px;">
                                        <div class="bg-white p-4 rounded-4 shadow-sm">
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Member ID:</div>
                                                <div class="col-7 fw-bold" style="color: #7c3aed;">{{ $member->member_code ?? 'GYM-' . $member->id }}</div>
                                            </div>
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Name:</div>
                                                <div class="col-7 text-dark fw-bold">{{ $member->name }}</div>
                                            </div>
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Email:</div>
                                                <div class="col-7 text-dark">{{ $member->email ?? 'N/A' }}</div>
                                            </div>
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Phone:</div>
                                                <div class="col-7 text-dark">{{ $member->phone }}</div>
                                            </div>
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Joining Date:</div>
                                                <div class="col-7 text-dark">{{ $member->joining_date ? \Carbon\Carbon::parse($member->joining_date)->format('d M Y') : 'N/A' }}</div>
                                            </div>
                                            <div class="mb-3 row border-bottom pb-2">
                                                <div class="col-5 text-muted fw-semibold">Branch:</div>
                                                <div class="col-7 text-dark">{{ optional($member->branch)->name ?? 'Main Branch' }}</div>
                                            </div>
                                            
                                            @if($latestFreeze)
                                                <div class="mt-4 pt-3 border-top">
                                                    <h6 class="fw-bold text-purple mb-3" style="color: #6f42c1;"><i class="fas fa-snowflake me-1"></i> Latest Freeze Information</h6>
                                                    <div class="row g-2 bg-light p-3 rounded-3 border">
                                                        <div class="col-6">
                                                            <span class="text-muted small">Freeze From:</span>
                                                            <p class="fw-bold mb-1">{{ $latestFreeze->freeze_from ? \Carbon\Carbon::parse($latestFreeze->freeze_from)->format('d M, Y') : '-' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted small">Freeze To:</span>
                                                            <p class="fw-bold mb-1">{{ $latestFreeze->freeze_to ? \Carbon\Carbon::parse($latestFreeze->freeze_to)->format('d M, Y') : '-' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted small">Total Days:</span>
                                                            <p class="fw-bold text-info mb-1">{{ $latestFreeze->freeze_days ?? 0 }} Days</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted small">Reason:</span>
                                                            <p class="fw-bold text-danger mb-1">{{ $latestFreeze->reason ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 bg-white px-4 py-3">
                                        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4 fs-6">No members found for this branch.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   <!-- Pagination Links Corrected -->
<div class="d-flex justify-content-center mt-4">
    {!! $members->links('pagination::bootstrap-5') !!}
</div>

@if(isset($downloadPdfId) && $downloadPdfId)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const pdfUrl = "{{ route('members.receipt', ':id') }}".replace(':id', "{{ $downloadPdfId }}");
        
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = pdfUrl;
        document.body.appendChild(iframe);
    });
</script>
@endif
@endsection