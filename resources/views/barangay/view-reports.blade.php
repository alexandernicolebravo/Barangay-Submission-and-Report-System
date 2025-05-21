@extends('layouts.barangay')

@section('title', 'View Reports')
@section('page-title', 'View Reports')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Reports</h5>
                        <div class="d-flex gap-2">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search reports...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <li><a class="dropdown-item" href="#" data-filter="all">All Reports</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="approved">Approved</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="pending">Pending</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="rejected">Rejected</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($reports->isEmpty())
                        <p class="text-muted">No reports found</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Report Type</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr class="report-row" data-status="{{ $report->status }}">
                                            <td>{{ $report->reportType->name }}</td>
                                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($report->file_path)
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary"
                                                            title="View File"
                                                            onclick="previewFile('{{ route('barangay.files.download', $report->id) }}', '{{ basename($report->file_path) }}')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('barangay.files.download', $report->id) }}?download=true" class="btn btn-sm btn-info" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                                @if($report->remarks)
                                                    <button class="btn btn-sm btn-secondary" title="View Remarks" data-bs-toggle="modal" data-bs-target="#remarksModal{{ $report->id }}">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($report->remarks)
                                            <div class="modal fade" id="remarksModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Remarks</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ $report->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ ($reports->currentPage() - 1) * $reports->perPage() + 1 }} to {{ min($reports->currentPage() * $reports->perPage(), $reports->total()) }} of {{ $reports->total() }} entries
                            </div>
                            <div>
                                {{ $reports->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                <div class="modal-body d-flex justify-content-center align-items-center p-3" style="min-height: 400px;">
                    <div id="previewContainer" class="d-flex justify-content-center align-items-center w-100">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const filterLinks = document.querySelectorAll('[data-filter]');
            const reportRows = document.querySelectorAll('.report-row');

            function filterReports() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('.dropdown-item.active')?.dataset.filter || 'all';

                reportRows.forEach(row => {
                    const reportText = row.textContent.toLowerCase();
                    const reportStatus = row.dataset.status;
                    const matchesSearch = reportText.includes(searchTerm);
                    const matchesFilter = activeFilter === 'all' || reportStatus === activeFilter;

                    row.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterReports);
            searchButton.addEventListener('click', filterReports);

            filterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    filterLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    filterReports();
                });
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
