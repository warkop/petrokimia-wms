<?php

use App\Http\Models\AlatBerat;
use Illuminate\Database\Seeder;

class AlatBeratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AlatBerat::class, 10)->create();
    }
}
