<?php

use App\Http\Models\RencanaHarian;
use Illuminate\Database\Seeder;

class RencanaHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RencanaHarian::class, 100)->create();
    }
}
