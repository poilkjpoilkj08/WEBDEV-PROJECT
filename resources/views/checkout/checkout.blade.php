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

    /* Modern scannable card layout adjustments */
    .checkout-card {
        border: 1px solid #eef0f2 !important;
        background-color: #ffffff;
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        background-color: #f8f9fa !important;
    }
    
    .max-width-fit {
        width: fit-content;
    }
    
    /* Store Selection Card Styling */
    .item-store-select {
        border-color: #ced4da !important;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .item-store-select:focus {
        border-color: #c25e25 !important;
        box-shadow: 0 0 0 0.2rem rgba(194, 94, 37, 0.15) !important;
    }
    
    /* Shipping Option Styling Custom Sync */
    .shipping-option {
        transition: all 0.25s ease;
        background: #ffffff;
        border: 1px solid #eef0f2 !important;
        border-left: 4px solid #ced4da !important;
    }
    
    .shipping-option:hover {
        background: #f8f9fa;
        border-left-color: #a64f1e !important;
    }
    
    input[type="radio"].shipping-radio:checked ~ .shipping-option,
    .shipping-option:has(input[type="radio"].shipping-radio:checked) {
        background: #fdf6f0 !important;
        border-color: #fbd3bc !important;
        border-left: 4px solid #c25e25 !important;
    }

    input[type="radio"].shipping-radio:checked + div .fw-bold {
        color: #c25e25 !important;
    }
    
    /* Form Validation Highlighting */
    .form-control.is-invalid,
    .form-select.is-invalid,
    .field-error-highlight {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
    }
    
    /* Order Summary Prominence Layouts */
    #shippingDisplay {
        font-size: 1.1rem !important;
        font-weight: bold !important;
        color: #c25e25 !important;
        display: block !important;
    }
    
    #shippingRate {
        display: block !important;
        color: #666 !important;
        font-size: 0.9rem !important;
        margin-top: 4px;
    }
    
    /* Map Modal Styling - Ensure map is visible and responsive */
    #mapPickerModal {
        z-index: 9999 !important;
    }
    
    #mapPickerModal .modal-dialog {
        z-index: 9999 !important;
    }
    
    #mapPickerModal .modal-content {
        position: relative;
        z-index: 10000;
        background: white;
    }
    
    #mapPickerContainer {
        position: relative;
        width: 100% !important;
        height: 480px !important;
        background: white !important;
        z-index: 1052;
        overflow: visible !important;
        display: block !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
    
    #mapElement {
        width: 100% !important;
        height: 100% !important;
        z-index: 1052 !important;
        background: white !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        display: block !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
    
    #mapElement > * {
        pointer-events: auto !important;
    }
    
    #mapElement img {
        visibility: visible !important;
        pointer-events: auto !important;
    }
    
    #mapSearchBox {
        pointer-events: auto !important;
    }
    
    #mapSearchInput {
        pointer-events: auto !important;
    }
    
    /* Ensure modal backdrop is never visible */
    .modal-backdrop {
        display: none !important;
        opacity: 0 !important;
        z-index: -9999 !important;
    }
    
    .modal-backdrop.show {
        display: none !important;
        opacity: 0 !important;
        z-index: -9999 !important;
    }
    
    .modal-backdrop.fade {
        display: none !important;
        opacity: 0 !important;
    }
    
    /* Prevent any overlay effect */
    body.modal-open {
        overflow: auto !important;
    }
    
    /* Map modal should be hidden and not blocking by default */
    #mapPickerModal {
        pointer-events: none !important;
        z-index: 1050 !important;
        position: fixed !important;
        display: none !important;
    }
    
    /* When shown, make modal overlay everything including navbar */
    #mapPickerModal.show {
        pointer-events: auto !important;
        z-index: 9999 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(0, 0, 0, 0.7) !important;
        overflow-y: auto !important;
    }
    
    #mapPickerModal .modal-dialog {
        pointer-events: auto !important;
        z-index: 10000 !important;
        position: relative !important;
        width: 95vw !important;
        max-width: 1200px !important;
        height: 85vh !important;
        max-height: 850px !important;
        margin: auto !important;
        top: auto !important;
        left: auto !important;
    }
    
    #mapPickerModal .modal-content {
        pointer-events: auto !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    #mapPickerModal .modal-body {
        pointer-events: auto !important;
        flex: 1 !important;
        overflow: hidden !important;
        padding: 0 !important;
    }
    
    #mapPickerContainer {
        pointer-events: auto !important;
        width: 100% !important;
        height: 100% !important;
        position: relative !important;
    }
    
    #mapElement {
        pointer-events: auto !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 1052 !important;
        background: white !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        display: block !important;
        visibility: visible !important;
    }
    
    #mapElement > * {
        pointer-events: auto !important;
    }
    
    #mapSearchBox {
        pointer-events: auto !important;
    }
    
    #mapSearchInput {
        pointer-events: auto !important;
    }
    
    .map-autocomplete-results {
        position: absolute !important;
        top: 50px !important;
        left: 15px !important;
        width: 320px !important;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1055 !important;
        display: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        pointer-events: auto !important;
    }
    
    .map-autocomplete-results.show {
        display: block !important;
    }
    
    .map-result-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 0.85rem;
        pointer-events: auto !important;
    }
    
    .map-result-item:hover {
        background-color: #f5f5f5;
    }
    }

    #grandTotalDisplay {
        color: #c25e25 !important;
        font-weight: 700 !important;
    }
    
    .error-message {
        display: none;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Location Search & Autocomplete Dropdowns */
    .location-suggestions,
    .street-suggestions,
    .map-autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #eef0f2;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        max-height: 260px;
        overflow-y: auto;
        z-index: 1050;
        display: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }
    
    .location-suggestions.show,
    .street-suggestions.show,
    .map-autocomplete-results.show {
        display: block;
    }
    
    .location-suggestion-item,
    .street-suggestion-item,
    .map-result-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    
    .location-suggestion-item:hover,
    .street-suggestion-item:hover,
    .map-result-item:hover {
        background-color: #f8f9fa;
    }

    .location-suggestion-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2d3748;
    }
    
    .location-suggestion-desc {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 1px;
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

    #mapSearchBox {
        background: white;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>

<div class="bg-white" style="position: relative; z-index: 4; padding-top: 40px;">
    <div class="container py-5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Checkout</h1>
                <p class="text-muted small mb-0">Review order details, pinpoint shipping data, and complete secure token execution payments.</p>
            </div>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-soft-orange btn-sm fw-bold px-4 rounded-pill shadow-sm">
                <i class="fas fa-shopping-cart me-2"></i>Back to Cart
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif

        <div class="row g-4 align-items-start">
            {{-- LEFT COLUMN: Orders Summary Array & Address Form Details --}}
            <div class="col-lg-7">

                {{-- Order Summary Block Layout Component --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 checkout-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                            <i class="fas fa-shopping-bag me-2" style="color: #c25e25;"></i>Order Summary & Inventory Selection
                            <span id="shippingCalculatedBadge" class="badge bg-success ms-2 rounded-pill fw-medium small" style="display: none; font-size: 0.7rem; padding: 0.4em 0.8em;">
                                <i class="fas fa-check-circle me-1"></i>Shipping Calculated
                            </span>
                        </h3>
                        
                        <div class="pe-1 mb-4" style="max-height: 500px; overflow-y: auto;">
                            @foreach($items as $item)
                            <div class="py-3 border-bottom cart-item" 
                                 data-book-id="{{ $item['book']->id }}"
                                 data-weight-grams="{{ (int)($item['book']->weight_grams ?? 300) }}"
                                 data-quantity="{{ $item['quantity'] }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $item['book']->cover_image_src }}" alt="{{ $item['book']->title }}"
                                             style="width:45px; height:60px; object-fit:contain; border-radius:4px; padding: 2px;" class="border shadow-sm bg-white">
                                        <div>
                                            <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="max-width: 280px;" title="{{ $item['book']->title }}">{{ $item['book']->title }}</h4>
                                            <small class="text-muted font-monospace bg-light px-2 py-0.5 rounded border" style="font-size: 0.7rem;">Qty: {{ $item['quantity'] }} × Rp {{ number_format($item['book']->price, 0, ',', '.') }} | {{ (int)($item['book']->weight_grams ?? 300) }}g</small>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-dark small">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </div>
                                
                                {{-- Store Selection configuration for matching inventory records --}}
                                <div class="ms-5 ps-2 mb-2">
                                    <label class="form-label fw-semibold text-dark small mb-1.5" style="font-size: 0.75rem;">
                                        Fulfillment Location
                                        <span class="item-store-required text-danger" style="display: @if(!$item['store_id']) inline @else none @endif;">*</span>
                                    </label>
                                    <select name="store_ids[{{ $item['book']->id }}]" form="paymentForm" 
                                            class="form-select form-select-sm rounded-3 item-store-select fw-medium text-secondary" 
                                            data-book-id="{{ $item['book']->id }}" 
                                            @if(!$item['store_id']) required @endif>
                                        <option value="">-- Select Store Location --</option>
                                        @foreach($stores as $store)
                                            @php
                                                $storeBook = $item['book']->storeLocations()->where('store_location_id', $store->id)->first();
                                                $storeStock = $storeBook ? $storeBook->pivot->stock : 0;
                                                $isSelected = $item['store_id'] == $store->id;
                                            @endphp
                                            <option value="{{ $store->id }}" 
                                                    {{ $isSelected ? 'selected' : '' }}
                                                    data-stock="{{ $storeStock }}"
                                                    data-lat="{{ $store->latitude }}"
                                                    data-lng="{{ $store->longitude }}">
                                                {{ $store->city }} (Stock: {{ $storeStock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="item-store-message d-block mt-1" 
                                           style="display: @if(!$item['store_id']) block @else none @endif; font-size: 0.7rem;">
                                        <span class="text-danger" style="display: @if(!$item['store_id']) inline @else none @endif;"><i class="fas fa-exclamation-triangle me-1"></i>Store selection required</span>
                                        <span class="text-success" style="display: @if($item['store_id']) inline @else none @endif;"><i class="fas fa-check me-1"></i>Store assigned successfully</span>
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Subtotal</span>
                            <span class="fw-semibold text-dark" id="subtotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Shipping Cost</span>
                            <div class="text-end">
                                <span id="shippingDisplay" class="d-block fw-bold text-dark" style="display: none; font-size: 1.1rem;">— awaiting details</span>
                                <span id="shippingRate" class="d-block text-muted small" style="display: none; margin-top: 4px;"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mb-2">
                            <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Payment Method</span>
                            <span class="text-muted small fw-semibold">Encrypted Midtrans Overlay</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-4 mt-3 border-top">
                            <span class="text-muted small text-uppercase fw-bold tracking-wider" style="font-size: 0.75rem;">Grand Total</span>
                            <span class="h3 fw-bold text-dark mb-0" id="grandTotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address Map Coordinates Card Layout --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden checkout-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                            <i class="fas fa-map-marker-alt me-2" style="color: #c25e25;"></i>Shipping Delivery Address
                        </h3>
                        
                        <form id="paymentForm">
                            @csrf
                            <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="shipping_latitude" id="shipping_latitude" value="">
                            <input type="hidden" name="shipping_longitude" id="shipping_longitude" value="">
                            <input type="hidden" name="shipping_province" id="shipping_province" value="">
                            <input type="hidden" name="shipping_city" id="shipping_city" value="">
                            <input type="hidden" name="shipping_district" id="shipping_district" value="">
                            <input type="hidden" name="shipping_street" id="shipping_street" value="">

                            <div class="row g-3">
                                {{-- Saved Profile Coordinates Verification --}}
                                @if($savedAddress)
                                <div class="col-12">
                                    <div class="alert bg-light border rounded-4 p-3 mb-0" style="border-color: #eef0f2 !important;">
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="radio" id="useSavedAddress" name="addressChoice" value="saved" checked>
                                            <label class="form-check-label ms-2" for="useSavedAddress">
                                                <strong class="text-dark">Use Saved Default Address</strong>
                                                <div class="small text-muted mt-1" style="font-size: 0.8rem; line-height: 1.4;">
                                                    {{ $savedAddress['street'] }}, {{ $savedAddress['district'] }}, {{ $savedAddress['city'] }}, {{ $savedAddress['province'] }} {{ $savedAddress['postal_code'] }}
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="enterNewAddress" name="addressChoice" value="new">
                                        <label class="form-check-label fw-bold text-dark small ms-2" for="enterNewAddress">
                                            Enter New Destination Address
                                        </label>
                                    </div>
                                </div>
                                <div id="newAddressSection" style="display: none; width: 100%;"></div>
                                @endif

                                <div class="row g-3 m-0 p-0" id="addressFormFields">
                                    {{-- Primary Form Fields --}}
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label fw-semibold text-dark small">Recipient Full Name *</label>
                                        <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}"
                                               required class="form-control rounded-3 text-dark border" style="border-color: #ced4da !important; font-size: 0.85rem;" placeholder="e.g. John Doe">
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label fw-semibold text-dark small">Contact Phone Number *</label>
                                        <input type="text" name="shipping_phone" required maxlength="50"
                                               class="form-control rounded-3 text-dark border" style="border-color: #ced4da !important; font-size: 0.85rem;" placeholder="e.g. +62 812 3456 7890">
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold text-dark small">Country *</label>
                                        <input type="text" value="Indonesia" readonly class="form-control rounded-3 text-muted bg-light border mb-1" style="font-size: 0.85rem;" disabled>
                                        <input type="hidden" name="shipping_country" value="Indonesia">
                                    </div>

                                    {{-- Hierarchical Input Auto-complete Box --}}
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold text-dark small">Location Lookup (Province / City / District) *</label>
                                        <div class="location-search-wrapper position-relative">
                                            <input type="text" id="location_search" placeholder="Type location destination here (e.g. Surabaya, Menteng)..." 
                                                   class="form-control rounded-3 text-dark border" style="border-color: #ced4da !important; font-size: 0.85rem;" autocomplete="off">
                                            <div id="locationSuggestions" class="location-suggestions shadow-sm"></div>
                                        </div>
                                    </div>

                                    {{-- Postal Mapping Input Frame --}}
                                    <div class="col-md-6 mt-3">
                                        <label class="form-label fw-semibold text-dark small">Postal Code *</label>
                                        <input type="text" id="shipping_postal_code_input" name="shipping_postal_code" required readonly
                                               class="form-control rounded-3 text-muted bg-light border" style="font-size: 0.85rem;" placeholder="Auto-populated from lookup">
                                    </div>

                                    {{-- Reverse Geocoded Pinpoint Coordinates --}}
                                    <div class="col-md-6 mt-3">
                                        <label class="form-label fw-semibold text-dark small d-block">Coordinate Pinpoint Mapping *</label>
                                        <div class="d-flex gap-2">
                                            <div class="flex-grow-1">
                                                <textarea id="location_display" readonly
                                                       class="form-control rounded-3 text-secondary border" 
                                                       placeholder="Select location above, then adjust pinpoint details on map" rows="3" 
                                                       style="resize: none; font-size: 0.8rem; min-height: 80px; overflow-y: auto; background-color: #f8f9fa;"></textarea>
                                                <small id="location_display_debug" class="text-muted d-none"></small>
                                            </div>
                                            <button type="button" id="mapPickerBtn" class="btn btn-outline-soft-orange btn-sm rounded-3 fw-bold px-3 text-nowrap shadow-sm align-self-start" style="display: none; height: 38px;">
                                                <i class="fas fa-map-marked-alt me-1"></i>Adjust Map
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Street Address Field --}}
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold text-dark small">Detailed Street Address *</label>
                                        <textarea name="shipping_address" id="shipping_address" required rows="3"
                                                  class="form-control rounded-3 text-dark border" style="border-color: #ced4da !important; font-size: 0.85rem;" 
                                                  placeholder="Enter complete building specs or block metrics (e.g. Jl. Raya Kertajaya No. 45, Cluster Emerald Block C-12)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Map Picker Modal Container Engine --}}
                <div class="modal fade" id="mapPickerModal" tabindex="-1" aria-labelledby="mapPickerModalLabel">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="modal-header border-bottom-0 py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark" id="mapPickerModalLabel">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>Pinpoint Shipping Delivery Point
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div id="mapPickerContainer" style="width: 100%; height: 100%; position: relative;">
                                    <div id="mapElement" style="width: 100%; height: 100%;"></div>
                                    <div id="mapSearchBox" style="position: absolute; top: 15px; left: 15px; z-index: 10; width: 320px;">
                                        <input type="text" id="mapSearchInput" class="form-control shadow-sm border rounded-3 py-2 px-3 text-dark" style="font-size: 0.85rem;" placeholder="Search location area or landmark..." />
                                    </div>
                                    <div style="position: absolute; bottom: 20px; left: 20px; z-index: 10; background: rgba(45, 55, 72, 0.95); color: white; padding: 10px 16px; border-radius: 30px; font-size: 0.8rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        <i class="fas fa-mouse-pointer text-warning me-2"></i>Click anywhere on the map to re-adjust the delivery marker pin
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 py-3 px-4 bg-light">
                                <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmMapLocation" class="btn btn-soft-orange fw-bold rounded-pill px-4 shadow-sm">Confirm Selection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Courier Shipping Radio Methods & Midtrans Manifest --}}
            <div class="col-lg-5">

                {{-- Courier Method Card Components --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 checkout-card">
                    <div class="card-header bg-light text-dark fw-bold py-3 border-bottom border-light">
                        <i class="fas fa-truck me-2" style="color: #c25e25;"></i>Shipping Delivery Tier
                    </div>
                    <div class="card-body p-0">
                        @php
                            $shippingMethods = \App\Http\Controllers\CheckoutController::shippingMethods();
                        @endphp
                        @foreach($shippingMethods as $key => $method)
                        <label class="d-flex align-items-center justify-content-between p-3 border-bottom shipping-option mb-0"
                               style="cursor:pointer;" for="ship_{{ $key }}">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="shipping_method" id="ship_{{ $key }}"
                                       value="{{ $key }}"
                                       data-base-cost="{{ $method['base_cost'] }}"
                                       form="paymentForm"
                                       class="form-check-input shipping-radio mt-0"
                                       {{ $loop->first ? 'required checked' : '' }} style="border-color: #ced4da !important;">
                                <div>
                                    <div class="fw-bold text-dark small">{{ $method['name'] }}</div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Midtrans Secure Token Processing Manifest Card --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden checkout-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                            <i class="fas fa-shield-alt me-2 text-muted"></i>Payment Manifest Console
                        </h3>
                        
                        <div class="p-3 bg-light rounded-4 border-0 mb-4 shadow-sm">
                            <span class="text-dark small text-uppercase fw-bold tracking-wider mb-2 d-block" style="font-size: 0.65rem; color: #4a5568 !important;">Supported Channels</span>
                            <div class="d-flex flex-wrap gap-1.5 small text-secondary">
                                <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.7rem;"><i class="fas fa-credit-card text-primary me-1"></i>Credit Card</span>
                                <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.7rem;"><i class="fas fa-qrcode text-danger me-1"></i>QRIS Code</span>
                                <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.7rem;"><i class="fas fa-university text-info me-1"></i>Virtual Acc.</span>
                                <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.7rem;"><i class="fas fa-wallet text-warning me-1"></i>E-Wallets</span>
                            </div>
                        </div>
                        
                        <div class="alert border-0 text-dark rounded-4 d-flex gap-2 p-3 mb-4" style="background-color: #fdf6f0 !important; border: 1px solid #fbd3bc !important;">
                            <i class="fas fa-info-circle mt-0.5" style="color: #c25e25;"></i>
                            <small class="lh-base text-secondary" style="font-size: 0.75rem;">An encrypted securely sandboxed Midtrans overlay modal token gateway will execute authorization parameters instantly.</small>
                        </div>
                        
                        <button type="button" id="payButton" class="btn btn-soft-orange w-100 fw-bold btn-lg rounded-3 py-2.5 text-uppercase" style="font-size: 0.85rem;">
                            <span id="buttonText"><i class="fas fa-lock me-2"></i>Secure Payment</span>
                            <span id="loadingSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Securing Session Parameters...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Refund Request Modal --}}
    <div class="modal fade" id="refundRequestModal" tabindex="-1" aria-labelledby="refundRequestLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-3 shadow-lg">
                <div class="modal-header border-bottom-0 py-3 px-4 bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="refundRequestLabel">
                        <i class="fas fa-undo-alt text-danger me-2"></i>Request Refund
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="refundForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="refundOrderId" />
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Reason for Refund</label>
                            <textarea id="refundReason" class="form-control rounded-3" rows="3" placeholder="Please explain why you want to request a refund..." required></textarea>
                            <small class="text-muted d-block mt-1">Max 500 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Upload Supporting Photo (Optional)</label>
                            <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center" id="uploadArea" style="cursor: pointer; background-color: #f8f9fa; border-color: #dee2e6;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #6c757d;"></i>
                                <p class="mt-2 text-muted small">Drag and drop or click to select image</p>
                                <input type="file" id="refundImage" accept="image/*" style="display: none;" />
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Max 5MB (JPG, PNG, GIF)</small>
                            </div>
                            <small id="imageFileName" class="d-block mt-2 text-success" style="display: none;"></small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 py-3 px-4">
                        <button type="button" class="btn btn-light fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger fw-bold rounded-pill px-4" id="submitRefundBtn">Submit Refund Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places,geocoding&loading=async"></script>
<script>
const subtotal = {{ $total }};
let currentShippingCost = 0;
const indonesianLocations = {!! json_encode($indonesianLocations) !!};
let mapInstance, mapMarker, storeMarker, mapPickerModal, selectedMapLocation = null;
let currentStoreLocation = null;
let userSelectedLocation = null;
let locationSearchInput, locationSuggestions, postalCodeSelect;

function buildLocationsList() {
    const locations = [];
    for (const province in indonesianLocations) {
        for (const city in indonesianLocations[province]) {
            const cityData = indonesianLocations[province][city];
            if (cityData.postal_code) {
                locations.push({
                    province, city, district: city, ...cityData
                });
            } else {
                for (const district in cityData) {
                    const districtData = cityData[district];
                    locations.push({
                        province, city, district, ...districtData
                    });
                }
            }
        }
    }
    return locations;
}

const allLocations = buildLocationsList();

function selectLocation(location) {
    locationSearchInput.value = `${location.district}, ${location.city}`;
    locationSuggestions.classList.remove('show');
    
    document.getElementById('shipping_province').value = location.province;
    document.getElementById('shipping_city').value = location.city;
    document.getElementById('shipping_district').value = location.district;
    document.getElementById('shipping_latitude').value = location.lat;
    document.getElementById('shipping_longitude').value = location.lng;
    
    document.getElementById('shipping_postal_code_input').value = location.postal_code;
    
    userSelectedLocation = { province: location.province, city: location.city, district: location.district, lat: location.lat, lng: location.lng };
    const streetName = location.streets && location.streets.length > 0 ? location.streets[0] : 'No street specified';
    document.getElementById('shipping_street').value = streetName;
    
    const displayElem = document.getElementById('location_display');
    const displayValue = `${streetName}\n${location.district}, Kecamatan ${location.district}, ${location.city}, ${location.province}`;
    displayElem.value = displayValue;
    displayElem.style.display = 'block';
    displayElem.style.visibility = 'visible';
    displayElem.style.opacity = '1';
    displayElem.style.color = '#333';
    displayElem.style.backgroundColor = '#fff';
    displayElem.dispatchEvent(new Event('change', { bubbles: true }));
    displayElem.dispatchEvent(new Event('input', { bubbles: true }));
    
    const searchElem = document.getElementById('location_search');
    searchElem.value = `${location.district}, ${location.city}, ${location.province}`;
    searchElem.dispatchEvent(new Event('change', { bubbles: true }));
    
    const editBtn = document.querySelector('#mapPickerBtn');
    if (editBtn) {
        editBtn.style.display = 'block';
    }
    
    calculateShippingCost();
}

function initMapPicker() {
    const mapElement = document.getElementById('mapElement');
    
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }
    
    // Ensure map element is visible and sized properly with proper pointer events
    mapElement.style.width = '100%';
    mapElement.style.height = '100%';
    mapElement.style.display = 'block';
    mapElement.style.visibility = 'visible';
    mapElement.style.opacity = '1';
    mapElement.style.background = 'white';
    mapElement.style.pointerEvents = 'auto';
    
    const storeSelects = document.querySelectorAll('.item-store-select');
    let storeLocation = null;
    
    if (storeSelects.length > 0) {
        // Get first selected store's location
        for (let select of storeSelects) {
            if (select.value) {
                const selectedOption = select.querySelector(`option[value="${select.value}"]`);
                if (selectedOption) {
                    const lat = parseFloat(selectedOption.dataset.lat);
                    const lng = parseFloat(selectedOption.dataset.lng);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        storeLocation = { lat, lng };
                        break;
                    }
                }
            }
        }
    }
    
    currentStoreLocation = storeLocation;
    
    const userLat = parseFloat(document.getElementById('shipping_latitude').value);
    const userLng = parseFloat(document.getElementById('shipping_longitude').value);
    const hasUserLocation = !isNaN(userLat) && !isNaN(userLng);
    
    let center = { lat: -6.2088, lng: 106.8456 };
    if (hasUserLocation) {
        center = { lat: userLat, lng: userLng };
    } else if (currentStoreLocation) {
        center = currentStoreLocation;
    }
    
    mapInstance = new google.maps.Map(mapElement, {
        zoom: 15,
        center: center,
        streetViewControl: false,
        backgroundColor: '#ffffff'
    });
    
    if (currentStoreLocation) {
        storeMarker = new google.maps.Marker({
            position: currentStoreLocation,
            map: mapInstance,
            title: 'Store Location',
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            label: {
                text: 'Toko',
                fontSize: '12px',
                fontWeight: 'bold'
            }
        });
    }
    
    const userMarkerPosition = hasUserLocation ? { lat: userLat, lng: userLng } : center;
    mapMarker = new google.maps.Marker({
        position: userMarkerPosition,
        map: mapInstance,
        title: 'Your Location (Tap map to move)',
        label: {
            text: 'Anda',
            fontSize: '12px',
            fontWeight: 'bold'
        }
    });
    
    selectedMapLocation = userMarkerPosition;
    
    mapInstance.addListener('click', function(event) {
        const clickedLocation = event.latLng;
        selectedMapLocation = clickedLocation;
        mapMarker.setPosition(clickedLocation);
    });
    
    const mapSearchInput = document.getElementById('mapSearchInput');
    const autocompleteService = new google.maps.places.AutocompleteService();
    const geocoder = new google.maps.Geocoder();
    let resultsContainer = document.querySelector('.map-autocomplete-results');
    
    if (!resultsContainer) {
        resultsContainer = document.createElement('div');
        resultsContainer.className = 'map-autocomplete-results';
        mapSearchInput.parentElement.appendChild(resultsContainer);
    }
    
    mapSearchInput.addEventListener('input', function() {
        const input = this.value.trim();
        if (!input || input.length < 2) {
            resultsContainer.classList.remove('show');
            return;
        }
        
        autocompleteService.getPlacePredictions(
            { input: input, componentRestrictions: { country: 'id' } },
            function(predictions) {
                resultsContainer.innerHTML = '';
                if (predictions) {
                    predictions.slice(0, 5).forEach(prediction => {
                        const div = document.createElement('div');
                        div.className = 'map-result-item';
                        div.textContent = prediction.description;
                        div.addEventListener('click', function() {
                            geocoder.geocode({ placeId: prediction.place_id }, function(results) {
                                if (results[0]) {
                                    const location = results[0].geometry.location;
                                    selectedMapLocation = location;
                                    mapInstance.panTo(location);
                                    mapMarker.setPosition(location);
                                    mapSearchInput.value = prediction.description;
                                    resultsContainer.classList.remove('show');
                                }
                            });
                        });
                        resultsContainer.appendChild(div);
                    });
                    resultsContainer.classList.add('show');
                }
            }
        );
    });
}

// Set up persistent modal cleanup handler (attach once globally)
const mapModal = document.getElementById('mapPickerModal');
if (mapModal) {
    mapModal.addEventListener('hidden.bs.modal', function() {
        // Aggressive cleanup of ALL modal elements - runs after Bootstrap hides modal
        setTimeout(() => {
            // Step 1: Remove all modal backdrops completely
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });
            
            // Step 2: Remove modal-open class to restore scrolling
            document.body.classList.remove('modal-open');
            
            // Step 3: Restore body styles
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Step 4: Force remove any remaining fade/show classes
            document.querySelectorAll('.modal').forEach(m => {
                m.classList.remove('show');
                m.style.display = 'none';
            });
            
            // Step 5: Double-check no backdrops are hiding
            const anyBackdrops = document.querySelectorAll('.modal-backdrop, [class*="backdrop"]');
            anyBackdrops.forEach(backdrop => {
                if (backdrop.className.includes('backdrop')) {
                    backdrop.remove();
                }
            });
        }, 200);
    });
}

// Map picker button handler with proper Bootstrap modal event handling
document.getElementById('mapPickerBtn').addEventListener('click', function() {
    const mapModal = document.getElementById('mapPickerModal');
    const mapElement = document.getElementById('mapElement');
    
    // Reset map instance
    mapInstance = null;
    mapMarker = null;
    selectedMapLocation = null;
    
    // Clear map container
    if (mapElement) {
        mapElement.innerHTML = '';
    }
    
    // Handle modal shown event - initialize map when fully displayed
    const handleModalShown = function() {
        // Small delay to ensure DOM is ready
        setTimeout(function() {
            const mapEl = document.getElementById('mapElement');
            if (mapEl) {
                mapEl.style.width = '100%';
                mapEl.style.height = '100%';
                mapEl.style.display = 'block';
                mapEl.style.visibility = 'visible';
                mapEl.style.opacity = '1';
            }
            
            // Initialize the map
            initMapPicker();
            
            // Resize and center the map with more aggressive centering
            if (mapInstance) {
                // Trigger resize event
                google.maps.event.trigger(mapInstance, 'resize');
                
                // Wait for resize to complete before setting center
                setTimeout(function() {
                    let centerLocation = null;
                    
                    // Priority: user selected location > store location > default Jakarta
                    if (userSelectedLocation && userSelectedLocation.lat && userSelectedLocation.lng) {
                        centerLocation = new google.maps.LatLng(userSelectedLocation.lat, userSelectedLocation.lng);
                    } else if (currentStoreLocation && currentStoreLocation.lat && currentStoreLocation.lng) {
                        centerLocation = new google.maps.LatLng(currentStoreLocation.lat, currentStoreLocation.lng);
                    } else {
                        centerLocation = new google.maps.LatLng(-6.2088, 106.8456); // Jakarta default
                    }
                    
                    // Set zoom first, then center
                    mapInstance.setZoom(15);
                    mapInstance.setCenter(centerLocation);
                    
                    // Ensure proper positioning
                    google.maps.event.trigger(mapInstance, 'resize');
                }, 100);
            }
        }, 300);
    };
    
    // Attach shown handler without once flag - persists across opens
    mapModal.addEventListener('shown.bs.modal', handleModalShown);
    
    // Show the modal using Bootstrap API with backdrop disabled
    // This prevents Bootstrap from creating the dark overlay entirely
    const modal = new bootstrap.Modal(mapModal, { 
        backdrop: false,  // Disable backdrop completely
        keyboard: true    // Allow ESC to close
    });
    modal.show();
});

let mapConfirmButton = document.getElementById('confirmMapLocation');
if (mapConfirmButton) {
    mapConfirmButton.addEventListener('click', function(e) {
        e.preventDefault();
        let locToSave = null;
        if (selectedMapLocation) {
            locToSave = selectedMapLocation;
        } else if (mapMarker) {
            locToSave = mapMarker.getPosition();
        } else if (mapInstance) {
            locToSave = mapInstance.getCenter();
        }
        
        if (locToSave) {
            const lat = typeof locToSave.lat === 'function' ? locToSave.lat() : locToSave.lat;
            const lng = typeof locToSave.lng === 'function' ? locToSave.lng() : locToSave.lng;
            
            document.getElementById('shipping_latitude').value = lat;
            document.getElementById('shipping_longitude').value = lng;
            
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const result = results[0];
                    let province = '';
                    let city = '';
                    let district = '';
                    let postalCode = '';
                    let addressLine = '';
                    
                    result.address_components.forEach(component => {
                        if (component.types.includes('administrative_area_level_1')) {
                            province = component.long_name;
                        } else if (component.types.includes('locality')) {
                            city = component.long_name;
                        } else if (component.types.includes('administrative_area_level_2')) {
                            district = component.long_name;
                        } else if (component.types.includes('postal_code')) {
                            postalCode = component.long_name;
                        } else if (component.types.includes('route')) {
                            addressLine = component.long_name;
                        }
                    });
                    
                    if (!addressLine) {
                        addressLine = result.formatted_address.split(',')[0];
                    }
                    
                    document.getElementById('shipping_province').value = province;
                    document.getElementById('shipping_city').value = city;
                    document.getElementById('shipping_district').value = district;
                    document.getElementById('shipping_postal_code_input').value = postalCode;
                    document.getElementById('shipping_street').value = addressLine || 'Lokasi dari peta';
                    
                    const displayElement = document.getElementById('location_display');
                    const displayText = `${addressLine || 'Lokasi dari peta'}\n${result.formatted_address}`;
                    displayElement.value = displayText;
                    displayElement.dispatchEvent(new Event('change', { bubbles: true }));
                    displayElement.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    displayElement.style.display = 'block';
                    displayElement.style.visibility = 'visible';
                    displayElement.style.opacity = '1';
                    displayElement.style.color = '#333';
                    displayElement.style.backgroundColor = '#fff';
                    
                    const searchElement = document.getElementById('location_search');
                    searchElement.value = result.formatted_address;
                    searchElement.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    userSelectedLocation = { 
                        province: province, 
                        city: city, 
                        district: district, 
                        lat: lat, 
                        lng: lng,
                        address: result.formatted_address
                    };
                } else {
                    let matchedLocation = null;
                    let minDistance = Infinity;
                    
                    for (let loc of allLocations) {
                        const R = 6371;
                        const dLat = (loc.lat - lat) * Math.PI / 180;
                        const dLng = (loc.lng - lng) * Math.PI / 180;
                        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                  Math.cos(lat * Math.PI / 180) * Math.cos(loc.lat * Math.PI / 180) *
                                  Math.sin(dLng / 2) * Math.sin(dLng / 2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                        const dist = R * c;
                        
                        if (dist < minDistance) {
                            minDistance = dist;
                            matchedLocation = loc;
                        }
                    }
                    
                    if (matchedLocation) {
                        document.getElementById('shipping_province').value = matchedLocation.province;
                        document.getElementById('shipping_city').value = matchedLocation.city;
                        document.getElementById('shipping_district').value = matchedLocation.district;
                        document.getElementById('shipping_postal_code_input').value = matchedLocation.postal_code;
                        
                        const streetName = matchedLocation.streets && matchedLocation.streets.length > 0 ? matchedLocation.streets[0] : 'Lokasi Terpilih';
                        document.getElementById('shipping_street').value = streetName;
                        
                        const displayText = `${streetName}\n${matchedLocation.district}, Kecamatan ${matchedLocation.district}, ${matchedLocation.city}, ${matchedLocation.province}`;
                        const displayElement = document.getElementById('location_display');
                        displayElement.value = displayText;
                        displayElement.dispatchEvent(new Event('change', { bubbles: true }));
                        displayElement.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        const searchElement = document.getElementById('location_search');
                        searchElement.value = `${matchedLocation.district}, ${matchedLocation.city}, ${matchedLocation.province}`;
                        searchElement.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        userSelectedLocation = { 
                            province: matchedLocation.province, 
                            city: matchedLocation.city, 
                            district: matchedLocation.district, 
                            lat: lat, 
                            lng: lng,
                            from: 'database_fallback'
                        };
                    } else {
                        const fallbackText = `Custom Point\nLatitude: ${lat.toFixed(6)}\nLongitude: ${lng.toFixed(6)}`;
                        const displayElement = document.getElementById('location_display');
                        displayElement.value = fallbackText;
                        displayElement.dispatchEvent(new Event('change', { bubbles: true }));
                        displayElement.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        displayElement.style.display = 'block';
                        displayElement.style.visibility = 'visible';
                        displayElement.style.opacity = '1';
                        
                        const searchElement = document.getElementById('location_search');
                        searchElement.value = fallbackText;
                        searchElement.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        userSelectedLocation = { lat: lat, lng: lng };
                    }
                }
            });
            
            const modalElement = document.getElementById('mapPickerModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || mapPickerModal;
            if (modal) {
                modal.hide();
            }
            
            // Aggressive backdrop cleanup after modal hide completes
            setTimeout(() => {
                // Remove ALL modal backdrop elements from DOM
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                    backdrop.remove();
                });
                
                // Remove modal-open from body
                document.body.classList.remove('modal-open');
                
                // Force restore scroll  
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                // Check again and remove any stuck backdrops
                const backdrops = document.querySelectorAll('[class*="backdrop"]');
                backdrops.forEach(el => {
                    if (el.style.display !== 'block' || el.className.includes('modal-backdrop')) {
                        el.remove();
                    }
                });
            }, 400);
            
            setTimeout(() => {
                const locationDisplayElem = document.getElementById('location_display');
                if (locationDisplayElem) {
                    locationDisplayElem.style.borderColor = '#c25e25';
                    locationDisplayElem.style.borderWidth = '2px';
                    setTimeout(() => {
                        locationDisplayElem.style.borderColor = '';
                        locationDisplayElem.style.borderWidth = '';
                    }, 3000);
                }
                calculateShippingCost();
            }, 500);
        }
    });
}

