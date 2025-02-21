<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Submissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-2xl font-bold">Report Submission Portal</h1>

    <div class="mt-6 bg-white shadow-md rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-4">Active Submissions</h2>

        <!-- Check if there are active report submissions -->
        @if($submissions->isEmpty())
            <p class="text-gray-500">No active submissions available.</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Title</th>
                        <th class="border p-2">Description</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr class="bg-white border">
                            <td class="border p-2">{{ $submission->title }}</td>
                            <td class="border p-2">{{ $submission->description ?? 'No description' }}</td>
                            <td class="border p-2">
                            <form action="{{ url('/barangay/submissions/'.$submission->id.'/submit') }}" method="POST" enctype="multipart/form-data">

    @csrf
    <input type="file" name="file">
    <button type="submit">Submit</button>
</form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</body>
</html>
