<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Submissions</title>
</head>
<body>
    <h1>Barangay Submissions</h1>

    @if($reportTypes->isEmpty())
        <p>No report types found.</p>
    @else
        <ul>
            @foreach($reportTypes as $reportType)
                <li>
                    {{ $reportType->name }} ({{ $reportType->frequency }})
                </li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('barangay.submissions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="report_type_id">Select Report Type:</label>
        <select name="report_type_id" id="report_type_id">
            <option value="">-- Select Report Type --</option>
            @foreach($reportTypes as $reportType)
                <option value="{{ $reportType->id }}" data-frequency="{{ $reportType->frequency }}">
                    {{ $reportType->name }}
                </option>
            @endforeach
        </select>

        <div id="weekly_fields" style="display: none;">
            <label for="month">Month:</label>
            <input type="month" name="month">

            <label for="week_number">Week Number:</label>
            <input type="number" name="week_number" min="1" max="5">

            <label for="num_of_clean_up_sites">Number of Clean-Up Sites:</label>
            <input type="number" name="num_of_clean_up_sites">

            <label for="num_of_participants">Number of Participants:</label>
            <input type="number" name="num_of_participants">

            <label for="num_of_barangays">Number of Barangays:</label>
            <input type="number" name="num_of_barangays">

            <label for="total_volume">Total Volume:</label>
            <input type="text" name="total_volume">

            <label for="file">Upload File:</label>
            <input type="file" name="file">

            <label for="deadline">Deadline:</label>
            <input type="date" name="deadline">
        </div>

        <button type="submit">Submit</button>
    </form>

    <script>
        document.getElementById('report_type_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const frequency = selectedOption.getAttribute('data-frequency');
            const weeklyFields = document.getElementById('weekly_fields');

            if (frequency === 'weekly') {
                weeklyFields.style.display = 'block';
            } else {
                weeklyFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
