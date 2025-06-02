@extends('layouts.barangay')

@section('title', 'Issuances')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-alt me-2"></i>
                    Issuances
                </h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Available Issuances
                    </h6>
                </div>
                <div class="card-body">
                    @if($issuances->count() > 0)
                        <div class="row">
                            @foreach($issuances as $issuance)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-circle bg-primary text-white me-3">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1">{{ $issuance->title }}</h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $issuance->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted small">File:</span>
                                                    <span class="badge bg-secondary">{{ strtoupper($issuance->file_type) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted small">Size:</span>
                                                    <span class="badge bg-info">{{ $issuance->file_size_human }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Uploaded by:</span>
                                                    <span class="small">{{ $issuance->uploader->name }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('barangay.issuances.show', $issuance) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-2"></i>
                                                    View Details
                                                </a>
                                                <a href="{{ route('barangay.issuances.download', $issuance) }}" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-download me-2"></i>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $issuances->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No issuances available</h5>
                            <p class="text-muted">There are currently no issuances uploaded by the admin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
