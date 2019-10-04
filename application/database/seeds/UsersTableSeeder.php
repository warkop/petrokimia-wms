<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => str_random(10).'@gmail.com',
            'password' => bcrypt('3n3rg33k'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
