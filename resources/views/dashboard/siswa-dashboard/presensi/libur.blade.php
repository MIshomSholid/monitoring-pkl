<div class="bg-white border rounded-lg p-6 shadow-sm max-w-xl">

    <h3 class="text-lg font-semibold mb-2">
        Presensi Libur / Tanggal Merah
    </h3>

    <p class="text-sm text-gray-600 mb-5">
        Gunakan presensi ini jika hari ini merupakan hari libur resmi atau tanggal merah.
        Presensi akan dicatat sebagai <strong>Libur</strong> dan menunggu validasi pembimbing.
    </p>

    <form method="POST" action="{{ route('siswa.presensi.libur') }}">
        @csrf

        {{-- JENIS PRESENSI --}}
        <input type="hidden" name="jenis_presensi" value="libur">

        {{-- TANGGAL --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">
                Tanggal Libur
            </label>
            <input
                type="date"
                name="tanggal"
                value="{{ now()->toDateString() }}"
                readonly
                class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
        </div>

        {{-- KETERANGAN --}}
        <div class="mb-5">
            <label class="block text-sm font-medium mb-1">
                Keterangan (opsional)
            </label>
            <textarea
                name="keterangan"
                rows="3"
                placeholder="Contoh: Hari libur nasional / tanggal merah"
                class="w-full border rounded px-3 py-2"></textarea>
        </div>

        {{-- INFO --}}
        <div class="mb-5 p-3 rounded bg-gray-50 border text-sm text-gray-600">
            • Tidak memerlukan foto kehadiran<br>
            • Akan menunggu validasi pembimbing lapangan<br>
            • Jika ditolak → dianggap <strong>Alpha</strong>
        </div>

        {{-- SUBMIT --}}
        <div class="flex justify-end gap-3">
            <button
                type="button"
                onclick="document.getElementById('liburBox').classList.add('hidden')"
                class="px-4 py-2 border rounded hover:bg-gray-100">
                Batal
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                Ajukan Presensi Libur
            </button>
        </div>
    </form>
</div>
