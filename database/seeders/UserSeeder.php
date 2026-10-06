<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ActivityLog; // Tambahkan ini agar bisa buat log dummy
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'noiaoia14@gmail.com',
            'phone' => '628111111111',
            'role' => 'super_admin',
            'password' => Hash::make('Aneira123'),
            'email_verified_at' => now(),
        ]);

        // 2. Akun Admin Dinas (Yang akan login dan mengelola portal)
        User::create([
            'name' => 'Admin Diskominfo',
            'username' => 'adminkominfo',
            'email' => 'admin_kominfo@soa.com',
            'phone' => '628333333333',
            'role' => 'admin_dinas',
            'instansi' => 'Dinas Komunikasi dan Informatika',
            'password' => Hash::make('Aneira123'),
            'email_verified_at' => now(),
        ]);

        // 3. Akun User (Warga)
        User::create([
            'name' => 'Aneira',
            'username' => 'Aneira',
            'email' => 'aneirasalsanayy@gmail.com',
            'phone' => '628222222222',
            'role' => 'user',
            'password' => Hash::make('Aneira123'),
            'email_verified_at' => now(),
        ]);
    }
}