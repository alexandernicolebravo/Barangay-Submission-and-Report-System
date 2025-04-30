<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>All Submissions</h2>

        @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $type)
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">{{ ucfirst($type) }} Reports</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Submitted By</th>
                                    <th>Report Type</th>
                                    <th>Status</th>
                                    <th>Submission Time</th>
                                    <th>File</th>
                                    <th>Submitted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports[$type] as $report)
                                    <tr>
                                        <td>{{ $report->id }}</td>
                                        <td>{{ $report->user->name }}</td>
                                        <td>{{ $report->reportType->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $submittedTimestamp = strtotime($report->created_at);
                                                $deadlineTimestamp = strtotime($report->deadline);
                                                // Add 1 day to deadline timestamp to include the entire deadline day
                                                $deadlineTimestamp = $deadlineTimestamp + (24 * 60 * 60);
                                                $isLate = $submittedTimestamp > $deadlineTimestamp;
                                                $submissionStatus = $isLate ? 'Late' : 'On Time';
                                                $badgeClass = $isLate ? 'danger' : 'success';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ $submissionStatus }}
                                            </span>
                                            @if($isLate)
                                                <small class="text-muted d-block">
                                                    Deadline: {{ date('Y-m-d', $deadlineTimestamp - (24 * 60 * 60)) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($report->file_path)
                                                <a href="{{ Storage::url($report->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                    View File
                                                </a>
                                            @else
                                                No file attached
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s', $submittedTimestamp) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $report->id }}">
                                                Update Status
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Update Status Modal -->
                                    <div class="modal fade" id="updateModal{{ $report->id }}" tabindex="-1" aria-labelledby="updateModalLabel{{ $report->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateModalLabel{{ $report->id }}">Update Report Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('update.report', $report->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select name="status" id="status" class="form-select" required>
                                                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $report->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="remarks" class="form-label">Remarks</label>
                                                            <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ $report->remarks }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No {{ $type }} reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
