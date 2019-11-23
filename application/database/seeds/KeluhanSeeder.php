<?php

use App\Http\Models\Keluhan;
use Illuminate\Database\Seeder;

class KeluhanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Keluhan::class, 5)->create();
    }
}
