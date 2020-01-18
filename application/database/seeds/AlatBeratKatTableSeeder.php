<?php

use Illuminate\Database\Seeder;

class AlatBeratKatTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('alat_berat_kat')->delete();
        
        \DB::table('alat_berat_kat')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama' => 'FORKLIFT',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => 'FORKLIFT & WHEEL LOADER',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'nama' => 'Forklift AlF3',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'nama' => 'Forklift Kaptan',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'nama' => 'Forklift Petrocas',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'nama' => 'Payloader',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            6 => 
            array (
                'id' => 7,
                'nama' => 'Wheel Loader',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            7 => 
            array (
                'id' => 9,
                'nama' => 'Forklift Cadangan',
                'start_date' => '2019-12-02',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2019-12-02 00:00:00',
            ),
            8 => 
            array (
                'id' => 8,
                'nama' => 'Excavator',
                'start_date' => '2019-12-02',
                'end_date' => '2020-01-08',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2019-12-02 00:00:00',
                'updated_at' => '2020-01-10 18:46:08',
            ),
        ));
        
        
    }
}