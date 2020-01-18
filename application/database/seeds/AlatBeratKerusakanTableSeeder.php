<?php

use Illuminate\Database\Seeder;

class AlatBeratKerusakanTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('alat_berat_kerusakan')->delete();
        
        \DB::table('alat_berat_kerusakan')->insert(array (
            0 => 
            array (
                'id' => 5,
                'nama' => 'Lain - lain',
                'start_date' => '2020-01-04',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2020-01-04 14:56:20',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 6,
                'nama' => 'Sumber Daya Rusak',
                'start_date' => '2020-01-10',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2020-01-10 16:42:58',
                'updated_at' => '2020-01-10 16:42:58',
            ),
            2 => 
            array (
                'id' => 1,
                'nama' => 'Ban bocor',
                'start_date' => '2019-11-22',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-22 00:00:00',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 2,
                'nama' => 'Rem rusak',
                'start_date' => '2019-11-22',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-22 00:00:00',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 3,
                'nama' => 'Oli bocor',
                'start_date' => '2019-11-22',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-22 00:00:00',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 4,
                'nama' => 'Trouble engine',
                'start_date' => '2019-11-22',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-22 00:00:00',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}