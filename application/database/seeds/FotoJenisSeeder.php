<?php

use App\Http\Models\JenisFoto;
use Illuminate\Database\Seeder;

class FotoJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(JenisFoto::class, 50)->create();
    }
}
