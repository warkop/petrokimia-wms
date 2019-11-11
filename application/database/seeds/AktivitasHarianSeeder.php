<?php

use App\Http\Models\AktivitasHarian;
use Illuminate\Database\Seeder;

class AktivitasHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AktivitasHarian::class, 50)->create();
    }
}
