<?php

use App\Http\Models\KerusakanAlatBerat;
use Illuminate\Database\Seeder;

class KerusakanAlatBeratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(KerusakanAlatBerat::class, 50)->create();
    }
}
