@if ($errors->any())
    <div id="toast-error"
        class="fixed top-6 right-6 z-50 transform translate-x-full
        bg-white border border-red-200 shadow-xl rounded-xl
        p-4 w-80 flex items-start gap-3">

        {{-- garis kiri --}}
        <div class="flex-shrink-0 w-2 h-10 bg-red-500 rounded-full"></div>

        {{-- isi --}}
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-800">
                Gagal Mengirim
            </p>
            <p class="text-sm text-gray-600 mt-1">
                {{ $errors->first() }}
            </p>
        </div>

        {{-- tombol close --}}
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">
            ×
        </button>
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

        // SLIDE IN (kanan → masuk)
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            toast.style.transition = "all 0.4s ease";
        }, 100);

        // AUTO CLOSE (5 detik biar konsisten)
        setTimeout(closeToast, 5000);
    }
});

function closeToast() {
    const toast = document.getElementById('toast-error');
    if (!toast) return;

    toast.classList.remove('translate-x-0');
    toast.classList.add('translate-x-full');
    toast.style.transition = "all 0.4s ease";

    setTimeout(() => {
        toast.remove();
    }, 400);
}
</script>