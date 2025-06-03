@extends('layouts.barangay')

@section('title', 'Issuances')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0 text-gray-800">
                    <i class="fas fa-file-alt me-2"></i>
                    Issuances
                </h1>
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
                            Available Issuances
                        </h6>
                        <div class="sort-controls">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="sortOrder" id="sortNewest" value="newest"
                                       {{ request('sort') == 'newest' || !request('sort') ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary btn-sm" for="sortNewest">
                                    <i class="fas fa-arrow-down me-1"></i>
                                    Newest
                                </label>

                                <input type="radio" class="btn-check" name="sortOrder" id="sortOldest" value="oldest"
                                       {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary btn-sm" for="sortOldest">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Oldest
                                </label>
                            </div>
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
                                                <div class="d-flex align-items-center">
                                                    <div class="file-icon-sm me-2">
                                                        <i class="fas fa-file-alt"></i>
                                                    </div>
                                                    <strong>{{ $issuance->title }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $issuance->file_name ?: 'Unknown' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $issuance->created_at->format('M d, Y h:i A') }}</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons-modern">
                                                    <button type="button" class="action-btn view-btn"
                                                            onclick="viewFile({{ $issuance->id }}, '{{ $issuance->title }}', '{{ $issuance->file_path }}')"
                                                            title="View File">
                                                        <i class="fas fa-eye"></i>
                                                        <span>View</span>
                                                    </button>
                                                    <a href="{{ route('barangay.issuances.download', $issuance) }}"
                                                       class="action-btn download-btn" title="Download">
                                                        <i class="fas fa-download"></i>
                                                        <span>Download</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $issuances->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No issuances available</h5>
                            <p class="text-muted">There are currently no issuances uploaded by the admin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View File Modal -->
<div class="modal fade" id="viewFileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    <span id="viewFileTitle">View File</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fileViewer" style="height: 500px; width: 100%;">
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

<style>
.file-icon-sm {
    width: 24px;
    height: 24px;
    background: #4e73df;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #f8f9fc;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
    border-color: #e3e6f0;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fc;
}

/* Modern Sort Controls */
.sort-controls .btn-group {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.sort-controls .btn {
    border: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.sort-controls .btn-outline-primary {
    background: white;
    color: #6c757d;
    border-color: #e3e6f0;
}

.sort-controls .btn-check:checked + .btn-outline-primary {
    background: #4e73df;
    color: white;
    box-shadow: none;
}

.sort-controls .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Modern Action Buttons */
.action-buttons-modern {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid;
    min-width: 80px;
    justify-content: center;
}

.view-btn {
    background: #f8f9fc;
    color: #4e73df;
    border-color: #e3e6f0;
}

.view-btn:hover {
    background: #4e73df;
    color: white;
    border-color: #4e73df;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
}

.download-btn {
    background: #1cc88a;
    color: white;
    border-color: #1cc88a;
}

.download-btn:hover {
    background: #17a673;
    border-color: #17a673;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(28, 200, 138, 0.3);
}

.action-btn i {
    font-size: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .sort-controls {
        align-self: center;
    }

    .action-buttons-modern {
        flex-direction: column;
        gap: 0.25rem;
    }

    .action-btn {
        min-width: 70px;
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }

    .action-btn span {
        display: none;
    }
}

@media (max-width: 576px) {
    .sort-controls .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }

    .table td {
        padding: 0.5rem;
    }
}
</style>

<script>
// Handle sort button changes
document.querySelectorAll('input[name="sortOrder"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const sortValue = this.value;
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        }
    });
});
</script>
@endsection
