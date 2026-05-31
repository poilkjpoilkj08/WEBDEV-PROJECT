@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Unified pure clean white background system theme */
        min-height: 100vh;
        padding-top: 100px;
        overflow-x: hidden; /* Prevent horizontal spillshifts when roulette wheel spins */
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
        background: rgba(248, 249, 250, 0.85); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 10px 28px;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid #eef0f2;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* --- ROULETTE ARENA CONTAINER --- */
    .roulette-arena {
        position: relative;
        height: 600px; /* Slightly heightened to accommodate the wider circle arc profile */
        width: 100%;
        margin-top: 20px;
        background: #fdfdfd;
        border: 1px solid #f1f3f5;
        border-radius: 24px;
        box-shadow: inset 0 10px 30px rgba(0,0,0,0.01), 0 4px 20px rgba(0,0,0,0.02);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Top Control Hub Console */
    .control-deck-hub {
        margin-top: 35px;
        position: relative;
        z-index: 50;
        text-align: center;
    }

    /* Center Indicator Arrow Down */
    .indicator-arrow-pin {
        position: absolute;
        bottom: 435px; /* Shifted to sit flush against the newly scaled upper wheel ring line */
        left: 50%;
        transform: translateX(-50%);
        z-index: 40;
        color: #a64f1e;
        font-size: 3rem;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        animation: pinBounce 1.2s infinite ease-in-out;
    }

    /* Expanded Half Roulette Wheel Deck Wheel Pivot */
    .wheel-deck-pivot {
        position: absolute;
        bottom: -980px; /* Deep center axis drop to cleanly hide the lower bulk of the 1400px circle */
        left: 50%;
        width: 1400px;
        height: 1400px;
        margin-left: -700px;
        background: #ffffff;
        border-radius: 50%;
        border: 16px solid #ffffff;
        box-shadow: 0 -15px 45px rgba(0,0,0,0.06), inset 0 0 60px rgba(0,0,0,0.01);
        transform: rotate(0deg);
        transform-origin: center center;
        transition: transform 6s cubic-bezier(0.15, 0.95, 0.3, 1);
        z-index: 10;
        overflow: hidden;
    }

    /* Outer Boundary Ring accents */
    .wheel-deck-pivot::before {
        content: '';
        position: absolute;
        top: -2px; left: -2px; right: -2px; bottom: -2px;
        border: 2px solid #eef0f2;
        border-radius: 50%;
        pointer-events: none;
    }

    /* Central Core Black Hole Axis Component */
    .wheel-core-socket {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 140px;
        height: 140px;
        margin-top: -70px;
        margin-left: -70px;
        background: #1a1d24;
        border-radius: 50%;
        border: 8px solid #ffffff;
        box-shadow: 0 0 25px rgba(0,0,0,0.35), inset 0 0 15px rgba(0,0,0,0.8);
        z-index: 30;
        pointer-events: none;
    }

    /* Individual Book Segment Slot Placements - Spread out on the larger radius grid */
    .wheel-book-segment {
        position: absolute;
        top: 0;
        left: 50%;
        width: 360px; /* Perfectly scaled wedge geometry width for a 12-slice wheel layout */
        height: 700px; /* Matches the 1400px circle radius factor to hit center socket core axis */
        margin-left: -180px;
        transform-origin: center bottom;
        text-align: center;
        padding-top: 75px; /* Increased padding-top to shift book covers inward away from the border edge */
        backface-visibility: hidden;
        z-index: 20;
    }

    /* Alternating Hive Segment Color Pattern Background Conical Slices */
    .segment-color-cone {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        clip-path: polygon(50% 100%, 0 0, 100% 0);
        pointer-events: none;
        z-index: 1;
    }

    /* Even segments: Premium soft off-white canvas background code */
    .wheel-book-segment:nth-child(even) .segment-color-cone {
        background-color: #fafafa;
    }

    /* Odd segments: Dynamic, rich Hive Orange hue variable */
    .wheel-book-segment:nth-child(odd) .segment-color-cone {
        background-color: #e8732a; 
    }

    .segment-inner-content {
        position: relative;
        z-index: 5;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Upscaled Book Cover Box Frames */
    .segment-image-wrap {
        width: 140px;  
        height: 200px; 
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform 0.3s ease;
    }

    .segment-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* --- BRAND ACTION INTERACTION COMPONENT STYLES --- */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }

    .btn-soft-orange:disabled {
        background-color: #e2e8f0 !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        cursor: not-allowed;
    }

    /* --- POPUP DECK OVERLAY MODAL CONFIGURATIONS --- */
    .custom-narrow-modal {
        max-width: 360px !important;
        margin-left: auto;
        margin-right: auto;
    }

    .modal-cover-preview {
        height: 250px; 
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .dismiss-subtext-label {
        font-size: 0.72rem;
        color: #94a3b8;
        letter-spacing: 0.02em;
        pointer-events: none;
        line-height: 1.4;
    }

    @keyframes pinBounce {
        0%, 100% { transform: translate(-50%, 0px); }
        50% { transform: translate(-50%, -8px); } 
    }
</style>

@php
    if(!isset($books) || $books->isEmpty()){
        $books = \App\Models\Book::where('status', 'available')->take(12)->get();
    }

    // Keep strict 12 segments on the enlarged canvas layout grid to avoid a cramped visual presentation
    $displayCollection = $books->take(12);
@endphp

<div class="container py-5 content-wrapper">
    <div class="text-center mb-4">
        <div class="glass-header-box mb-2">
            <h1 class="h3 mb-0 fw-bold text-dark">Book Roulette</h1>
        </div>
        <p class="text-muted small mb-0">Confused about what book to choose? Spin the wheel and let fate reveal your next adventure.</p>
    </div>

    <div class="roulette-arena">
        
        <div class="control-deck-hub">
            <button id="spinTriggerBtn" class="btn btn-soft-orange btn-lg px-5 py-3 rounded-pill fw-bold text-uppercase shadow brand-transition">
                <i class="fas fa-sync-alt me-2"></i>Spin the Wheel
            </button>
        </div>

        <div class="indicator-arrow-pin">
            <i class="fas fa-caret-down"></i>
        </div>

        <div class="wheel-deck-pivot" id="wheelDeck">
            
            <div class="wheel-core-socket"></div>

            @foreach($displayCollection as $index => $book)
                @php
                    // Map out precise angles for 12 clean, widespread equidistant slice segments (30 degrees each)
                    $angle = ($index * (360 / $displayCollection->count()));
                @endphp
                <div class="wheel-book-segment" style="transform: rotate({{ $angle }}deg);" 
                     data-book-id="{{ $book->id }}"
                     data-title="{{ $book->title }}"
                     data-author="{{ $book->author?->name ?? 'Unknown Author' }}"
                     data-cover="{{ asset($book->cover_image_url ?? $book->cover_image_src) }}"
                     data-url="{{ route('books.show', $book->id) }}"
                     data-angle="{{ $angle }}">
                    
                    <div class="segment-color-cone"></div>

                    <div class="segment-inner-content">
                        <div class="segment-image-wrap">
                            <img src="{{ asset($book->cover_image_url ?? $book->cover_image_src) }}" alt="Mysterious Book Option" loading="lazy">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<div class="modal fade" id="rouletteResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered custom-narrow-modal">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="modal-body text-center px-4 pb-4 pt-5">
                <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill mb-3 font-monospace text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.05em;">
                    <i class="fas fa-sparkles text-warning me-1"></i>Fate Has Chosen
                </span>
                
                <div class="mb-3.5">
                    <img id="modalBookCover" src="" class="modal-cover-preview img-fluid" alt="Selected Cover Title">
                </div>
                
                <h3 class="h6 fw-bold text-dark mb-1 lh-base text-truncate px-1" id="modalBookTitle">Book Title Target</h3>
                <p class="text-muted small mb-4">by <span class="fw-semibold text-secondary" id="modalBookAuthor">Author Frame</span></p>
                
                <div class="d-flex flex-column gap-2 mb-3">
                    <a id="modalBookLink" href="#" class="btn btn-soft-orange btn-md fw-bold py-3 rounded-pill text-uppercase shadow-sm w-100" style="font-size: 0.8rem; letter-spacing: 0.02em;">
                        <i class="fas fa-book-open me-2"></i>View Details
                    </a>
                    <button type="button" id="modalSpinAgainBtn" class="btn btn-light btn-md fw-bold text-secondary border rounded-pill py-3 w-100" style="font-size: 0.8rem; background-color: #fafafa;">
                        <i class="fas fa-redo me-1.5" style="font-size: 0.85em;"></i>Spin Again
                    </button>
                </div>

                <div class="text-center mt-3.5">
                    <span class="dismiss-subtext-label text-muted font-monospace"><i class="fas fa-info-circle me-1"></i>Click anywhere outside to dismiss this window</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spinBtn = document.getElementById('spinTriggerBtn');
        const modalSpinBtn = document.getElementById('modalSpinAgainBtn');
        const wheel = document.getElementById('wheelDeck');
        const segments = document.querySelectorAll('.wheel-book-segment');
        
        // Initialize explicit Bootstrap reference wrapper variable
        const resultModalEl = document.getElementById('rouletteResultModal');
        let bsResultModal = null;
        if(resultModalEl) {
            bsResultModal = new bootstrap.Modal(resultModalEl);
        }
        
        let isSpinning = false;
        let currentRotation = 0;
        
        // Setup simple persistent rotation for immediate visual confirmation on land layout structures
        let idleAngle = 0;
        let idleTimer = setInterval(() => {
            if(!isSpinning) {
                idleAngle += 0.15; // Smooth slow continuous rolling pace
                wheel.style.transform = `rotate(${idleAngle}deg)`;
            }
        }, 30);

        /**
         * Core execution module driving full math calculations and transition triggers
         */
        function executeRouletteTurn() {
            if (isSpinning || segments.length === 0) return;
            
            isSpinning = true;
            spinBtn.disabled = true;
            spinBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Consulting Fate...`;
            
            // 1. Calculate a completely random winner sector element index
            const totalSegments = segments.length;
            const winnerIndex = Math.floor(Math.random() * totalSegments);
            const winnerSegment = segments[winnerIndex];
            
            // 2. Extract intrinsic rotation values from layout definitions
            const segmentAngle = parseFloat(winnerSegment.getAttribute('data-angle'));
            
            // 3. Compute target parameters factoring in multiple full rotations (5-7 spins) to handle friction animations
            const fullSpinsCount = 5 + Math.floor(Math.random() * 3); 
            const targetedAdditionalDegrees = fullSpinsCount * 360;
            
            // Align selector perfectly underneath indicator point
            const baseTargetRotation = (360 - segmentAngle) % 360;
            const finalTargetAngle = currentRotation + targetedAdditionalDegrees + (baseTargetRotation - (currentRotation % 360));
            currentRotation = finalTargetAngle;
            
            // Force inline transition properties to process full rotation sequences instantly
            wheel.style.transition = "transform 6s cubic-bezier(0.15, 0.95, 0.3, 1)";
            wheel.style.transform = `rotate(${finalTargetAngle}deg)`;
            
            // 4. Await programmatic completion trigger hooks before executing overlay popup panels
            setTimeout(() => {
                const bookId = winnerSegment.getAttribute('data-book-id');
                const bookTitle = winnerSegment.getAttribute('data-title');
                const bookAuthor = winnerSegment.getAttribute('data-author');
                const bookCover = winnerSegment.getAttribute('data-cover');
                const bookUrl = winnerSegment.getAttribute('data-url');
                
                document.getElementById('modalBookCover').src = bookCover;
                document.getElementById('modalBookTitle').textContent = bookTitle;
                document.getElementById('modalBookAuthor').textContent = bookAuthor;
                document.getElementById('modalBookLink').href = bookUrl;
                
                if(bsResultModal) {
                    bsResultModal.show();
                }
                
                spinBtn.disabled = false;
                spinBtn.innerHTML = `<i class="fas fa-sync-alt me-2"></i>Spin the Wheel`;
                
                idleAngle = finalTargetAngle;
                isSpinning = false;
            }, 6100); 
        }

        // Direct standard button click mapping
        if (spinBtn) {
            spinBtn.addEventListener('click', executeRouletteTurn);
        }

        // Intercept Modal "Spin Again" actions, clean frames, dismiss overlays, and pipeline next execution
        if (modalSpinBtn) {
            modalSpinBtn.addEventListener('click', function() {
                if (bsResultModal) {
                    bsResultModal.hide();
                    
                    // Allow the dismiss animation window to snap shut before firing momentum sequences
                    setTimeout(executeRouletteTurn, 400);
                }
            });
        }
    });
</script>
@endsection