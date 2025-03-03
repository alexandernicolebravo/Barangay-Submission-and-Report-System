<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <script>
        function toggleFields() {
            let reportType = document.getElementById("report_type_id");
            let weeklyFields = document.getElementById("weekly-fields");
            let selectedOption = reportType.options[reportType.selectedIndex].dataset.frequency;

            if (selectedOption === "weekly") {
                weeklyFields.style.display = "block";
            } else {
                weeklyFields.style.display = "none";
            }
        }
    </script>
</head>
<body>

    <h2>Submit Report</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
        @csrf

        <label>Report Type:</label>
        <select id="report_type_id" name="report_type_id" onchange="toggleFields()" required>
            <option value="">Select Report Type</option>
            @foreach($reportTypes as $reportType)
                <option value="{{ $reportType->id }}" data-frequency="{{ $reportType->frequency }}">
                    {{ ucfirst($reportType->name) }} ({{ ucfirst($reportType->frequency) }})
                </option>
            @endforeach
        </select>
        <br>

        <div id="weekly-fields" style="display: none;">
            <label>Month:</label>
            <input type="text" name="month">
            <br>
            <label>Week Number:</label>
            <input type="number" name="week_number">
            <br>
            <label>Number of Clean-up Sites:</label>
            <input type="number" name="num_of_clean_up_sites">
            <br>
            <label>Number of Participants:</label>
            <input type="number" name="num_of_participants">
            <br>
            <label>Number of Barangays:</label>
            <input type="number" name="num_of_barangays">
            <br>
            <label>Total Volume Collected:</label>
            <input type="number" step="0.01" name="total_volume">
            <br>
        </div>

        <label>Upload Report File:</label>
        <input type="file" name="file" accept=".pdf, .doc, .docx, .xlsx" required>
        <br>

        <!-- <label>Deadline:</label>
        <input type="date" name="deadline" required>
        <br>

        <label>Remarks (Optional):</label>
        <textarea name="remarks"></textarea> -->
        <br>

        <button type="submit">Submit Report</button>
    </form>

</body>
</html>
