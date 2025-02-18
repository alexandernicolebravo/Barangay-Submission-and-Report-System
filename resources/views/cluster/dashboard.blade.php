<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cluster Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

    <h2 class="text-xl font-semibold mb-4">Manage File Submission Forms</h2>

    <!-- Add New Submission Form -->
    <form action="{{ route('cluster.store') }}" method="POST" class="mb-4 bg-white p-4 rounded shadow">
        @csrf
        <input type="text" name="title" placeholder="Form Title" class="border p-2 rounded w-full mb-2" required>
        <textarea name="description" placeholder="Description (optional)" class="border p-2 rounded w-full mb-2"></textarea>
        <button type="submit" class="bg-blue-500 text-white p-2 rounded w-full">Add Form</button>
    </form>

    <!-- Display Existing Forms -->
    <h3 class="text-md font-semibold mb-2">Existing Forms</h3>
    <ul class="mb-4">
        @foreach($forms as $form)
            <li class="bg-white p-3 rounded shadow mb-2 flex justify-between items-center">
                <span>{{ $form->title }} - {{ $form->description }}</span>
                <form action="{{ route('cluster.destroy', $form->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white p-2 rounded">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <!-- Barangay File Submission -->
    <h3 class="text-md font-semibold mb-2">Submit Files</h3>
    <form action="{{ route('cluster.submitFile') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
        @csrf
        <select name="form_id" class="border p-2 rounded w-full mb-2" required>
            <option value="">Select a Submission Form</option>
            @foreach($forms as $form)
                <option value="{{ $form->id }}">{{ $form->title }}</option>
            @endforeach
        </select>
        <input type="file" name="file" class="border p-2 rounded w-full mb-2" required>
        <button type="submit" class="bg-green-500 text-white p-2 rounded w-full">Submit File</button>
    </form>

    <!-- Display Uploaded Files -->
    <h3 class="text-md font-semibold mt-6 mb-2">Uploaded Files</h3>
    <ul>
        @foreach($files as $file)
            <li class="bg-white p-3 rounded shadow mb-2">
                {{ $file->file_name }} (Uploaded by User ID: {{ $file->user_id }})
                <a href="{{ route('barangay.download', $file->id) }}" class="text-blue-500 ml-2">Download</a>
            </li>
        @endforeach
    </ul>

</body>
</html> -->