async function calculateShippingCost() {
    try {
        const method = document.querySelector('.shipping-radio:checked')?.value;
        const lat = parseFloat(document.getElementById('shipping_latitude').value) || null;
        const lng = parseFloat(document.getElementById('shipping_longitude').value) || null;
        const destinationCity = document.getElementById('shipping_city').value || null;
        const destinationProvince = document.getElementById('shipping_province').value || null;

        if (!method || !lat || !lng || !destinationCity) {
            hideShippingCost();
            return;
        }

        const storeGroups = {};
        document.querySelectorAll('.cart-item').forEach(item => {
            const select = item.querySelector('.item-store-select');
            const storeId = select?.value;
            
            if (!storeId) return;
            
            if (!storeGroups[storeId]) {
                storeGroups[storeId] = {
                    store_id: storeId,
                    weight_grams: 0,
                    items: []
                };
            }
            
            const weightGrams = parseInt(item.dataset.weightGrams || 300);
            const quantity = parseInt(item.dataset.quantity || 1);
            const itemTotal = weightGrams * quantity;
            storeGroups[storeId].weight_grams += itemTotal;
            storeGroups[storeId].items.push({
                book_id: item.dataset.bookId,
                weight: weightGrams,
                qty: quantity,
                total: itemTotal
            });
        });

        const storeIds = Object.keys(storeGroups);
        if (storeIds.length === 0) {
            hideShippingCost();
            return;
        }

        let totalShippingCost = 0;
        window.shippingByStore = {};
        
        for (const storeId of storeIds) {
            const group = storeGroups[storeId];
            const payload = {
                store_id: group.store_id,
                latitude: lat,
                longitude: lng,
                method: method,
                weight_grams: Math.max(group.weight_grams, 100),
                destination_city: destinationCity,
                destination_province: destinationProvince
            };
            
            const response = await axios.post('{{ route("checkout.calculate-shipping") }}', payload, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
            });

            if (response.data.success) {
                const cost = response.data.cost;
                window.shippingByStore[storeId] = {
                    cost: cost,
                    display: response.data.display,
                    zone: response.data.zone,
                    breakdown: response.data.breakdown || {},
                    note: response.data.note,
                    origin_province: response.data.origin_province,
                    destination_province: response.data.destination_province
                };
                totalShippingCost += cost;
            } else {
                hideShippingCost();
                return;
            }
        }

        updateTotalsDisplay(totalShippingCost, 'Rp ' + totalShippingCost.toLocaleString('id-ID'), window.shippingByStore);
        displayPerStoreShippingBreakdown(window.shippingByStore);
    } catch (error) {
        console.error('[CALC] Error:', error.message);
        hideShippingCost();
    }
}

