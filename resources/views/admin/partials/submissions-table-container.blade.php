<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Report</th>
                <th>User</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @include('admin.partials.submissions-table')
        </tbody>
    </table>
</div>

@if($reports->hasPages())
<div class="pagination-container">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
        </div>
        <div>
            {{ $reports->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif
