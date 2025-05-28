@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="page-title">
                <i class="fas fa-users"></i>
                User Management
            </h2>
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
    <div class="card mb-3 shadow-sm" style="border: none; border-radius: 8px; overflow: hidden;">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <div class="d-flex flex-wrap align-items-end filter-container">
                        <div class="search-item me-3 mb-0">
                            <label class="filter-label">Search</label>
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="form-control search-input" id="searchInput" placeholder="Search users...">
                            </div>
                        </div>
                        <div class="filter-item me-3 mb-0">
                            <label class="filter-label">Role</label>
                            <select class="form-select filter-select" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="facilitator">Facilitator</option>
                                <option value="barangay">Barangay</option>
                            </select>
                        </div>
                        <div class="filter-item me-3 mb-0">
                            <label class="filter-label">Status</label>
                            <select class="form-select filter-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="filter-item mb-0">
                            <label class="filter-label">Cluster</label>
                            <select class="form-select filter-select" id="clusterFilter">
                                <option value="">All Clusters</option>
                                @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" class="btn btn-success add-user-btn w-100" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-1"></i> Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm" style="border: none; border-radius: 8px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table modern-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Role</th>
                            <th>Cluster</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-{{ $user->user_type }}">
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </td>
                            <td>
                                @if($user->user_type === 'barangay' && $user->cluster)
                                    <span class="cluster-badge" data-cluster-id="{{ $user->cluster->id }}">{{ $user->cluster->name }}</span>
                                @elseif($user->user_type === 'facilitator')
                                    @php
                                        $assignedClusters = $user->assignedClusters;
                                        $clusterIds = $assignedClusters->pluck('id')->toArray();
                                        $clusterNames = $assignedClusters->pluck('name')->toArray();
                                    @endphp
                                    @if(count($clusterIds) > 0)
                                        <span class="cluster-badge"
                                              data-bs-toggle="tooltip"
                                              data-bs-placement="top"
                                              data-cluster-ids="{{ json_encode($clusterIds) }}"
                                              title="{{ implode(', ', $clusterNames) }}">
                                            {{ count($clusterIds) }} cluster(s)
                                        </span>
                                    @else
                                        <span class="cluster-badge cluster-none">No clusters</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="status-indicator status-{{ $user->is_active ? 'active' : 'inactive' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-buttons">
                                    <button type="button" class="btn action-btn edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-user="{{ json_encode($user) }}"
                                            data-user-type="{{ $user->user_type ?? '' }}"
                                            data-save-scroll>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn action-btn {{ $user->is_active ? 'deactivate-btn' : 'activate-btn' }}"
                                            onclick="confirmStatusChange({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users text-muted mb-2"></i>
                                    <p>No users found</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" data-save-scroll>
                                        <i class="fas fa-user-plus me-1"></i> Add User
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination info and links -->
            <div class="pagination-wrapper">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="pagination-info" id="paginationInfo">
                            @if($users->total() > 0)
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                            @else
                                No users found
                            @endif
                        </div>
                        <div class="dropdown per-page-dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $users->perPage() }} per page
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="perPageDropdown">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10 per page</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25 per page</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 per page</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}">100 per page</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="pagination-container">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <form action="{{ route('admin.users.store') }}" method="POST" data-ajax data-ajax-refresh="#usersTable" data-ajax-reload="true">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Add New User
                    </h5>
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
                        <select class="form-select" name="user_type" id="userTypeSelect" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="facilitator">Facilitator</option>
                            <option value="barangay">Barangay</option>
                        </select>
                    </div>
                    <!-- Barangay Cluster Assignment -->
                    <div class="mb-3" id="barangayClusterContainer" style="display: none;">
                        <label class="form-label">Assign to Cluster <span class="text-danger">*</span></label>
                        <select class="form-select" name="cluster_id" id="clusterSelect">
                            <option value="">Select Cluster</option>
                            @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-danger">Required for Barangay users</div>
                    </div>

                    <!-- Facilitator Cluster Assignments -->
                    <div class="mb-3" id="facilitatorClustersContainer" style="display: none;">
                        <label class="form-label">Assign to Clusters <span class="text-danger">*</span></label>
                        <div class="card border" style="border-color: #e9ecef !important;">
                            <div class="card-body bg-light">
                                @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="clusters[]" value="{{ $cluster->id }}" id="cluster_{{ $cluster->id }}">
                                    <label class="form-check-label" for="cluster_{{ $cluster->id }}">
                                        {{ $cluster->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-text text-danger">Select at least one cluster for Facilitator users</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-save-scroll>Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <form id="editUserForm" method="POST" data-ajax data-ajax-refresh="#usersTable" data-ajax-reload="true">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Edit User
                    </h5>
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
                        <div id="editRoleSelectContainer">
                            <!-- The select element will be dynamically created here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editUserStatus" name="is_active" value="1">
                            <label class="form-check-label" for="editUserStatus">Active</label>
                        </div>
                        <div class="form-text">Toggle to activate or deactivate this user</div>
                    </div>
                    <!-- Barangay Cluster Assignment -->
                    <div class="mb-3" id="editBarangayClusterContainer" style="display: none;">
                        <label class="form-label">Assign to Cluster <span class="text-danger">*</span></label>
                        <select class="form-select" name="cluster_id" id="editClusterSelect">
                            <option value="">Select Cluster</option>
                            @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-danger">Required for Barangay users</div>
                    </div>

                    <!-- Facilitator Cluster Assignments -->
                    <div class="mb-3" id="editFacilitatorClustersContainer" style="display: none;">
                        <label class="form-label">Assign to Clusters <span class="text-danger">*</span></label>
                        <div class="card border" style="border-color: #e9ecef !important;">
                            <div class="card-body bg-light">
                                @foreach(App\Models\Cluster::where('is_active', true)->get() as $cluster)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="clusters[]" value="{{ $cluster->id }}" id="edit_cluster_{{ $cluster->id }}">
                                    <label class="form-check-label" for="edit_cluster_{{ $cluster->id }}">
                                        {{ $cluster->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-text text-danger">Select at least one cluster for Facilitator users</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-save-scroll>Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Change Confirmation Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt text-primary me-2"></i>
                    Confirm Status Change
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <p id="statusChangeMessage" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="statusChangeForm" method="POST" data-ajax data-ajax-refresh="#usersTable" data-ajax-reload="true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary" data-save-scroll>Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --primary: #4e73df;
        --primary-light: #eaefff;
        --primary-dark: #2e59d9;
        --success: #1cc88a;
        --danger: #e74a3b;
        --warning: #f6c23e;
        --info: #36b9cc;
        --gray-100: #f8f9fc;
        --gray-200: #eaecf4;
        --gray-300: #dddfeb;
        --gray-400: #d1d3e2;
        --gray-500: #b7b9cc;
        --gray-600: #858796;
        --gray-700: #6e707e;
        --gray-800: #5a5c69;
        --gray-900: #3a3b45;
        --border-radius: 8px;
        --box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    /* Modern Card Styles */
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 1.5rem;
    }

    /* Search and Filter Styles */
    .filter-container {
        flex-wrap: wrap;
        row-gap: 1rem;
    }

    .search-item {
        display: flex;
        flex-direction: column;
        min-width: 250px;
    }

    .search-container {
        position: relative;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
        z-index: 10;
    }

    .search-input {
        padding-left: 35px;
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        height: 38px;
        width: 100%;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .search-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        border-color: var(--primary-light);
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        min-width: 130px;
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        height: 38px;
        padding: 0 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        width: 100%;
    }

    .filter-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Fix for select option text */
    .filter-select option {
        font-size: 0.875rem;
        padding: 8px;
    }

    .add-user-btn {
        border-radius: 6px;
        background-color: var(--success);
        border: none;
        height: 38px;
        padding: 0.375rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .add-user-btn:hover {
        background-color: #169b6b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .add-user-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Modern Table Styles */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .modern-table thead {
        background-color: var(--gray-100);
    }

    .modern-table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-700);
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .modern-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--gray-200);
    }

    .modern-table tbody tr {
        transition: all 0.2s;
    }

    .modern-table tbody tr:hover {
        background-color: var(--gray-100);
    }

    /* User Info Styles */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .user-name {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 2px;
    }

    .user-email {
        font-size: 0.8rem;
        color: var(--gray-600);
    }

    /* Badge Styles */
    .role-badge {
        display: inline-block;
        padding: 0.35em 0.8em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 50px;
    }

    .role-admin {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .role-facilitator {
        background-color: rgba(54, 185, 204, 0.15);
        color: var(--info);
    }

    .role-barangay {
        background-color: rgba(28, 200, 138, 0.15);
        color: var(--success);
    }

    .cluster-badge {
        display: inline-block;
        padding: 0.35em 0.8em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 50px;
        background-color: rgba(78, 115, 223, 0.15);
        color: var(--primary);
    }

    .cluster-none {
        background-color: rgba(246, 194, 62, 0.15);
        color: var(--warning);
    }

    .status-indicator {
        display: inline-block;
        padding: 0.35em 0.8em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 50px;
    }

    .status-active {
        background-color: rgba(28, 200, 138, 0.15);
        color: var(--success);
    }

    .status-inactive {
        background-color: rgba(231, 74, 59, 0.15);
        color: var(--danger);
    }

    /* Action Button Styles */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
    }

    .edit-btn {
        background-color: rgba(78, 115, 223, 0.1);
        color: var(--primary);
    }

    .edit-btn:hover {
        background-color: var(--primary);
        color: white;
    }

    .deactivate-btn {
        background-color: rgba(231, 74, 59, 0.1);
        color: var(--danger);
    }

    .deactivate-btn:hover {
        background-color: var(--danger);
        color: white;
    }

    .activate-btn {
        background-color: rgba(28, 200, 138, 0.1);
        color: var(--success);
    }

    .activate-btn:hover {
        background-color: var(--success);
        color: white;
    }

    /* Pagination Styles */
    .pagination-wrapper {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-200);
    }

    .pagination-info {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-item .page-link {
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        padding: 0.375rem 0.75rem;
        margin: 0 0.2rem;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: var(--gray-500);
        border-color: var(--gray-300);
    }

    .pagination .page-item .page-link:hover {
        background-color: var(--gray-200);
        border-color: var(--gray-300);
        color: var(--gray-800);
    }

    .pagination .page-item.active .page-link:hover {
        background-color: var(--primary);
        color: white;
    }

    /* Per-page dropdown styles */
    .per-page-dropdown .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-color: var(--gray-300);
        color: var(--gray-700);
        background-color: white;
    }

    .per-page-dropdown .btn:hover {
        background-color: var(--gray-100);
        border-color: var(--gray-400);
    }

    .per-page-dropdown .dropdown-menu {
        min-width: 100%;
        padding: 0.25rem 0;
        font-size: 0.75rem;
        border-radius: 6px;
        border-color: var(--gray-300);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .per-page-dropdown .dropdown-item {
        padding: 0.25rem 0.75rem;
        color: var(--gray-700);
    }

    .per-page-dropdown .dropdown-item:hover {
        background-color: var(--gray-100);
        color: var(--gray-900);
    }

    /* Empty State Styles */
    .empty-state {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: var(--gray-400);
        margin-bottom: 1rem;
    }

    .empty-state p {
        font-size: 1rem;
        color: var(--gray-600);
        margin-bottom: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Specific scroll position handling for user management
    window.addEventListener('load', function() {
        // Force save scroll position on all form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (typeof saveScrollPosition === 'function') {
                    saveScrollPosition();
                }
            });
        });

        // Save position on all modal opens
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                if (typeof saveScrollPosition === 'function') {
                    saveScrollPosition();
                }
            });
        });

        // Save position on all action buttons
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (typeof saveScrollPosition === 'function') {
                    saveScrollPosition();
                }
            });
        });
    });

    // Client-side filtering without page refresh
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const clusterFilter = document.getElementById('clusterFilter');
    const userRows = document.querySelectorAll('table.modern-table tbody tr');

    // Add debounce function to prevent too many filter operations
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }

    // Function to filter the table rows
    const filterTable = debounce(() => {
        const searchText = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const clusterValue = clusterFilter.value;

        userRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const roleCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const statusCell = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const clusterCell = row.querySelector('td:nth-child(3)');

            // Check if the row matches all filter criteria
            const matchesSearch = searchText === '' || rowText.includes(searchText);
            const matchesRole = roleValue === '' || roleCell.includes(roleValue);
            const matchesStatus = statusValue === '' || statusCell.includes(statusValue);

            // Special handling for cluster filter
            let matchesCluster = true;
            if (clusterValue !== '') {
                // Check if the cluster cell contains a data attribute or specific text
                const clusterText = clusterCell.textContent.toLowerCase();

                // Check for single cluster (barangay)
                const singleClusterBadge = clusterCell.querySelector('[data-cluster-id]');
                if (singleClusterBadge) {
                    matchesCluster = singleClusterBadge.dataset.clusterId === clusterValue;
                }
                // Check for multiple clusters (facilitator)
                else {
                    const multiClusterBadge = clusterCell.querySelector('[data-cluster-ids]');
                    if (multiClusterBadge) {
                        try {
                            const clusterIds = JSON.parse(multiClusterBadge.dataset.clusterIds);
                            matchesCluster = clusterIds.includes(parseInt(clusterValue)) || clusterIds.includes(clusterValue);
                        } catch (e) {
                            console.error('Error parsing cluster IDs:', e);
                        }
                    }
                    // Fallback to text matching
                    else {
                        const clusterOption = clusterFilter.querySelector(`option[value="${clusterValue}"]`);
                        const clusterName = clusterOption ? clusterOption.textContent.toLowerCase() : '';
                        matchesCluster = clusterName !== '' && clusterText.includes(clusterName);
                    }
                }
            }

            // Show or hide the row based on all filters
            if (matchesSearch && matchesRole && matchesStatus && matchesCluster) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update the empty state message if no rows are visible
        updateEmptyState();
    }, 300);

    // Function to update empty state visibility
    function updateEmptyState() {
        let visibleRows = 0;
        userRows.forEach(row => {
            if (row.style.display !== 'none' && !row.classList.contains('empty-filter-results')) {
                visibleRows++;
            }
        });

        // Get or create the empty state row
        let emptyStateRow = document.querySelector('.empty-filter-results');
        if (!emptyStateRow) {
            emptyStateRow = document.createElement('tr');
            emptyStateRow.className = 'empty-filter-results';
            emptyStateRow.innerHTML = `
                <td colspan="5" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-filter text-muted mb-2"></i>
                        <p>No users match your filter criteria</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFiltersBtn">
                            Clear Filters
                        </button>
                    </div>
                </td>
            `;
            document.querySelector('table.modern-table tbody').appendChild(emptyStateRow);

            // Add event listener to the clear filters button
            document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);
        }

        // Show or hide the empty state row
        emptyStateRow.style.display = visibleRows === 0 ? '' : 'none';
    }

    // Function to clear all filters
    function clearFilters() {
        if (searchInput) searchInput.value = '';
        if (roleFilter) roleFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (clusterFilter) clusterFilter.value = '';

        // Show all rows
        userRows.forEach(row => {
            row.style.display = '';
        });

        // Hide the empty state
        const emptyStateRow = document.querySelector('.empty-filter-results');
        if (emptyStateRow) {
            emptyStateRow.style.display = 'none';
        }
    }

    // Add event listeners for dynamic filtering
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', filterTable);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }

    if (clusterFilter) {
        clusterFilter.addEventListener('change', filterTable);
    }

    // User type selection handling
    const userTypeSelect = document.getElementById('userTypeSelect');
    const barangayClusterContainer = document.getElementById('barangayClusterContainer');
    const facilitatorClustersContainer = document.getElementById('facilitatorClustersContainer');

    // Function to update container visibility based on selected user type
    function updateContainerVisibility(userType) {
        // Hide all containers first
        if (barangayClusterContainer) barangayClusterContainer.style.display = 'none';
        if (facilitatorClustersContainer) facilitatorClustersContainer.style.display = 'none';

        // Show the appropriate container based on user type
        if (userType === 'barangay') {
            if (barangayClusterContainer) barangayClusterContainer.style.display = 'block';
        } else if (userType === 'facilitator') {
            if (facilitatorClustersContainer) facilitatorClustersContainer.style.display = 'block';
        }
    }

    // Initialize container visibility based on current selection
    if (userTypeSelect) {
        updateContainerVisibility(userTypeSelect.value);

        // Add change event listener
        userTypeSelect.addEventListener('change', function() {
            updateContainerVisibility(this.value);
        });
    }

    // Add animation to action buttons
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Edit user modal handling
    document.getElementById('editUserModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const user = JSON.parse(button.getAttribute('data-user'));
        console.log('User data:', user);
        const form = this.querySelector('form');

        // Set the correct form action URL using the route name
        form.action = "{{ route('admin.users.update', '') }}/" + user.id;

        // Set basic user information
        document.getElementById('editName').value = user.name;
        document.getElementById('editEmail').value = user.email;

        // Get the user type from data attributes
        const userType = button.getAttribute('data-user-type');

        console.log('User data from attributes:', {
            'data-user-type': button.getAttribute('data-user-type'),
            'userType': userType,
            'user.user_type': user.user_type
        });

        // Dump the entire user object to see what's available
        console.log('FULL USER OBJECT:', JSON.stringify(user, null, 2));

        // Create a new select element with the correct role selected
        const selectContainer = document.getElementById('editRoleSelectContainer');
        selectContainer.innerHTML = ''; // Clear any existing content

        // Determine the correct user type to select first
        const effectiveUserType = user.user_type || userType;
        console.log('Effective user type determined as:', effectiveUserType);

        // Create the select element using HTML to ensure proper selection
        selectContainer.innerHTML = `
            <select class="form-select" name="user_type" id="editUserTypeSelect" required>
                <option value="admin" ${effectiveUserType === 'admin' ? 'selected' : ''}>Admin</option>
                <option value="facilitator" ${effectiveUserType === 'facilitator' ? 'selected' : ''}>Facilitator</option>
                <option value="barangay" ${effectiveUserType === 'barangay' ? 'selected' : ''}>Barangay</option>
            </select>
        `;

        // Get the select element reference
        const select = document.getElementById('editUserTypeSelect');

        // Log the selected value
        console.log('Select element created with value:', select.value);

        // Force the select element to update its value
        select.dispatchEvent(new Event('change'));

        // Add change event listener to the new select
        select.addEventListener('change', function() {
            updateEditContainerVisibility(this.value);
        });

        console.log('Created new select with role:', userType, 'selected:', select.value);

        // Set user status
        document.getElementById('editUserStatus').checked = user.is_active === 1 || user.is_active === true;

        const barangayClusterContainer = document.getElementById('editBarangayClusterContainer');
        const facilitatorClustersContainer = document.getElementById('editFacilitatorClustersContainer');
        const clusterSelect = document.getElementById('editClusterSelect');

        // Remove any existing cluster info elements
        const existingBarangayClusterInfo = barangayClusterContainer.querySelector('.alert');
        if (existingBarangayClusterInfo) {
            existingBarangayClusterInfo.remove();
        }

        const existingFacilitatorClusterInfo = facilitatorClustersContainer.querySelector('.alert');
        if (existingFacilitatorClusterInfo) {
            existingFacilitatorClusterInfo.remove();
        }

        // Hide all containers first
        barangayClusterContainer.style.display = 'none';
        facilitatorClustersContainer.style.display = 'none';

        // Show the appropriate container based on user type
        // Get the current selected value from the new select element
        const currentUserType = document.getElementById('editUserTypeSelect').value;
        console.log('Current user type from select:', currentUserType);

        if (currentUserType === 'barangay') {
            barangayClusterContainer.style.display = 'block';

            // Reset and set cluster select value if user is a barangay
            clusterSelect.value = '';

            // Make sure the correct cluster is selected
            if (user.cluster_id) {
                for (let i = 0; i < clusterSelect.options.length; i++) {
                    if (clusterSelect.options[i].value == user.cluster_id) {
                        clusterSelect.options[i].selected = true;
                        break;
                    }
                }
            }

            // Display current cluster information
            if (user.cluster && user.cluster.name) {
                const clusterInfo = document.createElement('div');
                clusterInfo.className = 'alert alert-info mt-2';
                clusterInfo.innerHTML = `<small>Currently assigned to: <strong>${user.cluster.name}</strong></small>`;
                barangayClusterContainer.appendChild(clusterInfo);
            }

        } else if (currentUserType === 'facilitator') {
            facilitatorClustersContainer.style.display = 'block';

            // Reset all cluster checkboxes
            const clusterCheckboxes = facilitatorClustersContainer.querySelectorAll('input[type="checkbox"]');
            clusterCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Check the appropriate cluster checkboxes if user is a facilitator
            console.log('Full user object:', user);

            // Get all checkboxes
            const allCheckboxes = facilitatorClustersContainer.querySelectorAll('input[type="checkbox"]');
            console.log('All checkboxes:', allCheckboxes);

            // Create a list to store assigned cluster names
            let assignedClusterNames = [];

            // First try with assigned_clusters array/object
            if (user.assigned_clusters) {
                console.log('Using assigned_clusters:', user.assigned_clusters);

                // Convert to array if it's not already
                const clusterIds = Array.isArray(user.assigned_clusters)
                    ? user.assigned_clusters
                    : Object.values(user.assigned_clusters);

                console.log('Cluster IDs:', clusterIds);

                // Check the appropriate checkboxes
                clusterIds.forEach(clusterId => {
                    // Try to find the checkbox
                    const checkboxId = 'edit_cluster_' + clusterId;
                    console.log('Looking for checkbox with ID:', checkboxId);

                    const checkbox = document.getElementById(checkboxId);
                    if (checkbox) {
                        checkbox.checked = true;
                        console.log('Checkbox found and checked:', checkbox);

                        // Get the cluster name from the label
                        const label = checkbox.nextElementSibling;
                        if (label) {
                            assignedClusterNames.push(label.textContent.trim());
                        }
                    } else {
                        console.log('Checkbox not found for cluster ID:', clusterId);
                    }
                });
            }

            // If we have assigned_clusters_names, use that as a fallback
            if (user.assigned_clusters_names) {
                console.log('Using assigned_clusters_names:', user.assigned_clusters_names);

                // Split the names
                const clusterNames = user.assigned_clusters_names.split(', ');
                console.log('Cluster names:', clusterNames);

                // Check checkboxes based on names
                allCheckboxes.forEach(checkbox => {
                    const label = checkbox.nextElementSibling;
                    if (label) {
                        const labelText = label.textContent.trim();
                        if (clusterNames.includes(labelText)) {
                            checkbox.checked = true;
                            console.log('Checkbox checked based on name:', labelText);

                            // Add to assigned names if not already there
                            if (!assignedClusterNames.includes(labelText)) {
                                assignedClusterNames.push(labelText);
                            }
                        }
                    }
                });
            }

            // If we still don't have any assigned clusters, try one more approach
            if (assignedClusterNames.length === 0 && user.assigned_clusters) {
                console.log('Trying alternative approach with assigned_clusters');

                // Try to match by ID attribute
                allCheckboxes.forEach(checkbox => {
                    const checkboxId = checkbox.id;
                    const clusterId = checkboxId.replace('edit_cluster_', '');

                    // Check if this ID is in the assigned clusters
                    if (Array.isArray(user.assigned_clusters)) {
                        if (user.assigned_clusters.includes(parseInt(clusterId)) ||
                            user.assigned_clusters.includes(clusterId)) {
                            checkbox.checked = true;
                            console.log('Checkbox checked by ID match:', checkboxId);

                            // Get the cluster name
                            const label = checkbox.nextElementSibling;
                            if (label) {
                                assignedClusterNames.push(label.textContent.trim());
                            }
                        }
                    } else if (typeof user.assigned_clusters === 'object') {
                        // Check if the ID is in the object values
                        const values = Object.values(user.assigned_clusters);
                        if (values.includes(parseInt(clusterId)) ||
                            values.includes(clusterId)) {
                            checkbox.checked = true;
                            console.log('Checkbox checked by ID match (object):', checkboxId);

                            // Get the cluster name
                            const label = checkbox.nextElementSibling;
                            if (label) {
                                assignedClusterNames.push(label.textContent.trim());
                            }
                        }
                    }
                });
            }

            // Display current cluster assignments
            if (assignedClusterNames.length > 0) {
                const clusterInfo = document.createElement('div');
                clusterInfo.className = 'alert alert-info mt-2';
                clusterInfo.innerHTML = `<small>Currently assigned to: <strong>${assignedClusterNames.join(', ')}</strong></small>`;
                facilitatorClustersContainer.appendChild(clusterInfo);
                console.log('Displayed assigned clusters:', assignedClusterNames);
            } else if (user.assigned_clusters_names) {
                // If we have the names but couldn't check boxes, still show the info
                const clusterInfo = document.createElement('div');
                clusterInfo.className = 'alert alert-info mt-2';
                clusterInfo.innerHTML = `<small>Currently assigned to: <strong>${user.assigned_clusters_names}</strong></small>`;
                facilitatorClustersContainer.appendChild(clusterInfo);
                console.log('Displayed assigned clusters from names:', user.assigned_clusters_names);
            } else {
                console.log('No assigned clusters found');
            }
        }

        // Remove existing event listeners to prevent duplicates
        const editUserTypeSelect = document.getElementById('editUserTypeSelect');
        const oldEditUserTypeSelect = editUserTypeSelect.cloneNode(true);
        editUserTypeSelect.parentNode.replaceChild(oldEditUserTypeSelect, editUserTypeSelect);

        // Function to update edit form container visibility
        function updateEditContainerVisibility(userType) {
            // Get the edit form containers
            const editBarangayContainer = document.getElementById('editBarangayClusterContainer');
            const editFacilitatorContainer = document.getElementById('editFacilitatorClustersContainer');

            // Hide all edit form containers
            editBarangayContainer.style.display = 'none';
            editFacilitatorContainer.style.display = 'none';

            // Show the appropriate container based on user type
            if (userType === 'barangay') {
                editBarangayContainer.style.display = 'block';
            } else if (userType === 'facilitator') {
                editFacilitatorContainer.style.display = 'block';
            }

            console.log('Updated container visibility for user type:', userType);
        }

        // Add change event listener to edit user type select
        document.getElementById('editUserTypeSelect').addEventListener('change', function() {
            updateEditContainerVisibility(this.value);
        });
    });

    // Status change confirmation
    function confirmStatusChange(userId, newStatus) {
        // Save scroll position before showing modal
        sessionStorage.setItem('scrollPosition', window.scrollY);

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
