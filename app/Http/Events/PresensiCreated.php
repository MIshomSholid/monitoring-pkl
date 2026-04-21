<?php

namespace App\Events;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PresensiCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Data presensi yang dibroadcast
     */
    public Presensi $presensi;

    /**
     * Create a new event instance.
     */
    public function __construct(Presensi $presensi)
    {
        // Load relasi penting biar frontend gak query ulang
        $this->presensi = $presensi->load([
            'penempatanPkl.siswa',
            'penempatanPkl.tempatPkl',
        ]);
    }

    /**
     * Channel broadcast
     * Bisa dipakai semua role (admin, guru, pembimbing)
     */
    public function broadcastOn(): Channel
    {
        return new Channel('presensi');
    }

    /**
     * Nama event di frontend (Socket / Echo)
     */
    public function broadcastAs(): string
    {
        return 'presensi.created';
    }

    /**
     * Payload yang dikirim ke realtime server
     */
    public function broadcastWith(): array
    {
        /** @var Carbon $tanggal */
        $tanggal = $this->presensi->tanggal;

        return [
            'id' => $this->presensi->id,
            'tanggal' => $tanggal->format('Y-m-d'),
            'jenis' => $this->presensi->jenis_presensi,
            'status' => $this->presensi->status_validasi,
            'waktu' => $this->presensi->waktu_presensi,

            // siswa
            'siswa' => [
                'id' => $this->presensi->penempatanPkl->siswa->id,
                'nama' => $this->presensi->penempatanPkl->siswa->nama_lengkap,
            ],

            // lokasi PKL
            'tempat_pkl' => [
                'nama' => $this->presensi->penempatanPkl->tempatPkl->nama_perusahaan,
                'latitude' => $this->presensi->latitude,
                'longitude' => $this->presensi->longitude,
                'jarak_meter' => $this->presensi->jarak_meter,
            ],
        ];
    }
}
