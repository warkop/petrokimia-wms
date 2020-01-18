<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(MaterialTableSeeder::class);
        $this->call(GudangTableSeeder::class);
        $this->call(KaruTableSeeder::class);
        $this->call(TenagaKerjaNonOrganikTableSeeder::class);
        $this->call(FotoJenisTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(AlatBeratTableSeeder::class);
        $this->call(AlatBeratKatTableSeeder::class);
        $this->call(AlatBeratKerusakanTableSeeder::class);
        $this->call(KeluhanTableSeeder::class);
    }
}
