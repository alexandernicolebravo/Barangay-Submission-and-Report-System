<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Reports</title>
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
        .overdue-badge {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .form-container {
            max-width: 900px;
            margin: 0 auto;
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
            <a class="nav-link" href="{{ route('barangay.submissions') }}">
                <i class="bi bi-list-ul"></i> View Reports
            </a>
            <a class="nav-link active" href="{{ route('barangay.overdue-reports') }}">
                <i class="bi bi-exclamation-triangle"></i> Overdue Reports
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Overdue Reports</h2>
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

            @if ($overdueReports->isEmpty())
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    No overdue reports found.
                </div>
            @else
                <div class="form-container">
                    @foreach ($overdueReports->groupBy('frequency') as $frequency => $types)
                        <div class="mb-5">
                            <h3 class="mb-4">{{ ucfirst($frequency) }} Reports</h3>
                            @foreach ($types as $reportType)
                                <div class="report-type-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>{{ $reportType->name }}</h5>
                                        <span class="overdue-badge">Overdue</span>
                                    </div>
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
                            @endforeach
                        </div>
                    @endforeach
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
