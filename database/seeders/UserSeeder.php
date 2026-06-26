<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /* ===============================
            GURU PEMBIMBING (5)
        =============================== */

        $guru = [
            ['nama' => 'Ardiansyah Putra', 'username' => 'ardiansyah'],
            ['nama' => 'Rizal Prakoso', 'username' => 'rizal'],
            ['nama' => 'Yusuf Maulana', 'username' => 'yusuf'],
            ['nama' => 'Hendra Kurniawan', 'username' => 'hendra'],
            ['nama' => 'Doni Saputra', 'username' => 'doni'],
        ];

        foreach ($guru as $g) {
            User::create([
                'username' => $g['username'],
                'email' => $g['username'].'@smkn7.sch.id',
                'password' => Hash::make('ishompro'),
                'role' => 'guru_pembimbing',
                'is_active' => true,
            ]);
        }


        /* ===============================
            PEMBIMBING LAPANGAN (5)
        =============================== */

        $pl = [
            ['nama' => 'Bambang Suryono', 'username' => 'bambang'],
            ['nama' => 'Surya Wijaya', 'username' => 'surya'],
            ['nama' => 'Joko Prasetyo', 'username' => 'joko'],
            ['nama' => 'Andika Firmansyah', 'username' => 'andika'],
            ['nama' => 'Ferry Hidayat', 'username' => 'ferry'],
        ];

        foreach ($pl as $p) {
            User::create([
                'username' => $p['username'],
                'email' => $p['username'].'@gmail.com',
                'password' => Hash::make('ishompro'),
                'role' => 'pembimbing_lapangan',
                'is_active' => true,
            ]);
        }


        /* ===============================
            SISWA (20)
        =============================== */

        $siswa = [
            ['nama'=>'Adit Pranata','username'=>'adit'],
            ['nama'=>'Bagus Ramadhan','username'=>'bagus'],
            ['nama'=>'Cahyo Nugroho','username'=>'cahyo'],
            ['nama'=>'Deni Firmansyah','username'=>'deni'],
            ['nama'=>'Erwin Saputra','username'=>'erwin'],
            ['nama'=>'Fikri Maulana','username'=>'fikri'],
            ['nama'=>'Galang Prasetya','username'=>'galang'],
            ['nama'=>'Hanif Kurniawan','username'=>'hanif'],
            ['nama'=>'Iqbal Ramadhan','username'=>'iqbal'],
            ['nama'=>'Jefri Hidayat','username'=>'jefri'],
            ['nama'=>'Kelvin Prakoso','username'=>'kelvin'],
            ['nama'=>'Luthfi Nugraha','username'=>'luthfi'],
            ['nama'=>'Miftah Fauzan','username'=>'miftah'],
            ['nama'=>'Naufal Saputra','username'=>'naufal'],
            ['nama'=>'Oky Firmansyah','username'=>'oky'],
            ['nama'=>'Prasetyo Aji','username'=>'prasetyo'],
            ['nama'=>'Qomarudin','username'=>'qomar'],
            ['nama'=>'Rama Dwi Putra','username'=>'rama'],
            ['nama'=>'Satrio Nugroho','username'=>'satrio'],
            ['nama'=>'Taufik Hidayat','username'=>'taufik'],
        ];

        foreach ($siswa as $s) {
            User::create([
                'username' => $s['username'],
                'email' => $s['username'].'@student.smkn7.sch.id',
                'password' => Hash::make('ishompro'),
                'role' => 'siswa',
                'is_active' => true,
            ]);
        }
    }
}