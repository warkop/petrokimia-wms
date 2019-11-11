<?php

use App\Http\Models\AktivitasFoto;
use Illuminate\Database\Seeder;

class AktivitasFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AktivitasFoto::class, 100)->create();
    }
}
