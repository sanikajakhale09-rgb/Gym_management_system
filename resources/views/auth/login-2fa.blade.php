<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Gym Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card border-0 shadow-sm rounded-4 p-4" style="width: 400px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Authenticator Code</h3>
            <p class="text-muted small">Open your <strong>Google Authenticator</strong> app and enter the 6-digit code for your gym account.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.2fa.verify') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">6-Digit OTP</label>
                <input type="text" name="otp" class="form-control text-center fs-4 tracking-widest rounded-3" placeholder="000000" maxlength="6" required autofocus autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold py-2">Verify & Access Dashboard</button>
        </form>
    </div>
</body>
</html>