@extends('layouts.barangay')

@section('title', 'Barangay Dashboard')

@section('content')
<div class="dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <h1 class="h3 mb-2 text-primary">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-secondary">Here's an overview of your reports and upcoming deadlines.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt text-info"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalReports }}</h3>
                    <p class="stat-label">Total Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $submittedReports }}</h3>
                    <p class="stat-label">Submitted Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $noSubmissionReports }}</h3>
                    <p class="stat-label">No Submission Reports</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Main Content -->
    <div class="row g-4">
        <!-- Recent Reports -->
        <div class="col-md-6">
            <div class="card h-100 recent-reports-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history me-2 text-primary"></i>
                        <h5 class="card-title mb-0">Recent Reports</h5>
                    </div>
                    <a href="{{ route('barangay.submissions') }}" class="btn-view-all">
                        View All
                        <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentReports->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <p>No recent reports</p>
                            <small class="text-muted">Your submitted reports will appear here</small>
                        </div>
                    @else
                        <div class="report-list">
                            @foreach($recentReports as $report)
                                <div class="report-item">
                                    <div class="report-icon">
                                        @php
                                            $iconClass = match($report->reportType->frequency) {
                                                'weekly' => 'fa-calendar-week text-info',
                                                'monthly' => 'fa-calendar-alt text-primary',
                                                'quarterly' => 'fa-calendar-check text-success',
                                                'semestral' => 'fa-calendar-plus text-warning',
                                                'annual' => 'fa-calendar text-danger',
                                                default => 'fa-file-alt text-secondary'
                                            };
                                        @endphp
                                        <i class="fas {{ $iconClass }}"></i>
                                    </div>
                                    <div class="report-content">
                                        <h6 class="report-title">{{ $report->reportType->name }}</h6>
                                        <div class="report-meta">
                                            <span class="report-date">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $report->created_at->format('M d, Y') }}
                                            </span>
                                            @php
                                                $displayStatus = $report->display_status ?? 'submitted';
                                            @endphp

                                            @if($displayStatus === 'resubmit')
                                                <span class="report-status status-resubmit">
                                                    <i class="fas fa-sync-alt me-1"></i>
                                                    Resubmit
                                                    @if($report->submission_count > 1)
                                                        <span class="badge bg-warning ms-1">{{ $report->submission_count }}</span>
                                                    @endif
                                                </span>
                                            @elseif($displayStatus === 'resubmitted')
                                                <span class="report-status status-resubmitted">
                                                    <i class="fas fa-check-double me-1"></i>
                                                    Resubmitted
                                                    @if($report->submission_count > 1)
                                                        <span class="badge bg-info ms-1">{{ $report->submission_count }}</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="report-status status-submitted">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Submitted
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="col-md-6">
            <div class="card h-100 upcoming-deadlines-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <h5 class="card-title mb-0">Upcoming Deadlines</h5>
                    </div>
                    <a href="{{ route('barangay.overdue-reports') }}" class="btn-view-all">
                        View All
                        <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingDeadlines->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <p>No upcoming deadlines</p>
                            <span class="text-muted mt-1 small">You're all caught up!</span>
                        </div>
                    @else
                        <div class="deadline-list-container">
                            <div class="deadline-list">
                                @foreach($upcomingDeadlines as $deadline)
                                    <div class="deadline-item">
                                        <div class="deadline-info">
                                            <div class="deadline-icon">
                                                @php
                                                    $iconClass = match($deadline->frequency) {
                                                        'weekly' => 'fa-calendar-week text-info',
                                                        'monthly' => 'fa-calendar-alt text-primary',
                                                        'quarterly' => 'fa-calendar-check text-success',
                                                        'semestral' => 'fa-calendar-plus text-warning',
                                                        'annual' => 'fa-calendar text-danger',
                                                        default => 'fa-calendar-day text-secondary'
                                                    };

                                                    // Check if this report type has been submitted
                                                    $isSubmitted = $submittedReportTypeIds->contains($deadline->id);
                                                @endphp
                                                <i class="fas {{ $iconClass }}"></i>
                                            </div>
                                            <div class="deadline-content">
                                                <h6 class="deadline-title">{{ $deadline->name }}</h6>
                                                <div class="deadline-meta">
                                                    <span class="deadline-date">
                                                        <i class="far fa-clock me-1"></i>
                                                        Due: {{ $deadline->deadline->format('M d, Y') }}
                                                    </span>
                                                    <span class="deadline-frequency frequency-{{ $deadline->frequency }}">
                                                        {{ ucfirst($deadline->frequency) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="deadline-actions">
                                            <button type="button"
                                                    class="btn btn-submit btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#submitModal{{ $deadline->id }}"
                                                    title="Submit {{ $deadline->name }}">
                                                <i class="fas fa-upload me-1"></i>
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($upcomingDeadlines) > 5)
                                <div class="scroll-indicator">
                                    <i class="fas fa-chevron-down"></i>
                                    <span>Scroll for more</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Modals for Upcoming Deadlines -->
@foreach($upcomingDeadlines as $deadline)
    <div class="modal fade" id="submitModal{{ $deadline->id }}" tabindex="-1" aria-labelledby="submitModalLabel{{ $deadline->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitModalLabel{{ $deadline->id }}">
                        Submit {{ $deadline->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('barangay.submissions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="report_type_id" value="{{ $deadline->id }}">
                    <input type="hidden" name="report_type" value="{{ $deadline->frequency }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file{{ $deadline->id }}" class="form-label">Upload Report File</label>
                            <input type="file"
                                   class="form-control"
                                   id="file{{ $deadline->id }}"
                                   name="file"
                                   accept=".pdf,.docx,.xlsx"
                                   required>
                            <div class="form-text">Accepted formats: PDF, DOCX, XLSX (Max: 2MB)</div>
                        </div>

                        @if($deadline->frequency === 'weekly')
                            <div class="mb-3">
                                <label class="form-label">Number of Clean-up Sites</label>
                                <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Number of Participants</label>
                                <input type="number" class="form-control" name="num_of_participants" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Number of Barangays</label>
                                <input type="number" class="form-control" name="num_of_barangays" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Volume</label>
                                <input type="number" class="form-control" name="total_volume" min="0" step="0.01" required>
                            </div>
                        @elseif($deadline->frequency === 'monthly')
                            <div class="mb-3">
                                <label class="form-label">Month</label>
                                <select class="form-select" name="month" required>
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($deadline->frequency === 'quarterly')
                            <div class="mb-3">
                                <label class="form-label">Quarter</label>
                                <select class="form-select" name="quarter_number" required>
                                    <option value="1">First Quarter</option>
                                    <option value="2">Second Quarter</option>
                                    <option value="3">Third Quarter</option>
                                    <option value="4">Fourth Quarter</option>
                                </select>
                            </div>
                        @elseif($deadline->frequency === 'semestral')
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <select class="form-select" name="sem_number" required>
                                    <option value="1">First Semester</option>
                                    <option value="2">Second Semester</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<style>
/* Modern Barangay Dashboard Styles */
:root {
    --primary-rgb: 79, 70, 229;
    --success-rgb: 16, 185, 129;
    --warning-rgb: 245, 158, 11;
    --danger-rgb: 239, 68, 68;
    --info-rgb: 6, 182, 212;
    --secondary-rgb: 100, 116, 139;
    --gray-rgb: 148, 163, 184;
    --primary-dark: #3730a3;
}

.dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: transparent;
}

.welcome-section {
    padding: 1.5rem 0;
    margin-bottom: 2rem;
}

.welcome-section h1 {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 2.25rem;
    margin-bottom: 0.5rem;
}

.welcome-section p {
    color: #64748b;
    font-size: 1.125rem;
    margin: 0;
}

/* Modern Stat Cards */
.stat-card {
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #c4b5fd;
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    opacity: 0.1;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon::before {
    opacity: 0.2;
}

.stat-icon i {
    z-index: 1;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon i {
    transform: scale(1.1);
}

.stat-icon.text-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

.stat-icon.text-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-icon.text-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-value {
    transform: scale(1.05);
}

.stat-label {
    color: #64748b;
    margin: 0;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Modern Cards */
.card {
    background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
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
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #c4b5fd;
}

.card:hover::before {
    opacity: 1;
}

.card-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.5rem;
}

.card-title {
    color: #0f172a;
    font-weight: 600;
    font-size: 1.125rem;
}

.card-title i {
    color: #4f46e5;
    margin-right: 0.5rem;
}

.btn-view-all {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-view-all:hover {
    color: #3730a3;
    transform: translateX(2px);
}

.list-group-item {
    border: none;
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background: var(--gray-100);
}

.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    font-size: 0.75rem;
}

.btn-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.btn-link:hover {
    color: var(--primary);
    text-decoration: underline;
}

.bg-primary-light {
    background: var(--primary-light);
}

.bg-success-light {
    background: var(--success-light);
}

.bg-warning-light {
    background: var(--warning-light);
}

.bg-danger-light {
    background: var(--danger-light);
}

/* Frequency Badge Colors */
.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #fff;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
    color: #fff;
}

.badge.bg-success {
    background-color: #198754 !important;
    color: #fff;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

/* Drop Zone Styles */
.drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.drop-zone.compact {
    padding: 1rem;
}

.drop-zone:hover {
    border-color: #0d6efd;
    background-color: #f1f3f5;
}

.drop-zone.dragover {
    border-color: #0d6efd;
    background-color: #e7f5ff;
}

.drop-zone__input {
    display: none;
}

.drop-zone__thumb {
    width: 100%;
    height: 100%;
    border-radius: 0.25rem;
    overflow: hidden;
    background-color: #cccccc;
    background-size: cover;
    position: relative;
}

.drop-zone__thumb::after {
    content: attr(data-label);
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 0.5rem 0;
    color: #ffffff;
    background: rgba(0, 0, 0, 0.75);
    font-size: 0.875rem;
    text-align: center;
}

.drop-zone__prompt {
    color: #6c757d;
}

.drop-zone__prompt i {
    color: #0d6efd;
}

/* Modern Recent Reports Styles */
.recent-reports-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.recent-reports-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.recent-reports-card .card-header {
    padding: 1.25rem 1.5rem;
    background: white;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.btn-view-all {
    color: var(--primary);
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

.btn-view-all:hover {
    color: var(--primary);
    transform: translateX(2px);
}

.btn-view-all i {
    font-size: 0.75rem;
    transition: transform 0.2s ease;
}

.btn-view-all:hover i {
    transform: translateX(2px);
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    text-align: center;
}

.empty-state-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(var(--primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: var(--primary);
    font-size: 1.5rem;
}

.empty-state p {
    color: var(--gray-600);
    margin-bottom: 0;
}

.report-list {
    padding: 0.5rem 0;
}

.report-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.report-item:last-child {
    border-bottom: none;
}

.report-item:hover {
    background-color: rgba(var(--primary-rgb), 0.02);
}

.report-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(var(--primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.25rem;
}

.report-content {
    flex: 1;
}

.report-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--gray-800);
}

.report-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8125rem;
}

.report-date {
    color: var(--gray-600);
    display: flex;
    align-items: center;
}

.report-status {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.75rem;
}

.report-status.status-submitted {
    background-color: rgba(var(--success-rgb), 0.1);
    color: var(--success);
}

.report-status.status-resubmit {
    background-color: rgba(var(--warning-rgb), 0.15);
    color: var(--warning);
    animation: pulse-resubmit 2s infinite;
    position: relative;
}

.report-status.status-resubmitted {
    background-color: rgba(var(--info-rgb), 0.15);
    color: var(--info);
    position: relative;
}

/* Badge styling for submission counts */
.report-status .badge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
    border-radius: 50%;
    min-width: 1.5rem;
    height: 1.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

@keyframes pulse-resubmit {
    0% {
        box-shadow: 0 0 0 0 rgba(var(--warning-rgb), 0.4);
        transform: scale(1);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(var(--warning-rgb), 0);
        transform: scale(1.02);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(var(--warning-rgb), 0);
        transform: scale(1);
    }
}

.report-status.status-no-submission {
    background-color: rgba(var(--danger-rgb), 0.1);
    color: var(--danger);
}

.report-status.status-pending {
    background-color: rgba(var(--warning-rgb), 0.1);
    color: var(--warning);
}

/* Upcoming Deadlines Styles */
.upcoming-deadlines-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.upcoming-deadlines-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.upcoming-deadlines-card .card-header {
    padding: 1.25rem 1.5rem;
    background: white;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.deadline-list {
    padding: 0.5rem 0;
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.deadline-list::-webkit-scrollbar {
    width: 6px;
}

.deadline-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.deadline-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.deadline-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.deadline-list-container {
    position: relative;
}

.scroll-indicator {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0.9) 50%, rgba(255,255,255,1) 100%);
    padding: 30px 0 10px;
    text-align: center;
    font-size: 0.8rem;
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    pointer-events: none;
    animation: pulse 2s infinite;
}

.scroll-indicator i {
    margin-bottom: 5px;
}

@keyframes pulse {
    0% {
        opacity: 0.7;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0.7;
    }
}

.deadline-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.deadline-item:last-child {
    border-bottom: none;
}

.deadline-item:hover {
    background-color: rgba(var(--primary-rgb), 0.02);
}

.deadline-actions {
    margin-left: 1rem;
}

.deadline-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.deadline-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(var(--primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.25rem;
}

.deadline-content {
    flex: 1;
}

.deadline-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--gray-800);
}

.deadline-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8125rem;
}

.deadline-date {
    color: var(--gray-600);
    display: flex;
    align-items: center;
}

.deadline-frequency {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.75rem;
}

.frequency-weekly {
    background-color: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
}

.frequency-monthly {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
}

.frequency-quarterly {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.frequency-semestral {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.frequency-annual {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.btn-submit {
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(var(--primary-rgb), 0.2);
}

.btn-submit:hover {
    background-color: var(--primary-dark, #0b5ed7);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(var(--primary-rgb), 0.3);
}

.btn-submit:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(var(--primary-rgb), 0.2);
}
</style>





@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle scroll indicator for upcoming deadlines
    const deadlineList = document.querySelector('.deadline-list');
    const scrollIndicator = document.querySelector('.scroll-indicator');

    if (deadlineList && scrollIndicator) {
        deadlineList.addEventListener('scroll', function() {
            // If user has scrolled down, hide the indicator
            if (deadlineList.scrollTop > 50) {
                scrollIndicator.style.opacity = '0';
                scrollIndicator.style.transition = 'opacity 0.5s ease';
            } else {
                scrollIndicator.style.opacity = '1';
            }
        });
    }






});
</script>
@endpush
@endsection
