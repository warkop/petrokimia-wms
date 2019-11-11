<?php

use App\Http\Models\RencanaAlatBerat;
use Illuminate\Database\Seeder;

class RencanaAlatBeratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RencanaAlatBerat::class, 200)->create();
    }
}
