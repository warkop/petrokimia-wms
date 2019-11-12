<?php

use App\Http\Models\AktivitasGudang;
use Illuminate\Database\Seeder;

class AktivitasGudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AktivitasGudang::class, 100)->create();
    }
}
