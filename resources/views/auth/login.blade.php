<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Admin Login</title>
    <!-- Favicon fix -->
    <link rel="icon" href="data:,">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f7f4ef; /* Soft warm beige background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .login-container {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 15px 35px rgba(139, 125, 107, 0.12);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            border: 1px solid #e6dfd5;
        }
        .image-side {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1000&auto=format&fit=crop') center/cover no-repeat;
            min-height: 450px;
            position: relative;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .brand-logo {
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.25rem;
            text-decoration: none;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .form-side {
            padding: 3rem 2.5rem;
            background-color: #ffffff;
            color: #4a4033;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .custom-input-group {
            background-color: #faf8f5;
            border: 1px solid #d5cbbe;
            border-radius: 50rem;
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }
        .custom-input-group:focus-within {
            border-color: #8b7d6b;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(139, 125, 107, 0.15);
        }
        .custom-input-group .input-icon {
            color: #8b7d6b;
            margin-right: 0.75rem;
        }
        .custom-input {
            background: transparent;
            border: none;
            color: #4a4033;
            width: 100%;
            outline: none;
            font-size: 0.95rem;
        }
        .custom-input::placeholder {
            color: #a39685;
        }
        .btn-soft-beige {
            background-color: #8b7d6b;
            border: none;
            color: #ffffff;
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.75rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(139, 125, 107, 0.25);
        }
        .btn-soft-beige:hover {
            background-color: #726554;
            color: #ffffff;
            box-shadow: 0 6px 15px rgba(139, 125, 107, 0.35);
        }
        .form-check-input:checked {
            background-color: #8b7d6b;
            border-color: #8b7d6b;
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="row login-container g-0">
                <!-- Left Image & Brand Section -->
                <div class="col-lg-6 image-side d-none d-lg-flex">
                    <div>
                        <a href="#" class="brand-logo">
                            <i class="fa-solid fa-dumbbell me-2"></i> WORKOUT
                        </a>
                    </div>
                    <div>
                        <h3 class="text-white fw-bold mb-1">BUILD YOUR</h3>
                        <h2 class="text-white fw-bold">ADMIN POWER</h2>
                    </div>
                </div>

                <!-- Right Form Section -->
                <div class="col-lg-6 form-side">
                    <div class="mb-4">
                        <h3 class="fw-bold mb-1" style="color: #4a4033;">WELCOME BACK</h3>
                        <h4 class="fw-bold" style="color: #8b7d6b;">GYM ADMIN!</h4>
                        <p class="text-muted small">Sign in to your account to access management dashboard.</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger bg-light border-danger text-danger py-2 small mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="custom-input-group">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <!-- ईमेलसाठी जास्तीत जास्त १६ कॅरेक्टरची मर्यादा (maxlength="16") -->
                            <input type="email" class="custom-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus maxlength="25" placeholder="Admin Email">
                        </div>

                        <div class="custom-input-group">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <!-- पासवर्डसाठी ७ ते ८ कॅरेक्टरची मर्यादा (minlength="7" maxlength="8") -->
                            <input type="password" class="custom-input @error('password') is-invalid @enderror" name="password" required minlength="1" maxlength="12" placeholder="Password">
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-soft-beige">
                                LOGIN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>