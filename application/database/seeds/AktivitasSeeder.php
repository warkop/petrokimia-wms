<?php

use App\Http\Models\Aktivitas;
use Illuminate\Database\Seeder;

class AktivitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Aktivitas::class, 50)->create();
    }
}
