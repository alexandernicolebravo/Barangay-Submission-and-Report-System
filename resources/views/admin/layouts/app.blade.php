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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Smooth Transitions CSS -->
    <link href="{{ asset('css/smooth-transitions.css') }}" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: rgba(59, 130, 246, 0.1);
            --secondary: #64748b;
            --success: #22c55e;
            --success-light: rgba(34, 197, 94, 0.1);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.1);
            --warning: #f59e0b;
            --warning-light: rgba(245, 158, 11, 0.1);
            --info: #8b5cf6;
            --info-light: rgba(139, 92, 246, 0.1);
            --dark: #1e293b;
            --gray-100: #f8fafc;
            --gray-200: #f1f5f9;
            --gray-300: #e2e8f0;
            --gray-400: #cbd5e1;
            --gray-500: #94a3b8;
            --gray-600: #64748b;
            --gray-700: #475569;
            --gray-800: #334155;
            --gray-900: #1e293b;
            --shadow-sm: 0 2px 12px rgba(0,0,0,0.04);
            --shadow-md: 0 5px 15px rgba(0,0,0,0.08);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        body {
            background: linear-gradient(135deg, #fafbfc 0%, #f4f6f8 100%);
            color: #475569;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            padding: 0;
            position: fixed;
            top: 0;
            width: inherit;
            max-width: inherit;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            z-index: 1030;
            border-right: 1px solid rgba(226, 232, 240, 0.6);
            backdrop-filter: blur(20px);
        }

        .sidebar-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }

        .sidebar-header h4 {
            color: #334155;
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
            letter-spacing: -0.025em;
        }

        .sidebar-header small {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-link {
            color: #64748b;
            padding: 0.875rem 2rem;
            margin: 0.25rem 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #6366f1;
            transform: scaleY(0);
            border-radius: 0 2px 2px 0;
        }

        .nav-link:hover {
            background: rgba(99, 102, 241, 0.08);
            color: #475569;
        }

        .nav-link.active {
            background: rgba(99, 102, 241, 0.12);
            color: #6366f1;
            font-weight: 600;
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            width: 1.5rem;
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }

        /* Main Content Styles */
        .main-content {
            padding: 2rem;
            margin-left: 16.666667%;
            margin-top: 72px; /* Account for fixed header */
            min-height: calc(100vh - 72px);
        }

        /* Card Styles */
        .card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            background: rgba(248, 250, 252, 0.6);
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
            padding: 1.5rem 2rem;
        }

        .card-header h5 {
            color: #334155;
            font-weight: 700;
            margin: 0;
            font-size: 1.125rem;
            letter-spacing: -0.025em;
        }

        .card-body {
            padding: 2rem;
            background: white;
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

        /* Header Styles */
        .admin-header {
            background: transparent;
            border-bottom: none;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
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

        /* Completely disable header effects when modal is open */
        body.modal-open .admin-header {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background: rgba(255, 255, 255, 0.95) !important;
            z-index: 1020 !important;
        }

        /* Prevent page scroll when modal is open */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        .header-content {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100%;
            padding: 0 2.5rem;
            margin-left: 16.666667%; /* Account for sidebar width */
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        /* Notification Styles */
        .notification-container {
            position: relative;
        }

        .notification-btn {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            color: #64748b;
            font-size: 1.125rem;
            padding: 0.625rem;
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-btn:hover {
            background: #6366f1;
            color: white;
            border-color: #6366f1;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            width: 380px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1030;
            display: none;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
        }

        .mark-all-read {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .mark-all-read:hover {
            text-decoration: underline;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background: var(--gray-100);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: var(--primary-light);
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .notification-icon.system {
            background: var(--danger-light);
            color: var(--danger);
        }

        .notification-icon.user {
            background: var(--info-light);
            color: var(--info);
        }

        .notification-icon.report {
            background: var(--success-light);
            color: var(--success);
        }

        .notification-icon.deadline {
            background: var(--warning-light);
            color: var(--warning);
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            color: var(--gray-800);
            font-size: 0.875rem;
            line-height: 1.4;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .notification-footer {
            padding: 0.75rem 1rem;
            text-align: center;
            border-top: 1px solid var(--gray-200);
        }

        .view-all-notifications {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .view-all-notifications:hover {
            text-decoration: underline;
        }

        /* Enhanced Dashboard Styles */
        .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #334155;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .page-title i {
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-right: 1rem;
        }

        /* Enhanced Stats Cards - Consistent with report type cards */
        .stat-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .stat-card .card-body {
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 22px;
            color: #fff;
        }

        .primary-icon {
            background-color: #4361ee;
        }

        .success-icon {
            background-color: #36b37e;
        }

        .warning-icon {
            background-color: #ffab00;
        }

        .danger-icon {
            background-color: #f5365c;
        }

        .stat-content {
            flex-grow: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            color: #2d3748;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            margin: 0;
            margin-top: 2px;
        }

        /* Enhanced Table Styles */
        .table {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table th {
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.9) 0%, rgba(241, 245, 249, 0.7) 100%);
            color: #475569;
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1.25rem;
            border: none;
        }

        .table td {
            color: #334155;
            padding: 1.25rem;
            vertical-align: middle;
            border-color: rgba(226, 232, 240, 0.6);
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        /* Simple Modal Fix - Like User Management */
        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        /* Ensure header doesn't interfere */
        .admin-header {
            z-index: 1020 !important;
        }

        /* User Profile Dropdown Styles */
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
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            color: #334155;
            font-weight: 600;
            font-size: 0.875rem;
            line-height: 1.2;
            transition: color 0.3s ease;
        }

        .user-role {
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.2;
            transition: color 0.3s ease;
        }

        .user-dropdown-arrow {
            color: #64748b;
            font-size: 0.75rem;
            transition: color 0.3s ease;
        }

        .user-dropdown-arrow {
            color: var(--gray-500);
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }

        .user-profile-btn.show .user-dropdown-arrow {
            transform: rotate(180deg);
        }

        .user-profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            width: 240px;
            z-index: 1030;
            display: none;
        }

        .user-profile-dropdown.show {
            display: block;
        }

        .user-profile-header {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-profile-header .user-avatar {
            width: 40px;
            height: 40px;
        }

        .user-profile-menu {
            padding: 0.5rem 0;
        }

        .user-profile-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: background 0.2s ease;
            font-size: 0.875rem;
        }

        .user-profile-item:hover {
            background: var(--gray-100);
            color: var(--gray-800);
        }

        .user-profile-item i {
            width: 1rem;
            color: var(--gray-500);
        }

        .user-profile-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.5rem 0;
        }

        .user-profile-item.logout {
            color: var(--danger);
        }

        .user-profile-item.logout:hover {
            background: var(--danger-light);
            color: var(--danger);
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
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="header-content">
            <div class="header-right">
                <!-- Notifications -->
                <div class="notification-container">
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h6>Notifications</h6>
                            <a href="#" class="mark-all-read" id="markAllRead">Mark all as read</a>
                        </div>
                        <div class="notification-list">
                            <!-- Sample notifications -->
                            <div class="notification-item unread" data-type="system">
                                <div class="notification-icon system">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-message">System maintenance scheduled for tonight at 2:00 AM</div>
                                    <div class="notification-time">2 hours ago</div>
                                </div>
                            </div>
                            <div class="notification-item unread" data-type="report">
                                <div class="notification-icon report">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-message">New report submitted by Barangay San Jose</div>
                                    <div class="notification-time">4 hours ago</div>
                                </div>
                            </div>
                            <div class="notification-item unread" data-type="deadline">
                                <div class="notification-icon deadline">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-message">Monthly report deadline approaching in 2 days</div>
                                    <div class="notification-time">1 day ago</div>
                                </div>
                            </div>
                            <div class="notification-item" data-type="user">
                                <div class="notification-icon user">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-message">New user registration: Maria Santos</div>
                                    <div class="notification-time">2 days ago</div>
                                </div>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="#" class="view-all-notifications">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="user-profile-container">
                    <button class="user-profile-btn" id="userProfileBtn">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'admin') }}</div>
                        </div>
                        <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                    </button>
                    <div class="user-profile-dropdown" id="userProfileDropdown">
                        <div class="user-profile-header">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'admin') }}</div>
                            </div>
                        </div>
                        <div class="user-profile-menu">
                            <a href="{{ route('admin.profile') }}" class="user-profile-item">
                                <i class="fas fa-user"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="#" class="user-profile-item">
                                <i class="fas fa-cog"></i>
                                <span>Account Settings</span>
                            </a>
                            <a href="#" class="user-profile-item">
                                <i class="fas fa-bell"></i>
                                <span>Notification Settings</span>
                            </a>
                            <a href="#" class="user-profile-item">
                                <i class="fas fa-palette"></i>
                                <span>Appearance</span>
                            </a>
                            <div class="user-profile-divider"></div>
                            <a href="{{ route('admin.dashboard') }}" class="user-profile-item">
                                <i class="fas fa-chart-bar"></i>
                                <span>My Dashboard</span>
                            </a>
                            <a href="{{ route('admin.view.submissions') }}" class="user-profile-item">
                                <i class="fas fa-file-alt"></i>
                                <span>View Reports</span>
                            </a>
                            <div class="user-profile-divider"></div>
                            <a href="{{ route('logout') }}" class="user-profile-item logout"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Layout Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4>Admin Panel</h4>
                    <small>Control Center</small>
                </div>
                <nav class="sidebar-nav">
                    <div class="nav flex-column">
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
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2canvas for seamless page transitions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

    <!-- Header Dropdown Functionality -->
    <script>
        $(document).ready(function() {
            // Notification dropdown functionality
            $('#notificationBtn').on('click', function(e) {
                e.stopPropagation();
                const dropdown = $('#notificationDropdown');
                const userDropdown = $('#userProfileDropdown');

                // Close user dropdown if open
                userDropdown.removeClass('show');
                $('#userProfileBtn').removeClass('show');

                // Toggle notification dropdown
                dropdown.toggleClass('show');
                $(this).toggleClass('show');
            });

            // User profile dropdown functionality
            $('#userProfileBtn').on('click', function(e) {
                e.stopPropagation();
                const dropdown = $('#userProfileDropdown');
                const notificationDropdown = $('#notificationDropdown');

                // Close notification dropdown if open
                notificationDropdown.removeClass('show');
                $('#notificationBtn').removeClass('show');

                // Toggle user profile dropdown
                dropdown.toggleClass('show');
                $(this).toggleClass('show');
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.notification-container, .user-profile-container').length) {
                    $('.notification-dropdown, .user-profile-dropdown').removeClass('show');
                    $('.notification-btn, .user-profile-btn').removeClass('show');
                }
            });

            // Mark all notifications as read
            $('#markAllRead').on('click', function(e) {
                e.preventDefault();
                $('.notification-item.unread').removeClass('unread');
                updateNotificationBadge();
            });

            // Mark individual notification as read when clicked
            $('.notification-item').on('click', function() {
                $(this).removeClass('unread');
                updateNotificationBadge();
            });

            // Update notification badge count
            function updateNotificationBadge() {
                const unreadCount = $('.notification-item.unread').length;
                const badge = $('#notificationBadge');

                if (unreadCount > 0) {
                    badge.text(unreadCount).show();
                } else {
                    badge.hide();
                }
            }

            // Initialize notification badge
            updateNotificationBadge();

            // Header scroll effect
            $(window).on('scroll', function() {
                const header = $('.admin-header');
                if ($(window).scrollTop() > 50) {
                    header.addClass('scrolled');
                } else {
                    header.removeClass('scrolled');
                }
            });

            // Simple modal fix - just z-index like user management
            $(document).on('show.bs.modal', '.modal', function() {
                $(this).css('z-index', 1055);
                setTimeout(() => {
                    $('.modal-backdrop').css('z-index', 1040);
                }, 0);
            });

            // Add smooth animations
            $('.notification-dropdown, .user-profile-dropdown').on('show', function() {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(-10px)'
                }).animate({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                }, 200);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
