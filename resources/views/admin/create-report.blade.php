<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-1/3">
        <h1 class="text-xl font-bold mb-4">Create Report Submission Portal</h1>
        <form action="{{ route('admin.store') }}" method="POST">
            @csrf
            <label class="block">Title:</label>
            <input type="text" name="title" required class="border p-2 w-full rounded">

            <label class="block mt-2">Description:</label>
            <textarea name="description" class="border p-2 w-full rounded"></textarea>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Create</button>
        </form>
    </div>
</body>
</html>
sdfsfsdfmwfoj
