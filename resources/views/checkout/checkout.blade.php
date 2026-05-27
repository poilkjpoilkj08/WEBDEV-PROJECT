@extends('base.base')
@section('content')

<style>
    /* --- SMOOTH SCROLLING & THEME BACKGROUND --- */
    html {
        scroll-behavior: smooth;
    }

    body {
        background-image: url("{{ asset('images/bg1.jpg') }}") !important;
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

    /* Remove core white background wrapper to let background image shine */
    .content-wrapper {
        background-color: transparent !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
    }

    /* Card lift hover animations */
    .hover-lift:hover {
        transform: translateY(-4px);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .hover-lift {
        transition: transform 0.2s ease;
    }
    
    .max-width-fit {
        width: fit-content;
    }
    
    /* Store Selection Styling */
    .store-card {
        transition: all 0.3s ease;
        background: white;
    }
    
    .store-card:hover {
        border-color: #0d6efd !important;
        background: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
    }
    
    input[type="radio"].store-radio:checked + div .store-card {
        border-color: #0d6efd !important;
        background: #e7f1ff;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }
    
    /* Shipping Option Styling */
    .shipping-option {
        transition: all 0.2s ease;
        background: white;
    }
    
    .shipping-option:hover {
        background: #f8f9fa;
        border-left: 3px solid #ccc;
    }
    
    input[type="radio"].shipping-radio:checked + div + span,
    input[type="radio"].shipping-radio:checked ~ .shipping-cost {
        color: #28a745 !important;
        font-weight: 700;
    }
    
    input[type="radio"].shipping-radio:checked ~ .shipping-option,
    .shipping-option:has(input[type="radio"].shipping-radio:checked) {
        background: #f0f8f5;
        border-left: 4px solid #28a745;
    }
    
    /* Form Validation Highlighting */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }
    
    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .field-error-highlight {
        background-color: #fff5f5 !important;
        border: 2px solid #dc3545 !important;
    }
    
    /* Order Summary Prominence */
    #shippingDisplay {
        font-size: 1.1rem !important;
        font-weight: bold !important;
        color: #198754 !important;
        display: block !important;
    }
    
    #shippingRate {
        display: block !important;
        color: #666 !important;
        font-size: 0.9rem !important;
        margin-top: 4px;
    }

    #grandTotalDisplay {
        color: #198754 !important;
        font-weight: 700 !important;
    }
    
    .error-message {
        display: none;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .field-error-highlight ~ .error-message {
        display: block;
    }
    
    /* Street Autocomplete Styling */
    .street-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .street-suggestions.show {
        display: block;
    }
    
    .street-suggestion-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.15s;
    }
    
    .street-suggestion-item:hover,
    .street-suggestion-item.active {
        background-color: #f8f9fa;
    }
    
    .street-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .street-input-wrapper {
        position: relative;
    }
    
    /* Location Search Styling */
    .location-search-wrapper {
        position: relative;
    }
    
    .location-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .location-suggestions.show {
        display: block;
    }
    
    .location-suggestions::-webkit-scrollbar {
        width: 6px;
    }
    
    .location-suggestions::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .location-suggestions::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .location-suggestions::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .location-suggestion-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.15s;
    }
    
    .location-suggestion-item:hover,
    .location-suggestion-item.active {
        background-color: #f8f9fa;
    }
    
    .location-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .location-suggestion-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #333;
    }
    
    .location-suggestion-desc {
        font-size: 0.85rem;
        color: #999;
        margin-top: 2px;
    }
    
    /* Map Picker Modal */
    #mapPickerModal .modal-body {
        padding: 0 !important;
    }
    
    #mapSearchBox {
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    #mapSearchInput {
        border: none;
    }
    
    #mapSearchInput:focus {
        border: none;
        box-shadow: none;
        outline: 1px solid #0d6efd;
    }
    
    .map-autocomplete-results {
        position: absolute;
        top: 45px;
        left: 10px;
        z-index: 11;
        width: 280px;
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    
    .map-autocomplete-results.show {
        display: block;
    }
    
    .map-result-item {
        padding: 10px 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        font-size: 0.9rem;
    }
    
    .map-result-item:hover {
        background-color: #f8f9fa;
    }
    
    .map-result-item:last-child {
        border-bottom: none;
    }
</style>

<div class="container py-5 content-wrapper">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-5 gap-3">
        <div>
            <div class="glass-header-box mb-2">
                <h1 class="h3 mb-0 fw-bold text-dark">Secure Checkout</h1>
            </div>
            <p class="text-white bg-dark bg-opacity-25 d-inline-block px-3 py-1 rounded-pill small ms-2 backdrop-blur mb-0">
                Review your order details, select shipping, and execute payment safely.
            </p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">
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
        {{-- LEFT: Order Summary + Shipping Form --}}
        <div class="col-lg-7">

            {{-- Order summary with Store Selection Card --}}
            <div class="card shadow-lg border-0 bg-white rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 p-md-5">
                    <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                        <i class="fas fa-shopping-bag text-primary me-2"></i>Order Summary & Store Selection
                        <span id="shippingCalculatedBadge" class="badge bg-success ms-2" style="display: none;">
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
                                         style="width:40px;height:55px;object-fit:cover;border-radius:4px;" class="border shadow-sm">
                                    <div>
                                        <h4 class="h6 fw-bold text-dark mb-1 text-truncate" style="max-width: 280px;" title="{{ $item['book']->title }}">{{ $item['book']->title }}</h4>
                                        <small class="text-muted font-monospace bg-light px-2 py-0.5 rounded border small">Qty: {{ $item['quantity'] }} × Rp {{ number_format($item['book']->price, 0, ',', '.') }} | Weight: {{ (int)($item['book']->weight_grams ?? 300) }}g</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-secondary">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                            
                            {{-- Store Selection for this item --}}
                            <div class="ms-5 ps-2 mb-2">
                                <label class="form-label fw-medium text-dark small mb-2">
                                    Select Store for this item
                                    <span class="item-store-required text-danger" style="display: @if(!$item['store_id']) inline @else none @endif;">*</span>
                                </label>
                                <select name="store_ids[{{ $item['book']->id }}]" form="paymentForm" 
                                        class="form-select form-select-sm rounded-2 border-info fw-semibold item-store-select" 
                                        data-book-id="{{ $item['book']->id }}" 
                                        @if(!$item['store_id']) required @endif>
                                    <option value="">-- Choose Store --</option>
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
                                       style="display: @if(!$item['store_id']) block @else none @endif;">
                                    <span class="text-danger" style="display: @if(!$item['store_id']) inline @else none @endif;">⚠️ Store selection required</span>
                                    <span class="text-success" style="display: @if($item['store_id']) inline @else none @endif;">✓ Store selected</span>
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted small text-uppercase fw-bold">Subtotal</span>
                        <span class="fw-semibold text-dark" id="subtotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 mb-2">
                        <span class="text-muted small text-uppercase fw-bold">Shipping cost</span>
                        <div class="text-end">
                            <span id="shippingDisplay" class="d-block fw-bold text-success" style="display: none; font-size: 1.1rem;">— select method</span>
                            <span id="shippingRate" class="d-block text-muted small" style="display: none; margin-top: 4px;"></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 mb-2">
                        <span class="text-muted small text-uppercase fw-bold">Payment Method</span>
                        <span class="text-muted small fw-semibold">— select at checkout</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top">
                        <span class="text-muted small text-uppercase fw-bold tracking-wider">Grand Total Amount</span>
                        <span class="h3 text-success fw-bold mb-0" id="grandTotalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping address form Card --}}
            <div class="card shadow-lg border-0 bg-white rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>Shipping Address
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
                            {{-- Saved Address Option --}}
                            @if($savedAddress)
                            <div class="col-12">
                                <div class="alert alert-info bg-light border border-info rounded-3 p-3 mb-0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="useSavedAddress" name="addressChoice" value="saved" checked>
                                        <label class="form-check-label" for="useSavedAddress">
                                            <strong>Use Saved Address</strong>
                                            <div class="small text-muted mt-1">
                                                {{ $savedAddress['street'] }}, {{ $savedAddress['district'] }}, {{ $savedAddress['city'] }}, {{ $savedAddress['province'] }} {{ $savedAddress['postal_code'] }}
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="enterNewAddress" name="addressChoice" value="new">
                                    <label class="form-check-label" for="enterNewAddress">
                                        <strong>Enter New Address</strong>
                                    </label>
                                </div>
                            </div>
                            <div id="newAddressSection" style="display: none; width: 100%;"></div>
                            @endif

                            <div class="row g-3" id="addressFormFields">
                            {{-- Full Name and Phone --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Full Name *</label>
                                <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}"
                                       required class="form-control rounded-3 border-secondary bg-light" placeholder="Recipient name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Phone Number *</label>
                                <input type="text" name="shipping_phone" required maxlength="50"
                                       class="form-control rounded-3 border-secondary bg-light" placeholder="+62 81234567890">
                            </div>

                            {{-- Country Selection (Indonesia only) --}}
                            <div class="col-12">
                                <label class="form-label fw-medium text-dark small">Country *</label>
                                <input type="text" name="shipping_country" value="Indonesia" readonly required class="form-control rounded-3 border-secondary bg-light" disabled>
                                <input type="hidden" name="shipping_country" value="Indonesia">
                            </div>

                            {{-- Location Search (Single searchbox for Province/City/District) --}}
                            <div class="col-12">
                                <label class="form-label fw-medium text-dark small">Location (Province/City/District) *</label>
                                <div class="location-search-wrapper position-relative">
                                    <input type="text" id="location_search" placeholder="Start typing location (e.g. Jakarta Pusat, Menteng)..." 
                                           class="form-control rounded-3 border-secondary bg-light" autocomplete="off">
                                    <div id="locationSuggestions" class="location-suggestions"></div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Type to search for province, city, or district
                                </small>
                            </div>

                            {{-- Postal Code (auto-populated from district) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Postal Code *</label>
                                <input type="text" id="shipping_postal_code_input" name="shipping_postal_code" required readonly
                                       class="form-control rounded-3 border-secondary bg-light" placeholder="Auto-filled">
                            </div>

                            {{-- Titik Lokasi (Location Point Display & Edit) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small d-block">Titik Lokasi (Pinpoint) *</label>
                                <div class="d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <textarea id="location_display" readonly
                                               class="form-control rounded-3 border-secondary" 
                                               placeholder="Select location first, then edit on map" rows="3" 
                                               style="resize: none; font-size: 0.95rem; background-color: #fff; color: #333; min-height: 80px; overflow-y: auto; display: block !important; visibility: visible !important; opacity: 1 !important;"></textarea>
                                        <small id="location_display_debug" class="text-muted d-block mt-1" style="max-height: 60px; overflow: auto; padding: 5px; background: #f5f5f5; border: 1px dashed #ccc; display: none;"></small>
                                    </div>
                                    <button type="button" id="mapPickerBtn" class="btn btn-outline-primary rounded-3 fw-bold px-4 text-nowrap shadow-sm" style="display: none;">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                </div>
                            </div>

                            {{-- Street Address --}}
                            <div class="col-12">
                                <label class="form-label fw-medium text-dark small">Street Address *</label>
                                <textarea name="shipping_address" id="shipping_address" required rows="3"
                                          class="form-control rounded-3 border-secondary bg-light" 
                                          placeholder="Enter your complete street address (e.g., Jl. Ahmad Yani No. 123, RT 01/RW 02)"></textarea>
                            </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Map Picker Modal --}}
            <div class="modal fade" id="mapPickerModal" tabindex="-1" aria-labelledby="mapPickerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="mapPickerModalLabel">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>Pick Location on Map
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="mapPickerContainer" style="width: 100%; height: 500px; position: relative;">
                                <div id="mapElement" style="width: 100%; height: 100%;"></div>
                                <div id="mapSearchBox" style="position: absolute; top: 10px; left: 10px; z-index: 10; width: 300px;">
                                    <input type="text" id="mapSearchInput" class="form-control" placeholder="Cari lokasi atau alamat..." />
                                </div>
                                <div style="position: absolute; bottom: 15px; left: 15px; z-index: 10; background: rgba(0,0,0,0.85); color: white; padding: 12px 16px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                    <i class="fas fa-hand-pointer text-info me-2"></i>Ketuk pada peta untuk menempatkan penanda
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmMapLocation" class="btn btn-primary">Confirm Location</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Shipping method + Payment --}}
        <div class="col-lg-5">

            {{-- Shipping method Selection Card --}}
            <div class="card shadow-lg border-0 bg-white rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white fw-bold py-3 border-bottom">
                    <i class="fas fa-truck me-2 text-primary"></i>Shipping Method
                </div>
                <div class="card-body p-0">
                    @php
                        $shippingMethods = \App\Http\Controllers\CheckoutController::shippingMethods();
                    @endphp
                    @foreach($shippingMethods as $key => $method)
                    <label class="d-flex align-items-center justify-content-between p-3 border-bottom shipping-option hover-lift mb-0"
                           style="cursor:pointer;" for="ship_{{ $key }}">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="shipping_method" id="ship_{{ $key }}"
                                   value="{{ $key }}"
                                   data-base-cost="{{ $method['base_cost'] }}"
                                   form="paymentForm"
                                   class="form-check-input shipping-radio mt-0"
                                   {{ $loop->first ? 'required checked' : '' }}>
                            <div>
                                <div class="fw-bold text-dark">{{ $method['name'] }}</div>
                            </div>
                        </div>
                        <div></div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Payment Gateway Processor Card --}}
            <div class="card shadow-lg border-0 bg-white rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <h3 class="h5 fw-bold text-dark border-bottom pb-3 mb-4">
                        <i class="fas fa-shield-alt text-muted me-2"></i>Payment Manifest
                    </h3>
                    
                    <div class="p-3 bg-light rounded-4 border-0 mb-4 shadow-sm">
                        <span class="text-dark small text-uppercase fw-bold tracking-wider mb-2 d-block" style="font-size: 0.75rem;">Supported Channels</span>
                        <div class="d-flex flex-wrap gap-1 small text-secondary">
                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5"><i class="fas fa-credit-card text-primary me-1"></i>Cards</span>
                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5"><i class="fas fa-qrcode text-danger me-1"></i>QRIS</span>
                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5"><i class="fas fa-university text-info me-1"></i>Transfer</span>
                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5"><i class="fas fa-wallet text-warning me-1"></i>Wallets</span>
                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5"><i class="fas fa-coins text-secondary me-1"></i>BNPL</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-muted small">Billing Email Address</label>
                        <input type="email" class="form-control rounded-3 border-light bg-light text-muted" value="{{ auth()->user()->email }}" disabled />
                    </div>
                    
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark rounded-3 d-flex gap-2 p-3 mb-4">
                        <i class="fas fa-info-circle text-warning mt-0.5"></i>
                        <small class="lh-base" style="font-size: 0.8rem;">A secure, encrypted Midtrans payment window overlay will display to process token authorizations safely.</small>
                    </div>
                    
                    <button type="button" id="payButton" class="btn btn-success w-100 fw-bold btn-lg rounded-pill shadow-sm py-2.5 text-uppercase fs-6">
                        <span id="buttonText"><i class="fas fa-lock me-2"></i>Pay Now</span>
                        <span id="loadingSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Securing Session...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,geocoding"></script>
<script>
const subtotal = {{ $total }};
let currentShippingCost = 0;
const indonesianLocations = {!! json_encode($indonesianLocations) !!};
let mapInstance, mapMarker, storeMarker, mapPickerModal, selectedMapLocation = null;
let currentStoreLocation = null;  // Track selected store location
let userSelectedLocation = null;  // Track user's manually selected location
let locationSearchInput, locationSuggestions, postalCodeSelect;  // Will be initialized in DOMContentLoaded

// Build flat location list for searching (3-level hierarchy: Province -> City -> District)
function buildLocationsList() {
    const locations = [];
    for (const province in indonesianLocations) {
        for (const city in indonesianLocations[province]) {
            const cityData = indonesianLocations[province][city];
            // Check if it's a district-level structure (has postal_code key)
            if (cityData.postal_code) {
                // Single district (e.g., Gresik -> Gresik)
                locations.push({
                    province, city, district: city, ...cityData
                });
            } else {
                // Multiple districts (e.g., Jakarta Pusat -> Menteng, Tanah Abang)
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
    
    // Update hidden fields
    document.getElementById('shipping_province').value = location.province;
    document.getElementById('shipping_city').value = location.city;
    document.getElementById('shipping_district').value = location.district;
    document.getElementById('shipping_latitude').value = location.lat;
    document.getElementById('shipping_longitude').value = location.lng;
    
    // Set postal code directly (singular, readonly)
    document.getElementById('shipping_postal_code_input').value = location.postal_code;
    
    // Update location display box with full address format (street, district, city, province)
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
    
    console.log('[SELECT-DEBUG] location_display setup:');
    console.log('  - value:', displayElem.value);
    console.log('  - computed style.display:', window.getComputedStyle(displayElem).display);
    
    const searchElem = document.getElementById('location_search');
    const searchValue = `${location.district}, ${location.city}, ${location.province}`;
    searchElem.value = searchValue;
    searchElem.dispatchEvent(new Event('change', { bubbles: true }));
    
    console.log('[SELECT] Location updated:', location.district, location.city, location.province);
    console.log('[SELECT] Display:', displayValue);
    
    // Show the Edit button now that location is selected
    const editBtn = document.querySelector('#mapPickerBtn');
    if (editBtn) {
        editBtn.style.display = 'block';
    }
    
    calculateShippingCost();
}

// Map Picker Implementation
function initMapPicker() {
    const mapElement = document.getElementById('mapElement');
    
    // Get store location from selected radio button
    const storeRadio = document.querySelector('.store-radio:checked');
    if (storeRadio) {
        currentStoreLocation = {
            lat: parseFloat(storeRadio.dataset.lat),
            lng: parseFloat(storeRadio.dataset.lng)
        };
    }
    
    // Get user location if already selected
    const userLat = parseFloat(document.getElementById('shipping_latitude').value);
    const userLng = parseFloat(document.getElementById('shipping_longitude').value);
    const hasUserLocation = !isNaN(userLat) && !isNaN(userLng);
    
    // Determine center: use user location, or store location, or Jakarta default
    let center = { lat: -6.2088, lng: 106.8456 };
    if (hasUserLocation) {
        center = { lat: userLat, lng: userLng };
    } else if (currentStoreLocation) {
        center = currentStoreLocation;
    }
    
    mapInstance = new google.maps.Map(mapElement, {
        zoom: 15,
        center: center,
        streetViewControl: false
    });
    
    // Add store marker with label
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
    
    // Add user location marker (tap to move)
    const userMarkerPosition = hasUserLocation ? { lat: userLat, lng: userLng } : center;
    mapMarker = new google.maps.Marker({
        position: userMarkerPosition,
        map: mapInstance,
        title: 'Lokasi Anda (Tap map to move)',
        label: {
            text: 'Anda',
            fontSize: '12px',
            fontWeight: 'bold'
        }
    });
    
    selectedMapLocation = userMarkerPosition;
    
    // Add map click listener to place marker on tap
    mapInstance.addListener('click', function(event) {
        const clickedLocation = event.latLng;
        selectedMapLocation = clickedLocation;
        mapMarker.setPosition(clickedLocation);
        console.log('[MAP] Marker placed at:', clickedLocation.lat(), clickedLocation.lng());
    });
    
    // Map search functionality
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

// Map Picker Button
document.getElementById('mapPickerBtn').addEventListener('click', function() {
    mapPickerModal = new bootstrap.Modal(document.getElementById('mapPickerModal'));
    mapPickerModal.show();
    
    // Always reinitialize map when modal is shown
    setTimeout(function() {
        mapInstance = null;  // Clear old instance
        mapMarker = null;
        selectedMapLocation = null;
        initMapPicker();
    }, 300);
});

// Confirm Map Location
let mapConfirmButton = document.getElementById('confirmMapLocation');
if (mapConfirmButton) {
    mapConfirmButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get current marker position from map click or marker position
        let locToSave = null;
        
        if (selectedMapLocation) {
            locToSave = selectedMapLocation;
        } else if (mapMarker) {
            locToSave = mapMarker.getPosition();
        } else if (mapInstance) {
            locToSave = mapInstance.getCenter();
        }
        
        if (locToSave) {
            // Ensure it's a LatLng object
            const lat = typeof locToSave.lat === 'function' ? locToSave.lat() : locToSave.lat;
            const lng = typeof locToSave.lng === 'function' ? locToSave.lng() : locToSave.lng;
            
            console.log('[CONFIRM] Marker position - Lat:', lat, 'Lng:', lng);
            
            // ALWAYS save coordinates directly
            document.getElementById('shipping_latitude').value = lat;
            document.getElementById('shipping_longitude').value = lng;
            
            // Use Reverse Geocoding to get address from coordinates
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const result = results[0];
                    console.log('[GEOCODE] Address result:', result.formatted_address);
                    
                    // Extract address components
                    let province = '';
                    let city = '';
                    let district = '';
                    let postalCode = '';
                    let addressLine = '';
                    
                    // Parse address components from Reverse Geocoding
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
                    
                    // Use formatted address as fallback
                    if (!addressLine) {
                        addressLine = result.formatted_address.split(',')[0];
                    }
                    
                    console.log('[GEOCODE] Parsed - Province:', province, 'City:', city, 'District:', district, 'Address:', addressLine);
                    
                    // Update form fields
                    document.getElementById('shipping_province').value = province;
                    document.getElementById('shipping_city').value = city;
                    document.getElementById('shipping_district').value = district;
                    document.getElementById('shipping_postal_code_input').value = postalCode;
                    document.getElementById('shipping_street').value = addressLine || 'Lokasi dari peta';
                    
                    // Update display element with address from Geocoding API
                    const displayElement = document.getElementById('location_display');
                    const displayText = `${addressLine || 'Lokasi dari peta'}\n${result.formatted_address}`;
                    displayElement.value = displayText;
                    displayElement.dispatchEvent(new Event('change', { bubbles: true }));
                    displayElement.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    // Force visibility
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
                    
                    console.log('[CONFIRM] Location saved from Geocoding API:');
                    console.log('  - Province:', province);
                    console.log('  - City:', city);
                    console.log('  - District:', district);
                    console.log('  - Address:', addressLine);
                    console.log('  - Coordinates: Lat', lat, 'Lng', lng);
                    
                } else {
                    // Fallback if Geocoding fails - try database matching first
                    console.log('[GEOCODE] Failed - Status:', status, '- Trying database matching...');
                    
                    // Try to find matching location from database using Haversine formula
                    let matchedLocation = null;
                    let minDistance = Infinity; // Find closest location, no threshold
                    
                    for (let loc of allLocations) {
                        // Haversine distance calculation
                        const R = 6371; // km
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
                        console.log('[FALLBACK] Found database match within', minDistance.toFixed(2), 'km:', matchedLocation);
                        
                        // Use database location
                        document.getElementById('shipping_province').value = matchedLocation.province;
                        document.getElementById('shipping_city').value = matchedLocation.city;
                        document.getElementById('shipping_district').value = matchedLocation.district;
                        document.getElementById('shipping_postal_code_input').value = matchedLocation.postal_code;
                        
                        const streetName = matchedLocation.streets && matchedLocation.streets.length > 0 
                            ? matchedLocation.streets[0] 
                            : 'Lokasi Terpilih';
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
                        
                        console.log('[FALLBACK] Location matched from database');
                        
                    } else {
                        // No database match - use coordinates only
                        console.log('[FALLBACK] No database match - Using coordinates only');
                        
                        const fallbackText = `Lokasi Khusus\nLatitude: ${lat.toFixed(6)}\nLongitude: ${lng.toFixed(6)}`;
                        const displayElement = document.getElementById('location_display');
                        displayElement.value = fallbackText;
                        displayElement.dispatchEvent(new Event('change', { bubbles: true }));
                        displayElement.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        displayElement.style.display = 'block';
                        displayElement.style.visibility = 'visible';
                        displayElement.style.opacity = '1';
                        displayElement.style.color = '#333';
                        displayElement.style.backgroundColor = '#fff';
                        
                        const searchElement = document.getElementById('location_search');
                        searchElement.value = fallbackText;
                        searchElement.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        userSelectedLocation = { lat: lat, lng: lng };
                        console.log('[FALLBACK] Coordinates-only location set');
                    }
                }
            });  // Close geocoder.geocode callback
            
            // Hide modal
            const modalElement = document.getElementById('mapPickerModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || mapPickerModal;
            if (modal) {
                modal.hide();
            }
            
            // Verify fields were set correctly after modal close
            setTimeout(() => {
                const verifyDisplayValue = document.getElementById('location_display').value;
                const verifySearchValue = document.getElementById('location_search').value;
                const verifyProvince = document.getElementById('shipping_province').value;
                const verifyCity = document.getElementById('shipping_city').value;
                const verifyDistrict = document.getElementById('shipping_district').value;
                const verifyLat = document.getElementById('shipping_latitude').value;
                const verifyLng = document.getElementById('shipping_longitude').value;
                
                const locDisplay = document.getElementById('location_display');
                const computedStyle = window.getComputedStyle(locDisplay);
                
                console.log('[VERIFY] After modal close:');
                console.log('  - location_display.value:', verifyDisplayValue);
                console.log('  - location_search.value:', verifySearchValue);
                console.log('  - Province:', verifyProvince);
                console.log('  - City:', verifyCity);
                console.log('  - District:', verifyDistrict);
                console.log('  - Latitude:', verifyLat);
                console.log('  - Longitude:', verifyLng);
                console.log('[VERIFY] Computed Styles:');
                console.log('  - display:', computedStyle.display);
                console.log('  - visibility:', computedStyle.visibility);
                console.log('  - opacity:', computedStyle.opacity);
                console.log('  - color:', computedStyle.color);
                console.log('  - backgroundColor:', computedStyle.backgroundColor);
                console.log('  - height:', locDisplay.offsetHeight);
                console.log('  - width:', locDisplay.offsetWidth);
                
                // Add visual indicator that location was updated
                const locationDisplayElem = document.getElementById('location_display');
                const locationSearchElem = document.getElementById('location_search');
                if (locationDisplayElem) {
                    locationDisplayElem.classList.add('border-success');
                    locationDisplayElem.style.borderColor = '#28a745';
                    locationDisplayElem.style.borderWidth = '3px';
                    locationDisplayElem.style.boxShadow = '0 0 10px rgba(40, 167, 69, 0.5)';
                    console.log('[VISUAL] Applied success styling to location_display');
                    
                    // Remove styling after 3 seconds
                    setTimeout(() => {
                        locationDisplayElem.classList.remove('border-success');
                        locationDisplayElem.style.borderColor = '';
                        locationDisplayElem.style.borderWidth = '';
                        locationDisplayElem.style.boxShadow = '';
                    }, 3000);
                }
                if (locationSearchElem) {
                    locationSearchElem.classList.add('border-success');
                    locationSearchElem.style.borderColor = '#28a745';
                    locationSearchElem.style.borderWidth = '3px';
                    locationSearchElem.style.boxShadow = '0 0 10px rgba(40, 167, 69, 0.5)';
                    
                    setTimeout(() => {
                        locationSearchElem.classList.remove('border-success');
                        locationSearchElem.style.borderColor = '';
                        locationSearchElem.style.borderWidth = '';
                        locationSearchElem.style.boxShadow = '';
                    }, 3000);
                }
                
                // Auto-scroll to location fields to show user
                const locationSection = document.getElementById('location_display');
                if (locationSection) {
                    locationSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    console.log('[SCROLL] Scrolled to location_display');
                }
                
                // Calculate shipping cost after location confirmed
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

        console.log('[CALC] === SHIPPING COST CALCULATION START (Multi-Store) ===');
        console.log('[CALC] method:', method);
        console.log('[CALC] destination:', destinationCity, ',', destinationProvince);

        // No method selected - hide shipping
        if (!method) {
            console.log('[CALC] ❌ No shipping method selected');
            hideShippingCost();
            return;
        }

        // Address NOT filled - hide shipping
        if (!lat || !lng || !destinationCity) {
            console.log('[CALC] ⚠️ Address incomplete - hiding shipping');
            hideShippingCost();
            return;
        }

        // GROUP ITEMS BY STORE
        const storeGroups = {};
        document.querySelectorAll('.cart-item').forEach(item => {
            const select = item.querySelector('.item-store-select');
            const storeId = select?.value;
            
            if (!storeId) {
                console.warn('[CALC] ⚠️ Item has no store selected:', item.dataset.bookId);
                return;
            }
            
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
            console.log(`[CALC] Book #${item.dataset.bookId}: ${weightGrams}g × ${quantity} = ${itemTotal}g`);
        });

        const storeIds = Object.keys(storeGroups);
        console.log('[CALC] Grouped into', storeIds.length, 'store(s):', storeIds);
        storeIds.forEach(sid => {
            console.log(`[CALC] Store #${sid}: Total Weight = ${storeGroups[sid].weight_grams}g = ${(storeGroups[sid].weight_grams/1000).toFixed(3)}kg`);
        });
        
        if (storeIds.length === 0) {
            console.log('[CALC] ❌ No items with selected stores');
            hideShippingCost();
            return;
        }

        // CALCULATE SHIPPING FOR EACH STORE
        let totalShippingCost = 0;
        // Make shippingByStore global so form submission can access it for breakdown data
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
            
            console.log(`[CALC] API Request Store #${storeId}:`, {
                weight_grams_raw: group.weight_grams,
                weight_grams_sent: payload.weight_grams,
                weight_kg: (payload.weight_grams / 1000).toFixed(3),
                items_count: group.items.length,
                items: group.items
            });
            
            const response = await axios.post('{{ route("checkout.calculate-shipping") }}', payload, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
            });

            if (response.data.success) {
                const cost = response.data.cost;
                const breakdown = response.data.breakdown || {};
                
                console.log(`[CALC] API Response Store #${storeId}:`, {
                    cost: response.data.cost,
                    zone: response.data.zone,
                    breakdown: breakdown
                });
                
                window.shippingByStore[storeId] = {
                    cost: cost,
                    display: response.data.display,
                    zone: response.data.zone,
                    breakdown: breakdown,
                    note: response.data.note,
                    origin_province: response.data.origin_province,
                    destination_province: response.data.destination_province
                };
                
                totalShippingCost += cost;
                
                console.log(`[CALC] ✓ Store #${storeId}: ${response.data.display} (Zone ${breakdown.zone}) | ${response.data.origin_province} → ${response.data.destination_province}`);
            } else {
                console.error(`[CALC] ❌ Store #${storeId} failed:`, response.data.error);
                hideShippingCost();
                return;
            }
        }

        console.log('[CALC] ✓ All stores calculated - Total:', 'Rp ' + totalShippingCost.toLocaleString('id-ID'));
        console.log('[CALC] Per-store breakdown:', window.shippingByStore);
        
        // Update display with per-store breakdown
        updateTotalsDisplay(totalShippingCost, 'Rp ' + totalShippingCost.toLocaleString('id-ID'), window.shippingByStore);
        
        // Also display detailed per-store breakdown below subtotal
        displayPerStoreShippingBreakdown(window.shippingByStore);
        
        console.log('[CALC] === SHIPPING COST CALCULATION END ===');
    } catch (error) {
        console.error('[CALC] ❌ ERROR:', error.message);
        console.error('[CALC] Response:', error.response?.data);
        hideShippingCost();
    }
}

function displayPerStoreShippingBreakdown(shippingByStore) {
    // Find or create the breakdown container - insert after shipping cost section
    let breakdownContainer = document.getElementById('perStoreShippingBreakdown');
    
    if (!breakdownContainer) {
        breakdownContainer = document.createElement('div');
        breakdownContainer.id = 'perStoreShippingBreakdown';
        
        // Find the shipping cost div and insert after it
        const shippingDisplay = document.getElementById('shippingDisplay');
        if (shippingDisplay && shippingDisplay.parentElement) {
            shippingDisplay.parentElement.parentElement.insertAdjacentElement('afterend', breakdownContainer);
        } else {
            // Fallback: insert before grand total
            const grandTotal = document.getElementById('grandTotalDisplay');
            if (grandTotal) {
                grandTotal.closest('.d-flex').insertAdjacentElement('beforebegin', breakdownContainer);
            } else {
                console.error('[DISPLAY] Could not find insertion point for breakdown');
                return;
            }
        }
    }

    // Zone category labels
    const zoneLabels = {
        'A': 'Same Province',
        'B': 'Same Province, Different City',
        'C': 'Same Island, Different Province',
        'D': 'Different Main Islands',
        'E': 'Remote Area'
    };

    // Build HTML for each store
    let html = '<div class="p-3 my-3 rounded-2" style="background: #f0f8f5; border-left: 4px solid #198754;"><p class="mb-2 text-muted small fw-bold">SHIPPING BREAKDOWN PER STORE</p>';
    
    // Build a map of store IDs to store names and books
    const storeInfo = {};
    document.querySelectorAll('.item-store-select').forEach(select => {
        const storeId = parseInt(select.value);
        const bookTitle = select.closest('.cart-item').dataset.bookTitle || select.closest('.cart-item').querySelector('h6')?.textContent || 'Unknown';
        const selectedOption = select.options[select.selectedIndex];
        const storeName = selectedOption?.textContent?.split('(')[0]?.trim() || 'Unknown Store';
        
        if (storeId && shippingByStore[storeId]) {
            if (!storeInfo[storeId]) {
                storeInfo[storeId] = { name: storeName, books: [] };
            }
            if (!storeInfo[storeId].books.includes(bookTitle)) {
                storeInfo[storeId].books.push(bookTitle);
            }
        }
    });
    
    for (const [storeId, data] of Object.entries(shippingByStore)) {
        const b = data.breakdown;
        const totalWeight = (b.weight_kg || 0).toFixed(2);
        const extraWeight = (b.extra_kg || 0).toFixed(2);
        const zoneCategory = zoneLabels[b.zone] || 'Unknown Zone';
        const storeName = storeInfo[storeId]?.name || `Store #${storeId}`;
        const books = storeInfo[storeId]?.books || [];
        
        console.log(`[DISPLAY] Store #${storeId} breakdown:`, {weight_kg: b.weight_kg, extra_kg: b.extra_kg, weight_fee: b.weight_fee, zone_base: b.zone_base, service_surcharge: b.service_surcharge});
        
        // Build weight display: show total, then clarify if there's extra above 1kg
        let weightDisplay = `${totalWeight}kg`;
        if (extraWeight > 0) {
            weightDisplay += ` (above 1kg: ${extraWeight}kg)`;
        }
        
        // Build books list HTML
        let booksHtml = books.length > 0 ? books.map(b => `<div class="ps-2 small text-muted">• ${b}</div>`).join('') : '';
        
        html += `
        <div class="mb-2 p-2" style="background: white; border-left: 3px solid #198754; border-radius: 3px;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong>Store: ${storeName}</strong>
                    ${booksHtml}
                    <small class="d-block text-muted mt-1">Zone <strong style="color: #198754;">${b.zone}</strong> (${zoneCategory})</small>
                </div>
                <strong style="color: #198754; font-size: 1rem;">${data.display}</strong>
            </div>
            <small class="d-block text-muted mb-2">Weight: ${weightDisplay}</small>
            <div class="ps-2" style="font-size: 0.8rem; color: #666;">
                <div class="d-flex justify-content-between mb-1">
                    <span>• Base:</span>
                    <span>Rp ${(b.zone_base || 0).toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>• Weight Fee:</span>
                    <span>Rp ${(b.weight_fee || 0).toLocaleString('id-ID')}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>• Service (${b.service_level || 'N/A'}):</span>
                    <span>Rp ${(b.service_surcharge || 0).toLocaleString('id-ID')}</span>
                </div>
            </div>
        </div>
        `;
    }
    
    html += '</div>';
    breakdownContainer.innerHTML = html;
    breakdownContainer.style.display = 'block';
    
    console.log('[DISPLAY] ✓ Per-store breakdown displayed with', Object.keys(shippingByStore).length, 'store(s)');
}

function hideShippingCost() {
    console.log('[HIDE] hideShippingCost called');
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
    if (perStoreBreakdown) {
        perStoreBreakdown.style.display = 'none';
    }
    
    // Show only subtotal without shipping
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        grandTotalDisplay.style.color = 'inherit';
    }
    currentShippingCost = 0;
}

function showBaseCost(baseCost) {
    console.log('[BASE] showBaseCost called with:', baseCost);
    currentShippingCost = baseCost;
    
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingRate = document.getElementById('shippingRate');
    
    if (shippingDisplay) {
        shippingDisplay.style.cssText = 'display: block !important; visibility: visible !important; font-size: 1.1rem; font-weight: bold; color: #6c757d;';
        shippingDisplay.textContent = baseCost === 0 ? 'FREE' : 'Rp ' + baseCost.toLocaleString('id-ID');
        shippingDisplay.title = '(base cost only - enter full address for distance-based calculation)';
        console.log('[BASE] shippingDisplay updated with base cost:', shippingDisplay.textContent);
    }
    
    if (shippingRate) {
        shippingRate.style.cssText = 'display: block !important; visibility: visible !important;';
        shippingRate.textContent = '(awaiting address...)';
        shippingRate.style.color = '#999';
    }
    
    // Update grand total with base cost
    const grand = subtotal + baseCost;
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + grand.toLocaleString('id-ID');
        console.log('[BASE] Grand total updated to:', grand);
    }
}

function updateTotalsDisplay(totalCost, totalDisplay, shippingByStore) {
    console.log('[UPDATE] Updating totals - total cost:', totalCost, 'per-store breakdown:', shippingByStore);
    
    currentShippingCost = totalCost;
    const grand = subtotal + totalCost;
    
    // Show success badge
    const badge = document.getElementById('shippingCalculatedBadge');
    if (badge) {
        badge.style.display = 'inline-block';
        console.log('[UPDATE] ✓ Showing shipping calculated badge');
    }
    
    // Update main shipping cost display (total)
    const shippingDisplay = document.getElementById('shippingDisplay');
    const shippingRate = document.getElementById('shippingRate');
    
    if (shippingDisplay) {
        shippingDisplay.style.cssText = 'display: block !important; visibility: visible !important; font-size: 1.1rem; font-weight: bold; color: #198754;';
        shippingDisplay.textContent = totalCost === 0 ? 'FREE' : totalDisplay;
        shippingDisplay.title = `Total shipping for ${Object.keys(shippingByStore).length} store(s)`;
        console.log('[UPDATE] ✓ Total shipping cost displayed:', shippingDisplay.textContent);
    }
    
    if (shippingRate) {
        const numStores = Object.keys(shippingByStore).length;
        shippingRate.style.cssText = 'display: block !important; visibility: visible !important;';
        shippingRate.textContent = `${numStores} store${numStores > 1 ? 's' : ''} (see breakdown below)`;
        shippingRate.style.color = '#666';
        shippingRate.style.fontSize = '0.9rem';
        console.log('[UPDATE] ✓ Number of stores displayed:', shippingRate.textContent);
    }
    
    // Update grand total
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = 'Rp ' + grand.toLocaleString('id-ID');
        grandTotalDisplay.style.color = '#198754';
        console.log('[UPDATE] ✓ Grand total:', grandTotalDisplay.textContent);
    }
    
    console.log('[UPDATE] === TOTALS UPDATE COMPLETE ===');
}

// Form validation
function validateCheckoutForm() {
    const requiredFields = [
        { id: 'shipping_name', type: 'text', name: 'Recipient Name' },
        { id: 'shipping_phone', type: 'tel', name: 'Phone Number' },
        { id: 'location_search', type: 'text', name: 'Location' },
        { id: 'shipping_postal_code_input', type: 'select', name: 'Postal Code' },
        { id: 'shipping_address', type: 'textarea', name: 'Street Address' },
        { id: 'shipping-method-0', type: 'radio', name: 'Shipping Method' }
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
    
    // Validate all books have store selections
    const storeSelects = document.querySelectorAll('.item-store-select');
    storeSelects.forEach(select => {
        if (!select.value) {
            select.classList.add('field-error-highlight');
            emptyFields.push('Store location for ' + select.dataset.bookId);
            isValid = false;
        } else {
            select.classList.remove('field-error-highlight');
            
            // CRITICAL: Validate quantity does not exceed store stock
            const cartItem = select.closest('.cart-item');
            const quantity = parseInt(cartItem.dataset.quantity || 1);
            const selectedOption = select.options[select.selectedIndex];
            const stock = parseInt(selectedOption.dataset.stock || 0);
            
            if (quantity > stock) {
                stockErrors.push(`"${cartItem.dataset.bookTitle}" - Need ${quantity} but only ${stock} available at selected store`);
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        let message = 'Please fix the following errors:\n\n';
        if (emptyFields.length > 0) {
            message += 'Required fields:\n' + emptyFields.map(f => '• ' + f).join('\n');
        }
        if (stockErrors.length > 0) {
            if (emptyFields.length > 0) message += '\n\n';
            message += 'Inventory issues:\n' + stockErrors.map(f => '• ' + f).join('\n');
        }
        alert(message);
    }
    
    return isValid;
}


// Real-time validation
['shipping_name', 'shipping_phone', 'location_search', 'shipping_postal_code_input', 'shipping_address'].forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('field-error-highlight');
            }
        });
        field.addEventListener('change', function() {
            if (this.value.trim()) {
                this.classList.remove('field-error-highlight');
            }
        });
    }
});