function displayPerStoreShippingBreakdown(shippingByStore) {
    let breakdownContainer = document.getElementById('perStoreShippingBreakdown');
    
    if (!breakdownContainer) {
        breakdownContainer = document.createElement('div');
        breakdownContainer.id = 'perStoreShippingBreakdown';
        const shippingDisplay = document.getElementById('shippingDisplay');
        if (shippingDisplay && shippingDisplay.parentElement) {
            shippingDisplay.parentElement.parentElement.insertAdjacentElement('afterend', breakdownContainer);
        } else {
            const grandTotal = document.getElementById('grandTotalDisplay');
            if (grandTotal) grandTotal.closest('.d-flex').insertAdjacentElement('beforebegin', breakdownContainer);
        }
    }

    const zoneLabels = {
        'A': 'Same Province', 'B': 'Same Province, Different City', 'C': 'Same Island, Different Province', 'D': 'Different Main Islands', 'E': 'Remote Area'
    };

    let html = '<div class="p-3 my-3 rounded-4" style="background: #fdf6f0; border: 1px solid #fbd3bc; border-left: 4px solid #c25e25 !important;"><p class="mb-2 text-dark small fw-bold text-uppercase tracking-wider" style="font-size: 0.65rem;">Courier Breakdown Logistics</p>';
    
    const storeInfo = {};
    document.querySelectorAll('.item-store-select').forEach(select => {
        const storeId = parseInt(select.value);
        const bookTitle = select.closest('.cart-item').querySelector('h4')?.textContent || 'Unknown';
        const selectedOption = select.options[select.selectedIndex];
        const storeName = selectedOption?.textContent?.split('(')[0]?.trim() || 'Unknown Store';
        
        if (storeId && shippingByStore[storeId]) {
            if (!storeInfo[storeId]) storeInfo[storeId] = { name: storeName, books: [] };
            if (!storeInfo[storeId].books.includes(bookTitle)) storeInfo[storeId].books.push(bookTitle);
        }
    });
    
    for (const [storeId, data] of Object.entries(shippingByStore)) {
        const b = data.breakdown;
        const totalWeight = (b.weight_kg || 0).toFixed(2);
        const extraWeight = (b.extra_kg || 0).toFixed(2);
        const zoneCategory = zoneLabels[b.zone] || 'Unknown Zone';
        const storeName = storeInfo[storeId]?.name || `Store #${storeId}`;
        const books = storeInfo[storeId]?.books || [];
        
        let weightDisplay = `${totalWeight}kg`;
        if (extraWeight > 0) weightDisplay += ` (above 1kg: ${extraWeight}kg)`;
        let booksHtml = books.length > 0 ? books.map(b => `<div class="ps-2 small text-muted text-truncate" style="font-size: 0.75rem;">• ${b}</div>`).join('') : '';
        
        html += `
        <div class="mb-2 p-3 bg-white border rounded-3" style="border-color: #eef0f2 !important;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong class="text-dark small">From: ${storeName}</strong>
                    ${booksHtml}
                    <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">Zone <strong>${b.zone}</strong> (${zoneCategory})</small>
                </div>
                <strong style="color: #c25e25; font-size: 0.9rem;">${data.display}</strong>
            </div>
            <small class="d-block text-muted mb-2" style="font-size: 0.7rem;">Combined Weight: ${weightDisplay}</small>
            <div class="ps-1 text-secondary" style="font-size: 0.75rem;">
                <div class="d-flex justify-content-between mb-0.5"><span>Base Tariff:</span><span>Rp ${(b.zone_base || 0).toLocaleString('id-ID')}</span></div>
                <div class="d-flex justify-content-between mb-0.5"><span>Weight Fee:</span><span>Rp ${(b.weight_fee || 0).toLocaleString('id-ID')}</span></div>
                <div class="d-flex justify-content-between;"><span>Service (${b.service_level || 'N/A'}):</span><span>Rp ${(b.service_surcharge || 0).toLocaleString('id-ID')}</span></div>
            </div>
        </div>`;
    }
    
    html += '</div>';
    breakdownContainer.innerHTML = html;
    breakdownContainer.style.display = 'block';
}

