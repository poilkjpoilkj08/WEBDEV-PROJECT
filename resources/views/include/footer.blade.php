<footer class="text-white py-4 mt-auto" style="background: linear-gradient(135deg, #c25e25, #a64f1e); backdrop-filter: blur(5px);">
    <div class="container-fluid px-4 px-md-5">
        <div class="row align-items-start">
            <!-- Brand Column -->
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="fw-bold mb-2">
                    <a class="text-white d-flex align-items-center text-decoration-none" 
                       href="{{ route('home') }}" 
                       style="box-shadow: none !important; outline: none !important;">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="BookHive Logo" 
                             height="30"
                             class="me-2 d-inline-block align-top"
                             style="object-fit: contain; filter: none !important;">
                        BookHive
                    </a>
                </h5>
                <p class="text-white-50 mb-3 small" style="line-height: 1.5; max-width: 300px;">
                    Your trusted source for quality books. We connect readers with amazing stories from talented authors.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50 hover-orange-cream fs-6" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white-50 hover-orange-cream fs-6" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white-50 hover-orange-cream fs-6" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white-50 hover-orange-cream fs-6" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="col-md-2 col-6 mb-3 mb-md-0">
                <h6 class="fw-bold mb-2 text-uppercase tracking-wider extra-small" style="letter-spacing: 0.5px; font-size: 0.75rem;">Quick Links</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-1"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-orange-cream">Home</a></li>
                    <li class="mb-1"><a href="{{ route('books.listing') }}" class="text-white-50 text-decoration-none hover-orange-cream">Books</a></li>
                    <li class="mb-1"><a href="{{ route('authors.index') }}" class="text-white-50 text-decoration-none hover-orange-cream">Authors</a></li>
                    <li class="mb-1"><a href="{{ route('about') }}" class="text-white-50 text-decoration-none hover-orange-cream">About Us</a></li>
                </ul>
            </div>

            <!-- Categories Column -->
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <h6 class="fw-bold mb-2 text-uppercase tracking-wider extra-small" style="letter-spacing: 0.5px; font-size: 0.75rem;">Categories</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-1"><a href="#" class="text-white-50 text-decoration-none hover-orange-cream">Fiction</a></li>
                    <li class="mb-1"><a href="#" class="text-white-50 text-decoration-none hover-orange-cream">Non-Fiction</a></li>
                    <li class="mb-1"><a href="#" class="text-white-50 text-decoration-none hover-orange-cream">Science Fiction</a></li>
                    <li class="mb-1"><a href="#" class="text-white-50 text-decoration-none hover-orange-cream">Mystery</a></li>
                </ul>
            </div>

            <!-- Contact Info Column -->
            <div class="col-md-3 mb-0">
                <h6 class="fw-bold mb-2 text-uppercase tracking-wider extra-small" style="letter-spacing: 0.5px; font-size: 0.75rem;">Contact Info</h6>
                <ul class="list-unstyled text-white-50 mb-0 small">
                    <li class="mb-1 d-flex align-items-center"><i class="fas fa-phone me-2 text-white-50 icon-accent" style="font-size: 0.85rem;"></i><span>+1 (555) 123-4567</span></li>
                    <li class="mb-1 d-flex align-items-center"><i class="fas fa-envelope me-2 text-white-50 icon-accent" style="font-size: 0.85rem;"></i><span>info@bookhive.com</span></li>
                    <li class="mb-1 d-flex align-items-start"><i class="fas fa-map-marker-alt me-2 mt-1 text-white-50 icon-accent" style="font-size: 0.85rem;"></i><span>123 Real Estate Ave, City, ST 12345</span></li>
                </ul>
            </div>
        </div>

        <hr class="my-3" style="border-color: rgba(255,255,255,0.08);">

        <!-- Bottom Legal Row -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <span class="text-white-50 extra-small" style="font-size: 0.8rem;">{{ __('messages.copyright', ['year' => date('Y')]) }}</span>
            </div>

            <div class="d-flex gap-3">
                <a href="#" class="text-white-50 text-decoration-none hover-orange-cream extra-small" style="font-size: 0.8rem;">Terms</a>
                <a href="#" class="text-white-50 text-decoration-none hover-orange-cream extra-small" style="font-size: 0.8rem;">Privacy</a>
                <a href="#" class="text-white-50 text-decoration-none hover-orange-cream extra-small" style="font-size: 0.8rem;">Cookies</a>
            </div>
        </div>
    </div>

    <style>
        /* Modernized Soft Cream Highlight to match Terracotta theme seamlessly */
        .hover-orange-cream {
            display: inline-block;
            transition: color 0.2s ease, transform 0.2s ease !important;
        }

        .hover-orange-cream:hover {
            color: #ffdcb3 !important; /* Soft premium cream hint */
            transform: translateY(-1px); /* Clean micro-lift movement */
        }
        
        /* Subtle aesthetic tweak for contact icons */
        .icon-accent {
            transition: color 0.2s ease;
        }
        
        footer:hover .icon-accent {
            color: #ffdcb3 !important;
        }
    </style>
</footer>