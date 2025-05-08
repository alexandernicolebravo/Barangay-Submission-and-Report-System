@extends('layouts.barangay')

@section('title', 'Submit Report')
@section('page-title', 'Submit Report')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Submit Report
                </h5>
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

                <form method="POST" action="{{ route('barangay.submissions.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_type" class="form-label fw-bold">
                                    <i class="fas fa-tasks me-2"></i>
                                    Report Type
                                </label>
                                <select id="report_type" class="form-select form-select-lg @error('report_type_id') is-invalid @enderror" name="report_type_id" required>
                                    <option value="">Select Report Type</option>
                                    @foreach($reportTypes as $reportType)
                                        <option value="{{ $reportType->id }}" data-frequency="{{ $reportType->frequency }}">
                                            {{ $reportType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('report_type_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file" class="form-label fw-bold">
                                    <i class="fas fa-file-upload me-2"></i>
                                    Upload File
                                </label>
                                <div class="drop-zone" id="dropZone">
                                    <div class="drop-zone__prompt">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                                        <p class="mb-2">Drag and drop your file here or click to browse</p>
                                        <small class="text-muted">Accepted formats: PDF, DOC, DOCX, XLSX (Max: 2MB)</small>
                                    </div>
                                    <input type="file" name="file" id="file" class="drop-zone__input" accept=".pdf,.doc,.docx,.xlsx" required>
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Report Fields -->
                    <div id="weekly-fields" class="report-fields" style="display: none;">
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
                                                <option value="{{ $month }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                        @error('month')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Week Number</label>
                                        <input type="number" class="form-control" name="week_number" min="1" max="52" required>
                                        @error('week_number')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
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
                                        <label class="form-label">Number of Clean-up Sites</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" required>
                                            <span class="input-group-text">sites</span>
                                        </div>
                                        @error('num_of_clean_up_sites')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Participants</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_participants" min="0" required>
                                            <span class="input-group-text">people</span>
                                        </div>
                                        @error('num_of_participants')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Barangays</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="num_of_barangays" min="0" required>
                                            <span class="input-group-text">barangays</span>
                                        </div>
                                        @error('num_of_barangays')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Total Volume</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="total_volume" min="0" step="0.01" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                        @error('total_volume')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Report Fields -->
                    <div id="monthly-fields" class="report-fields" style="display: none;">
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
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                    @error('month')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quarterly Report Fields -->
                    <div id="quarterly-fields" class="report-fields" style="display: none;">
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
                                        <option value="1">Q1 (January - March)</option>
                                        <option value="2">Q2 (April - June)</option>
                                        <option value="3">Q3 (July - September)</option>
                                        <option value="4">Q4 (October - December)</option>
                                    </select>
                                    @error('quarter_number')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Semestral Report Fields -->
                    <div id="semestral-fields" class="report-fields" style="display: none;">
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
                                        <option value="1">First Semester (January - June)</option>
                                        <option value="2">Second Semester (July - December)</option>
                                    </select>
                                    @error('sem_number')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('barangay.dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Drop Zone */
    .drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .drop-zone:hover {
        border-color: var(--accent-color);
        background-color: rgba(var(--accent-color-rgb), 0.05);
    }

    .drop-zone.dragover {
        border-color: var(--accent-color);
        background-color: rgba(var(--accent-color-rgb), 0.1);
    }

    .drop-zone__thumb {
        padding: 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Form Controls */
    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--accent-color-rgb), 0.25);
    }

    .input-group:focus-within .input-group-text {
        border-color: var(--accent-color);
        background-color: var(--accent-color);
        color: white;
    }

    /* Submit Button Animation */
    @keyframes submitPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .btn-primary:active {
        animation: submitPulse 0.3s ease;
    }

    /* Loading Spinner */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading .fa-spinner {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZoneElement = document.querySelector('.drop-zone');
    const inputElement = dropZoneElement.querySelector('.drop-zone__input');
    const reportTypeSelect = document.getElementById('report_type');
    const weeklyFields = document.getElementById('weekly-fields');
    const monthlyFields = document.getElementById('monthly-fields');
    const quarterlyFields = document.getElementById('quarterly-fields');
    const semestralFields = document.getElementById('semestral-fields');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.querySelector('form');

    // Handle file drop
    dropZoneElement.addEventListener('click', e => {
        inputElement.click();
    });

    inputElement.addEventListener('change', e => {
        if (inputElement.files.length) {
            updateThumbnail(dropZoneElement, inputElement.files[0]);
        }
    });

    dropZoneElement.addEventListener('dragover', e => {
        e.preventDefault();
        dropZoneElement.classList.add('dragover');
    });

    ['dragleave', 'dragend'].forEach(type => {
        dropZoneElement.addEventListener(type, e => {
            dropZoneElement.classList.remove('dragover');
        });
    });

    dropZoneElement.addEventListener('drop', e => {
        e.preventDefault();

        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
        }

        dropZoneElement.classList.remove('dragover');
    });

    function updateThumbnail(dropZoneElement, file) {
        let thumbnailElement = dropZoneElement.querySelector('.drop-zone__thumb');

        if (dropZoneElement.querySelector('.drop-zone__prompt')) {
            dropZoneElement.querySelector('.drop-zone__prompt').remove();
        }

        if (!thumbnailElement) {
            thumbnailElement = document.createElement('div');
            thumbnailElement.classList.add('drop-zone__thumb');
            dropZoneElement.appendChild(thumbnailElement);
        }

        thumbnailElement.dataset.label = file.name;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            };
        } else {
            thumbnailElement.style.backgroundImage = null;
        }
    }

    // Handle report type change
    reportTypeSelect.addEventListener('change', function() {
        const frequency = this.options[this.selectedIndex].dataset.frequency;

        // Hide all fields first
        [weeklyFields, monthlyFields, quarterlyFields, semestralFields].forEach(field => {
            field.style.display = 'none';
        });

        // Show relevant fields
        switch(frequency) {
            case 'weekly':
                weeklyFields.style.display = 'block';
                break;
            case 'monthly':
                monthlyFields.style.display = 'block';
                break;
            case 'quarterly':
                quarterlyFields.style.display = 'block';
                break;
            case 'semestral':
                semestralFields.style.display = 'block';
                break;
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        // Validate required fields
        const reportType = reportTypeSelect.value;
        const file = inputElement.files[0];

        if (!reportType) {
            e.preventDefault();
            alert('Please select a report type');
            return;
        }

        if (!file) {
            e.preventDefault();
            alert('Please upload a file');
            return;
        }

        // Check file size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
            e.preventDefault();
            alert('File size must be less than 2MB');
            return;
        }

        // Check file type
        const allowedTypes = ['.pdf', '.doc', '.docx', '.xlsx'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            e.preventDefault();
            alert('Invalid file type. Please upload a PDF, DOC, DOCX, or XLSX file');
            return;
        }

        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

        // Let the form submit naturally
        return true;
    });
});
</script>
@endpush
@endsection
