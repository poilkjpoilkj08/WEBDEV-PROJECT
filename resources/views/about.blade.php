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

    .typed-text {
        font-size: 1.15rem;
        line-height: 1.8;
        font-weight: 500;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        display: inline;
    }

    .cursor {
        display: inline-block;
        width: 3px;
        background-color: #ff922b;
        margin-left: 4px;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
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
                <span id="output-text" class="typed-text"></span><span id="cursor" class="cursor d-none">|</span>
            </div>

            <hr class="border-white opacity-25 my-5">

            <!-- "OUR TEAM" SECTION -->
            <div class="text-center mb-5">
                <div class="glass-header-box mb-4">
                    <h2 class="h3 mb-0 fw-bold text-dark">Our Team</h2>
                </div>
                <p class="text-white bg-dark bg-opacity-25 d-inline-block px-4 py-2 rounded-pill lead">
                    The creative minds building the future of literary communities.
                </p>
            </div>

        </div>
    </div>
</div>

<script>
    const statements = {
        mission: "At BookHive, we believe in the power of stories to transform lives. Our mission is to connect readers with exceptional books from talented authors across all genres, making quality literature accessible to everyone.",
        vision: "Our vision is to build a thriving, dynamic ecosystem where independent authors can seamlessly reach their ideal audience, and enthusiastic readers can instantly unearth their next favorite book."
    };

    const anatomyBox = document.getElementById('anatomy-box');
    const hiveImg = document.getElementById('hive-image');
    const bookImg = document.getElementById('book-image');
    const btnHive = document.getElementById('btn-hive');
    const btnBook = document.getElementById('btn-book');
    const outputText = document.getElementById('output-text');
    const cursor = document.getElementById('cursor');

    let typingTimer = null;
    let currentActiveType = null;

    function startTyping(text, type) {
        currentActiveType = type;
        clearInterval(typingTimer);
        outputText.textContent = "";
        
        // Show the cursor right when typing starts
        cursor.classList.remove('d-none');
        
        let charIndex = 0;
        
        typingTimer = setInterval(() => {
            if (charIndex < text.length) {
                outputText.textContent += text.charAt(charIndex);
                charIndex++;
            } else {
                clearInterval(typingTimer);
                // Hide the cursor immediately when generation completes
                cursor.classList.add('d-none');
            }
        }, 25);
    }

    // Explicit click routing using buttons exclusively
    btnHive.addEventListener('click', () => {
        bookImg.classList.add('img-fade-blur');
        hiveImg.classList.remove('img-fade-blur');
        startTyping(statements.mission, 'mission');
    });

    btnBook.addEventListener('click', () => {
        hiveImg.classList.add('img-fade-blur');
        bookImg.classList.remove('img-fade-blur');
        startTyping(statements.vision, 'vision');
    });

    // Hover bridging effects from buttons over to image layouts
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
</script>

@endsection