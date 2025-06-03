@extends('layouts.barangay')

@section('title', 'View Issuance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0 text-gray-800">
                    <i class="fas fa-eye me-2"></i>
                    View Issuance
                </h1>
                <a href="{{ route('barangay.issuances.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Issuances
                </a>
            </div>

            <div class="card shadow-sm border-0" style="border-radius: 8px;">
                <div class="card-header border-0 bg-white py-3" style="border-radius: 8px 8px 0 0;">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>
                        {{ $issuance->title }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- File Preview -->
                            <div class="file-preview mb-4">
                                <div id="fileViewer" style="min-height: 500px; border: 1px solid #e3e6f0; border-radius: 8px; background: #f8f9fc;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Loading file preview...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- File Information -->
                            <div class="card border-0 bg-light">
                                <div class="card-header bg-transparent border-0">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-info-circle me-2"></i>
                                        File Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-item mb-3">
                                        <label class="text-muted small">Title:</label>
                                        <p class="mb-0 fw-bold">{{ $issuance->title }}</p>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="text-muted small">File Name:</label>
                                        <p class="mb-0">{{ $issuance->file_name ?: 'Unknown' }}</p>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="text-muted small">Upload Date:</label>
                                        <p class="mb-0">{{ $issuance->created_at->format('F d, Y h:i A') }}</p>
                                    </div>
                                    
                                    @if($issuance->file_size)
                                    <div class="info-item mb-3">
                                        <label class="text-muted small">File Size:</label>
                                        <p class="mb-0">{{ $issuance->file_size_human }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($issuance->file_type)
                                    <div class="info-item mb-4">
                                        <label class="text-muted small">File Type:</label>
                                        <span class="badge bg-secondary">{{ strtoupper($issuance->file_type) }}</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Download Button -->
                                    <div class="d-grid">
                                        <a href="{{ route('barangay.issuances.download', $issuance) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i>
                                            Download File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileViewer = document.getElementById('fileViewer');
    const filePath = '{{ $issuance->file_path }}';
    const fileUrl = `/storage/${filePath}`;
    const fileExtension = filePath.split('.').pop().toLowerCase();
    
    if (['pdf'].includes(fileExtension)) {
        // For PDF files, use iframe
        fileViewer.innerHTML = `<iframe src="${fileUrl}" style="width: 100%; height: 500px; border: none; border-radius: 8px;"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        // For images
        fileViewer.innerHTML = `
            <div class="text-center p-3">
                <img src="${fileUrl}" style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 8px;" class="shadow-sm">
            </div>
        `;
    } else if (['txt'].includes(fileExtension)) {
        // For text files, fetch and display content
        fetch(fileUrl)
            .then(response => response.text())
            .then(text => {
                fileViewer.innerHTML = `
                    <div class="p-3">
                        <pre style="white-space: pre-wrap; max-height: 500px; overflow-y: auto; background: white; padding: 1rem; border-radius: 8px; font-size: 0.9rem;">${text}</pre>
                    </div>
                `;
            })
            .catch(error => {
                fileViewer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h6>Cannot load file content</h6>
                        <p class="text-muted">Please download the file to view its contents.</p>
                    </div>
                `;
            });
    } else {
        // For other file types, show download message
        fileViewer.innerHTML = `
            <div class="text-center py-5">
                <div class="file-icon-large mb-3">
                    <i class="fas fa-file fa-3x text-primary"></i>
                </div>
                <h6>Preview not available</h6>
                <p class="text-muted mb-3">This file type cannot be previewed in the browser.</p>
                <a href="{{ route('barangay.issuances.download', $issuance) }}" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    Download to View
                </a>
            </div>
        `;
    }
});
</script>

<style>
.info-item label {
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.file-preview {
    background: #f8f9fc;
    border-radius: 8px;
    overflow: hidden;
}

.file-icon-large {
    width: 80px;
    height: 80px;
    background: #f8f9fc;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>
@endsection
