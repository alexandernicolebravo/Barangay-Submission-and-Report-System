@extends('layouts.barangay')

@section('title', 'Debug Modals Test')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bug me-2"></i>Modal Debug Test Page</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This page tests the same modal structure as submissions page to identify issues.
                    </div>

                    <!-- Test Buttons -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#testUpdateModal">
                                <i class="fas fa-edit me-2"></i>Test Update Modal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#testViewFileModal">
                                <i class="fas fa-eye me-2"></i>Test View File Modal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#testRemarksModal">
                                <i class="fas fa-comment me-2"></i>Test Remarks Modal
                            </button>
                        </div>
                    </div>

                    <!-- Console Test Button -->
                    <button type="button" class="btn btn-success" onclick="testConsole()">
                        <i class="fas fa-terminal me-2"></i>Test Console
                    </button>

                    <!-- Debug Info -->
                    <div class="mt-4">
                        <h6>Debug Information:</h6>
                        <ul id="debugInfo" class="list-unstyled">
                            <li>✅ Page loaded successfully</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Update Modal (Same structure as submissions) -->
<div class="modal fade" id="testUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Test Update Modal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Update modal is working! This uses the same structure as submissions page.
                </div>
                
                <form>
                    <div class="mb-3">
                        <label class="form-label">Test File Upload</label>
                        <input type="file" class="form-control" accept=".pdf,.docx,.xlsx">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Test Select</label>
                        <select class="form-select">
                            <option>Option 1</option>
                            <option>Option 2</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Test Input</label>
                        <input type="text" class="form-control" placeholder="Test input">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Test Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Test View File Modal (Same structure as submissions) -->
<div class="modal fade" id="testViewFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file me-2"></i>Test View File Modal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    View file modal is working! This uses the same structure as submissions page.
                </div>
                
                <div class="text-center p-4" style="background: #f8f9fa; border-radius: 8px;">
                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                    <h6>Test Document Preview</h6>
                    <p class="text-muted">This would show the actual file preview</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Remarks Modal (Same structure as submissions) -->
<div class="modal fade" id="testRemarksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment me-2"></i>Test Remarks Modal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Test Remarks:</strong> This is a test remark to check if the modal displays properly.
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Remarks modal is working correctly!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Same modal styles as submissions page */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Debug console function
    function testConsole() {
        console.log('🔧 Debug Test Started');
        console.log('Current time:', new Date().toLocaleString());
        console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Loaded' : 'Not loaded');
        console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'Not loaded');
        
        // Test modal elements
        const updateModal = document.getElementById('testUpdateModal');
        const viewFileModal = document.getElementById('testViewFileModal');
        const remarksModal = document.getElementById('testRemarksModal');
        
        console.log('Update modal element:', updateModal);
        console.log('View file modal element:', viewFileModal);
        console.log('Remarks modal element:', remarksModal);
        
        // Add to debug info
        const debugInfo = document.getElementById('debugInfo');
        debugInfo.innerHTML += '<li>✅ Console test completed - check browser console</li>';
        
        alert('Debug test completed! Check the browser console for detailed information.');
    }
    
    // Modal event listeners for debugging
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Debug page loaded');
        
        // Add debug info
        const debugInfo = document.getElementById('debugInfo');
        debugInfo.innerHTML += '<li>✅ DOM content loaded</li>';
        debugInfo.innerHTML += '<li>✅ Bootstrap: ' + (typeof bootstrap !== 'undefined' ? 'Loaded' : 'Not loaded') + '</li>';
        debugInfo.innerHTML += '<li>✅ jQuery: ' + (typeof $ !== 'undefined' ? 'Loaded' : 'Not loaded') + '</li>';
        
        // Test modal events
        ['testUpdateModal', 'testViewFileModal', 'testRemarksModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('show.bs.modal', function() {
                    console.log(`🔓 ${modalId} is opening...`);
                    debugInfo.innerHTML += `<li>✅ ${modalId} opened successfully</li>`;
                });
                
                modal.addEventListener('shown.bs.modal', function() {
                    console.log(`✅ ${modalId} opened successfully!`);
                });
                
                modal.addEventListener('hide.bs.modal', function() {
                    console.log(`🔒 ${modalId} is closing...`);
                });
                
                modal.addEventListener('hidden.bs.modal', function() {
                    console.log(`✅ ${modalId} closed successfully!`);
                });
            }
        });
    });
</script>
@endpush
