<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
</head>
<body>

    <h2>All Submissions</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h3>Weekly Reports</h3>
    <table border="1">
        <tr>
            <th>User</th>
            <th>Report Type</th>
            <th>Month</th>
            <th>Week</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>
        @foreach($weeklyReports as $report)
        <tr>
            <td>{{ $report->user->name }}</td>
            <td>{{ $report->reportType->name }}</td>
            <td>{{ $report->month }}</td>
            <td>{{ $report->week_number }}</td>
            <td>{{ ucfirst($report->status) }}</td>
            <td>{{ $report->remarks ?? 'No remarks' }}</td>
            <td>
                <form method="POST" action="{{ route('reports.update', $report->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="weekly_report" value="1">
                    <input type="text" name="remarks" placeholder="Enter remarks">
                    <select name="status">
                        <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $report->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <h3>Other Reports</h3>
    <table border="1">
        <tr>
            <th>User</th>
            <th>Report Type</th>
            <th>File</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>
        @foreach($reportFiles as $report)
        <tr>
            <td>{{ $report->user->name }}</td>
            <td>{{ $report->reportType->name }}</td>
            <td><a href="{{ asset('storage/' . $report->file_path) }}" target="_blank">View File</a></td>
            <td>{{ ucfirst($report->status) }}</td>
            <td>{{ $report->remarks ?? 'No remarks' }}</td>
            <td>
                <form method="POST" action="{{ route('reports.update', $report->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="text" name="remarks" placeholder="Enter remarks">
                    <select name="status">
                        <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $report->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</body>
</html>
