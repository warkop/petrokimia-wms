<?php

use App\Http\Models\AreaStok;
use Illuminate\Database\Seeder;

class AreaStokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AreaStok::class, 100)->create();
    }
}
