@extends('layouts.barangay')

@section('title', 'Submit Report')
@section('page-title', 'Submit Report')

@section('content')
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="mb-0">Your report has been submitted successfully!</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Submit New Report</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('barangay.store-report') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf
                        <div class="mb-3">
                            <label for="report_type_id" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type_id" name="report_type_id" required>
                                <option value="">Select Report Type</option>
                                @foreach($reportTypes as $type)
                                    <option value="{{ $type->id }}" {{ in_array($type->id, $submittedReportTypeIds->toArray()) ? 'disabled' : '' }}>
                                        {{ $type->name }} ({{ ucfirst($type->frequency) }})
                                        @if(in_array($type->id, $submittedReportTypeIds->toArray()))
                                            - Already Submitted
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Weekly Report Fields -->
                        <div id="weeklyFields" class="d-none">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="num_of_clean_up_sites" class="form-label">Number of Clean-up Sites</label>
                                    <input type="number" class="form-control" id="num_of_clean_up_sites" name="num_of_clean_up_sites" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="num_of_participants" class="form-label">Number of Participants</label>
                                    <input type="number" class="form-control" id="num_of_participants" name="num_of_participants" min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="num_of_barangays" class="form-label">Number of Barangays</label>
                                    <input type="number" class="form-control" id="num_of_barangays" name="num_of_barangays" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="total_volume" class="form-label">Total Volume</label>
                                    <input type="number" class="form-control" id="total_volume" name="total_volume" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Report Fields -->
                        <div id="monthlyFields" class="d-none">
                            <div class="mb-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-select" id="month" name="month">
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                        </div>

                        <!-- Quarterly Report Fields -->
                        <div id="quarterlyFields" class="d-none">
                            <div class="mb-3">
                                <label for="quarter_number" class="form-label">Quarter Number</label>
                                <select class="form-select" id="quarter_number" name="quarter_number">
                                    <option value="1">First Quarter</option>
                                    <option value="2">Second Quarter</option>
                                    <option value="3">Third Quarter</option>
                                    <option value="4">Fourth Quarter</option>
                                </select>
                            </div>
                        </div>

                        <!-- Semestral Report Fields -->
                        <div id="semestralFields" class="d-none">
                            <div class="mb-3">
                                <label for="sem_number" class="form-label">Semester Number</label>
                                <select class="form-select" id="sem_number" name="sem_number">
                                    <option value="1">First Semester</option>
                                    <option value="2">Second Semester</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">Report File</label>
                            <div class="file-upload-container" id="dropZone">
                                <input type="file" class="d-none" id="file" name="file" accept=".pdf,.doc,.docx,.xlsx" required>
                                <div class="text-center p-4 border rounded">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <p class="mb-2">Drag and drop your file here or</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file').click()">
                                        Browse Files
                                    </button>
                                    <p class="mt-2 text-muted small">Accepted formats: PDF, DOC, DOCX, XLSX (Max size: 2MB)</p>
                                    <div id="fileInfo" class="mt-2 d-none">
                                        <p class="mb-0"><strong>Selected file:</strong> <span id="fileName"></span></p>
                                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearFile()">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('barangay.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i> Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Submitted Reports</h5>
                </div>
                <div class="card-body">
                    @foreach($submittedReportsByFrequency as $frequency => $reports)
                        @if($reports->isNotEmpty())
                            <h6 class="text-muted">{{ ucfirst($frequency) }} Reports</h6>
                            @foreach($reports as $report)
                                <div class="report-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6>{{ $report->reportType->name }}</h6>
                                        <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-0">
                                        <small>Submitted: {{ $report->created_at->format('M d, Y') }}</small>
                                    </p>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
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
        .report-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('file');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const reportForm = document.getElementById('reportForm');
            const reportTypeSelect = document.getElementById('report_type_id');
            const weeklyFields = document.getElementById('weeklyFields');
            const monthlyFields = document.getElementById('monthlyFields');
            const quarterlyFields = document.getElementById('quarterlyFields');
            const semestralFields = document.getElementById('semestralFields');

            // Show/hide fields based on report type
            reportTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const frequency = selectedOption.textContent.match(/\((.*?)\)/)[1].toLowerCase();

                // Hide all fields first
                weeklyFields.classList.add('d-none');
                monthlyFields.classList.add('d-none');
                quarterlyFields.classList.add('d-none');
                semestralFields.classList.add('d-none');

                // Show relevant fields
                switch(frequency) {
                    case 'weekly':
                        weeklyFields.classList.remove('d-none');
                        break;
                    case 'monthly':
                        monthlyFields.classList.remove('d-none');
                        break;
                    case 'quarterly':
                        quarterlyFields.classList.remove('d-none');
                        break;
                    case 'semestral':
                        semestralFields.classList.remove('d-none');
                        break;
                }
            });

            // Show success modal if there's a success message
            @if(session('success'))
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif

            // Drag and drop functionality
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileInfo(files[0]);
                }
            });

            // File input change handler
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    updateFileInfo(e.target.files[0]);
                }
            });

            function updateFileInfo(file) {
                fileName.textContent = file.name;
                fileInfo.classList.remove('d-none');
            }

            window.clearFile = function() {
                fileInput.value = '';
                fileInfo.classList.add('d-none');
            };

            // Form submission
            reportForm.addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';
            });
        });
    </script>
    @endpush
@endsection
