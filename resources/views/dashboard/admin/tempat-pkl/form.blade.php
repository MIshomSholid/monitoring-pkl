<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- NAMA PERUSAHAAN --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nama Perusahaan
        </label>
        <input
            type="text"
            name="nama_perusahaan"
            value="{{ old('nama_perusahaan', $tempatPkl->nama_perusahaan ?? '') }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >
    </div>

    {{-- KUOTA --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Kuota Siswa
        </label>
        <input
            type="number"
            name="kuota_siswa"
            min="1"
            value="{{ old('kuota_siswa', $tempatPkl->kuota_siswa ?? 1) }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >
    </div>

    {{-- ALAMAT --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Alamat
        </label>
        <textarea
            name="alamat"
            rows="3"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >{{ old('alamat', $tempatPkl->alamat ?? '') }}</textarea>
    </div>

    {{-- LATITUDE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Latitude
        </label>
        <input
            type="text"
            name="latitude"
            value="{{ old('latitude', $tempatPkl->latitude ?? '') }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >
    </div>

    {{-- LONGITUDE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Longitude
        </label>
        <input
            type="text"
            name="longitude"
            value="{{ old('longitude', $tempatPkl->longitude ?? '') }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >
    </div>

    {{-- RADIUS --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Radius Presensi (meter)
        </label>
        <input
            type="number"
            name="radius_meter"
            min="50"
            value="{{ old('radius_meter', $tempatPkl->radius_meter ?? 300) }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
            required
        >
    </div>

    {{-- JAM MASUK --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Jam Masuk
        </label>
        <input
            type="time"
            name="jam_masuk"
            value="{{ old('jam_masuk', isset($tempatPkl) ? substr($tempatPkl->jam_masuk,0,5) : '') }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
        >
        <p class="text-xs text-gray-500 mt-1">
            Digunakan untuk menentukan keterlambatan presensi
        </p>
    </div>

    {{-- TOLERANSI --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Toleransi Keterlambatan (menit)
        </label>
        <input
            type="number"
            min="0"
            name="toleransi_keterlambatan"
            value="{{ old('toleransi_keterlambatan', $tempatPkl->toleransi_keterlambatan ?? 0) }}"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-indigo-200"
        >
    </div>

    {{-- HARI WAJIB --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Hari Wajib Masuk
        </label>

        @php
            $hariList = [
                'senin' => 'Senin',
                'selasa' => 'Selasa',
                'rabu' => 'Rabu',
                'kamis' => 'Kamis',
                'jumat' => 'Jumat',
                'sabtu' => 'Sabtu',
                'minggu' => 'Minggu',
            ];

            $hariTerpilih = old(
                'hari_wajib',
                $tempatPkl->hari_wajib ?? ['senin','selasa','rabu','kamis','jumat']
            );
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ($hariList as $key => $label)
                <label class="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        name="hari_wajib[]"
                        value="{{ $key }}"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        {{ in_array($key, $hariTerpilih) ? 'checked' : '' }}
                    >
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

</div>
