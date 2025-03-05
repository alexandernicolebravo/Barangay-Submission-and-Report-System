<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <style>
        .tab { display: inline-block; padding: 10px 15px; cursor: pointer; border: 1px solid #ddd; background: #f4f4f4; margin-right: 5px; }
        .tab.active { background: #ddd; font-weight: bold; }
        .tab-content { display: none; padding: 15px; border: 1px solid #ddd; margin-top: 10px; }
        .tab-content.active { display: block; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
    </style>
    <script>
        function showTab(reportType) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(reportType + "-content").classList.add('active');

            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(reportType + "-tab").classList.add('active');
        }
    </script>
</head>
<body>

    <h2>Submit Report</h2>

    @if ($reportTypes->isEmpty())
        <p>No report types available.</p>
    @else
        <div>
            @foreach ($reportTypes->groupBy('frequency') as $frequency => $types)
                <span class="tab {{ $loop->first ? 'active' : '' }}" id="{{ $frequency }}-tab" onclick="showTab('{{ $frequency }}')">
                    {{ ucfirst($frequency) }}
                </span>
            @endforeach
        </div>

        @foreach ($reportTypes->groupBy('frequency') as $frequency => $types)
            <div id="{{ $frequency }}-content" class="tab-content {{ $loop->first ? 'active' : '' }}">
                <h3>{{ ucfirst($frequency) }} Report</h3>

                @foreach ($types as $reportType)
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <fieldset>
                            <legend>{{ $reportType->name }}</legend>
                            <input type="hidden" name="report_type_id" value="{{ $reportType->id }}">

                            <p><strong>Deadline:</strong> {{ $reportType->deadline }}</p>

                            @if ($frequency === 'weekly')
                                <label for="month_{{ $reportType->id }}">Month:</label>
                                <input type="text" name="month" required><br>

                                <label for="week_number_{{ $reportType->id }}">Week Number:</label>
                                <input type="number" name="week_number" required><br>

                                <label for="num_of_clean_up_sites_{{ $reportType->id }}">Number of Clean-up Sites:</label>
                                <input type="number" name="num_of_clean_up_sites" required><br>

                                <label for="num_of_participants_{{ $reportType->id }}">Number of Participants:</label>
                                <input type="number" name="num_of_participants" required><br>

                                <label for="num_of_barangays_{{ $reportType->id }}">Number of Barangays Involved:</label>
                                <input type="number" name="num_of_barangays" required><br>

                                <label for="total_volume_{{ $reportType->id }}">Total Volume (kg/lbs):</label>
                                <input type="number" name="total_volume" required><br>

                            @elseif ($frequency === 'monthly')
                                <label for="month_{{ $reportType->id }}">Month:</label>
                                <input type="text" name="month" required><br>

                            @elseif ($frequency === 'quarterly')
                                <label for="quarter_number_{{ $reportType->id }}">Quarter Number:</label>
                                <input type="number" name="quarter_number" required><br>

                            @elseif ($frequency === 'semestral')
                                <label for="sem_number_{{ $reportType->id }}">Semester Number:</label>
                                <input type="number" name="sem_number" required><br>
                            @endif

                            <label for="file_{{ $reportType->id }}">Upload Report:</label>
                            <input type="file" name="file" required><br>

                            <button type="submit">Submit {{ $reportType->name }}</button>
                        </fieldset>
                    </form>
                @endforeach

                View Submitted Reports
                <h3>Submitted {{ ucfirst($frequency) }} Reports</h3>

                @php
                    $submittedReports = $submittedReportsByFrequency[$frequency] ?? collect();
                @endphp

                @if ($submittedReports->isEmpty())
                    <p>No submitted reports available.</p>
                @else
                <table>
    <thead>
        <tr>
            <th>Report Name</th>
            <th>Submitted By</th>
            <th>Submitted At</th>
            <th>File</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <!-- <tbody>
        @foreach ($submittedReports as $report)
            <tr>
                <td>{{ $report->reportType->name }}</td>
                <td>{{ $report->user->name }}</td>
                <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                <td><a href="{{ url('/files/' . basename($report->file_path)) }}" target="_blank">View File</a>
                <br>
                <td>{{ $report->status ?? 'Pending' }}</td>
                <td>{{ $report->remarks ?? 'No remarks' }}</td>
            </tr>
        @endforeach
    </tbody> -->

    <tbody>
    @foreach ($submittedReports as $report)
        <tr>
            <td>{{ $report->reportType->name }}</td>
            <td>{{ $report->user->name }}</td>
            <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
            <td>
            <a href="{{ asset('storage/reports/' . basename($report->file_path)) }}" target="_blank">View File</a>


            </td>
            <td>{{ $report->status ?? 'Pending' }}</td>
            <td>{{ $report->remarks ?? 'No remarks' }}</td>
        </tr>
    @endforeach
</tbody>

</table>

                @endif
            </div>
        @endforeach
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let firstTab = document.querySelector('.tab');
            if (firstTab) {
                showTab(firstTab.id.replace('-tab', ''));
            }
        });
    </script>

</body>
</html>
