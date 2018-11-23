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

        DB::table('users')->delete();

        factory(App\User::class)->create([
            'email' => 'user@example.com',
            'password' => 'secret'
        ]);
        factory(App\User::class, 'manager')->create([
            'email' => 'manager@example.com',
            'password' => 'secret'
        ]);
        factory(App\User::class, 'admin')->create([
            'email' => 'admin@example.com',
            'password' => 'secret'
        ]);
    }
}
