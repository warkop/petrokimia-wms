<?php

use App\Http\Models\Karu;
use Illuminate\Database\Seeder;

class KaroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Karu::class, 100)->create();
    }
}
