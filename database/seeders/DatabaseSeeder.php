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
            'password' => Hash::make('user1@gmail.com')
        ]);

        User::create([
            'name' => 'user 2',
            'email' => "user2@gmail.com",
            'password' => Hash::make('user1@gmail.com')
        ]);

        User::create([
            'name' => 'user 3',
            'email' => "user3@gmail.com",
            'password' => Hash::make('user1@gmail.com')
        ]);

        User::create([
            'name' => 'Mohamed Higgy',
            'email' => "mohamed.higgy31@gmail.com",
            'password' => Hash::make('mohamed.higgy31@gmail.com'),
            'email_verified_at' => now(),
            'verified_at' => now()
        ]);

        User::create([
            'name' => 'Elsayed Kamal',
            'email' => "elsayedkamal581999@gmail.com",
            'password' => Hash::make('elsayedkamal581999@gmail.com'),
            'email_verified_at' => now(),
            'verified_at' => now()
        ]);

        //User::factory()->count(100)->create();
    }
}
