@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-image: url("{{ asset('images/background1.jpg') }}") !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important; 
        background-position: center center !important; 
        background-size: cover !important; 
        min-height: 100vh;
        padding-top: 100px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* GLASS BOX FOR HEADERS */
    .glass-header-box {
        background: rgba(255, 255, 255, 0.45); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* --- FAQ INTERACTIVE PANEL DESIGN --- */
    .faq-control-panel {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Interactive Question Target Nodes */
    .faq-btn {
        background: rgba(255, 255, 255, 0.85);
        border: 2px solid #ffffff;
        color: #212529;
        font-weight: 700;
        padding: 14px 24px;
        border-radius: 16px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        text-align: left;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.92rem;
    }

    .faq-btn i.fa-chevron-right {
        transition: transform 0.3s ease;
        color: #ff922b;
    }

    .faq-btn:hover {
        background: #ffffff;
        border-color: #ff922b;
        transform: translateX(6px);
        box-shadow: 0 6px 20px rgba(255, 146, 43, 0.15);
    }

    /* Active Highlight Status State */
    .faq-btn.active {
        background: #ffffff;
        border-color: #ff922b;
        color: #ff922b;
        box-shadow: 0 8px 25px rgba(255, 146, 43, 0.2);
    }

    .faq-btn.active i.fa-chevron-right {
        transform: rotate(90deg);
    }

    /* --- CLEAN MINIMAL RESPONSE BOX PANEL --- */
    .faq-response-box {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        min-height: 400px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
    }

    .monitor-screen {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .typed-text {
        font-size: 1.1rem;
        line-height: 1.8;
        font-weight: 500;
        color: #2b303a;
        transition: color 0.3s ease;
    }

    /* Idle instructions state text adjustment token */
    .typed-text.prompt {
        color: #adb5bd;
        font-style: italic;
    }

    .cursor {
        display: inline-block;
        width: 3px;
        background-color: #ff922b;
        margin-left: 4px;
        animation: blink 1s infinite;
        font-weight: bold;
    }

    /* --- ANIMATION KEYFRAMES --- */
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    /* ===== RESPONSIVE STYLES FOR FAQ PAGE ===== */
    @media (max-width: 768px) {
        /* Body padding adjustment */
        body {
            padding-top: 80px;
        }

        /* Container padding */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Glass header box */
        .glass-header-box {
            padding: 10px 20px;
            border-radius: 30px;
        }

        /* Heading sizing */
        .h1, h1 {
            font-size: 1.5rem;
        }

        .h2, h2 {
            font-size: 1.25rem;
        }

        .h5, h5 {
            font-size: 1rem;
        }

        /* FAQ button sizing */
        .faq-btn {
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        /* Gap adjustments */
        .gap-4 {
            gap: 1.5rem !important;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.95rem;
        }

        .small {
            font-size: 0.9rem;
        }

        /* Row/column spacing */
        .row {
            gap: 1rem !important;
        }
    }

    @media (max-width: 576px) {
        /* Extra small screens */
        body {
            padding-top: 70px;
            padding-left: 0;
            padding-right: 0;
        }

        /* Container padding */
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        /* Glass header box */
        .glass-header-box {
            padding: 8px 16px;
            border-radius: 20px;
            margin-bottom: 1.5rem !important;
        }

        /* Heading sizing */
        .h1, h1 {
            font-size: 1.25rem;
        }

        .h2, h2 {
            font-size: 1.1rem;
        }

        .h5, h5 {
            font-size: 0.95rem;
        }

        .mb-5 {
            margin-bottom: 1.5rem !important;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        /* FAQ button sizing and styling */
        .faq-btn {
            padding: 10px 12px;
            font-size: 0.8rem;
            border-radius: 12px;
            min-height: 44px;
            display: flex;
            align-items: center;
        }

        .faq-btn:hover {
            transform: translateX(4px);
        }

        /* FAQ icon sizing */
        .faq-btn i {
            font-size: 0.9rem;
        }

        /* FAQ control panel */
        .faq-control-panel {
            gap: 8px;
        }

        /* FAQ response box */
        .faq-response-box {
            padding: 1rem;
            border-radius: 12px;
            min-height: 200px;
        }

        /* Response text sizing */
        .faq-response-content {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Cursor sizing */
        .cursor {
            width: 2px;
            margin-left: 2px;
        }

        /* Text utilities */
        .text-muted {
            font-size: 0.85rem;
        }

        .small {
            font-size: 0.8rem;
        }

        /* Icon sizing */
        .fa-4x {
            font-size: 1.75rem;
        }

        .fa-3x {
            font-size: 1.5rem;
        }

        .fa-2x {
            font-size: 1.2rem;
        }

        /* Row/column spacing */
        .row {
            gap: 0.75rem !important;
        }

        .gap-4 {
            gap: 1rem !important;
        }

        .gap-3 {
            gap: 0.75rem !important;
        }

        /* Column sizing */
        .col-xl-11 {
            flex-basis: 100%;
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }

        /* Link wrapping */
        a {
            word-break: break-word;
        }
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <!-- Top Page Header -->
            <div class="text-center mb-5">
                <div class="glass-header-box mb-3">
                    <h1 class="h2 mb-0 fw-bold text-dark">Frequently Asked Questions</h1>
                </div>
                <div class="mt-2">
                    <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill shadow-sm backdrop-blur lead mb-0">
                        Got any questions? Try clicking any of the options below!
                    </p>
                </div>
            </div>

            <!-- Main Interactive Framework Row Splits -->
            <div class="row g-4 align-items-stretch">
                
                <!-- Left Split: Interactive Question Controls Navigation -->
                <div class="col-lg-5">
                    <div class="faq-control-panel">
                        <button class="faq-btn" data-faq-key="q1">
                            <span>What is BookHive?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q2">
                            <span>Are the books on BookHive official copies?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q3">
                            <span>How do store stocks and locations work?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q4">
                            <span>What are my shipping and delivery options?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q5">
                            <span>What book details can I view before buying?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q6">
                            <span>How can I find a specific book quickly?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q7">
                            <span>What is the Roulette Wheel feature?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="faq-btn" data-faq-key="q8">
                            <span>Can I save titles to buy or read later?</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Right Split: Clean Response Output Box -->
                <div class="col-lg-7">
                    <div class="card border-0 faq-response-box h-100 p-3">
                        <div class="monitor-screen">
                            <div class="w-100 text-center text-md-start">
                                <span id="output-text" class="typed-text prompt">
                                    <i class="fas fa-arrow-left me-2 opacity-50"></i>Select any of the questions to see the answer.
                                </span><span id="cursor" class="cursor d-none">|</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <hr class="border-white opacity-25 my-5">

            <!-- Dynamic Customer Service Footer Note -->
            <div class="text-center">
                <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill shadow-sm backdrop-blur mb-0 small">
                    <i class="fas fa-info-circle text-warning me-2"></i>Still looking for further information or questions? Feel free to <a href="mailto:support@bookhive.test" class="fw-bold text-white text-decoration-underline ms-1">contact administration directly</a>.
                </p>
            </div>

        </div>
    </div>
</div>

<script>
    // Comprehensive array containing updated answers fitting the specific 2026 BookHive ecosystem
    const faqManifest = {
        q1: "BookHive is an online book reseller platform originated from Indonesia in 2026. We are dedicated to providing book enthusiasts across the nation with a modern, seamless storefront for browsing, researching, and securely purchasing their next favorite reads.",
        q2: "Yes, absolutely. BookHive deals strictly in official book copies. When you shop with us, you are guaranteed to receive genuine, high-quality prints directly authorized by publishers and distributed through our official store channels.",
        q3: "We connect directly with physical stores currently selling your desired titles. Our checkout system maps real-time inventory updates, showing you exactly which storefront locations have your chosen books in stock before you commit to a purchase.",
        q4: "BookHive delivers books straight to your doorstep with various shipping options tailored for maximum flexibility. Whether you need a cost-effective choice for budget purposes or an expedited service for a quick delivery, we have options to fit your needs.",
        q5: "Every book in our showcase features full analytical mapping details. You can easily view critical metadata parameters including the publication year, the total page count, and a beautifully descriptive synopsis providing structural insight into the story.",
        q6: "To prevent users from wasting time searching through titles they don't know, BookHive features an instant search filter framework. Simply type keywords into the search box to find and access your desired book records in seconds.",
        q7: "For a fun and interactive experience, we have a unique Roulette Wheel module! Whenever you feel confused on what book you want to read next but still want to explore new horizons anyway, you can spin the wheel to randomly select a curated, recommended book.",
        q8: "Yes, you do! You can easily save books to your customized Wishlist profile. Additionally, if you change your mind or want to modify your order items before checkout, you can adjust quantities or remove records dynamically straight from your Cart dashboard."
    };

    const outputText = document.getElementById('output-text');
    const cursor = document.getElementById('cursor');
    const faqButtons = document.querySelectorAll('.faq-btn');

    let typingTimer = null;
    let currentlySelectedKey = null;

    /**
     * Executes the incremental typewriter data printing engine
     */
    function triggerTypewriterOutput(text, targetKey) {
        currentlySelectedKey = targetKey;
        clearInterval(typingTimer);
        
        outputText.classList.remove('prompt');
        outputText.textContent = "";
        cursor.classList.remove('d-none');
        
        let charIndex = 0;
        
        typingTimer = setInterval(() => {
            if (charIndex < text.length) {
                outputText.textContent += text.charAt(charIndex);
                charIndex++;
            } else {
                clearInterval(typingTimer);
                cursor.classList.add('d-none');
            }
        }, 15);
    }

    // Initialize click mapping loops over control nodes
    faqButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedKey = this.getAttribute('data-faq-key');
            
            // Toggle active visual focus states across navigation controls
            faqButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Execute text generator payload
            if (faqManifest[selectedKey]) {
                triggerTypewriterOutput(faqManifest[selectedKey], selectedKey);
            }
        });
    });
</script>

@endsection