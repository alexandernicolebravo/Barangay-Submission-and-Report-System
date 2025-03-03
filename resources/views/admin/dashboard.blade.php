<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Report Type Form -->
        <form action="{{ route('admin.report_types.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Report Type Name:</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Frequency:</label>
                <select name="frequency" class="w-full border p-2 rounded" required>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semestral">Semestral</option>
                    <option value="annual">Annual</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Report Type</button>
        </form>

        <!-- Report Types Table -->
        <h2 class="text-xl font-semibold mb-2">Existing Report Types</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Frequency</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportTypes as $reportType)
                    <tr class="border">
                        <td class="border p-2">{{ $reportType->name }}</td>
                        <td class="border p-2">{{ ucfirst($reportType->frequency) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
