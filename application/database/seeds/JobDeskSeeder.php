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
                'nama'          => 'Admin Loket',
            ],
            [
                'id'            => 2,
                'nama'          => 'Operator',
            ],
            [
                'id'            => 3,
                'nama'          => 'Checker',
            ],
            [
                'id'            => 4,
                'nama'          => 'Housekeeper',
            ],
            [
                'id'            => 5,
                'nama'          => 'Tally',
            ]
        ];

        JobDesk::truncate();

        foreach ($data as $key) {
            JobDesk::firstOrCreate([
                'id' => $key['id'],
                'nama' => $key['nama'],
            ]);
        }
    }
}
