<?php

use App\Http\Models\RencanaAreaTkbm;
use Illuminate\Database\Seeder;

class RencanaTkbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RencanaAreaTkbm::class, 300)->create();
    }
}
