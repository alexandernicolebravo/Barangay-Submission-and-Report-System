@extends('admin.layouts.app')

@section('title', 'View Submissions')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title">
            <i class="fas fa-list"></i>
            View Submissions
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
            Report Submissions
        </h5>
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control search-box" id="submissionSearch" placeholder="Search submissions...">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Report Type</th>
                        <th>Barangay</th>
                        <th>Submission Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $submission->reportType->name }}</div>
                                    <small style="color: var(--gray-600);">{{ $submission->reportType->description }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: var(--info-light); display: flex; align-items: center; justify-content: center; color: var(--info);">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $submission->barangay->name }}</div>
                                    <small style="color: var(--gray-600);">{{ $submission->barangay->cluster }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="color: var(--gray-600);">{{ $submission->submission_date }}</td>
                        <td style="color: var(--gray-600);">{{ $submission->due_date }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => ['bg' => 'var(--warning-light)', 'text' => 'var(--warning)'],
                                    'approved' => ['bg' => 'var(--success-light)', 'text' => 'var(--success)'],
                                    'rejected' => ['bg' => 'var(--danger-light)', 'text' => 'var(--danger)']
                                ];
                                $statusIcons = [
                                    'pending' => 'clock',
                                    'approved' => 'check-circle',
                                    'rejected' => 'times-circle'
                                ];
                            @endphp
                            <span class="badge" style="background: {{ $statusColors[$submission->status]['bg'] }}; color: {{ $statusColors[$submission->status]['text'] }};">
                                <i class="fas fa-{{ $statusIcons[$submission->status] }} me-1"></i>
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#viewModal{{ $submission->id }}">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                            </button>
                            <button type="button" class="btn btn-sm" style="background: var(--info-light); color: var(--info); border: none;" data-bs-toggle="modal" data-bs-target="#updateModal{{ $submission->id }}">
                                <i class="fas fa-edit"></i>
                                <span>Update</span>
                            </button>
                        </td>
                    </tr>

                    <!-- View Modal -->
                    <div class="modal fade" id="viewModal{{ $submission->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-eye me-2" style="color: var(--primary);"></i>
                                        View Submission Details
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Report Type</label>
                                            <div class="form-control bg-light">{{ $submission->reportType->name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Barangay</label>
                                            <div class="form-control bg-light">{{ $submission->barangay->name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Submission Date</label>
                                            <div class="form-control bg-light">{{ $submission->submission_date }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Due Date</label>
                                            <div class="form-control bg-light">{{ $submission->due_date }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <div>
                                                <span class="badge" style="background: {{ $statusColors[$submission->status]['bg'] }}; color: {{ $statusColors[$submission->status]['text'] }};">
                                                    <i class="fas fa-{{ $statusIcons[$submission->status] }} me-1"></i>
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-control bg-light">{{ $submission->remarks ?? 'No remarks' }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Attached Files</label>
                                            <div class="form-control bg-light">
                                                @if($submission->files->count() > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($submission->files as $file)
                                                            <li>
                                                                <a href="{{ route('download.file', $file->id) }}" class="text-decoration-none">
                                                                    <i class="fas fa-file me-2"></i>
                                                                    {{ $file->filename }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No files attached
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal{{ $submission->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit me-2" style="color: var(--primary);"></i>
                                        Update Submission Status
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.update-submission', $submission->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="pending" {{ $submission->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $submission->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Remarks</label>
                                            <textarea class="form-control" name="remarks" rows="3">{{ $submission->remarks }}</textarea>
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
    document.getElementById('submissionSearch').addEventListener('keyup', function() {
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
