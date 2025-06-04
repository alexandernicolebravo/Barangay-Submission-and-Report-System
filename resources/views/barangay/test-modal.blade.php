<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal Functionality</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <h1>Test Modal Functionality</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Resubmission Modal</h5>
                    </div>
                    <div class="card-body">
                        <p>This is a test to check if the modal functionality works correctly.</p>
                        
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testResubmitModal">
                            <i class="fas fa-edit"></i> Test Resubmit Modal
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Console Logs</h5>
                    </div>
                    <div class="card-body">
                        <p>Open browser developer tools (F12) to see console logs.</p>
                        <button type="button" class="btn btn-info" onclick="testConsole()">
                            <i class="fas fa-bug"></i> Test Console
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Resubmit Modal -->
    <div class="modal fade" id="testResubmitModal" tabindex="-1" aria-labelledby="testResubmitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testResubmitModalLabel">
                        <i class="fas fa-edit text-primary"></i> Test Resubmit Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="testResubmitForm" action="/test-resubmit-action" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This is a test modal to check functionality. No actual submission will occur.
                        </div>
                        
                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="testFile" class="form-label">
                                <i class="fas fa-file-upload"></i> Upload New File
                            </label>
                            <input type="file" class="form-control" id="testFile" name="file" accept=".pdf,.docx,.xlsx,.jpg,.png">
                            <div class="form-text">Allowed formats: PDF, DOCX, XLSX, JPG, PNG</div>
                        </div>
                        
                        <!-- Report Type -->
                        <div class="mb-3">
                            <label for="testReportType" class="form-label">
                                <i class="fas fa-list"></i> Report Type
                            </label>
                            <select class="form-select" id="testReportType" name="report_type" required>
                                <option value="">Select Report Type</option>
                                <option value="weekly">Weekly Report</option>
                                <option value="monthly">Monthly Report</option>
                                <option value="quarterly">Quarterly Report</option>
                                <option value="semestral">Semestral Report</option>
                                <option value="annual">Annual Report</option>
                            </select>
                        </div>
                        
                        <!-- Dynamic Fields Container -->
                        <div id="dynamicFields"></div>
                        
                        <!-- Test Data -->
                        <div class="mb-3">
                            <label for="testNotes" class="form-label">
                                <i class="fas fa-sticky-note"></i> Test Notes
                            </label>
                            <textarea class="form-control" id="testNotes" name="notes" rows="3" placeholder="Enter any test notes here..."></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Test Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Test console function
        function testConsole() {
            console.log('✅ Console test successful!');
            console.log('Current time:', new Date().toLocaleString());
            console.log('Modal element:', document.getElementById('testResubmitModal'));
            alert('Console test completed! Check developer tools for logs.');
        }
        
        // Test form submission
        document.getElementById('testResubmitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('🚀 Form submission intercepted');
            console.log('Form data:', new FormData(this));
            
            // Get form data
            const formData = new FormData(this);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            console.log('Form data object:', data);
            
            // Show success message
            alert('✅ Test form submission successful!\n\nForm data logged to console.\n\nIn real scenario, this would submit to the server.');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('testResubmitModal'));
            modal.hide();
        });
        
        // Test report type change
        document.getElementById('testReportType').addEventListener('change', function() {
            const reportType = this.value;
            const dynamicFields = document.getElementById('dynamicFields');
            
            console.log('📝 Report type changed to:', reportType);
            
            // Clear previous fields
            dynamicFields.innerHTML = '';
            
            // Add fields based on report type
            if (reportType === 'weekly') {
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar"></i> Month</label>
                        <input type="text" class="form-control" name="month" placeholder="e.g., January 2024">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-hashtag"></i> Week Number</label>
                        <input type="number" class="form-control" name="week_number" min="1" max="52" placeholder="1-52">
                    </div>
                `;
            } else if (reportType === 'monthly') {
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar"></i> Month</label>
                        <input type="text" class="form-control" name="month" placeholder="e.g., January 2024">
                    </div>
                `;
            } else if (reportType === 'quarterly') {
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-quarter"></i> Quarter</label>
                        <select class="form-select" name="quarter_number">
                            <option value="">Select Quarter</option>
                            <option value="1">Q1 (Jan-Mar)</option>
                            <option value="2">Q2 (Apr-Jun)</option>
                            <option value="3">Q3 (Jul-Sep)</option>
                            <option value="4">Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                `;
            } else if (reportType === 'semestral') {
                dynamicFields.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-alt"></i> Semester</label>
                        <select class="form-select" name="sem_number">
                            <option value="">Select Semester</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                `;
            }
            
            console.log('✅ Dynamic fields updated for:', reportType);
        });
        
        // Test modal events
        document.getElementById('testResubmitModal').addEventListener('show.bs.modal', function() {
            console.log('🔓 Modal is opening...');
        });
        
        document.getElementById('testResubmitModal').addEventListener('shown.bs.modal', function() {
            console.log('✅ Modal opened successfully!');
        });
        
        document.getElementById('testResubmitModal').addEventListener('hide.bs.modal', function() {
            console.log('🔒 Modal is closing...');
        });
        
        document.getElementById('testResubmitModal').addEventListener('hidden.bs.modal', function() {
            console.log('✅ Modal closed successfully!');
        });
        
        // Log when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎉 Test modal page loaded successfully!');
            console.log('Bootstrap version:', bootstrap.Tooltip.VERSION);
        });
    </script>
</body>
</html>
