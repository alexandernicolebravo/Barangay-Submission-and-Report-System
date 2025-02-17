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

    <!-- Create User Form -->
    <h2 class="text-xl mt-4">Create User</h2>
    <form action="{{ route('admin.store') }}" method="POST">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Role:</label>
    <select name="role" id="role-select" required>
        <option value="cluster">Cluster</option>
        <option value="barangay">Barangay</option>
    </select>

    <div id="cluster-selection" style="display: none;">
        <label>Assign to Cluster:</label>
        <select name="cluster_id">
            <option value="">-- Select Cluster --</option>
            @foreach($clusters as $cluster)
                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit">Create</button>
</form>

    <!-- Display Existing Users -->
    <h2 class="text-xl mt-6">Existing Users</h2>

    <!-- Clusters -->
    <h3 class="text-lg font-semibold mt-4">Clusters</h3>
    <ul class="list-disc ml-6">
        @foreach($clusters as $cluster)
            <li>
                {{ $cluster->name }} ({{ $cluster->email }})
                <form method="POST" action="{{ route('admin.users.destroy', $cluster->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>

    <!-- Barangays -->
    <h3 class="text-lg font-semibold mt-4">Barangays</h3>
    <ul class="list-disc ml-6">
        @foreach($barangays as $barangay)
            <li>
                {{ $barangay->name }} ({{ $barangay->email }})
                <form method="POST" action="{{ route('admin.users.destroy', $barangay->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
    <script>
    document.getElementById('role-select').addEventListener('change', function () {
        document.getElementById('cluster-selection').style.display = this.value === 'barangay' ? 'block' : 'none';
    });
</script>
</body>
</html>