function hideShippingCost() {
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingRate = document.getElementById('shippingRate');
    const perStoreBreakdown = document.getElementById('perStoreShippingBreakdown');
    
    if (shippingDisplay) {
        shippingDisplay.style.display = 'none';
        shippingDisplay.textContent = '';
    }
    if (shippingRate) {
        shippingRate.style.display = 'none';
        shippingRate.textContent = '';
    }
    if (perStoreBreakdown) perStoreBreakdown.style.display = 'none';
    
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        grandTotalDisplay.style.color = 'inherit';
    }
    currentShippingCost = 0;
}

function showBaseCost(baseCost) {
    currentShippingCost = baseCost;
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingRate = document.getElementById('shippingRate');
    
    if (shippingDisplay) {
        shippingDisplay.style.cssText = 'display: block !important; visibility: visible !important; font-size: 1.1rem; font-weight: bold; color: #6c757d;';
        shippingDisplay.textContent = baseCost === 0 ? 'FREE' : 'Rp ' + baseCost.toLocaleString('id-ID');
    }
    if (shippingRate) {
        shippingRate.style.cssText = 'display: block !important; visibility: visible !important;';
        shippingRate.textContent = '(awaiting delivery address lookup...)';
        shippingRate.style.color = '#999';
    }
    
    const grand = subtotal + baseCost;
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) grandTotalDisplay.textContent = 'Rp ' + grand.toLocaleString('id-ID');
}

