<?php

use App\Http\Models\RencanaTkbm;
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
        factory(RencanaTkbm::class, 300)->create();
    }
}
