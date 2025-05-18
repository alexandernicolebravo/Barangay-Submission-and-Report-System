@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="page-title">
                <i class="fas fa-users"></i>
                User Management
            </h2>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                    </div>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Cluster</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'cluster' ? 'info' : 'success' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->role === 'barangay' && $user->cluster)
                                    {{ $user->cluster->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-user="{{ json_encode($user) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}"
                                            onclick="confirmStatusChange({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="roleSelect" required>
                            <option value="">Select Role</option>
                            <option value="cluster">Cluster</option>
                            <option value="barangay">Barangay</option>
                        </select>
                    </div>
                    <div class="mb-3" id="clusterSelectContainer" style="display: none;">
                        <label class="form-label">Assign to Cluster <span class="text-danger">*</span></label>
                        <select class="form-select" name="cluster_id" id="clusterSelect">
                            <option value="">Select Cluster</option>
                            @foreach($users->whereIn('role', ['cluster', 'facilitator', 'admin'])->where('is_active', true) as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }} ({{ ucfirst($cluster->role) }})</option>
                            @endforeach
                        </select>
                        <div class="form-text text-danger">Required for Barangay users</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="editRoleSelect" required>
                            <option value="cluster">Cluster</option>
                            <option value="barangay">Barangay</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editClusterSelectContainer">
                        <label class="form-label">Assign to Cluster <span class="text-danger">*</span></label>
                        <select class="form-select" name="cluster_id" id="editClusterSelect">
                            <option value="">Select Cluster</option>
                            @foreach($users->whereIn('role', ['cluster', 'facilitator', 'admin'])->where('is_active', true) as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }} ({{ ucfirst($cluster->role) }})</option>
                            @endforeach
                        </select>
                        <div class="form-text text-danger">Required for Barangay users</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Change Confirmation Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusChangeMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="statusChangeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (!status) {
                row.style.display = '';
                return;
            }

            const statusCell = row.querySelector('td:nth-child(5)');
            const isActive = statusCell.textContent.trim().toLowerCase() === 'active';
            row.style.display = (status === 'active' && isActive) || (status === 'inactive' && !isActive) ? '' : 'none';
        });
    });

    // Role selection handling
    document.getElementById('roleSelect').addEventListener('change', function() {
        const clusterContainer = document.getElementById('clusterSelectContainer');
        clusterContainer.style.display = this.value === 'barangay' ? 'block' : 'none';
    });

    // Edit user modal handling
    document.getElementById('editUserModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const user = JSON.parse(button.getAttribute('data-user'));
        const form = this.querySelector('form');

        // Set the correct form action URL using the route name
        form.action = "{{ route('admin.users.update', '') }}/" + user.id;

        document.getElementById('editName').value = user.name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRoleSelect').value = user.role;

        const clusterContainer = document.getElementById('editClusterSelectContainer');
        const clusterSelect = document.getElementById('editClusterSelect');

        // Set initial display of cluster container based on role
        clusterContainer.style.display = user.role === 'barangay' ? 'block' : 'none';

        // Reset cluster select value
        clusterSelect.value = '';

        // If user is a barangay and has a cluster_id, set the cluster select value
        if (user.role === 'barangay' && user.cluster_id) {
            clusterSelect.value = user.cluster_id;
        }

        // Remove existing event listeners to prevent duplicates
        const editRoleSelect = document.getElementById('editRoleSelect');
        const oldEditRoleSelect = editRoleSelect.cloneNode(true);
        editRoleSelect.parentNode.replaceChild(oldEditRoleSelect, editRoleSelect);

        // Add event listener to handle role change in edit form
        document.getElementById('editRoleSelect').addEventListener('change', function() {
            const isBarangay = this.value === 'barangay';
            clusterContainer.style.display = isBarangay ? 'block' : 'none';

            // If changing to cluster role, clear the cluster_id
            if (!isBarangay) {
                clusterSelect.value = '';
            }
        });
    });

    // Status change confirmation
    function confirmStatusChange(userId, newStatus) {
        const modal = document.getElementById('statusChangeModal');
        const message = document.getElementById('statusChangeMessage');
        const form = document.getElementById('statusChangeForm');

        message.textContent = `Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this user?`;

        // Set the correct form action URL using the route name
        form.action = "{{ route('admin.users.destroy', '') }}/" + userId;

        new bootstrap.Modal(modal).show();
    }
</script>
@endpush
@endsection
