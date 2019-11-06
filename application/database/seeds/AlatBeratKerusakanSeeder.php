<?php

use App\Http\Models\AlatBeratKerusakan;
use Illuminate\Database\Seeder;

class AlatBeratKerusakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AlatBeratKerusakan::class, 50)->create();
    }
}
