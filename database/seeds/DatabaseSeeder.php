<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $user = new App\User();
        $user->name = 'Demo User';
        $user->email = 'admin@demo.com';
        $user->password = bcrypt('12345678');
        $user->role = 'admin';
        $user->save();

        factory('App\User', 30)->create();
    }
}
