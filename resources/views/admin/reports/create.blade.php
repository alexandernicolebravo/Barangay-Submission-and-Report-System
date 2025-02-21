<form action="{{ route('admin.reports.store') }}" method="POST">
    @csrf
    <label>Title:</label>
    <input type="text" name="title" required>
    <label>Description:</label>
    <textarea name="description" required></textarea>
    <button type="submit">Create Report</button>
</form>
