@extends('facilitator.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card profile-card">
                <div class="card-header bg-primary text-white text-center" style="background: var(--primary-gradient) !important;">
                    <div class="profile-avatar mb-2" style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 600; margin: 0 auto; border: 3px solid rgba(255, 255, 255, 0.4);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-0 mt-2">{{ $user->name }}</h4>
                    <div class="small text-white-50">Facilitator Account</div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('facilitator.profile.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                        <hr class="my-4">
                        <h5 class="mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Enter new password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8" placeholder="Confirm new password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Save Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 