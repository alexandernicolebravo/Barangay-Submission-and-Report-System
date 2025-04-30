<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
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
        .report-type-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .report-type-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .report-type-card h5 {
            color: #0d6efd;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .report-type-card .deadline {
            color: #dc3545;
            font-size: 0.9em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .submitted-reports {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .report-status {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 15px;
        }
        .file-upload-container {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .file-upload-container:hover {
            border-color: #0d6efd;
            background: #f0f7ff;
        }
        .file-upload-container.dragover {
            border-color: #0d6efd;
            background: #e6f0ff;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 6px;
            padding: 10px 15px;
        }
        .btn-submit {
            padding: 10px 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
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
            <a class="nav-link active" href="{{ route('barangay.submit-report') }}">
                <i class="bi bi-plus-circle"></i> Submit New Report
            </a>
            <a class="nav-link" href="{{ route('barangay.view-reports') }}">
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
                <h2>Submit Report</h2>
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

            @if ($allReportTypes->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No report types available.
                </div>
            @else
                <div class="form-container">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h3>Select Report Type</h3>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="#weekly-reports" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar-week"></i> Weekly Reports
                                </a>
                                <a href="#monthly-reports" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar-month"></i> Monthly Reports
                                </a>
                                <a href="#quarterly-reports" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar3"></i> Quarterly Reports
                                </a>
                                <a href="#semestral-reports" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar2"></i> Semestral Reports
                                </a>
                                <a href="#annual-reports" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar"></i> Annual Reports
                                </a>
                            </div>
                        </div>
                    </div>

                    @foreach ($allReportTypes->groupBy('frequency') as $frequency => $types)
                        <div class="mb-5" id="{{ $frequency }}-reports">
                            <h3 class="mb-4">{{ ucfirst($frequency) }} Reports</h3>
                            @if($types->where('deadline', '>', now())->isEmpty())
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    No active {{ $frequency }} reports available at the moment.
                                </div>
                            @else
                                @foreach ($types as $reportType)
                                    @if (!Carbon\Carbon::parse($reportType->deadline)->isPast())
                                        @if($submittedReportTypeIds->contains($reportType->id))
                                            <div class="report-type-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5>{{ $reportType->name }}</h5>
                                                    <span class="badge bg-success">Already Submitted</span>
                                                </div>
                                                <p class="deadline">
                                                    <i class="bi bi-calendar-check"></i>
                                                    Deadline: {{ $reportType->deadline }}
                                                </p>
                                                <div class="mt-3">
                                                    <a href="{{ route('barangay.submissions') }}" class="btn btn-outline-primary">
                                                        <i class="bi bi-arrow-right-circle"></i> View/Resubmit Report
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="report-type-card">
                                                <h5>{{ $reportType->name }}</h5>
                                                <p class="deadline">
                                                    <i class="bi bi-calendar-check"></i>
                                                    Deadline: {{ $reportType->deadline }}
                                                </p>

                                                <form action="{{ route('barangay.store-report') }}" method="POST" enctype="multipart/form-data" class="report-form">
                                                    @csrf
                                                    <input type="hidden" name="report_type_id" value="{{ $reportType->id }}">

                                                    <div class="row">
                                                        @if ($frequency === 'weekly')
                                                            <div class="col-md-6 mb-3">
                                                                <label for="month_{{ $reportType->id }}" class="form-label">Month</label>
                                                                <select name="month" class="form-select" required>
                                                                    <option value="">Select Month</option>
                                                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                        <option value="{{ $month }}">{{ $month }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="week_number_{{ $reportType->id }}" class="form-label">Week Number</label>
                                                                <input type="number" name="week_number" class="form-control" min="1" max="52" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="num_of_clean_up_sites_{{ $reportType->id }}" class="form-label">Number of Clean-up Sites</label>
                                                                <input type="number" name="num_of_clean_up_sites" class="form-control" min="0" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="num_of_participants_{{ $reportType->id }}" class="form-label">Number of Participants</label>
                                                                <input type="number" name="num_of_participants" class="form-control" min="0" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="num_of_barangays_{{ $reportType->id }}" class="form-label">Number of Barangays Involved</label>
                                                                <input type="number" name="num_of_barangays" class="form-control" min="0" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="total_volume_{{ $reportType->id }}" class="form-label">Total Volume (kg/lbs)</label>
                                                                <input type="number" name="total_volume" class="form-control" min="0" step="0.01" required>
                                                            </div>
                                                        @elseif ($frequency === 'monthly')
                                                            <div class="col-md-6 mb-3">
                                                                <label for="month_{{ $reportType->id }}" class="form-label">Month</label>
                                                                <select name="month" class="form-select" required>
                                                                    <option value="">Select Month</option>
                                                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                        <option value="{{ $month }}">{{ $month }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @elseif ($frequency === 'quarterly')
                                                            <div class="col-md-6 mb-3">
                                                                <label for="quarter_number_{{ $reportType->id }}" class="form-label">Quarter Number</label>
                                                                <select name="quarter_number" class="form-select" required>
                                                                    <option value="">Select Quarter</option>
                                                                    <option value="1">1st Quarter</option>
                                                                    <option value="2">2nd Quarter</option>
                                                                    <option value="3">3rd Quarter</option>
                                                                    <option value="4">4th Quarter</option>
                                                                </select>
                                                            </div>
                                                        @elseif ($frequency === 'semestral')
                                                            <div class="col-md-6 mb-3">
                                                                <label for="sem_number_{{ $reportType->id }}" class="form-label">Semester Number</label>
                                                                <select name="sem_number" class="form-select" required>
                                                                    <option value="">Select Semester</option>
                                                                    <option value="1">1st Semester</option>
                                                                    <option value="2">2nd Semester</option>
                                                                </select>
                                                            </div>
                                                        @endif

                                                        <div class="col-12 mb-3">
                                                            <label for="file_{{ $reportType->id }}" class="form-label">Upload Report</label>
                                                            <div class="file-upload-container" id="dropZone_{{ $reportType->id }}">
                                                                <input type="file" name="file" class="d-none" id="fileInput_{{ $reportType->id }}" required>
                                                                <div class="text-center">
                                                                    <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                                                                    <p class="mb-2">Drag and drop your file here or</p>
                                                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput_{{ $reportType->id }}').click()">
                                                                        Browse Files
                                                                    </button>
                                                                    <p class="mt-2 text-muted small">Accepted formats: PDF, DOC, DOCX, XLSX (Max size: 2MB)</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary btn-submit">
                                                        <i class="bi bi-upload"></i> Submit {{ $reportType->name }}
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach

                    <div class="submitted-reports">
                        <h3 class="mb-4">Recently Submitted Reports</h3>
                        @foreach ($submittedReportsByFrequency as $frequency => $reports)
                            @if($reports->isNotEmpty())
                                <h4 class="mb-3">{{ ucfirst($frequency) }} Reports</h4>
                                @foreach($reports as $report)
                                    <div class="report-type-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1">{{ $report->reportType->name }}</h5>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i>
                                                    Submitted on: {{ $report->created_at->format('M d, Y H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </div>
                                        @if($report->remarks)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-chat-left-text"></i>
                                                    Remarks: {{ $report->remarks }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
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
