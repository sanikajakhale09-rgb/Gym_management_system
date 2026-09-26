@extends('auth.sidebar')

@section('title', 'Notifications - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <!-- Page Header -->
    <div class="row justify-content-center mb-4">
        <div class="col-xl-10 col-xxl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-4 rounded-4 shadow-sm border">
                <div class="mb-2 mb-md-0">
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-bell text-warning me-2"></i> Notifications
                    </h3>
                    <p class="text-muted small mb-0">Stay updated with recent activities and alerts.</p>
                </div>
                <div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-semibold border border-warning border-opacity-25">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Live Alerts
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Centered Card Wrapper -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-xxl-9">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary text-uppercase small tracking-wide">
                        <i class="fa-solid fa-list-ul me-2 text-warning"></i> Recent Alerts
                    </span>
                    <span class="text-muted small"><i class="fa-solid fa-envelope-open-text text-warning me-1"></i> System Log</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notif)
                            @php
                                // Veg-vegale colors ani icons sathi condition
                                $badgeColor = 'text-warning bg-warning';
                                $iconClass = 'fa-bell';
                                if(str_contains(strtolower($notif->title), 'expire')) {
                                    $badgeColor = 'text-danger bg-danger';
                                    $iconClass = 'fa-triangle-exclamation';
                                } elseif(str_contains(strtolower($notif->title), 'payment') || str_contains(strtolower($notif->title), 'fees')) {
                                    $badgeColor = 'text-success bg-success';
                                    $iconClass = 'fa-wallet';
                                } elseif(str_contains(strtolower($notif->title), 'member')) {
                                    $badgeColor = 'text-primary bg-primary';
                                    $iconClass = 'fa-user-plus';
                                }
                            @endphp

                            <a href="{{ route('notifications.redirect', $notif->id) }}" 
                               class="list-group-item list-group-item-action p-4 border-bottom transition-all @if(!$notif->is_read) bg-light fw-bold border-start border-warning border-4 @else bg-white hover-bg-light @endif">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <div class="{{ $badgeColor }} bg-opacity-10 rounded-circle p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                            <i class="fa-solid {{ $iconClass }} fa-sm"></i>
                                        </div>
                                        <h6 class="mb-0 text-dark fs-6 fw-bold">{{ $notif->title }}</h6>
                                    </div>
                                    <small class="text-muted bg-light px-3 py-1 rounded-pill border small">
                                        <i class="fa-regular fa-clock me-1 text-secondary"></i> {{ $notif->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-0 text-secondary ps-5 ms-2 small lh-base">{{ $notif->message }}</p>
                            </a>
                        @empty
                            <div class="text-center text-muted py-5 my-4">
                                <div class="bg-warning bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px;">
                                    <i class="fa-regular fa-bell-slash fa-2x text-warning"></i>
                                }
                                </div>
                                <h5 class="fw-bold text-dark mb-1">No Notifications</h5>
                                <p class="text-muted small mb-0">You're all caught up! No new notifications found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Centered Pagination links -->
            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection