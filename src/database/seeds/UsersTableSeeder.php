<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $admin = User::firstOrNew(['email' => env('ADMIN_EMAIL')]);

      $admin->fill([
        'name'     => env('ADMIN_NAME'),
        'password' => bcrypt(env('ADMIN_PASSWORD')),
      ])->save();
    }
}
