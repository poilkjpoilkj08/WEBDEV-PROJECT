

<div class="card shadow-sm border-0 mb-5" id="store-map-section">
    <div class="card-body">
        <h2 class="h5 mb-1">
            <i class="fas fa-map-marker-alt text-danger me-2"></i>Find This Book Near You
        </h2>
        <p class="text-muted small mb-3">Stores that currently carry <strong>{{ $book->title }}</strong></p>

        {{-- Map container --}}
        <div id="book-map" style="height: 400px; border-radius: 12px; overflow: hidden; z-index: 0;"></div>

        {{-- Store list below the map --}}
        <div id="store-list" class="mt-3 row g-2"></div>
    </div>
</div>

{{-- Leaflet CSS & JS (free, no API key needed) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Default center: Jakarta
    const map = L.map('book-map').setView([-6.2, 106.8], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const storeList = document.getElementById('store-list');

    // Custom red marker
    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    fetch('/api/stores/book/{{ $book->id }}')
        .then(r => r.json())
        .then(stores => {
            if (!stores.length) {
                storeList.innerHTML = '<p class="text-muted">No store locations found for this book yet.</p>';
                return;
            }

            const bounds = [];

            stores.forEach(store => {
                const lat = parseFloat(store.latitude);
                const lng = parseFloat(store.longitude);
                bounds.push([lat, lng]);

                const marker = L.marker([lat, lng], { icon: redIcon }).addTo(map);
                marker.bindPopup(`
                    <strong>${store.name}</strong><br>
                    ${store.address}, ${store.city}<br>
                    ${store.phone ? '📞 ' + store.phone + '<br>' : ''}
                    ${store.opening_hours ? '🕐 ' + store.opening_hours + '<br>' : ''}
                    <span class="badge bg-success">In Stock: ${store.stock}</span>
                `);

                // Store card
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <div class="border rounded-3 p-3 h-100" style="cursor:pointer" onclick="flyTo(${lat}, ${lng})">
                        <div class="fw-bold mb-1"><i class="fas fa-store text-primary me-1"></i>${store.name}</div>
                        <div class="text-muted small">${store.address}</div>
                        <div class="text-muted small">${store.city}</div>
                        ${store.phone ? `<div class="small mt-1">📞 ${store.phone}</div>` : ''}
                        ${store.opening_hours ? `<div class="small">🕐 ${store.opening_hours}</div>` : ''}
                        <span class="badge bg-success mt-2">Stock: ${store.stock}</span>
                    </div>`;
                storeList.appendChild(col);
            });

            if (bounds.length) map.fitBounds(bounds, { padding: [50, 50] });
        })
        .catch(() => {
            storeList.innerHTML = '<p class="text-danger">Could not load store locations.</p>';
        });

    // Try to show user's location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const { latitude, longitude } = pos.coords;
            L.circleMarker([latitude, longitude], {
                radius: 8, color: '#0d6efd', fillColor: '#0d6efd', fillOpacity: 0.7
            }).addTo(map).bindPopup('📍 Your location');
        });
    }

    window.flyTo = function(lat, lng) {
        map.flyTo([lat, lng], 14, { duration: 1 });
    };
});
</script>
