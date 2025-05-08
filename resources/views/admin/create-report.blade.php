@extends('admin.layouts.app')

@section('title', 'Manage Report Types')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title">
            <i class="fas fa-file-alt"></i>
            Manage Report Types
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

<!-- Create Report Type Form -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2" style="color: var(--primary);"></i>
            Create New Report Type
        </h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#reportForm">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="card-body collapse show" id="reportForm">
        <form method="POST" action="{{ route('admin.store-report') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Report Type Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Frequency</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                        <select class="form-select" name="frequency" required>
                            @foreach(App\Models\ReportType::frequencies() as $frequency)
                                <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Due Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" class="form-control" name="deadline" required>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Allowed File Types</label>
                    <div class="row">
                        @foreach(App\Models\ReportType::availableFileTypes() as $extension => $description)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allowed_file_types[]" value="{{ $extension }}" id="fileType{{ $extension }}">
                                    <label class="form-check-label" for="fileType{{ $extension }}">
                                        <i class="fas fa-file-{{ $extension }} me-1"></i>
                                        {{ $description }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Create Report Type</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Existing Report Types -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2" style="color: var(--primary);"></i>
            Existing Report Types
        </h5>
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control search-box" id="reportSearch" placeholder="Search report types...">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Frequency</th>
                        <th>Due Date</th>
                        <th>Allowed File Types</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportTypes as $type)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $type->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--info-light); color: var(--info);">
                                {{ ucfirst($type->frequency) }}
                            </span>
                        </td>
                        <td style="color: var(--gray-600);">{{ $type->deadline }}</td>
                        <td>
                            @if($type->allowed_file_types)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($type->allowed_file_types as $extension)
                                        <span class="badge" style="background: var(--primary-light); color: var(--primary);">
                                            <i class="fas fa-file-{{ $extension }} me-1"></i>
                                            {{ strtoupper($extension) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No restrictions</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#editModal{{ $type->id }}">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <form action="{{ route('admin.destroy-report', $type->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: var(--danger-light); color: var(--danger); border: none;">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit me-2" style="color: var(--primary);"></i>
                                        Edit Report Type
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.update-report', $type->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ $type->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Frequency</label>
                                            <select class="form-select" name="frequency" required>
                                                @foreach(App\Models\ReportType::frequencies() as $frequency)
                                                    <option value="{{ $frequency }}" {{ $type->frequency == $frequency ? 'selected' : '' }}>
                                                        {{ ucfirst($frequency) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Due Date</label>
                                            <input type="date" class="form-control" name="deadline" value="{{ $type->deadline }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Allowed File Types</label>
                                            <div class="row">
                                                @foreach(App\Models\ReportType::availableFileTypes() as $extension => $description)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="allowed_file_types[]"
                                                                value="{{ $extension }}" id="editFileType{{ $type->id }}{{ $extension }}"
                                                                {{ in_array($extension, $type->allowed_file_types ?? []) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="editFileType{{ $type->id }}{{ $extension }}">
                                                                <i class="fas fa-file-{{ $extension }} me-1"></i>
                                                                {{ $description }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
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

@push('scripts')
<script>
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
