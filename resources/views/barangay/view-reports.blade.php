@extends('layouts.barangay')

@section('title', 'View Reports')
@section('page-title', 'View Reports')

@push('styles')
<style>
    .modern-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
    }

    .filter-controls {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
    }

    .modern-select, .modern-input {
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 0.5rem 0.75rem;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .modern-select:focus, .modern-input:focus {
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        color: white;
    }

    .modern-select option {
        background: #2d3748;
        color: white;
    }

    .modern-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .filter-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .filter-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-1px);
    }

    .clear-btn {
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid rgba(239, 68, 68, 0.3);
        color: white;
    }

    .clear-btn:hover {
        background: rgba(239, 68, 68, 0.3);
        border-color: rgba(239, 68, 68, 0.5);
        color: white;
    }

    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        font-weight: 600;
        color: #374151;
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table-modern tbody tr {
        border: none;
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .table-modern tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-approved {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .status-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .status-rejected {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .action-btn {
        border: none;
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-view {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-download {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }

    .btn-remarks {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .modern-select, .modern-input {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filter Reports
            </h5>
            <button class="filter-btn clear-btn" id="clearFilters">
                <i class="fas fa-times me-1"></i>
                Clear All
            </button>
        </div>

        <div class="filter-controls">
            <div class="filter-group">
                <label class="filter-label">Search Reports</label>
                <input type="text" id="searchInput" class="modern-input" placeholder="Search by report name..." style="min-width: 250px;">
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select id="statusFilter" class="modern-select">
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="submitted">Submitted</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Report Type</label>
                <select id="typeFilter" class="modern-select">
                    <option value="">All Types</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semestral">Semestral</option>
                    <option value="annual">Annual</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date Range</label>
                <input type="date" id="dateFrom" class="modern-input" style="width: 150px;">
            </div>

            <div class="filter-group">
                <label class="filter-label">&nbsp;</label>
                <input type="date" id="dateTo" class="modern-input" style="width: 150px;">
            </div>

            <div class="filter-group">
                <label class="filter-label">&nbsp;</label>
                <button class="filter-btn" id="applyFilters">
                    <i class="fas fa-search me-1"></i>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-bottom: 2px solid #e5e7eb;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: #374151; font-weight: 600;">
                            <i class="fas fa-file-alt me-2" style="color: #6366f1;"></i>
                            My Reports
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary" id="reportCount">{{ $reports->total() }} reports</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($reports->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h6 class="mb-2">No reports found</h6>
                            <p class="mb-3">You haven't submitted any reports yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-modern mb-0" id="reportsTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <i class="fas fa-file-alt me-1"></i>
                                            Report Details
                                        </th>
                                        <th>
                                            <i class="fas fa-calendar me-1"></i>
                                            Submission Date
                                        </th>
                                        <th>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Status
                                        </th>
                                        <th>
                                            <i class="fas fa-cogs me-1"></i>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr class="report-row"
                                            data-status="{{ $report->status }}"
                                            data-type="{{ strtolower($report->reportType->frequency ?? '') }}"
                                            data-name="{{ strtolower($report->reportType->name ?? '') }}"
                                            data-date="{{ $report->created_at->format('Y-m-d') }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="report-icon me-3">
                                                        @php
                                                            $frequency = strtolower($report->reportType->frequency ?? '');
                                                            $iconData = match($frequency) {
                                                                'weekly' => ['icon' => 'fa-calendar-week', 'color' => '#3b82f6'],
                                                                'monthly' => ['icon' => 'fa-calendar-alt', 'color' => '#10b981'],
                                                                'quarterly' => ['icon' => 'fa-calendar-check', 'color' => '#f59e0b'],
                                                                'semestral' => ['icon' => 'fa-calendar-plus', 'color' => '#06b6d4'],
                                                                'annual' => ['icon' => 'fa-calendar', 'color' => '#ef4444'],
                                                                default => ['icon' => 'fa-file', 'color' => '#6b7280']
                                                            };
                                                        @endphp
                                                        <i class="fas {{ $iconData['icon'] }}" style="color: {{ $iconData['color'] }}; font-size: 1.25rem;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $report->reportType->name }}</div>
                                                        <small class="text-muted">{{ ucfirst($report->reportType->frequency ?? 'Unknown') }} Report</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark fw-medium">{{ $report->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($report->status) {
                                                        'approved' => 'status-approved',
                                                        'pending' => 'status-pending',
                                                        'rejected' => 'status-rejected',
                                                        'submitted' => 'status-pending',
                                                        default => 'status-pending'
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($report->file_path)
                                                        <button type="button"
                                                                class="action-btn btn-view"
                                                                title="View File"
                                                                onclick="previewFile('{{ route('barangay.files.download', $report->id) }}', '{{ basename($report->file_path) }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('barangay.files.download', $report->id) }}?download=true"
                                                           class="action-btn btn-download"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                    @if($report->remarks)
                                                        <button type="button"
                                                                class="action-btn btn-remarks"
                                                                title="View Remarks"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#remarksModal{{ $report->id }}">
                                                            <i class="fas fa-comment"></i>
                                                        </button>
                                                    @endif
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2">
                            <div class="text-muted small">
                                Showing {{ ($reports->currentPage() - 1) * $reports->perPage() + 1 }} to {{ min($reports->currentPage() * $reports->perPage(), $reports->total()) }} of {{ $reports->total() }} entries
                            </div>
                            <div class="pagination-wrapper">
                                {{ $reports->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Remarks Modals -->
    @foreach($reports as $report)
        @if($report->remarks)
            <div class="modal fade" id="remarksModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-comment me-2"></i>
                                Remarks for {{ $report->reportType->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ $report->remarks }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if(!$reports->isEmpty())
    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 98vw !important; width: 98vw !important; margin: 1vh auto;">
            <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); height: 98vh;">
                <div class="modal-header bg-light" style="flex-shrink: 0; padding: 0.75rem 1.5rem;">
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
                <div class="modal-body p-0 bg-light" style="height: calc(98vh - 120px); overflow: hidden;">
                    <div id="previewContainer" class="w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: #f8f9fa;">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Loading document preview...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="flex-shrink: 0; padding: 0.75rem 1.5rem;">
                    <div class="d-flex align-items-center text-muted me-auto small">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>If the document doesn't load correctly, please use the download button.</span>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        /* Fix pagination overlapping issues */
        .pagination-wrapper {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-height: 40px;
        }

        .pagination-wrapper .pagination {
            margin: 0;
            flex-wrap: nowrap;
        }

        .pagination-wrapper .pagination .page-item {
            margin: 0 2px;
        }

        .pagination-wrapper .pagination .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            color: #6c757d;
            background-color: #fff;
            transition: all 0.15s ease-in-out;
        }

        .pagination-wrapper .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #495057;
        }

        .pagination-wrapper .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .pagination-wrapper .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            opacity: 0.5;
        }

        /* Responsive pagination */
        @media (max-width: 768px) {
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                gap: 1rem;
            }

            .pagination-wrapper {
                justify-content: center;
                width: 100%;
            }

            .pagination-wrapper .pagination .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        /* Table responsive improvements */
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .table-modern {
            margin-bottom: 0;
        }

        .table-modern thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Fix card body padding */
        .card-body.p-0 {
            padding: 0 !important;
        }

        .card-body.p-0 .d-flex.justify-content-between.align-items-center {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            margin-top: 0;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter elements
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const reportRows = document.querySelectorAll('.report-row');
            const reportCount = document.getElementById('reportCount');

            // Filter function
            function filterReports() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();
                const typeValue = typeFilter.value.toLowerCase();
                const dateFrom = dateFromInput.value;
                const dateTo = dateToInput.value;

                let visibleCount = 0;

                reportRows.forEach(row => {
                    const reportName = row.dataset.name || '';
                    const reportStatus = row.dataset.status || '';
                    const reportType = row.dataset.type || '';
                    const reportDate = row.dataset.date || '';

                    // Search filter
                    const matchesSearch = !searchTerm || reportName.includes(searchTerm) ||
                                        row.textContent.toLowerCase().includes(searchTerm);

                    // Status filter
                    const matchesStatus = !statusValue || reportStatus === statusValue;

                    // Type filter
                    const matchesType = !typeValue || reportType === typeValue;

                    // Date range filter
                    let matchesDate = true;
                    if (dateFrom && reportDate < dateFrom) matchesDate = false;
                    if (dateTo && reportDate > dateTo) matchesDate = false;

                    // Show/hide row
                    const shouldShow = matchesSearch && matchesStatus && matchesType && matchesDate;
                    row.style.display = shouldShow ? '' : 'none';

                    if (shouldShow) visibleCount++;
                });

                // Update count
                reportCount.textContent = `${visibleCount} reports`;
            }

            // Clear filters function
            function clearFilters() {
                searchInput.value = '';
                statusFilter.value = '';
                typeFilter.value = '';
                dateFromInput.value = '';
                dateToInput.value = '';
                filterReports();
            }

            // Event listeners
            searchInput.addEventListener('input', filterReports);
            statusFilter.addEventListener('change', filterReports);
            typeFilter.addEventListener('change', filterReports);
            dateFromInput.addEventListener('change', filterReports);
            dateToInput.addEventListener('change', filterReports);
            applyFiltersBtn.addEventListener('click', filterReports);
            clearFiltersBtn.addEventListener('click', clearFilters);

            // Real-time search
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterReports, 300);
            });
        });

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
                        // PDF preview - Advanced viewer with thumbnails and controls
                        previewContainer.style.padding = '0';
                        previewContainer.innerHTML = `
                            <embed src="${fileUrl}"
                                   type="application/pdf"
                                   style="width: 100%; height: 100%; border: none;"
                                   title="${fileName}">
                            </embed>`;
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
                                    <i class="fas fa-download me-2"></i> Download File
                                </a>
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
    </script>
    @endpush
@endsection
