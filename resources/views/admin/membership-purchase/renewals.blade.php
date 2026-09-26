@extends('auth.sidebar')

@section('content')
<div class="container-fluid px-4 py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    <!-- Centering Main Wrapper using Flex Row -->
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            <div class="row mb-4 align-items-center">
                <div class="col-md-12 text-center text-md-start">
                    <h2 class="mt-4 fw-bold text-dark"><i class="fas fa-sync-alt me-2" style="color: #6f42c1;"></i>Membership Renewals</h2>
                    <p class="text-muted mb-0">View and manage all gym membership renewals and history</p>
                </div>
            </div>

            <!-- Search Card -->
            <div class="card mb-4 shadow-sm border-0 rounded-4 bg-white">
                <div class="card-body p-4">
                    <form action="{{ route('admin.membership-purchase.renewals') }}" method="GET" class="row g-3 align-items-center justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control border-0 bg-white shadow-none ps-2" placeholder="Search by member name or phone..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm fw-semibold" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2d84 100%); border: none;">Filter</button>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('admin.membership-purchase.renewals') }}" class="btn btn-secondary w-100 rounded-pill shadow-sm fw-semibold">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Renewals List Table -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2d84 100%);">
                    <h5 class="mb-0 fw-bold text-white"><i class="fas fa-sync-alt me-2"></i> Renewals List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center text-md-start">
                            <thead class="table-light text-uppercase fs-7 text-secondary">
                                <tr>
                                    <th class="ps-4">Member Name</th>
                                    <th>Plan Name</th>
                                    <th>Previous Expiry</th>
                                    <th>Renewal Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($renewalsSubscriptions ?? [] as $subscription)
                                    @php
                                        /** @var \App\Models\MemberSubscription $subscription */
                                        $member = $subscription->member ?? null;
                                        $memberName = $member ? $member->name : 'Unknown Member';
                                        $memberPhone = $member ? $member->phone : '';
                                        
                                        $plan = $subscription->membershipPlan ?? ($subscription->plan ?? null);
                                        $planName = $plan ? $plan->name : 'N/A';
                                        
                                        $prevExpiryFormatted = $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M, Y') : '-';
                                        $renewalDateFormatted = $subscription->updated_at ? \Carbon\Carbon::parse($subscription->updated_at)->format('d M, Y') : '-';
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark">{{ $memberName }}</strong>
                                            <br><small class="text-muted">{{ $memberPhone }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $planName }}</span></td>
                                        <td>{{ $prevExpiryFormatted }}</td>
                                        <td>{{ $renewalDateFormatted }}</td>
                                        <td><span class="fw-semibold text-success">₹{{ $subscription->price_paid ?? '0.00' }}</span></td>
                                        <td>
                                            <span class="badge fw-bold px-3 py-2 rounded-pill" style="background-color: #e0f2fe !important; color: #0284c7 !important;">Renewed</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-1">
                                                <!-- Renew Action Button (Triggers Renew Modal) -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#renewModal{{ $subscription->id }}" title="Renew Membership">
                                                    <i class="fas fa-redo-alt"></i>
                                                </button>

                                                <!-- View Details Button (Modal Trigger) -->
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $subscription->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Edit / Update Button -->
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $subscription->id }}" title="Edit Renewal">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Delete Form & Button -->
                                                <form action="{{ route('admin.membership-purchase.delete', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this renewal record?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="Delete Renewal">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Renew Modal for each subscription using your form code -->
                                            <div class="modal fade text-start" id="renewModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="renewModalLabel{{ $subscription->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content rounded-4 shadow">
                                                        @if($member)
                                                            <form action="{{ route('members.storeRenewal', $member->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header bg-light">
                                                                    <h5 class="modal-title fw-bold text-dark" id="renewModalLabel{{ $subscription->id }}">
                                                                        <i class="fas fa-redo-alt me-2" style="color: #6f42c1;"></i> Renew Membership ({{ $memberName }})
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <!-- Tumche form fields ithe astil -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Current Plan</label>
                                                                        <input type="text" class="form-control" value="{{ $planName }}" disabled>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Renewal Amount / Notes</label>
                                                                        <input type="text" name="notes" class="form-control" placeholder="Enter details if required">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2d84 100%); border: none;">Renew Membership</button>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-dark">Error</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <p class="text-danger mb-0">Member information is missing for this subscription.</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- View Details Modal for each subscription -->
                                            <div class="modal fade text-start" id="viewModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $subscription->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 shadow">
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-dark" id="viewModalLabel{{ $subscription->id }}">
                                                                <i class="fas fa-info-circle me-2" style="color: #6f42c1;"></i> Renewal Details (#{{ $subscription->id }})
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Member Name</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $memberName }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Phone</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $memberPhone ?: '-' }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Plan Name</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $planName }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Status</label>
                                                                    <p class="mb-2">
                                                                        <span class="badge fw-bold px-3 py-1 rounded-pill bg-success bg-opacity-10 text-success">
                                                                            {{ ucfirst($subscription->status) }}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Previous Expiry</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $prevExpiryFormatted }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="text-muted small fw-semibold">Renewal Date</label>
                                                                    <p class="fw-bold text-dark mb-2">{{ $renewalDateFormatted }}</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="text-muted small fw-semibold">Amount Paid</label>
                                                                    <p class="fw-bold text-success mb-2">₹ {{ $subscription->price_paid ?? '0.00' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Update Modal for each subscription -->
                                            <div class="modal fade text-start" id="editModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $subscription->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content rounded-4 shadow">
                                                        <form action="{{ route('admin.membership-purchase.update', $subscription->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-dark" id="editModalLabel{{ $subscription->id }}">Update Renewal Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Member</label>
                                                                    <input type="text" class="form-control" value="{{ $memberName }}" disabled>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Amount (₹)</label>
                                                                    <input type="number" step="0.01" name="price_paid" class="form-control" value="{{ $subscription->price_paid }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">End Date</label>
                                                                    <input type="date" name="end_date" class="form-control" value="{{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') : '' }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #6f42c1 0%, #4e2d84 100%); border: none;">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                            No membership renewal records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links Section -->
            @if(isset($renewalsSubscriptions) && method_exists($renewalsSubscriptions, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $renewalsSubscriptions->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection