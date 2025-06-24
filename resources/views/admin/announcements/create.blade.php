@extends('admin.layouts.app')

@section('title', 'Create Announcement')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    #image-preview-container {
        display: none;
        position: relative;
        margin-top: 1rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        background: #fff;
        overflow: hidden;
        transition: box-shadow 0.2s;
        max-width: 100%;
    }
    #image-preview-container.active {
        display: inline-block;
        box-shadow: 0 4px 24px rgba(0,0,0,0.16);
    }
    #image-preview {
        max-width: 100%;
        max-height: 260px;
        border-radius: 12px 12px 0 0;
        display: block;
        background: #f8fafc;
        object-fit: contain;
        transition: filter 0.2s;
    }
    #remove-image {
        position: absolute;
        top: 10px;
        right: 10px;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        background: #fff;
        color: #e3342f;
        border: none;
        font-size: 18px;
        transition: background 0.2s, color 0.2s;
        cursor: pointer;
    }
    #remove-image:hover, #remove-image:focus {
        background: #e3342f;
        color: #fff;
        outline: none;
    }
    .note-editor {
        border-radius: var(--radius-sm);
    }
    .note-toolbar {
        background-color: var(--gray-100);
    }
    .color-picker {
        width: 40px;
        height: 40px;
        padding: 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="page-title">
            <i class="fas fa-bullhorn"></i>
            Create Announcement
        </h2>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Announcements
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                            <option value="" disabled selected>Select category (optional)</option>
                            <option value="announcement" {{ old('category') == 'announcement' ? 'selected' : '' }}>General Announcement</option>
                            <option value="recognition" {{ old('category') == 'recognition' ? 'selected' : '' }}>Recognition</option>
                            <option value="important_update" {{ old('category') == 'important_update' ? 'selected' : '' }}>Important Update</option>
                            <option value="upcoming_event" {{ old('category') == 'upcoming_event' ? 'selected' : '' }}>Upcoming Event</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror" id="button_text" name="button_text" value="{{ old('button_text') }}" placeholder="e.g. Read More, Learn More">
                                <small class="text-muted">Text to display on the button</small>
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_link" class="form-label">Button Link</label>
                                <input type="text" class="form-control @error('button_link') is-invalid @enderror" id="button_link" name="button_link" value="{{ old('button_link') }}" placeholder="e.g. https://example.com">
                                <small class="text-muted">URL that will open in a new tab when button is clicked</small>
                                @error('button_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        <small class="text-muted">Recommended size: 800x600 pixels. Max size: 25MB.</small>
                        <div class="alert alert-info mt-2" style="font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> If you upload an image, the title, category, and content fields will be disabled since the image contains the content.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="image-preview-container" aria-live="polite">
                            <img id="image-preview" src="#" alt="Image Preview" style="display:none;">
                            <button type="button" id="remove-image" title="Remove image preview" aria-label="Remove image preview" tabindex="0" style="display:none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="background_color" class="form-label">Background Color</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="color-picker me-2" id="color_picker" value="#f8fafc">
                            <input type="text" class="form-control @error('background_color') is-invalid @enderror" id="background_color" name="background_color" value="{{ old('background_color', '#f8fafc') }}">
                        </div>
                        @error('background_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', 0) }}" min="0" max="10">
                        <small class="text-muted">Priority: 0 (lowest) to 10 (highest). Higher numbers appear first.</small>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="starts_at" class="form-label">Start Date</label>
                        <input type="text" class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" placeholder="Select start date...">
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="ends_at" class="form-label">End Date</label>
                        <input type="text" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" placeholder="Select end date...">
                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('#content').summernote({
            placeholder: 'Enter announcement content here...',
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        
        // Initialize Flatpickr for date pickers
        flatpickr("#starts_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            allowInput: true
        });
        
        flatpickr("#ends_at", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            allowInput: true
        });
        
        // Modern image preview and field management
        function handleImageChange() {
            const file = $('#image')[0].files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result).show();
                    $('#image-preview-container').addClass('active');
                    $('#remove-image').show();
                }
                reader.readAsDataURL(file);

                // Disable text overlay fields when image is uploaded
                $('#title').prop('disabled', true).removeClass('is-invalid').val('');
                $('#category').prop('disabled', true).removeClass('is-invalid').val('');
                $('#content').summernote('code', '').summernote('disable');
                $('#content').next('.note-editor').find('.note-editable').attr('contenteditable', 'false');
                // Also disable the Summernote toolbar for clarity
                $('.note-toolbar').css('pointer-events', 'none').css('opacity', '0.5');
                // Add a visual overlay to the content field
                if ($('#content-disabled-overlay').length === 0) {
                    $('#content').next('.note-editor').append('<div id="content-disabled-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.6);z-index:20;cursor:not-allowed;"></div>');
                }

                // Update labels to show they're disabled
                $('label[for="title"]').html('Title <small class="text-muted">(disabled - image contains content)</small>');
                $('label[for="category"]').html('Category <small class="text-muted">(disabled - image contains content)</small>');
                $('label[for="content"]').html('Content <small class="text-muted">(disabled - image contains content)</small>');

                // Clear any validation errors for these fields
                $('#title, #category').next('.invalid-feedback').hide();
                $('#content').next('.invalid-feedback').hide();
            } else {
                $('#image-preview').hide();
                $('#image-preview-container').removeClass('active');
                $('#remove-image').hide();

                // Enable text overlay fields when no image
                $('#title').prop('disabled', false);
                $('#category').prop('disabled', false);
                $('#content').summernote('enable');
                $('#content').next('.note-editor').find('.note-editable').attr('contenteditable', 'true');
                // Restore Summernote toolbar
                $('.note-toolbar').css('pointer-events', '').css('opacity', '');
                // Remove the overlay if present
                $('#content-disabled-overlay').remove();

                // Restore original labels
                $('label[for="title"]').html('Title');
                $('label[for="category"]').html('Category');
                $('label[for="content"]').html('Content');
            }
        }

        $('#image').change(handleImageChange);

        // Remove image functionality
        $('#remove-image').click(function() {
            // Clear the file input
            $('#image').val('');
            // Hide preview
            $('#image-preview').hide();
            $('#image-preview-container').removeClass('active');
            $('#remove-image').hide();
            // Enable text overlay fields
            $('#title').prop('disabled', false);
            $('#category').prop('disabled', false);
            $('#content').summernote('enable');
            $('#content').next('.note-editor').find('.note-editable').attr('contenteditable', 'true');
            // Restore Summernote toolbar
            $('.note-toolbar').css('pointer-events', '').css('opacity', '');
            // Remove the overlay if present
            $('#content-disabled-overlay').remove();
            // Restore original labels
            $('label[for="title"]').html('Title');
            $('label[for="category"]').html('Category');
            $('label[for="content"]').html('Content');
        });
        
        // Color picker
        $('#color_picker').change(function() {
            $('#background_color').val($(this).val());
        });
        
        $('#background_color').change(function() {
            $('#color_picker').val($(this).val());
        });
    });
</script>
@endpush 