{{-- resources/views/admin/store-locations/partials/form.blade.php --}}

{{-- Hidden lat/lng inputs (submitted with form, unchanged controller) --}}
<input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude',  $store?->latitude) }}">
<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $store?->longitude) }}">

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $store?->name) }}" required
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. BookHive Surabaya">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $store?->phone) }}"
               class="form-control @error('phone') is-invalid @enderror" placeholder="+62 31 1234567">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
        <textarea name="address" rows="2" required
                  class="form-control @error('address') is-invalid @enderror"
                  placeholder="Street address">{{ old('address', $store?->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
        <input type="text" name="city" value="{{ old('city', $store?->city) }}" required
               class="form-control @error('city') is-invalid @enderror" placeholder="Surabaya">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
        <input type="text" name="country" value="{{ old('country', $store?->country ?? 'Indonesia') }}" required
               class="form-control @error('country') is-invalid @enderror">
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" value="{{ old('email', $store?->email) }}"
               class="form-control @error('email') is-invalid @enderror" placeholder="store@bookhive.com">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- ── Replaced lat/lng inputs with Google Maps-style picker ── --}}
    <div class="col-12">
        <label class="form-label fw-semibold">
            <i class="fas fa-map-pin me-1 text-danger"></i>Store Location (Pin on Map)
            <span class="text-danger">*</span>
        </label>

        {{-- Search box --}}
        <div class="input-group mb-2">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" id="map-search-input"
                   class="form-control border-start-0"
                   placeholder="Search address or click on the map to drop a pin…">
            <button type="button" class="btn btn-outline-secondary" id="map-search-btn">Search</button>
        </div>

        {{-- Selected coordinate pill --}}
        <div id="coord-display-wrapper" class="mb-2"
             style="{{ ($store?->latitude || old('latitude')) ? '' : 'display:none' }}">
            <span class="badge bg-light text-dark border px-3 py-2 me-1">
                <i class="fas fa-crosshairs me-1 text-primary"></i>
                <span id="coord-lat">{{ old('latitude', $store?->latitude ?? '') }}</span>,
                <span id="coord-lng">{{ old('longitude', $store?->longitude ?? '') }}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-pin-btn" title="Remove pin">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @error('latitude')  <div class="text-danger small mb-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div> @enderror
        @error('longitude') <div class="text-danger small mb-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div> @enderror

        {{-- Map --}}
        <div id="store-map"
             style="width:100%;height:380px;border-radius:10px;border:1px solid #dee2e6;overflow:hidden;z-index:0;"></div>
        <p class="text-muted small mt-1 mb-0">
            <i class="fas fa-info-circle me-1"></i>
            Click anywhere on the map to place a pin, or search for an address above. Drag the pin to adjust precisely.
        </p>
    </div>
    {{-- ── End map picker ── --}}

    <div class="col-md-8">
        <label class="form-label fw-semibold">Opening Hours</label>
        <input type="text" name="opening_hours" value="{{ old('opening_hours', $store?->opening_hours) }}"
               class="form-control @error('opening_hours') is-invalid @enderror"
               placeholder="Mon–Fri 09:00–21:00, Sat–Sun 10:00–22:00">
        @error('opening_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $store?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Active (visible on map)</label>
        </div>
    </div>
</div>

{{-- Leaflet (loaded only if not already in layout) --}}
@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
@endonce

{{-- Map init script --}}
<script>
(function () {
    const INITIAL_LAT  = {{ old('latitude',  $store?->latitude  ?? -7.2575)  }};
    const INITIAL_LNG  = {{ old('longitude', $store?->longitude ?? 112.7521) }};
    const HAS_PIN      = {{ ($store?->latitude || old('latitude')) ? 'true' : 'false' }};

    const latInput     = document.getElementById('latitude');
    const lngInput     = document.getElementById('longitude');
    const coordLat     = document.getElementById('coord-lat');
    const coordLng     = document.getElementById('coord-lng');
    const coordWrapper = document.getElementById('coord-display-wrapper');
    const clearBtn     = document.getElementById('clear-pin-btn');
    const searchInput  = document.getElementById('map-search-input');
    const searchBtn    = document.getElementById('map-search-btn');

    let map, marker;

    function makePinIcon() {
        return L.divIcon({
            className: '',
            html: `<div style="width:32px;height:42px;filter:drop-shadow(0 3px 6px rgba(0,0,0,.4))">
                       <svg viewBox="0 0 32 42" xmlns="http://www.w3.org/2000/svg">
                           <path d="M16 0C7.163 0 0 7.163 0 16c0 10.5 16 26 16 26S32 26.5 32 16C32 7.163 24.837 0 16 0z" fill="#e53935"/>
                           <circle cx="16" cy="16" r="7" fill="white"/>
                       </svg>
                   </div>`,
            iconSize:   [32, 42],
            iconAnchor: [16, 42],
        });
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

    function placePin(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: makePinIcon(), draggable: true }).addTo(map);
            marker.on('dragend', function () {
                const p = marker.getLatLng();
                updateCoords(p.lat, p.lng);
            });
        }
        updateCoords(lat, lng);
    }

    function initMap() {
        map = L.map('store-map').setView([INITIAL_LAT, INITIAL_LNG], HAS_PIN ? 16 : 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        if (HAS_PIN) {
            placePin(INITIAL_LAT, INITIAL_LNG);
        }

        map.on('click', function (e) {
            placePin(e.latlng.lat, e.latlng.lng);
        });
    }

    // Clear pin
    clearBtn.addEventListener('click', function () {
        if (marker) { map.removeLayer(marker); marker = null; }
        latInput.value = '';
        lngInput.value = '';
        coordWrapper.style.display = 'none';
    });

    // Nominatim search (no API key needed)
    function doSearch() {
        const q = searchInput.value.trim();
        if (!q) return;
        searchBtn.disabled   = true;
        searchBtn.innerHTML  = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1`, {
            headers: { 'Accept-Language': 'id,en' }
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                map.setView([lat, lng], 17);
                placePin(lat, lng);
            } else {
                alert('Location not found. Try a more specific address or click the map directly.');
            }
        })
        .catch(() => alert('Search failed. Please check your connection and try again.'))
        .finally(() => {
            searchBtn.disabled  = false;
            searchBtn.innerHTML = 'Search';
        });
    }

    searchBtn.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

    // Wait for Leaflet to be available
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            const check = setInterval(function () {
                if (typeof L !== 'undefined') { clearInterval(check); initMap(); }
            }, 100);
        });
    }
})();
</script>