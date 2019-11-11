<?php

use App\Http\Models\TenagaKerjaNonOrganik;
use Illuminate\Database\Seeder;

class TenagaKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TenagaKerjaNonOrganik::class, 100)->create();
    }
}
