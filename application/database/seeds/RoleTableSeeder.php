<?php

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
        DB::table('role')->insert([
            'id'            => 1,
            'nama'          => 'Administrator',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'id'            => 2,
            'nama'          => 'Departemen',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'id'            => 3,
            'nama'          => 'Checker',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'id'            => 4,
            'nama'          => 'Loket',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'id'            => 5,
            'nama'          => 'Karu',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
