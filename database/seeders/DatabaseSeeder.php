<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'user 1',
            'email' => "user1@gmail.com",
            'password' => Hash::make('password')
        ]);

        User::create([
            'name' => 'user 2',
            'email' => "user2@gmail.com",
            'password' => Hash::make('password')
        ]);

        User::create([
            'name' => 'user 3',
            'email' => "user3@gmail.com",
            'password' => Hash::make('password')
        ]);
    }
}
