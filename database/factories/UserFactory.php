<?php

namespace Database\Factories;

use App\Models\Posts\Post;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password1#--'), // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->follow(User::where("id", "!=", $user->id)->get()->random());
            $user->posts()->saveMany(Post::factory()->count(rand(1, 10))->make());
            $randomUsers = User::all()->random(rand(2, 20));
            $user->posts()->Published()->get()->each(function (Post $post) use ($randomUsers) {
                $randomUsers->each(function (User $user) use ($post) {
                    $post->love($user);
                });
            });
        });
    }
}
