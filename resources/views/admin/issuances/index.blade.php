@extends('admin.layouts.app')

@section('title', 'Issuances Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-alt me-2"></i>
                    Issuances Management
                </h1>
                @if(!request('archived'))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIssuanceModal">
                        <i class="fas fa-plus me-2"></i>
                        Upload New Issuance
                    </button>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>
                            {{ request('archived') ? 'Archived Issuances' : 'Active Issuances' }}
                        </h6>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.issuances.index') }}"
                               class="btn btn-sm {{ !request('archived') ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-list me-1"></i>
                                Active
                            </a>
                            <a href="{{ route('admin.issuances.index', ['archived' => 'true']) }}"
                               class="btn btn-sm {{ request('archived') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-archive me-1"></i>
                                Archived
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($issuances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>File Name</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issuances as $issuance)
                                        <tr>
                                            <td>
                                                <strong>{{ $issuance->title }}</strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-file me-2 text-muted"></i>
                                                {{ $issuance->file_name ?: 'Unknown' }}
                                            </td>
                                            <td>{{ $issuance->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="viewFile({{ $issuance->id }}, '{{ $issuance->title }}', '{{ $issuance->file_path }}')"
                                                            title="View File">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('admin.issuances.download', $issuance) }}"
                                                       class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if(request('archived'))
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                                onclick="confirmUnarchive({{ $issuance->id }})" title="Unarchive">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                onclick="reuploadFile({{ $issuance->id }}, '{{ $issuance->title }}')"
                                                                title="Reupload">
                                                            <i class="fas fa-upload"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                onclick="confirmArchive({{ $issuance->id }})" title="Archive">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $issuances->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-{{ request('archived') ? 'archive' : 'file-alt' }} fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                {{ request('archived') ? 'No archived issuances found' : 'No active issuances found' }}
                            </h5>
                            <p class="text-muted">
                                @if(request('archived'))
                                    There are currently no archived issuances.
                                @else
                                    Upload your first issuance to get started.
                                @endif
                            </p>
                            @if(!request('archived'))
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIssuanceModal">
                                    <i class="fas fa-plus me-2"></i>
                                    Upload New Issuance
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Issuance Modal -->
<div class="modal fade" id="createIssuanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>
                    Upload New Issuance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.issuances.store') }}" method="POST" enctype="multipart/form-data" id="createIssuanceForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-2"></i>
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       placeholder="Enter issuance title..."
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-4">
                                <label for="file" class="form-label">
                                    <i class="fas fa-file me-2"></i>
                                    File <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="file"
                                       name="file"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Maximum file size: 20MB. Allowed formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, JPEG, PNG, ZIP, RAR
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Upload Guidelines
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Maximum file size: 20MB
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Multiple file formats supported
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Files will be accessible to all barangays
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Use descriptive titles for easy identification
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>
                        Upload Issuance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View File Modal -->
<div class="modal fade" id="viewFileModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    <span id="viewFileTitle">View File</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fileViewer" style="height: 600px; width: 100%;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading file...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadFileBtn" href="#" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Reupload File Modal -->
<div class="modal fade" id="reuploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>
                    Reupload File
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reuploadForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are reuploading the issuance. You can update both the title and file.
                    </div>

                    <div class="mb-4">
                        <label for="reuploadTitle" class="form-label">
                            <i class="fas fa-heading me-2"></i>
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="reuploadTitleInput"
                               name="title"
                               placeholder="Enter issuance title..."
                               required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-4">
                        <label for="reuploadFile" class="form-label">
                            <i class="fas fa-file me-2"></i>
                            New File <span class="text-danger">*</span>
                        </label>
                        <input type="file"
                               class="form-control"
                               id="reuploadFile"
                               name="file"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                               required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Maximum file size: 20MB. This will replace the existing file.
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-upload me-2"></i>
                        Reupload File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Archive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive this issuance? Archived issuances will no longer be visible to barangay users.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Unarchive Confirmation Modal -->
<div class="modal fade" id="unarchiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Unarchive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to unarchive this issuance? It will become visible to barangay users again.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="unarchiveForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">Unarchive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// View File Modal
function viewFile(issuanceId, title, filePath) {
    document.getElementById('viewFileTitle').textContent = title;
    document.getElementById('downloadFileBtn').href = `/admin/issuances/${issuanceId}/download`;

    const fileViewer = document.getElementById('fileViewer');
    const fileUrl = `/storage/${filePath}`;
    const fileExtension = filePath.split('.').pop().toLowerCase();

    // Clear previous content
    fileViewer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">Loading file...</p></div>';

    if (['pdf'].includes(fileExtension)) {
        // For PDF files, use iframe
        fileViewer.innerHTML = `<iframe src="${fileUrl}" style="width: 100%; height: 600px; border: none;"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        // For images
        fileViewer.innerHTML = `<img src="${fileUrl}" style="max-width: 100%; max-height: 600px; object-fit: contain;" class="d-block mx-auto">`;
    } else if (['txt'].includes(fileExtension)) {
        // For text files, fetch and display content
        fetch(fileUrl)
            .then(response => response.text())
            .then(text => {
                fileViewer.innerHTML = `<pre style="white-space: pre-wrap; max-height: 600px; overflow-y: auto; padding: 1rem; background: #f8f9fa; border-radius: 0.375rem;">${text}</pre>`;
            })
            .catch(error => {
                fileViewer.innerHTML = '<div class="alert alert-warning">Cannot preview this file type. Please download to view.</div>';
            });
    } else {
        // For other file types, show download message
        fileViewer.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-file fa-3x text-muted mb-3"></i>
                <h5>Preview not available</h5>
                <p class="text-muted">This file type cannot be previewed. Please download to view the file.</p>
                <a href="/admin/issuances/${issuanceId}/download" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    Download File
                </a>
            </div>
        `;
    }

    new bootstrap.Modal(document.getElementById('viewFileModal')).show();
}

// Reupload File Modal
function reuploadFile(issuanceId, title) {
    document.getElementById('reuploadTitleInput').value = title;
    document.getElementById('reuploadForm').action = `/admin/issuances/${issuanceId}`;
    new bootstrap.Modal(document.getElementById('reuploadModal')).show();
}

// Archive Confirmation Modal
function confirmArchive(issuanceId) {
    document.getElementById('archiveForm').action = `/admin/issuances/${issuanceId}/archive`;
    new bootstrap.Modal(document.getElementById('archiveModal')).show();
}

// Unarchive Confirmation Modal
function confirmUnarchive(issuanceId) {
    document.getElementById('unarchiveForm').action = `/admin/issuances/${issuanceId}/unarchive`;
    new bootstrap.Modal(document.getElementById('unarchiveModal')).show();
}

// File size validation for create modal
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const maxSize = 20 * 1024 * 1024; // 20MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 20MB limit. Please choose a smaller file.');
            e.target.value = '';
        }
    }
});

