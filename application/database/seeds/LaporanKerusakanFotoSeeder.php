<?php

use App\Http\Models\LaporanKerusakanFoto;
use Illuminate\Database\Seeder;

class LaporanKerusakanFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LaporanKerusakanFoto::class, 50)->create();
    }
}
