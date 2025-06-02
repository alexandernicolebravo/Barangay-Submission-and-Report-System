@extends('admin.layouts.app')

@section('title', 'View Submissions')

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

            // Get file extension and set appropriate icon
            const extension = fileName.split('.').pop().toLowerCase();
            const fileTypeIcon = document.getElementById('fileTypeIcon');
            const fileIconElement = fileTypeIcon.querySelector('i');

            // Set icon and color based on file type
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

            // Update icon class and background color
            fileIconElement.className = `fas ${iconClass} fa-lg text-${bgColorClass}`;
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
                    if (contentType === 'application/pdf' || extension === 'pdf') {
                        // PDF preview
                        previewContainer.innerHTML = `
                            <div class="bg-white rounded shadow-sm" style="width: 95%; height: 65vh;">
                                <iframe src="${fileUrl}"
                                        style="width: 100%; height: 100%; border: none; border-radius: 0.375rem;"
                                        title="${fileName}">
                                </iframe>
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

        // Force scroll to top on page load
        window.scrollTo(0, 0);

        // Clear any scroll position from sessionStorage
        Object.keys(sessionStorage).forEach(key => {
            if (key.startsWith('scrollPos_')) {
                sessionStorage.removeItem(key);
            }
        });

        // For links that navigate to different pages
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                // Force scroll to top before navigation
                window.scrollTo(0, 0);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Get all filter elements
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.querySelector('input[name="search"]');
            const filterSelects = document.querySelectorAll('select');
            const tableContainer = document.querySelector('.table-responsive');
            const paginationContainer = document.querySelector('.pagination-container');
            const clusterSelect = document.querySelector('select[name="cluster_id"]');
            const barangaySelect = document.querySelector('select[name="barangay_id"]');

            // Store original barangay options
            const originalBarangayOptions = Array.from(barangaySelect.options);

            // Function to update barangay options based on cluster selection
            const updateBarangayOptions = (clusterId) => {
                // Clear current options except "All Barangays"
                barangaySelect.innerHTML = '<option value="">All Barangays</option>';

                if (clusterId) {
                    // Fetch barangays for the selected cluster
                    fetch(`/admin/get-barangays-by-cluster/${clusterId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        data.barangays.forEach(barangay => {
                            const option = new Option(barangay.name, barangay.id);
                            barangaySelect.add(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching barangays:', error);
                        // Restore original options on error
                        restoreOriginalBarangayOptions();
                    });
                } else {
                    // Restore all original options
                    restoreOriginalBarangayOptions();
                }
            };

            // Function to restore original barangay options
            const restoreOriginalBarangayOptions = () => {
                barangaySelect.innerHTML = '';
                originalBarangayOptions.forEach(option => {
                    barangaySelect.add(option.cloneNode(true));
                });
            };

            // Function to handle AJAX requests
            const handleAjaxRequest = (params) => {
                // Show loading state
                if (tableContainer) {
                    tableContainer.style.opacity = '0.5';
                }

                fetch(`${filterForm.action}?${new URLSearchParams(params).toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = html;

                    // Update table content
                    const newTableContainer = tempContainer.querySelector('.table-responsive');
                    if (newTableContainer && tableContainer) {
                        tableContainer.innerHTML = newTableContainer.innerHTML;
                    }

                    // Update pagination
                    const newPagination = tempContainer.querySelector('.pagination-container');
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }

                    // Re-initialize any interactive elements
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        new bootstrap.Tooltip(el);
                    });

                    // Restore opacity
                    if (tableContainer) {
                        tableContainer.style.opacity = '1';
                    }
                })
                .catch(error => {
                    console.error('Filter request failed:', error);
                    if (tableContainer) {
                        tableContainer.style.opacity = '1';
                    }
                });
            };

            // Function to get all filter values
            const getFilterValues = () => {
                return {
                    search: searchInput.value.trim(),
                    barangay_id: document.querySelector('select[name="barangay_id"]').value,
                    type: document.querySelector('select[name="type"]').value,
                    cluster_id: document.querySelector('select[name="cluster_id"]')?.value || '',
                    timeliness: document.querySelector('select[name="timeliness"]').value,
                    ajax: true
                };
            };

            // Add event listener for cluster selection
            clusterSelect.addEventListener('change', function() {
                const clusterId = this.value;

                // Update barangay options
                updateBarangayOptions(clusterId);

                // Reset barangay selection
                barangaySelect.value = '';

                // Apply filter
                handleAjaxRequest(getFilterValues());
            });

            // Add event listener for search input
            searchInput.addEventListener('input', function() {
                handleAjaxRequest(getFilterValues());
            });

            // Add event listeners for other select elements (excluding cluster)
            filterSelects.forEach(select => {
                if (select !== clusterSelect) {
                    select.addEventListener('change', function() {
                        handleAjaxRequest(getFilterValues());
                    });
                }
            });

            // Handle pagination clicks
            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    const url = e.target.closest('.pagination a').href;
                    const urlParams = new URLSearchParams(url.split('?')[1]);
                    const page = urlParams.get('page');

                    if (page) {
                        const filterParams = getFilterValues();
                        filterParams.page = page;
                        handleAjaxRequest(filterParams);
                    }
                }
            });
        });
    </script>
@endpush

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

    .timeliness-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
        margin-left: 0.5rem;
    }

    .timeliness-badge.late {
        background-color: var(--danger-light);
        color: var(--danger);
    }

    .timeliness-badge.ontime {
        background-color: var(--success-light);
        color: var(--success);
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

    .modal .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }

    .modal .btn-light {
        background-color: var(--light);
        border-color: var(--border-color);
    }

    .modal .text-success {
        color: var(--success) !important;
    }

    .modal .text-warning {
        color: var(--warning) !important;
    }

    /* Submission Info Cards */
    .submission-info-card {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        width: 48%;
        transition: all 0.2s ease;
    }

    .submission-info-card:hover {
        background-color: #f0f0f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .submission-info-card:hover .info-icon {
        transform: scale(1.1);
    }

    .bg-primary-light { background-color: rgba(var(--primary-rgb), 0.1); }
    .bg-success-light { background-color: rgba(var(--success-rgb), 0.1); }
    .bg-danger-light { background-color: rgba(var(--danger-rgb), 0.1); }
    .bg-warning-light { background-color: rgba(var(--warning-rgb), 0.1); }
    .bg-info-light { background-color: rgba(var(--info-rgb), 0.1); }

    .text-primary { color: var(--primary); }
    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }
    .text-warning { color: var(--warning); }
    .text-info { color: var(--info); }

    .info-content {
        flex: 1;
        min-width: 0;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 500;
        color: #212529;
    }

    .file-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .file-section:hover {
        background-color: #f0f0f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .remarks-section {
        height: 100%;
        border-radius: 0.5rem;
        background-color: rgba(var(--primary-rgb), 0.03);
        border: 1px solid rgba(var(--primary-rgb), 0.1);
        padding: 1.5rem;
    }

    .current-remarks {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
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

        <form id="filterForm" class="d-flex flex-wrap gap-2" method="GET" action="{{ route('admin.view.submissions') }}">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control search-box" name="search" value="{{ request('search') }}" placeholder="Search...">
            </div>

            <!-- Cluster Filter -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text">
                    <i class="fas fa-layer-group"></i>
                </span>
                <select class="form-select" name="cluster_id">
                    <option value="">All Clusters</option>
                    @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                        <option value="{{ $cluster->id }}" {{ request('cluster_id') == $cluster->id ? 'selected' : '' }}>
                            {{ $cluster->name }}
                        </option>
                    @endforeach
                </select>
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

            <!-- Report Type Filter -->
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-file-alt"></i>
                </span>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="semestral" {{ request('type') == 'semestral' ? 'selected' : '' }}>Semestral</option>
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
            @if(request()->hasAny(['search', 'barangay_id', 'type', 'cluster_id', 'timeliness']))
                <a href="{{ route('admin.view.submissions') }}" class="btn btn-light">
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
                            <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
                                <div class="modal-header bg-light">
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

                                        $statusIcon = match($report->status) {
                                            'submitted' => 'fa-check-circle',
                                            'no submission' => 'fa-times-circle',
                                            'pending' => 'fa-clock',
                                            'approved' => 'fa-thumbs-up',
                                            'rejected' => 'fa-thumbs-down',
                                            default => 'fa-info-circle'
                                        };
                                        $statusClass = str_replace(' ', '-', $report->status);
                                        $isLate = \Carbon\Carbon::parse($report->updated_at)->isAfter($report->reportType->deadline);
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 p-2 rounded-circle" style="background-color: rgba(var(--{{ $colorClass }}-rgb), 0.1);">
                                            <i class="fas {{ $iconClass }} text-{{ $colorClass }}"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title mb-0 fw-bold">{{ $report->reportType->name }}</h5>
                                            <div class="text-muted small">{{ ucfirst(str_replace('Report', '', class_basename($report->model_type))) }} Report</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <div class="row g-3">
                                        <!-- File Information Section -->
                                        <div class="col-12">
                                            <div class="card border-0 bg-light-subtle mb-3">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2">
                                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                                        Submitted File
                                                    </h6>
                                                    <div class="d-flex align-items-center p-3 bg-white rounded border">
                                                        <div class="me-3 p-2 rounded" style="background-color: rgba(var(--{{ $colorClass }}-rgb), 0.1);">
                                                            <i class="fas {{ $iconClass }} fa-lg text-{{ $colorClass }}"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-0 fw-medium">{{ $fileName }}</p>
                                                            <small class="text-muted">Last updated on {{ \Carbon\Carbon::parse($report->updated_at)->format('M d, Y h:i A') }}</small>
                                                            <span class="badge bg-{{ $isLate ? 'danger' : 'success' }} ms-2">
                                                                {{ $isLate ? 'Late' : 'On Time' }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                onclick="previewFile('{{ route('admin.files.download', ['id' => $report->unique_id]) }}', '{{ $fileName }}')">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </button>
                                                            <a href="{{ route('admin.files.download', ['id' => $report->unique_id, 'download' => true]) }}"
                                                               class="btn btn-sm btn-outline-secondary ms-1">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submission Details -->
                                        <div class="col-md-6">
                                            <div class="card bg-light h-100">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2">
                                                        <i class="fas fa-info-circle me-2 text-primary"></i>
                                                        Submission Details
                                                    </h6>
                                                    <div class="list-group list-group-flush">
                                                        <div class="list-group-item bg-transparent px-0 py-2 border-0 border-bottom">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted">Submitted By:</span>
                                                                <span class="fw-medium">{{ $report->user->name }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="list-group-item bg-transparent px-0 py-2 border-0 border-bottom">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted">Status:</span>
                                                                <span class="status-badge {{ $statusClass }}">
                                                                    <i class="fas {{ $statusIcon }}"></i>
                                                                    {{ ucfirst($report->status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="list-group-item bg-transparent px-0 py-2 border-0 border-bottom">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted">Last Updated:</span>
                                                                <span>{{ \Carbon\Carbon::parse($report->updated_at)->format('M d, Y h:i A') }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="list-group-item bg-transparent px-0 py-2 border-0">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted">Deadline:</span>
                                                                <span>{{ \Carbon\Carbon::parse($report->reportType->deadline)->format('M d, Y h:i A') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks Section -->
                                        <div class="col-md-6">
                                            <div class="card bg-light h-100">
                                                <div class="card-body p-3">
                                                    <form action="{{ route('admin.update.report', $report->unique_id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="type" value="{{ strtolower(str_replace('Report', '', class_basename($report->model_type))) }}">

                                                        <h6 class="card-title mb-2 d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <i class="fas fa-comment-alt me-2 text-primary"></i>
                                                                Remarks
                                                            </span>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="enableUpdate{{ $report->unique_id }}"
                                                                       name="can_update"
                                                                       value="1"
                                                                       {{ $report->can_update ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="enableUpdate{{ $report->unique_id }}">
                                                                    Allow Barangay to Update
                                                                </label>
                                                            </div>
                                                        </h6>

                                                        <textarea class="form-control form-control-sm bg-white border"
                                                                id="remarks{{ $report->unique_id }}"
                                                                name="remarks"
                                                                rows="5"
                                                                placeholder="Enter your remarks or feedback here...">{{ $report->remarks }}</textarea>

                                                        <div class="d-flex justify-content-end mt-3">
                                                            <button type="submit" id="saveRemarks{{ $report->unique_id }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-save me-1"></i>
                                                                Save Changes
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Report-Specific Details -->
                                        <div class="col-12 mt-3">
                                            <div class="card bg-light">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-2">
                                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                        Report Details
                                                    </h6>

                                                    @php
                                                        $reportType = strtolower(str_replace('Report', '', class_basename($report->model_type)));
                                                    @endphp

                                                    @if($reportType == 'weekly')
                                                    <div class="row g-2 mt-2">
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Month</div>
                                                                <div class="fw-medium">{{ $report->month }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Week Number</div>
                                                                <div class="fw-medium">{{ $report->week_number }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Clean-up Sites</div>
                                                                <div class="fw-medium">{{ $report->num_of_clean_up_sites }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Participants</div>
                                                                <div class="fw-medium">{{ $report->num_of_participants }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Barangays</div>
                                                                <div class="fw-medium">{{ $report->num_of_barangays }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Total Volume (m³)</div>
                                                                <div class="fw-medium">{{ $report->total_volume }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @elseif($reportType == 'monthly')
                                                    <div class="row g-2 mt-2">
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Month</div>
                                                                <div class="fw-medium">{{ $report->month }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Year</div>
                                                                <div class="fw-medium">{{ $report->year ?? date('Y') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @elseif($reportType == 'quarterly')
                                                    <div class="row g-2 mt-2">
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Quarter</div>
                                                                <div class="fw-medium">
                                                                    @switch($report->quarter_number)
                                                                        @case(1)
                                                                            Q1 (Jan-Mar)
                                                                            @break
                                                                        @case(2)
                                                                            Q2 (Apr-Jun)
                                                                            @break
                                                                        @case(3)
                                                                            Q3 (Jul-Sep)
                                                                            @break
                                                                        @case(4)
                                                                            Q4 (Oct-Dec)
                                                                            @break
                                                                        @default
                                                                            Quarter {{ $report->quarter_number }}
                                                                    @endswitch
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Year</div>
                                                                <div class="fw-medium">{{ $report->year }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @elseif($reportType == 'semestral')
                                                    <div class="row g-2 mt-2">
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Semester</div>
                                                                <div class="fw-medium">
                                                                    {{ $report->sem_number == 1 ? '1st Sem (Jan-Jun)' : '2nd Sem (Jul-Dec)' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Year</div>
                                                                <div class="fw-medium">{{ $report->year }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @elseif($reportType == 'annual')
                                                    <div class="row g-2 mt-2">
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 bg-white">
                                                                <div class="small text-muted">Year</div>
                                                                <div class="fw-medium">{{ $report->year }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- File Preview Modal will be created dynamically by JavaScript -->
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
        @if($reports->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="pagination-info">
                Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
                        </div>
            <div class="pagination-container">
                {{ $reports->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-3">Success!</h5>
                <p class="text-muted mb-0" id="successMessage">The operation was completed successfully.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-3">Confirm Deletion</h5>
                <p class="text-muted mb-0">Are you sure you want to delete this report type? This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remarks Update Success Modal -->
<div class="modal fade" id="remarksUpdateSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-3">Remarks Saved</h5>
                <p class="text-muted mb-0">The report remarks have been saved successfully.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
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

    // Add animation to the remarks textarea
    const remarksTextareas = document.querySelectorAll('textarea[name="remarks"]');
    remarksTextareas.forEach(textarea => {
        textarea.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 0.25rem rgba(13, 110, 253, 0.25)';
            this.style.borderColor = '#86b7fe';
        });

        textarea.addEventListener('blur', function() {
            this.style.boxShadow = '';
            this.style.borderColor = '';
        });
    });
});

// File preview function
function previewFile(url, fileName) {
    console.log('previewFile called with:', { url, fileName });

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

    // Get file extension and set appropriate icon
    const extension = fileName.split('.').pop().toLowerCase();
    const fileTypeIcon = document.getElementById('fileTypeIcon');
    const fileIconElement = fileTypeIcon.querySelector('i');

    // Set icon and color based on file type
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

    // Update icon class and background color
    fileIconElement.className = `fas ${iconClass} fa-lg text-${bgColorClass}`;
    fileTypeIcon.style.backgroundColor = `rgba(var(--${bgColorClass}-rgb), 0.1)`;

    // Show the modal using jQuery (compatible with our modal setup)
    $('#filePreviewModal').modal('show');

    // Fetch the file with proper headers
    console.log('Fetching file from URL:', url);
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: File not found or access denied`);
            }

            // Check if response is JSON (from AJAX request)
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    console.log('JSON response data:', data);
                    if (data.success) {
                        console.log('Fetching file from URL:', data.file_url);
                        // Use the file URL from the JSON response
                        return fetch(data.file_url).then(fileResponse => {
                            console.log('File response status:', fileResponse.status);
                            if (!fileResponse.ok) {
                                throw new Error(`File fetch failed: ${fileResponse.status} - ${fileResponse.statusText}`);
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
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                url: url,
                fileName: fileName
            });
            previewContainer.innerHTML = `
                <div class="bg-white rounded shadow-sm p-4 text-center" style="max-width: 500px;">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-exclamation-circle fa-4x mb-3"></i>
                        <h5 class="mb-3">Error Loading File</h5>
                        <p class="text-muted mb-4">${error.message}</p>
                        <p class="text-muted small">URL: ${url}</p>
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

// Function to show success modal
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    $('#successModal').modal('show');
}

// Function to show remarks update success modal
function showRemarksUpdateSuccessModal() {
    $('#remarksUpdateSuccessModal').modal('show');
}

// Function to show delete confirmation modal
function showDeleteConfirmationModal(reportTypeId) {
    document.getElementById('confirmDeleteBtn').onclick = function() {
        // Handle delete action here
        $('#deleteConfirmationModal').modal('hide');
    };
    $('#deleteConfirmationModal').modal('show');
}


</script>
@endpush
<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
