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
                                                    <a href="{{ route('barangay.files.download', $report->id) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip"
                                                       title="Download Report">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($report->status === 'rejected')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#resubmitModal{{ $report->id }}"
                                                                data-bs-toggle="tooltip"
                                                                title="Resubmit Report">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Resubmit Modal -->
                                        <div class="modal fade" id="resubmitModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Resubmit Report</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('barangay.submissions.resubmit', $report->id) }}" method="POST" enctype="multipart/form-data" id="resubmitForm{{ $report->id }}">
                                                            @csrf
                                                            <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">
                                                            <input type="hidden" name="report_type" value="{{ $report->type }}">

                                                            <div class="mb-3">
                                                                <label for="file" class="form-label">Upload New Report</label>
                                                                <div class="file-upload-container" id="dropZone{{ $report->id }}">
                                                                    <input type="file" name="file" class="d-none" id="fileInput{{ $report->id }}" required accept=".pdf,.doc,.docx,.xlsx">
                                                                    <div class="text-center p-4 border rounded">
                                                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                                                        <p class="mb-2">Drag and drop your file here or</p>
                                                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput{{ $report->id }}').click()">
                                                                            Browse Files
                                                                        </button>
                                                                        <p class="mt-2 text-muted small">Accepted formats: PDF, DOC, DOCX, XLSX (Max size: 2MB)</p>
                                                                        <div id="fileInfo{{ $report->id }}" class="mt-2 d-none">
                                                                            <p class="mb-0"><strong>Selected file:</strong> <span id="fileName{{ $report->id }}"></span></p>
                                                                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearFile({{ $report->id }})">
                                                                                <i class="fas fa-times"></i> Remove
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="text-end">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary" id="submitBtn{{ $report->id }}">
                                                                    <i class="fas fa-upload me-2"></i>Resubmit
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

                fileInput.value = '';
                fileInfo.classList.add('d-none');
                submitBtn.disabled = true;
            }

            // Form submission handling
            document.getElementById('resubmitForm{{ $report->id }}').addEventListener('submit', function(e) {
                const fileInput = document.getElementById('fileInput{{ $report->id }}');
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Please select a file to upload');
                }
            });
        });
    </script>
    @endpush
@endsection
