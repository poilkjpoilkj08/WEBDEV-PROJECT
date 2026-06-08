{{-- resources/views/admin/store-locations/partials/form.blade.php --}}

{{-- Hidden inputs for location hierarchy & coordinates --}}
<input type="hidden" name="province"   id="province"   value="{{ old('province', $store?->province ?? '') }}">
<input type="hidden" name="city"       id="city"       value="{{ old('city', $store?->city ?? '') }}">
<input type="hidden" name="district"   id="district"   value="{{ old('district', $store?->district ?? '') }}">
<input type="hidden" name="latitude"   id="latitude"   value="{{ old('latitude',  $store?->latitude) }}">
<input type="hidden" name="longitude"  id="longitude"  value="{{ old('longitude', $store?->longitude) }}">

<style>
    .location-search-wrapper {
        position: relative;
    }
    .location-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    .location-suggestions.show {
        display: block;
    }
    .location-suggestion-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
        min-height: 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .location-suggestion-item:hover,
    .location-suggestion-item:active {
        background-color: #f8f9fa;
    }
    .location-suggestion-label {
        font-weight: 600;
        color: #333;
        font-size: 0.95rem;
    }
    .location-suggestion-desc {
        font-size: 0.85rem;
        color: #666;
        margin-top: 3px;
    }

    /* Mobile Optimization */
    @media (max-width: 576px) {
        .location-suggestions {
            max-height: 250px;
        }
        
        .location-suggestion-item {
            padding: 14px 12px;
            min-height: 52px;
        }
        
        .location-suggestion-label {
            font-size: 0.9rem;
        }
        
        .location-suggestion-desc {
            font-size: 0.8rem;
        }
        
        #store-map {
            height: 300px !important;
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .form-control,
        .form-select {
            font-size: 16px;
            min-height: 44px;
            padding: 0.5rem 0.75rem;
        }
        
        .btn {
            min-height: 44px;
            font-size: 0.95rem;
            padding: 0.5rem 0.75rem;
        }
        
        .badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.6rem !important;
        }
        
        .alert {
            font-size: 0.85rem;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .row.g-3 {
            row-gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        #store-map {
            height: 350px !important;
        }
        
        .form-control {
            font-size: 16px;
        }
    }
</style>

