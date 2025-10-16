<?php namespace Lutfiyapr\KehadiranPegawai\Updates;

use Lutfiyapr\KehadiranPegawai\Models\Pegawai;
use Winter\Storm\Database\Updates\Seeder;

class SeedPegawaiTable extends Seeder
{
    public function run()
    {
        $pegawai = [
            [
                'nip' => '198501012010011001',
                'nama' => 'Ahmad Fauzi',
                'jabatan' => 'Manager',
                'email' => 'ahmad.fauzi@example.com',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'status' => 'aktif'
            ],
            [
                'nip' => '199002152012012002',
                'nama' => 'Siti Nurhaliza',
                'jabatan' => 'Staff Admin',
                'email' => 'siti.nurhaliza@example.com',
                'no_telp' => '081234567891',
                'alamat' => 'Jl. Sudirman No. 45, Bandung',
                'status' => 'aktif'
            ],
            [
                'nip' => '198808202015011003',
                'nama' => 'Budi Santoso',
                'jabatan' => 'Supervisor',
                'email' => 'budi.santoso@example.com',
                'no_telp' => '081234567892',
                'alamat' => 'Jl. Gatot Subroto No. 78, Surabaya',
                'status' => 'aktif'
            ]
        ];

        foreach ($pegawai as $p) {
            Pegawai::create($p);
        }
    }
}
