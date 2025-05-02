<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Report Types</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            padding: 20px 0;
            color: white;
        }

        .sidebar .nav-link {
            color: white;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--secondary-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--accent-color);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h4>Admin Panel</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="{{ route('admin.create-report') }}">
                        <i class="fas fa-file-alt me-2"></i> Create Report Type
                    </a>
                    <a class="nav-link" href="{{ route('view.submissions') }}">
                        <i class="fas fa-list me-2"></i> View Submissions
                    </a>
                    <a class="nav-link" href="{{ route('admin.create-report') }}">
                        <i class="fas fa-chart-bar me-2"></i> Report Types
                    </a>
                    <a class="nav-link" href="{{ route('barangay.submissions') }}">
                        <i class="fas fa-building me-2"></i> Barangay Submissions
                    </a>
                    <a class="nav-link" href="{{ route('barangay.overdue-reports') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i> Overdue Reports
                    </a>
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>Manage Report Types</h2>
                        <hr>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Report Type Creation Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create New Report Type</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.store-report') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="frequency" class="form-label">Frequency</label>
                                    <select class="form-select" id="frequency" name="frequency" required>
                                        @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $frequency)
                                            <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Create Report Type</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Type List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Existing Report Types</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Frequency</th>
                                        <th>Deadline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportTypes as $reportType)
                                    <tr>
                                        <td>{{ $reportType->name }}</td>
                                        <td>{{ ucfirst($reportType->frequency) }}</td>
                                        <td>{{ $reportType->deadline ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="openEditModal(
                                                '{{ $reportType->id }}',
                                                '{{ $reportType->name }}',
                                                '{{ $reportType->frequency }}',
                                                '{{ $reportType->deadline }}'
                                            )">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="{{ route('admin.destroy-report', $reportType->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Report Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFrequency" class="form-label">Frequency</label>
                            <select class="form-select" id="editFrequency" name="frequency" required>
                                @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $frequency)
                                    <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDeadline" class="form-label">Deadline</label>
                            <input type="date" class="form-control" id="editDeadline" name="deadline">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Report Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(id, name, frequency, deadline) {
            document.getElementById('editForm').action = "{{ route('admin.update-report', '') }}/" + id;
            document.getElementById('editName').value = name;
            document.getElementById('editFrequency').value = frequency;
            document.getElementById('editDeadline').value = deadline;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html>
