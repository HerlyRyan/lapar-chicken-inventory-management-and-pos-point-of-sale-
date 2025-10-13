<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Lapar Chicken InvetPOS</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #dc2626;
            --primary-orange: #ea580c;
            --primary-yellow: #eab308;
            --light-yellow: #fef3c7;
            --light-orange: #fed7aa;
            --light-red: #fecaca;
            --gradient-main: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);
            --gradient-reverse: linear-gradient(135deg, #eab308 0%, #ea580c 50%, #dc2626 100%);
            --gradient-bg: linear-gradient(135deg, #fef3c7 0%, #fed7aa 30%, #fecaca 60%, #dc2626 100%);
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background: var(--gradient-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23a)"/><circle cx="800" cy="300" r="100" fill="url(%23a)"/><circle cx="300" cy="700" r="120" fill="url(%23a)"/><circle cx="700" cy="800" r="80" fill="url(%23a)"/></svg>');
            z-index: 1;
        }
        
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            background: var(--gradient-main);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid var(--primary-yellow);
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.25);
            background: white;
        }
        
        .btn-login {
            background: var(--gradient-main);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
        }
        
        .btn-login:hover {
            background: var(--gradient-reverse);
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(220, 38, 38, 0.4);
            color: white;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }
        
        .text-decoration-none {
            color: var(--primary-red);
            transition: color 0.3s ease;
        }
        
        .text-decoration-none:hover {
            color: var(--primary-orange);
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '🍗';
            position: absolute;
            font-size: 2rem;
            animation: float 6s ease-in-out infinite;
            opacity: 0.1;
        }
        
        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: -2s;
        }
        
        .floating-elements::after {
            top: 60%;
            right: 10%;
            animation-delay: -4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--primary-red);
            border-left: 4px solid var(--primary-red);
        }
    </style>
</head>
<body>
    <div class="floating-elements"></div>
    
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6">
                    <div class="login-card">
                        <div class="login-header">
                            <div class="logo-container">
                                <i class="bi bi-shop text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <h3 class="mb-1 fw-bold">Lapar Chicken</h3>
                            <p class="mb-0 opacity-90">Sistem Inventori & Penjualan</p>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold" style="color: var(--primary-red);">Selamat Datang</h4>
                                <p class="text-muted">Silakan masuk untuk mengakses sistem</p>
                            </div>
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-2"></i>Email
                                    </label>
                                    <input id="email" type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" 
                                           required autocomplete="email" autofocus
                                           placeholder="Masukkan email Anda">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="bi bi-lock me-2"></i>Password
                                    </label>
                                    <input id="password" type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required autocomplete="current-password"
                                           placeholder="Masukkan password Anda">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-login btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                    </button>
                                </div>
                                
                                @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a class="text-decoration-none" href="{{ route('password.request') }}">
                                            <i class="bi bi-question-circle me-1"></i>Lupa password?
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>
                        
                        <div class="card-footer text-center" style="background: rgba(254, 243, 199, 0.3); border: none; padding: 1rem;">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Sistem aman dan terpercaya
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

