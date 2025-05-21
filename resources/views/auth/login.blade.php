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
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .announcement-side {
            background-color: var(--primary);
            height: 100%;
            padding: 0;
            overflow: hidden;
            position: relative;
            color: white;
            background-image: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }

        .announcement-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://via.placeholder.com/1500x800/003366/ffffff?text=DILG');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 0;
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
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 100;
        }

        .logo-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .login-form {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .login-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 28px;
        }

        .login-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-control {
            height: 50px;
            border-radius: 8px;
            box-shadow: none;
            margin-bottom: 20px;
            padding-left: 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }

        .login-btn {
            height: 50px;
            border-radius: 8px;
            font-weight: 600;
            background-color: var(--primary);
            border: none;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }

        .carousel-item {
            height: 100vh;
        }

        .announcement-content {
            position: relative;
            z-index: 1;
            padding: 0 20px;
        }

        .announcement-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
        }

        .announcement-text {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        .announcement-badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .dilg-pattern {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
        }

        @media (max-width: 768px) {
            .announcement-side {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 login-container">
            <!-- Left side: Announcements Carousel -->
            <div class="col-md-7 announcement-side">
                <div class="logo-container">
                    <img src="{{ asset('images/dilg.png') }}" alt="DILG Logo" class="logo-img">
                </div>

                <div class="dilg-pattern"></div>

                @php
                    use Illuminate\Support\Facades\Schema;

                    $announcements = [];
                    try {
                        // Check if the announcements table exists before trying to query it
                        if (Schema::hasTable('announcements')) {
                            $announcements = \App\Models\Announcement::getActiveAnnouncements();
                        }
                    } catch (\Exception $e) {
                        // Silently handle database errors
                    }
                @endphp

                @if(count($announcements) > 0)
                    <x-announcement-carousel :announcements="$announcements" />
                @else
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="announcement-content text-center">
                            <div class="announcement-badge">
                                <i class="fas fa-star me-2"></i> Official Government Platform
                            </div>
                            <h1 class="announcement-title">Department of the Interior and Local Government</h1>
                            <p class="announcement-text">
                                Welcome to the DILG Barangay Reporting and Monitoring System.
                                This platform manages submissions, tracks performance, and
                                facilitates communication between government offices.
                            </p>
                            <div class="mt-4">
                                <img src="{{ asset('images/dilg.png') }}" alt="DILG Logo" style="width: 120px; height: auto; opacity: 0.9;">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right side: Login form -->
            <div class="col-md-5 login-side">
                <div class="login-form">
                    <h2 class="login-title">Sign In</h2>
                    <p class="login-subtitle">Enter your credentials to access your account</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
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
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary login-btn">Sign In</button>
                        </div>
                    </form>

                    <div class="text-center mt-4 text-muted small">
                        <p>Department of the Interior and Local Government</p>
                        <p>&copy; {{ date('Y') }} DILG. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
