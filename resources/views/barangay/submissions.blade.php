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
                            <a href="{{ route('barangay.submit-report') }}" class="btn btn-primary mt-3">
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
                                                                'docx' => 'fa-file-word',
                                                                'doc' => 'fa-file-word', // Keep this for backward compatibility with existing files
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
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewFileModal{{ $reportId }}"
                                                            title="View File">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View File
                                                    </button>
                                                    @php
                                                        // Determine button style based on status and can_update flag
                                                        if ($report->status === 'rejected') {
                                                            // Rejected reports always show warning style
                                                            $btnClass = 'btn-outline-warning';
                                                            $icon = 'fa-redo';
                                                            $text = 'Resubmit';
                                                            $isDisabled = false;
                                                            $tooltip = 'Resubmit (Required)';
                                                        } elseif ($report->can_update) {
                                                            // Can update - show primary style
                                                            $btnClass = 'btn-outline-primary';
                                                            $icon = 'fa-upload';
                                                            $text = 'Update';
                                                            $isDisabled = false;
                                                            $tooltip = 'Update Report';
                                                        } else {
                                                            // Cannot update - show secondary style (disabled)
                                                            $btnClass = 'btn-outline-secondary';
                                                            $icon = 'fa-upload';
                                                            $text = 'Update';
                                                            $isDisabled = true;
                                                            $tooltip = 'Update Disabled';
                                                        }

                                                        // Override if approved
                                                        if ($report->status === 'approved') {
                                                            $isDisabled = true;
                                                            $tooltip = 'Cannot update approved reports';
                                                        }
                                                    @endphp
                                                    <button type="button"
                                                            class="btn btn-sm {{ $btnClass }} {{ $isDisabled ? 'opacity-50' : '' }}"
                                                            data-bs-toggle="{{ $isDisabled ? '' : 'modal' }}"
                                                            data-bs-target="{{ $isDisabled ? '' : '#resubmitModal' . $reportId }}"
                                                            title="{{ $tooltip }}"
                                                            {{ $isDisabled ? 'disabled' : '' }}>
                                                        <i class="fas {{ $icon }} me-1"></i>
                                                        {{ $text }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Resubmit/Update Modal -->
                                        <div class="modal fade" id="resubmitModal{{ $reportId }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light py-2">
                                                        <div class="d-flex align-items-center">
                                                            @if($report->status === 'rejected')
                                                            <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--warning-rgb), 0.1);">
                                                                <i class="fas fa-redo text-warning"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold">Resubmit Report</h5>
                                                                <div class="text-muted small">{{ $report->reportType->name }}</div>
                                                            </div>
                                                            @else
                                                            <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--info-rgb), 0.1);">
                                                                <i class="fas fa-upload text-info"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold">Update Report</h5>
                                                                <div class="text-muted small">{{ $report->reportType->name }}</div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        @if($report->status === 'rejected' && $report->remarks)
                                                        <div class="alert alert-warning py-2 px-3 mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                <strong>Admin Remarks:</strong> <span class="ms-2">{{ $report->remarks }}</span>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <!-- File Comparison Section -->
                                                        <div class="card border-0 bg-light-subtle mb-4">
                                                            <div class="card-body">
                                                                <h6 class="card-title mb-3">
                                                                    <i class="fas fa-exchange-alt me-2 text-primary"></i>
                                                                    Replace Current File
                                                                </h6>
                                                                <div class="d-flex align-items-center p-3 bg-white rounded border">
                                                                    @php
                                                                        $fileExtension = pathinfo($report->file_path, PATHINFO_EXTENSION);
                                                                        $iconClass = 'fa-file';
                                                                        $colorClass = 'primary';

                                                                        if ($fileExtension == 'pdf') {
                                                                            $iconClass = 'fa-file-pdf';
                                                                            $colorClass = 'danger';
                                                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                                                            $iconClass = 'fa-file-word';
                                                                            $colorClass = 'primary';
                                                                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                                                            $iconClass = 'fa-file-excel';
                                                                            $colorClass = 'success';
                                                                        }
                                                                    @endphp
                                                                    <div class="me-3 p-2 rounded" style="background-color: rgba(var(--{{ $colorClass }}-rgb), 0.1);">
                                                                        <i class="fas {{ $iconClass }} fa-lg text-{{ $colorClass }}"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <p class="mb-0 fw-medium">{{ basename($report->file_path) }}</p>
                                                                        <small class="text-muted">Submitted on {{ $report->created_at->format('M d, Y h:i A') }}</small>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                                                data-bs-toggle="modal" data-bs-target="#viewFileModal{{ $reportId }}">
                                                                            <i class="fas fa-eye me-1"></i> View File
                                                                        </button>
                                                                        <a href="{{ route('barangay.direct.files.download', $reportId) }}?download=true" class="btn btn-sm btn-outline-secondary ms-1">
                                                                            <i class="fas fa-download me-1"></i> Download
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <form action="{{ route('barangay.submissions.resubmit', $reportId) }}" method="POST" enctype="multipart/form-data" id="resubmitForm{{ $reportId }}">
                                                            @csrf
                                                            <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">



                                                            <!-- Report Type Specific Fields -->
                                                            @php
                                                                // Get the model type from the report
                                                                $reportClassName = $report->model_type ?? class_basename(get_class($report));

                                                                // Determine the report type based on the model class
                                                                if ($reportClassName === 'WeeklyReport') {
                                                                    $reportType = 'weekly';
                                                                } elseif ($reportClassName === 'MonthlyReport') {
                                                                    $reportType = 'monthly';
                                                                } elseif ($reportClassName === 'QuarterlyReport') {
                                                                    $reportType = 'quarterly';
                                                                } elseif ($reportClassName === 'SemestralReport') {
                                                                    $reportType = 'semestral';
                                                                } elseif ($reportClassName === 'AnnualReport') {
                                                                    $reportType = 'annual';
                                                                } else {
                                                                    $reportType = strtolower($report->reportType->frequency);
                                                                }

                                                                // Ensure report type is lowercase for consistent comparison
                                                                $reportType = strtolower($reportType);
                                                            @endphp
                                                            <input type="hidden" name="report_type" value="{{ $reportType }}">
                                                            <div id="reportFields{{ $reportId }}" data-report-type="{{ $reportType }}" data-report-id="{{ $reportId }}"></div>

                                                            @if($reportType == 'weekly')
                                                            <div class="card mb-3" style="background-color: rgba(var(--primary-rgb), 0.03);">
                                                                <div class="card-body p-2">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                                        <h6 class="card-title mb-0 small">Report Details</h6>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Month</label>
                                                                            <select class="form-select form-select-sm" name="month" required>
                                                                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                                    <option value="{{ $month }}" {{ $report->month === $month ? 'selected' : '' }}>{{ $month }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Week Number</label>
                                                                            <input type="number" class="form-control form-control-sm" name="week_number" min="1" max="52" required value="{{ $report->week_number }}">
                                                                        </div>
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Clean-up Sites</label>
                                                                            <input type="number" class="form-control form-control-sm" name="num_of_clean_up_sites" min="0" required value="{{ $report->num_of_clean_up_sites }}">
                                                                        </div>
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Participants</label>
                                                                            <input type="number" class="form-control form-control-sm" name="num_of_participants" min="0" required value="{{ $report->num_of_participants }}">
                                                                        </div>
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Barangays</label>
                                                                            <input type="number" class="form-control form-control-sm" name="num_of_barangays" min="0" required value="{{ $report->num_of_barangays }}">
                                                                        </div>
                                                                        <div class="col-md-3 mb-2">
                                                                            <label class="form-label small">Total Volume (m³)</label>
                                                                            <input type="number" class="form-control form-control-sm" name="total_volume" min="0" step="0.01" required value="{{ $report->total_volume }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType == 'monthly')
                                                            <div class="card mb-3" style="background-color: rgba(var(--primary-rgb), 0.03);">
                                                                <div class="card-body p-2">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                                        <h6 class="card-title mb-0 small">Report Details</h6>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-2">
                                                                            <label class="form-label small">Month</label>
                                                                            <select class="form-select form-select-sm" name="month" required>
                                                                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                                    <option value="{{ $month }}" {{ $report->month === $month ? 'selected' : '' }}>{{ $month }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <!-- Year field removed as requested -->
                                                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType == 'quarterly')
                                                            <div class="card mb-3" style="background-color: rgba(var(--primary-rgb), 0.03);">
                                                                <div class="card-body p-2">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                                        <h6 class="card-title mb-0 small">Report Details</h6>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-2">
                                                                            <label class="form-label small">Quarter</label>
                                                                            <select class="form-select form-select-sm" name="quarter_number" required>
                                                                                <option value="1" {{ $report->quarter_number == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                                                                                <option value="2" {{ $report->quarter_number == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                                                                                <option value="3" {{ $report->quarter_number == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                                                                                <option value="4" {{ $report->quarter_number == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                                                                            </select>
                                                                        </div>
                                                                        <!-- Year field removed as requested -->
                                                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType == 'semestral')
                                                            <div class="card mb-3" style="background-color: rgba(var(--primary-rgb), 0.03);">
                                                                <div class="card-body p-2">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                                        <h6 class="card-title mb-0 small">Report Details</h6>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-2">
                                                                            <label class="form-label small">Semester</label>
                                                                            <select class="form-select form-select-sm" name="sem_number" required>
                                                                                <option value="1" {{ $report->sem_number == 1 ? 'selected' : '' }}>1st Sem (Jan-Jun)</option>
                                                                                <option value="2" {{ $report->sem_number == 2 ? 'selected' : '' }}>2nd Sem (Jul-Dec)</option>
                                                                            </select>
                                                                        </div>
                                                                        <!-- Year field removed as requested -->
                                                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType == 'annual')
                                                            <div class="card mb-3" style="background-color: rgba(var(--primary-rgb), 0.03);">
                                                                <div class="card-body p-2">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                                        <h6 class="card-title mb-0 small">Report Details</h6>
                                                                    </div>
                                                                    <div class="row">
                                                                        <!-- Year field removed as requested -->
                                                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif

                                                            <div class="mb-3">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="fas fa-upload text-primary me-2"></i>
                                                                    <h6 class="mb-0 small">Upload New Report</h6>
                                                                </div>



                                                                <div class="mb-3">
                                                                    <label for="fileInput{{ $reportId }}" class="form-label small">Select New File</label>
                                                                    <input type="file" name="file" id="fileInput{{ $reportId }}" accept=".pdf,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar" class="form-control form-control-sm">
                                                                    <small class="d-block mt-1 text-muted" style="font-size: 0.7rem;">PDF, DOCX, XLS, XLSX, JPG, JPEG, PNG, ZIP, RAR (Max: 100MB)</small>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-sm {{ $report->status === 'rejected' ? 'btn-outline-warning' : 'btn-outline-primary' }}" id="submitBtn{{ $reportId }}">
                                                                    <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }} me-1"></i>
                                                                    {{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                                                </button>
                                                            </div>

                                                            <!-- Add a hidden field to ensure the form is properly submitted -->
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- View File Modal -->
                                        <div class="modal fade" id="viewFileModal{{ $reportId }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1);">
                                                                <i class="fas fa-file-alt text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold">{{ $report->reportType->name ?? 'Report Submission' }}</h5>
                                                                <div class="text-muted small">{{ basename($report->file_path) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="{{ route('barangay.direct.files.download', $reportId) }}?download=true"
                                                               class="btn btn-sm btn-primary">
                                                                <i class="fas fa-download me-1"></i>
                                                                Download
                                                            </a>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body p-0">
                                                        <div class="file-viewer-modal" id="fileViewer{{ $reportId }}" style="height: 70vh; background: #f8f9fc;">
                                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                                <div class="text-center py-5">
                                                                    <div class="spinner-border text-primary" role="status">
                                                                        <span class="visually-hidden">Loading...</span>
                                                                    </div>
                                                                    <p class="mt-3 text-muted">Loading file preview...</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted">
                                    {{ $reports->firstItem() ?? 0 }}-{{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }}
                                </small>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle py-0 px-2" type="button" id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $reports->perPage() }}
                                    </button>
                                    <ul class="dropdown-menu shadow-sm" aria-labelledby="perPageDropdown">
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 per page</a></li>
                                    </ul>
                                </div>
                            </div>
                            @if($reports->hasPages())
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Previous Page Link --}}
                                        <li class="page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $reports->previousPageUrl() }}" rel="prev" aria-label="Previous">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Pagination Elements --}}
                                        @php
                                            $currentPage = $reports->currentPage();
                                            $lastPage = $reports->lastPage();
                                            $range = 2; // Show 2 pages before and after current page

                                            $startPage = max($currentPage - $range, 1);
                                            $endPage = min($currentPage + $range, $lastPage);

                                            // Always show first page
                                            if ($startPage > 1) {
                                                echo '<li class="page-item"><a class="page-link" href="'.$reports->url(1).'">1</a></li>';

                                                // Add ellipsis if needed
                                                if ($startPage > 2) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                            }

                                            // Main page range
                                            for ($i = $startPage; $i <= $endPage; $i++) {
                                                if ($i == $currentPage) {
                                                    echo '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
                                                } else {
                                                    echo '<li class="page-item"><a class="page-link" href="'.$reports->url($i).'">'.$i.'</a></li>';
                                                }
                                            }

                                            // Always show last page
                                            if ($endPage < $lastPage) {
                                                // Add ellipsis if needed
                                                if ($endPage < $lastPage - 1) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }

                                                echo '<li class="page-item"><a class="page-link" href="'.$reports->url($lastPage).'">'.$lastPage.'</a></li>';
                                            }
                                        @endphp

                                        {{-- Next Page Link --}}
                                        <li class="page-item {{ !$reports->hasMorePages() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $reports->nextPageUrl() }}" rel="next" aria-label="Next">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

            --neutral: #f8f9fa; /* Light gray - clean, professional background */
            --neutral-light: #f9fafb; /* Very light gray - for subtle backgrounds */
            --neutral-dark: #e9ecef; /* Slightly darker neutral for contrast */
            --text-primary: #495057; /* Dark gray - easy to read but less harsh than black */
            --text-secondary: #6c757d; /* Medium gray - secondary information */
            --border-color: #dee2e6; /* Light gray border - subtle separation */
        }

        body {
            color: var(--text-primary);
        }

        /* Compact Design */
        .card {
            border-radius: 0.375rem;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: var(--neutral);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
        }

        .card-body {
            padding: 0.75rem 1rem;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--light);
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .table td {
            vertical-align: middle;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .table tr:hover {
            background-color: rgba(var(--primary-rgb), 0.03);
        }

        /* Badge Styles - Color Psychology */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* File Viewer Modal Styles */
        .file-viewer-modal {
            background: #f8f9fc;
            border-radius: 8px;
            overflow: hidden;
        }

        .file-viewer-modal iframe {
            border-radius: 8px;
        }

        .modal-xl {
            max-width: 90vw;
        }

        @media (max-width: 768px) {
            .modal-xl {
                max-width: 95vw;
                margin: 0.5rem;
            }

            .file-viewer-modal {
                height: 60vh !important;
            }
        }
            border-radius: 0.375rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
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
            background-color: var(--secondary-light);
            color: var(--secondary);
            border: 1px solid rgba(var(--secondary-rgb), 0.2);
        }

        /* Frequency Badge Styles */
        .frequency-weekly {
            background-color: var(--info-light) !important;
            color: var(--info);
            border: 1px solid rgba(var(--info-rgb), 0.3);
        }

        .frequency-monthly {
            background-color: var(--primary-light) !important;
            color: var(--primary);
            border: 1px solid rgba(var(--primary-rgb), 0.3);
        }

        .frequency-quarterly {
            background-color: var(--success-light) !important;
            color: var(--success);
            border: 1px solid rgba(var(--success-rgb), 0.3);
        }

        .frequency-semestral {
            background-color: var(--warning-light) !important;
            color: var(--warning);
            border: 1px solid rgba(var(--warning-rgb), 0.3);
        }

        .frequency-annual {
            background-color: var(--danger-light) !important;
            color: var(--danger);
            border: 1px solid rgba(var(--danger-rgb), 0.3);
        }

        /* Status Colors */
        .status-submitted {
            color: var(--success);
        }

        .status-approved {
            color: var(--primary);
        }

        .status-rejected {
            color: var(--danger);
        }

        .status-pending {
            color: var(--warning);
        }

        /* Button Styles - Color Psychology */
        .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-warning {
            background-color: var(--warning);
            border-color: var(--warning);
        }

        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: #fff;
        }

        .btn-outline-danger {
            border-color: var(--danger);
            color: var(--danger);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger);
            color: #fff;
        }

        .btn-outline-info {
            border-color: var(--info);
            color: var(--info);
        }

        .btn-outline-info:hover {
            background-color: var(--info);
            color: #fff;
        }

        /* Form Styles - Compact */
        .form-select, .form-control {
            border-radius: 0.25rem;
            border: 1px solid var(--border-color);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            height: calc(1.5em + 0.75rem + 2px);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }

        /* Filter Styles */
        .filter-active {
            background-color: rgba(var(--primary-rgb), 0.1);
            border-color: var(--primary);
        }

        /* Pagination Styles - Compact */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            color: var(--primary);
            background-color: #fff;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .pagination .page-link:hover {
            background-color: var(--neutral-dark);
            border-color: var(--border-color);
            color: var(--primary);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            color: var(--text-secondary);
            pointer-events: none;
            background-color: #fff;
            border-color: var(--border-color);
        }

        /* Dropdown Styles - Compact */
        .dropdown-menu {
            min-width: 8rem;
            border-radius: 0.25rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.875rem;
            padding: 0.25rem 0;
        }

        .dropdown-item {
            padding: 0.375rem 0.75rem;
            transition: background-color 0.15s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: var(--neutral);
        }

        /* Modal Styles - Compact */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 1rem;
        }

        .modal-header {
            padding: 0.75rem 1rem;
        }

        .modal-footer {
            padding: 0.75rem 1rem;
        }

        /* File Upload Styles */
        .file-upload-container {
            position: relative;
            border: 2px dashed var(--border-color);
            border-radius: 0.375rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            background-color: var(--neutral);
        }

        .file-upload-container.dragover {
            border-color: var(--primary);
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .file-upload-container:hover {
            border-color: var(--primary);
        }

        /* Input Group Styles */
        .input-group {
            margin-bottom: 0;
        }

        .input-group .form-control {
            border-right: 0;
        }

        .input-group .btn {
            border-left: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-text {
            background-color: #fff;
            border-right: 0;
        }

        .search-box {
            border-radius: 0.375rem;
        }

        .search-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }

        /* Enhanced Filter Section */
        .filter-container {
            background-color: var(--neutral-light);
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
        }

        .filter-header {
            color: var(--text-primary);
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(var(--primary-rgb), 0.1);
            padding-bottom: 0.5rem;
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .filter-group {
            margin-bottom: 0.75rem;
        }

        .filter-label {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .filter-container .form-control,
        .filter-container .form-select {
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
            height: calc(1.5em + 0.75rem + 2px);
        }

        .filter-container .form-control:focus,
        .filter-container .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.15rem rgba(var(--primary-rgb), 0.15);
        }

        .filter-container .input-group .btn {
            border-top-right-radius: 0.25rem !important;
            border-bottom-right-radius: 0.25rem !important;
            height: calc(1.5em + 0.75rem + 2px);
        }

        .filter-active {
            border-color: var(--primary) !important;
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .filter-active-btn {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .active-filters {
            padding-top: 0.75rem;
            border-top: 1px dashed rgba(var(--primary-rgb), 0.2);
            margin-top: 0.75rem;
        }

        .filter-badge {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
            background-color: rgba(var(--primary-rgb), 0.1);
            color: var(--primary);
            border: 1px solid rgba(var(--primary-rgb), 0.2);
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }

        /* Status Colors */
        .status-submitted {
            color: var(--info);
        }

        .status-approved {
            color: var(--success);
        }

        .status-rejected {
            color: var(--danger);
        }

        .status-pending {
            color: var(--warning);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }
            .card-header .d-flex > * {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.5rem;
            }
            .d-flex.justify-content-between > * {
                width: 100%;
            }
            .pagination {
                justify-content: center;
            }

            .table td, .table th {
                padding: 0.4rem 0.5rem;
                font-size: 0.8rem;
            }

            /* Improved filter container for mobile */
            .filter-container {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .filter-header {
                font-size: 0.8rem;
                padding-bottom: 0.5rem;
                margin-bottom: 0.75rem;
            }

            .filter-container .row > div {
                margin-bottom: 0.75rem;
            }

            .filter-group {
                margin-bottom: 0.5rem;
            }

            .filter-label {
                margin-bottom: 0.25rem;
                font-size: 0.7rem;
            }

            .filter-container .form-control,
            .filter-container .form-select {
                font-size: 0.8rem;
                height: calc(1.5em + 0.75rem + 2px);
                padding: 0.375rem 0.5rem;
            }

            .filter-container .input-group .btn {
                height: calc(1.5em + 0.75rem + 2px);
            }

            .active-filters {
                padding-top: 0.5rem;
                margin-top: 0.5rem;
            }

            .filter-badge {
                margin-bottom: 0.25rem;
                font-size: 0.65rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle search form submission
            const searchForm = document.querySelector('form[action="{{ route('barangay.submissions') }}"]');
            const searchInput = document.getElementById('searchInput');

            // Submit search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.submit();
                }
            });

            // Check if we need to show the success modal (after form submission)
            @if(session('success'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                backdrop: 'static',  // Prevent closing when clicking outside
                keyboard: false      // Prevent closing with keyboard
            });
            document.getElementById('successModalMessage').textContent = "{{ session('success') }}";

            // Set the title based on the report status
            @if(session('reportStatus') === 'rejected')
            document.getElementById('successModalTitle').textContent = "Report Resubmitted Successfully";
            @else
            document.getElementById('successModalTitle').textContent = "Report Updated Successfully";
            @endif

            // Show the modal
            successModal.show();

            // Set up countdown timer
            let countdown = 2;
            const countdownElement = document.getElementById('countdown');

            // Update countdown every second
            const countdownInterval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    successModal.hide();
                    window.location.reload();
                }
            }, 1000);
            @endif
        });

        // Only define the previewFile function if there are reports
        @if(!$reports->isEmpty())
        // File preview function
        function previewFile(url, fileName) {
            // Set the file name in the modal
            document.getElementById('previewFileName').textContent = fileName;

            // Set the download link
            const downloadLink = document.getElementById('downloadLink');
            downloadLink.href = url + '?download=true';

            // Show loading spinner
            const previewContainer = document.getElementById('previewContainer');
            previewContainer.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading document preview...</p>
                </div>
            `;

            // Get file extension
            const extension = fileName.split('.').pop().toLowerCase();

            // Update file type icon based on extension
            const fileTypeIcon = document.getElementById('fileTypeIcon');
            const fileIconElement = fileTypeIcon.querySelector('i');

            // Set icon and background color based on file type
            let iconClass = 'fa-file';
            let bgColorClass = 'primary';

            switch(extension) {
                case 'pdf':
                    iconClass = 'fa-file-pdf';
                    bgColorClass = 'danger';
                    break;
                case 'docx':
                    iconClass = 'fa-file-word';
                    bgColorClass = 'primary';
                    break;
                case 'xls':
                case 'xlsx':
                    iconClass = 'fa-file-excel';
                    bgColorClass = 'success';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    iconClass = 'fa-file-image';
                    bgColorClass = 'info';
                    break;
                case 'zip':
                case 'rar':
                    iconClass = 'fa-file-archive';
                    bgColorClass = 'secondary';
                    break;
                default:
                    iconClass = 'fa-file';
                    bgColorClass = 'primary';
            }

            // Update icon class
            fileIconElement.className = `fas ${iconClass} fa-lg text-${bgColorClass}`;

            // Update background color
            fileTypeIcon.style.backgroundColor = `rgba(var(--${bgColorClass}-rgb), 0.1)`;

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            modal.show();

            // Fetch the file with proper headers
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('File not found or access denied');
                    }

                    // Check if response is JSON (from AJAX request)
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            if (data.success) {
                                // Use the file URL from the JSON response
                                return fetch(data.file_url).then(fileResponse => {
                                    if (!fileResponse.ok) {
                                        throw new Error('File not found or access denied');
                                    }
                                    return fileResponse.blob().then(blob => ({
                                        blob,
                                        contentType: data.mime_type || fileResponse.headers.get('content-type'),
                                        fileName: data.file_name
                                    }));
                                });
                            } else {
                                throw new Error(data.error || 'Failed to load file');
                            }
                        });
                    } else {
                        // Direct file response
                        return response.blob().then(blob => ({ blob, contentType }));
                    }
                })
                .then(({ blob, contentType }) => {
                    const fileUrl = URL.createObjectURL(blob);

                    // Create preview based on content type and extension
                    if (contentType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        // Image preview
                        previewContainer.innerHTML = `
                            <div class="text-center p-3 bg-white rounded shadow-sm" style="max-width: 95%;">
                                <img src="${fileUrl}" class="img-fluid" alt="${fileName}" style="max-height: 65vh;">
                                <div class="mt-3 text-muted small">
                                    <i class="fas fa-info-circle me-1"></i> Image preview: ${fileName}
                                </div>
                            </div>`;
                    } else if (contentType === 'application/pdf' || extension === 'pdf') {
                        // PDF preview
                        previewContainer.innerHTML = `
                            <div class="bg-white rounded shadow-sm" style="width: 95%; height: 65vh;">
                                <iframe src="${fileUrl}"
                                        style="width: 100%; height: 100%; border: none; border-radius: 0.375rem;"
                                        title="${fileName}">
                                </iframe>
                            </div>`;
                    } else if (contentType.startsWith('text/') || ['txt', 'csv', 'html'].includes(extension)) {
                        // Text preview
                        fetch(fileUrl)
                            .then(response => response.text())
                            .then(text => {
                                previewContainer.innerHTML = `
                                    <div class="bg-white rounded shadow-sm" style="width: 95%; max-height: 65vh; overflow-y: auto;">
                                        <pre class="text-start p-4 mb-0" style="white-space: pre-wrap;">${text}</pre>
                                        <div class="p-3 border-top text-muted small">
                                            <i class="fas fa-info-circle me-1"></i> Text document: ${fileName}
                                        </div>
                                    </div>`;
                            });
                    } else if (['docx', 'xls', 'xlsx'].includes(extension)) {
                        // Office documents - use Google Docs Viewer
                        const googleDocsUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + url)}&embedded=true`;
                        previewContainer.innerHTML = `
                            <div class="bg-white rounded shadow-sm" style="width: 95%; height: 65vh;">
                                <iframe src="${googleDocsUrl}"
                                        style="width: 100%; height: 100%; border: none; border-radius: 0.375rem;"
                                        title="${fileName}">
                                </iframe>
                                <div class="p-3 border-top text-muted small">
                                    <i class="fas fa-info-circle me-1"></i> Office document preview powered by Google Docs
                                </div>
                            </div>`;
                    } else {
                        // Unsupported file type
                        previewContainer.innerHTML = `
                            <div class="bg-white rounded shadow-sm p-4 text-center" style="max-width: 500px;">
                                <div class="mb-3">
                                    <i class="fas ${iconClass} fa-4x text-${bgColorClass} mb-3"></i>
                                    <h5 class="mb-3">Preview Not Available</h5>
                                    <p class="text-muted mb-4">This file type cannot be previewed in the browser.</p>
                                </div>
                                <a href="${downloadLink.href}" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i> Download to View
                                </a>
                                <div class="mt-4 text-start text-muted small">
                                    <div><strong>File name:</strong> ${fileName}</div>
                                    <div><strong>File type:</strong> ${contentType || 'Unknown'}</div>
                                    <div><strong>Extension:</strong> ${extension}</div>
                                </div>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Preview error:', error);
                    previewContainer.innerHTML = `
                        <div class="bg-white rounded shadow-sm p-4 text-center" style="max-width: 500px;">
                            <div class="mb-3 text-danger">
                                <i class="fas fa-exclamation-circle fa-4x mb-3"></i>
                                <h5 class="mb-3">Error Loading File</h5>
                                <p class="text-muted mb-4">${error.message}</p>
                            </div>
                            <a href="${downloadLink.href}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i> Try Downloading Instead
                            </a>
                            <div class="mt-4 text-start text-muted small">
                                <div><strong>File name:</strong> ${fileName}</div>
                                <div><strong>Extension:</strong> ${extension}</div>
                            </div>
                        </div>`;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search and filter functionality
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const frequencyFilter = document.getElementById('frequencyFilter');
            const sortBy = document.getElementById('sortBy');
            const table = document.querySelector('table');
            const rows = table.getElementsByTagName('tr');

            function filterTable() {
                const searchText = searchInput.value.toLowerCase();
                const frequencyValue = frequencyFilter.value.toLowerCase();
                const sortValue = sortBy.value;

                let visibleRows = [];

                // First, filter the rows
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    const reportType = cells[0].textContent.toLowerCase();
                    const frequency = cells[1].textContent.toLowerCase();
                    const status = cells[3].textContent.toLowerCase();
                    const date = cells[2].textContent.toLowerCase();
                    const remarks = cells[4].textContent.toLowerCase();

                    // Improved search to look across multiple fields
                    const matchesSearch = searchText === '' ||
                                         reportType.includes(searchText) ||
                                         frequency.includes(searchText) ||
                                         status.includes(searchText) ||
                                         date.includes(searchText) ||
                                         remarks.includes(searchText);

                    const matchesFrequency = !frequencyValue || frequency.includes(frequencyValue);

                    if (matchesSearch && matchesFrequency) {
                        row.style.display = '';
                        visibleRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                }

                // Then, sort the visible rows
                visibleRows.sort((a, b) => {
                    const aCells = a.getElementsByTagName('td');
                    const bCells = b.getElementsByTagName('td');

                    switch(sortValue) {
                        case 'newest':
                            return new Date(bCells[2].textContent) - new Date(aCells[2].textContent);
                        case 'oldest':
                            return new Date(aCells[2].textContent) - new Date(bCells[2].textContent);
                        case 'type':
                            return aCells[0].textContent.localeCompare(bCells[0].textContent);
                        case 'status':
                            return aCells[3].textContent.localeCompare(bCells[3].textContent);
                        default:
                            return 0;
                    }
                });

                // Reorder the rows in the table
                const tbody = table.querySelector('tbody');
                visibleRows.forEach(row => tbody.appendChild(row));
            }

            // Add event listeners for real-time filtering
            searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                filterTable();
            });

            searchInput.addEventListener('keyup', function() {
                filterTable();

                // Show/hide clear filters button
                const clearFiltersBtn = document.querySelector('a[href="{{ route('barangay.submissions') }}"]');
                if (clearFiltersBtn) {
                    if (searchInput.value || frequencyFilter.value || sortBy.value !== 'newest') {
                        clearFiltersBtn.classList.remove('d-none');
                    } else {
                        clearFiltersBtn.classList.add('d-none');
                    }
                }
            });

            frequencyFilter.addEventListener('change', function() {
                filterTable();

                // Submit form to update URL parameters for server-side filtering
                const form = document.querySelector('form[action="{{ route('barangay.submissions') }}"]');
                if (form) {
                    form.submit();
                }
            });

            sortBy.addEventListener('change', function() {
                filterTable();

                // Submit form to update URL parameters for server-side sorting
                const form = document.querySelector('form[action="{{ route('barangay.submissions') }}"]');
                if (form) {
                    form.submit();
                }
            });

            // File upload drag and drop functionality
            document.querySelectorAll('.file-upload-container').forEach(container => {
                const fileInput = container.querySelector('input[type="file"]');
                const fileInfo = container.querySelector('.file-upload-container .d-none');
                const fileName = container.querySelector('.file-upload-container .d-none span');

                container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    container.classList.add('dragover');
                });

                container.addEventListener('dragleave', () => {
                    container.classList.remove('dragover');
                });

                container.addEventListener('drop', (e) => {
                    e.preventDefault();
                    container.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        updateFileInfo(files[0], fileInfo, fileName);
                    }
                });

                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        updateFileInfo(e.target.files[0], fileInfo, fileName);
                    }
                });
            });

            function updateFileInfo(file, fileInfo, fileName) {
                fileName.textContent = file.name;
                fileInfo.classList.remove('d-none');
            }

            window.clearFile = function(id) {
                const fileInput = document.getElementById('fileInput' + id);
                const fileInfo = document.getElementById('fileInfo' + id);
                fileInput.value = '';
                fileInfo.classList.add('d-none');
            };

            // Simple file validation for all file inputs
            document.addEventListener('change', function(e) {
                if (e.target && e.target.type === 'file' && e.target.name === 'file') {
                    const file = e.target.files[0];
                    if (file) {
                        const fileSize = file.size / 1024 / 1024; // in MB
                        if (fileSize > 100) {
                            alert('File size must be less than 100MB');
                            e.target.value = '';
                            return;
                        }
                    }
                }
            });

            // Debug form submissions
            document.addEventListener('submit', function(e) {
                if (e.target.id && e.target.id.startsWith('resubmitForm')) {
                    console.log('Form submission detected:', e.target.id);
                    console.log('Form action:', e.target.action);
                    console.log('Form method:', e.target.method);

                    // Get form data
                    const formData = new FormData(e.target);
                    console.log('Form data:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key, value);
                    }

                    // Allow the form to submit normally
                    return true;
                }
            });
        });
        @endif
    </script>
    @endpush

    @if(!$reports->isEmpty())
    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center">
                        <div id="fileTypeIcon" class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1);">
                            <i class="fas fa-file fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">
                                <span id="previewFileName"></span>
                            </h5>
                            <div class="text-muted small">Document Preview</div>
                        </div>
                    </div>
                    <div>
                        <a id="downloadLink" href="#" class="btn btn-primary me-2">
                            <i class="fas fa-download me-1"></i>
                            Download
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-light">
                    <div id="previewContainer" class="d-flex justify-content-center align-items-center p-4" style="min-height: 70vh; background-color: #f8f9fa;">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Loading document preview...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex align-items-center text-muted me-auto small">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>If the document doesn't load correctly, please use the download button.</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white justify-content-center">
                    <h5 class="modal-title text-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Success
                    </h5>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h4 class="mb-3" id="successModalTitle">Report Updated Successfully</h4>
                        <p class="text-muted" id="successModalMessage">Your report has been successfully updated and will be reviewed by the admin.</p>
                        <div class="mt-3">
                            <small class="text-muted">This message will close automatically in <span id="countdown">2</span> seconds</small>
                        </div>
                    </div>
                </div>
                <!-- No footer with close button -->
            </div>
        </div>
    </div>

    @endif

    @push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle form submission with loading state
        document.querySelectorAll('form[id^="resubmitForm"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

                // Re-enable button after 10 seconds as fallback
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            });
        });

        // Handle file viewer modals
        document.querySelectorAll('[id^="viewFileModal"]').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const modalId = modal.id;
                const reportId = modalId.replace('viewFileModal', '');
                const fileViewer = document.getElementById('fileViewer' + reportId);

                if (fileViewer && !fileViewer.dataset.loaded) {
                    loadFilePreview(reportId, fileViewer);
                    fileViewer.dataset.loaded = 'true';
                }
            });
        });

        function loadFilePreview(reportId, fileViewer) {
            // Make an AJAX request to get file information
            fetch(`/barangay/direct-files/${reportId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('File not found');
                    }

                    const contentType = response.headers.get('content-type');
                    const contentDisposition = response.headers.get('content-disposition');

                    // Get filename from content-disposition header
                    let filename = 'document';
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }

                    const fileExtension = filename.split('.').pop().toLowerCase();
                    const fileUrl = `/barangay/direct-files/${reportId}`;

                    if (['pdf'].includes(fileExtension)) {
                        // For PDF files, use iframe
                        fileViewer.innerHTML = `<iframe src="${fileUrl}" style="width: 100%; height: 100%; border: none;"></iframe>`;
                    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                        // For images
                        fileViewer.innerHTML = `
                            <div class="d-flex align-items-center justify-content-center h-100 p-3">
                                <img src="${fileUrl}" style="max-width: 100%; max-height: 100%; object-fit: contain;" class="shadow-sm rounded">
                            </div>
                        `;
                    } else if (['txt'].includes(fileExtension)) {
                        // For text files, fetch and display content
                        response.text().then(text => {
                            fileViewer.innerHTML = `
                                <div class="p-3 h-100">
                                    <pre style="white-space: pre-wrap; height: 100%; overflow-y: auto; background: white; padding: 1.5rem; margin: 0; font-size: 0.9rem; line-height: 1.5; border-radius: 8px;">${text}</pre>
                                </div>
                            `;
                        });
                    } else if (['docx', 'doc'].includes(fileExtension)) {
                        // For Word documents, use Google Docs Viewer
                        const googleDocsUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + fileUrl)}&embedded=true`;
                        fileViewer.innerHTML = `
                            <iframe src="${googleDocsUrl}" style="width: 100%; height: 100%; border: none;"></iframe>
                        `;
                    } else if (['xls', 'xlsx'].includes(fileExtension)) {
                        // For Excel documents, use Google Docs Viewer
                        const googleDocsUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + fileUrl)}&embedded=true`;
                        fileViewer.innerHTML = `
                            <iframe src="${googleDocsUrl}" style="width: 100%; height: 100%; border: none;"></iframe>
                        `;
                    } else {
                        // For other file types, show download message
                        fileViewer.innerHTML = `
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-file fa-3x text-primary"></i>
                                </div>
                                <h5>Preview not available</h5>
                                <p class="text-muted mb-4">This file type cannot be previewed in the browser.</p>
                                <a href="/barangay/direct-files/${reportId}?download=true" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>
                                    Download to View
                                </a>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading file:', error);
                    fileViewer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <h6>Unable to load file preview</h6>
                            <p class="text-muted">Please try downloading the file instead.</p>
                            <a href="/barangay/direct-files/${reportId}?download=true" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>
                                Download File
                            </a>
                        </div>
                    `;
                });
        }
    </script>
    @endpush
@endsection
