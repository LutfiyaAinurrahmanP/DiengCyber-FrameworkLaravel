<?php namespace Lutfiyapr\KehadiranPegawai\Updates;

use Lutfiyapr\KehadiranPegawai\Models\Users;
use Winter\Storm\Database\Updates\Seeder;

class SeedAdminUser extends Seeder
{
    public function run()
    {
        // Buat admin
        $admin = Users::create([
            'username' => 'admin',
            'email' => 'admin@kehadiran.com',
            'password' => 'admin123', // Will be hashed
            'role' => 'admin'
        ]);

        // Buat user pegawai untuk testing
        $userPegawai = Users::create([
            'pegawai_id' => 1, // Ahmad Fauzi
            'username' => 'ahmad.fauzi',
            'email' => 'ahmad.fauzi@example.com',
            'password' => 'password123',
            'role' => 'pegawai'
        ]);

        Users::create([
            'pegawai_id' => 2, // Siti Nurhaliza
            'username' => 'siti.nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
            'password' => 'password123',
            'role' => 'pegawai'
        ]);

        Users::create([
            'pegawai_id' => 3, // Budi Santoso
            'username' => 'budi.santoso',
            'email' => 'budi.santoso@example.com',
            'password' => 'password123',
            'role' => 'pegawai'
        ]);
    }
}
