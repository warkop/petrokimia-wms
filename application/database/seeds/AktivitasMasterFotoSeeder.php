<?php

use App\Http\Models\AktivitasMasterFoto;
use Illuminate\Database\Seeder;

class AktivitasMasterFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AktivitasMasterFoto::class, 200)->create();
    }
}
