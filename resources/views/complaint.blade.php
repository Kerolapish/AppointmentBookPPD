@extends('layouts.app')

@section('title', 'Submit Complaint')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/complaint.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

<div class="main-container">
    
    <div class="page-header">
        <div class="icon-bubble">
            <i class="fa-solid fa-comment-dots"></i>
        </div>
        <h1>Submit Your Complaint</h1>
        <p>Your feedback is important to us. Please share your concerns and we'll work to resolve them promptly. All complaints are handled with care and confidentiality.</p>
    </div>

    <div class="form-card">
        <form action="#" method="POST">
            @csrf
            
            <div class="form-grid">
                
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="Enter your full name" value="{{ Auth::user()->name ?? '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" value="{{ Auth::user()->email ?? '' }}">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Ips</label>
                    <input type="text" name="ips" class="form-input" placeholder="Enter Ips">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Purpose of visit</label>
                    <select name="purpose" class="form-select">
                        <option value="" disabled selected>Select a purpose...</option>
                        <option value="complaint">Complaint</option>
                        <option value="feedback">Feedback</option>
                        <option value="inquiry">General Inquiry</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Location</label>
                    <textarea name="location" class="form-textarea" placeholder="Please provide details about the location or incident..."></textarea>
                </div>

            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">
                    Submit Complaint <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>

        </form>
    </div>

    <div class="contact-grid">
        <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
            <h3>Call Us</h3>
            <p>Speak directly with our support team.</p>
            <a href="#" class="contact-link">1-800-SUPPORT</a>
        </div>

        <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-comments"></i></div>
            <h3>Live Chat</h3>
            <p>Get instant help from our agents.</p>
            <a href="#" class="contact-link">Available 24/7</a>
        </div>

        <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-circle-question"></i></div>
            <h3>FAQ</h3>
            <p>Find answers to common questions.</p>
            <a href="#" class="contact-link">View FAQ</a>
        </div>
    </div>

    <div class="footer-links">
        © 2024 Support Center. Your satisfaction is our priority.
    </div>

</div>

@endsection