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

    /* Book Cover Box Frames */
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

    /* ==========================================================================
       ROULETTE TARGETED RESPONSIVE LAYOUT CONSTRAINTS
       ========================================================================== */
    @media (max-width: 768px) {
        .roulette-arena {
            height: 480px; /* Scaled down frame boundaries cleanly */
        }

        /* Scaled down the entire 1400px structural element via vectors to prevent layout clipping */
        .wheel-deck-pivot {
            transform-origin: center center;
            scale: 0.75;
            bottom: -820px;
        }

        .indicator-arrow-pin {
            bottom: 310px;
            font-size: 2.5rem;
        }

        .btn {
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
        }

        .glass-header-box {
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 0.95rem;
        }

        .h2 { font-size: 1.75rem; }
        .h3 { font-size: 1.5rem; }
        .modal-dialog { max-width: 90%; }
    }

    @media (max-width: 576px) {
        body {
            padding-top: 70px;
        }

        .container {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .roulette-arena {
            height: 360px; /* Micro dimension parameter limit block */
        }

        /* Aggressive scaling optimization to keep exactly half the circle crisp and functional */
        .wheel-deck-pivot {
            scale: 0.52;
            bottom: -720px;
        }

        /* Scaled the card images within segments down slightly for extreme mobile viewports */
        .segment-image-wrap {
            width: 105px;
            height: 150px;
        }

        .wheel-book-segment {
            padding-top: 90px;
        }

        .indicator-arrow-pin {
            bottom: 215px;
            font-size: 2.2rem;
        }

        .h2, h2 { font-size: 1.25rem; }
        .h3, h3 { font-size: 1.1rem; }

        .btn {
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
            min-height: 44px;
        }

        .btn-lg {
            padding: 0.65rem 1rem;
        }

        .glass-header-box {
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
        }

        .dismiss-subtext-label { font-size: 0.65rem; }
        .modal-body img { max-height: 220px; }
        body { overflow-x: hidden; }

        /* --- EXCLUSIVE MOBILE OVERRIDES FOR SLIM SMOOTH RESULTS POPUP --- */
        .custom-narrow-modal {
            max-width: 315px !important; /* Slimmer card width profile */
        }
        
        .modal-cover-preview {
            height: 210px; /* Scaled cleanly inside mobile cards */
        }

        .modal.fade .modal-dialog {
            transform: scale(0.92) translateY(8px);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
    }
</style>

@php
    if(!isset($books) || $books->isEmpty()){
        $books = \App\Models\Book::where('status', 'available')->take(12)->get();
    }
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
    <div class="modal-dialog modal-dialog-centered custom-narrow-modal">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="modal-body text-center px-4 pb-4 pt-5">
                <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill mb-3 font-monospace text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.05rem;">
                    <i class="fas fa-sparkles text-warning me-1"></i>Fate Has Chosen
                </span>
                
                <div class="mb-3.5">
                    <img id="modalBookCover" src="" class="modal-cover-preview img-fluid" alt="Selected Cover Title" style="max-height: 200px; object-fit: cover; border-radius: 0.5rem;">
                </div>
                
                <h3 class="h6 fw-bold text-dark mb-1 lh-base text-truncate px-1" id="modalBookTitle">Dune</h3>
                <p class="text-muted small mb-4">by <span class="fw-semibold text-secondary" id="modalBookAuthor">Author Frame</span></p>
                
                <div class="d-flex flex-column gap-2 mb-3">
                    <a id="modalBookLink" href="#" class="btn btn-soft-orange btn-sm fw-bold py-2 rounded-pill text-uppercase shadow-sm w-100" style="font-size: 0.75rem; letter-spacing: 0.02em;">
                        <i class="fas fa-book-open me-2"></i>View Details
                    </a>
                    <button type="button" id="modalSpinAgainBtn" class="btn btn-light btn-sm fw-bold text-secondary border rounded-pill py-2 w-100" style="font-size: 0.75rem; background-color: #fafafa;">
                        <i class="fas fa-redo me-1.5" style="font-size: 0.85em;"></i>Spin Again
                    </button>
                </div>

                <div class="text-center mt-3.5">
                    <span class="dismiss-subtext-label text-muted font-monospace small"><i class="fas fa-info-circle me-1"></i>Click anywhere outside to dismiss</span>
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
        
        const resultModalEl = document.getElementById('rouletteResultModal');
        let bsResultModal = null;
        if(resultModalEl) {
            bsResultModal = new bootstrap.Modal(resultModalEl);
        }
        
        let isSpinning = false;
        let currentRotation = 0;
        
        let idleAngle = 0;
        let idleTimer = setInterval(() => {
            if(!isSpinning) {
                idleAngle += 0.15; 
                // Enhanced execution to support composite inline string scale operations without wiping responsive adjustments
                const isMobile = window.innerWidth < 768;
                const mobileScaleFactor = window.innerWidth < 576 ? 0.52 : 0.75;
                
                if (isMobile) {
                    wheel.style.transform = `rotate(${idleAngle}deg) scale(${mobileScaleFactor})`;
                } else {
                    wheel.style.transform = `rotate(${idleAngle}deg)`;
                }
            }
        }, 30);

        function executeRouletteTurn() {
            if (isSpinning || segments.length === 0) return;
            
            isSpinning = true;
            spinBtn.disabled = true;
            spinBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Consulting Fate...`;
            
            const totalSegments = segments.length;
            const winnerIndex = Math.floor(Math.random() * totalSegments);
            const winnerSegment = segments[winnerIndex];
            
            const segmentAngle = parseFloat(winnerSegment.getAttribute('data-angle'));
            const fullSpinsCount = 5 + Math.floor(Math.random() * 3); 
            const targetedAdditionalDegrees = fullSpinsCount * 360;
            
            const baseTargetRotation = (360 - segmentAngle) % 360;
            const finalTargetAngle = currentRotation + targetedAdditionalDegrees + (baseTargetRotation - (currentRotation % 360));
            currentRotation = finalTargetAngle;
            
            wheel.style.transition = "transform 6s cubic-bezier(0.15, 0.95, 0.3, 1)";
            
            // Re-inject dynamic scale multipliers synchronously during calculations to prevent resizing pops mid-spin
            const isMobile = window.innerWidth < 768;
            const mobileScaleFactor = window.innerWidth < 576 ? 0.52 : 0.75;
            
            if (isMobile) {
                wheel.style.transform = `rotate(${finalTargetAngle}deg) scale(${mobileScaleFactor})`;
            } else {
                wheel.style.transform = `rotate(${finalTargetAngle}deg)`;
            }
            
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

        if (spinBtn) {
            spinBtn.addEventListener('click', executeRouletteTurn);
        }

        if (modalSpinBtn) {
            modalSpinBtn.addEventListener('click', function() {
                if (bsResultModal) {
                    bsResultModal.hide();
                    setTimeout(executeRouletteTurn, 400);
                }
            });
        }
    });
</script>
@endsection