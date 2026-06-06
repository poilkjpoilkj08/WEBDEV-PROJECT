@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-color: #ffffff; /* Overriding global master background image to pure clean white */
        min-height: 100vh;
        padding-top: 40px;
    }

    /* Fixed Header Logic Compatibility */
    nav.navbar {
        position: fixed !important;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }

    /* Table Component Alignment */
    .cart-table th {
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #4a5568;
    }

    /* Modern scannable card layout adjustments */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        background-color: #f8f9fa !important;
    }
    
    .min-width-md-300 {
        min-width: 300px;
    }

    /* --- CAROUSEL SLIDER ADAPTATIONS MATCHING HOME BLADE --- */
    .bh-slider-outer-wrapper {
        position: relative;
        overflow: hidden;
    }

    .bh-slider-track-corridor {
        display: flex !important;
        gap: 20px !important;
        overflow-x: auto !important;
        scroll-behavior: smooth !important;
        scrollbar-width: none !important; /* Firefox override */
        -ms-overflow-style: none !important; /* IE override */
        padding: 15px 5px !important;
    }

    .bh-slider-track-corridor::-webkit-scrollbar {
        display: none !important; /* Chrome/Safari override */
    }

    .bh-slider-item-node {
        flex: 0 0 240px !important;
        max-width: 240px !important;
    }

    .slider-nav-btn {
        width: 36px; 
        height: 36px;
        display: flex; 
        align-items: center; 
        justify-content: center;
        background: #ffffff; 
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .slider-nav-btn:hover {
        background: #f8f9fa;
        color: #c25e25;
    }

    /* Unified Recommendation Cards Custom Aesthetics */
    .recommendation-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef0f2 !important;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }

    /* Unified Muted Soft Orange Buttons global style helper */
    .btn-soft-orange {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    
    .btn-soft-orange:hover, .btn-soft-orange:focus {
        background-color: #a64f1e !important;
        border-color: #a64f1e !important;
        color: #ffffff !important;
    }

    .btn-outline-soft-orange {
        background-color: transparent !important;
        border: 2px solid #c25e25 !important;
        color: #c25e25 !important;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .btn-outline-soft-orange:hover, .btn-outline-soft-orange.active {
        background-color: #c25e25 !important;
        border-color: #c25e25 !important;
        color: #ffffff !important;
    }

    /* ===== INLINE RE-ENFORCED RESPONSIVE RULES FOR SEPARATION ===== */
    @media (min-width: 768px) {
        .bh-desktop-only-block { display: block !important; }
        .bh-mobile-only-block { display: none !important; }
    }
    
    @media (max-width: 767.98px) {
        .bh-desktop-only-block { display: none !important; }
        .bh-mobile-only-block { display: block !important; }
        
        .h3 { font-size: 1.5rem !important; }
        .btn { padding: 0.65rem 1rem !important; font-size: 0.95rem !important; min-height: 44px; }
        .form-control, .form-select { font-size: 16px !important; padding: 0.75rem !important; }
        .btn-block, .btn-soft-orange, .btn-outline-soft-orange { width: 100% !important; margin-bottom: 0.5rem !important; }
    }

    @media (max-width: 576px) {
        .container { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        h1, .h1, h2, .h2, h3, .h3 { font-size: 1.25rem !important; }
        .alert { padding: 0.75rem !important; font-size: 0.9rem !important; margin-bottom: 1rem !important; }
        .badge { padding: 0.5rem 0.75rem !important; font-size: 0.8rem !important; }
        body { overflow-x: hidden; }
    }
</style>

<div class="bg-white" style="position: relative; z-index: 4; padding-top: 40px;">
    <div class="container py-5">
        
        <!-- HEADER BLOCK: Side-by-Side Unified Row -->
        <div style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; width: 100% !important; margin-bottom: 3rem !important; gap: 12px !important;">
            <div style="min-width: 0 !important; flex-grow: 1 !important;">
                <h1 class="h3 mb-1 fw-bold text-dark text-truncate">
                    <i class="fas fa-shopping-basket me-2" style="color: #c25e25;"></i>Your Cart
                </h1>
                <p class="text-muted small mb-0 d-none d-sm-block">Review your selected items and select store locations before checking out</p>
                <p class="text-muted small mb-0 d-block d-sm-none">Review items and store stock options</p>
            </div>
            <div style="flex-shrink: 0 !important;">
                <a href="{{ route('books.listing') }}" class="btn btn-outline-soft-orange btn-sm fw-bold px-3 px-sm-4 rounded-pill shadow-sm" style="display: inline-flex !important; align-items: center !important; white-space: nowrap !important; font-size: clamp(0.75rem, 2.5vw, 0.85rem) !important;">
                    <i class="fas fa-store me-1.5"></i>Shop<span class="d-none d-sm-inline ms-1">Catalog</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($items->isEmpty())
            <div class="alert bg-light border text-center p-5 rounded-4" style="border-color: #eef0f2 !important;">
                <i class="fas fa-shopping-cart text-muted fa-3x mb-3 opacity-40" style="color: #c25e25 !important;"></i>
                <h4 class="h6 fw-bold text-dark mb-2">Your shopping cart is empty</h4>
                <p class="text-secondary small mb-4 mx-auto" style="max-width: 400px;">Looks like you haven't added any books yet. Explore our catalog dashboard to fill your hive!</p>
                <a href="{{ route('books.listing') }}" class="btn btn-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                    Browse Books
                </a>
            </div>
        @else
            <!-- 1. DESKTOP VIEWPORT LAYOUT DISPLAY: Table view locked via class selector -->
            <div class="table-responsive rounded-4 border overflow-hidden shadow-sm bg-white mb-4 bh-desktop-only-block" style="border-color: #eef0f2 !important; min-width: 100% !important;">
                <table class="table mb-0 align-middle cart-table" style="min-width: 800px !important;">
                    <thead class="bg-light text-dark">
                        <tr class="border-bottom" style="border-color: #eef0f2 !important;">
                            <th class="ps-4 py-3 fw-bold border-0">Book Details</th>
                            <th class="text-center py-3 fw-bold border-0">Store Location</th>
                            <th class="text-center py-3 fw-bold border-0">Price</th>
                            <th class="text-center py-3 fw-bold border-0">Quantity</th>
                            <th class="text-center py-3 fw-bold border-0">Subtotal</th>
                            <th class="pe-4 border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="hover-lift border-bottom" style="border-color: #eef0f2 !important;">
                            <td class="ps-4 py-4">
                                <div class="d-flex gap-3 align-items-center">
                                    @if($item['book']->cover_image_url || $item['book']->cover_image_src)
                                        <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}" width="70" class="rounded-3 border shadow-sm bg-white" style="height: 95px; object-fit: contain; padding: 4px;" />
                                    @else
                                        <div class="rounded-3 border bg-light text-muted d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 95px;">
                                            <i class="fas fa-book fa-lg opacity-30" style="color: #c25e25;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-1 fw-bold text-dark fs-6">{{ $item['book']->title }}</h5>
                                        <p class="mb-0 text-muted small"><i class="fas fa-feather-alt me-1 text-secondary"></i>by {{ $item['book']->author?->name ?? 'Unknown Author' }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="text-center">
                                <select name="store_ids[{{ $item['book']->id }}]" class="form-select form-select-sm store-selector rounded-3 fw-semibold border text-secondary shadow-sm" data-book-id="{{ $item['book']->id }}" style="width: 190px; margin: 0 auto; border-color: #ced4da !important;">
                                    <option value="">Select Store Location</option>
                                    @foreach($stores as $store)
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $store->id)->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                            $isSelected = $item['store_id'] == $store->id;
                                        @endphp
                                        <option value="{{ $store->id }}" {{ $isSelected ? 'selected' : '' }} data-stock="{{ $storeStock }}">
                                            {{ $store->city }} (Stock: {{ $storeStock }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="store-status-msg d-block mt-1 animate slideIn" style="display: {{ $item['store_id'] ? 'none' : 'block' }};">
                                    <span class="text-danger fw-medium" style="font-size: 0.75rem;"><i class="fas fa-exclamation-triangle me-1"></i>Required</span>
                                </small>
                            </td>

                            <td class="text-center text-dark fw-semibold">Rp {{ number_format($item['book']->price, 0, ',', '.') }}</td>
                            
                            <td class="text-center">
                                <div class="d-inline-block">
                                    <input type="number" name="quantities[{{ $item['book']->id }}]" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center quantity-input fw-bold border rounded-3 shadow-sm mb-1" style="width: 85px; margin: 0 auto; border-color: #ced4da !important;" data-book-id="{{ $item['book']->id }}" />
                                    @if($item['store_id'])
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $item['store_id'])->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                        @endphp
                                        <small class="text-muted text-uppercase font-monospace" style="font-size: 0.65rem;">Max: {{ $storeStock }}</small>
                                    @else
                                        <small class="text-warning text-uppercase font-monospace" style="font-size: 0.65rem;">Select Store</small>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center text-dark fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-sm btn-light text-danger border btn-remove-item rounded-3 px-3 fw-medium shadow-sm" data-book-id="{{ $item['book']->id }}">
                                    <i class="fas fa-trash-alt me-1"></i>Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 2. MOBILE VIEWPORT DECK LAYOUT DISPLAY: Cards isolated cleanly inside class selector -->
            <div class="mb-4 bh-mobile-only-block">
                @foreach($items as $item)
                <div class="card border shadow-sm rounded-4 mb-3" style="border-color: #eef0f2 !important; background: #ffffff;">
                    <div class="card-body p-3">
                        
                        <!-- Top Row Content Block Meta Alignment -->
                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if($item['book']->cover_image_url || $item['book']->cover_image_src)
                                <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}" width="65" class="rounded-3 border shadow-sm bg-white" style="height: 88px; object-fit: contain; padding: 3px; flex-shrink: 0;" />
                            @else
                                <div class="rounded-3 border bg-light text-muted d-flex align-items-center justify-content-center shadow-sm" style="width: 65px; height: 88px; flex-shrink: 0;">
                                    <i class="fas fa-book opacity-30" style="color: #c25e25;"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1" style="min-width: 0;">
                                <h5 class="mb-1 fw-bold text-dark fs-6 text-truncate" title="{{ $item['book']->title }}">{{ $item['book']->title }}</h5>
                                <p class="mb-1 text-muted small text-truncate"><i class="fas fa-feather-alt me-1 text-secondary"></i>by {{ $item['book']->author?->name ?? 'Unknown Author' }}</p>
                                <div class="text-dark fw-semibold small">Unit Price: Rp {{ number_format($item['book']->price, 0, ',', '.') }}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light text-danger border btn-remove-item rounded-3 p-2 lh-1 shadow-sm" data-book-id="{{ $item['book']->id }}" title="Remove item">
                                <i class="fas fa-trash-alt m-0"></i>
                            </button>
                        </div>
                        
                        <hr class="my-2 opacity-10">

                        <!-- Interactive Functional Settings Row Container -->
                        <div class="row g-2 align-items-end text-start">
                            <div class="col-7">
                                <label class="text-muted d-block font-monospace mb-1" style="font-size: 0.65rem; text-transform: uppercase;">Store Location Required</label>
                                <select name="store_ids[{{ $item['book']->id }}]" class="form-select form-select-sm store-selector rounded-3 fw-semibold border text-secondary shadow-sm" data-book-id="{{ $item['book']->id }}" style="border-color: #ced4da !important;">
                                    <option value="">Select Store Location</option>
                                    @foreach($stores as $store)
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $store->id)->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                            $isSelected = $item['store_id'] == $store->id;
                                        @endphp
                                        <option value="{{ $store->id }}" {{ $isSelected ? 'selected' : '' }} data-stock="{{ $storeStock }}">
                                            {{ $store->city }} (Stock: {{ $storeStock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-5 text-end">
                                <label class="text-muted d-block font-monospace mb-1 text-start ps-2" style="font-size: 0.65rem; text-transform: uppercase;">Qty</label>
                                <div class="d-inline-block w-100">
                                    <input type="number" name="quantities[{{ $item['book']->id }}]" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center quantity-input fw-bold border rounded-3 shadow-sm mb-1" style="border-color: #ced4da !important;" data-book-id="{{ $item['book']->id }}" />
                                    @if($item['store_id'])
                                        @php
                                            $storeBook = $item['book']->storeLocations()->where('store_location_id', $item['store_id'])->first();
                                            $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                        @endphp
                                        <small class="text-muted text-uppercase font-monospace d-block text-center" style="font-size: 0.62rem;">Max: {{ $storeStock }}</small>
                                    @else
                                        <small class="text-warning text-uppercase font-monospace d-block text-center" style="font-size: 0.62rem;">Select Store</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Status Alert + Live-Updated row Subtotal tracker -->
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-light">
                            <div>
                                <small class="store-status-msg animate slideIn m-0" style="display: {{ $item['store_id'] ? 'none' : 'block' }};">
                                    <span class="text-danger fw-medium" style="font-size: 0.72rem;"><i class="fas fa-exclamation-triangle me-1"></i>Location Required</span>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small font-monospace me-1">Subtotal:</span>
                                <span class="text-dark fw-bold" style="font-size: 0.95rem;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total Bar Module -->
            <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-3 mt-4 mb-5">
                <div class="card bg-white border shadow-sm rounded-4 p-4 text-end min-width-md-300" style="border-color: #eef0f2 !important; width: 100%; max-width: 360px;">
                    <span class="text-muted small text-uppercase fw-bold tracking-wider mb-1 d-block" style="font-size: 0.7rem;">Grand Total</span>
                    <h2 class="mb-0 fw-bold text-dark" style="font-size: 1.75rem;">Rp {{ number_format($total, 0, ',', '.') }}</h2>
                    <a href="{{ route('checkout.show') }}" class="btn btn-soft-orange w-100 fw-bold rounded-3 shadow-sm mt-3 py-2 text-uppercase" style="font-size: 0.85rem;">
                        <i class="fas fa-shopping-bag me-2"></i>Proceed to Checkout
                    </a>
                </div>
            </div>

            @php
                if (!isset($recommended_books) || $recommended_books->isEmpty()) {
                    $cartBookIds = collect($items)->pluck('book.id')->all();
                    $recommended_books = \App\Models\Book::where('status', 'available')
                        ->whereNotIn('id', $cartBookIds)
                        ->inRandomOrder()
                        ->take(4) 
                        ->get();
                }
            @endphp

            @if($recommended_books->count() > 0)
            <div class="mt-5 pt-4 border-top text-start" style="border-color: #eef0f2 !important;">
                <!-- Slider Header Rows Split Configuration Tracking -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="h5 mb-1 fw-bold text-dark"><i class="fas fa-magic me-2 text-warning"></i>Add to Your Hive</h3>
                        <p class="text-muted small mb-0">Recommended reads tailored specifically for your collection</p>
                    </div>
                    <!-- Conditional rendering rules for button toggles if item length supports it -->
                    @if($recommended_books->count() > 1)
                    <div class="d-flex gap-2 align-items-center">
                        <button class="slider-nav-btn" id="recPrevBtn">
                            <i class="fas fa-chevron-left small"></i>
                        </button>
                        <button class="slider-nav-btn" id="recNextBtn">
                            <i class="fas fa-chevron-right small"></i>
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Slider Sliding Core track Component corridor mapping -->
                <div class="bh-slider-outer-wrapper">
                    <div class="bh-slider-track-corridor" id="recBooksSlider">
                        @foreach($recommended_books as $recBook)
                        <div class="bh-slider-item-node">
                            <div class="card h-100 border-0 overflow-hidden recommendation-card bg-white rounded-4 shadow-sm m-0">
                                <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; padding: 12px; position: relative;">
                                    @if($recBook->cover_image_url || $recBook->cover_image_src)
                                        <img src="{{ $recBook->cover_image_src }}" class="img-fluid rounded-2" alt="{{ $recBook->title }}" style="max-height: 100%; object-fit: contain;" />
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-book fa-2x mb-1 opacity-20" style="color: #c25e25;"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column p-3 pt-2">
                                    <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="font-size: 0.85rem;" title="{{ $recBook->title }}">{{ $recBook->title }}</h4>
                                    <p class="text-muted mb-3 text-truncate" style="font-size: 0.75rem;">by {{ $recBook->author?->name ?? 'Unknown Author' }}</p>
                                    
                                    <div class="mt-auto">
                                        <div class="fw-bold text-dark mb-2 small">Rp {{ number_format($recBook->price, 0, ',', '.') }}</div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('books.show', $recBook->id) }}" class="btn btn-light btn-sm text-dark border flex-grow-1 px-1 fw-medium rounded-3" style="font-size: 0.7rem;">
                                                <i class="fas fa-info-circle me-1 opacity-60"></i>Details
                                            </a>
                                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline flex-grow-1 m-0 p-0">
                                                @csrf
                                                <input type="hidden" name="book_id" value="{{ $recBook->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-soft-orange btn-sm w-100 px-1 fw-bold rounded-3" style="font-size: 0.7rem;">
                                                    <i class="fas fa-plus me-1"></i>Add
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <form id="removeItemForm" action="{{ route('cart.remove') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" id="removeBookId" name="book_id">
            </form>

            <form id="updateCartForm" action="{{ route('cart.update') }}" method="POST" style="display: none;">
                @csrf
                <div id="quantitiesContainer"></div>
            </form>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // DETECT ACTIVE VIEWPORT INPUT NODES EXCLUSIVELY: Resolves double render data cross-talk conflicts
                function getStoreStockForBook(bookId) {
                    const isMobileLayout = window.innerWidth < 768;
                    const contextSelector = isMobileLayout ? '.bh-mobile-only-block' : '.bh-desktop-only-block';
                    
                    const storeSelector = document.querySelector(`${contextSelector} .store-selector[data-book-id="${bookId}"]`);
                    if (!storeSelector || !storeSelector.value) return 0;
                    const selectedOption = storeSelector.options[storeSelector.selectedIndex];
                    return parseInt(selectedOption.getAttribute('data-stock')) || 0;
                }
                
                // Handle store selector changes
                document.querySelectorAll('.store-selector').forEach(selector => {
                    selector.addEventListener('change', function() {
                        const bookId = this.getAttribute('data-book-id');
                        
                        // Select ALL matching store selector inputs across views to sync values instantly
                        document.querySelectorAll(`.store-selector[data-book-id="${bookId}"]`).forEach(el => {
                            el.value = this.value;
                        });

                        const storeStock = getStoreStockForBook(bookId);
                        
                        // Sync validation display tags uniformly across both structural node systems
                        document.querySelectorAll(`.store-selector[data-book-id="${bookId}"]`).forEach(el => {
                            const statusMsg = el.closest('div, td').querySelector('.store-status-msg');
                            if (statusMsg) {
                                statusMsg.style.display = this.value ? 'none' : 'block';
                            }
                        });
                        
                        document.querySelectorAll(`.quantity-input[data-book-id="${bookId}"]`).forEach(qIn => {
                            const subtextEl = qIn.closest('div')?.querySelector('small');
                            if (storeStock <= 0 && elValueChecked(this.value)) {
                                qIn.value = 0;
                                qIn.disabled = true;
                                qIn.max = 0;
                                if (subtextEl) {
                                    subtextEl.className = "text-danger text-uppercase font-monospace d-block text-center";
                                    subtextEl.textContent = "Max: 0";
                                }
                            } else {
                                qIn.disabled = false;
                                qIn.max = storeStock || 1;
                                if (subtextEl) {
                                    if (storeStock > 0) {
                                        subtextEl.className = "text-muted text-uppercase font-monospace d-block text-center";
                                        subtextEl.textContent = "Max: " + storeStock;
                                    } else {
                                        subtextEl.className = "text-warning text-uppercase font-monospace d-block text-center";
                                        subtextEl.textContent = "Select Store";
                                    }
                                }
                                if (storeStock > 0 && SkinnerInt(qIn.value) > storeStock) {
                                    qIn.value = storeStock;
                                } else if (SkinnerInt(qIn.value) < 1) {
                                    qIn.value = 1;
                                }
                            }
                        });
                        
                        function elValueChecked(val) { return val !== ''; }
                        
                        if (storeStock <= 0 && this.value) {
                            showToast('Selected store has no stock for this book', 'warning', 3000);
                        } else if (this.value) {
                            const anyQtyInput = document.querySelector(`.quantity-input[data-book-id="${bookId}"]`);
                            if (anyQtyInput && SkinnerInt(anyQtyInput.value) > storeStock) {
                                showToast(`Quantity adjusted to max available: ${storeStock}`, 'info', 2000);
                            }
                        }
                        
                        function SkinnerInt(val) { return parseInt(val) || 0; }
                        updateCheckoutButton();
                    });
                });
                
                // Validate quantity against store stock on quantity change
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', function() {
                        const bookId = this.getAttribute('data-book-id');
                        const storeStock = getStoreStockForBook(bookId);
                        const currentQty = parseInt(this.value) || 0;
                        let targetVal = currentQty;
                        
                        if (storeStock > 0 && currentQty > storeStock) {
                            targetVal = storeStock;
                            showToast(`Cannot exceed store stock of ${storeStock}. Quantity adjusted.`, 'warning', 2000);
                        } else if (currentQty < 1) {
                            targetVal = 1;
                            showToast('Quantity must be at least 1', 'warning', 2000);
                        }
                        
                        document.querySelectorAll(`.quantity-input[data-book-id="${bookId}"]`).forEach(qIn => {
                            qIn.value = targetVal;
                        });
                    });
                    
                    input.addEventListener('blur', function() {
                        const currentQty = parseInt(this.value) || 0;
                        if (currentQty < 1) {
                            document.querySelectorAll(`.quantity-input[data-book-id="${this.getAttribute('data-book-id')}"]`).forEach(qIn => {
                                qIn.value = 1;
                            });
                        }
                    });
                });

                // Handle remove buttons
                document.querySelectorAll('.btn-remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        if (confirm('Remove this book from your shopping cart?')) {
                            const bookId = this.getAttribute('data-book-id');
                            document.getElementById('removeBookId').value = bookId;
                            document.getElementById('removeItemForm').submit();
                        }
                    });
                });

                // Prevent checkout if stores are not selected or stock is 0
                function updateCheckoutButton() {
                    let isMobileViewActive = window.innerWidth < 768;
                    const selectorQuery = isMobileViewActive ? '.bh-mobile-only-block .store-selector' : '.bh-desktop-only-block .store-selector';
                    
                    const allStoresSelected = Array.from(document.querySelectorAll(selectorQuery)).every(selector => {
                        return selector.value !== '';
                    });
                    
                    let hasZeroStock = false;
                    document.querySelectorAll(selectorQuery).forEach(selector => {
                        if (selector.value) {
                            const selectedOption = selector.options[selector.selectedIndex];
                            const storeStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                            if (storeStock <= 0) {
                                hasZeroStock = true;
                            }
                        } else {
                            hasZeroStock = true; 
                        }
                    });
                    
                    const checkoutBtn = document.querySelector('a[href*="checkout"]');
                    if (checkoutBtn) {
                        if (!allStoresSelected || hasZeroStock) {
                            checkoutBtn.classList.add('disabled');
                            checkoutBtn.style.pointerEvents = 'none';
                            checkoutBtn.style.opacity = '0.6';
                        } else {
                            checkoutBtn.classList.remove('disabled');
                            checkoutBtn.style.pointerEvents = 'auto';
                            checkoutBtn.style.opacity = '1';
                        }
                    }
                }
                
                updateCheckoutButton();

                // AJAX quantity and store updates without page reload
                document.querySelectorAll('.quantity-input, .store-selector').forEach(input => {
                    input.addEventListener('change', async function() {
                        const isMobileView = window.innerWidth < 768;
                        const selectorQuery = isMobileView ? '.bh-mobile-only-block .store-selector' : '.bh-desktop-only-block .store-selector';
                        
                        const allStoresSelected = Array.from(document.querySelectorAll(selectorQuery)).every(selector => {
                            return selector.value !== '';
                        });
                        
                        if (!allStoresSelected) {
                            return;
                        }
                        
                        try {
                            const formData = new FormData();
                            
                            // Always compile backend payloads referencing desktop data tokens uniformly
                            document.querySelectorAll('.bh-desktop-only-block .quantity-input').forEach(qInput => {
                                const bookId = qInput.getAttribute('data-book-id');
                                formData.append('quantities[' + bookId + ']', SkinnerInt(qInput.value) || 0);
                            });
                            
                            document.querySelectorAll('.bh-desktop-only-block .store-selector').forEach(selector => {
                                const bookId = selector.getAttribute('data-book-id');
                                formData.append('store_ids[' + bookId + ']', selector.value || '');
                            });
                            
                            function SkinnerInt(val) { return parseInt(val) || 0; }

                            const response = await fetch('{{ route("cart.update") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                }
                            });
                            
                            if (response.ok) {
                                let newGrandTotal = 0;
                                
                                // Recalculate values smoothly across desktop table columns
                                document.querySelectorAll('.bh-desktop-only-block tbody tr').forEach(row => {
                                    const priceText = row.querySelector('td:nth-child(3)').textContent;
                                    const price = parseInt(priceText.replace(/\D/g, ''));
                                    const rowQuantityInput = row.querySelector('.quantity-input');
                                    const rowQuantity = parseInt(rowQuantityInput.value) || 0;
                                    const rowSubtotal = price * rowQuantity;
                                    row.querySelector('td:nth-child(5)').textContent = 'Rp ' + rowSubtotal.toLocaleString('id-ID');
                                    newGrandTotal += rowSubtotal;
                                });
                                
                                // Sync values to Mobile Cards Row Views
                                document.querySelectorAll('.bh-mobile-only-block .card').forEach(card => {
                                    const qIn = card.querySelector('.quantity-input');
                                    const bookId = qIn.getAttribute('data-book-id');
                                    
                                    const matchRow = document.querySelector(`.bh-desktop-only-block .quantity-input[data-book-id="${bookId}"]`)?.closest('tr');
                                    if (matchRow) {
                                        const subTotalText = matchRow.querySelector('td:nth-child(5)').textContent;
                                        const mobileSubTotalSpan = card.querySelector('.border-top .fw-bold');
                                        if (mobileSubTotalSpan) {
                                            mobileSubTotalSpan.textContent = subTotalText;
                                        }
                                    }
                                });
                                
                                const totalDisplay = document.querySelector('.tracking-wider').closest('div').querySelector('h2');
                                if (totalDisplay) {
                                    totalDisplay.textContent = 'Rp ' + newGrandTotal.toLocaleString('id-ID');
                                }
                                
                                updateCheckoutButton();
                                showToast('Cart updated successfully', 'success', 2000);
                            } else {
                                showToast('Error updating cart data configurations', 'error', 3000);
                            }
                        } catch (error) {
                            console.error('Error updating cart:', error);
                            showToast('Error updating cart parameters', 'error', 3000);
                        }
                    });
                });

                // --- Recommendations Slider Navigation Engine Initialization ---
                const recBooksSlider = document.getElementById('recBooksSlider');
                const recNextBtn = document.getElementById('recNextBtn');
                const recPrevBtn = document.getElementById('recPrevBtn');

                if (recBooksSlider && recNextBtn && recPrevBtn) {
                    recNextBtn.onclick = () => recBooksSlider.scrollBy({ left: 260, behavior: 'smooth' });
                    recPrevBtn.onclick = () => recBooksSlider.scrollBy({ left: -260, behavior: 'smooth' });
                }
            });
            </script>
        @endif
    </div>
</div>
@endsection