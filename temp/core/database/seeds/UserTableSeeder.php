<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        User::create([
            'name' => 'Pete Houston',
             'username' => 'petehouston',
             'password' => '123secret'
         ]);
        User::create([
            'name' => 'Taylor Otwell',
            'username' => 'taylorotwell',
            'password' => 'greatsecret'
        ]);
    }
}
