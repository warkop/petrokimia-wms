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
            'role_name'     => 'Administrator',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'end_date'      => date('Y-m-d', strtotime("31-12-2030")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'role_name'     => 'Departemen',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'end_date'      => date('Y-m-d', strtotime("31-12-2030")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'role_name'     => 'Checker',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'end_date'      => date('Y-m-d', strtotime("31-12-2030")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        DB::table('role')->insert([
            'role_name'     => 'Loket',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'end_date'      => date('Y-m-d', strtotime("31-12-2030")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
