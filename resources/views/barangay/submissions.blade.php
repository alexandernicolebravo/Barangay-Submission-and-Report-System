@extends('layouts.barangay')

@section('title', 'My Submissions')
@section('page-title', 'My Submissions')

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-lg me-2"></i>
            <strong>{{ session('success') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-file-alt text-primary me-2"></i>Submitted Reports
                        </h5>
                        @if(!$reports->isEmpty() && (!empty($search) || !empty($frequency) || ($sortBy ?? 'newest') != 'newest'))
                            <a href="{{ route('barangay.submissions') }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>

                    @if(!$reports->isEmpty())
                    <form id="filterForm" class="d-flex flex-wrap gap-2" method="GET" action="{{ route('barangay.submissions') }}">
                        <div class="input-group" style="width: 200px;">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control search-box" name="search" id="searchInput" value="{{ $search ?? '' }}" placeholder="Search...">
                        </div>

                        <div class="input-group" style="width: 200px;">
                            <span class="input-group-text">
                                <i class="fas fa-filter"></i>
                            </span>
                            <select class="form-select" name="frequency" id="frequencyFilter">
                                <option value="">All Frequencies</option>
                                <option value="weekly" {{ ($frequency ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ ($frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ ($frequency ?? '') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="semestral" {{ ($frequency ?? '') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                <option value="annual" {{ ($frequency ?? '') == 'annual' ? 'selected' : '' }}>Annual</option>
                            </select>
                        </div>

                        <div class="input-group" style="width: 200px;">
                            <span class="input-group-text">
                                <i class="fas fa-sort"></i>
                            </span>
                            <select class="form-select" name="sort_by" id="sortBy">
                                <option value="newest" {{ ($sortBy ?? 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="type" {{ ($sortBy ?? '') == 'type' ? 'selected' : '' }}>Report Type</option>
                            </select>
                        </div>

                        @if(!empty($search) || !empty($frequency) || ($sortBy ?? 'newest') != 'newest')
                            <a href="{{ route('barangay.submissions') }}" class="btn btn-light">
                                <i class="fas fa-times"></i>
                                Clear Filters
                            </a>
                        @endif

                        <!-- Hidden submit button for browsers that don't support auto-submit -->
                        <button type="submit" class="d-none">Filter</button>
                    </form>
                    @endif
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($reports->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No reports have been submitted yet</h5>
                            <a href="{{ route('barangay.submit-report') }}" class="btn btn-primary mt-3" data-save-scroll>
                                <i class="fas fa-plus me-2"></i>Submit New Report
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Report</th>
                                        <th>Frequency</th>
                                        <th>Submitted</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2" style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                                        @php
                                                            $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                                            $icon = match($extension) {
                                                                'pdf' => 'fa-file-pdf',
                                                                'doc', 'docx' => 'fa-file-word',
                                                                'xls', 'xlsx' => 'fa-file-excel',
                                                                'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                                'txt' => 'fa-file-alt',
                                                                default => 'fa-file'
                                                            };
                                                        @endphp
                                                        <i class="fas {{ $icon }} fa-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 500; color: var(--dark);">{{ $report->reportType->name }}</div>
                                                        <small class="text-muted">{{ ucfirst(str_replace('Report', '', class_basename($report->model_type))) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $frequencyClass = 'info'; // Default for weekly

                                                    if ($report->reportType->frequency === 'monthly') {
                                                        $frequencyClass = 'primary';
                                                    } elseif ($report->reportType->frequency === 'quarterly') {
                                                        $frequencyClass = 'success';
                                                    } elseif ($report->reportType->frequency === 'semestral') {
                                                        $frequencyClass = 'warning';
                                                    } elseif ($report->reportType->frequency === 'annual') {
                                                        $frequencyClass = 'danger';
                                                    }
                                                @endphp
                                                <span class="status-badge frequency-{{ $report->reportType->frequency }}">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ ucfirst($report->reportType->frequency) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-nowrap">{{ $report->created_at->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                                    @if($report->updated_at && $report->updated_at->ne($report->created_at))
                                                    <small class="status-success mt-1">
                                                        <i class="fas fa-sync-alt me-1"></i> Updated
                                                    </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'warning'; // Default for pending
                                                    $iconClass = 'fa-clock';

                                                    if ($report->status === 'approved') {
                                                        $statusClass = 'primary';
                                                        $iconClass = 'fa-check-circle';
                                                    } elseif ($report->status === 'rejected') {
                                                        $statusClass = 'danger';
                                                        $iconClass = 'fa-times-circle';
                                                    } elseif ($report->status === 'submitted') {
                                                        $statusClass = 'success'; // Green for submitted
                                                        $iconClass = 'fa-check';
                                                    }
                                                @endphp
                                                <span class="status-badge {{ $report->status }}">
                                                    <i class="fas {{ $iconClass }}"></i>
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($report->remarks)
                                                    <button type="button"
                                                            class="btn btn-link btn-sm p-0 text-decoration-none"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="{{ $report->remarks }}">
                                                        <i class="fas fa-comment-alt text-primary"></i>
                                                        <small>View</small>
                                                    </button>
                                                @else
                                                    <small class="text-muted">None</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-1">
                                                    @php
                                                        // Store the report ID in a variable for consistency
                                                        // Use the unique_id if available, otherwise fall back to regular id
                                                        $reportId = $report->unique_id ?? $report->id;
                                                    @endphp
                                                    <button type="button"
                                                            class="btn btn-sm"
                                                            style="background: var(--primary-light); color: var(--primary); border: none;"
                                                            onclick="previewFile('{{ route('barangay.direct.files.download', $reportId) }}', '{{ basename($report->file_path) }}')"
                                                            data-bs-toggle="tooltip"
                                                            title="View/Download">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm"
                                                            style="background: {{ $report->status === 'rejected' ? 'var(--warning-light)' : 'var(--info-light)' }};
                                                                   color: {{ $report->status === 'rejected' ? 'var(--warning)' : 'var(--info)' }};
                                                                   border: none;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#resubmitModal{{ $reportId }}"
                                                            title="{{ $report->status === 'rejected' ? 'Resubmit (Required)' : 'Update Report' }}"
                                                            {{ $report->status === 'approved' ? 'disabled' : '' }}>
                                                        <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }} me-1"></i>
                                                        {{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resubmit Modals -->
    @foreach ($reports as $report)
        @php
            // Store the report ID in a variable for consistency
            $reportId = $report->unique_id ?? $report->id;
        @endphp
        <div class="modal fade" id="resubmitModal{{ $reportId }}" tabindex="-1" aria-labelledby="resubmitModalLabel{{ $reportId }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resubmitModalLabel{{ $reportId }}">
                            {{ $report->status === 'rejected' ? 'Resubmit Report' : 'Update Report' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('barangay.resubmit-report', $reportId) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3 p-2 rounded" style="background-color: rgba(var(--primary-rgb), 0.1);">
                                        <i class="fas fa-file-alt fa-lg text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $report->reportType->name }}</h5>
                                        <div class="text-muted">{{ ucfirst($report->reportType->frequency) }} Report</div>
                                    </div>
                                </div>

                                @if($report->remarks)
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading mb-1">Admin Remarks</h6>
                                            <p class="mb-0">{{ $report->remarks }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-4">
                                    <h6 class="mb-3">Current Submission</h6>
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        @php
                                            $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                            $icon = match($extension) {
                                                'pdf' => 'fa-file-pdf',
                                                'doc', 'docx' => 'fa-file-word',
                                                'xls', 'xlsx' => 'fa-file-excel',
                                                'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                'txt' => 'fa-file-alt',
                                                default => 'fa-file'
                                            };
                                            $colorClass = match($extension) {
                                                'pdf' => 'danger',
                                                'doc', 'docx' => 'primary',
                                                'xls', 'xlsx' => 'success',
                                                'jpg', 'jpeg', 'png', 'gif' => 'info',
                                                'txt' => 'secondary',
                                                default => 'primary'
                                            };
                                        @endphp
                                        <div class="me-3 p-2 rounded" style="background-color: rgba(var(--{{ $colorClass }}-rgb), 0.1);">
                                            <i class="fas {{ $icon }} fa-lg text-{{ $colorClass }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ basename($report->file_path) }}</div>
                                            <div class="text-muted small">Submitted on {{ $report->created_at->format('M d, Y h:i A') }}</div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="previewFile('{{ route('barangay.direct.files.download', $reportId) }}', '{{ basename($report->file_path) }}')">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden fields for report type information -->
                                <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">
                                <input type="hidden" name="report_type" value="{{ strtolower(str_replace('App\\Models\\', '', $report->model_type)) }}">

                                <div class="mb-3">
                                    <label for="file{{ $reportId }}" class="form-label">Upload New File</label>
                                    <input type="file" class="form-control" id="file{{ $reportId }}" name="file">
                                    <div class="form-text">Accepted file types: PDF, Word, Excel, Images</div>
                                </div>

                                @if($report->model_type === 'App\\Models\\WeeklyReport')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="month{{ $reportId }}" class="form-label">Month</label>
                                        <select class="form-select" id="month{{ $reportId }}" name="month" required>
                                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $loop->iteration }}" {{ $report->month == $loop->iteration ? 'selected' : '' }}>{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="week_number{{ $reportId }}" class="form-label">Week Number</label>
                                        <select class="form-select" id="week_number{{ $reportId }}" name="week_number" required>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ $report->week_number == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_of_clean_up_sites{{ $reportId }}" class="form-label">Number of Clean-up Sites</label>
                                        <input type="number" class="form-control" id="num_of_clean_up_sites{{ $reportId }}" name="num_of_clean_up_sites" value="{{ $report->num_of_clean_up_sites }}" required min="0">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_of_participants{{ $reportId }}" class="form-label">Number of Participants</label>
                                        <input type="number" class="form-control" id="num_of_participants{{ $reportId }}" name="num_of_participants" value="{{ $report->num_of_participants }}" required min="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_of_barangays{{ $reportId }}" class="form-label">Number of Barangays</label>
                                        <input type="number" class="form-control" id="num_of_barangays{{ $reportId }}" name="num_of_barangays" value="{{ $report->num_of_barangays }}" required min="0">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="total_volume{{ $reportId }}" class="form-label">Total Volume (kg)</label>
                                        <input type="number" class="form-control" id="total_volume{{ $reportId }}" name="total_volume" value="{{ $report->total_volume }}" required min="0" step="0.01">
                                    </div>
                                </div>
                                @elseif($report->model_type === 'App\\Models\\MonthlyReport')
                                <div class="mb-3">
                                    <label for="month{{ $reportId }}" class="form-label">Month</label>
                                    <select class="form-select" id="month{{ $reportId }}" name="month" required>
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $loop->iteration }}" {{ $report->month == $loop->iteration ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif($report->model_type === 'App\\Models\\QuarterlyReport')
                                <div class="mb-3">
                                    <label for="quarter_number{{ $reportId }}" class="form-label">Quarter</label>
                                    <select class="form-select" id="quarter_number{{ $reportId }}" name="quarter_number" required>
                                        <option value="1" {{ $report->quarter_number == 1 ? 'selected' : '' }}>First Quarter (Jan-Mar)</option>
                                        <option value="2" {{ $report->quarter_number == 2 ? 'selected' : '' }}>Second Quarter (Apr-Jun)</option>
                                        <option value="3" {{ $report->quarter_number == 3 ? 'selected' : '' }}>Third Quarter (Jul-Sep)</option>
                                        <option value="4" {{ $report->quarter_number == 4 ? 'selected' : '' }}>Fourth Quarter (Oct-Dec)</option>
                                    </select>
                                </div>
                                @elseif($report->model_type === 'App\\Models\\SemestralReport')
                                <div class="mb-3">
                                    <label for="sem_number{{ $reportId }}" class="form-label">Semester</label>
                                    <select class="form-select" id="sem_number{{ $reportId }}" name="sem_number" required>
                                        <option value="1" {{ $report->sem_number == 1 ? 'selected' : '' }}>First Semester (Jan-Jun)</option>
                                        <option value="2" {{ $report->sem_number == 2 ? 'selected' : '' }}>Second Semester (Jul-Dec)</option>
                                    </select>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }} me-1"></i>
                                {{ $report->status === 'rejected' ? 'Resubmit Report' : 'Update Report' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('styles')
<style>
    /* Define CSS variables for status colors */
    :root {
        --primary: #0d6efd; /* Trustworthy blue - monthly reports */
        --primary-light: rgba(13, 110, 253, 0.1);
        --primary-rgb: 13, 110, 253;

        --success: #198754; /* Forest green - quarterly reports and submitted status */
        --success-light: rgba(25, 135, 84, 0.1);
        --success-rgb: 25, 135, 84;

        --warning: #ffc107; /* Amber - semestral reports and pending items */
        --warning-light: rgba(255, 193, 7, 0.1);
        --warning-rgb: 255, 193, 7;

        --danger: #dc3545; /* Red - annual reports and rejected status */
        --danger-light: rgba(220, 53, 69, 0.1);
        --danger-rgb: 220, 53, 69;

        --info: #0dcaf0; /* Light blue - weekly reports */
        --info-light: rgba(13, 202, 240, 0.1);
        --info-rgb: 13, 202, 240;

        --secondary: #6c757d;
        --secondary-light: rgba(108, 117, 125, 0.1);
        --secondary-rgb: 108, 117, 125;

        /* Status Colors */
        --submitted: var(--success);
        --submitted-light: var(--success-light);
        --submitted-rgb: var(--success-rgb);
    }

    .table th {
        background: var(--light);
        font-weight: 600;
    }

    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }

    .search-box {
        border-radius: 0.375rem;
    }

    .search-box:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    }

    /* Status Badge Styles */
    .status-badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        line-height: 1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .status-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-badge:hover::before {
        transform: translateX(100%);
    }

    .status-badge i {
        font-size: 0.875rem;
    }

    .status-badge.submitted {
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid rgba(var(--success-rgb), 0.2);
    }

    .status-badge.pending {
        background-color: var(--warning-light);
        color: var(--warning);
        border: 1px solid rgba(var(--warning-rgb), 0.2);
    }

    .status-badge.approved {
        background-color: var(--primary-light);
        color: var(--primary);
        border: 1px solid rgba(var(--primary-rgb), 0.2);
    }

    .status-badge.rejected {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid rgba(var(--danger-rgb), 0.2);
    }

    /* Frequency Badge Styles */
    .status-badge.frequency-weekly {
        background-color: var(--info-light);
        color: var(--info);
        border: 1px solid rgba(var(--info-rgb), 0.2);
    }

    .status-badge.frequency-monthly {
        background-color: var(--primary-light);
        color: var(--primary);
        border: 1px solid rgba(var(--primary-rgb), 0.2);
    }

    .status-badge.frequency-quarterly {
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid rgba(var(--success-rgb), 0.2);
    }

    .status-badge.frequency-semestral {
        background-color: var(--warning-light);
        color: var(--warning);
        border: 1px solid rgba(var(--warning-rgb), 0.2);
    }

    .status-badge.frequency-annual {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid rgba(var(--danger-rgb), 0.2);
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem 2rem;
    }

    .modal-body {
        padding: 0 2rem 2rem;
    }

    .modal-footer {
        border-top: none;
        padding: 1rem 2rem 1.5rem;
        background-color: var(--light);
    }

    .modal .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .modal .btn:hover {
        transform: translateY(-1px);
    }

    .modal .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .modal .btn-outline-primary {
        color: var(--primary);
        border-color: var(--primary);
    }

    .modal .btn-outline-primary:hover {
        background-color: var(--primary-light);
        color: var(--primary);
    }
</style>
@endpush

@push('scripts')
<script>
    // Function to preview files
    function previewFile(url, fileName) {
        // Get the file extension
        const extension = fileName.split('.').pop().toLowerCase();

        // Get the modal elements
        const modal = document.getElementById('filePreviewModal') || createPreviewModal();
        const previewContainer = document.getElementById('previewContainer');
        const previewFileName = document.getElementById('previewFileName');
        const downloadLink = document.getElementById('downloadLink');
        const fileIcon = document.getElementById('fileIcon');
        const fileIconContainer = document.getElementById('fileIconContainer');

        // Set the file name and download link
        previewFileName.textContent = fileName;
        downloadLink.href = url + '&download=true';

        // Set the appropriate icon based on file extension
        let iconClass = 'fa-file';
        let colorClass = 'primary';

        switch(extension) {
            case 'pdf':
                iconClass = 'fa-file-pdf';
                colorClass = 'danger';
                break;
            case 'doc':
            case 'docx':
                iconClass = 'fa-file-word';
                colorClass = 'primary';
                break;
            case 'xls':
            case 'xlsx':
                iconClass = 'fa-file-excel';
                colorClass = 'success';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                iconClass = 'fa-file-image';
                colorClass = 'info';
                break;
            case 'txt':
                iconClass = 'fa-file-alt';
                colorClass = 'secondary';
                break;
        }

        fileIcon.className = 'fas ' + iconClass;
        fileIcon.classList.add('text-' + colorClass);
        fileIconContainer.style.backgroundColor = `rgba(var(--${colorClass}-rgb), 0.1)`;

        // Show loading indicator
        previewContainer.innerHTML = `
            <div class="d-flex justify-content-center align-items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        // Show the modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Determine how to display the file based on its type
        if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            // For images, create an img element
            const img = new Image();
            img.onload = function() {
                previewContainer.innerHTML = '';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.maxHeight = '70vh';
                img.classList.add('img-fluid', 'rounded');
                previewContainer.appendChild(img);
            };
            img.onerror = function() {
                previewContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Failed to load image.
                    </div>
                `;
            };
            img.src = url;
        } else if (extension === 'pdf') {
            // For PDFs, use an iframe
            previewContainer.innerHTML = `
                <iframe src="${url}" width="100%" height="600" style="border: none;"></iframe>
            `;
        } else {
            // For other file types, show a download prompt
            previewContainer.innerHTML = `
                <div class="text-center p-5">
                    <div class="mb-4">
                        <i class="fas ${iconClass} fa-4x text-${colorClass}"></i>
                    </div>
                    <h5 class="mb-3">Preview not available</h5>
                    <p class="text-muted mb-4">This file type cannot be previewed directly. Please download the file to view its contents.</p>
                    <a href="${url}&download=true" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Download File
                    </a>
                </div>
            `;
        }
    }

    // Function to create the preview modal if it doesn't exist
    function createPreviewModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'filePreviewModal';
        modal.tabIndex = '-1';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-2">
                        <div class="d-flex align-items-center">
                            <div id="fileIconContainer" class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1);">
                                <i id="fileIcon" class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0 fw-bold">
                                    <span id="previewFileName"></span>
                                </h5>
                                <div class="text-muted small">File Preview</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="previewContainer" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="#" id="downloadLink" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>
                            Download
                        </a>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Add event listeners for filter changes
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            // For any input or select that might trigger a filter
            filterForm.querySelectorAll('input, select').forEach(el => {
                el.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });
</script>
@endpush
