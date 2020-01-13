<?php

use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use Illuminate\Database\Seeder;

class UserCheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $checker = TenagaKerjaNonOrganik::get();

        foreach ($checker as $keyChecker) {
            $users = Users::where('id_tkbm', $keyChecker->id)->first();

            $username = trim(str_replace(' ', '', strtolower($keyChecker->nama)));
            $username = str_replace('.', '', $username);
            $username = str_replace("'", '', $username);
            $username = str_replace("`", '', $username);
            $username = str_replace(",", '', $username);
            $username = str_replace("-", '', $username);

            if (empty($users)) {
                $users = new Users;
            }

            $users->fill([
                'id_tkbm'   => $keyChecker->id,
                'role_id'   => 3,
                'name'      => $keyChecker->nama,
                'username'  => $username.$keyChecker->id,
                'password'  => bcrypt('qwerty123456'),
                'email'     => $username . $keyChecker->id.'@gmail.com',
                'start_date'    => now(),
            ])->save();
        }
    }
}
