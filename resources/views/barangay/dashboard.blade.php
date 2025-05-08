@extends('layouts.barangay')

@section('title', 'Barangay Dashboard')

@section('content')
<div class="dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <h1 class="h3 mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-muted">Here's an overview of your reports and upcoming deadlines.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light">
                    <i class="fas fa-file-alt text-primary"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $totalReports }}</h3>
                    <p class="stat-label">Total Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success-light">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $approvedReports }}</h3>
                    <p class="stat-label">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning-light">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $pendingReports }}</h3>
                    <p class="stat-label">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger-light">
                    <i class="fas fa-times-circle text-danger"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $rejectedReports }}</h3>
                    <p class="stat-label">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitReportModal">
                <i class="fas fa-plus-circle me-2"></i>
                Submit New Report
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Recent Reports -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Reports</h5>
                    <a href="{{ route('barangay.submissions') }}" class="btn btn-link btn-sm p-0">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentReports->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No recent reports</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentReports as $report)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $report->reportType->name }}</h6>
                                            <small class="text-muted">Submitted: {{ $report->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
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
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Upcoming Deadlines</h5>
                    <a href="{{ route('barangay.overdue-reports') }}" class="btn btn-link btn-sm p-0">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingDeadlines->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No upcoming deadlines</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($upcomingDeadlines as $deadline)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $deadline->name }}</h6>
                                            <small class="text-muted">Deadline: {{ $deadline->deadline->format('M d, Y') }}</small>
                                        </div>
                                        <span class="badge bg-info">
                                            {{ ucfirst($deadline->frequency) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard {
    max-width: 1400px;
    margin: 0 auto;
}

.welcome-section {
    padding: 1rem 0;
}

.stat-card {
    background: white;
    border-radius: var(--radius-md);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

.stat-label {
    color: var(--gray-600);
    margin: 0;
    font-size: 0.875rem;
}

.card {
    border: none;
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

.card-title {
    color: var(--gray-800);
    font-weight: 600;
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
</style>

<!-- Submit Report Modal -->
<div class="modal fade" id="submitReportModal" tabindex="-1" aria-labelledby="submitReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitReportModalLabel">
                    <i class="fas fa-file-alt me-2"></i>
                    Submit New Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitReportForm" method="POST" action="{{ route('barangay.submissions.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_type" class="form-label">
                                    Report Type
                                </label>
                                <select id="report_type" class="form-select @error('report_type_id') is-invalid @enderror" name="report_type_id" required>
                                    <option value="">Select Report Type</option>
                                    @foreach($reportTypes as $reportType)
                                        <option value="{{ $reportType->id }}" data-frequency="{{ $reportType->frequency }}">
                                            {{ $reportType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('report_type_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file" class="form-label">
                                    Upload File
                                </label>
                                <div class="drop-zone" id="dropZone">
                                    <div class="drop-zone__prompt">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-primary"></i>
                                        <p class="mb-1">Drag and drop your file here or click to browse</p>
                                        <small class="text-muted">Accepted formats: PDF, DOC, DOCX, XLSX (Max: 2MB)</small>
                                    </div>
                                    <input type="file" name="file" id="file" class="drop-zone__input" accept=".pdf,.doc,.docx,.xlsx" required>
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Report Fields -->
                    <div id="weekly-fields" class="report-fields" style="display: none;">
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-4">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Report Period
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Month</label>
                                        <select class="form-select" name="month" required>
                                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Week Number</label>
                                        <input type="number" class="form-control" name="week_number" min="1" max="52" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-4">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Report Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Clean-up Sites</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" required>
                                            <span class="input-group-text">sites</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Participants</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_participants" min="0" required>
                                            <span class="input-group-text">people</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Barangays</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_barangays" min="0" required>
                                            <span class="input-group-text">barangays</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Total Volume</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="total_volume" min="0" step="0.01" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Report Fields -->
                    <div id="monthly-fields" class="report-fields" style="display: none;">
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-4">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Report Period
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Month</label>
                                    <select class="form-select" name="month" required>
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Report type change handler
    const reportTypeSelect = document.getElementById('report_type');
    const weeklyFields = document.getElementById('weekly-fields');
    const monthlyFields = document.getElementById('monthly-fields');

    reportTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const frequency = selectedOption.dataset.frequency;

        // Hide all fields first
        weeklyFields.style.display = 'none';
        monthlyFields.style.display = 'none';

        // Show relevant fields
        if (frequency === 'weekly') {
            weeklyFields.style.display = 'block';
        } else if (frequency === 'monthly') {
            monthlyFields.style.display = 'block';
        }
    });

    // Drop zone functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.querySelector('.drop-zone__input');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    ['dragleave', 'dragend'].forEach(type => {
        dropZone.addEventListener(type, (e) => {
            dropZone.classList.remove('dragover');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateThumbnail(dropZone, e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            updateThumbnail(dropZone, fileInput.files[0]);
        }
    });

    function updateThumbnail(dropZone, file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                dropZone.querySelector('.drop-zone__prompt').innerHTML = `
                    <img src="${reader.result}" alt="${file.name}" class="drop-zone__thumb">
                    <p class="mb-1">${file.name}</p>
                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                `;
            };
        } else {
            dropZone.querySelector('.drop-zone__prompt').innerHTML = `
                <i class="fas fa-file fa-2x mb-2 text-primary"></i>
                <p class="mb-1">${file.name}</p>
                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
            `;
        }
    }
});
</script>
@endpush
@endsection
