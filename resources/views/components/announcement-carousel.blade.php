<div id="announcementCarousel" class="carousel slide h-100" data-bs-ride="carousel">
    <div class="carousel-indicators">
        @foreach($announcements as $index => $announcement)
            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
    
    <div class="carousel-inner h-100">
        @foreach($announcements as $index => $announcement)
            <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}" 
                 style="background-color: {{ $announcement->background_color }}">
                
                <div class="container d-flex align-items-center h-100">
                    <div class="announcement-content">
                        @if(Str::contains(strtolower($announcement->title), ['congratulations', 'award', 'recognition', 'achievement']))
                            <div class="announcement-badge">
                                <i class="fas fa-award me-2"></i> Recognition
                            </div>
                        @elseif(Str::contains(strtolower($announcement->title), ['update', 'notice', 'alert']))
                            <div class="announcement-badge">
                                <i class="fas fa-bell me-2"></i> Important Update
                            </div>
                        @elseif(Str::contains(strtolower($announcement->title), ['event', 'meeting', 'conference']))
                            <div class="announcement-badge">
                                <i class="fas fa-calendar me-2"></i> Upcoming Event
                            </div>
                        @else
                            <div class="announcement-badge">
                                <i class="fas fa-info-circle me-2"></i> Announcement
                            </div>
                        @endif

                        <div class="row w-100">
                            <div class="col-md-{{ $announcement->image_path ? '6' : '12' }}">
                                <h2 class="announcement-title">{{ $announcement->title }}</h2>
                                <div class="announcement-text">
                                    {!! $announcement->content !!}
                                </div>
                                @if($announcement->button_text && $announcement->button_link)
                                    <a href="{{ $announcement->button_link }}" class="btn btn-light btn-lg mt-3 px-4">
                                        {{ $announcement->button_text }}
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                @endif
                            </div>
                            
                            @if($announcement->image_path)
                                <div class="col-md-6">
                                    <div class="p-3 p-md-4">
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}" 
                                             class="img-fluid rounded shadow-lg" 
                                             alt="{{ $announcement->title }}"
                                             style="border: 5px solid rgba(255,255,255,0.1);">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = new bootstrap.Carousel(document.getElementById('announcementCarousel'), {
            interval: 7000,
            wrap: true
        });
    });
</script>
