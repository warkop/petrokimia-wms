<?php

use App\Http\Models\KategoriAlatBerat;
use Illuminate\Database\Seeder;

class KategoriAlatBeratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(KategoriAlatBerat::class, 5)->create();
    }
}
