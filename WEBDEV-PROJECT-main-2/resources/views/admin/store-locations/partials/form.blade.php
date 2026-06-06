<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Store Name *</label>
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
        <label class="form-label fw-semibold">Address *</label>
        <textarea name="address" rows="2" required
                  class="form-control @error('address') is-invalid @enderror"
                  placeholder="Street address">{{ old('address', $store?->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">City *</label>
        <input type="text" name="city" value="{{ old('city', $store?->city) }}" required
               class="form-control @error('city') is-invalid @enderror" placeholder="Surabaya">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Country *</label>
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
    <div class="col-md-6">
        <label class="form-label fw-semibold">Latitude *</label>
        <input type="number" name="latitude" step="0.0000001"
               value="{{ old('latitude', $store?->latitude) }}" required
               class="form-control @error('latitude') is-invalid @enderror" placeholder="-7.2575">
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Longitude *</label>
        <input type="number" name="longitude" step="0.0000001"
               value="{{ old('longitude', $store?->longitude) }}" required
               class="form-control @error('longitude') is-invalid @enderror" placeholder="112.7521">
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
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

{{-- Mini map tip --}}
<div class="alert alert-info mt-3 py-2 small">
    <i class="fas fa-map-pin me-1"></i>
    Need coordinates? Right-click a location on
    <a href="https://maps.google.com" target="_blank">Google Maps</a> → "What's here?" to copy lat/lng.
</div>
