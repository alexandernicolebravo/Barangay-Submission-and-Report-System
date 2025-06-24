<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DILG - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #003366;
            --primary-light: #0055a4;
            --primary-dark: #002244;
            --secondary: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
        }
        
        body {
            background-color: #f8f9fa;
            height: 100vh;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }
        
        .login-container {
            height: 100vh;
            max-width: 100vw;
            overflow: hidden;
        }
        
        .announcement-side {
            height: 100%;
            padding: 0;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .login-side {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background-color: #ffffff;
            position: relative;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
            width: 100%;
            /* justify-content: center; Removed to allow left-align for logo */
        }
        
        .logo-img {
            width: 200px;
            height: 200px;
            /* max-height: 100%; */ /* Removed to allow fixed height and overflow */
            object-fit: contain;
            filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.1));
            /* margin-right will be handled by text container */
        }
        
        .logo-text-container {
            flex-grow: 1;
            text-align: center;
            padding-left: 20px;
            padding-bottom: 20px;
    
        }
        
        .logo-text {
            color: #FFFFFF;
            font-size: 22px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            text-shadow: none;
            white-space: nowrap;
        }
        
        .login-form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 0 20px;
        }
        
        .login-form {
            width: 100%;
            max-width: 380px;
            padding: 40px;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: slideUpFadeIn 0.7s ease-out forwards; /* Added animation */
        }
        
        .login-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-label {
            color: #4b5563;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .form-control {
            height: 50px;
            border-radius: 12px;
            box-shadow: none;
            margin-bottom: 20px;
            padding-left: 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
            background-color: #fff;
            transform: translateY(-1px);
        }
        
        .login-btn {
            height: 50px;
            border-radius: 12px;
            font-weight: 600;
            background-color: var(--primary);
            border: none;
            transition: all 0.3s ease-in-out; /* Smoother transition */
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px) scale(1.03); /* More lift and slight scale */
            box-shadow: 0 8px 20px rgba(0, 51, 102, 0.25); /* More pronounced shadow */
        }
        
        .carousel-item {
            height: 100%;
        }
        
        .announcement-content {
            padding: 30px;
            max-width: 90%;
            margin: 0 auto;
        }
        
        .announcement-content-overlay {
            width: 100%;
        }
        
        .announcement-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            line-height: 1.2;
        }
        
        .announcement-text {
            font-size: 15px;
            opacity: 0.9;
            margin-bottom: 25px;
            color: white;
            max-height: 200px;
            overflow-y: auto;
            line-height: 1.5;
        }
        
        /* Custom scrollbar for announcement text */
        .announcement-text::-webkit-scrollbar {
            width: 4px;
        }
        
        .announcement-text::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        
        .announcement-text::-webkit-scrollbar-track {
            background: transparent;
        }
        
        /* Category-specific badge colors */
        .announcement-badge {
            display: inline-block;
            padding: 8px 16px;
            color: #ffffff;
            backdrop-filter: blur(4px);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .badge-announcement {
            background: #0f2754; /* Dark blue for general announcements */
        }
        
        .badge-recognition {
            background: #9b1d1f; /* Red for recognition */
        }
        
        .badge-upcoming_event {
            background: #ec9c27; /* Orange for upcoming events */
        }
        
        .badge-important_update {
            background: #570001; /* Dark red for important updates */
        }
        
        .footer-text {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        
        .remember-me-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me-container input {
            margin-right: 10px;
        }
        
        .remember-me-container label {
            font-size: 14px;
            color: #6c757d;
            cursor: pointer;
        }
        
        .forgot-password {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .announcement-header {
            background-color: #0f2754;
            padding: 8px 40px;
            padding-top: 25px;
            box-sizing: border-box;
            width: 100%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            height: 100px; /* Added fixed height for the header bar */
            overflow: visible; /* Ensure overflowing content is visible */
        }
        
        .announcement-body {
            flex-grow: 1;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #003366 0%, #001a33 100%);
        }
        
        @media (max-width: 768px) {
            .announcement-side {
                display: none;
            }
            
            .login-form {
                padding: 30px;
                box-shadow: none;
                /* animation: none; /* Optionally disable animation on smaller screens */
            }
        }

        @keyframes slideUpFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicondilg.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32dilg.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16dilg.png') }}?v=2">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 login-container">
            <!-- Left side: Announcements Carousel -->
            <div class="col-md-8 announcement-side">
                <div class="announcement-header">
                    <div class="logo-container">
                        <img src="{{ asset('images/smiledilg.png') }}" alt="DILG Logo" class="logo-img">
                        <div class="logo-text-container">
                        <div class="logo-text">Department of Interior and Local Government - Bacolod City</div>
                        </div>
                    </div>
                </div>
                
                <div class="announcement-body">
                    @php
                        $announcements = [];
                        try {
                            $announcements = \App\Models\Announcement::getActiveAnnouncements();
                        } catch (\Exception $e) {
                            // Silently handle database errors
                        }
                    @endphp
                    
                    @if(count($announcements) > 0)
                        <x-announcement-carousel :announcements="$announcements" />
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="announcement-content text-center">
                                <div class="announcement-badge badge-announcement">
                                    <i class="fas fa-star me-2"></i> Official Government Platform
                                </div>
                                <h1 class="announcement-title">Department of the Interior and Local Government<br>Bacolod City</h1>
                                <p class="announcement-text">
                                    Welcome to the DILG Barangay Reporting and Monitoring System. 
                                    This platform manages submissions, tracks performance, and 
                                    facilitates communication between government offices.
                                </p>
                                <div class="mt-4">
                                    <img src="{{ asset('images/Assets for Alex.png') }}" alt="DILG Logo" style="width: 220px; height: auto; opacity: 0.9;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                    <!-- Carousel Footer -->
<div class="footer" style="background: #003366; color: #fff; font-size: 15px; padding: 10px 20px;">
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2 mb-2 mb-md-0">
            <span>
                Contact Us:
            </span>
            <span>
                <i class="fab fa-facebook me-1"></i>
                <a href="https://www.facebook.com/DILGBCD" target="_blank" rel="noopener noreferrer" style="color: #fff; text-decoration: underline;">@DILGBCD</a>
            </span>
            <span class="mx-2 d-none d-md-inline">|</span>
            <span>
                <i class="fas fa-envelope me-1"></i>
                 dilgr6.bacolodcity@gmail.com
            </span>
            <span class="mx-2 d-none d-md-inline">|</span>
            <span> <i class="fas fa-phone me-1"></i>
                (034) 724-2363
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/footerbook.png') }}" alt="Footer Book Logo" style="height: 40px; width: auto;">
            <img src="{{ asset('images/footerbago.png') }}" alt="Bago City Logo" style="height: 40px; width: auto;">
        </div>
    </div>
</div>
            </div>

            
            
            <!-- Right side: Login form -->
            <div class="col-md-4 login-side">
                <div class="login-form-container">
                    <div class="login-form">
                        <h2 class="login-title">Sign In</h2>
                        <p class="login-subtitle">Enter your credentials to access your account</p>

        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required autocomplete="email" autofocus placeholder="Enter your email">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="remember-me-container">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                @if (Route::has('password.request'))
                                    <a class="forgot-password" href="{{ route('password.request') }}">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary login-btn w-100">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In
                            </button>
    </form>
                    </div>
                    
                    <div class="footer-text">
                        <p>Department of the Interior and Local Government<br>Bacolod City</p>
                        <p>© {{ date('Y') }} DILG. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    @media (max-width: 991.98px) {
        .login-container {
            flex-direction: column !important;
            height: auto !important;
            min-height: 100vh !important;
        }
        .announcement-side, .login-side {
            width: 100% !important;
            min-height: 300px !important;
            padding: 1rem !important;
        }
        .login-form {
            max-width: 100% !important;
            padding: 1.5rem !important;
        }
        .logo-img {
            width: 120px !important;
            height: 120px !important;
        }
    }
    @media (max-width: 575.98px) {
        .login-form {
            padding: 1rem !important;
        }
        .logo-img {
            width: 80px !important;
            height: 80px !important;
        }
        .login-title {
            font-size: 1.2rem !important;
        }
        .login-btn {
            font-size: 1rem !important;
            padding: 0.5rem 1rem !important;
        }
    }
</body>
</html>