// File size validation for reupload modal
document.getElementById('reuploadFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const maxSize = 20 * 1024 * 1024; // 20MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 20MB limit. Please choose a smaller file.');
            e.target.value = '';
        }
    }
});

// Handle form submission errors
document.getElementById('createIssuanceForm').addEventListener('submit', function(e) {
    // Clear previous error states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
});

// Handle reupload form submission
document.getElementById('reuploadForm').addEventListener('submit', function(e) {
    // Clear previous error states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

    // Validate required fields
    const title = document.getElementById('reuploadTitleInput').value.trim();
    const file = document.getElementById('reuploadFile').files[0];

    if (!title) {
        e.preventDefault();
        document.getElementById('reuploadTitleInput').classList.add('is-invalid');
        document.querySelector('#reuploadTitleInput + .invalid-feedback').textContent = 'The title field is required.';
        return false;
    }

    if (!file) {
        e.preventDefault();
        document.getElementById('reuploadFile').classList.add('is-invalid');
        document.querySelector('#reuploadFile + .form-text + .invalid-feedback').textContent = 'Please select a file to upload.';
        return false;
    }
});

// Reset modal forms when closed
document.getElementById('createIssuanceModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('createIssuanceForm').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
});

document.getElementById('reuploadModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('reuploadForm').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
});

// Show modal with validation errors if there are any
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        // Check if this is a reupload error (when coming back from update route)
        const isReuploadError = '{{ request()->route()->getName() }}' === 'admin.issuances.update' ||
                               window.location.href.includes('/admin/issuances/') &&
                               '{{ request()->method() }}' === 'PUT';

        if (isReuploadError) {
            // Show reupload modal with errors
            const reuploadModal = new bootstrap.Modal(document.getElementById('reuploadModal'));
            reuploadModal.show();

            // Show validation errors for reupload form
            @if($errors->has('title'))
                document.getElementById('reuploadTitleInput').classList.add('is-invalid');
                document.querySelector('#reuploadTitleInput + .invalid-feedback').textContent = '{{ $errors->first('title') }}';
            @endif

            @if($errors->has('file'))
                document.getElementById('reuploadFile').classList.add('is-invalid');
                document.querySelector('#reuploadFile + .form-text + .invalid-feedback').textContent = '{{ $errors->first('file') }}';
            @endif

            // Restore form values for reupload
            @if(old('title'))
                document.getElementById('reuploadTitleInput').value = '{{ old('title') }}';
            @endif
        } else {
            // Show create modal with errors
            const modal = new bootstrap.Modal(document.getElementById('createIssuanceModal'));
            modal.show();

            // Show validation errors for create form
            @if($errors->has('title'))
                document.getElementById('title').classList.add('is-invalid');
                document.querySelector('#title + .invalid-feedback').textContent = '{{ $errors->first('title') }}';
            @endif

            @if($errors->has('file'))
                document.getElementById('file').classList.add('is-invalid');
                document.querySelector('#file + .form-text + .invalid-feedback').textContent = '{{ $errors->first('file') }}';
            @endif

            // Restore form values for create
            @if(old('title'))
                document.getElementById('title').value = '{{ old('title') }}';
            @endif
        }
    });
@endif
</script>
@endsection
