<?php

use App\Http\Models\AlatBeratHistory;
use Illuminate\Database\Seeder;

class AlatBeratHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AlatBeratHistory::class, 100)->create();
    }
}
