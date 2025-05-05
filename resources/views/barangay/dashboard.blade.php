@extends('layouts.barangay')

@section('title', 'Barangay Dashboard')
@section('page-title', 'Barangay Dashboard')

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="stat-card bg-primary text-white">
                <i class="fas fa-file-alt"></i>
                <h3>Total Reports</h3>
                <p>{{ $totalReports }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card bg-success text-white">
                <i class="fas fa-check-circle"></i>
                <h3>Approved</h3>
                <p>{{ $approvedReports }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card bg-warning text-white">
                <i class="fas fa-clock"></i>
                <h3>Pending</h3>
                <p>{{ $pendingReports }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card bg-danger text-white">
                <i class="fas fa-times-circle"></i>
                <h3>Rejected</h3>
                <p>{{ $rejectedReports }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Reports</h5>
                </div>
                <div class="card-body">
                    @if($recentReports->isEmpty())
                        <p class="text-muted">No recent reports</p>
                    @else
                        @foreach($recentReports as $report)
                            <div class="report-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6>{{ $report->reportType->name }}</h6>
                                    <span class="badge bg-{{ $report->status === 'approved' ? 'success' : ($report->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <p class="text-muted mb-0">
                                    <small>Submitted: {{ $report->created_at->format('M d, Y') }}</small>
                                </p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Deadlines</h5>
                </div>
                <div class="card-body">
                    @if($upcomingDeadlines->isEmpty())
                        <p class="text-muted">No upcoming deadlines</p>
                    @else
                        @foreach($upcomingDeadlines as $deadline)
                            <div class="report-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6>{{ $deadline->name }}</h6>
                                    <span class="badge bg-info">
                                        {{ ucfirst($deadline->frequency) }}
                                    </span>
                                </div>
                                <p class="text-muted mb-0">
                                    <small>Deadline: {{ $deadline->deadline->format('M d, Y') }}</small>
                                </p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
