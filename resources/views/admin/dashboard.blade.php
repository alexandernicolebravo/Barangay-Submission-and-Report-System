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
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $users->count() }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $users->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $clusters->count() }}</div>
                    <div class="stat-label">Total Clusters</div>
                </div>
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Registration Form -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-user-plus me-2" style="color: #3b82f6;"></i>
            Add New User
        </h5>
        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#userForm">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="card-body collapse show" id="userForm">
        <form method="POST" action="{{ route('admin.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-select" name="role" required>
                            <option value="cluster">Cluster</option>
                            <option value="barangay">Barangay</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign to Cluster (if Barangay)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                        <select class="form-select" name="cluster_id">
                            <option value="">None</option>
                            @foreach($clusters as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" style="background: #3b82f6; border: none;">
                        <i class="fas fa-user-plus"></i>
                        <span>Create User</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- User List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-users me-2" style="color: #3b82f6;"></i>
            User Management
        </h5>
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <i class="fas fa-search" style="color: #64748b;"></i>
            </span>
            <input type="text" class="form-control search-box" id="userSearch" placeholder="Search users...">
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
                        <th>Cluster Assigned</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #1e293b;">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color: #64748b;">{{ $user->email }}</td>
                        <td>
                            <span class="badge" style="background: {{ $user->role === 'cluster' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(139, 92, 246, 0.1)' }}; color: {{ $user->role === 'cluster' ? '#3b82f6' : '#8b5cf6' }};">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->role == 'barangay' && $user->cluster_id)
                                <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                                    {{ $clusters->firstWhere('id', $user->cluster_id)->name ?? 'Unassigned' }}
                                </span>
                            @else
                                <span style="color: #94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background: {{ $user->is_active ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $user->is_active ? '#22c55e' : '#ef4444' }};">
                                <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: {{ $user->is_active ? 'rgba(239, 68, 68, 0.1)' : 'rgba(34, 197, 94, 0.1)' }}; color: {{ $user->is_active ? '#ef4444' : '#22c55e' }}; border: none;">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                    <span>{{ $user->is_active ? 'Deactivate' : 'Activate' }}</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('userSearch').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
