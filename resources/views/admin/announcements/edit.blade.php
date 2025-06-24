@extends('admin.layouts.app')

@section('title', 'Edit Announcement')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    #image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        margin-top: 1rem;
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
            Edit Announcement
        </h2>
        <div>
            <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="announcement" {{ old('category', $announcement->category) == 'announcement' ? 'selected' : '' }}>General Announcement</option>
                            <option value="recognition" {{ old('category', $announcement->category) == 'recognition' ? 'selected' : '' }}>Recognition</option>
                            <option value="important_update" {{ old('category', $announcement->category) == 'important_update' ? 'selected' : '' }}>Important Update</option>
                            <option value="upcoming_event" {{ old('category', $announcement->category) == 'upcoming_event' ? 'selected' : '' }}>Upcoming Event</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">{{ old('content', $announcement->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror" id="button_text" name="button_text" value="{{ old('button_text', $announcement->button_text) }}" placeholder="e.g. Read More, Learn More">
                                <small class="text-muted">Text to display on the button</small>
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_link" class="form-label">Button Link</label>
                                <input type="text" class="form-control @error('button_link') is-invalid @enderror" id="button_link" name="button_link" value="{{ old('button_link', $announcement->button_link) }}" placeholder="e.g. https://example.com">
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
                        <small class="text-muted">Leave empty to keep current image. Recommended size: 800x600 pixels. Max size: 25MB.</small>
                        <div class="alert alert-info mt-2" style="font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> If you upload an image, the title, category, and content fields will be disabled since the image contains the content.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div id="image-preview-container" style="{{ $announcement->image_path ? 'display: inline-block; position: relative; margin-top: 1rem;' : 'display: none; position: relative; margin-top: 1rem; display: inline-block;' }}">
                            <img id="image-preview" src="{{ $announcement->image_path ? asset('storage/' . $announcement->image_path) : '#' }}" alt="Image Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: block;">
                            <button type="button" id="remove-image" class="btn btn-danger btn-sm" style="position: absolute; top: 8px; right: 8px; border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                <i class="fas fa-times" style="font-size: 12px;"></i>
                            </button>
                            @if($announcement->image_path)
                                <div class="mt-1">
                                    <small class="text-muted">Current image</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="background_color" class="form-label">Background Color</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="color-picker me-2" id="color_picker" value="{{ old('background_color', $announcement->background_color) }}">
                            <input type="text" class="form-control @error('background_color') is-invalid @enderror" id="background_color" name="background_color" value="{{ old('background_color', $announcement->background_color) }}">
                        </div>
                        @error('background_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', $announcement->priority) }}" min="0" max="10">
                        <small class="text-muted">Priority: 0 (lowest) to 10 (highest). Higher numbers appear first.</small>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="starts_at" class="form-label">Start Date</label>
                        <input type="text" class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" value="{{ old('starts_at', $announcement->starts_at ? $announcement->starts_at->format('Y-m-d H:i') : '') }}" placeholder="Select start date...">
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="ends_at" class="form-label">End Date</label>
                        <input type="text" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at', $announcement->ends_at ? $announcement->ends_at->format('Y-m-d H:i') : '') }}" placeholder="Select end date...">
                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Announcement
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
        
        // Check if announcement already has an image and disable fields accordingly
        const hasExistingImage = {{ $announcement->image_path ? 'true' : 'false' }};
        if (hasExistingImage) {
            // Disable text overlay fields if image exists
            $('#title').prop('disabled', true);
            $('#category').prop('disabled', true);
            $('#content').summernote('disable');

            // Update labels to show they're disabled
            $('label[for="title"]').html('Title <small class="text-muted">(disabled - image contains content)</small>');
            $('label[for="category"]').html('Category <small class="text-muted">(disabled - image contains content)</small>');
            $('label[for="content"]').html('Content <small class="text-muted">(disabled - image contains content)</small>');
        }

        // Image preview and field management
        function handleImageChange() {
            const file = $('#image')[0].files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result);
                    $('#image-preview-container').show();
                }
                reader.readAsDataURL(file);

                // Disable text overlay fields when image is uploaded
                $('#title').prop('disabled', true).removeClass('is-invalid');
                $('#category').prop('disabled', true).removeClass('is-invalid');
                $('#content').summernote('disable');

                // Update labels to show they're disabled
                $('label[for="title"]').html('Title <small class="text-muted">(disabled - image contains content)</small>');
                $('label[for="category"]').html('Category <small class="text-muted">(disabled - image contains content)</small>');
                $('label[for="content"]').html('Content <small class="text-muted">(disabled - image contains content)</small>');

                // Clear any validation errors for these fields
                $('#title, #category').next('.invalid-feedback').hide();
                $('#content').next('.invalid-feedback').hide();
            } else if (!hasExistingImage) {
                $('#image-preview-container').hide();

                // Only enable fields if there's no existing image
                $('#title').prop('disabled', false);
                $('#category').prop('disabled', false);
                $('#content').summernote('enable');

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
            $('#image-preview-container').hide();

            // Enable text overlay fields
            $('#title').prop('disabled', false);
            $('#category').prop('disabled', false);
            $('#content').summernote('enable');

            // Restore original labels
            $('label[for="title"]').html('Title');
            $('label[for="category"]').html('Category');
            $('label[for="content"]').html('Content');

            // Mark that we no longer have an existing image
            hasExistingImage = false;
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