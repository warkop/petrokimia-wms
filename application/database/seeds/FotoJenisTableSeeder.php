<?php

use Illuminate\Database\Seeder;

class FotoJenisTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('foto_jenis')->delete();
        
        \DB::table('foto_jenis')->insert(array (
            0 => 
            array (
                'id' => 4,
                'nama' => 'Tampak Atas',
                'start_date' => '2019-10-07',
                'end_date' => NULL,
                'created_by' => 2,
                'updated_by' => 1,
                'created_at' => '2019-11-20 14:22:03',
                'updated_at' => '2019-11-20 14:31:03',
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => 'Tampak Depan',
                'start_date' => '2019-10-22',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2019-11-20 14:21:56',
                'updated_at' => '2019-11-20 14:31:25',
            ),
            2 => 
            array (
                'id' => 3,
                'nama' => 'Tampak Belakang',
                'start_date' => '2019-10-08',
                'end_date' => NULL,
                'created_by' => 2,
                'updated_by' => 1,
                'created_at' => '2019-11-20 14:22:00',
                'updated_at' => '2019-11-20 14:31:38',
            ),
            3 => 
            array (
                'id' => 1,
                'nama' => 'Tampak Samping Kiri',
                'start_date' => '2019-10-01',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2019-11-20 14:21:54',
                'updated_at' => '2019-11-20 14:31:52',
            ),
            4 => 
            array (
                'id' => 5,
                'nama' => 'Tampak Samping Kanan',
                'start_date' => '2019-10-01',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-20 14:32:06',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}