<footer class="text-white py-5 mt-auto" style="background: linear-gradient(135deg,#2c3e50,#34495e);">
    <div class="container-fluid px-4 px-md-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-book me-2 text-warning"></i>BookHive
                </h5>
                <p class="text-white-50 mb-3">Your trusted source for quality books. We connect readers with amazing stories from talented authors across all genres.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50 hover-gold fs-5" title="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-white-50 hover-gold fs-5" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-white-50 hover-gold fs-5" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-white-50 hover-gold fs-5" title="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-gold">Home</a></li>
                    <li class="mb-2"><a href="{{ route('books.listing') }}" class="text-white-50 text-decoration-none hover-gold">Books</a></li>
                    <li class="mb-2"><a href="{{ route('authors.index') }}" class="text-white-50 text-decoration-none hover-gold">Authors</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white-50 text-decoration-none hover-gold">About Us</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Book Categories</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-gold">Fiction</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-gold">Non-Fiction</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-gold">Science Fiction</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-gold">Mystery</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Contact Info</h6>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-2"><i class="fas fa-phone me-2 text-warning"></i>+1 (555) 123-4567</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-warning"></i>info@bookhive.com</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-warning"></i>123 Real Estate Ave, City, ST 12345</li>
                    <li class="mb-2"><i class="fas fa-clock me-2 text-warning"></i>Mon-Fri: 9AM-6PM</li>
                </ul>
            </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <span class="text-white-50">{{ __('messages.copyright', ['year' => date('Y')]) }}</span>
            </div>

            <div class="d-flex gap-4">
                <a href="#" class="text-white-50 text-decoration-none hover-gold small">Terms of Service</a>
                <a href="#" class="text-white-50 text-decoration-none hover-gold small">Privacy Policy</a>
                <a href="#" class="text-white-50 text-decoration-none hover-gold small">Cookie Policy</a>
            </div>
        </div>
    </div>

    <style>
        .hover-gold:hover {
            color: #ffd700 !important;
            transition: color 0.3s ease;
        }

        .hover-gold {
            transition: color 0.3s ease;
        }
    </style>
</footer>