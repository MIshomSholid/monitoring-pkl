<div class="bg-white p-6 rounded-lg shadow max-w-2xl space-y-5">

    {{-- STATUS --}}
    <div class="bg-gray-50 border rounded p-3">
        <p id="statusLokasi" class="text-sm text-gray-600">
            Mengambil lokasi GPS…
        </p>
    </div>

    {{-- MAP --}}
    <div>
        <p class="text-sm font-medium text-gray-700 mb-2">
            Peta Lokasi Presensi
        </p>
        <div id="map" class="w-full h-64 rounded border"></div>
    </div>

    {{-- KAMERA --}}
    <div>
        <p class="text-sm font-medium text-gray-700 mb-2">
            Kamera Presensi
        </p>
        <video id="video" class="w-full h-48 rounded border object-cover" autoplay playsinline>
        </video>
    </div>

    {{-- FORM --}}
    <form id="formHadir" method="POST" action="{{ route('siswa.presensi.hadir') }}" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="file" name="foto_presensi" id="foto_presensi" hidden>
        <canvas id="canvas" class="hidden"></canvas>

        <button type="button" id="btnKirim" onclick="ambilFoto()" disabled
            class="w-full py-2 rounded bg-gray-400 text-white cursor-not-allowed transition">
            Ambil Foto & Kirim Presensi
        </button>
    </form>
</div>

{{-- DATA PKL (DIAMBIL JS) --}}
@if($presensiAktif)
    <div id="presensiData" data-lat="{{ $latPkl }}" data-lng="{{ $lngPkl }}" data-radius="{{ $radius }}">
    </div>
@endif
{{-- ================= SCRIPT KHUSUS PRESENSI HADIR ================= --}}
<script>
    window.ambilFoto = function () {

        // ⛔ PENGAMAN UTAMA
        if (!window.isDalamRadius) {
            alert('Presensi ditolak: Anda berada di luar radius lokasi PKL.');
            return;
        }

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const fotoInput = document.getElementById('foto_presensi');
        const form = document.getElementById('formHadir');

        if (!video || !canvas || !fotoInput || !form) {
            alert('Komponen kamera belum siap');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            if (!blob) {
                alert('Gagal mengambil foto');
                return;
            }

            const file = new File([blob], 'presensi.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;

            form.submit();
        }, 'image/jpeg', 0.9);
    };
</script>