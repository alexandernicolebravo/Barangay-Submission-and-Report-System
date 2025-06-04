@extends('layouts.barangay')

@section('title', 'Overdue Reports')
@section('page-title', 'Overdue Reports')

@section('content')
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

    <!-- Modern Filter Section -->
    <div class="filter-section mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-white fw-medium">
                    <i class="fas fa-search me-2"></i>Search Reports
                </label>
                <input type="text"
                       class="form-control modern-input"
                       id="searchInput"
                       placeholder="Search by report name..."
                       autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label text-white fw-medium">
                    <i class="fas fa-calendar-alt me-2"></i>Report Type
                </label>
                <select class="form-select modern-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semestral">Semestral</option>
                    <option value="annual">Annual</option>
                    <option value="executive_order">Executive Order</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-white fw-medium">
                    <i class="fas fa-clock me-2"></i>Overdue Period
                </label>
                <select class="form-select modern-select" id="overdueFilter">
                    <option value="">All Overdue</option>
                    <option value="week">Past Week</option>
                    <option value="month">Past Month</option>
                    <option value="quarter">Past Quarter</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-light w-100" id="clearFilters">
                    <i class="fas fa-times me-2"></i>Clear
                </button>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-white-50">
                <i class="fas fa-info-circle me-2"></i>
                <span id="reportCount">{{ $reports->total() }}</span> overdue reports found
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-light btn-sm" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Overdue Reports
                </h5>
                <div class="d-flex align-items-center gap-3">
                    <small class="text-muted">
                        Total: <span class="fw-bold text-danger">{{ $reports->total() }}</span>
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($reports->isEmpty())
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 class="empty-title mb-3">Excellent! No Overdue Reports</h4>
                    <p class="empty-description mb-4">
                        You're all caught up! All your reports have been submitted on time.<br>
                        Keep up the great work maintaining your submission schedule.
                    </p>
                    <div class="empty-actions">
                        <a href="{{ route('barangay.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Return to Dashboard
                        </a>
                        <a href="{{ route('barangay.view-reports') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-eye me-2"></i>View All Reports
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0" id="reportsTable">
                        <thead class="table-header">
                            <tr>
                                <th class="border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        Report Details
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        Type
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock me-2 text-primary"></i>
                                        Deadline
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-2 text-primary"></i>
                                        Status
                                    </div>
                                </th>
                                <th class="border-0 text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <i class="fas fa-cogs me-2 text-primary"></i>
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="report-row" data-report-name="{{ strtolower($report->name) }}" data-report-type="{{ strtolower($report->frequency) }}" data-deadline="{{ $report->deadline }}">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="report-icon me-3">
                                                @if($report->frequency === 'weekly')
                                                    <i class="fas fa-calendar-week"></i>
                                                @elseif($report->frequency === 'monthly')
                                                    <i class="fas fa-calendar-alt"></i>
                                                @elseif($report->frequency === 'quarterly')
                                                    <i class="fas fa-calendar-check"></i>
                                                @elseif($report->frequency === 'semestral')
                                                    <i class="fas fa-calendar-plus"></i>
                                                @elseif($report->frequency === 'annual')
                                                    <i class="fas fa-calendar-day"></i>
                                                @elseif($report->frequency === 'executive_order')
                                                    <i class="fas fa-gavel"></i>
                                                @else
                                                    <i class="fas fa-file-alt"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-dark">{{ $report->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $report->frequency)) }} Report
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge-modern badge-{{ $report->frequency }}">
                                            @if($report->frequency === 'weekly')
                                                <i class="fas fa-calendar-week me-1"></i>Weekly
                                            @elseif($report->frequency === 'monthly')
                                                <i class="fas fa-calendar-alt me-1"></i>Monthly
                                            @elseif($report->frequency === 'quarterly')
                                                <i class="fas fa-calendar-check me-1"></i>Quarterly
                                            @elseif($report->frequency === 'semestral')
                                                <i class="fas fa-calendar-plus me-1"></i>Semestral
                                            @elseif($report->frequency === 'annual')
                                                <i class="fas fa-calendar-day me-1"></i>Annual
                                            @elseif($report->frequency === 'executive_order')
                                                <i class="fas fa-gavel me-1"></i>Executive Order
                                            @else
                                                <i class="fas fa-file-alt me-1"></i>{{ ucfirst($report->frequency) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="deadline-info">
                                            <div class="fw-medium text-dark">
                                                {{ \Carbon\Carbon::parse($report->deadline)->format('M d, Y') }}
                                            </div>
                                            <small class="text-danger fw-medium">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ \Carbon\Carbon::parse($report->deadline)->diffForHumans() }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge-modern badge-overdue">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            Overdue
                                        </span>
                                    </td>
                                    <td class="py-3 text-end">
                                        <button type="button"
                                                class="btn btn-warning btn-sm action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#submitModal{{ $report->id }}"
                                                title="Submit this overdue report">
                                            <i class="fas fa-upload me-1"></i>
                                            Submit Now
                                        </button>
                                    </td>
                                </tr>

                                        <!-- Submit Modal -->
                                        <div class="modal fade" id="submitModal{{ $report->id }}" tabindex="-1" aria-labelledby="submitModalLabel{{ $report->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="submitModalLabel{{ $report->id }}">
                                                            Submit {{ $report->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('barangay.submissions.store') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="report_type_id" value="{{ $report->id }}">
                                                        <input type="hidden" name="report_type" value="{{ $report->frequency }}">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="file{{ $report->id }}" class="form-label">Upload Report File</label>
                                                                <input type="file"
                                                                       class="form-control"
                                                                       id="file{{ $report->id }}"
                                                                       name="file"
                                                                       accept=".pdf,.docx,.xlsx"
                                                                       required>
                                                                <div class="form-text">Accepted formats: PDF, DOCX, XLSX (Max: 2MB)</div>
                                                            </div>

                                                            @if($report->frequency === 'weekly')
                                                                <div class="mb-3">
                                                                    <label class="form-label">Number of Clean-up Sites</label>
                                                                    <input type="number" class="form-control" name="num_of_clean_up_sites" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Number of Participants</label>
                                                                    <input type="number" class="form-control" name="num_of_participants" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Number of Barangays</label>
                                                                    <input type="number" class="form-control" name="num_of_barangays" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Total Volume</label>
                                                                    <input type="number" class="form-control" name="total_volume" min="0" step="0.01" required>
                                                                </div>
                                                            @elseif($report->frequency === 'monthly')
                                                                <div class="mb-3">
                                                                    <label class="form-label">Month</label>
                                                                    <select class="form-select" name="month" required>
                                                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                            <option value="{{ $month }}">{{ $month }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @elseif($report->frequency === 'quarterly')
                                                                <div class="mb-3">
                                                                    <label class="form-label">Quarter</label>
                                                                    <select class="form-select" name="quarter_number" required>
                                                                        <option value="1">First Quarter</option>
                                                                        <option value="2">Second Quarter</option>
                                                                        <option value="3">Third Quarter</option>
                                                                        <option value="4">Fourth Quarter</option>
                                                                    </select>
                                                                </div>
                                                            @elseif($report->frequency === 'semestral')
                                                                <div class="mb-3">
                                                                    <label class="form-label">Semester</label>
                                                                    <select class="form-select" name="sem_number" required>
                                                                        <option value="1">First Semester</option>
                                                                        <option value="2">Second Semester</option>
                                                                    </select>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fas fa-upload me-1"></i>
                                                                Submit Report
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

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-muted">
                                    Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $reports->perPage() }} per page
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="perPageDropdown">
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25 per page</a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 per page</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($reports->hasPages())
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if($reports->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reports->previousPageUrl() }}" rel="prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                                                @if($page == $reports->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if($reports->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $reports->nextPageUrl() }}" rel="next">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Modern Filter Section */
        .filter-section {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(220, 53, 69, 0.2);
            position: relative;
            overflow: hidden;
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="20" cy="80" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .modern-input, .modern-select {
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .modern-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .modern-input:focus, .modern-select:focus {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
            color: white;
        }

        .modern-select option {
            background: #dc3545;
            color: white;
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 8px 32px rgba(40, 167, 69, 0.3);
        }

        .empty-icon i {
            font-size: 2rem;
            color: white;
        }

        .empty-title {
            color: #28a745;
            font-weight: 700;
        }

        .empty-description {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* Modern Table */
        .table-modern {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: none;
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .table-header th {
            font-weight: 600;
            color: #495057;
            border: none;
            padding: 1.25rem 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-row {
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.3s ease;
        }

        .report-row:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .report-row:last-child {
            border-bottom: none;
        }

        /* Report Icons */
        .report-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        /* Modern Badges */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .badge-weekly {
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: white;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
        }

        .badge-monthly {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        .badge-quarterly {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .badge-semestral {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }

        .badge-annual {
            background: linear-gradient(135deg, #6f42c1, #e83e8c);
            color: white;
            box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
        }

        .badge-executive_order {
            background: linear-gradient(135deg, #343a40, #6c757d);
            color: white;
            box-shadow: 0 2px 8px rgba(52, 58, 64, 0.3);
        }

        .badge-overdue {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3); }
            50% { box-shadow: 0 4px 16px rgba(220, 53, 69, 0.5); }
            100% { box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3); }
        }

        /* Action Button */
        .action-btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            border: none;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(255, 193, 7, 0.4);
            background: linear-gradient(135deg, #fd7e14, #dc3545);
            color: white;
        }

        /* Pagination styles */
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            padding: 0.375rem 0.75rem;
            color: #6c757d;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* Form and dropdown styles */
        .form-select {
            border-radius: 0.375rem;
        }
        .dropdown-menu {
            min-width: 8rem;
        }
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 0.5rem;
        }
        .modal-header {
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }
            .card-header .d-flex > * {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            .d-flex.justify-content-between > * {
                width: 100%;
            }
            .pagination {
                justify-content: center;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Modern Filter System
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const overdueFilter = document.getElementById('overdueFilter');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const reportCountSpan = document.getElementById('reportCount');
            const reportRows = document.querySelectorAll('.report-row');

            // Debounced search function
            let searchTimeout;
            function debounceSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterReports, 300);
            }

            // Main filter function
            function filterReports() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const typeValue = typeFilter.value.toLowerCase();
                const overdueValue = overdueFilter.value.toLowerCase();

                let visibleCount = 0;
                const now = new Date();

                reportRows.forEach(row => {
                    const reportName = row.dataset.reportName || '';
                    const reportType = row.dataset.reportType || '';
                    const deadline = new Date(row.dataset.deadline);

                    // Search filter
                    const matchesSearch = !searchTerm || reportName.includes(searchTerm);

                    // Type filter
                    const matchesType = !typeValue || reportType === typeValue;

                    // Overdue period filter
                    let matchesOverdue = true;
                    if (overdueValue) {
                        const daysDiff = Math.floor((now - deadline) / (1000 * 60 * 60 * 24));
                        switch(overdueValue) {
                            case 'week':
                                matchesOverdue = daysDiff <= 7;
                                break;
                            case 'month':
                                matchesOverdue = daysDiff <= 30;
                                break;
                            case 'quarter':
                                matchesOverdue = daysDiff <= 90;
                                break;
                        }
                    }

                    const shouldShow = matchesSearch && matchesType && matchesOverdue;

                    if (shouldShow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                reportCountSpan.textContent = visibleCount;

                // Show/hide empty state
                const tableContainer = document.querySelector('.table-responsive');
                const emptyState = document.querySelector('.empty-state');

                if (visibleCount === 0 && reportRows.length > 0) {
                    if (tableContainer) tableContainer.style.display = 'none';
                    if (!document.querySelector('.filter-empty-state')) {
                        const filterEmptyState = document.createElement('div');
                        filterEmptyState.className = 'filter-empty-state text-center py-5';
                        filterEmptyState.innerHTML = `
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-muted"></i>
                            </div>
                            <h5 class="mb-2">No reports match your filters</h5>
                            <p class="text-muted">Try adjusting your search criteria or clear all filters.</p>
                            <button type="button" class="btn btn-outline-primary" onclick="clearAllFilters()">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </button>
                        `;
                        document.querySelector('.card-body').appendChild(filterEmptyState);
                    }
                } else {
                    if (tableContainer) tableContainer.style.display = '';
                    const filterEmptyState = document.querySelector('.filter-empty-state');
                    if (filterEmptyState) filterEmptyState.remove();
                }
            }

            // Clear all filters
            function clearAllFilters() {
                searchInput.value = '';
                typeFilter.value = '';
                overdueFilter.value = '';
                filterReports();
            }

            // Global function for empty state button
            window.clearAllFilters = clearAllFilters;

            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', debounceSearch);
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        filterReports();
                    }
                });
            }

            if (typeFilter) {
                typeFilter.addEventListener('change', filterReports);
            }

            if (overdueFilter) {
                overdueFilter.addEventListener('change', filterReports);
            }

            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', clearAllFilters);
            }

            // File input validation
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    const allowedTypes = ['.pdf', '.docx', '.xlsx'];

                    if (file) {
                        // Check file size
                        if (file.size > maxSize) {
                            alert('File size must be less than 2MB');
                            this.value = '';
                            return;
                        }

                        // Check file type
                        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                        if (!allowedTypes.includes(fileExtension)) {
                            alert('Only PDF, DOCX, and XLSX files are allowed');
                            this.value = '';
                            return;
                        }
                    }
                });
            });

            // Handle form submission with loading state
            const submitForms = document.querySelectorAll('form[action*="submissions.store"]');
            submitForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        // Disable button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
