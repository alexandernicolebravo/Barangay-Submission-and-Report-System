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
                                        @php
                                            $reportId = $report->unique_id ?? $report->id;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2" style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                                        @php
                                                            $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                                            $icon = match($extension) {
                                                                'pdf' => 'fa-file-pdf',
                                                                'docx' => 'fa-file-word',
                                                                'doc' => 'fa-file-word',
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
                                                <span class="badge bg-info">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ ucfirst($report->reportType->frequency) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-nowrap">{{ $report->created_at->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                                    @if($report->updated_at && $report->updated_at->ne($report->created_at))
                                                    <small class="text-success mt-1">
                                                        <i class="fas fa-sync-alt me-1"></i> Updated
                                                    </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($report->status === 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Approved
                                                    </span>
                                                @elseif($report->status === 'rejected')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Rejected
                                                    </span>
                                                @elseif($report->status === 'submitted')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-check me-1"></i>
                                                        Submitted
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($report->remarks)
                                                    <button type="button"
                                                            class="btn btn-link btn-sm p-0"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#remarksModal{{ $reportId }}">
                                                        <i class="fas fa-comment-alt text-primary me-1"></i>
                                                        View Remarks
                                                    </button>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewFileModal{{ $reportId }}">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View
                                                    </button>
                                                    @if($report->status === 'rejected' || $report->can_update)
                                                        <button type="button"
                                                                class="btn btn-sm {{ $report->status === 'rejected' ? 'btn-outline-warning' : 'btn-outline-secondary' }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#updateModal{{ $reportId }}">
                                                            <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-edit' }} me-1"></i>
                                                            {{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Update/Resubmit Modal -->
                                        <div class="modal fade" id="updateModal{{ $reportId }}" tabindex="-1" aria-labelledby="updateModalLabel{{ $reportId }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateModalLabel{{ $reportId }}">
                                                            <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-edit' }} me-2"></i>
                                                            {{ $report->status === 'rejected' ? 'Resubmit Report' : 'Update Report' }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if($report->status === 'rejected' && $report->remarks)
                                                        <div class="alert alert-warning">
                                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Admin Remarks:</h6>
                                                            <p class="mb-0">{{ $report->remarks }}</p>
                                                        </div>
                                                        @endif

                                                        <form action="{{ route('barangay.submissions.resubmit', $reportId) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">

                                                            <div class="mb-3">
                                                                <label for="file{{ $reportId }}" class="form-label">Select New File</label>
                                                                <input type="file" class="form-control" id="file{{ $reportId }}" name="file" required>
                                                                <div class="form-text">Accepted formats: PDF, DOCX, XLS, XLSX, JPG, PNG (Max: 100MB)</div>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-2">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn {{ $report->status === 'rejected' ? 'btn-warning' : 'btn-primary' }}">
                                                                    <i class="fas {{ $report->status === 'rejected' ? 'fa-redo' : 'fa-upload' }} me-1"></i>
                                                                    {{ $report->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- View File Modal -->
                                        <div class="modal fade" id="viewFileModal{{ $reportId }}" tabindex="-1" aria-labelledby="viewFileModalLabel{{ $reportId }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width: 98vw !important; width: 98vw !important; margin: 1vh auto;">
                                                <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); height: 98vh;">
                                                    <div class="modal-header bg-light" style="flex-shrink: 0; padding: 0.75rem 1.5rem;">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1);">
                                                                <i class="fas fa-file-alt fa-lg text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="modal-title mb-0 fw-bold" id="viewFileModalLabel{{ $reportId }}">
                                                                    {{ $report->reportType->name }}
                                                                </h5>
                                                                <div class="text-muted small">Document Preview</div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('barangay.direct.files.download', $reportId) }}?download=true" class="btn btn-primary me-2">
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body p-0 bg-light" style="height: calc(98vh - 120px); overflow: hidden;">
                                                        <div id="filePreviewContainer{{ $reportId }}" class="w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: #f8f9fa;">
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

                                        <!-- Remarks Modal -->
                                        @if($report->remarks)
                                        <div class="modal fade" id="remarksModal{{ $reportId }}" tabindex="-1" aria-labelledby="remarksModalLabel{{ $reportId }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="remarksModalLabel{{ $reportId }}">
                                                            <i class="fas fa-comment-alt me-2"></i>
                                                            Remarks
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <h6><i class="fas fa-info-circle me-2"></i>Admin/Facilitator Remarks:</h6>
                                                            <p class="mb-0">{{ $report->remarks }}</p>
                                                        </div>
                                                        @if($report->can_update)
                                                        <div class="alert alert-success">
                                                            <i class="fas fa-check-circle me-2"></i>
                                                            You can resubmit this report with the necessary changes.
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        @if($report->can_update)
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#updateModal{{ $reportId }}">
                                                            <i class="fas fa-upload me-1"></i>Resubmit Report
                                                        </button>
                                                        @endif
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($reports->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $reports->links() }}
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-submit filter form on change
        document.getElementById('frequencyFilter')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('sortBy')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Search with debounce
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });

        // File preview functionality
        document.addEventListener('show.bs.modal', function(event) {
            const modal = event.target;
            console.log('Modal opening:', modal.id);
            if (modal.id && modal.id.startsWith('viewFileModal')) {
                const reportId = modal.id.replace('viewFileModal', '');
                console.log('Loading preview for report ID:', reportId);
                loadFilePreview(reportId);
            }
        });

        // Add ESC key functionality to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    const modal = bootstrap.Modal.getInstance(openModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            }
        });
    });

    function loadFilePreview(reportId) {
        console.log('loadFilePreview called with reportId:', reportId);
        const container = document.getElementById('filePreviewContainer' + reportId);
        const fileUrl = '{{ route("barangay.direct.files.download", ":reportId") }}'.replace(':reportId', reportId);

        console.log('Container found:', !!container);
        console.log('File URL:', fileUrl);

        if (!container) {
            console.error('Container not found for reportId:', reportId);
            return;
        }

        // Show loading spinner
        container.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading file preview...</p>
                </div>
            </div>
        `;

        // Try to load the file
        fetch(fileUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('File not found or access denied');
                }

                const contentType = response.headers.get('content-type');
                const contentDisposition = response.headers.get('content-disposition');

                // Check if it's a PDF
                if (contentType && contentType.includes('application/pdf')) {
                    return response.blob().then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        container.style.padding = '0';
                        container.innerHTML = `
                            <embed src="${blobUrl}"
                                   type="application/pdf"
                                   style="width: 100%; height: 100%; border: none;"
                                   title="PDF Preview">
                            </embed>
                        `;
                    });
                }

                // Check if it's an image
                if (contentType && contentType.startsWith('image/')) {
                    return response.blob().then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        container.innerHTML = `
                            <div class="d-flex align-items-center justify-content-center h-100 p-3">
                                <img src="${blobUrl}"
                                     class="img-fluid"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain;"
                                     alt="Image Preview">
                            </div>
                        `;
                    });
                }

                // For other file types, show download option
                throw new Error('Preview not available for this file type');
            })
            .catch(error => {
                console.error('Preview error:', error);
                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fas fa-file fa-3x text-muted mb-3"></i>
                            <h5>Preview Not Available</h5>
                            <p class="text-muted mb-3">${error.message}</p>
                            <a href="${fileUrl}?download=true" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i>Download to View
                            </a>
                        </div>
                    </div>
                `;
            });
    }
</script>
@endpush
