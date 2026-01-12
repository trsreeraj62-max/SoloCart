@extends('layouts.app')

@section('title', 'My Profile - SoloCart')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2"></i>My Profile</h5>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="#" method="POST">
                        @csrf
                        <!-- Update Profile Logic to be implemented -->
                        
                        <div class="mb-4 text-center">
                            <div class="avatar-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary text-white fs-1" style="width: 100px; height: 100px; border-radius: 50%;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <h4 class="mb-1">{{ Auth::user()->name ?? 'User Name' }}</h4>
                            <p class="text-muted">{{ Auth::user()->email ?? 'email@example.com' }}</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', Auth::user()->name ?? '') }}" readonly>
                                <div class="form-text">Profile updates coming soon.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" readonly>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" readonly>
                            </div>

                            <div class="col-12 mt-4">
                                <hr>
                                <h6 class="fw-bold mb-3">Security</h6>
                                <button type="button" class="btn btn-outline-danger" disabled>Change Password (Coming Soon)</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
