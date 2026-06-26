<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PembimbingLapangan;

class PembimbingLapanganSeeder extends Seeder
{
    public function run(): void
    {
        $pembimbing = [
            [
                'username' => 'bambang',
                'nama_lengkap' => 'Bambang Suryono',
                'jabatan' => 'Supervisor PKL',
                'no_telepon' => '081341111111',
                'email_perusahaan' => 'bambang@gmail.com',
            ],
            [
                'username' => 'surya',
                'nama_lengkap' => 'Surya Wijaya',
                'jabatan' => 'Supervisor PKL',
                'no_telepon' => '081342222222',
                'email_perusahaan' => 'surya@gmail.com',
            ],
            [
                'username' => 'joko',
                'nama_lengkap' => 'Joko Prasetyo',
                'jabatan' => 'Supervisor PKL',
                'no_telepon' => '081343333333',
                'email_perusahaan' => 'joko@gmail.com',
            ],
            [
                'username' => 'andika',
                'nama_lengkap' => 'Andika Firmansyah',
                'jabatan' => 'Supervisor PKL',
                'no_telepon' => '081344444444',
                'email_perusahaan' => 'andika@gmail.com',
            ],
            [
                'username' => 'ferry',
                'nama_lengkap' => 'Ferry Hidayat',
                'jabatan' => 'Supervisor PKL',
                'no_telepon' => '081345555555',
                'email_perusahaan' => 'ferry@gmail.com',
            ],
        ];

        foreach ($pembimbing as $data) {

            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                $this->command->warn("User {$data['username']} tidak ditemukan.");
                continue;
            }

            PembimbingLapangan::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nama_lengkap'     => $data['nama_lengkap'],
                    'jabatan'          => $data['jabatan'],
                    'no_telepon'       => $data['no_telepon'],
                    'email_perusahaan' => $data['email_perusahaan'],
                    'foto_profil'      => 'pembimbing-profil/pembimbing.png',
                ]
            );
        }

        $this->command->info('Pembimbing Lapangan Seeder berhasil dijalankan.');
    }
}