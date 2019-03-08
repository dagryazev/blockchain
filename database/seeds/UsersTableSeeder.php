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
          'name' => 'Denis',
          'email' => 'denis@mail.ru',
          'password' => bcrypt('1234'),
      ]);
    }
}
