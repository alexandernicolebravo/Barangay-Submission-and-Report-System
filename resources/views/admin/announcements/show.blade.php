@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">View Announcement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
        <li class="breadcrumb-item active">View</li>
    </ol>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-bullhorn me-1"></i> Announcement Details</div>
                    <div>
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="announcement-preview mb-4 p-4 rounded" style="background-color: {{ $announcement->background_color }}; color: white;">
                        <div class="row">
                            <div class="col-md-{{ $announcement->image_path ? '7' : '12' }}">
                                <div class="announcement-badge d-inline-block px-3 py-1 rounded-pill mb-3" style="background-color: rgba(255,255,255,0.2);">
                                    @if(Str::contains(strtolower($announcement->title), ['congratulations', 'award', 'recognition', 'achievement']))
                                        <i class="fas fa-award me-2"></i> Recognition
                                    @elseif(Str::contains(strtolower($announcement->title), ['update', 'notice', 'alert']))
                                        <i class="fas fa-bell me-2"></i> Important Update
                                    @elseif(Str::contains(strtolower($announcement->title), ['event', 'meeting', 'conference']))
                                        <i class="fas fa-calendar me-2"></i> Upcoming Event
                                    @else
                                        <i class="fas fa-info-circle me-2"></i> Announcement
                                    @endif
                                </div>
                                <h2 class="mb-3">{{ $announcement->title }}</h2>
                                <div class="mb-4">
                                    {!! $announcement->content !!}
                                </div>
                                @if($announcement->button_text && $announcement->button_link)
                                    <a href="{{ $announcement->button_link }}" class="btn btn-light" target="_blank">
                                        {{ $announcement->button_text }}
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                @endif
                            </div>
                            @if($announcement->image_path)
                                <div class="col-md-5">
                                    <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                         alt="{{ $announcement->title }}" 
                                         class="img-fluid rounded shadow" 
                                         style="border: 5px solid rgba(255,255,255,0.1);">
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Content Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">Title</th>
                            <td>{{ $announcement->title }}</td>
                        </tr>
                        <tr>
                            <th>Content</th>
                            <td>{!! $announcement->content !!}</td>
                        </tr>
                        <tr>
                            <th>Button</th>
                            <td>
                                @if($announcement->button_text && $announcement->button_link)
                                    Text: {{ $announcement->button_text }}<br>
                                    Link: <a href="{{ $announcement->button_link }}" target="_blank">{{ $announcement->button_link }}</a>
                                @else
                                    <span class="text-muted">No button</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i> Settings
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 150px;">Status</th>
                            <td>
                                <span class="badge {{ $announcement->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>{{ $announcement->priority }}</td>
                        </tr>
                        <tr>
                            <th>Background</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $announcement->background_color }}; border-radius: 4px;"></div>
                                    {{ $announcement->background_color }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>
                                @if($announcement->starts_at)
                                    {{ $announcement->starts_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>
                                @if($announcement->ends_at)
                                    {{ $announcement->ends_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $announcement->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $announcement->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
