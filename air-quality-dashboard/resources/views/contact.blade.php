@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">Contact Breazy AQI</h1>
                <p class="lead text-muted">Have questions about air quality in Colombo? We're here to help!</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4 p-md-5">
                    <div class="row">
                        <div class="col-md-7">
                            <h2 class="h4 mb-4">Send us a message</h2>
                            <form action="{{ route('contact.submit') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <div class="border-start border-primary border-3 ps-4 h-100 mt-4 mt-md-0">
                                <h2 class="h4 mb-4">Contact Information</h2>
                                <div class="mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>Address
                                    </h5>
                                    <p class="mb-0">University of Colombo,<br>Colombo 03, Sri Lanka</p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-phone me-2"></i>Phone
                                    </h5>
                                    <p class="mb-0">+94 11 123 4567</p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </h5>
                                    <p class="mb-0">info@breazyaqi.lk</p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-clock me-2"></i>Working Hours
                                    </h5>
                                    <p class="mb-0">Monday - Friday: 9am - 5pm<br>Weekend: Closed</p>
                                </div>
                                <h5 class="text-primary mb-3 mt-5">Follow Us</h5>
                                <div class="d-flex">
                                    <a href="#" class="btn btn-outline-primary rounded-circle me-2">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary rounded-circle me-2">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary rounded-circle me-2">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary rounded-circle">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4">Our Location</h2>
                    <div class="ratio ratio-21x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798584005687!2d79.85769731477253!3d6.900698995012632!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25926b85d1b33%3A0x2f72b2cc4c3fd245!2sUniversity%20of%20Colombo!5e0!3m2!1sen!2sus!4v1650452512050!5m2!1sen!2sus" 
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
@endsection