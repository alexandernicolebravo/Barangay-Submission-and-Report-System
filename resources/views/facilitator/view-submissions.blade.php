@extends('facilitator.layouts.app')

@section('title', 'View Submissions')

@push('styles')
<style>
/* Define CSS variables for status colors */
:root {
    --success-rgb: 25, 135, 84;
    --danger-rgb: 220, 53, 69;
    --warning-rgb: 255, 193, 7;
    --primary-rgb: 13, 110, 253;
    --secondary-rgb: 108, 117, 125;
    --info-rgb: 13, 202, 240;
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

/* Pagination Styles */
.pagination {
    margin: 0;
    padding: 1rem 0;
}

.pagination .page-item .page-link {
    color: var(--primary);
    border: 1px solid var(--border-color);
    padding: 0.5rem 1rem;
    margin: 0 0.2rem;
    border-radius: 0.375rem;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-500);
    border-color: var(--border-color);
}

.pagination .page-item .page-link:hover {
    background-color: var(--primary-light);
    border-color: var(--primary);
    color: var(--primary);
}

.pagination .page-item.active .page-link:hover {
    background-color: var(--primary);
    color: white;
}

.pagination-info {
    color: var(--gray-600);
    font-size: 0.875rem;
}

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

.status-badge.no-submission {
    background-color: var(--danger-light);
    color: var(--danger);
    border: 1px solid rgba(var(--danger-rgb), 0.2);
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
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title">
            <i class="fas fa-file-alt"></i>
            Report Submissions
        </h2>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-file-alt me-2" style="color: var(--primary);"></i>
            All Submissions
        </h5>

        <form id="filterForm" class="d-flex flex-wrap gap-2" method="GET" action="{{ route('facilitator.view-submissions') }}">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control search-box" name="search" value="{{ request('search') }}" placeholder="Search...">
            </div>

            <!-- Barangay Filter -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text">
                    <i class="fas fa-building"></i>
                </span>
                <select class="form-select" name="barangay_id">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" {{ request('barangay_id') == $barangay->id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-filter"></i>
                </span>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                </select>
            </div>

            <!-- Timeliness Filter -->
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-clock"></i>
                </span>
                <select class="form-select" name="timeliness">
                    <option value="">All Submissions</option>
                    <option value="late" {{ request('timeliness') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="ontime" {{ request('timeliness') == 'ontime' ? 'selected' : '' }}>On Time</option>
                </select>
            </div>

            @if(request()->hasAny(['search', 'barangay_id', 'type', 'timeliness']))
                <a href="{{ route('facilitator.view-submissions') }}" class="btn btn-light">
                    <i class="fas fa-times"></i>
                    Clear Filters
                </a>
            @endif
        </form>
    </div>
    <div class="card-body">
        @if(isset($selectedBarangay))
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Showing submissions for <strong>{{ $selectedBarangay->name }}</strong>
        </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Report</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
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

                                        $colorClass = match($extension) {
                                            'pdf' => 'danger',
                                            'doc', 'docx' => 'primary',
                                            'xls', 'xlsx' => 'success',
                                            'jpg', 'jpeg', 'png', 'gif' => 'info',
                                            'txt' => 'secondary',
                                            default => 'primary'
                                        };

                                        $isLate = \Carbon\Carbon::parse($report->updated_at)->isAfter($report->reportType->deadline);
                                    @endphp
                                    <i class="fas {{ $icon }} fa-sm"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $report->reportType->name }}</div>
                                    <small class="text-muted">{{ ucfirst(str_replace('Report', '', class_basename($report->model_type))) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $report->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($report->updated_at)->format('M d, Y') }}</td>
                        <td>
                            @php
                                $statusIcon = match($report->status) {
                                    'submitted' => 'fa-check-circle',
                                    'no submission' => 'fa-times-circle',
                                    'pending' => 'fa-clock',
                                    'approved' => 'fa-thumbs-up',
                                    'rejected' => 'fa-thumbs-down',
                                    default => 'fa-info-circle'
                                };
                                $statusClass = str_replace(' ', '-', $report->status);
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }}"></i>
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#viewSubmissionModal{{ $report->unique_id }}">
                                <i class="fas fa-eye me-1"></i>
                                View
                            </button>
                        </td>
                    </tr>

                    <!-- View Submission Modal -->
                    <div class="modal fade" id="viewSubmissionModal{{ $report->unique_id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-light py-2">
                                    @php
                                        $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                        $fileName = basename($report->file_path);
                                        $iconClass = match($extension) {
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
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--{{ $colorClass }}-rgb), 0.1); color: var(--{{ $colorClass }});">
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title mb-0 fw-bold">{{ $fileName }}</h5>
                                            <div class="text-muted small">{{ $report->reportType->name }} - {{ $report->user->name }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="p-3 border-bottom">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1); color: var(--primary);">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-muted small">Submitted</div>
                                                        <div class="fw-medium">{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--{{ $isLate ? 'danger' : 'success' }}-rgb), 0.1); color: var(--{{ $isLate ? 'danger' : 'success' }});">
                                                        <i class="fas {{ $isLate ? 'fa-clock' : 'fa-check-circle' }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-muted small">Status</div>
                                                        <div class="fw-medium">{{ $isLate ? 'Late Submission' : 'On Time' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-bottom">
                                        <h6 class="fw-bold mb-3">Remarks</h6>
                                        @if($report->remarks)
                                            <div class="p-3 rounded" style="background-color: rgba(var(--info-rgb), 0.1);">
                                                <i class="fas fa-quote-left text-muted me-2"></i>
                                                {{ $report->remarks }}
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-comment-slash mb-2" style="font-size: 1.5rem;"></i>
                                                <p class="mb-0">No remarks added yet</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div id="previewContainer" class="text-center p-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <a href="{{ route('facilitator.files.download', $report->id) }}" id="downloadLink" class="btn btn-primary">
                                        <i class="fas fa-download me-1"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--gray-400);"></i>
                                <p class="mb-0" style="color: var(--gray-600);">No submissions found</p>
                                @if(isset($selectedBarangay))
                                <p class="text-muted mt-2">No reports have been submitted by {{ $selectedBarangay->name }}</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports instanceof \Illuminate\Pagination\LengthAwarePaginator && $reports->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="pagination-info">
                Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
            </div>
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    View Report Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    <span class="fw-bold">Barangay:</span>
                                    <span id="viewBarangay" class="ms-2"></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    <span class="fw-bold">Report Type:</span>
                                    <span id="viewReportType" class="ms-2"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span class="fw-bold">Frequency:</span>
                                    <span id="viewFrequency" class="ms-2"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    <span class="fw-bold">Submitted:</span>
                                    <span id="viewSubmitted" class="ms-2"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <span class="fw-bold">Status:</span>
                                    <span id="viewStatus" class="ms-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Report Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="reportDetails"></div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-paperclip me-2 text-primary"></i>
                            Attached Files
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="attachedFiles"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-comment me-2 text-primary"></i>
                            Remarks
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="viewRemarks"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRemarksModal" id="openRemarksFromView">
                    <i class="fas fa-comment me-1"></i> Add Remarks
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Remarks Modal -->
<div class="modal fade" id="addRemarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addRemarksForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" id="remarkReportType">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-comment me-2 text-primary"></i>
                        Add Remarks
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-primary me-2"></i>
                                <span class="fw-bold">Barangay:</span>
                                <span id="remarkBarangay" class="ms-2"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <span class="fw-bold">Report Type:</span>
                                <span id="remarkReportName" class="ms-2"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarkText" rows="5" placeholder="Enter your remarks here..." required></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            These remarks will be visible to the barangay.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Remarks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message in URL and show modal
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('remarks_updated') && urlParams.get('remarks_updated') === 'true') {
        showRemarksUpdateSuccessModal();

        // Clean URL without refreshing the page
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }

    // Get all filter elements
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.querySelector('input[name="search"]');
    const filterSelects = document.querySelectorAll('select');
    const tableBody = document.querySelector('.table tbody');

    // Function to handle AJAX requests
    const handleAjaxRequest = (params) => {
        // Show loading state
        tableBody.style.opacity = '0.5';

        fetch(`${filterForm.action}?${new URLSearchParams(params).toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const tempContainer = document.createElement('div');
            tempContainer.innerHTML = html;

            // Replace the table body with the new content
            tableBody.innerHTML = tempContainer.querySelector('tbody').innerHTML;

            // Restore opacity
            tableBody.style.opacity = '1';
        })
        .catch(error => {
            console.error('Search request failed:', error);
            tableBody.style.opacity = '1';
        });
    };

    // Function to get all filter values
    const getFilterValues = () => {
        return {
            search: searchInput.value.trim(),
            barangay_id: document.querySelector('select[name="barangay_id"]').value,
            type: document.querySelector('select[name="type"]').value,
            timeliness: document.querySelector('select[name="timeliness"]').value,
            ajax: true
        };
    };

    // Add event listener for search input
    searchInput.addEventListener('input', function() {
        handleAjaxRequest(getFilterValues());
    });

    // Add event listeners for all select elements
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            handleAjaxRequest(getFilterValues());
        });
    });
});

function previewFile(url, fileName) {
    // Set the file name in the modal
    document.getElementById('previewFileName').textContent = fileName;

    // Set the download link
    const downloadLink = document.getElementById('downloadLink');
    downloadLink.href = url + '?download=true';

    // Show loading spinner
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;

    // Get file extension
    const extension = fileName.split('.').pop().toLowerCase();

    // Fetch the file to determine content type
    fetch(url)
        .then(response => {
            const contentType = response.headers.get('content-type');
            const fileUrl = response.url;

            // Create preview based on content type and extension
            if (contentType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                // Image preview
                previewContainer.innerHTML = `
                    <div class="text-center">
                        <img src="${fileUrl}" class="img-fluid" alt="${fileName}" style="max-height: 70vh;">
                    </div>`;
            } else if (contentType === 'application/pdf' || extension === 'pdf') {
                // PDF preview
                previewContainer.innerHTML = `
                    <div style="height: 70vh;">
                        <iframe src="${fileUrl}"
                                style="width: 100%; height: 100%; border: none;"
                                title="${fileName}">
                        </iframe>
                    </div>`;
            } else if (contentType.startsWith('text/') || ['txt', 'csv', 'html'].includes(extension)) {
                // Text preview
                fetch(fileUrl)
                    .then(response => response.text())
                    .then(text => {
                        previewContainer.innerHTML = `
                            <div style="height: 70vh; overflow: auto;">
                                <pre style="white-space: pre-wrap; word-break: break-word; padding: 1rem;">${text}</pre>
                            </div>`;
                    });
            } else if (['doc', 'docx', 'xls', 'xlsx'].includes(extension)) {
                // Office documents - use Google Docs Viewer
                const googleDocsUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + url)}&embedded=true`;
                previewContainer.innerHTML = `
                    <div style="height: 70vh;">
                        <iframe src="${googleDocsUrl}"
                                style="width: 100%; height: 100%; border: none;"
                                title="${fileName}">
                        </iframe>
                    </div>`;
            } else {
                // Unsupported file type
                previewContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This file type cannot be previewed. Please download to view.
                        <div class="mt-2">
                            <small class="text-muted">
                                File type: ${contentType}<br>
                                Extension: ${extension}
                            </small>
                        </div>
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            previewContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                    <div class="mt-2">
                        <small class="text-muted">
                            File: ${fileName}<br>
                            Extension: ${extension}
                        </small>
                    </div>
                </div>`;
        });
}

// Function to show success modal
function showSuccessModal(message) {
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    document.getElementById('successMessage').textContent = message;
    successModal.show();
}

// Function to show remarks update success modal
function showRemarksUpdateSuccessModal() {
    const remarksUpdateSuccessModal = new bootstrap.Modal(document.getElementById('remarksUpdateSuccessModal'));
    remarksUpdateSuccessModal.show();
}

// Function to show delete confirmation modal
function showDeleteConfirmationModal(reportTypeId) {
    const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    document.getElementById('confirmDeleteBtn').onclick = function() {
        // Handle delete action here
        deleteConfirmationModal.hide();
    };
    deleteConfirmationModal.show();
}
</script>
@endpush

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3" id="successMessage">Operation completed successfully!</h5>
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Remarks Update Success Modal -->
<div class="modal fade" id="remarksUpdateSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-2">Remarks Updated Successfully!</h5>
                <p class="text-muted mb-3">The remarks have been saved and the barangay has been notified.</p>
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <div class="d-flex align-items-center">
                    <div class="me-2 p-2 rounded-circle" id="fileIconContainer">
                        <i class="fas fa-file-alt" id="fileIcon"></i>
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
</div>
@endsection
