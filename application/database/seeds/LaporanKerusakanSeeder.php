<?php

use App\Http\Models\LaporanKerusakan;
use Illuminate\Database\Seeder;

class LaporanKerusakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LaporanKerusakan::class, 50)->create();
    }
}
