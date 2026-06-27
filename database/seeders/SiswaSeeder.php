<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $defaultFotoSiswa = 'https://res.cloudinary.com/dl7px9jnw/image/upload/v1782582533/siswa-profil/x52a6pxh6cnuihkwycx2.png';

        $siswa = [

            [
                'username' => 'adit',
                'nis' => '26121/5540001',
                'nama_lengkap' => 'Adit Pranata',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111001',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'bagus',
                'nis' => '26121/5540002',
                'nama_lengkap' => 'Bagus Ramadhan',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111002',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'cahyo',
                'nis' => '26121/5540003',
                'nama_lengkap' => 'Cahyo Nugroho',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111003',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'deni',
                'nis' => '26121/5540004',
                'nama_lengkap' => 'Deni Firmansyah',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111004',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'erwin',
                'nis' => '26121/5540005',
                'nama_lengkap' => 'Erwin Saputra',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111005',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'fikri',
                'nis' => '26121/5540006',
                'nama_lengkap' => 'Fikri Maulana',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111006',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'galang',
                'nis' => '26121/5540007',
                'nama_lengkap' => 'Galang Prasetya',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111007',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'hanif',
                'nis' => '26121/5540008',
                'nama_lengkap' => 'Hanif Kurniawan',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111008',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'iqbal',
                'nis' => '26121/5540009',
                'nama_lengkap' => 'Iqbal Ramadhan',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111009',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'jefri',
                'nis' => '26121/5540010',
                'nama_lengkap' => 'Jefri Hidayat',
                'kelas' => 'XII TKJ 1',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111010',
                'alamat' => 'Surabaya',
            ],

            [
                'username' => 'kelvin',
                'nis' => '26121/5540011',
                'nama_lengkap' => 'Kelvin Prakoso',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111011',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'luthfi',
                'nis' => '26121/5540012',
                'nama_lengkap' => 'Luthfi Nugraha',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111012',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'miftah',
                'nis' => '26121/5540013',
                'nama_lengkap' => 'Miftah Fauzan',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111013',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'naufal',
                'nis' => '26121/5540014',
                'nama_lengkap' => 'Naufal Saputra',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111014',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'oky',
                'nis' => '26121/5540015',
                'nama_lengkap' => 'Oky Firmansyah',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111015',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'prasetyo',
                'nis' => '26121/5540016',
                'nama_lengkap' => 'Prasetyo Aji',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111016',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'qomar',
                'nis' => '26121/5540017',
                'nama_lengkap' => 'Qomarudin',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111017',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'rama',
                'nis' => '26121/5540018',
                'nama_lengkap' => 'Rama Dwi Putra',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111018',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'satrio',
                'nis' => '26121/5540019',
                'nama_lengkap' => 'Satrio Nugroho',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111019',
                'alamat' => 'Surabaya',
            ],
            [
                'username' => 'taufik',
                'nis' => '26121/5540020',
                'nama_lengkap' => 'Taufik Hidayat',
                'kelas' => 'XII TKJ 2',
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'no_telepon' => '081311111020',
                'alamat' => 'Surabaya',
            ],

        ];

        foreach ($siswa as $data) {

            $user = User::where('username', $data['username'])
                ->where('role', 'siswa')
                ->first();

            if (!$user) {
                $this->command->warn("User {$data['username']} tidak ditemukan.");
                continue;
            }

            Siswa::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nis' => $data['nis'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'kelas' => $data['kelas'],
                    'jurusan' => $data['jurusan'],
                    'no_telepon' => $data['no_telepon'],
                    'alamat' => $data['alamat'],
                    'foto_profil' => $defaultFotoSiswa,
                ]
            );
        }

        $this->command->info('Siswa Seeder berhasil dijalankan.');
    }
}