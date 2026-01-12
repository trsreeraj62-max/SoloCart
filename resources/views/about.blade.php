@extends('layouts.app')

@section('title', 'About Us - SoloCart')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4 text-center">About SoloCart</h1>
                    
                    <div class="row align-items-center mb-5">
                        <div class="col-md-6">
                            <h3 class="fw-bold mb-3">Our Mission</h3>
                            <p class="text-muted lead">
                                To revolutionize the online shopping experience by providing a seamless, secure, and enjoyable platform for discovering premium products.
                            </p>
                            <p class="text-muted">
                                Only at SoloCart do we prioritize quality over quantity. We curate our collections to ensure that every product you see meets our high standards of excellence.
                            </p>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="bg-light rounded-3 p-5 d-flex align-items-center justify-content-center" style="height: 250px;">
                                <span class="fw-bold text-muted fs-3">Our Story Image</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 text-center">
                        <div class="col-md-4">
                            <div class="p-4 border rounded-3 h-100">
                                <h4 class="fw-bold mb-2">Quality First</h4>
                                <p class="text-muted small">We never compromise on the quality of our products.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 border rounded-3 h-100">
                                <h4 class="fw-bold mb-2">Customer Focus</h4>
                                <p class="text-muted small">Your satisfaction is our top priority, always.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 border rounded-3 h-100">
                                <h4 class="fw-bold mb-2">Secure Shopping</h4>
                                <p class="text-muted small">Your data is always safe with our secure payments.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
