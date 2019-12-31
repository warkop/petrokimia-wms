<?php

use App\Http\Models\GudangStok;
use Illuminate\Database\Seeder;

class GudangPalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(GudangStok::class, 150)->create();
    }
}
