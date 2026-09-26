@extends('auth.sidebar')

@section('title', 'Two-Factor Authentication - Gym Admin')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Success & Error Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm text-white border-0" role="alert" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm text-white border-0" role="alert" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4 justify-content-center text-center">
        <div class="col-12">
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-shield-halved me-2" style="color: #7c3aed;"></i>Two-Factor Authentication (2FA)
            </h2>
            <p class="text-muted small mb-0">Secure your gym admin account using Google Authenticator.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 bg-white p-4">
                @if(!$user->google2fa_enabled)
                    <h5 class="fw-bold mb-3" style="color: #581c87;">
                        <i class="fa-solid fa-qrcode me-2" style="color: #7c3aed;"></i>Step 1: Scan QR Code
                    </h5>
                    <p class="text-muted small">Open <strong>Google Authenticator</strong> or <strong>Authy</strong> app on your mobile and scan the QR code below:</p>
                    
                    <div class="text-center">
                        <div class="mb-3 p-3 rounded-4 text-center d-inline-block shadow-sm" style="background-color: #faf5ff; border: 2px dashed #d8b4fe !important;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="img-fluid rounded-3">
                        </div>
                    </div>

                    <p class="text-muted small mb-3 text-center">
                        Or enter this manual key in your app: 
                        <strong class="px-3 py-1.5 rounded shadow-sm fw-bold" style="background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; font-size: 14px;">{{ $user->google2fa_secret }}</strong>
                    </p>

                    <hr class="my-4" style="background-color: #e9d5ff; opacity: 0.5;">

                    <h5 class="fw-bold mb-3" style="color: #581c87;">
                        <i class="fa-solid fa-key me-2" style="color: #7c3aed;"></i>Step 2: Enter 6-Digit OTP to Activate
                    </h5>
                    <form action="{{ url('/dashboard/two-factor/enable') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Google Authenticator OTP Code</label>
                            <input type="text" name="otp" class="form-control rounded-pill text-center py-2.5 shadow-sm" style="border-color: #d8b4fe; letter-spacing: 4px; font-weight: 600; font-size: 1.15rem;" placeholder="------" maxlength="6" required autocomplete="off">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn text-white rounded-pill px-5 py-2.5 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="fa-solid fa-circle-check me-1"></i> Verify & Enable 2FA
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3" style="font-size: 3.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h4 class="fw-bold text-dark">2FA is Active & Secured</h4>
                        <p class="text-muted small">Your gym admin account is protected with two-factor authentication.</p>
                        
                        <form action="{{ url('/dashboard/two-factor/disable') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="btn text-white rounded-pill px-5 py-2.5 fw-semibold shadow-sm" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); border: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'" onclick="return confirm('Are you sure you want to disable 2FA?')">
                                <i class="fa-solid fa-shield-slash me-1"></i> Disable 2FA
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection