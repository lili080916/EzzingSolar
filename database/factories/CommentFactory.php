<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $users = \App\Models\User::all()
                    ->pluck('id')
                    ->toArray();

        return [
            'user_id' => $this->faker->randomElement($users),
            'text' => $this->faker->text
        ];
    }
}
