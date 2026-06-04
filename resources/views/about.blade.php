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

    /* --- ANATOMY LOGO SYSTEM --- */
    .anatomy-container {
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 850px;
        margin: 40px auto;
        position: relative;
    }

    /* Central Orange Circle Cover */
    #logo-cover {
        position: relative;
        width: 280px;
        height: 280px;
        background-color: #ff922b; /* Vibrant Orange */
        border-radius: 50%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 5;
        flex-shrink: 0;
    }

    /* Perfect Dead-Center Alignment for the Images */
    .logo-img {
        position: absolute;
        width: 75%;
        height: 75%;
        object-fit: contain;
        top: 12.5%;
        left: 12.5%;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        pointer-events: none; 
    }

    #hive-image { z-index: 6; }
    #book-image { z-index: 7; }

    /* Interactive Anatomy Pointer Blocks */
    .anatomy-side {
        display: flex;
        align-items: center;
        width: 240px;
        position: relative;
    }
    .anatomy-left { justify-content: flex-end; }
    .anatomy-right { justify-content: flex-start; }

    /* Labeled Click Action Targets */
    .anatomy-btn {
        background: rgba(255, 255, 255, 0.85);
        border: 2px solid #ffffff;
        color: #212529;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 30px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
        z-index: 10;
    }
    .anatomy-btn:hover {
        background: #ff922b;
        color: #ffffff;
        border-color: #ff922b;
        transform: translateY(-2px);
    }

    /* SVG Anatomy Guide Indicator Lines */
    .anatomy-line {
        flex-grow: 1;
        height: 2px;
        background: rgba(255, 255, 255, 0.6);
        position: relative;
    }
    /* Pointer dots hitting the orange circle edge */
    .anatomy-line::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        background: #ffffff;
        border-radius: 50%;
        top: -3px;
    }
    .anatomy-left .anatomy-line::after { right: 0; }
    .anatomy-right .anatomy-line::after { left: 0; }

    /* Interactive Grayscale Fade and Blur Filters */
    .img-fade-blur {
        filter: blur(8px) grayscale(40%);
        opacity: 0.45; 
    }

    /* Target hover effect via the button wrapper actions */
    .hovering-hive #hive-image,
    .hovering-book #book-image {
        transform: scale(1.06);
        filter: blur(0) grayscale(0%) !important;
        opacity: 1 !important;
    }

    /* --- TYPING GENERATOR TEXT CONTAINER --- */
    .text-generator-container {
        min-height: 120px; 
        max-width: 700px;
        margin: 0 auto;
    }

    .about-typed-text {
        font-size: 1.15rem;
        line-height: 1.8;
        font-weight: 500;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        display: inline;
    }

    .about-cursor {
        display: inline-block;
        width: 3px;
        background-color: #ff922b;
        margin-left: 4px;
        animation: blink 1s infinite;
    }

    /* --- FAQ INTERACTIVE PANEL DESIGN --- */
    .faq-control-panel {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

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

    .faq-typed-text {
        font-size: 1.1rem;
        line-height: 1.8;
        font-weight: 500;
        color: #2b303a;
        transition: color 0.3s ease;
    }

    .faq-typed-text.prompt {
        color: #adb5bd;
        font-style: italic;
    }

    .faq-cursor {
        display: inline-block;
        width: 3px;
        background-color: #ff922b;
        margin-left: 4px;
        animation: blink 1s infinite;
        font-weight: bold;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <!-- Top Page Header -->
            <div class="text-center mb-5">
                <div class="glass-header-box mb-3">
                    <h1 class="h2 mb-0 fw-bold text-dark">{{ __('messages.about_us') }}</h1>
                </div>
                <div class="mt-2">
                    <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill shadow-sm backdrop-blur lead mb-0">
                        Discover the story behind BookHive
                    </p>
                </div>
            </div>

            <!-- Anatomy Map Framework -->
            <div id="anatomy-box" class="anatomy-container">
                
                <!-- Left Node: Hive (Mission) -->
                <div class="anatomy-side anatomy-left">
                    <button id="btn-hive" class="anatomy-btn">Hive (Mission)</button>
                    <div class="anatomy-line"></div>
                </div>

                <!-- Central Overlap Target -->
                <div id="logo-cover">
                    <img id="hive-image" src="{{ asset('images/Hive.png') }}" alt="Hive Mission" class="logo-img">
                    <img id="book-image" src="{{ asset('images/Book.png') }}" alt="Book Vision" class="logo-img">
                </div>

                <!-- Right Node: Book (Vision) -->
                <div class="anatomy-side anatomy-right">
                    <div class="anatomy-line"></div>
                    <button id="btn-book" class="anatomy-btn">Book (Vision)</button>
                </div>

            </div>

            <!-- Typewriter Display Engine -->
            <div class="text-generator-container text-center mb-5 mt-4">
                <span id="output-text" class="about-typed-text"></span><span id="about-cursor" class="about-cursor d-none">|</span>
            </div>

            <hr class="border-white opacity-25 my-5">

            <!-- "FAQ" INTEGRATED INTERACTIVE SECTION -->
            <div class="text-center mb-5">
                <div class="glass-header-box mb-3">
                    <h2 class="h3 mb-0 fw-bold text-dark">Frequently Asked Questions</h2>
                </div>
                <div class="mt-2">
                    <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill shadow-sm backdrop-blur lead mb-0">
                        Got any questions? Try clicking any of the options below!
                    </p>
                </div>
            </div>

            <!-- Main Interactive Framework Row Splits -->
            <div class="row g-4 align-items-stretch mb-5">
                
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
                                <span id="faq-output-text" class="faq-typed-text prompt">
                                    <i class="fas fa-arrow-left me-2 opacity-50"></i>Select any of the questions to see the answer.
                                </span><span id="faq-cursor" class="faq-cursor d-none">|</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

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
    // Manifest Configurations
    const statements = {
        mission: "At BookHive, we believe in the power of stories to transform lives. Our mission is to connect readers with exceptional books from talented authors across all genres, making quality literature accessible to everyone.",
        vision: "Our vision is to build a thriving, dynamic ecosystem where independent authors can seamlessly reach their ideal audience, and enthusiastic readers can instantly unearth their next favorite book."
    };

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

    // DOM Elements Mapping
    const anatomyBox = document.getElementById('anatomy-box');
    const hiveImg = document.getElementById('hive-image');
    const bookImg = document.getElementById('book-image');
    const btnHive = document.getElementById('btn-hive');
    const btnBook = document.getElementById('btn-book');
    const outputText = document.getElementById('output-text');
    const aboutCursor = document.getElementById('about-cursor');

    const faqOutputText = document.getElementById('faq-output-text');
    const faqCursor = document.getElementById('faq-cursor');
    const faqButtons = document.querySelectorAll('.faq-btn');

    let aboutTypingTimer = null;
    let faqTypingTimer = null;
    let currentActiveType = null;

    // --- ABOUT TYPEWRITER ENGINE ---
    function startAboutTyping(text, type) {
        currentActiveType = type;
        clearInterval(aboutTypingTimer);
        outputText.textContent = "";
        aboutCursor.classList.remove('d-none');
        
        let charIndex = 0;
        aboutTypingTimer = setInterval(() => {
            if (charIndex < text.length) {
                outputText.textContent += text.charAt(charIndex);
                charIndex++;
            } else {
                clearInterval(aboutTypingTimer);
                aboutCursor.classList.add('d-none');
            }
        }, 25);
    }

    // --- FAQ TYPEWRITER ENGINE ---
    function triggerFaqTypewriterOutput(text) {
        clearInterval(faqTypingTimer);
        
        faqOutputText.classList.remove('prompt');
        faqOutputText.textContent = "";
        faqCursor.classList.remove('d-none');
        
        let charIndex = 0;
        faqTypingTimer = setInterval(() => {
            if (charIndex < text.length) {
                faqOutputText.textContent += text.charAt(charIndex);
                charIndex++;
            } else {
                clearInterval(faqTypingTimer);
                faqCursor.classList.add('d-none');
            }
        }, 15);
    }

    // --- ABOUT EVENT LISTENERS ---
    btnHive.addEventListener('click', () => {
        bookImg.classList.add('img-fade-blur');
        hiveImg.classList.remove('img-fade-blur');
        startAboutTyping(statements.mission, 'mission');
    });

    btnBook.addEventListener('click', () => {
        hiveImg.classList.add('img-fade-blur');
        bookImg.classList.remove('img-fade-blur');
        startAboutTyping(statements.vision, 'vision');
    });

    btnHive.addEventListener('mouseenter', () => {
        anatomyBox.classList.add('hovering-hive');
        hiveImg.classList.remove('img-fade-blur');
    });
    btnHive.addEventListener('mouseleave', () => {
        anatomyBox.classList.remove('hovering-hive');
        if (currentActiveType === 'vision') hiveImg.classList.add('img-fade-blur');
    });

    btnBook.addEventListener('mouseenter', () => {
        anatomyBox.classList.add('hovering-book');
        bookImg.classList.remove('img-fade-blur');
    });
    btnBook.addEventListener('mouseleave', () => {
        anatomyBox.classList.remove('hovering-book');
        if (currentActiveType === 'mission') bookImg.classList.add('img-fade-blur');
    });

    // --- FAQ EVENT LISTENERS ---
    faqButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedKey = this.getAttribute('data-faq-key');
            
            faqButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (faqManifest[selectedKey]) {
                triggerFaqTypewriterOutput(faqManifest[selectedKey]);
            }
        });
    });
</script>

@endsection