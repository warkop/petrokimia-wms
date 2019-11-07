<?php

use App\Http\Models\AlatBeratHistoryFoto;
use Illuminate\Database\Seeder;

class AlatBeratHistoryFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AlatBeratHistoryFoto::class, 50)->create();
    }
}
