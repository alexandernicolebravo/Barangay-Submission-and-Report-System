@forelse($reports as $report)
<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="me-2" style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                @php
                    $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                    $icon = match($extension) {
                        'pdf' => 'fa-file-pdf',
                        'doc', 'docx' => 'fa-file-word',
                        'xls', 'xlsx' => 'fa-file-excel',
                        'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                        'txt' => 'fa-file-alt',
                        default => 'fa-file'
                    };

                    $colorClass = match($extension) {
                        'pdf' => 'danger',
                        'doc', 'docx' => 'primary',
                        'xls', 'xlsx' => 'success',
                        'jpg', 'jpeg', 'png', 'gif' => 'info',
                        'txt' => 'secondary',
                        default => 'primary'
                    };

                    $isLate = \Carbon\Carbon::parse($report->updated_at)->isAfter($report->reportType->deadline);
                @endphp
                <i class="fas {{ $icon }} fa-sm"></i>
            </div>
            <div>
                <div style="font-weight: 500; color: var(--dark);">{{ $report->reportType->name }}</div>
                <small class="text-muted">{{ ucfirst(str_replace('Report', '', class_basename($report->model_type))) }}</small>
            </div>
        </div>
    </td>
    <td>{{ $report->user->name }}</td>
    <td>{{ \Carbon\Carbon::parse($report->updated_at)->format('M d, Y') }}</td>
    <td>
        @php
            $displayStatus = $report->display_status ?? 'submitted';
        @endphp

        @if($displayStatus === 'resubmit')
            <span class="status-badge resubmit">
                <i class="fas fa-sync-alt"></i>
                Resubmit
                @if($report->submission_count > 1)
                    <span class="badge bg-dark ms-1">{{ $report->submission_count }}</span>
                @endif
            </span>
        @elseif($displayStatus === 'resubmitted')
            <span class="status-badge resubmitted">
                <i class="fas fa-check-double"></i>
                Resubmitted
                @if($report->submission_count > 1)
                    <span class="badge bg-dark ms-1">{{ $report->submission_count }}</span>
                @endif
            </span>
        @else
            @php
                $statusIcon = match($report->status) {
                    'submitted' => 'fa-check-circle',
                    'no submission' => 'fa-times-circle',
                    'pending' => 'fa-clock',
                    'approved' => 'fa-thumbs-up',
                    'rejected' => 'fa-thumbs-down',
                    default => 'fa-info-circle'
                };
                $statusClass = str_replace(' ', '-', $report->status);
            @endphp
            <span class="status-badge {{ $statusClass }}">
                <i class="fas {{ $statusIcon }}"></i>
                {{ ucfirst($report->status) }}
            </span>
        @endif
    </td>
    <td>
        <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#viewSubmissionModal{{ $report->unique_id }}">
            <i class="fas fa-eye me-1"></i>
            View
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-4">
        <div class="d-flex flex-column align-items-center">
            <i class="fas fa-inbox fa-3x mb-3" style="color: var(--gray-400);"></i>
            <p class="mb-0" style="color: var(--gray-600);">No submissions found</p>
            @if(isset($selectedBarangay))
            <p class="text-muted mt-2">No reports have been submitted by {{ $selectedBarangay->name }}</p>
            @endif
        </div>
    </td>
</tr>
@endforelse
