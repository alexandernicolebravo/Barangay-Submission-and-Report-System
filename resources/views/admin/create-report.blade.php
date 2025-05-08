@extends('admin.layouts.app')

@section('title', 'Create Report Type')

@push('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .table th {
        background: var(--light);
        font-weight: 600;
    }

    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }

    .search-box {
        border-radius: 0.375rem;
    }

    .search-box:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    }

    .is-invalid {
        border-color: var(--danger) !important;
    }

    .invalid-feedback {
        display: block;
        color: var(--danger);
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    /* Delete Modal Styles */
    .delete-icon-container {
        width: 80px;
        height: 80px;
        background-color: var(--danger-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .delete-icon-container i {
        font-size: 2rem;
        color: var(--danger);
    }

    #deleteReportModal .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #deleteReportModal .modal-body {
        padding: 2rem;
    }

    #deleteReportModal .modal-footer {
        padding: 0 2rem 2rem;
    }

    #deleteReportModal .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    #deleteReportModal .btn-light {
        background-color: var(--light);
        border-color: var(--border-color);
        color: var(--gray-700);
    }

    #deleteReportModal .btn-light:hover {
        background-color: var(--gray-200);
    }

    #deleteReportModal .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }

    #deleteReportModal .btn-danger:hover {
        background-color: var(--danger-dark);
        border-color: var(--danger-dark);
    }

    #deleteReportModal .text-muted {
        color: var(--gray-600) !important;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title">
            <i class="fas fa-file-alt"></i>
            Report Types Management
        </h2>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-file-alt me-2" style="color: var(--primary);"></i>
            Report Types
        </h5>
        <div class="d-flex gap-2">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control search-box" id="reportTypeSearch" placeholder="Search...">
            </div>
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-filter"></i>
                </span>
                <select class="form-select" id="frequencyFilter">
                    <option value="">All Frequencies</option>
                    @foreach(App\Models\ReportType::frequencies() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportTypeModal">
                <i class="fas fa-plus"></i>
                <span>Add Report Type</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Frequency</th>
                        <th>Deadline</th>
                        <th>Allowed File Types</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportTypes as $reportType)
                    <tr data-frequency="{{ $reportType->frequency }}" data-name="{{ strtolower($reportType->name) }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $reportType->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--info-light); color: var(--info);">
                                {{ App\Models\ReportType::frequencies()[$reportType->frequency] }}
                            </span>
                        </td>
                        <td style="color: var(--gray-600);">{{ $reportType->deadline->format('M d, Y') }}</td>
                        <td>
                            @if($reportType->allowed_file_types)
                                @foreach($reportType->allowed_file_types as $type)
                                    <span class="badge me-1" style="background: var(--primary-light); color: var(--primary);">
                                        {{ strtoupper($type) }}
                                    </span>
                                @endforeach
                            @else
                                <span style="color: var(--gray-600);">No restrictions</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#editReportTypeModal{{ $reportType->id }}">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button type="button" class="btn btn-sm" style="background: var(--danger-light); color: var(--danger); border: none;" data-bs-toggle="modal" data-bs-target="#deleteReportModal" data-report-id="{{ $reportType->id }}" data-report-name="{{ $reportType->name }}">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Report Type Modal -->
                    <div class="modal fade" id="editReportTypeModal{{ $reportType->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit me-2" style="color: var(--primary);"></i>
                                        Edit Report Type
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.update-report', $reportType->id) }}" method="POST" id="editForm{{ $reportType->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $reportType->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Frequency</label>
                                            <select class="form-select @error('frequency') is-invalid @enderror" name="frequency" required>
                                                @foreach(App\Models\ReportType::frequencies() as $key => $value)
                                                    <option value="{{ $key }}" {{ old('frequency', $reportType->frequency) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deadline</label>
                                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ old('deadline', $reportType->deadline->format('Y-m-d')) }}" required>
                                            @error('deadline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Allowed File Types</label>
                                            <div class="row">
                                                @foreach(App\Models\ReportType::availableFileTypes() as $key => $value)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input @error('allowed_file_types') is-invalid @enderror" type="checkbox" name="allowed_file_types[]" value="{{ $key }}"
                                                                {{ in_array($key, old('allowed_file_types', $reportType->allowed_file_types ?? [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                {{ $value }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('allowed_file_types')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            <span>Save Changes</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Report Type Modal -->
<div class="modal fade" id="createReportTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2" style="color: var(--primary);"></i>
                    Add New Report Type
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.store-report') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <select class="form-select @error('frequency') is-invalid @enderror" name="frequency" required>
                            @foreach(App\Models\ReportType::frequencies() as $key => $value)
                                <option value="{{ $key }}" {{ old('frequency') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="date" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ old('deadline') }}" required>
                        @error('deadline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Allowed File Types</label>
                        <div class="row">
                            @foreach(App\Models\ReportType::availableFileTypes() as $key => $value)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input @error('allowed_file_types') is-invalid @enderror" type="checkbox" name="allowed_file_types[]" value="{{ $key }}" {{ in_array($key, old('allowed_file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {{ $value }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('allowed_file_types')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Create Report Type</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteReportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <div class="delete-icon-container">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
                <h5 class="mb-3">Delete Report Type</h5>
                <p class="text-muted mb-0">Are you sure you want to delete this report type? This action cannot be undone and all associated reports will be affected.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('reportTypeSearch');
    const frequencyFilter = document.getElementById('frequencyFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const selectedFrequency = frequencyFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const name = row.dataset.name;
            const frequency = row.dataset.frequency;

            const matchesSearch = name.includes(searchText);
            const matchesFrequency = !selectedFrequency || frequency === selectedFrequency;

            row.style.display = matchesSearch && matchesFrequency ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    frequencyFilter.addEventListener('change', filterTable);

    // Form validation and submission
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                alert('Please fill in all required fields.');
                return;
            }

            // Submit the form
            form.submit();
        });
    });

    // Reset form on modal close
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                const invalidFields = form.querySelectorAll('.is-invalid');
                invalidFields.forEach(field => field.classList.remove('is-invalid'));
            }
        });
    });
});

function showDeleteConfirmation(reportId, reportName) {
    const modal = new bootstrap.Modal(document.getElementById('deleteReportModal'));

    // Update the confirmation message with the report name
    const message = document.querySelector('#deleteReportModal .text-muted');
    message.textContent = `Are you sure you want to delete "${reportName}"? This action cannot be undone and all associated reports will be affected.`;

    // Set up the delete button action
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        // Send delete request
        fetch(`/admin/create-report/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                modal.hide();
                // Show success message
                showSuccessMessage('Report type deleted successfully');
                // Reload the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to delete report type');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage(error.message);
        });
    };

    modal.show();
}

function showSuccessMessage(message) {
    // You can implement this function to show a success toast or alert
    alert(message); // Replace with your preferred notification system
}

function showErrorMessage(message) {
    // You can implement this function to show an error toast or alert
    alert(message); // Replace with your preferred notification system
}
</script>
@endpush
@endsection
