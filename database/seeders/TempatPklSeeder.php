<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TempatPkl;

class TempatPklSeeder extends Seeder
{
    public function run(): void
    {
        $tempatPkl = [

            [
                'nama_perusahaan' => 'PT Surabaya Digital Solusi',
                'alamat' => 'Jl. Ahmad Yani No. 120, Surabaya',
                'no_telepon' => '0318654101',
                'email' => 'hrd@surabayadigital.co.id',
                'latitude' => -7.33669850,
                'longitude' => 112.72034780,
                'radius_meter' => 300,
                'jam_masuk' => '07:30:00',
                'toleransi_keterlambatan' => 15,
                'hari_wajib' => [
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat'
                ],
                'kuota_siswa' => 4,
                'deskripsi' => 'Perusahaan software house dan pengembangan sistem informasi.',
            ],

            [
                'nama_perusahaan' => 'CV Mitra Teknologi Nusantara',
                'alamat' => 'Jl. Raya Jemursari No. 88, Surabaya',
                'no_telepon' => '0318654102',
                'email' => 'info@mitrateknologi.id',
                'latitude' => -7.32109540,
                'longitude' => 112.74791410,
                'radius_meter' => 300,
                'jam_masuk' => '08:00:00',
                'toleransi_keterlambatan' => 15,
                'hari_wajib' => [
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat'
                ],
                'kuota_siswa' => 4,
                'deskripsi' => 'Bergerak di bidang IT Support dan Network.',
            ],

            [
                'nama_perusahaan' => 'PT Global Network Indonesia',
                'alamat' => 'Jl. Ngagel Jaya Selatan No. 15, Surabaya',
                'no_telepon' => '0318654103',
                'email' => 'career@globalnetwork.co.id',
                'latitude' => -7.29272890,
                'longitude' => 112.75998420,
                'radius_meter' => 300,
                'jam_masuk' => '08:00:00',
                'toleransi_keterlambatan' => 20,
                'hari_wajib' => [
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat'
                ],
                'kuota_siswa' => 4,
                'deskripsi' => 'Perusahaan jaringan komputer dan data center.',
            ],

            [
                'nama_perusahaan' => 'PT Media Kreasi Informatika',
                'alamat' => 'Jl. Dharmahusada Indah No. 25, Surabaya',
                'no_telepon' => '0318654104',
                'email' => 'recruitment@mediakreasi.id',
                'latitude' => -7.27584210,
                'longitude' => 112.78241350,
                'radius_meter' => 300,
                'jam_masuk' => '07:45:00',
                'toleransi_keterlambatan' => 15,
                'hari_wajib' => [
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat'
                ],
                'kuota_siswa' => 4,
                'deskripsi' => 'Perusahaan pengembang website dan aplikasi mobile.',
            ],

            [
                'nama_perusahaan' => 'PT Cipta Inovasi Teknologi',
                'alamat' => 'Jl. Raya Rungkut Industri No. 55, Surabaya',
                'no_telepon' => '0318654105',
                'email' => 'hr@ciptainovasi.co.id',
                'latitude' => -7.33011680,
                'longitude' => 112.78126930,
                'radius_meter' => 300,
                'jam_masuk' => '08:00:00',
                'toleransi_keterlambatan' => 15,
                'hari_wajib' => [
                    'senin',
                    'selasa',
                    'rabu',
                    'kamis',
                    'jumat'
                ],
                'kuota_siswa' => 4,
                'deskripsi' => 'Perusahaan pengembangan software enterprise.',
            ],

        ];

        foreach ($tempatPkl as $data) {

            TempatPkl::updateOrCreate(
                [
                    'nama_perusahaan' => $data['nama_perusahaan']
                ],
                $data
            );
        }

        $this->command->info('Tempat PKL Seeder berhasil dijalankan.');
    }
}