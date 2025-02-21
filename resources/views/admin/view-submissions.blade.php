<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submitted Files</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold">Submitted Files</h1>

    <div class="mt-4">
        @foreach($submissions as $submission)
        <div class="border p-4 mb-2 bg-white rounded shadow">
            <h2 class="text-lg font-semibold">{{ $submission->title }}</h2>
            <p>{{ $submission->description }}</p>
            <p class="text-sm text-gray-600">Status: {{ ucfirst($submission->status) }}</p>
            <p class="text-sm text-gray-800 font-semibold">Submitted by: {{ $submission->submitted_by }}</p>

            <a href="{{ asset('storage/' . $submission->file_path) }}"
               class="bg-blue-500 text-white px-4 py-2 rounded mt-2 inline-block"
               target="_blank">
               View File
            </a>
        </div>
        @endforeach
    </div>
</body>
</html>
