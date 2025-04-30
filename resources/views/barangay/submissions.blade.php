<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Reports</title>
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
            transition: all 0.3s ease;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
            transform: translateX(5px);
        }
        .report-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .file-upload-container {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            margin-top: 15px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }
            .sidebar.active {
                width: 250px;
                padding: 20px;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.sidebar-active {
                margin-left: 250px;
            }
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
            <a class="nav-link active" href="{{ route('barangay.submissions') }}">
                <i class="bi bi-list-ul"></i> View Reports
            </a>
            <a class="nav-link" href="{{ route('barangay.overdue-reports') }}">
                <i class="bi bi-exclamation-triangle"></i> Overdue Reports
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Submitted Reports</h2>
                <button class="btn btn-outline-primary d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($reports->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    No reports have been submitted yet.
                </div>
            @else
                @foreach ($reports as $report)
                    <div class="report-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">{{ $report->reportType->name }}</h5>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    Submitted on: {{ $report->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                            <span class="status-badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>

                        @if($report->remarks)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-chat-left-text"></i>
                                    Remarks: {{ $report->remarks }}
                                </small>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('barangay.files.download', $report->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i> Download Report
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#resubmitModal{{ $report->id }}">
                                <i class="bi bi-arrow-clockwise"></i> Resubmit
                            </button>
                        </div>

                        <!-- Resubmit Modal -->
                        <div class="modal fade" id="resubmitModal{{ $report->id }}" tabindex="-1" aria-labelledby="resubmitModalLabel{{ $report->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="resubmitModalLabel{{ $report->id }}">Resubmit Report</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('barangay.submissions.resubmit', $report->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="report_type_id" value="{{ $report->report_type_id }}">

                                            <div class="mb-3">
                                                <label for="file" class="form-label">Upload New Report</label>
                                                <div class="file-upload-container" id="dropZone{{ $report->id }}">
                                                    <input type="file" name="file" class="d-none" id="fileInput{{ $report->id }}" required>
                                                    <div class="text-center">
                                                        <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                                                        <p class="mb-2">Drag and drop your file here or</p>
                                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput{{ $report->id }}').click()">
                                                            Browse Files
                                                        </button>
                                                        <p class="mt-2 text-muted small">Accepted formats: PDF, DOC, DOCX, XLSX (Max size: 2MB)</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-upload"></i> Resubmit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('sidebar-active');
        });

        // File upload drag and drop functionality
        document.querySelectorAll('.file-upload-container').forEach(container => {
            const fileInput = container.querySelector('input[type="file"]');

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
                fileInput.files = e.dataTransfer.files;
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    container.querySelector('p').textContent = fileInput.files[0].name;
                }
            });
        });
    </script>
</body>
</html>
