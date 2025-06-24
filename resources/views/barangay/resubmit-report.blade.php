<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resubmit Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #343a40;
            padding: 20px;
            color: white;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="sidebar">
        <h4 class="mb-4">Report Management</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('barangay.submit-report') }}">
                <i class="bi bi-plus-circle"></i> Submit New Report
            </a>
            <a class="nav-link" href="{{ route('barangay.submissions') }}">
                <i class="bi bi-list-ul"></i> View Reports
            </a>
            <a class="nav-link" href="{{ route('barangay.dashboard') }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="form-container">
                <h2 class="mb-4">Resubmit Report</h2>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Current Report Details</h5>
                        <p class="mb-1"><strong>Report Type:</strong> {{ $report->reportType->name }}</p>
                        <p class="mb-1"><strong>Current File:</strong> {{ $report->file_name }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </p>
                        @if($report->remarks)
                            <p class="mb-1"><strong>Remarks:</strong> {{ $report->remarks }}</p>
                        @endif
                    </div>
                </div>

                <form action="{{ route('barangay.submissions.resubmit', $report->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Weekly Report Fields -->
                    @if($report->reportType->frequency === 'weekly')
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
                                                <option value="{{ $month }}" {{ (isset($report->month) && $report->month === $month) ? 'selected' : '' }}>{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Week Number <span class="text-danger">*</span></label>
                                        <select class="form-select" name="week_number" required>
                                            <option value="">Select Week</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ (($report->week_number ?? '') == $i) ? 'selected' : '' }}>Week {{ $i }}</option>
                                            @endfor
                                        </select>
                                        <small class="form-text text-muted">Choose week or type custom week number (limit: 5 weeks)</small>
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
                                        <label class="form-label">Number of Clean-up Sites <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" value="{{ $report->num_of_clean_up_sites ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Participants <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="num_of_participants" min="0" value="{{ $report->num_of_participants ?? '' }}" required>
                                        <small class="form-text text-muted">Do NOT include barangay officials and staff</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Barangay and/or LGU Officials Participated <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="num_of_barangays" min="0" value="{{ $report->num_of_barangays ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Total Volume of Wastes Collected (in Kilograms) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="total_volume" min="0" step="0.01" value="{{ $report->total_volume ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Monthly Report Fields -->
                    @if($report->reportType->frequency === 'monthly')
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
                                            <option value="{{ $month }}" {{ (isset($report->month) && $report->month === $month) ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quarterly Report Fields -->
                    @if($report->reportType->frequency === 'quarterly')
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-4">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Report Period
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Quarter</label>
                                    <select class="form-select" name="quarter_number" required>
                                        <option value="">Select Quarter</option>
                                        <option value="1" {{ (isset($report->quarter_number) && $report->quarter_number == 1) ? 'selected' : '' }}>First Quarter (January - March)</option>
                                        <option value="2" {{ (isset($report->quarter_number) && $report->quarter_number == 2) ? 'selected' : '' }}>Second Quarter (April - June)</option>
                                        <option value="3" {{ (isset($report->quarter_number) && $report->quarter_number == 3) ? 'selected' : '' }}>Third Quarter (July - September)</option>
                                        <option value="4" {{ (isset($report->quarter_number) && $report->quarter_number == 4) ? 'selected' : '' }}>Fourth Quarter (October - December)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Semestral Report Fields -->
                    @if($report->reportType->frequency === 'semestral')
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-4">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Report Period
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Semester</label>
                                    <select class="form-select" name="sem_number" required>
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ (isset($report->sem_number) && $report->sem_number == 1) ? 'selected' : '' }}>First Semester (January - June)</option>
                                        <option value="2" {{ (isset($report->sem_number) && $report->sem_number == 2) ? 'selected' : '' }}>Second Semester (July - December)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Current File Display -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-file-alt me-2"></i>
                                Current File
                            </h6>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-white">
                                <div class="d-flex align-items-center">
                                    @php
                                        $extension = strtolower(pathinfo($report->file_name, PATHINFO_EXTENSION));
                                        $iconClass = match($extension) {
                                            'pdf' => 'fa-file-pdf text-danger',
                                            'doc', 'docx' => 'fa-file-word text-primary',
                                            'xls', 'xlsx' => 'fa-file-excel text-success',
                                            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-info',
                                            default => 'fa-file text-secondary'
                                        };
                                    @endphp
                                    <i class="fas {{ $iconClass }} fa-2x me-3"></i>
                                    <div>
                                        <div class="fw-medium">{{ $report->file_name }}</div>
                                        <small class="text-muted">Uploaded on {{ $report->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('barangay.direct.files.download', $report->id) }}?download=true"
                                       class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick="previewCurrentFile('{{ route('barangay.direct.files.download', $report->id) }}', '{{ $report->file_name }}')">
                                        <i class="fas fa-eye me-1"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload New Report File</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX, XLSX, JPG, PNG (Max size: 25MB)</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('barangay.submissions') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Resubmit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 98vw !important; width: 98vw !important; margin: 1vh auto;">
            <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); height: 98vh;">
                <div class="modal-header bg-light" style="flex-shrink: 0; padding: 0.75rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <div class="me-3 p-2 rounded-circle" style="background-color: rgba(var(--primary-rgb), 0.1);">
                            <i class="fas fa-file-alt fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="previewFileName">Document Preview</h5>
                            <div class="text-muted small">Current Report File</div>
                        </div>
                    </div>
                    <div>
                        <a id="downloadLink" href="#" class="btn btn-primary me-2">
                            <i class="fas fa-download me-1"></i>Download
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewCurrentFile(url, fileName) {
            // Set the file name in the modal
            document.getElementById('previewFileName').textContent = fileName;

            // Set the download link
            const downloadLink = document.getElementById('downloadLink');
            downloadLink.href = url + '?download=true';

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            modal.show();

            // Load the file preview
            const previewContainer = document.getElementById('previewContainer');

            // Reset container
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

            if (extension === 'pdf') {
                // For PDF files, use embed
                previewContainer.innerHTML = `
                    <embed src="${url}"
                           type="application/pdf"
                           width="100%"
                           height="100%"
                           style="border: none;">
                `;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                // For images
                previewContainer.innerHTML = `
                    <img src="${url}"
                         alt="${fileName}"
                         style="max-width: 100%; max-height: 100%; object-fit: contain;">
                `;
            } else {
                // For other file types, show a message
                previewContainer.innerHTML = `
                    <div class="text-center p-5">
                        <i class="fas fa-file fa-5x text-muted mb-3"></i>
                        <h5 class="text-muted">Preview not available</h5>
                        <p class="text-muted">This file type cannot be previewed in the browser.</p>
                        <p class="text-muted">Please use the download button to view the file.</p>
                    </div>
                `;
            }
        }

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
    </script>
</body>
</html>
