<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
</head>
<body>
    <h1>Submitted Reports</h1>

    @foreach ($submittedReportsByFrequency as $frequency => $reports)
        <h2>{{ ucfirst($frequency) }} Reports</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Submitted By</th>
                    <th>Report Type</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>File</th> <!-- Updated this column -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>{{ $report->user->name }}</td>
                        <td>{{ $report->reportType->name }}</td>
                        <td>{{ ucfirst($report->status) }}</td>
                        <td>{{ $report->remarks }}</td>
                        <td>
                            @if($report->file_path)
                                @php
                                    $fileName = basename($report->file_path);
                                @endphp
                                <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank">{{ $fileName }}</a>
                            @else
                                No File
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('update.report', $report->id) }}" method="POST">
                                @csrf
                                <select name="status">
                                    <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $report->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <input type="text" name="remarks" placeholder="Add remarks" value="{{ $report->remarks }}">
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
