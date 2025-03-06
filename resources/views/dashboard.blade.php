<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

    <h2>Admin Dashboard</h2>

    <!-- Success & Error Messages -->
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <!-- User Registration Form -->
    <h3>Add User</h3>
    <form method="POST" action="{{ route('admin.store') }}">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <label>Role:</label>
        <select name="role" required>
            <option value="cluster">Cluster</option>
            <option value="barangay">Barangay</option>
        </select>
        <br>
        <label>Assign to Cluster (if Barangay):</label>
        <select name="cluster_id">
            <option value="">None</option>
            @foreach($clusters as $cluster)
                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
            @endforeach
        </select>
        <br>
        <button type="submit">Create User</button>
    </form>

    <hr>

    <!-- User List -->
    <h3>User List</h3>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Cluster Assigned</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>
                @if($user->role == 'barangay' && $user->cluster_id)
                    {{ $clusters->firstWhere('id', $user->cluster_id)->name ?? 'Unassigned' }}
                @else
                    N/A
                @endif
            </td>
            <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
            <td>
                <form action="{{ route('admin.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</body>
</html>
