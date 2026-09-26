@extends('auth.sidebar')

@section('title', 'Member Documents - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); min-height: 100vh;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-folder-open me-2" style="color: #4f46e5;"></i> Member Documents & Details
            </h2>
            <p class="text-muted small mb-0">Manage uploaded documents and view complete member profiles</p>
        </div>
        <div>
            <a href="{{ route('members.pdf') }}" class="btn btn-sm rounded-pill px-3 text-white shadow-sm fw-semibold me-2" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border: none;">
                <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('members.excel') }}" class="btn btn-sm rounded-pill px-3 text-white shadow-sm fw-semibold" style="background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); border: none;">
                <i class="fa-solid fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4">
        @forelse($members as $member)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white position-relative hover-shadow transition-all">
                <!-- Top Accent Line -->
                <div style="height: 4px; background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);"></div>
                
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Member Profile Icon, Name & Code -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); font-size: 18px;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $member->name }}</h5>
                                <span class="badge bg-light text-dark border small mt-1">{{ $member->member_code ?? 'GYM-ID' }}</span>
                            </div>
                        </div>

                        <!-- Complete Member Information List -->
                        <div class="bg-light p-3 rounded-3 mb-3 small">
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="text-muted d-block">Phone:</span>
                                    <strong class="text-dark">{{ $member->phone }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Blood Group:</span>
                                    <strong class="text-danger">{{ $member->blood_group ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-6 mt-2">
                                    <span class="text-muted d-block">Membership Plan:</span>
                                    <strong class="text-primary">{{ optional($member->membershipPlan)->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-6 mt-2">
                                    <span class="text-muted d-block">Assigned Trainer:</span>
                                    <strong class="text-dark">{{ optional($member->trainer)->name ?? 'No Trainer' }}</strong>
                                </div>
                                <div class="col-6 mt-2">
                                    <span class="text-muted d-block">Paid / Pending:</span>
                                    <span class="text-success fw-bold">₹{{ $member->paid_amount }}</span> / 
                                    <span class="text-danger fw-bold">₹{{ $member->pending_amount ?? 0 }}</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <span class="text-muted d-block">Status:</span>
                                    <span class="badge {{ $member->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $member->status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Action Section -->
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-secondary fw-semibold">
                            <i class="fa-solid fa-file-lines me-1 text-muted"></i> ID Proof:
                        </span>
                        
                        @if($member->document_path)
                            <a href="{{ asset('storage/' . $member->document_path) }}" target="_blank" class="btn btn-sm rounded-pill px-3 text-white shadow-sm fw-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); border: none;">
                                <i class="fa-solid fa-eye me-1"></i> View Document
                            </a>
                        @else
                            <span class="badge rounded-pill bg-light text-muted border px-3 py-2 fw-normal">
                                <i class="fa-solid fa-circle-xmark me-1 text-danger"></i> Not Uploaded
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="card border-0 shadow-sm rounded-4 p-5 bg-white mx-auto" style="max-width: 400px;">
                <div class="mb-3 text-muted opacity-50" style="font-size: 50px;">
                    <i class="fa-solid fa-folder-closed"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Members Found</h5>
                <p class="text-muted small mb-0">There are no member records available at the moment.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .hover-shadow {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.08) !important;
    }
</style>
@endsection