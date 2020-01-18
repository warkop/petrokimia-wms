<?php

use Illuminate\Database\Seeder;

class KeluhanTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('keluhan')->delete();
        
        \DB::table('keluhan')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama' => 'Kedisiplinan operator',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => 'Kantong produk sobek',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'nama' => 'Merusak pilar gudang',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'nama' => 'Pallet rusak',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'nama' => 'Terplas rusak',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'nama' => 'Staple roboh',
                'start_date' => '2019-11-23',
                'end_date' => NULL,
                'created_by' => 1,
                'updated_by' => NULL,
                'created_at' => '2019-11-23 13:56:11',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}