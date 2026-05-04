@if ($errors->any())
    <div id="toast-error" class="fixed top-5 right-5 flex items-center gap-2 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg 
            transform translate-x-[120%] opacity-0 transition-all duration-500 ease-in-out z-50">

        <span>❌</span>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<div class="bg-white p-6 rounded shadow max-w-lg">

    <h2 class="text-lg font-semibold mb-4">
        Presensi Izin
    </h2>

    <p class="text-sm text-gray-600 mb-4">
        Silakan isi form di bawah ini untuk mengajukan presensi izin hari ini.
    </p>

    <form method="POST" action="{{ route('siswa.presensi.izin') }}" enctype="multipart/form-data">
        @csrf

        {{-- JENIS IZIN --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Jenis Presensi
            </label>
            <input type="text" value="Izin" disabled class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600">
            <p class="text-xs text-gray-500 mt-1">
                Presensi hari ini akan tercatat sebagai <b>IZIN</b>
            </p>
        </div>

        {{-- KETERANGAN IZIN --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Keterangan Izin <span class="text-red-500">*</span>
            </label>
            <textarea name="keterangan_izin" rows="4" class="w-full border rounded px-3 py-2"
                placeholder="Contoh: Sakit, izin keluarga, dll" required></textarea>
        </div>

        {{-- BUKTI IZIN --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Bukti Izin (Opsional)
            </label>
            <input type="file" name="bukti_izin" class="w-full border rounded px-3 py-2" accept=".jpg,.jpeg,.png,.pdf">
            <p class="text-xs text-gray-500 mt-1">
                Format JPG, PNG, atau PDF (maks. 2MB)
            </p>
        </div>

        {{-- INFO STATUS --}}
        <div class="bg-yellow-50 text-yellow-700 text-sm p-3 rounded mb-4">
            ⏳ Presensi izin akan berstatus
            <b>Menunggu Validasi</b> oleh Pembimbing Lapangan.
        </div>

        {{-- ACTION --}}
        <div class="flex gap-3">
            <button type="button" onclick="showHadir()" class="w-1/2 px-4 py-2 border rounded">
                Kembali
            </button>

            <button type="submit" class="w-1/2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Kirim Presensi Izin
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toast = document.getElementById('toast-error');

        if (toast) {

            // MASUK (dari kanan)
            setTimeout(() => {
                toast.classList.remove('translate-x-[120%]', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            }, 100);

            // KELUAR (lebih smooth)
            setTimeout(() => {
                toast.style.transform = 'translateX(-120%)';
                toast.style.opacity = '0';
            }, 3000);

            // HAPUS
            setTimeout(() => {
                toast.remove();
            }, 3600);
        }
    });
</script>