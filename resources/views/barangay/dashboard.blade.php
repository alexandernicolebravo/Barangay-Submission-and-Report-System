<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Barangay Weekly Report</h1>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Weekly Report Form -->
        <form action="{{ route('barangays.weekly.store') }}" method="POST" enctype="multipart/form-data" class="mb-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Month:</label>
                <input type="text" name="month" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Week Number:</label>
                <input type="number" name="week_number" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Number of Clean-Up Sites:</label>
                <input type="number" name="num_of_clean_up_sites" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Number of Participants:</label>
                <input type="number" name="num_of_participants" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Number of Barangays:</label>
                <input type="number" name="num_of_barangays" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Total Volume Collected (kg):</label>
                <input type="number" name="total_volume" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Upload File:</label>
                <input type="file" name="file" class="w-full border p-2 rounded" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit Report</button>
        </form>

        <!-- Submitted Weekly Reports -->
        <h2 class="text-xl font-semibold mb-2">Submitted Reports</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Month</th>
                    <th class="border p-2">Week</th>
                    <th class="border p-2">Clean-Up Sites</th>
                    <th class="border p-2">Participants</th>
                    <th class="border p-2">Barangays</th>
                    <th class="border p-2">Total Volume</th>
                    <th class="border p-2">File</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weeklyReports as $report)
                    <tr class="border">
                        <td class="border p-2">{{ $report->month }}</td>
                        <td class="border p-2">{{ $report->week_number }}</td>
                        <td class="border p-2">{{ $report->num_of_clean_up_sites }}</td>
                        <td class="border p-2">{{ $report->num_of_participants }}</td>
                        <td class="border p-2">{{ $report->num_of_barangays }}</td>
                        <td class="border p-2">{{ $report->total_volume }} kg</td>
                        <td class="border p-2">
                            <a href="{{ asset('storage/' . $report->file_path) }}" class="text-blue-500" target="_blank">View File</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