<div class="row g-3">
    {{-- Store Name --}}
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $store?->name) }}" required
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. BookHive Surabaya">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Contact Phone Number * --}}
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
        <input type="tel" name="phone" value="{{ old('phone', $store?->phone) }}" required
               class="form-control @error('phone') is-invalid @enderror" placeholder="+62 31 1234567">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Street Address --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Street Address <span class="text-danger">*</span></label>
        <textarea name="address" rows="2" required
                  class="form-control @error('address') is-invalid @enderror"
                  placeholder="Street address">{{ old('address', $store?->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Country (locked to Indonesia) --}}
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
        <input type="text" value="Indonesia" readonly required
               class="form-control-plaintext fw-semibold text-dark" style="padding: 0.375rem 0.75rem;">
        <input type="hidden" name="country" value="Indonesia">
    </div>

    {{-- Location Lookup (Single Autocomplete Field) * --}}
    <div class="col-12 col-md-9">
        <label class="form-label fw-semibold">
            <i class="fas fa-search me-1"></i>Location Lookup (Province / City / District) <span class="text-danger">*</span>
        </label>
        <div class="location-search-wrapper position-relative">
            <input type="text" id="location-search" 
                   class="form-control @if($errors->has('province') || $errors->has('city') || $errors->has('district')) is-invalid @endif"
                   placeholder="Type location (e.g. Menteng, Surabaya)..." 
                   autocomplete="off">
            @error('province')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
            @error('city')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
            @error('district')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
            <div id="location-suggestions" class="location-suggestions shadow-sm"></div>
        </div>
    </div>

    {{-- Postal Code * --}}
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">Postal Code <span class="text-danger">*</span></label>
        <input type="text" id="postal-code" name="postal_code" 
               value="{{ old('postal_code', $store?->postal_code ?? '') }}" required
               class="form-control @error('postal_code') is-invalid @enderror"
               placeholder="Auto-populated from lookup">
        @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Email --}}
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" value="{{ old('email', $store?->email) }}"
               class="form-control @error('email') is-invalid @enderror" placeholder="store@bookhive.com">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Coordinate Pinpoint Mapping * --}}
    <div class="col-12">
        <label class="form-label fw-semibold">
            <i class="fas fa-map me-1"></i>Coordinate Pinpoint Mapping <span class="text-danger">*</span>
        </label>

        {{-- Coordinate Display Badge --}}
        <div id="coord-display-wrapper" class="mb-3" style="{{ ($store?->latitude || old('latitude')) ? '' : 'display:none' }}">
            <span class="badge bg-light text-dark border px-3 py-2 me-2">
                <i class="fas fa-crosshairs me-1 text-danger"></i>
                <span id="coord-lat">{{ old('latitude', $store?->latitude ?? '') }}</span>,
                <span id="coord-lng">{{ old('longitude', $store?->longitude ?? '') }}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-pin-btn" title="Remove pin">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>

        @error('latitude')  <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div> @enderror
        @error('longitude') <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div> @enderror

        {{-- Map Container - FIXED sizing and styling --}}
        <div id="store-map" class="border rounded-3 shadow-sm" 
             style="width:100%;height:450px;background:#f0f0f0;position:relative;z-index:1;margin-bottom:1rem;display:block;visibility:visible;opacity:1;">
        </div>

        <div class="alert alert-info alert-sm mb-0">
            <i class="fas fa-info-circle me-2"></i>
            <small>Click on the map to place/move a pin, or search for address above. Location lookup will sync with map automatically.</small>
        </div>
    </div>

    {{-- Opening Hours --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Opening Hours</label>
        <input type="text" name="opening_hours" value="{{ old('opening_hours', $store?->opening_hours) }}"
               class="form-control"
               placeholder="Mon–Fri 09:00–21:00, Sat–Sun 10:00–22:00">
    </div>

    {{-- Active Toggle --}}
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $store?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Active (visible on store map)</label>
        </div>
    </div>
</div>

{{-- Google Maps API --}}
@once
    @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places,geocoding"></script>
    @endpush
@endonce

{{-- Indonesian Locations Data --}}
<script>
const INDONESIAN_LOCATIONS = {!! json_encode($indonesianLocations) !!};
</script>

{{-- Map & Form Management Script with Google Maps & Bidirectional Sync --}}
<script>
(function () {
    // ══════════════════════════════════════════════════════════════
    // Build flattened locations list for autocomplete
    // ══════════════════════════════════════════════════════════════
    
    function buildLocationsList() {
        const locations = [];
        for (const province in INDONESIAN_LOCATIONS) {
            for (const city in INDONESIAN_LOCATIONS[province]) {
                const cityData = INDONESIAN_LOCATIONS[province][city];
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

    // Form element references
    const locationSearch   = document.getElementById('location-search');
    const locationSuggestions = document.getElementById('location-suggestions');
    const postalCodeInput  = document.getElementById('postal-code');
    const provinceInput    = document.getElementById('province');
    const cityInput        = document.getElementById('city');
    const districtInput    = document.getElementById('district');
    const latInput         = document.getElementById('latitude');
    const lngInput         = document.getElementById('longitude');
    const coordLat         = document.getElementById('coord-lat');
    const coordLng         = document.getElementById('coord-lng');
    const coordWrapper     = document.getElementById('coord-display-wrapper');
    const clearBtn         = document.getElementById('clear-pin-btn');
    const mapContainer     = document.getElementById('store-map');

    // Get existing values or defaults (center on Indonesia)
    const INITIAL_LAT      = {{ old('latitude',  $store?->latitude  ?? -6.2088)  }};
    const INITIAL_LNG      = {{ old('longitude', $store?->longitude ?? 106.8456) }};
    const HAS_PIN          = {{ ($store?->latitude || old('latitude')) ? 'true' : 'false' }};

    let map, marker, geocoder, infoWindow;
    let isSettingFromLocation = false;
    let isSettingFromMap = false;

    // ══════════════════════════════════════════════════════════════
    // Initialize Google Maps and Geocoder
    // ══════════════════════════════════════════════════════════════
    
    function initMap() {
        geocoder = new google.maps.Geocoder();
        
        const initialCenter = {
            lat: INITIAL_LAT,
            lng: INITIAL_LNG
        };

        map = new google.maps.Map(mapContainer, {
            zoom: HAS_PIN ? 16 : 12,
            center: initialCenter,
            streetViewControl: false,
            mapTypeControl: true,
            fullscreenControl: true
        });

        infoWindow = new google.maps.InfoWindow();

        // Place initial marker if has coordinates
        if (HAS_PIN) {
            placeMarker(INITIAL_LAT, INITIAL_LNG);
        }

        // Map click handler - place/move marker and reverse geocode
        map.addListener('click', function(e) {
            isSettingFromMap = true;
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            placeMarker(lat, lng);
            reverseGeocodeAndUpdateLookup(lat, lng);
            isSettingFromMap = false;
        });
    }

    // ══════════════════════════════════════════════════════════════
    // Reverse Geocode and Update Location Lookup
    // ══════════════════════════════════════════════════════════════
    
    function reverseGeocodeAndUpdateLookup(lat, lng) {
        // Find closest matching Indonesian location to the clicked/dragged coordinates
        let closestLocation = null;
        let minDistance = Infinity;
        
        allLocations.forEach(loc => {
            const locDistance = Math.sqrt(
                Math.pow(loc.lat - lat, 2) + Math.pow(loc.lng - lng, 2)
            );
            if (locDistance < minDistance) {
                minDistance = locDistance;
                closestLocation = loc;
            }
        });
        
        // Always update to the closest location, no distance threshold
        if (closestLocation) {
            isSettingFromMap = true;
            locationSearch.value = `${closestLocation.district}, ${closestLocation.city}`;
            provinceInput.value = closestLocation.province;
            cityInput.value = closestLocation.city;
            districtInput.value = closestLocation.district;
            postalCodeInput.value = closestLocation.postal_code;
            isSettingFromMap = false;
        }
    }

    // ══════════════════════════════════════════════════════════════
    // Place Marker on Map
    // ══════════════════════════════════════════════════════════════
    
    function placeMarker(lat, lng) {
        updateCoords(lat, lng);
        
        if (marker) {
            marker.setPosition({ lat: lat, lng: lng });
        } else {
            marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                draggable: true,
                title: 'Drag to adjust location, click map to move'
            });
            
            marker.addListener('dragend', function() {
                const pos = marker.getPosition();
                const lat = pos.lat();
                const lng = pos.lng();
                isSettingFromMap = true;
                updateCoords(lat, lng);
                reverseGeocodeAndUpdateLookup(lat, lng);
                isSettingFromMap = false;
            });
        }
        
        map.panTo({ lat: lat, lng: lng });
        map.setZoom(16);
    }

    function updateCoords(lat, lng) {
        const rLat = Math.round(lat * 1e7) / 1e7;
        const rLng = Math.round(lng * 1e7) / 1e7;
        latInput.value       = rLat;
        lngInput.value       = rLng;
        coordLat.textContent = rLat;
        coordLng.textContent = rLng;
        coordWrapper.style.display = '';
    }

    // ══════════════════════════════════════════════════════════════
    // Location Selection Handler (from lookup dropdown)
    // ══════════════════════════════════════════════════════════════
    
    function selectLocation(location) {
        if (isSettingFromMap) return;
        
        isSettingFromLocation = true;
        
        locationSearch.value = `${location.district}, ${location.city}`;
        locationSuggestions.classList.remove('show');
        
        provinceInput.value = location.province;
        cityInput.value = location.city;
        districtInput.value = location.district;
        postalCodeInput.value = location.postal_code;
        
        // Update map to show this location
        if (map && location.lat && location.lng) {
            placeMarker(location.lat, location.lng);
        }
        
        isSettingFromLocation = false;
    }

    // ══════════════════════════════════════════════════════════════
    // Autocomplete Search Handler
    // ══════════════════════════════════════════════════════════════
    
    if (locationSearch) {
        locationSearch.addEventListener('input', function() {
            if (isSettingFromMap) return;
            
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
        
        locationSearch.addEventListener('blur', function() {
            setTimeout(() => { locationSuggestions.classList.remove('show'); }, 200);
        });
    }

    // ══════════════════════════════════════════════════════════════
    // Clear Pin Button Handler
    // ══════════════════════════════════════════════════════════════
    
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
            latInput.value = '';
            lngInput.value = '';
            coordWrapper.style.display = 'none';
            locationSearch.value = '';
            provinceInput.value = '';
            cityInput.value = '';
            districtInput.value = '';
            postalCodeInput.value = '';
        });
    }

    // ══════════════════════════════════════════════════════════════
    // Initialize map when Google Maps is available
    // ══════════════════════════════════════════════════════════════
    
    if (typeof google !== 'undefined' && google.maps) {
        setTimeout(initMap, 100);
    } else {
        const checkInterval = setInterval(function() {
            if (typeof google !== 'undefined' && google.maps) {
                clearInterval(checkInterval);
                setTimeout(initMap, 100);
            }
        }, 100);
    }
})();
</script>
