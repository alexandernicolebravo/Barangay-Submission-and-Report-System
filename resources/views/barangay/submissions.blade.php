@extends('layouts.barangay')

@section('title', 'My Submissions')
@section('page-title', 'My Submissions')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">Submitted Reports</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="input-group" style="min-width: 250px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search reports...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <select class="form-select" id="statusFilter" style="width: auto;">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <select class="form-select" id="frequencyFilter" style="width: auto;">
                                <option value="">All Frequencies</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="semestral">Semestral</option>
                                <option value="annual">Annual</option>
                            </select>
                            <select class="form-select" id="sortBy" style="width: auto;">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="type">Report Type</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
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
                                <thead class="table-light">
                                    <tr>
                                        <th>Report Type</th>
                                        <th>Frequency</th>
                                        <th>Submitted Date</th>
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
                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                    {{ $report->reportType->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($report->reportType->frequency) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $report->created_at->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                                    @if($report->updated_at && $report->updated_at->ne($report->created_at))
                                                    <small class="text-success mt-1">
                                                        <i class="fas fa-sync-alt me-1"></i> Updated: {{ $report->updated_at->format('M d, Y h:i A') }}
                                                    </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
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
                                                        <span class="ms-1">View Remarks</span>
                                                    </button>
                                                @else
                                                    <span class="text-muted">No remarks</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="previewFile('{{ route('barangay.files.download', $report->id) }}', '{{ basename($report->file_path) }}')"
                                                            data-bs-toggle="tooltip"
                                                            title="View/Download Report">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm {{ $report->status === 'rejected' ? 'btn-outline-warning' : 'btn-outline-secondary' }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#resubmitModal{{ $report->id }}"
                                                            title="{{ $report->status === 'rejected' ? 'Resubmit Report (Required)' : 'Update Report' }}"
                                                            {{ $report->status === 'approved' ? 'disabled' : '' }}>
                                                        <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }}"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Resubmit/Update Modal -->
                                        <div class="modal fade" id="resubmitModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light">
                                                        <div class="d-flex align-items-center">
                                                            @if($report->status === 'rejected')
                                                            <div class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--warning-rgb), 0.1);">
                                                                <i class="fas fa-redo fa-lg text-warning"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold">Resubmit Report</h5>
                                                                <div class="text-muted small">{{ $report->report_type }}</div>
                                                            </div>
                                                            @else
                                                            <div class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--info-rgb), 0.1);">
                                                                <i class="fas fa-upload fa-lg text-info"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold">Update Report</h5>
                                                                <div class="text-muted small">{{ $report->report_type }}</div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        @if($report->status === 'rejected')
                                                        <div class="alert alert-warning mb-4">
                                                            <div class="d-flex">
                                                                <div class="me-3">
                                                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="alert-heading mb-1">Resubmission Required</h6>
                                                                    <p class="mb-0">This report was rejected. Please upload a new file to replace the previous submission.</p>
                                                                    @if($report->remarks)
                                                                    <div class="mt-2 p-2 bg-white rounded">
                                                                        <strong>Admin Remarks:</strong> {{ $report->remarks }}
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="alert alert-info mb-4">
                                                            <div class="d-flex">
                                                                <div class="me-3">
                                                                    <i class="fas fa-info-circle fa-lg"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="alert-heading mb-1">Update Information</h6>
                                                                    <p class="mb-0">You can update your previously submitted report by modifying the fields below and uploading a new file. All information will be updated.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <form action="{{ route('barangay.submissions.resubmit', $report->id) }}" method="POST" enctype="multipart/form-data" id="resubmitForm{{ $report->id }}">
                                                            @csrf
                                                            <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">
                                                            @php
                                                                $reportType = $report->type ?? $report->reportType->frequency;
                                                            @endphp
                                                            <input type="hidden" name="report_type" value="{{ $reportType }}">

                                                            <!-- Report Type Specific Fields -->
                                                            @php
                                                                $reportType = $report->type ?? $report->reportType->frequency;
                                                            @endphp

                                                            @if($reportType === 'weekly')
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
                                                                                    <option value="{{ $month }}" {{ $report->month === $month ? 'selected' : '' }}>{{ $month }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Week Number</label>
                                                                            <input type="number" class="form-control" name="week_number" min="1" max="52" required value="{{ $report->week_number }}">
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
                                                                                <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" required value="{{ $report->num_of_clean_up_sites }}">
                                                                                <span class="input-group-text">sites</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Number of Participants</label>
                                                                            <div class="input-group">
                                                                                <input type="number" class="form-control" name="num_of_participants" min="0" required value="{{ $report->num_of_participants }}">
                                                                                <span class="input-group-text">people</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Number of Barangays</label>
                                                                            <div class="input-group">
                                                                                <input type="number" class="form-control" name="num_of_barangays" min="0" required value="{{ $report->num_of_barangays }}">
                                                                                <span class="input-group-text">barangays</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Total Volume</label>
                                                                            <div class="input-group">
                                                                                <input type="number" class="form-control" name="total_volume" min="0" step="0.01" required value="{{ $report->total_volume }}">
                                                                                <span class="input-group-text">m³</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType === 'monthly')
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
                                                                                <option value="{{ $month }}" {{ $report->month === $month ? 'selected' : '' }}>{{ $month }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType === 'quarterly')
                                                            <div class="card bg-light mb-4">
                                                                <div class="card-body">
                                                                    <h6 class="card-title mb-4">
                                                                        <i class="fas fa-calendar-alt me-2"></i>
                                                                        Report Period
                                                                    </h6>
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Quarter</label>
                                                                            <select class="form-select" name="quarter_number" required>
                                                                                <option value="">Select Quarter</option>
                                                                                <option value="1" {{ $report->quarter_number == 1 ? 'selected' : '' }}>First Quarter (January - March)</option>
                                                                                <option value="2" {{ $report->quarter_number == 2 ? 'selected' : '' }}>Second Quarter (April - June)</option>
                                                                                <option value="3" {{ $report->quarter_number == 3 ? 'selected' : '' }}>Third Quarter (July - September)</option>
                                                                                <option value="4" {{ $report->quarter_number == 4 ? 'selected' : '' }}>Fourth Quarter (October - December)</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Year</label>
                                                                            <input type="number" class="form-control" name="year" min="2020" max="{{ date('Y') }}" value="{{ $report->year ?? date('Y') }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType === 'semestral')
                                                            <div class="card bg-light mb-4">
                                                                <div class="card-body">
                                                                    <h6 class="card-title mb-4">
                                                                        <i class="fas fa-calendar-alt me-2"></i>
                                                                        Report Period
                                                                    </h6>
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Semester</label>
                                                                            <select class="form-select" name="sem_number" required>
                                                                                <option value="">Select Semester</option>
                                                                                <option value="1" {{ $report->sem_number == 1 ? 'selected' : '' }}>First Semester (January - June)</option>
                                                                                <option value="2" {{ $report->sem_number == 2 ? 'selected' : '' }}>Second Semester (July - December)</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label">Year</label>
                                                                            <input type="number" class="form-control" name="year" min="2020" max="{{ date('Y') }}" value="{{ $report->year ?? date('Y') }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif($reportType === 'annual')
                                                            <div class="card bg-light mb-4">
                                                                <div class="card-body">
                                                                    <h6 class="card-title mb-4">
                                                                        <i class="fas fa-calendar-alt me-2"></i>
                                                                        Report Period
                                                                    </h6>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Year</label>
                                                                        <input type="number" class="form-control" name="year" min="2020" max="{{ date('Y') }}" value="{{ $report->year ?? date('Y') }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif

                                                            <div class="mb-4">
                                                                <label for="file" class="form-label fw-medium">Upload New Report</label>
                                                                <div class="file-upload-container" id="dropZone{{ $report->id }}">
                                                                    <input type="file" name="file" class="d-none" id="fileInput{{ $report->id }}" required accept=".pdf,.doc,.docx,.xlsx">
                                                                    <div class="text-center p-4 border rounded bg-light-subtle">
                                                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                                                        <p class="mb-2">Drag and drop your file here or</p>
                                                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput{{ $report->id }}').click()">
                                                                            <i class="fas fa-folder-open me-1"></i> Browse Files
                                                                        </button>
                                                                        <p class="mt-2 text-muted small">Accepted formats: PDF, DOC, DOCX, XLSX (Max size: 2MB)</p>
                                                                        <div id="fileInfo{{ $report->id }}" class="mt-3 d-none p-3 border rounded bg-white">
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fas fa-file-alt text-primary me-2"></i>
                                                                                <div>
                                                                                    <p class="mb-0 fw-medium"><span id="fileName{{ $report->id }}"></span></p>
                                                                                    <small class="text-muted">Click "{{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}" to upload this file</small>
                                                                                </div>
                                                                                <button type="button" class="btn btn-sm btn-link text-danger ms-auto" onclick="clearFile({{ $report->id }})">
                                                                                    <i class="fas fa-times"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary" id="submitBtn{{ $report->id }}" disabled>
                                                                    <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }} me-1"></i>
                                                                    {{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-muted">
                                    Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $reports->perPage() }} per page
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="perPageDropdown">
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 per page</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($reports->hasPages())
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if($reports->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reports->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                                                @if($page == $reports->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if($reports->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reports->nextPageUrl() }}" rel="next">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .file-upload-container {
            position: relative;
        }
        .file-upload-container.dragover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }
        .table > :not(caption) > * > * {
            padding: 1rem;
        }
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            padding: 0.375rem 0.75rem;
            color: #6c757d;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
        .form-select, .form-control {
            border-radius: 0.375rem;
        }
        .dropdown-menu {
            min-width: 8rem;
        }
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
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
                gap: 1rem;
            }
            .d-flex.justify-content-between > * {
                width: 100%;
            }
            .pagination {
                justify-content: center;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
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
                case 'doc':
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
                case 'txt':
                    iconClass = 'fa-file-alt';
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

            // Fetch the file
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('File not found or access denied');
                    }
                    const contentType = response.headers.get('content-type');
                    return response.blob().then(blob => ({ blob, contentType }));
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
                    } else if (['doc', 'docx', 'xls', 'xlsx'].includes(extension)) {
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
            const statusFilter = document.getElementById('statusFilter');
            const frequencyFilter = document.getElementById('frequencyFilter');
            const sortBy = document.getElementById('sortBy');
            const table = document.querySelector('table');
            const rows = table.getElementsByTagName('tr');

            function filterTable() {
                const searchText = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();
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

                    const matchesSearch = reportType.includes(searchText);
                    const matchesStatus = !statusValue || status.includes(statusValue);
                    const matchesFrequency = !frequencyValue || frequency.includes(frequencyValue);

                    if (matchesSearch && matchesStatus && matchesFrequency) {
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

            searchButton.addEventListener('click', filterTable);
            searchInput.addEventListener('keyup', filterTable);
            statusFilter.addEventListener('change', filterTable);
            frequencyFilter.addEventListener('change', filterTable);
            sortBy.addEventListener('change', filterTable);

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

            // File upload handling for report {{ $report->id }}
            const dropZone{{ $report->id }} = document.getElementById('dropZone{{ $report->id }}');
            const fileInput{{ $report->id }} = document.getElementById('fileInput{{ $report->id }}');
            const fileInfo{{ $report->id }} = document.getElementById('fileInfo{{ $report->id }}');
            const fileName{{ $report->id }} = document.getElementById('fileName{{ $report->id }}');
            const submitBtn{{ $report->id }} = document.getElementById('submitBtn{{ $report->id }}');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone{{ $report->id }}.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone{{ $report->id }}.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone{{ $report->id }}.addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            dropZone{{ $report->id }}.addEventListener('drop', handleDrop, false);

            function preventDefaults (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(e) {
                dropZone{{ $report->id }}.classList.add('dragover');
            }

            function unhighlight(e) {
                dropZone{{ $report->id }}.classList.remove('dragover');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput{{ $report->id }}.files = files;
                handleFiles(files);
            }

            fileInput{{ $report->id }}.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    const fileSize = file.size / 1024 / 1024; // in MB

                    if (fileSize > 2) {
                        alert('File size must be less than 2MB');
                        clearFile({{ $report->id }});
                        return;
                    }

                    const validTypes = ['.pdf', '.doc', '.docx', '.xlsx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

                    if (!validTypes.includes(fileExtension)) {
                        alert('Invalid file type. Please upload PDF, DOC, DOCX, or XLSX files only.');
                        clearFile({{ $report->id }});
                        return;
                    }

                    fileName{{ $report->id }}.textContent = file.name;
                    fileInfo{{ $report->id }}.classList.remove('d-none');
                    submitBtn{{ $report->id }}.disabled = false;
                }
            }

            function clearFile(id) {
                const fileInput = document.getElementById('fileInput' + id);
                const fileInfo = document.getElementById('fileInfo' + id);
                const submitBtn = document.getElementById('submitBtn' + id);

                if (fileInput && fileInfo && submitBtn) {
                    fileInput.value = '';
                    fileInfo.classList.add('d-none');
                    submitBtn.disabled = true;
                }
            }

            // Form submission handling
            const resubmitForm = document.getElementById('resubmitForm{{ $report->id }}');
            if (resubmitForm) {
                resubmitForm.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('fileInput{{ $report->id }}');
                    if (!fileInput || !fileInput.files.length) {
                        e.preventDefault();
                        alert('Please select a file to upload');
                    }
                });
            }

            // Enable submit button if file is already selected
            const existingFileInput = document.getElementById('fileInput{{ $report->id }}');
            const existingSubmitBtn = document.getElementById('submitBtn{{ $report->id }}');
            if (existingFileInput && existingSubmitBtn && existingFileInput.files.length > 0) {
                existingSubmitBtn.disabled = false;
            }
        });
    </script>
    @endpush

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
@endsection
