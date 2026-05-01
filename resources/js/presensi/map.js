let userMarker = null;
let pklMarker = null;

export function initMap(containerId, lat, lng, radius, pklLat = null, pklLng = null) {
    const el = document.getElementById(containerId);
    if (!el) return null;

    const map = L.map(el).setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // 🔵 circle radius (biru transparan)
    L.circle([lat, lng], {
        radius,
        color: 'blue',
        fillColor: '#3b82f6',
        fillOpacity: 0.2
    }).addTo(map);

    // ICON PKL 
    const pklIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    // ICON USER 
    const userIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    // simpan ke global biar bisa dipakai di updateUserMarker
    window.userIcon = userIcon;

    // MARKER PKL
    if (pklLat && pklLng) {
        pklMarker = L.marker([pklLat, pklLng], { icon: pklIcon })
            .addTo(map)
            .bindPopup("Lokasi PKL");
    }

    return map;
}

export function updateUserMarker(map, lat, lng) {
    if (!map) return;

    if (!userMarker) {
        userMarker = L.marker([lat, lng], {
            icon: window.userIcon
        }).addTo(map)
          .bindPopup("Lokasi Anda");
    } else {
        userMarker.setLatLng([lat, lng]);
    }
}