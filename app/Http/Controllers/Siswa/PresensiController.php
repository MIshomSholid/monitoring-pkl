<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Presensi;
use Cloudinary\Cloudinary;

class PresensiController extends Controller
{
    /* ================= HALAMAN UTAMA ================= */
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $penempatan = $siswa?->penempatanAktif;

        if (!$penempatan) {
            return view('dashboard.siswa-dashboard.presensi.index', [
                'presensiAktif' => false,
                'alasan' => 'Anda belum memiliki penempatan PKL.',
                'presensiHariIni' => null,
                'riwayatPresensi' => collect(),

                // TAMBAHAN WAJIB
                'latPkl' => null,
                'lngPkl' => null,
                'radius' => 0,
            ]);
        }

        $tempat = $penempatan->tempatPkl;

        if (!$penempatan->isAktifHariIni()) {
            return view('dashboard.siswa-dashboard.presensi.index', [
                'presensiAktif' => false,
                'alasan' => 'Presensi hanya dapat dilakukan selama masa PKL.',
                'presensiHariIni' => null,
                'riwayatPresensi' => collect(),

                // TAMBAHAN WAJIB
                'latPkl' => null,
                'lngPkl' => null,
                'radius' => 0,
            ]);
        }

        if ($this->hariIniTidakWajib($tempat)) {
            return view('dashboard.siswa-dashboard.presensi.index', [
                'presensiAktif' => false,
                'alasan' => 'Hari ini bukan hari wajib PKL.',
                'presensiHariIni' => null,
                'riwayatPresensi' => collect(),
                'latPkl' => null,
                'lngPkl' => null,
                'radius' => 0,
            ]);
        }

        $today = now()->toDateString();

        $presensiHariIni = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->where('tanggal', $today)
            ->first();

