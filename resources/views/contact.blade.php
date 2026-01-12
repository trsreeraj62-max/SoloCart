@extends('layouts.app')

@section('title', 'Contact Us - SoloCart')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h2 class="mb-0 fw-bold text-center">Contact Us</h2>
                </div>
                <div class="card-body p-5">
                    <p class="text-center text-muted mb-4">Have questions? We'd love to hear from you.</p>

                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help?" required>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-5 text-center">
                <h5 class="fw-bold">Other ways to reach us</h5>
                <p class="text-muted">support@solocart.com | +1 (555) 123-4567</p>
                <p class="text-muted">123 Commerce St, Shopping City, SC 12345</p>
            </div>
        </div>
    </div>
</div>
@endsection
