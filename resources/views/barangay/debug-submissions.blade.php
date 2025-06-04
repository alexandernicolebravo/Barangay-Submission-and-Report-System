@extends('layouts.barangay')

@section('title', 'Debug Submissions')
@section('page-title', 'Debug Submissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Debug Submissions Information</h5>
                </div>
                <div class="card-body">
                    @php
                        $userId = Auth::id();
                        
                        // Get all reports for the current user
                        $weeklyReports = \App\Models\WeeklyReport::with('reportType')->where('user_id', $userId)->get();
                        $monthlyReports = \App\Models\MonthlyReport::with('reportType')->where('user_id', $userId)->get();
                        $quarterlyReports = \App\Models\QuarterlyReport::with('reportType')->where('user_id', $userId)->get();
                        $semestralReports = \App\Models\SemestralReport::with('reportType')->where('user_id', $userId)->get();
                        $annualReports = \App\Models\AnnualReport::with('reportType')->where('user_id', $userId)->get();
                        
                        // Combine all reports
                        $allReports = collect()
                            ->merge($weeklyReports->map(function ($report) {
                                $report->model_type = 'WeeklyReport';
                                $report->unique_id = 'weekly_' . $report->id;
                                return $report;
                            }))
                            ->merge($monthlyReports->map(function ($report) {
                                $report->model_type = 'MonthlyReport';
                                $report->unique_id = 'monthly_' . $report->id;
                                return $report;
                            }))
                            ->merge($quarterlyReports->map(function ($report) {
                                $report->model_type = 'QuarterlyReport';
                                $report->unique_id = 'quarterly_' . $report->id;
                                return $report;
                            }))
                            ->merge($semestralReports->map(function ($report) {
                                $report->model_type = 'SemestralReport';
                                $report->unique_id = 'semestral_' . $report->id;
                                return $report;
                            }))
                            ->merge($annualReports->map(function ($report) {
                                $report->model_type = 'AnnualReport';
                                $report->unique_id = 'annual_' . $report->id;
                                return $report;
                            }));
                    @endphp
                    
                    <h6>User Information:</h6>
                    <ul>
                        <li><strong>User ID:</strong> {{ $userId }}</li>
                        <li><strong>User Name:</strong> {{ Auth::user()->name }}</li>
                        <li><strong>User Type:</strong> {{ Auth::user()->user_type }}</li>
                    </ul>
                    
                    <h6>Report Counts:</h6>
                    <ul>
                        <li><strong>Weekly Reports:</strong> {{ $weeklyReports->count() }}</li>
                        <li><strong>Monthly Reports:</strong> {{ $monthlyReports->count() }}</li>
                        <li><strong>Quarterly Reports:</strong> {{ $quarterlyReports->count() }}</li>
                        <li><strong>Semestral Reports:</strong> {{ $semestralReports->count() }}</li>
                        <li><strong>Annual Reports:</strong> {{ $annualReports->count() }}</li>
                        <li><strong>Total Reports:</strong> {{ $allReports->count() }}</li>
                    </ul>
                    
                    @if($allReports->count() > 0)
                    <h6>Sample Report for Testing:</h6>
                    @php $sampleReport = $allReports->first(); @endphp
                    <div class="alert alert-info">
                        <p><strong>Report ID:</strong> {{ $sampleReport->unique_id ?? $sampleReport->id }}</p>
                        <p><strong>Report Type:</strong> {{ $sampleReport->reportType->name ?? 'N/A' }}</p>
                        <p><strong>File Path:</strong> {{ $sampleReport->file_path ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> {{ $sampleReport->status ?? 'N/A' }}</p>
                        <p><strong>Created:</strong> {{ $sampleReport->created_at ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Test View Modal:</h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testViewModal">
                            <i class="fas fa-eye me-1"></i>Test View File
                        </button>
                        
                        <h6 class="mt-3">Test Download Links:</h6>
                        @php $reportId = $sampleReport->unique_id ?? $sampleReport->id; @endphp
                        <a href="{{ route('barangay.direct.files.download', $reportId) }}" class="btn btn-outline-primary me-2" target="_blank">
                            <i class="fas fa-eye me-1"></i>View File (Direct)
                        </a>
                        <a href="{{ route('barangay.direct.files.download', $reportId) }}?download=true" class="btn btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Download File
                        </a>
                    </div>
                    
                    <!-- Test Modal -->
                    <div class="modal fade" id="testViewModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-file-alt me-2"></i>Test File Preview
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div id="testFilePreviewContainer" style="height: 70vh; background: #f8f9fa;">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary mb-3" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="text-muted">Loading file preview...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <h6>No Reports Found</h6>
                        <p>No reports have been submitted yet. Please submit a report first to test the view functionality.</p>
                        <a href="{{ route('barangay.submit-report') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Submit a Report
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Debug submissions page loaded');
        
        // Test modal functionality
        document.getElementById('testViewModal')?.addEventListener('show.bs.modal', function() {
            console.log('Test modal opening');
            loadTestFilePreview();
        });
    });
    
    function loadTestFilePreview() {
        const container = document.getElementById('testFilePreviewContainer');
        @if(isset($sampleReport))
        const reportId = '{{ $reportId }}';
        const fileUrl = '{{ route("barangay.direct.files.download", ":reportId") }}'.replace(':reportId', reportId);
        
        console.log('Loading test preview for:', reportId);
        console.log('File URL:', fileUrl);
        
        // Try to load the file
        fetch(fileUrl)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error('File not found or access denied (Status: ' + response.status + ')');
                }
                
                const contentType = response.headers.get('content-type');
                console.log('Content type:', contentType);
                
                // Check if it's a PDF
                if (contentType && contentType.includes('application/pdf')) {
                    return response.blob().then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        container.innerHTML = `
                            <iframe src="${blobUrl}" 
                                    style="width: 100%; height: 100%; border: none;" 
                                    title="PDF Preview">
                            </iframe>
                        `;
                    });
                }
                
                // Check if it's an image
                if (contentType && contentType.startsWith('image/')) {
                    return response.blob().then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        container.innerHTML = `
                            <div class="d-flex align-items-center justify-content-center h-100 p-3">
                                <img src="${blobUrl}" 
                                     class="img-fluid" 
                                     style="max-height: 100%; max-width: 100%; object-fit: contain;" 
                                     alt="Image Preview">
                            </div>
                        `;
                    });
                }
                
                // For other file types, show download option
                throw new Error('Preview not available for this file type (' + contentType + ')');
            })
            .catch(error => {
                console.error('Preview error:', error);
                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fas fa-file fa-3x text-muted mb-3"></i>
                            <h5>Preview Error</h5>
                            <p class="text-muted mb-3">${error.message}</p>
                            <a href="${fileUrl}?download=true" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i>Download to View
                            </a>
                        </div>
                    </div>
                `;
            });
        @else
        container.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5>No Sample Report</h5>
                    <p class="text-muted">No reports available for testing.</p>
                </div>
            </div>
        `;
        @endif
    }
</script>
@endpush