function updateTotalsDisplay(totalCost, totalDisplay, shippingByStore) {
    currentShippingCost = totalCost;
    const grand = subtotal + totalCost;
    
    const badge = document.getElementById('shippingCalculatedBadge');
    if (badge) badge.style.display = 'inline-block';
    
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingRate = document.getElementById('shippingRate');
    
    if (shippingDisplay) {
        shippingDisplay.style.cssText = 'display: block !important; visibility: visible !important; font-size: 1.1rem; font-weight: bold; color: #c25e25;';
        shippingDisplay.textContent = totalCost === 0 ? 'FREE' : totalDisplay;
    }
    if (shippingRate) {
        const numStores = Object.keys(shippingByStore).length;
        shippingRate.style.cssText = 'display: block !important; visibility: visible !important;';
        shippingRate.textContent = `${numStores} dispatch point${numStores > 1 ? 's' : ''} active`;
        shippingRate.style.color = '#666';
        shippingRate.style.fontSize = '0.85rem';
    }
    
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + grand.toLocaleString('id-ID');
        grandTotalDisplay.style.color = '#c25e25';
    }
}

function validateCheckoutForm() {
    const requiredFields = [
        { id: 'shipping_name', type: 'text', name: 'Recipient Name' },
        { id: 'shipping_phone', type: 'tel', name: 'Phone Number' },
        { id: 'location_search', type: 'text', name: 'Location' },
        { id: 'shipping_postal_code_input', type: 'select', name: 'Postal Code' },
        { id: 'shipping_address', type: 'textarea', name: 'Street Address' }
    ];
    
    let isValid = true;
    const emptyFields = [];
    const stockErrors = [];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element) {
            const value = element.value.trim();
            if (!value) {
                element.classList.add('field-error-highlight');
                emptyFields.push(field.name);
                isValid = false;
            } else {
                element.classList.remove('field-error-highlight');
            }
        }
    });
    
    const storeSelects = document.querySelectorAll('.item-store-select');
    storeSelects.forEach(select => {
        if (!select.value) {
            select.classList.add('field-error-highlight');
            emptyFields.push('Fulfillment Location Selection');
            isValid = false;
        } else {
            select.classList.remove('field-error-highlight');
            const cartItem = select.closest('.cart-item');
            const quantity = parseInt(cartItem.dataset.quantity || 1);
            const selectedOption = select.options[select.selectedIndex];
            const stock = parseInt(selectedOption.dataset.stock || 0);
            
            if (quantity > stock) {
                stockErrors.push(`Inventory Shortage on item row: allocation triggers exceeded.`);
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        let message = 'Validation alert processing failed:\n\n';
        if (emptyFields.length > 0) message += 'Missing inputs:\n' + emptyFields.map(f => '• ' + f).join('\n');
        if (stockErrors.length > 0) message += '\n\nStock faults:\n' + stockErrors.map(f => '• ' + f).join('\n');
        alert(message);
    }
    return isValid;
}

['shipping_name', 'shipping_phone', 'location_search', 'shipping_postal_code_input', 'shipping_address'].forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function() { if (this.value.trim()) this.classList.remove('field-error-highlight'); });
        field.addEventListener('change', function() { if (this.value.trim()) this.classList.remove('field-error-highlight'); });
    }
});

