@extends('auth.sidebar')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-bell text-primary me-2"></i> All Notifications
                    </h3>
                    <p class="text-muted small mb-0">Stay updated with your latest system activities and alerts.</p>
                </div>
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-check-all me-1"></i> Mark All As Read
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notifications Card Container -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notif)
                            <div class="list-group-item p-4 d-flex justify-content-between align-items-center transition-hover @if(!$notif->read_at) bg-light-subtle border-start border-primary border-4 @else bg-white @endif border-bottom">
                                <div class="ms-2 me-auto">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-dark me-2">{{ $notif->title }}</h6>
                                        @if(!$notif->read_at)
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">New</span>
                                        @endif
                                    </div>
                                    <p class="text-secondary small mb-2">{{ $notif->message }}</p>
                                    <small class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i> {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now' }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!$notif->read_at)
                                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-circle p-2 lh-1" title="Mark as Read">
                                                <i class="bi bi-check fs-6"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('notifications.redirect', $notif->id) }}" class="btn btn-sm btn-light text-primary rounded-circle p-2 lh-1 shadow-sm" title="View Details">
                                        <i class="bi bi-arrow-right fs-6"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-bell-slash display-4 opacity-50"></i>
                                </div>
                                <h5 class="text-muted fw-normal">No notifications found</h5>
                                <p class="text-muted small mb-0">You're all caught up! Check back later for new updates.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pagination links -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection