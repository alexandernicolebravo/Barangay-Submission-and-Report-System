<!-- Users Table Content -->
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
                        <p>No users found matching your criteria</p>
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
                    <li><a class="dropdown-item per-page-link" href="#" data-per-page="10">10 per page</a></li>
                    <li><a class="dropdown-item per-page-link" href="#" data-per-page="25">25 per page</a></li>
                    <li><a class="dropdown-item per-page-link" href="#" data-per-page="50">50 per page</a></li>
                    <li><a class="dropdown-item per-page-link" href="#" data-per-page="100">100 per page</a></li>
                </ul>
            </div>
        </div>
        <div class="pagination-container">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
