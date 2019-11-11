<?php

use App\Http\Models\MaterialTrans;
use Illuminate\Database\Seeder;

class MaterialTransSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(MaterialTrans::class, 100)->create();
    }
}
