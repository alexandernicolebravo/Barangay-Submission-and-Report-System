<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report Type</title>
</head>
<body>

    <h2>Create Report Type</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <!-- Report Type Form -->
    <h3>Add Report Type</h3>
    <form method="POST" action="{{ route('report_types.store') }}">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <br>
        <label>Frequency:</label>
        <select name="frequency" required>
            @foreach(\App\Models\ReportType::frequencies() as $frequency)
                <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
            @endforeach
        </select>
        <br>
        <label>Deadline:</label>
        <input type="date" name="deadline" required>
        <br>
        <button type="submit">Create Report Type</button>
    </form>

    <hr>

    <!-- Report Type List -->
    <h3>Existing Report Types</h3>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Frequency</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        @foreach($reportTypes as $reportType)
        <tr>
            <td>{{ $reportType->formatted_name }}</td>
            <td>{{ ucfirst($reportType->frequency) }}</td>
            <td>{{ $reportType->deadline ?? 'N/A' }}</td>
            <td>
                <form action="{{ route('report_types.destroy', $reportType->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</body>
</html>
