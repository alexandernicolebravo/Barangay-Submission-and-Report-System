<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>@yield('title', 'Barangay Portal')</title>

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
            /* Modern Color Palette */
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
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
            overflow-x: hidden;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Modern Sidebar Styles */
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
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(79, 70, 229, 0.05) 100%);
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

        /* Font Sizes */
        .fs-7 {
            font-size: 0.85rem !important;
        }

        /* Main Content Styles */
        .main-content {
            padding: 2rem;
            margin-left: 280px;
            margin-top: 72px; /* Account for header height */
            min-height: calc(100vh - 72px);
            transition: all 0.3s ease;
        }

        /* Modern Card Styles */
        .card {
            background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-normal);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: var(--transition-normal);
        }

        .card:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-2px);
            border-color: var(--primary-light);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
            position: relative;
        }

        .card-header h5 {
            color: var(--gray-900);
            font-weight: 600;
            margin: 0;
            font-size: 1.125rem;
        }

        .card-body {
            padding: 1.5rem;
            background: white;
        }

        /* Form Styles */
        .form-control, .form-select {
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            padding: 0.625rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: white;
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

        /* Modern Button Styles */
        .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            transition: var(--transition-normal);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            cursor: pointer;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition-normal);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            color: var(--gray-800);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
            width: 100%;
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
            white-space: nowrap;
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

        /* Header Styles */
        .barangay-header {
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

        .barangay-header.scrolled {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(20px);
        }

        /* Disable header effects when modal is open */
        body.modal-open .barangay-header {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background: rgba(255, 255, 255, 0.95) !important;
            z-index: 1020 !important;
        }

        /* Fix modal z-index and backdrop issues - Simplified */
        .modal {
            z-index: 1055 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            outline: 0;
        }

        .modal-backdrop {
            z-index: 1050 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
        }

        .modal.show {
            display: block !important;
        }

        .modal-dialog {
            margin: 1.75rem auto !important;
            max-width: 500px !important;
            position: relative !important;
            width: auto !important;
            pointer-events: none !important;
        }

        .modal-lg {
            max-width: 800px !important;
        }

        /* Disable all transitions that cause conflicts */
        .modal.fade .modal-dialog {
            transition: none !important;
            transform: none !important;
        }

        .modal.show .modal-dialog {
            transform: none !important;
        }

        /* Fix any overlay conflicts */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        /* Ensure modals appear above everything - Simplified */
        .modal-content {
            position: relative !important;
            z-index: 1056 !important;
            background-color: #fff !important;
            border: 1px solid rgba(0, 0, 0, 0.2) !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            pointer-events: auto !important;
            outline: 0 !important;
        }

        /* Disable hover effects on modal elements */
        .modal * {
            pointer-events: auto !important;
        }

        /* Prevent any card hover effects when modal is open */
        body.modal-open .card:hover {
            transform: none !important;
            box-shadow: var(--shadow-sm) !important;
        }

        /* File preview styling */
        .file-preview-container {
            min-height: 200px;
            max-height: 600px;
            overflow: auto;
        }

        .file-preview-container iframe {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .file-preview-container img {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .pdf-preview, .image-preview {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
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
            display: flex !important;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            z-index: 1000;
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
            display: none; /* Hidden by default, shown when there are notifications */
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
            z-index: 1001;
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

        .notification-loading,
        .notification-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .notification-loading i {
            margin-right: 8px;
        }

        .notification-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.875rem;
            margin-bottom: 4px;
        }

        .notification-message {
            color: #4b5563;
            font-size: 0.8rem;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-time {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        .notification-badge {
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 500;
        }

        .notification-item.unread .notification-title {
            color: #0ea5e9;
        }

        .notification-item.unread .notification-message {
            color: #0f172a;
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

        .notification-icon.announcement {
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

        /* Drop Zone Styles */
        .drop-zone {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .drop-zone:hover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }

        .drop-zone.dragover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }

        .drop-zone__thumb {
            padding: 1rem;
            background: white;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
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

        .alert-warning {
            background: var(--warning-light);
            color: var(--warning);
        }

        .alert-info {
            background: var(--info-light);
            color: var(--info);
        }

        /* Page Title */
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

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Responsive Design */
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
            .barangay-header {
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

        /* Loading Spinner */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-200);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Barangay Header -->
    <header class="barangay-header">
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
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here via JavaScript -->
                            <div class="notification-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading notifications...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="user-profile-container">
                    <button class="user-profile-btn" id="userProfileBtn">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'B', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'Barangay User' }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'barangay') }}</div>
                        </div>
                        <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                    </button>
                    <div class="user-profile-dropdown" id="userProfileDropdown">
                        <div class="user-profile-header">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'B', 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name ?? 'Barangay User' }}</div>
                                <div class="user-role">{{ ucfirst(Auth::user()->user_type ?? 'barangay') }}</div>
                            </div>
                        </div>
                        <div class="user-profile-menu">
                            <a href="{{ route('barangay.profile') }}">
                                <i class="fas fa-user"></i>
                                Profile Settings
                            </a>
                            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                        <h4>Barangay Portal</h4>
                        <small>{{ auth()->user()->name }}</small>
                    </div>
                    <nav class="d-flex flex-column h-100">
                        <!-- Navigation Links Section -->
                        <div class="navigation-links">
                            <a href="{{ route('barangay.dashboard') }}" class="nav-link {{ request()->routeIs('barangay.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('barangay.submissions') }}" class="nav-link {{ request()->routeIs('barangay.submissions') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>
                                My Submissions
                            </a>
                            <a href="{{ route('barangay.overdue-reports') }}" class="nav-link {{ request()->routeIs('barangay.overdue-reports') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-circle"></i>
                                Overdue Reports
                            </a>
                            <a href="{{ route('barangay.issuances.index') }}" class="nav-link {{ request()->routeIs('barangay.issuances.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>
                                Issuances
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
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX Form Handling -->
    <script src="{{ asset('js/ajax-forms.js') }}"></script>

    <!-- Vite Assets for Real-time Notifications -->
    {{-- @vite(['resources/js/app.js']) --}}
    <!-- Note: Vite disabled for now. Using polling-based notifications instead. -->

    <!-- Header JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notification dropdown functionality
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationList = document.getElementById('notificationList');

            // User profile dropdown functionality
            const userProfileBtn = document.getElementById('userProfileBtn');
            const userProfileDropdown = document.getElementById('userProfileDropdown');

            // Toggle notification dropdown
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const wasHidden = !notificationDropdown.classList.contains('show');
                notificationDropdown.classList.toggle('show');
                userProfileDropdown.classList.remove('show');

                if (wasHidden && notificationDropdown.classList.contains('show')) {
                    // If dropdown was just opened, check for unread notifications
                    const unreadItems = notificationList.querySelectorAll('.notification-item.unread');
                    if (unreadItems.length > 0) {
                        markAllNotificationsAsRead();
                    }
                }
            });

            // Toggle user profile dropdown
            userProfileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userProfileDropdown.classList.toggle('show');
                notificationDropdown.classList.remove('show');
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                notificationDropdown.classList.remove('show');
                userProfileDropdown.classList.remove('show');
            });

            // Prevent dropdown from closing when clicking inside
            notificationDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            userProfileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Load notifications when page loads (Manual Refresh System)
            loadNotifications();

            // Refresh notifications every 30 seconds for manual refresh system
            setInterval(loadNotifications, 30000);

            // Load notifications from server
            function loadNotifications() {
                console.log('Loading notifications from:', "{{ route('barangay.notifications.get') }}"); // Debug log

                fetch("{{ route('barangay.notifications.get') }}")
                    .then(response => {
                        console.log('Response received:', response); // Debug log

                        if (!response.ok) {
                            // Log the response text even for non-ok responses before throwing error
                            response.text().then(text => {
                                console.error('Error loading notifications: Network response was not ok.', {
                                    status: response.status,
                                    statusText: response.statusText,
                                    responseBody: text
                                });
                            }).catch(err => {
                                console.error('Error reading response text for non-ok response:', err);
                            });
                            throw new Error('Network response was not ok: ' + response.status + ' ' + response.statusText);
                        }
                        // Check content type before trying to parse as JSON
                        const contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            // If not JSON, log the response and throw an error
                            return response.text().then(text => {
                                console.error('Error loading notifications: Expected JSON, got ' + contentType, text);
                                throw new TypeError("Expected JSON, got " + contentType + ". Response body: " + text.substring(0, 200) + "...");
                            });
                        }
                    })
                    .then(data => {
                        console.log('Notification data received:', data); // Debug log
                        updateNotificationUI(data.notifications, data.unread_count);
                    })
                    .catch(error => {
                        console.error('Error loading notifications (catch block):', error);
                        if (notificationList) {
                            notificationList.innerHTML = '<li class="notification-item">Failed to load notifications. Check console.</li>';
                        }
                        if (notificationBadge) {
                            notificationBadge.style.display = 'none';
                        }
                    });
            }

            // Update notification UI with fetched data
            function updateNotificationUI(notifications, unreadCount) {
                displayNotifications(notifications);
                updateNotificationBadge(unreadCount);
            }

            // Display notifications in the dropdown
            function displayNotifications(notifications) {
                const notificationList = document.getElementById('notificationList');

                console.log('Displaying notifications:', notifications); // Debug log

                if (!notifications || notifications.length === 0) {
                    notificationList.innerHTML = '<div class="notification-empty">No notifications</div>';
                    return;
                }

                let html = '';
                notifications.forEach(notification => {
                    console.log('Processing notification:', notification); // Debug log

                    const iconClass = getNotificationIcon(notification.type);
                    const unreadClass = notification.read_at ? '' : 'unread';

                    // Handle different data structures
                    const title = notification.title || notification.data?.title || 'Notification';
                    const message = notification.message || notification.data?.message || 'You have a new notification';
                    const time = notification.time || notification.created_at || 'Recently';
                    const redirectUrl = notification.redirect_url || notification.data?.redirect_url || '#';
                    const canUpdate = notification.data?.can_update || false;

                    html += `
                        <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-type="${notification.type}" data-redirect-url="${redirectUrl}">
                            <div class="notification-icon ${notification.type}">
                                <i class="fas ${iconClass}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${title}</div>
                                <div class="notification-message">${message}</div>
                                <div class="notification-meta">
                                    <span class="notification-time">${time}</span>
                                    ${canUpdate ? '<span class="notification-badge">Action Required</span>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                notificationList.innerHTML = html;

                // Add click event listeners to notification items
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const notificationId = this.getAttribute('data-id');
                        const redirectUrl = this.getAttribute('data-redirect-url');

                        if (this.classList.contains('unread')) {
                            markNotificationAsRead(notificationId, this);
                        }

                        // Redirect to the notification's target page
                        if (redirectUrl && redirectUrl !== '#') {
                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 100); // Small delay to allow the read status to update
                        }
                    });
                });
            }

            // Get icon class based on notification type
            function getNotificationIcon(type) {
                switch(type) {
                    case 'report':
                        return 'fa-file-alt';
                    case 'new_report_type':
                        return 'fa-plus-circle';
                    case 'new_submission':
                        return 'fa-upload';
                    case 'announcement':
                        return 'fa-bullhorn';
                    case 'user':
                        return 'fa-user';
                    default:
                        return 'fa-bell';
                }
            }

            // Mark individual notification as read
            function markNotificationAsRead(notificationId, element) {
                fetch(`{{ route("barangay.notifications.read", ":id") }}`.replace(':id', notificationId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        element.classList.remove('unread');
                        updateNotificationBadgeCount();
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            }

            // Mark all notifications as read
            function markAllNotificationsAsRead() {
                fetch('{{ route("barangay.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                        });
                        updateNotificationBadge(0);
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
            }

            // Update notification badge count
            function updateNotificationBadge(count = null) {
                if (count === null) {
                    count = document.querySelectorAll('.notification-item.unread').length;
                }

                if (count > 0) {
                    notificationBadge.textContent = count;
                    notificationBadge.style.display = 'flex';
                } else {
                    notificationBadge.style.display = 'none';
                }
            }

            // Update notification badge count from DOM
            function updateNotificationBadgeCount() {
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                updateNotificationBadge(unreadCount);
            }

            // Make fetchUnreadCount globally available for app.js
            window.fetchUnreadCount = function() {
                fetch("{{ route('barangay.notifications.unread-count') }}")
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationBadge(data.unread_count);
                    })
                    .catch(error => {
                        console.error('Error fetching unread count:', error);
                    });
            };

            // Add click event listener for dynamically added notifications
            function addNotificationClickHandler(item) {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    const redirectUrl = this.getAttribute('data-redirect-url');

                    if (this.classList.contains('unread')) {
                        markNotificationAsRead(notificationId, this);
                    }

                    // Redirect to the notification's target page
                    if (redirectUrl && redirectUrl !== '#') {
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 100); // Small delay to allow the read status to update
                    }
                });
            }

            // Enhanced function to add new notifications from real-time updates
            window.addNewNotificationToBarangayUI = function(notification) {
                const notificationList = document.getElementById('notificationList');
                if (!notificationList) return;

                // Remove "No notifications" message if present
                const noNotificationsMsg = notificationList.querySelector('.notification-empty');
                if (noNotificationsMsg) {
                    notificationList.innerHTML = '';
                }

                const iconClass = getNotificationIcon(notification.notification_type || 'general');
                const unreadClass = 'unread';

                const html = `
                    <div class="notification-item ${unreadClass}" data-id="${notification.id || 'temp-' + Date.now()}" data-type="${notification.notification_type || 'general'}" data-redirect-url="${notification.redirect_url || '#'}">
                        <div class="notification-icon ${notification.notification_type || 'system'}">
                            <i class="fas ${iconClass}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notification.full_report_title || notification.title || 'New Notification'}</div>
                            <div class="notification-message">${notification.message || 'You have a new notification'}</div>
                            <div class="notification-meta">
                                <span class="notification-time">Just now</span>
                                ${notification.can_update ? '<span class="notification-badge">Action Required</span>' : ''}
                            </div>
                        </div>
                    </div>
                `;

                notificationList.insertAdjacentHTML('afterbegin', html);

                // Add click handler to the new notification
                const newItem = notificationList.firstElementChild;
                addNotificationClickHandler(newItem);

                // Update badge count
                if (typeof window.fetchUnreadCount === 'function') {
                    window.fetchUnreadCount();
                }
            };

            // Header scroll effect
            window.addEventListener('scroll', function() {
                const header = document.querySelector('.barangay-header');
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
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

    @stack('scripts')
</body>
</html>
