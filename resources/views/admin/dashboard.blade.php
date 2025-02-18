<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>

    <!-- Display Success & Error Messages -->
    @if(session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif
    @if(session('confirm'))
        <script>
            if (confirm("{{ session('confirm') }}")) {
                document.getElementById("confirm-delete-form").submit();
            }
        </script>
    @endif

    <!-- Create User Form -->
    <h2 class="text-xl mt-4">Create User</h2>
    <form action="{{ route('admin.store') }}" method="POST" class="space-y-2">
        @csrf
        <label class="block">Name:</label>
        <input type="text" name="name" required class="border p-1 w-full">

        <label class="block">Email:</label>
        <input type="email" name="email" required class="border p-1 w-full">

        <label class="block">Password:</label>
        <input type="password" name="password" required class="border p-1 w-full">

        <label class="block">Role:</label>
        <select name="role" id="role-select" required class="border p-1 w-full">
            <option value="cluster">Cluster</option>
            <option value="barangay">Barangay</option>
        </select>

        <div id="cluster-selection" class="mt-2 hidden">
            <label class="block">Assign to Cluster:</label>
            <select name="cluster_id" class="border p-1 w-full">
                <option value="">-- Select Cluster --</option>
                @foreach($clusters as $cluster)
                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Create</button>
    </form>

   <!-- Display Existing Users -->
   <h2 class="text-xl mt-6">Existing Users</h2>

<!-- Clusters -->
<h3 class="text-lg font-semibold mt-4">Clusters</h3>
<ul class="list-disc ml-6">
    @foreach($clusters as $cluster)
        <li class="flex justify-between items-center">
            <span>{{ $cluster->name }} ({{ $cluster->email }}) -
                <span class="{{ $cluster->is_active ? 'text-green-500' : 'text-red-500' }}">
                    {{ $cluster->is_active ? 'Active' : 'Inactive' }}
                </span>
            </span>
            <form method="POST" action="{{ route('admin.users.destroy', $cluster->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" class="text-yellow-500 confirm-deactivate" data-id="{{ $cluster->id }}">
                    {{ $cluster->is_active ? 'Disable' : 'Reactivate' }}
                </button>
            </form>
        </li>
    @endforeach
</ul>

<!-- Barangays -->
<h3 class="text-lg font-semibold mt-4">Barangays</h3>
<ul class="list-disc ml-6">
    @foreach($barangays as $barangay)
        <li class="flex justify-between items-center">
            <span>{{ $barangay->name }} ({{ $barangay->email }}) -
                <span class="{{ $barangay->is_active ? 'text-green-500' : 'text-red-500' }}">
                    {{ $barangay->is_active ? 'Active' : 'Inactive' }}
                </span>
            </span>
            <form method="POST" action="{{ route('admin.users.destroy', $barangay->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" class="text-red-500 confirm-deactivate" data-id="{{ $barangay->id }}">
                    {{ $barangay->is_active ? 'Disable' : 'Reactivate' }}
                </button>
            </form>
        </li>
    @endforeach
</ul>

<script>
    document.querySelectorAll('.confirm-deactivate').forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();

            const userId = this.dataset.id;
            const response = await fetch(`/admin/users/${userId}/confirm-deactivation`);
            const data = await response.json();

            if (data.confirm) {
                if (confirm(data.confirm)) {
                    // If the user confirms, submit the form
                    this.closest('form').submit();
                }
            }
        });
    });
</script>

Create this in table format . It should separate the barangays based on their assigned clusters 



</body>
</html>
