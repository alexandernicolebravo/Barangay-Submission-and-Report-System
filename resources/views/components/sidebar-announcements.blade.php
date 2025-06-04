@php
    $announcements = [];
    try {
        $announcements = \App\Models\Announcement::active()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    } catch (\Exception $e) {
        // Silently handle any errors
    }
@endphp

<style>
    .sidebar-announcements {
        padding: 0.5rem 1rem;
        border-top: 1px solid var(--gray-200);
        margin: 0;
    }
    
    .announcements-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding: 0 0.5rem;
    }
    
    .announcements-slider {
        position: relative;
        border-radius: 8px;
        background-color: var(--gray-100);
        margin: 0;
        padding: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .slider-wrapper {
        position: relative;
        min-height: 120px;
    }
    
    .sidebar-announcement {
        display: flex;
        flex-direction: column;
    }
    
    .announcement-content-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .announcement-title {
        font-weight: 600;
        font-size: 14px;
        color: var(--gray-800);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    
    .announcement-image {
        width: 100%;
        height: 100px;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 8px;
        object-fit: cover;
    }
    
    .announcement-date {
        font-size: 11px;
        color: var(--gray-600);
        margin-top: auto;
    }
    
    .announcement-category {
        text-align: right;
        margin-bottom: 6px;
    }
    
    .announcement-category .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        padding: 0;
        font-size: 0.7rem;
        line-height: 1;
        border-radius: 50%;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Navigation arrows */
    .slider-nav {
        position: absolute;
        width: 24px;
        height: 24px;
        background-color: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 5;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .slider-nav.prev {
        left: -12px;
    }
    
    .slider-nav.next {
        right: -12px;
    }
    
    /* Dots navigation */
    .slider-dots {
        display: flex;
        justify-content: center;
        margin-top: 10px;
        gap: 5px;
    }
    
    .slider-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--gray-300);
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .slider-dot.active {
        background-color: var(--primary);
    }
    
    /* Fix for the modals */
    .modal-announcement {
        z-index: 1050 !important;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    /* Fix for the tooltip */
    .announcement-link {
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }
    
    .announcement-link:hover {
        transform: translateY(-2px);
    }
</style>

@if(count($announcements) > 0)
    <div class="sidebar-announcements">
        <div class="announcements-header">
            <h6 class="text-muted fw-semibold fs-7 text-uppercase m-0">
                <i class="fas fa-bullhorn me-2"></i> Announcements
            </h6>
            <span class="badge bg-primary">{{ count($announcements) }}</span>
        </div>
        
        <div class="announcements-slider" id="announcements-slider">
            @if(count($announcements) > 1)
                <div class="slider-nav prev" onclick="prevAnnouncement()">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="slider-nav next" onclick="nextAnnouncement()">
                    <i class="fas fa-chevron-right"></i>
                </div>
            @endif
            
            <div class="slider-wrapper" id="slider-wrapper">
                @foreach($announcements as $index => $announcement)
                    <div class="sidebar-announcement" data-index="{{ $index }}" style="display: {{ $index == 0 ? 'block' : 'none' }};" onclick="openAnnouncementModal({{ $announcement->id }})">
                        <div class="announcement-content-wrapper">
                            <div class="announcement-category">
                                @if($announcement->category == 'recognition')
                                    <span class="badge bg-success">
                                        <i class="fas fa-award"></i>
                                    </span>
                                @elseif($announcement->category == 'important_update')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-bell"></i>
                                    </span>
                                @elseif($announcement->category == 'upcoming_event')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="announcement-title">{{ $announcement->title }}</div>
                            
                            @if($announcement->image_path)
                                <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                     alt="{{ $announcement->title }}"
                                     class="announcement-image">
                            @endif
                            
                            <div class="announcement-date">{{ $announcement->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($announcements) > 1)
                <div class="slider-dots" id="slider-dots">
                    @foreach($announcements as $index => $announcement)
                        <div class="slider-dot {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}" onclick="goToAnnouncement({{ $index }})"></div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
    <div class="sidebar-announcements">
        <div class="announcements-header">
            <h6 class="text-muted fw-semibold fs-7 text-uppercase m-0">
                <i class="fas fa-bullhorn me-2"></i> Announcements
            </h6>
        </div>
        <div class="announcements-slider">
            <div class="p-2 text-center text-muted fs-7">
                No announcements available
            </div>
        </div>
    </div>
@endif

<!-- Modals container (outside the sidebar to prevent nesting issues) -->
<div class="announcement-modals">
    @foreach($announcements as $announcement)
        @php
            // Determine if the modal should be in "image-only" mode
            $isModalImageOnly = $announcement->image_path && 
                                empty(trim(strip_tags((string) $announcement->title))) && 
                                empty(trim(strip_tags((string) $announcement->content))) && 
                                empty(trim(strip_tags((string) $announcement->button_text)));
        @endphp
        <div class="modal fade modal-announcement" id="modal-announcement-{{ $announcement->id }}" tabindex="-1" aria-labelledby="announcementModalLabel-{{ $announcement->id }}" aria-hidden="true">
            <div class="modal-dialog {{ $isModalImageOnly ? 'modal-xl' : 'modal-lg' }}">
                <div class="modal-content">
                    @if($isModalImageOnly)
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close" onclick="closeAnnouncementModal({{ $announcement->id }})"></button>
                        </div>
                        <div class="modal-body p-2 text-center">
                            <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                 alt="Announcement Image"
                                 class="img-fluid rounded mx-auto d-block"
                                 style="max-height: 80vh;">
                        </div>
                    @else
                        <div class="modal-header" style="{{ $announcement->background_color ? 'background-color: ' . $announcement->background_color : '' }}">
                            <h5 class="modal-title" id="announcementModalLabel-{{ $announcement->id }}">{{ $announcement->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeAnnouncementModal({{ $announcement->id }})"></button>
                        </div>
                        <div class="modal-body">
                            @if($announcement->image_path)
                                <div class="text-center mb-3">
                                    <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                         alt="{{ $announcement->title }}"
                                         class="img-fluid rounded mx-auto d-block"
                                         style="max-height: 300px;">
                                </div>
                            @endif
                            
                            <div class="announcement-content">
                                {!! $announcement->content !!}
                            </div>
                            
                            @if($announcement->button_text && $announcement->button_link)
                                <div class="text-center mt-4">
                                    <a href="{{ $announcement->button_link }}" class="btn btn-primary" target="_blank">
                                        {{ $announcement->button_text }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <small class="text-muted me-auto">Posted {{ $announcement->created_at->diffForHumans() }}</small>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeAnnouncementModal({{ $announcement->id }})">Close</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    // Variables to track the current announcement
    let currentAnnouncementIndex = 0;
    const announcementCount = {{ count($announcements) }};
    
    // Functions to navigate between announcements
    function showAnnouncement(index) {
        // Hide all announcements
        document.querySelectorAll('.sidebar-announcement').forEach(announcement => {
            announcement.style.display = 'none';
        });
        
        // Show the selected announcement
        const announcement = document.querySelector(`.sidebar-announcement[data-index="${index}"]`);
        if (announcement) {
            announcement.style.display = 'block';
        }
        
        // Update dots
        document.querySelectorAll('.slider-dot').forEach(dot => {
            dot.classList.remove('active');
        });
        
        const activeDot = document.querySelector(`.slider-dot[data-index="${index}"]`);
        if (activeDot) {
            activeDot.classList.add('active');
        }
        
        // Update current index
        currentAnnouncementIndex = index;
    }
    
    function nextAnnouncement() {
        let nextIndex = (currentAnnouncementIndex + 1) % announcementCount;
        showAnnouncement(nextIndex);
    }
    
    function prevAnnouncement() {
        let prevIndex = (currentAnnouncementIndex - 1 + announcementCount) % announcementCount;
        showAnnouncement(prevIndex);
    }
    
    function goToAnnouncement(index) {
        showAnnouncement(index);
    }
    
    function openAnnouncementModal(id) {
        const modalId = `modal-announcement-${id}`;
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,  // Allow clicking outside to close
            keyboard: true   // Allow ESC key to close
        });
        modal.show();

        // Store the current modal ID for ESC key handling
        window.currentAnnouncementModalId = id;
    }

    function closeAnnouncementModal(id) {
        const modalId = `modal-announcement-${id}`;
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        // Clear the current modal ID
        window.currentAnnouncementModalId = null;
    }

    // Add ESC key event listener for closing modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && window.currentAnnouncementModalId) {
            closeAnnouncementModal(window.currentAnnouncementModalId);
        }
    });

    // Add event listeners for all announcement modals to handle backdrop clicks
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($announcements as $announcement)
            const modal{{ $announcement->id }} = document.getElementById('modal-announcement-{{ $announcement->id }}');
            if (modal{{ $announcement->id }}) {
                modal{{ $announcement->id }}.addEventListener('hidden.bs.modal', function() {
                    window.currentAnnouncementModalId = null;
                });
            }
        @endforeach
    });
    
    // Auto-rotate announcements every 8 seconds if more than one
    if (announcementCount > 1) {
        setInterval(() => {
            nextAnnouncement();
        }, 8000);
    }
</script> 