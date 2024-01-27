<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentsFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $postId = Post::pluck('id')->first();

        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'is_approved' => true,
            'post_id' => $postId,
            'text' => $this->faker->text(400),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