// Validate store selection on change
document.querySelectorAll('.item-store-select').forEach(select => {
    select.addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('field-error-highlight');
        }
    });
});

// Pay button
document.getElementById('payButton').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (!validateCheckoutForm()) {
        return;
    }
    
    const payButton = this;
    payButton.disabled = true;
    document.getElementById('buttonText').classList.add('d-none');
    document.getElementById('loadingSpinner').classList.remove('d-none');

    const formData = new FormData(document.getElementById('paymentForm'));
    formData.append('shipping_cost', currentShippingCost);
    
    // CRITICAL: Send zone-based shipping breakdown for database storage
    // This allows order display to show detailed breakdown instead of just recalculating
    if (window.shippingByStore && Object.keys(window.shippingByStore).length > 0) {
        // Get the first store's breakdown as the "primary" breakdown
        const firstStoreId = Object.keys(window.shippingByStore)[0];
        const firstStoreData = window.shippingByStore[firstStoreId];
        
        if (firstStoreData && firstStoreData.breakdown) {
            formData.append('shipping_zone', firstStoreData.breakdown.zone || 'C');
            formData.append('shipping_breakdown', JSON.stringify(firstStoreData.breakdown));
            console.log('[SUBMIT] Including shipping breakdown:', firstStoreData.breakdown);
        }
    }

    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        body: formData,
        headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => {
        if (!r.ok && r.status === 422) {
            return r.json().then(data => {
                const errorMessages = [];
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        errorMessages.push(`${field}: ${data.errors[field].join(', ')}`);
                    });
                }
                throw new Error(errorMessages.join('\n') || 'Validation failed');
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success && data.snapToken) {
            snap.pay(data.snapToken, {
                onSuccess: function(result) {
                    console.log('[PAYMENT] Success callback triggered', result);
                    
                    // Mark payment as complete on backend, passing payment_type from Midtrans result
                    fetch('{{ route("checkout.mark-payment-complete") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ payment_type: result.payment_type || null })
                    })
                    .then(response => {
                        console.log('[PAYMENT] markPaymentComplete response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`Payment complete call failed: ${response.status} ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('[PAYMENT] markPaymentComplete success:', data);
                        
                        // Save address to user record
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
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(addressData)
                        });
                    })
                    .then(response => {
                        console.log('[PAYMENT] saveAddress response:', response.status);
                        return response.json();
                    })
                    .then(() => {
                        // Clear cart
                        return fetch('{{ route("cart.clear") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        });
                    })
                    .then(() => {
                        console.log('[PAYMENT] Cart cleared:', response.status);
                        // Force refresh to bypass cache
                        window.location.href = '{{ route("orders.index") }}?success=true&t=' + Date.now();
                    })
                    .catch(error => {
                        console.error('[PAYMENT] Error during post-payment operations:', error);
                        // Payment already succeeded and order status updated, just redirect
                        // Errors here are non-critical (address save or cart clear failures)
                        window.location.href = '{{ route("orders.index") }}?success=true&t=' + Date.now();
                    });
                },
                onPending: function() {
                    alert('Payment is being processed. Your order is pending until payment is confirmed. Please check your orders page for updates.');
                    resetBtn();
                },
                onError: function() {
                    alert('Payment authentication failed. Your order remains pending. You can retry payment from your orders page.');
                    resetBtn();
                },
                onClose: function() { 
                    alert('You closed the payment window. Your order remains pending. Complete payment from your orders page or retry checkout.');
                    resetBtn(); 
                }
            });
        } else {
            alert('Error: ' + (data.error || data.message || 'Failed to initiate payment.'));
            resetBtn();
        }
    })
    .catch(err => { 
        alert('Checkout Error:\n\n' + (err.message || 'An unexpected error occurred. Please try again.'));
        console.error('Checkout error:', err);
        resetBtn();
    });

    function resetBtn() {
        payButton.disabled = false;
        document.getElementById('buttonText').classList.remove('d-none');
        document.getElementById('loadingSpinner').classList.add('d-none');
    }
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    console.log('[INIT] DOMContentLoaded fired');
    
    // Initialize location search elements NOW that DOM is loaded
    locationSearchInput = document.getElementById('location_search');
    locationSuggestions = document.getElementById('locationSuggestions');
    postalCodeSelect = document.getElementById('shipping_postal_code_input');
    
    console.log('[INIT] Location search input:', !!locationSearchInput);
    console.log('[INIT] Location suggestions:', !!locationSuggestions);
    
    // Setup location search functionality
    if (locationSearchInput) {
        locationSearchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            locationSuggestions.innerHTML = '';
            
            if (!query || query.length < 2) {
                locationSuggestions.classList.remove('show');
                return;
            }
            
            const filtered = allLocations.filter(loc => {
                const searchText = `${loc.district} ${loc.city} ${loc.province}`.toLowerCase();
                return searchText.includes(query);
            });
            
            if (filtered.length > 0) {
                filtered.forEach(loc => {
                    const div = document.createElement('div');
                    div.className = 'location-suggestion-item';
                    div.innerHTML = `
                        <div class="location-suggestion-label">${loc.district}</div>
                        <div class="location-suggestion-desc">${loc.city}, ${loc.province} - ${loc.postal_code}</div>
                    `;
                    div.addEventListener('click', function() {
                        selectLocation(loc);
                    });
                    locationSuggestions.appendChild(div);
                });
                locationSuggestions.classList.add('show');
            } else {
                locationSuggestions.classList.remove('show');
            }
        });
        
        locationSearchInput.addEventListener('blur', function() {
            setTimeout(() => {
                locationSuggestions.classList.remove('show');
            }, 200);
        });
        
        console.log('[INIT] Location search event listeners attached');
    } else {
        console.error('[INIT] locationSearchInput not found!');
    }
    
    const selectedStore = document.querySelector('.store-radio:checked');
    const selectedMethod = document.querySelector('.shipping-radio:checked');
    
    console.log('[INIT] Store selected:', !!selectedStore, 'Method selected:', !!selectedMethod);
    
    if (selectedStore && selectedMethod) {
        // IMPORTANT: Do NOT fill shipping coordinates with store coordinates
        // Only show base shipping cost until user enters their delivery address
        const baseCost = parseInt(selectedMethod.dataset.baseCost || 0);
        console.log('[INIT] Showing base cost:', baseCost);
        showBaseCost(baseCost);
    }
    
    // Initialize per-km rates for all methods with base costs
    console.log('[INIT] Initializing per-km rates for all shipping methods');
    document.querySelectorAll('.shipping-radio').forEach(radio => {
        const methodCode = radio.value;
        const baseCost = parseInt(radio.dataset.baseCost || 0);
        const rateDisplay = document.querySelector(`.shipping-rate[data-method="${methodCode}"]`);
        
        if (rateDisplay && baseCost > 0) {
            // Show base rate as estimate
            rateDisplay.style.cssText = 'display: block !important; color: #999; font-size: 0.85rem;';
            rateDisplay.textContent = `(loading rates...)`;
            console.log(`[INIT] Method ${methodCode} rate display initialized`);
        }
    });
    
    // Trigger calculation if everything is already filled (e.g., from saved address)
    const userLat = document.getElementById('shipping_latitude').value;
    const userLng = document.getElementById('shipping_longitude').value;
    const userCity = document.getElementById('shipping_city').value;
    if (userLat && userLng && userCity && selectedStore && selectedMethod) {
        console.log('[INIT] Auto-calculating with pre-filled address');
        setTimeout(() => calculateShippingCost(), 100);
    }
    

    
    // Handle saved address toggle
    @if($savedAddress)
    const useSavedBtn = document.getElementById('useSavedAddress');
    const enterNewBtn = document.getElementById('enterNewAddress');
    const newAddressSection = document.getElementById('newAddressSection');
    const addressFormFields = document.getElementById('addressFormFields');
    
    if (useSavedBtn && enterNewBtn) {
        useSavedBtn.addEventListener('change', function() {
            if (this.checked) {
                // Pre-fill form with saved address
                document.getElementById('shipping_latitude').value = {{ json_encode($savedAddress['latitude']) }};
                document.getElementById('shipping_longitude').value = {{ json_encode($savedAddress['longitude']) }};
                document.getElementById('shipping_street').value = '{{ $savedAddress['street'] }}';
                document.getElementById('shipping_postal_code_input').value = '{{ $savedAddress['postal_code'] }}';
                document.getElementById('shipping_province').value = '{{ $savedAddress['province'] }}';
                document.getElementById('shipping_city').value = '{{ $savedAddress['city'] }}';
                document.getElementById('shipping_district').value = '{{ $savedAddress['district'] }}';
                
                // Display the saved address in the location display
                let displayText = '{{ $savedAddress['district'] }}, {{ $savedAddress['city'] }}, {{ $savedAddress['province'] }}';
                document.getElementById('location_display').value = displayText;
                
                // Show the edit map button
                const editBtn = document.getElementById('mapPickerBtn');
                if (editBtn) {
                    editBtn.style.display = 'block';
                }
                
                // Recalculate shipping with saved address
                calculateShippingCost();
            }
        });
        
        enterNewBtn.addEventListener('change', function() {
            if (this.checked) {
                // Clear form fields to allow new entry
                document.getElementById('shipping_latitude').value = '';
                document.getElementById('shipping_longitude').value = '';
                document.getElementById('shipping_street').value = '';
                document.getElementById('shipping_postal_code_input').value = '';
                document.getElementById('shipping_province').value = '';
                document.getElementById('shipping_city').value = '';
                document.getElementById('shipping_district').value = '';
                document.getElementById('location_search').value = '';
                document.getElementById('location_display').value = '';
                
                // Hide the edit map button
                const editBtn = document.getElementById('mapPickerBtn');
                if (editBtn) {
                    editBtn.style.display = 'none';
                }
                
                // Hide shipping cost until new address is entered
                hideShippingCost();
            }
        });
    }
    @endif
    
    // CRITICAL: Debounce function to prevent API call spam on rapid address changes
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Create debounced shipping calculation (500ms delay)
    const debouncedCalculateShipping = debounce(calculateShippingCost, 500);
    
    // Store selection changes - validate and update location
    document.querySelectorAll('.item-store-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const bookId = this.dataset.bookId;
            const selectedOption = this.options[this.selectedIndex];
            const storeStock = parseInt(selectedOption.dataset.stock || 0);
            const cartItem = this.closest('.cart-item');
            const currentQuantity = parseInt(cartItem.dataset.quantity || 1);
            const messageEl = this.closest('div').querySelector('.item-store-message');
            const requiredBadge = this.closest('div').querySelector('.item-store-required');
            
            if (!this.value) {
                // No store selected - show error, add required attribute
                this.setAttribute('required', 'required');
                if (requiredBadge) requiredBadge.style.display = 'inline';
                if (messageEl) {
                    const errSpan = messageEl.querySelector('.text-danger');
                    const okSpan = messageEl.querySelector('.text-success');
                    if (errSpan) errSpan.style.display = 'inline';
                    if (okSpan) okSpan.style.display = 'none';
                    messageEl.style.display = 'block';
                }
                return;
            }
            
            // Store selected - remove required attribute, show success
            this.removeAttribute('required');
            if (requiredBadge) requiredBadge.style.display = 'none';
            if (messageEl) {
                const errSpan = messageEl.querySelector('.text-danger');
                const okSpan = messageEl.querySelector('.text-success');
                if (errSpan) errSpan.style.display = 'none';
                if (okSpan) okSpan.style.display = 'inline';
                messageEl.style.display = 'block';
            }
            
            if (storeStock <= 0) {
                alert('⚠️ Selected store has no stock for this book');
                this.value = '';
                this.setAttribute('required', 'required');
                return;
            }
            
            // Check if quantity exceeds store stock
            if (currentQuantity > storeStock) {
                alert(`⚠️ Selected store only has ${storeStock} stock but your quantity is ${currentQuantity}.\n\nPlease go back to cart and adjust quantity to max ${storeStock}.`);
                console.log(`[STORE] Stock mismatch: Qty ${currentQuantity} > Stock ${storeStock}`);
            }
            
            // If we have location data already, recalculate shipping with first selected store
            const userLat = document.getElementById('shipping_latitude').value;
            const userLng = document.getElementById('shipping_longitude').value;
            
            if (userLat && userLng) {
                // Update current store location to first selected store
                let firstSelect = null;
                document.querySelectorAll('.item-store-select').forEach(select => {
                    if (!firstSelect && select.value) {
                        firstSelect = select;
                    }
                });
                
                if (firstSelect) {
                    const selectedOption = firstSelect.options[firstSelect.selectedIndex];
                    currentStoreLocation = {
                        lat: parseFloat(selectedOption.dataset.lat),
                        lng: parseFloat(selectedOption.dataset.lng)
                    };
                    calculateShippingCost();
                }
            }
        });
    });

    // Shipping method changes
    document.querySelectorAll('.shipping-radio').forEach(function(radio) {
        radio.addEventListener('change', calculateShippingCost);
    });
    
    // Watch for changes on ALL address/location fields that affect shipping
    // CRITICAL: Use debounce to prevent API spam on rapid consecutive changes
    const addressFields = [
        'shipping_latitude',
        'shipping_longitude', 
        'shipping_city',
        'shipping_province',
        'shipping_district',
        'shipping_street',
        'shipping_postal_code_input'
    ];
    
    addressFields.forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                console.log('[ADDRESS] Changed:', fieldId, '=', this.value);
                debouncedCalculateShipping();
            });
            
            // Also watch for input/blur to catch manual edits
            field.addEventListener('blur', function() {
                console.log('[ADDRESS] Blur on:', fieldId);
                debouncedCalculateShipping();
            });
        }
    });
    
    // Watch for dropdown changes (province/city selects)
    document.querySelectorAll('select[id*="shipping"]').forEach(function(select) {
        select.addEventListener('change', function() {
            if (!this.classList.contains('item-store-select')) {
                console.log('[SELECT] Changed:', this.id, '=', this.value);
                debouncedCalculateShipping();
            }
        });
    });
});
</script>
@endsection

