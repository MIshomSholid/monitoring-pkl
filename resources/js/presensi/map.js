let userMarker = null;

export function initMap(containerId, lat, lng, radius) {
    const el = document.getElementById(containerId);
    if (!el) return null;

    const map = L.map(el).setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    L.circle([lat, lng], { radius }).addTo(map);

    return map;
}

export function updateUserMarker(map, lat, lng) {
    if (!map) return;

    if (!userMarker) {
        userMarker = L.marker([lat, lng]).addTo(map);
    } else {
        userMarker.setLatLng([lat, lng]);
    }
}
