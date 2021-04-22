<?php

namespace Database\Factories\Posts;

use App\Models\Posts\LoveReaction;
use App\Models\Posts\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoveReactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoveReaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => User::all()->random()->id,
            "post_id" => Post::all()->random()->id
        ];
    }
}