        $riwayatPresensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.siswa-dashboard.presensi.index', [
            'presensiAktif' => true,
            'penempatan' => $penempatan,
            'presensiHariIni' => $presensiHariIni,
            'riwayatPresensi' => $riwayatPresensi,
            'latPkl' => $penempatan->tempatPkl->latitude,
            'lngPkl' => $penempatan->tempatPkl->longitude,
            'radius' => $penempatan->tempatPkl->radius_meter,
        ]);
    }

    /* ================= PRESENSI HADIR ================= */
    public function hadir(Request $request)
    {
        $request->validate([
            'foto_presensi' => 'required|image|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $penempatan = $this->getPenempatanAktifAtauGagal();
        $today = now()->toDateString();

        $tempat = $penempatan->tempatPkl;

        if ($this->hariIniTidakWajib($tempat)) {
            return back()->withErrors([
                'presensi' => 'Hari ini bukan hari wajib PKL.'
            ]);
        }

        return DB::transaction(function () use ($request, $penempatan, $today) {

            $sudahPresensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->exists();

            if ($sudahPresensi) {
                return back()->withErrors([
                    'presensi' => 'Anda sudah melakukan presensi hari ini'
                ]);
            }

            $tempat = $penempatan->tempatPkl;

            $jarak = $this->hitungJarak(
                $request->latitude,
                $request->longitude,
                $tempat->latitude,
                $tempat->longitude
            );

            if ($jarak > $tempat->radius_meter) {
                return back()->withErrors([
                    'lokasi' => 'Anda berada di luar jangkauan absensi (' . round($jarak) . ' meter)'
                ]);
            }

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $upload = $cloudinary->uploadApi()->upload(
                $request->file('foto_presensi')->getRealPath(),
                [
                    'folder' => 'presensi',
                ]
            );

            $fotoPath = $upload['secure_url'];

            $presensi = Presensi::create([
                'penempatan_pkl_id' => $penempatan->id,
                'tanggal' => $today,
                'jenis_presensi' => 'hadir',
                'waktu_presensi' => now()->format('H:i:s'),
                'foto_presensi' => $fotoPath,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'jarak_meter' => $jarak,
                'status_validasi' => 'menunggu',
            ]);

            $this->broadcastPresensi($presensi, $penempatan);

            return redirect()
                ->route('siswa.presensi')
                ->with('success', 'Presensi berhasil, menunggu validasi');
        });
    }

    /* ================= PRESENSI IZIN ================= */
    public function izin(Request $request)
    {
        $request->validate([
            'keterangan_izin' => 'required|string',
            'bukti_izin' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $penempatan = $this->getPenempatanAktifAtauGagal();
        $today = now()->toDateString();

        $tempat = $penempatan->tempatPkl;

        if ($this->hariIniTidakWajib($tempat)) {
            return back()->withErrors([
                'presensi' => 'Hari ini bukan hari wajib PKL.'
            ]);
        }

        return DB::transaction(function () use ($request, $penempatan, $today) {

            $sudahPresensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->exists();

            if ($sudahPresensi) {
                return back()->withErrors([
                    'presensi' => 'Anda sudah melakukan presensi hari ini'
                ]);
            }

            $buktiPath = null;

            if ($request->hasFile('bukti_izin')) {

                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                ]);

                $upload = $cloudinary->uploadApi()->upload(
                    $request->file('bukti_izin')->getRealPath(),
                    [
                        'folder' => 'izin',
                        'resource_type' => 'auto'
                    ]
                );

                $buktiPath = $upload['secure_url'];
            }

            $presensi = Presensi::create([
                'penempatan_pkl_id' => $penempatan->id,
                'tanggal' => $today,
                'jenis_presensi' => 'izin',
                'waktu_presensi' => now()->format('H:i:s'),
                'keterangan_izin' => $request->keterangan_izin,
                'bukti_izin' => $buktiPath,
                'status_validasi' => 'menunggu',
            ]);

            $this->broadcastPresensi($presensi, $penempatan);

            return redirect()
                ->route('siswa.presensi')
                ->with('success', 'Pengajuan izin berhasil dikirim');
        });
    }

    /* ================= PRESENSI LIBUR ================= */
    public function libur()
    {
        $penempatan = $this->getPenempatanAktifAtauGagal();
        $today = now()->toDateString();

        $tempat = $penempatan->tempatPkl;

        if ($this->hariIniTidakWajib($tempat)) {
            return back()->withErrors([
                'presensi' => 'Hari ini bukan hari wajib PKL.'
            ]);
        }

        return DB::transaction(function () use ($penempatan, $today) {

            $sudahPresensi = Presensi::where('penempatan_pkl_id', $penempatan->id)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->exists();

            if ($sudahPresensi) {
                return back()->withErrors([
                    'presensi' => 'Anda sudah melakukan presensi hari ini'
                ]);
            }

            $presensi = Presensi::create([
                'penempatan_pkl_id' => $penempatan->id,
                'tanggal' => $today,
                'jenis_presensi' => 'libur',
                'waktu_presensi' => now()->format('H:i:s'),
                'status_validasi' => 'menunggu',
            ]);

            $this->broadcastPresensi($presensi, $penempatan);

            return redirect()
                ->route('siswa.presensi')
                ->with('success', 'Presensi libur berhasil dicatat');
        });
    }

    /* ================= BROADCAST HELPER ================= */
    private function broadcastPresensi($presensi, $penempatan)
    {
        $url = config('services.realtime.url') . '/broadcast';
        
        $response = Http::timeout(3)
            ->retry(2, 100)
            ->post($url, [
                'event' => 'presensi.created',
                'data' => [
                    'id' => $presensi->id,
                    'tanggal' => \Carbon\Carbon::parse($presensi->tanggal)->format('d M Y'),
                    'jenis_presensi' => $presensi->jenis_presensi,
                    'waktu_presensi' => $presensi->waktu_presensi,
                    'nama_siswa' => $penempatan->siswa->nama_lengkap,
                    'nama_perusahaan' => $penempatan->tempatPkl->nama_perusahaan,
                    'status_validasi' => $presensi->status_validasi,
                    'jarak_meter' => $presensi->jarak_meter,

                    'foto_presensi' => $presensi->foto_presensi,
                    'bukti_izin' => $presensi->bukti_izin,
                    'latitude' => $presensi->latitude,
                    'longitude' => $presensi->longitude,

                    'siswa_user_id' => $penempatan->siswa->user_id,
                    'guru_user_id' => optional($penempatan->guruPembimbing)->user_id,
                    'pembimbing_user_id' => optional($penempatan->pembimbingLapangan)->user_id,
                ]
            ]);
 
        if (!$response->successful()) {
            \Log::error('Realtime broadcast gagal', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }
    }

    /* ================= HELPER ================= */
    private function getPenempatanAktifAtauGagal()
    {
        $siswa = Auth::user()->siswa;
        $penempatan = $siswa?->penempatanAktif;

        if (
            !$penempatan ||
            !$penempatan->isAktifHariIni() ||
            $penempatan->status !== 'aktif'
        ) {
            abort(403, 'Presensi hanya dapat dilakukan selama masa PKL aktif.');
        }

        return $penempatan;
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function hariIniTidakWajib($tempat)
    {
        if (empty($tempat->hari_wajib)) {
            return false;
        }

        $hariMap = [
            'Sunday' => 'minggu',
            'Monday' => 'senin',
            'Tuesday' => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday' => 'kamis',
            'Friday' => 'jumat',
            'Saturday' => 'sabtu',
        ];

        $hariSekarang = $hariMap[now()->format('l')];

        return !in_array($hariSekarang, $tempat->hari_wajib);
    }
}