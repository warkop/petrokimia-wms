<?php

use App\Http\Models\JobDesk;
use Illuminate\Database\Seeder;

class JobDeskSeeder extends Seeder
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
                'nama'          => 'Housekeeper',
            ],
            [
                'id'            => 2,
                'nama'          => 'Checker',
            ],
            [
                'id'            => 3,
                'nama'          => 'Operator',
            ],
            [
                'id'            => 4,
                'nama'          => 'Admin Loket',
            ]
        ];

        foreach ($data as $key) {
            JobDesk::firstOrCreate([
                'id' => $key['id'],
                'nama' => $key['nama'],
            ]);
        }
    }
}
