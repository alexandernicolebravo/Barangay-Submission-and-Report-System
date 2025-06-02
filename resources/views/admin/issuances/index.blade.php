@extends('layouts.admin')

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
                <a href="{{ route('admin.issuances.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Upload New Issuance
                </a>
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
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        All Issuances
                    </h6>
                </div>
                <div class="card-body">
                    @if($issuances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>File Name</th>
                                        <th>File Size</th>
                                        <th>File Type</th>
                                        <th>Uploaded By</th>
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
                                                {{ $issuance->file_name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $issuance->file_size_human }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ strtoupper($issuance->file_type) }}</span>
                                            </td>
                                            <td>{{ $issuance->uploader->name }}</td>
                                            <td>{{ $issuance->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.issuances.show', $issuance) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.issuances.download', $issuance) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('admin.issuances.edit', $issuance) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete({{ $issuance->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No issuances found</h5>
                            <p class="text-muted">Upload your first issuance to get started.</p>
                            <a href="{{ route('admin.issuances.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Upload New Issuance
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this issuance? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(issuanceId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/issuances/${issuanceId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
