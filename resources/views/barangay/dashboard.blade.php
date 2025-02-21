<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Barangay Dashboard</h2>

        @if(session('success'))
            <p class="text-green-600">{{ session('success') }}</p>
        @endif

        <!-- Forms from Admin -->
        <h3 class="text-xl font-semibold mt-6">Active Submission Portals</h3>
        <table class="w-full border-collapse border border-gray-300 mt-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">Title</th>
                    <th class="border px-4 py-2">Description</th>
                    <th class="border px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr class="bg-white">
                    <td class="border px-4 py-2">{{ $report->title }}</td>
                    <td class="border px-4 py-2">{{ $report->description }}</td>
                    <td class="border px-4 py-2">
                        <span class="{{ $report->status == 'Completed' ? 'text-green-600' : ($report->status == 'Rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ $report->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Submit a File -->
        <h3 class="text-xl font-semibold mt-6">Submit a File</h3>
        <form action="{{ route('barangay.files.store') }}" method="POST" enctype="multipart/form-data" class="mt-2">
            @csrf
            <label for="barangay_report_id" class="block text-gray-700">Select Report:</label>
            <select name="barangay_report_id" required class="w-full p-2 border rounded mt-1">
                @foreach($reports as $report)
                    <option value="{{ $report->id }}">{{ $report->title }}</option>
                @endforeach
            </select>
            <input type="file" name="file" required class="w-full p-2 border rounded mt-2">
            <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
        </form>

        <!-- Uploaded Files -->
        <h3 class="text-xl font-semibold mt-6">Uploaded Files</h3>
        <table class="w-full border-collapse border border-gray-300 mt-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">File Name</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                <tr class="bg-white">
                    <td class="border px-4 py-2">{{ $file->file_name }}</td>
                    <td class="border px-4 py-2">
                        <span class="{{ $file->status == 'Completed' ? 'text-green-600' : ($file->status == 'Rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ $file->status }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('barangay.files.download', $file->id) }}" class="text-blue-600">Download</a> |
                        <a href="{{ route('barangay.files.view', $file->id) }}" target="_blank" class="text-blue-600">View</a> |
                        <form action="{{ route('barangay.files.destroy', $file->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
sdasdadas
