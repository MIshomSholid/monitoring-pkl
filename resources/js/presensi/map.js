let userMarker = null;
let pklMarker = null;

export function initMap(containerId, lat, lng, radius, pklLat = null, pklLng = null) {
    const el = document.getElementById(containerId);
    if (!el) return null;

    const map = L.map(el).setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // circle radius (biru transparan)
    L.circle([lat, lng], {
        radius,
        color: 'blue',
        fillColor: '#3b82f6',
        fillOpacity: 0.2
    }).addTo(map);

    // ICON BASE (SAMA)
    const baseIcon = 'https://cdn-icons-png.flaticon.com/512/684/684908.png';

    // ICON PKL (MERAH)
    const pklIcon = L.icon({
        iconUrl: baseIcon,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        className: 'marker-red'
    });

    // ICON USER (BIRU)
    const userIcon = L.icon({
        iconUrl: baseIcon,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        className: 'marker-blue'
    });

    // simpan global
    window.userIcon = userIcon;

    // MARKER PKL
    if (pklLat && pklLng) {
        pklMarker = L.marker([pklLat, pklLng], { icon: pklIcon })
            .addTo(map)
            .bindPopup("Lokasi PKL");
    }

    // TAMBAH LEGEND
    const legend = L.control({ position: 'bottomleft' });

    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = `
            <div style="
                background: white;
                padding: 10px;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                font-size: 13px;
            ">
                <b>Keterangan</b><br>
                <div style="display:flex;align-items:center;margin-top:5px;">
                    <img src="${baseIcon}" width="18" class="marker-blue" style="margin-right:6px;">
                    Posisi Siswa
                </div>
                <div style="display:flex;align-items:center;margin-top:5px;">
                    <img src="${baseIcon}" width="18" class="marker-red" style="margin-right:6px;">
                    Lokasi PKL
                </div>
            </div>
        `;
        return div;
    };

    legend.addTo(map);

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