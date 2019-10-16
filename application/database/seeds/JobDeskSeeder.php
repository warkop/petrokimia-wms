<?php

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
        DB::table('job_desk')->insert([
            'id'            => 1,
            'nama'          => 'Housekeeper',
            'start_date'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('job_desk')->insert([
            'id'            => 2,
            'nama'          => 'Checker',
            'start_date'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('job_desk')->insert([
            'id'            => 3,
            'nama'          => 'Operator',
            'start_date'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('job_desk')->insert([
            'id'            => 4,
            'nama'          => 'Admin Loket',
            'start_date'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
