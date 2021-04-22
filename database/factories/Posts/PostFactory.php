<?php

namespace Database\Factories\Posts;

use App\Models\Posts\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "content" => $this->faker->paragraph(rand(1, 5)),
            'user_id' => User::all()->random()->id,
            'published_at' => $this->faker->randomElement([now(), now()->subMinutes(rand(1, 10)), null])
        ];
    }
}
