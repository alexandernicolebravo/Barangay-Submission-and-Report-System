<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Include Tailwind CSS or your custom styles -->
</head>
<body>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold">Upload Files</h2>

        <!-- Success Message -->
        @if(session('success'))
            <div style="color: green;">
                {{ session('success') }}
            </div>
        @endif

        <!-- File Upload Section -->
<form action="{{ route('barangay.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
        <label for="file" class="block text-sm font-medium text-gray-700">Upload File</label>
        <input type="file" id="file" name="file" class="mt-1 block w-full" accept=".pdf,.doc,.docx,.xlsx,.png,.jpg,.jpeg"
            onchange="previewFile()" required>

        <!-- Display the preview once a file is chosen -->
        <div id="file-preview" class="mt-4">
            <img id="file-image-preview" src="" alt="File Preview" class="hidden max-w-full" />
            <embed id="file-pdf-preview" src="" type="application/pdf" class="hidden w-full h-64" />
            <p id="file-name" class="mt-2 text-sm text-gray-500"></p>
        </div>
    </div>

    <!-- Submit button, initially hidden until file is chosen -->
    <div id="submit-section" class="hidden">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Submit</button>
    </div>
</form>

        <!-- Submitted Files Section -->
<h3 class="mt-6 text-xl font-semibold">Submitted Files</h3>
<ul>
    @foreach($files as $file)
        <li class="mb-4">
            <span>{{ $file->file_name }}</span>
            <!-- View Link -->
            @if(in_array(pathinfo($file->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                <a href="{{ route('barangay.view', $file->id) }}" class="ml-4 text-blue-600">View</a>
            @endif
            <!-- Download Link -->
            <a href="{{ route('barangay.download', $file->id) }}" class="ml-4 text-blue-600">Download</a>
            <!-- Delete (Cancel Upload) Form -->
            <form action="{{ route('barangay.destroy', $file->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="ml-4 p-2 bg-red-500 text-white rounded" onclick="return confirm('Are you sure you want to cancel the upload?')">Cancel Upload</button>
            </form>
        </li>
    @endforeach
</ul>

    </div>
</body>
<script>
    function previewFile() {
        const fileInput = document.getElementById('file');
        const previewSection = document.getElementById('file-preview');
        const submitSection = document.getElementById('submit-section');
        const fileName = document.getElementById('file-name');
        const fileImagePreview = document.getElementById('file-image-preview');
        const filePdfPreview = document.getElementById('file-pdf-preview');

        const file = fileInput.files[0];
        const fileExtension = file.name.split('.').pop().toLowerCase();

        // Show the submit button and hide the upload button
        submitSection.classList.remove('hidden');

        // Display file name
        fileName.textContent = file.name;

        // Hide previews initially
        fileImagePreview.classList.add('hidden');
        filePdfPreview.classList.add('hidden');

        // If it's an image, display image preview
        if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
            const reader = new FileReader();
            reader.onload = function (e) {
                fileImagePreview.src = e.target.result;
                fileImagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
        // If it's a PDF, display PDF preview
        else if (fileExtension === 'pdf') {
            const reader = new FileReader();
            reader.onload = function (e) {
                filePdfPreview.src = e.target.result;
                filePdfPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
</html>