document.querySelectorAll('.item-store-select').forEach(select => {
    select.addEventListener('change', function() { if (this.value) this.classList.remove('field-error-highlight'); });
});

document.getElementById('payButton').addEventListener('click', function(e) {
    e.preventDefault();
    if (!validateCheckoutForm()) return;
    
    const payButton = this;
    payButton.disabled = true;
    document.getElementById('buttonText').classList.add('d-none');
    document.getElementById('loadingSpinner').classList.remove('d-none');

    const formData = new FormData(document.getElementById('paymentForm'));
    formData.append('shipping_cost', currentShippingCost);
    
    if (window.shippingByStore && Object.keys(window.shippingByStore).length > 0) {
        const firstStoreId = Object.keys(window.shippingByStore)[0];
        const firstStoreData = window.shippingByStore[firstStoreId];
        if (firstStoreData && firstStoreData.breakdown) {
            formData.append('shipping_zone', firstStoreData.breakdown.zone || 'C');
            formData.append('shipping_breakdown', JSON.stringify(firstStoreData.breakdown));
        }
    }

    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
        if (!r.ok) {
            if (r.status === 422) {
                return r.json().then(data => {
                    const errorMessages = [];
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => { errorMessages.push(`${field}: ${data.errors[field].join(', ')}`); });
                    }
                    throw new Error(errorMessages.join('\n') || 'Validation failed');
                });
            } else if (r.status >= 500) {
                return r.json().then(data => {
                    throw new Error(data.details || data.error || `Server error: ${r.status}`);
                }).catch(e => {
                    throw new Error(`Server error: ${r.status}`);
                });
            }
            throw new Error(`HTTP ${r.status}: ${r.statusText}`);
        }
        return r.json();
    })
    .then(data => {
        console.log('Checkout response:', data);
        if (data.success && data.snapToken) {
            snap.pay(data.snapToken, {
                onSuccess: function(result) {
                    fetch('{{ route("checkout.mark-payment-complete") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json'
                        },
                        body: JSON.stringify({ payment_type: result.payment_type || null })
                    })
                    .then(response => { if (!response.ok) throw new Error(); return response.json(); })
                    .then(() => {
                        const addressData = {
                            latitude: parseFloat(document.getElementById('shipping_latitude').value),
                            longitude: parseFloat(document.getElementById('shipping_longitude').value),
                            street: document.getElementById('shipping_street').value,
                            postal_code: document.getElementById('shipping_postal_code_input').value,
                            province: document.getElementById('shipping_province').value,
                            city: document.getElementById('shipping_city').value,
                            district: document.getElementById('shipping_district').value,
                        };
                        return fetch('{{ route("checkout.save-address") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(addressData)
                        });
                    })
                    .then(() => {
                        return fetch('{{ route("cart.clear") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        });
                    })
                    .then(() => { window.location.href = '{{ route("orders.index") }}?success=true&t=' + Date.now(); })
                    .catch(() => { window.location.href = '{{ route("orders.index") }}?success=true&t=' + Date.now(); });
                },
                onPending: function() { alert('Transaction processing status: Pending.'); resetBtn(); },
                onError: function() { alert('Transaction authentication signature failed.'); resetBtn(); },
                onClose: function() { 
                    // Clear cart on payment cancellation
                    fetch('{{ route("cart.clear") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => {
                        window.location.href = '{{ route("orders.index") }}?cancelled=true&t=' + Date.now();
                    }).catch(() => {
                        window.location.href = '{{ route("orders.index") }}?cancelled=true&t=' + Date.now();
                    });
                }
            });
        } else {
            const errorMsg = data.message || data.error || data.details || 'Payment engine execution error.';
            console.error('Checkout error:', data);
            alert('Error: ' + errorMsg);
            resetBtn();
        }
    })
    .catch(err => { 
        console.error('Checkout exception:', err);
        alert('Checkout Fault:\n\n' + (err.message || 'An operational error has occurred. Retrying validation recommended.'));
        resetBtn();
    });

    function resetBtn() {
        payButton.disabled = false;
        document.getElementById('buttonText').classList.remove('d-none');
        document.getElementById('loadingSpinner').classList.add('d-none');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    locationSearchInput = document.getElementById('location_search');
    locationSuggestions = document.getElementById('locationSuggestions');
    postalCodeSelect = document.getElementById('shipping_postal_code_input');
    
    if (locationSearchInput) {
        locationSearchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            locationSuggestions.innerHTML = '';
            if (!query || query.length < 2) {
                locationSuggestions.classList.remove('show');
                return;
            }
            const filtered = allLocations.filter(loc => {
                return `${loc.district} ${loc.city} ${loc.province}`.toLowerCase().includes(query);
            });
            if (filtered.length > 0) {
                filtered.forEach(loc => {
                    const div = document.createElement('div');
                    div.className = 'location-suggestion-item';
                    div.innerHTML = `
                        <div class="location-suggestion-label">${loc.district}</div>
                        <div class="location-suggestion-desc">${loc.city}, ${loc.province} - ${loc.postal_code}</div>`;
                    div.addEventListener('click', function() { selectLocation(loc); });
                    locationSuggestions.appendChild(div);
                });
                locationSuggestions.classList.add('show');
            } else {
                locationSuggestions.classList.remove('show');
            }
        });
        locationSearchInput.addEventListener('blur', function() { setTimeout(() => { locationSuggestions.classList.remove('show'); }, 200); });
    }
    
    const selectedStore = document.querySelector('.store-radio:checked');
    const selectedMethod = document.querySelector('.shipping-radio:checked');
    if (selectedStore && selectedMethod) {
        showBaseCost(parseInt(selectedMethod.dataset.baseCost || 0));
    }
    
    document.querySelectorAll('.shipping-radio').forEach(radio => {
        const methodCode = radio.value;
        const baseCost = parseInt(radio.dataset.baseCost || 0);
        const rateDisplay = document.querySelector(`.shipping-rate[data-method="${methodCode}"]`);
        if (rateDisplay && baseCost > 0) {
            rateDisplay.style.cssText = 'display: block !important; color: #999; font-size: 0.85rem;';
            rateDisplay.textContent = `(loading rates...)`;
        }
    });
    
    const userLat = document.getElementById('shipping_latitude').value;
    const userLng = document.getElementById('shipping_longitude').value;
    const userCity = document.getElementById('shipping_city').value;
    if (userLat && userLng && userCity && selectedMethod) {
        setTimeout(() => calculateShippingCost(), 100);
    }
    
    @if($savedAddress)
    const useSavedBtn = document.getElementById('useSavedAddress');
    const enterNewBtn = document.getElementById('enterNewAddress');
    
    if (useSavedBtn && enterNewBtn) {
        useSavedBtn.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('shipping_latitude').value = {{ json_encode($savedAddress['latitude']) }};
                document.getElementById('shipping_longitude').value = {{ json_encode($savedAddress['longitude']) }};
                document.getElementById('shipping_street').value = '{{ $savedAddress['street'] }}';
                document.getElementById('shipping_postal_code_input').value = '{{ $savedAddress['postal_code'] }}';
                document.getElementById('shipping_province').value = '{{ $savedAddress['province'] }}';
                document.getElementById('shipping_city').value = '{{ $savedAddress['city'] }}';
                document.getElementById('shipping_district').value = '{{ $savedAddress['district'] }}';
                document.getElementById('location_display').value = '{{ $savedAddress['district'] }}, {{ $savedAddress['city'] }}, {{ $savedAddress['province'] }}';
                
                const editBtn = document.getElementById('mapPickerBtn');
                if (editBtn) editBtn.style.display = 'block';
                calculateShippingCost();
            }
        });
        
        enterNewBtn.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('shipping_latitude').value = '';
                document.getElementById('shipping_longitude').value = '';
                document.getElementById('shipping_street').value = '';
                document.getElementById('shipping_postal_code_input').value = '';
                document.getElementById('shipping_province').value = '';
                document.getElementById('shipping_city').value = '';
                document.getElementById('shipping_district').value = '';
                document.getElementById('location_search').value = '';
                document.getElementById('location_display').value = '';
                
                const editBtn = document.getElementById('mapPickerBtn');
                if (editBtn) editBtn.style.display = 'none';
                hideShippingCost();
            }
        });
    }
    @endif
    
    function sampleDebounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => { clearTimeout(timeout); func(...args); };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    const debouncedCalculateShipping = sampleDebounce(calculateShippingCost, 500);
    
    document.querySelectorAll('.item-store-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const storeStock = parseInt(selectedOption.dataset.stock || 0);
            const cartItem = this.closest('.cart-item');
            const currentQuantity = parseInt(cartItem.dataset.quantity || 1);
            const messageEl = this.closest('div').querySelector('.item-store-message');
            const requiredBadge = this.closest('div').querySelector('.item-store-required');
            
            if (!this.value) {
                this.setAttribute('required', 'required');
                if (requiredBadge) requiredBadge.style.display = 'inline';
                return;
            }
            
            this.removeAttribute('required');
            if (requiredBadge) requiredBadge.style.display = 'none';
            if (messageEl) messageEl.style.display = 'block';
            
            if (storeStock <= 0) {
                alert('⚠️ Selected store has no stock for this book');
                this.value = '';
                this.setAttribute('required', 'required');
                return;
            }
            
            if (currentQuantity > storeStock) {
                alert(`⚠️ Selected store only has ${storeStock} stock but your quantity is ${currentQuantity}.`);
            }
            
            const userLat = document.getElementById('shipping_latitude').value;
            const userLng = document.getElementById('shipping_longitude').value;
            if (userLat && userLng) {
                let firstSelect = null;
                document.querySelectorAll('.item-store-select').forEach(sel => {
                    if (!firstSelect && sel.value) firstSelect = sel;
                });
                if (firstSelect) {
                    const opt = firstSelect.options[firstSelect.selectedIndex];
                    currentStoreLocation = { lat: parseFloat(opt.dataset.lat), lng: parseFloat(opt.dataset.lng) };
                    calculateShippingCost();
                }
            }
        });
    });

    document.querySelectorAll('.shipping-radio').forEach(function(radio) {
        radio.addEventListener('change', calculateShippingCost);
    });
    
    const addressFields = [
        'shipping_latitude', 'shipping_longitude', 'shipping_city', 'shipping_province', 'shipping_district', 'shipping_street', 'shipping_postal_code_input'
    ];
    addressFields.forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() { debouncedCalculateShipping(); });
            field.addEventListener('blur', function() { debouncedCalculateShipping(); });
        }
    });

    // ===== Refund Modal Handler =====
    const uploadArea = document.getElementById('uploadArea');
    const refundImage = document.getElementById('refundImage');
    const imageFileName = document.getElementById('imageFileName');
    let selectedFile = null;

    // File upload click handler
    uploadArea.addEventListener('click', () => refundImage.click());

    // File input change
    refundImage.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                refundImage.value = '';
                return;
            }
            if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                alert('Only JPG, PNG, and GIF formats are allowed');
                refundImage.value = '';
                return;
            }
            selectedFile = file;
            imageFileName.textContent = '✓ ' + file.name;
            imageFileName.style.display = 'block';
            uploadArea.style.borderColor = '#28a745';
            uploadArea.style.backgroundColor = '#f0fdf4';
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#c25e25';
        uploadArea.style.backgroundColor = '#fff5f0';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#dee2e6';
        uploadArea.style.backgroundColor = '#f8f9fa';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#dee2e6';
        uploadArea.style.backgroundColor = '#f8f9fa';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            refundImage.files = files;
            const event = new Event('change', { bubbles: true });
            refundImage.dispatchEvent(event);
        }
    });

    // Submit refund request
    document.getElementById('submitRefundBtn').addEventListener('click', async function() {
        const orderId = document.getElementById('refundOrderId').value;
        const reason = document.getElementById('refundReason').value.trim();

        if (!orderId) {
            alert('Order ID not found');
            return;
        }

        if (!reason || reason.length < 10) {
            alert('Please provide a detailed reason (at least 10 characters)');
            return;
        }

        if (reason.length > 500) {
            alert('Reason must be less than 500 characters');
            return;
        }

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('reason', reason);
        if (selectedFile) {
            formData.append('image', selectedFile);
        }

        try {
            const response = await fetch('{{ route("refunds.request") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('✓ Refund request submitted successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('refundRequestModal'));
                modal.hide();
                document.getElementById('refundForm').reset();
                selectedFile = null;
                imageFileName.style.display = 'none';
            } else {
                alert('Error: ' + (data.message || 'Failed to submit refund request'));
            }
        } catch (err) {
            console.error('Refund request error:', err);
            alert('Failed to submit refund request. Please try again.');
        }
    });
});
</script>
@endsection