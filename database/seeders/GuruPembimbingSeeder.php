<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GuruPembimbing;

class GuruPembimbingSeeder extends Seeder
{
    public function run(): void
    {
        // Default foto guru di Cloudinary
        $defaultFotoGuru = 'https://res.cloudinary.com/dl7px9jnw/image/upload/v1782581961/guru-profil/yaoptbpkdq2ye3xt47vc.png';

        $guru = [
            [
                'username' => 'ardiansyah',
                'nip' => '198503152010011001',
                'nama_lengkap' => 'Ardiansyah Putra',
                'mata_pelajaran' => 'Praktik Kerja Lapangan',
                'no_telepon' => '081231111111',
            ],
            [
                'username' => 'rizal',
                'nip' => '198704182011011002',
                'nama_lengkap' => 'Rizal Prakoso',
                'mata_pelajaran' => 'Praktik Kerja Lapangan',
                'no_telepon' => '081232222222',
            ],
            [
                'username' => 'yusuf',
                'nip' => '198902242012011003',
                'nama_lengkap' => 'Yusuf Maulana',
                'mata_pelajaran' => 'Praktik Kerja Lapangan',
                'no_telepon' => '081233333333',
            ],
            [
                'username' => 'hendra',
                'nip' => '199103152013011004',
                'nama_lengkap' => 'Hendra Kurniawan',
                'mata_pelajaran' => 'Praktik Kerja Lapangan',
                'no_telepon' => '081234444444',
            ],
            [
                'username' => 'doni',
                'nip' => '199305202014011005',
                'nama_lengkap' => 'Doni Saputra',
                'mata_pelajaran' => 'Praktik Kerja Lapangan',
                'no_telepon' => '081235555555',
            ],
        ];

        foreach ($guru as $data) {

            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                $this->command->warn("User {$data['username']} tidak ditemukan.");
                continue;
            }

            GuruPembimbing::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nip'             => $data['nip'],
                    'nama_lengkap'    => $data['nama_lengkap'],
                    'mata_pelajaran'  => $data['mata_pelajaran'],
                    'no_telepon'      => $data['no_telepon'],
                    'foto_profil'     => $defaultFotoGuru,
                ]
            );
        }

        $this->command->info('Guru Pembimbing Seeder berhasil dijalankan.');
    }
}