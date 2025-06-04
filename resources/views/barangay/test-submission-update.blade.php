@extends('layouts.barangay')

@section('title', 'Test Submission Update')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Submission Update Functionality</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Testing Update Functionality</h5>
                        <p>This page tests the submission update functionality to ensure it works properly without crashing.</p>
                    </div>

                    <!-- Test Form -->
                    <form action="{{ route('barangay.submissions.resubmit', 1) }}" method="POST" enctype="multipart/form-data" class="report-update-form" data-ajax="true" data-ajax-reset="false">
                        @csrf
                        <input type="hidden" name="report_type_id" value="1">
                        <input type="hidden" name="report_id" value="1">
                        <input type="hidden" name="report_type" value="weekly">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">Select File to Upload</label>
                                    <input type="file" class="form-control" name="file" id="file" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="form-text text-muted">Allowed formats: PDF, DOC, DOCX, XLS, XLSX</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <select class="form-control" name="month" id="month">
                                        <option value="January">January</option>
                                        <option value="February">February</option>
                                        <option value="March">March</option>
                                        <option value="April">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="August">August</option>
                                        <option value="September">September</option>
                                        <option value="October">October</option>
                                        <option value="November">November</option>
                                        <option value="December">December</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="week_number">Week Number</label>
                                    <input type="number" class="form-control" name="week_number" id="week_number" min="1" max="52" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="num_of_clean_up_sites">Clean-up Sites</label>
                                    <input type="number" class="form-control" name="num_of_clean_up_sites" id="num_of_clean_up_sites" min="0" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="num_of_participants">Participants</label>
                                    <input type="number" class="form-control" name="num_of_participants" id="num_of_participants" min="0" value="10">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="num_of_barangays">Barangays</label>
                                    <input type="number" class="form-control" name="num_of_barangays" id="num_of_barangays" min="0" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_volume">Total Volume</label>
                                    <input type="number" step="0.01" class="form-control" name="total_volume" id="total_volume" min="0" value="100.50">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Test Update Submission
                            </button>
                            <a href="{{ route('barangay.submissions') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Submissions
                            </a>
                        </div>
                    </form>

                    <hr>

                    <div class="alert alert-warning">
                        <h5>Test Instructions:</h5>
                        <ol>
                            <li>Select a file to upload</li>
                            <li>Fill in the required fields</li>
                            <li>Click "Test Update Submission"</li>
                            <li>Check if the form submits without crashing</li>
                            <li>Verify that success/error messages appear properly</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test page loaded successfully');
    console.log('AJAX forms script loaded:', typeof window.ajaxForms !== 'undefined');
    
    // Test form validation
    const form = document.querySelector('.report-update-form');
    if (form) {
        console.log('Test form found');
        form.addEventListener('submit', function(e) {
            console.log('Form submission triggered');
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput && !fileInput.files.length) {
                e.preventDefault();
                alert('Please select a file to upload');
                return false;
            }
        });
    }
});
</script>
@endsection
