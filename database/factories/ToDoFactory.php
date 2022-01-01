<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Helpers\UtilHelper as Util;
use App\Models\User;

class ToDoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $todo_reference_number = 'TOD' . Util::generateString(true);
        $user = User::inRandomOrder()->first();
        return [
            'todo_reference_number' => $todo_reference_number,
            'user_reference_number' => $user->user_reference_number,
            'title' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'body' => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            'due_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'attachment' => $this->faker->imageUrl(800, 500, 'cats', true, 'Faker'),
            'status' => 'incomplete'
        ];
    }
}
