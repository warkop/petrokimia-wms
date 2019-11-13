<?php

use App\Http\Models\GudangPallet;
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
        factory(GudangPallet::class, 150)->create();
    }
}
