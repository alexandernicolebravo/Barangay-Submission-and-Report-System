@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .stat-card .stat-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem;
    }

    .card-header h5 {
        color: #1e293b;
        font-weight: 600;
    }

    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .input-group-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        color: #334155;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .search-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        width: 300px;
    }

    .search-box:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="page-title" style="color: #1e293b; font-weight: 600;">
            <i class="fas fa-tachometer-alt me-2" style="color: #3b82f6;"></i>
            Dashboard Overview
        </h2>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #dcfce7; color: #166534; border: none; border-radius: 8px;">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #fee2e2; color: #991b1b; border: none; border-radius: 8px;">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card" style="background: linear-gradient(45deg, var(--primary), #60a5fa);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1">Total Users</h6>
                        <h3 class="text-white mb-0">{{ $users->count() }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="background: linear-gradient(45deg, var(--success), #34d399);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1">Active Users</h6>
                        <h3 class="text-white mb-0">{{ $users->where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-check fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="background: linear-gradient(45deg, var(--info), #818cf8);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1">Total Clusters</h6>
                        <h3 class="text-white mb-0">{{ $users->where('role', 'cluster')->count() }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-layer-group fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Management -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-users me-2" style="color: var(--primary);"></i>
            User Management
        </h5>
        <div class="d-flex gap-2">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control search-box" id="userSearch" placeholder="Search...">
            </div>
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text">
                    <i class="fas fa-filter"></i>
                </span>
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="cluster">Cluster</option>
                    <option value="barangay">Barangay</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus"></i>
                <span>Add User</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Cluster</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr data-role="{{ $user->role }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--dark);">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color: var(--gray-600);">{{ $user->email }}</td>
                        <td>
                            <span class="badge" style="background: var(--info-light); color: var(--info);">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="color: var(--gray-600);">
                            {{ $user->cluster ? $user->cluster->name : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge" style="background: {{ $user->is_active ? 'var(--success-light)' : 'var(--danger-light)' }}; color: {{ $user->is_active ? 'var(--success)' : 'var(--danger)' }};">
                                <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm" style="background: var(--primary-light); color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <form action="{{ route('admin.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: var(--danger-light); color: var(--danger); border: none;">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit me-2" style="color: var(--primary);"></i>
                                        Edit User
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <select class="form-select" name="role" required>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="cluster" {{ $user->role == 'cluster' ? 'selected' : '' }}>Cluster</option>
                                                <option value="barangay" {{ $user->role == 'barangay' ? 'selected' : '' }}>Barangay</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="is_active" required>
                                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            <span>Save Changes</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2" style="color: var(--primary);"></i>
                    Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
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
                        <select class="form-select" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="cluster">Cluster</option>
                            <option value="barangay">Barangay</option>
                        </select>
                    </div>
                    <div class="mb-3 cluster-select" style="display: none;">
                        <label class="form-label">Assign to Cluster</label>
                        <select class="form-select" name="cluster_id">
                            <option value="">Select Cluster</option>
                            @foreach($users->where('role', 'cluster') as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Create User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('userSearch').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Role filter
    document.getElementById('roleFilter').addEventListener('change', function() {
        const role = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            if (!role) {
                row.style.display = '';
                return;
            }
            const rowRole = row.dataset.role;
            row.style.display = rowRole === role ? '' : 'none';
        });
    });

    // Show/hide cluster select based on role
    document.querySelector('select[name="role"]').addEventListener('change', function() {
        const clusterSelect = document.querySelector('.cluster-select');
        clusterSelect.style.display = this.value === 'barangay' ? 'block' : 'none';
    });
</script>
@endpush
@endsection
