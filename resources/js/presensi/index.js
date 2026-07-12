import { startCamera, stopCamera } from "./camera";
import { startGPS, stopGPS } from "./gps";
import { initMap, updateUserMarker } from "./map";

let watchId = null;
let map = null;

// Menyimpan status apakah siswa berada di dalam radius PKL
window.isDalamRadius = false;

// Fungsi utama saat siswa membuka presensi hadir
window.startPresensiHadir = function () {
    // Mengambil data lokasi PKL dari HTML (data-lat, data-lng, data-radius)
    const data = document.getElementById("presensiData");
    const latitudeInput = document.getElementById("latitude");
    const longitudeInput = document.getElementById("longitude");
    const statusLokasi = document.getElementById("statusLokasi");
    const btnKirim = document.getElementById("btnKirim");
    const videoEl = document.getElementById("video");

    // Validasi apakah data lokasi PKL tersedia
    if (!data || !data.dataset.lat || !data.dataset.lng) {
        console.warn("Presensi tidak aktif / belum masa PKL");
        return;
    }

    // Validasi apakah seluruh elemen halaman sudah tersedia
    if (
        !latitudeInput ||
        !longitudeInput ||
        !statusLokasi ||
        !btnKirim ||
        !videoEl
    ) {
        console.warn("Elemen presensi belum siap");
        return;
    }

    // Mengambil koordinat dan radius PKL dari data HTML
    const PKL_LAT = parseFloat(data.dataset.lat);
    const PKL_LNG = parseFloat(data.dataset.lng);
    const PKL_RADIUS = parseFloat(data.dataset.radius);

    // Validasi koordinat PKL agar tidak bernilai NaN
    if (isNaN(PKL_LAT) || isNaN(PKL_LNG)) {
        console.warn("Koordinat PKL tidak valid");
        return;
    }

    map = initMap("map", PKL_LAT, PKL_LNG, PKL_RADIUS, PKL_LAT, PKL_LNG);

    startCamera(videoEl);

    watchId = startGPS({
        onUpdate: ({ lat, lng }) => {
            latitudeInput.value = lat;
            longitudeInput.value = lng;

            updateUserMarker(map, lat, lng);

            const jarak = hitungJarak(lat, lng, PKL_LAT, PKL_LNG);
            const jarakBulat = Math.round(jarak);

            if (jarak <= PKL_RADIUS) {
                window.isDalamRadius = true;

                btnKirim.disabled = false;
                btnKirim.classList.remove("bg-gray-400", "cursor-not-allowed");
                btnKirim.classList.add("bg-green-600");

                statusLokasi.innerText = `Dalam radius (${jarakBulat} m)`;
            } else {
                window.isDalamRadius = false;

                const selisih = Math.max(0, jarakBulat - PKL_RADIUS);

                btnKirim.disabled = true;
                btnKirim.classList.remove("bg-green-600");
                btnKirim.classList.add("bg-gray-400", "cursor-not-allowed");

                statusLokasi.innerText = `Di luar radius (${selisih} m dari batas lokasi)`;
            }
        },

        onError: (msg) => {
            statusLokasi.innerText = msg;
            btnKirim.disabled = true;
        },
    });
};

window.stopPresensiHadir = function () {
    stopCamera();
    stopGPS(watchId);
};

// Menghitung jarak dua koordinat menggunakan rumus Haversine
function hitungJarak(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;//lintang
    const dLon = ((lon2 - lon1) * Math.PI) / 180;//bujur
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *//rumus harvesine
            Math.sin(dLon / 2) ** 2;

    return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));//mengubah menjadi jarak
}
