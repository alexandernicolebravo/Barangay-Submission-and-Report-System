<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Report Types</title>
    <script>
        function openEditModal(id, name, frequency, deadline) {
            document.getElementById('editForm').action = "{{ route('admin.update-report', '') }}/" + id;
            document.getElementById('editName').value = name;
            document.getElementById('editFrequency').value = frequency;
            document.getElementById('editDeadline').value = deadline;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</head>
<body>

    <h2>Create Report Type</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <!-- Report Type Creation Form -->
    <form method="POST" action="{{ route('admin.store-report') }}">
        @csrf
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="frequency">Frequency:</label>
            <select id="frequency" name="frequency" required>
                @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $frequency)
                    <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="deadline">Deadline:</label>
            <input type="date" id="deadline" name="deadline">
        </div>

        <button type="submit">Create Report Type</button>
    </form>

    <hr>

    <!-- Report Type List -->
    <h3>Existing Report Types</h3>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Frequency</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        @foreach($reportTypes as $reportType)
        <tr>
            <td>{{ $reportType->name }}</td>
            <td>{{ ucfirst($reportType->frequency) }}</td>
            <td>{{ $reportType->deadline ?? 'N/A' }}</td>
            <td>
                <button onclick="openEditModal(
                    '{{ $reportType->id }}',
                    '{{ $reportType->name }}',
                    '{{ $reportType->frequency }}',
                    '{{ $reportType->deadline }}'
                )">Edit</button>

                <form action="{{ route('admin.destroy-report', $reportType->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background:white; border:1px solid #ccc;">
        <h3>Edit Report Type</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name" required>
            </div>

            <div>
                <label for="editFrequency">Frequency:</label>
                <select id="editFrequency" name="frequency" required>
                    @foreach(['weekly', 'monthly', 'quarterly', 'semestral', 'annual'] as $frequency)
                        <option value="{{ $frequency }}">{{ ucfirst($frequency) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="editDeadline">Deadline:</label>
                <input type="date" id="editDeadline" name="deadline">
            </div>

            <button type="submit">Update Report Type</button>
            <button type="button" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>

</body>
</html>
