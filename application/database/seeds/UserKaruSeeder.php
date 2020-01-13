<?php

use App\Http\Models\Karu;
use App\Http\Models\Users;
use Illuminate\Database\Seeder;

class UserKaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $karu = Karu::get();

        foreach ($karu as $keyKaru) {
            $users = Users::where('id_karu', $keyKaru->id)->first();

            $username = trim(str_replace(' ', '', strtolower($keyKaru->nama)));
            $username = str_replace('.', '', $username);
            $username = str_replace("'", '', $username);
            $username = str_replace("`", '', $username);
            $username = str_replace(",", '', $username);
            $username = str_replace("-", '', $username);

            if (empty($users)) {
                $users = new Users;
            }

            $users->fill([
                'id_karu'   => $keyKaru->id,
                'role_id'   => 5,
                'name'      => $keyKaru->nama,
                'username'  => $username.$keyKaru->id,
                'password'  => bcrypt('qwerty123456'),
                'email'     => $username . $keyKaru->id . '@gmail.com',
                'start_date'    => now(),
            ])->save();
        }
    }
}
