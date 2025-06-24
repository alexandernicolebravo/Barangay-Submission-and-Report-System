<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- DILG Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicondilg.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16dilg.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32dilg.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Modern UI CSS -->
    <link href="{{ asset('css/modern-ui.css') }}" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* Modern Admin Color Palette */
            --primary: #6366f1;
            --primary-light: #a5b4fc;
            --primary-dark: #4338ca;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary: #64748b;
            --success: #10b981;
            --success-light: rgba(16, 185, 129, 0.1);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.1);
            --warning: #f59e0b;
            --warning-light: rgba(245, 158, 11, 0.1);
            --info: #06b6d4;
            --info-light: rgba(6, 182, 212, 0.1);
            --dark: #1e293b;

            /* Enhanced Gray Scale */
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;

            /* Modern Shadows */
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

            /* Border Radius */
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;

            /* Transitions */
            --transition-fast: 0.15s ease-in-out;
            --transition-normal: 0.3s ease-in-out;
            --transition-slow: 0.5s ease-in-out;
        }

        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Modern Admin Sidebar */
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            min-height: 100vh;
            padding: 2rem 0;
            position: fixed;
            width: inherit;
            max-width: inherit;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            border-right: 1px solid var(--gray-200);
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 1rem;
        }

        .sidebar-header h4 {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .sidebar-header small {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .nav-link {
            color: var(--gray-700);
            padding: 0.875rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: var(--radius-md);
            transition: var(--transition-normal);
            display: flex;
            align-items: center;
            font-weight: 500;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--primary-gradient);
            transition: var(--transition-normal);
            z-index: -1;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(99, 102, 241, 0.05) 100%);
            color: var(--primary);
            transform: translateX(4px);
            box-shadow: var(--shadow-md);
        }

        .nav-link:hover::before {
            width: 4px;
        }

        .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-lg);
            transform: translateX(4px);
        }

        .nav-link.active::before {
            width: 4px;
            background: rgba(255, 255, 255, 0.3);
        }

        .nav-link i {
            width: 1.5rem;
            font-size: 1.1rem;
            margin-right: 0.875rem;
            transition: var(--transition-fast);
        }

        .nav-link:hover i,
        .nav-link.active i {
            transform: scale(1.1);
        }

        /* Modern Admin Header */
        .admin-header {
            background: transparent;
            border-bottom: none;
            padding: 0;
            position: fixed;
            top: 0;
            left: 16.666667%;
            right: 0;
            width: calc(100% - 16.666667%);
            height: 72px;
            z-index: 1020;
            transition: all 0.3s ease;
        }

        .admin-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(20px);
        }

        .header-content {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100%;
            padding: 0 2rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* User Profile Styles */
        .user-profile-container {
            position: relative;
        }

        .user-profile-btn {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .user-profile-btn:hover {
            background: #6366f1;
            border-color: #6366f1;
        }

        .user-profile-btn:hover .user-name,
        .user-profile-btn:hover .user-role,
        .user-profile-btn:hover .user-dropdown-arrow {
            color: white;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .user-role {
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .user-dropdown-arrow {
            color: #94a3b8;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }

        .user-profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            width: 240px;
            overflow: hidden;
            display: none;
            z-index: 1050;
        }

        .user-profile-dropdown.show {
            display: block;
        }

        .user-profile-header {
            padding: 1.25rem;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-profile-header .user-avatar {
            width: 40px;
            height: 40px;
        }

        .user-profile-header .user-name {
            font-size: 0.9375rem;
        }

        .user-profile-menu {
            padding: 0.5rem 0;
        }

        .user-profile-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .user-profile-menu a:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        .user-profile-menu a i {
            width: 16px;
            color: #94a3b8;
        }

        .user-profile-menu .logout-btn {
            border-top: 1px solid #f1f5f9;
            color: #dc2626;
        }

        .user-profile-menu .logout-btn:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* Main Content Styles */
        .main-content {
            padding: 2rem;
            margin-left: 16.666667%;
            margin-top: 72px;
            min-height: calc(100vh - 72px);
        }

        /* Card Styles */
        .card {
            background: white;
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem;
        }

        .card-header h5 {
            color: var(--dark);
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Styles */
        .form-control, .form-select {
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            padding: 0.625rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-label {
            color: var(--gray-700);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
        }

        /* Button Styles */
        .btn {
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--gray-100);
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .table td {
            color: var(--gray-800);
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-200);
        }

        /* Utility Classes */
        .page-title {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title i {
            color: var(--primary);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
                width: 100%;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: relative !important;
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                border-right: none !important;
                padding: 1rem 0.5rem !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 1rem !important;
            }
            .admin-header {
                left: 0 !important;
                width: 100% !important;
            }
            .card {
                margin-bottom: 1rem !important;
            }
            .table {
                font-size: 0.95rem;
            }
        }
        @media (max-width: 575.98px) {
            .main-content {
                padding: 0.5rem !important;
            }
            .card-header, .card-body {
                padding: 0.75rem !important;
            }
            .form-section {
                padding: 0.75rem !important;
            }
            .btn, .btn-primary {
                padding: 0.5rem 1rem !important;
                font-size: 1rem !important;
            }
            .sidebar-header h4 {
                font-size: 1.1rem !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Admin Panel</h4>
                    <small>Control Center</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.create-report') ? 'active' : '' }}" href="{{ route('admin.create-report') }}">
                        <i class="fas fa-file-alt"></i> Report Types
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.view.submissions') ? 'active' : '' }}" href="{{ route('admin.view.submissions') }}">
                        <i class="fas fa-inbox"></i> View Submissions
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.user-management') ? 'active' : '' }}" href="{{ route('admin.user-management') }}">
                        <i class="fas fa-users"></i> User Management
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
                        <i class="fas fa-bullhorn"></i> Announcements
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.issuances.*') ? 'active' : '' }}" href="{{ route('admin.issuances.index') }}">
                        <i class="fas fa-file-alt"></i> Issuances
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Modern Header -->
                <header class="admin-header">
                    <div class="header-content">
                        <div class="header-right">
                            <!-- User Profile Dropdown -->
                            <div class="user-profile-container">
                                <div class="user-profile-btn" onclick="toggleUserDropdown()">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                        <div class="user-role">Administrator</div>
                                    </div>
                                    <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                                </div>
                                <div class="user-profile-dropdown" id="userProfileDropdown">
                                    <div class="user-profile-header">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                            <div class="user-role">Administrator</div>
                                        </div>
                                    </div>
                                    <div class="user-profile-menu">
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn">
                                            <i class="fas fa-sign-out-alt"></i>
                                            Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Initialize AJAX CSRF Token -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // User Profile Dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userProfileDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.querySelector('.user-profile-container');
            const dropdown = document.getElementById('userProfileDropdown');

            if (container && !container.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.admin-header');
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
