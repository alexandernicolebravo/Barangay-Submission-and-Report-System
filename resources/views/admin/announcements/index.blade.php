@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Announcements</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Announcements</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-bullhorn me-1"></i> Manage Announcements</div>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create New
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Schedule</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($announcement->image_path)
                                            <div class="me-3">
                                                <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                                     alt="{{ $announcement->title }}" 
                                                     class="rounded" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $announcement->title }}</strong>
                                            @if($announcement->button_text)
                                                <div class="small text-muted">
                                                    Button: {{ $announcement->button_text }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form action="{{ route('admin.announcements.toggle-status', $announcement) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $announcement->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $announcement->priority }}</td>
                                <td>
                                    @if($announcement->starts_at || $announcement->ends_at)
                                        <div class="small">
                                            @if($announcement->starts_at)
                                                <div>From: {{ $announcement->starts_at->format('M d, Y') }}</div>
                                            @endif
                                            @if($announcement->ends_at)
                                                <div>Until: {{ $announcement->ends_at->format('M d, Y') }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No schedule</span>
                                    @endif
                                </td>
                                <td>{{ $announcement->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                        <p class="mb-1">No announcements found</p>
                                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-plus me-1"></i> Create New Announcement
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
