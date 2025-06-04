<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Facilitator Panel') - {{ config('app.name', 'Laravel') }}</title>

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
    <!-- Smooth Transitions CSS -->
    <link href="{{ asset('css/smooth-transitions.css') }}" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* Modern Facilitator Color Palette */
            --primary: #8b5cf6;
            --primary-light: #c4b5fd;
            --primary-dark: #7c3aed;
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

        /* Modern Facilitator Sidebar */
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            min-height: 100vh;
            padding: 2rem 0;
            position: fixed;
            width: 280px;
            box-shadow: var(--shadow-lg);
            z-index: 1030;
            transition: var(--transition-normal);
            overflow-y: auto;
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
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(139, 92, 246, 0.05) 100%);
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

        /* Header Styles */
        .facilitator-header {
            background: transparent;
            border-bottom: none;
            padding: 0;
            position: fixed;
            top: 0;
            left: 280px; /* Start after sidebar */
            right: 0;
            width: calc(100% - 280px); /* Adjust width to not overlap sidebar */
            height: 72px;
            z-index: 1020;
            transition: all 0.3s ease;
        }

        .facilitator-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(20px);
        }

        /* Disable header effects when modal is open */
        body.modal-open .facilitator-header {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background: rgba(255, 255, 255, 0.95) !important;
            z-index: 1020 !important;
        }

        .header-content {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100%;
            padding: 0 2rem;
            margin-left: 0; /* Remove margin since header already starts after sidebar */
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Notification Styles */
        .notification-container {
            position: relative;
        }

        .notification-btn {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .notification-btn:hover {
            background: #6366f1;
            border-color: #6366f1;
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            width: 320px;
            max-height: 400px;
            overflow: hidden;
            display: none;
            z-index: 1050;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
        }

        .mark-all-read {
            color: #6366f1;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .mark-all-read:hover {
            color: #4f46e5;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f8fafc;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            gap: 0.75rem;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item.unread {
            background: #f0f9ff;
            border-left: 3px solid #0ea5e9;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.system {
            background: #fef3c7;
            color: #d97706;
        }

        .notification-icon.report {
            background: #dbeafe;
            color: #2563eb;
        }

        .notification-icon.submission {
            background: #dcfce7;
            color: #16a34a;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .notification-message {
            color: #64748b;
            font-size: 0.8125rem;
            line-height: 1.4;
        }

        .notification-time {
            color: #94a3b8;
            font-size: 0.75rem;
            margin-top: 0.25rem;
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
            margin-left: 280px;
            margin-top: 72px; /* Account for header height */
            min-height: calc(100vh - 72px);
            transition: all 0.3s ease;
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

        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: var(--radius-sm);
        }

        .badge-primary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .badge-success {
            background: var(--success-light);
            color: var(--success);
        }

        .badge-danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        .badge-warning {
            background: var(--warning-light);
            color: var(--warning);
        }

        .badge-info {
            background: var(--info-light);
            color: var(--info);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: var(--success-light);
            color: var(--success);
        }

        .alert-danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        .modal-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.25rem;
            border-top: 1px solid var(--gray-200);
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
    </style>
    @stack('styles')
</head>
<body>
    <!-- Facilitator Header -->
    <header class="facilitator-header">
        <div class="header-content">
            <div class="header-right">
                <!-- Notifications -->
                <div class="notification-container">
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge"></span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h6>Notifications</h6>
                        </div>
                        <div class="notification-list">
                            <!-- Notifications will be populated here by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="user-profile-container">
                    <button class="user-profile-btn" id="userProfileBtn">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'F', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'Facilitator' }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'facilitator') }}</div>
                        </div>
                        <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                    </button>
                    <div class="user-profile-dropdown" id="userProfileDropdown">
                        <div class="user-profile-header">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'F', 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name ?? 'Facilitator' }}</div>
                                <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'facilitator') }}</div>
                            </div>
                        </div>
                        <div class="user-profile-menu">
                            <a href="#" onclick="alert('Profile settings coming soon!')">
                                <i class="fas fa-user"></i>
                                Profile Settings
                            </a>
                            <a href="#" onclick="alert('Account settings coming soon!')">
                                <i class="fas fa-cog"></i>
                                Account Settings
                            </a>
                            <a href="#" onclick="alert('Help center coming soon!')">
                                <i class="fas fa-question-circle"></i>
                                Help Center
                            </a>
                            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto">
                <div class="sidebar">
                    <div class="sidebar-header">
                        <h4>Facilitator Panel</h4>
                        <small>{{ auth()->user()->name }}</small>
                    </div>
                    <nav class="d-flex flex-column h-100">
                        <!-- Navigation Links Section -->
                        <div class="navigation-links">
                            <a href="{{ route('facilitator.dashboard') }}" class="nav-link {{ request()->routeIs('facilitator.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('facilitator.view-submissions') }}" class="nav-link {{ request()->routeIs('facilitator.view-submissions') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>
                                View Submissions
                            </a>
                        </div>

                        <!-- Spacer to push announcements to very bottom -->
                        <div class="flex-grow-1"></div>

                        <!-- Announcements Section at Absolute Bottom Edge -->
                        <div class="announcements-bottom-section" style="border-top: 2px solid var(--gray-200); padding-top: 1rem; margin-top: 1rem;">
                            @include('components.sidebar-announcements')
                        </div>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden logout form for header -->
    <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2canvas for seamless page transitions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Header JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let notificationsCurrentPage = 1;
            let notificationsLastPage = 1;
            let notificationsLoading = false;
            const notificationList = document.querySelector('.notification-list');
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const markAllReadBtn = document.getElementById('markAllRead');
            const userProfileBtn = document.getElementById('userProfileBtn');
            const userProfileDropdown = document.getElementById('userProfileDropdown');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function formatTimeAgo(timestamp) {
                const now = new Date();
                const past = new Date(timestamp);
                const msPerMinute = 60 * 1000;
                const msPerHour = msPerMinute * 60;
                const msPerDay = msPerHour * 24;
                const elapsed = now - past;

                if (elapsed < msPerMinute) {
                     return Math.round(elapsed/1000) + ' seconds ago';
                } else if (elapsed < msPerHour) {
                     return Math.round(elapsed/msPerMinute) + ' minutes ago';
                } else if (elapsed < msPerDay ) {
                     return Math.round(elapsed/msPerHour ) + ' hours ago';
                } else {
                     return past.toLocaleDateString() + ' ' + past.toLocaleTimeString();
                }
            }

            function getNotificationIcon(type) {
                const baseType = type.split('\\\\').pop().replace('Notification', '').toLowerCase();
                if (baseType.includes('deadline')) return '<div class="notification-icon deadline"><i class="fas fa-clock"></i></div>';
                if (baseType.includes('reportremarks')) return '<div class="notification-icon report"><i class="fas fa-file-alt"></i></div>';
                if (baseType.includes('newsubmission')) return '<div class="notification-icon submission"><i class="fas fa-file-upload"></i></div>'; // submission icon for facilitator
                return '<div class="notification-icon system"><i class="fas fa-bell"></i></div>';
            }

            function populateNotifications(data, append = false) {
                if (!append) {
                    notificationList.innerHTML = '';
                }
                if (data && data.data && data.data.length > 0) {
                    data.data.forEach(notification => {
                        const isUnread = !notification.read_at;
                        const notificationData = notification.data;
                        const message = notificationData.message || 'New notification';
                        const redirectUrl = notificationData.redirect_url || '#';
                        let title = 'Notification';
                        if (notificationData.notification_type === 'report_remarks') title = 'Report Remarks Added';
                        else if (notificationData.notification_type === 'upcoming_deadline') title = 'Upcoming Deadline';
                        else if (notificationData.notification_type === 'new_submission_received') title = 'New Submission';
                        else if (notificationData.full_report_title) title = notificationData.full_report_title;
                        else if (notificationData.report_name) title = notificationData.report_name;

                        const item = document.createElement('div');
                        item.className = `notification-item ${isUnread ? 'unread' : ''}`;
                        item.dataset.id = notification.id;
                        item.dataset.url = redirectUrl;
                        item.innerHTML = `
                            ${getNotificationIcon(notification.type)}
                            <div class="notification-content">
                                <div class="notification-title">${title}</div>
                                <div class="notification-message">${message}</div>
                                <div class="notification-time">${formatTimeAgo(notification.created_at)}</div>
                            </div>
                        `;
                        notificationList.appendChild(item);
                    });
                    notificationsLastPage = data.last_page;
                    const loadMoreWrapper = document.getElementById('loadMoreNotificationsWrapper');
                    if (notificationsCurrentPage >= notificationsLastPage) {
                        if(loadMoreWrapper) loadMoreWrapper.style.display = 'none';
                    } else {
                        if(loadMoreWrapper) loadMoreWrapper.style.display = 'block';
                    }
                } else if (!append) {
                    notificationList.innerHTML = '<div class="notification-item text-center"><small>No notifications found.</small></div>';
                    const loadMoreWrapper = document.getElementById('loadMoreNotificationsWrapper');
                    if(loadMoreWrapper) loadMoreWrapper.style.display = 'none';
                }
                updateNotificationBadgeDOM();
            }

            function fetchNotifications(page = 1, append = false) {
                if (notificationsLoading) return Promise.resolve();
                notificationsLoading = true;
                
                let loader = document.getElementById('notificationsLoader');
                if (!loader) {
                    loader = document.createElement('div');
                    loader.className = 'text-center p-2 small';
                    loader.id = 'notificationsLoader';
                    loader.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    notificationList.appendChild(loader);
                }

                return fetch('{{ route("notifications.index") }}?page=' + page, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}, on ${response.url}`);
                    }
                    return response.json();
                })
                .then(responseData => {
                    populateNotifications(responseData, append);
                    notificationsCurrentPage = responseData.current_page;
                    notificationsLastPage = responseData.last_page;
                    const loadMoreButton = document.getElementById('loadMoreNotificationsBtn');
                    if (loadMoreButton) {
                        const loadMoreWrapper = document.getElementById('loadMoreNotificationsWrapper');
                         if (notificationsCurrentPage >= notificationsLastPage) {
                            if(loadMoreWrapper) loadMoreWrapper.style.display = 'none';
                        } else {
                            if(loadMoreWrapper) loadMoreWrapper.style.display = 'block';
                        }
                    }
                })
                .finally(() => {
                    notificationsLoading = false;
                    if(loader) loader.remove();
                });
            }

            function fetchUnreadCount() {
                fetch('{{ route("notifications.unread-count") }}', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.unread_count > 0) {
                        notificationBadge.textContent = data.unread_count;
                        notificationBadge.style.display = 'flex';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching unread count:', error));
            }

            function markNotificationAsRead(notificationId, element) {
                fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        element.classList.remove('unread');
                        fetchUnreadCount(); 
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    alert('Error marking notification as read. Please try again.');
                });
            }

            function markAllNotificationsAsRead() {
                fetch('{{ route("notifications.mark-all-as-read") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item.unread').forEach(item => item.classList.remove('unread'));
                        fetchUnreadCount();
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                    alert('Error marking all notifications as read. Please try again.');
                });
            }

            fetchUnreadCount();

            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userProfileDropdown.classList.remove('show');
                
                const wasHidden = !notificationDropdown.classList.contains('show');
                notificationDropdown.classList.toggle('show');

                if (wasHidden && notificationDropdown.classList.contains('show')) {
                    const currentlyUnreadInList = notificationList.querySelector('.notification-item.unread');
                    const isListEffectivelyEmpty = notificationList.children.length === 0 ||
                                               (notificationList.children.length === 1 && notificationList.querySelector('.text-center'));

                    if (isListEffectivelyEmpty) {
                        notificationsCurrentPage = 1;
                        fetchNotifications(notificationsCurrentPage, false)
                            .then(() => {
                                if (notificationList.querySelector('.notification-item.unread')) {
                                    markAllNotificationsAsRead();
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching notifications on bell click:', error);
                                if (!isListEffectivelyEmpty) { // Check again, in case of race condition or partial load
                                 notificationList.innerHTML = `<div class="notification-item text-center text-danger"><small>Error loading notifications.</small></div>`;
                                }
                            });
                    } else if (currentlyUnreadInList) {
                        markAllNotificationsAsRead();
                    }
                }
            });
            
            if (!document.getElementById('loadMoreNotificationsWrapper')) {
                const loadMoreWrapper = document.createElement('div');
                loadMoreWrapper.className = 'notification-footer text-center p-2';
                loadMoreWrapper.id = 'loadMoreNotificationsWrapper';
                loadMoreWrapper.style.display = 'none';
                loadMoreWrapper.innerHTML = '<button class="btn btn-link btn-sm" id="loadMoreNotificationsBtn">Load More</button>';
                notificationList.parentNode.insertBefore(loadMoreWrapper, notificationList.nextSibling);
                
                document.getElementById('loadMoreNotificationsBtn').addEventListener('click', function() {
                    if (notificationsCurrentPage < notificationsLastPage && !notificationsLoading) {
                        notificationsCurrentPage++;
                        fetchNotifications(notificationsCurrentPage, true);
                    }
                });
            }

            userProfileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.remove('show');
                userProfileDropdown.classList.toggle('show');
            });

            notificationList.addEventListener('click', function(e) {
                const item = e.target.closest('.notification-item');
                if (item) {
                    const notificationId = item.dataset.id;
                    const redirectUrl = item.dataset.url;
                    if (notificationId && item.classList.contains('unread')) {
                        markNotificationAsRead(notificationId, item);
                    }
                    if (redirectUrl && redirectUrl !== '#') {
                        window.location.href = redirectUrl;
                    }
                }
            });

            function updateNotificationBadgeDOM() {
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.style.display = 'flex';
                } else {
                    notificationBadge.style.display = 'none';
                }
            }

            // Header scroll effect
            window.addEventListener('scroll', function() {
                const header = document.querySelector('.facilitator-header');
                if (header) {
                    if (window.scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }
            });

            // Modal z-index fix
            document.addEventListener('show.bs.modal', function(e) {
                e.target.style.zIndex = '1055';
                setTimeout(() => {
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.style.zIndex = '1040';
                    }
                }, 0);
            });
        });
    </script>

    <!-- Initialize AJAX CSRF Token -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <!-- Scroll Position Preservation -->
    <script src="{{ asset('js/scroll-position.js') }}"></script>
    <!-- AJAX Form Handling -->
    <script src="{{ asset('js/ajax-forms.js') }}"></script>
    @stack('scripts')
</body>
</html>
