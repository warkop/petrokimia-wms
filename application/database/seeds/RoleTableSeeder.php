<?php

use App\Http\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'            => 1,
                'nama'          => 'Administrator',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 2,
                'nama'          => 'Departemen',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 3,
                'nama'          => 'Checker',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 4,
                'nama'          => 'Loket',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 5,
                'nama'          => 'Karu',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 6,
                'nama'          => 'Gudang Penyangga',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
            [
                'id'            => 7,
                'nama'          => 'Admin Gudang',
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ],
        ];

        Role::truncate();

        foreach ($data as $key) {
            Role::firstOrCreate([
                'id'            => $key['id'],
                'nama'          => $key['nama'],
                'start_date'    => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ]);
        }
    }
}
