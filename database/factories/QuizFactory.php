<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition()
{
    return [
        'title' => $this->faker->sentence,
        'description' => $this->faker->paragraph,
        'duration' => $this->faker->numberBetween(10, 60),
        'created_by' => Member::factory(), // ✅ This ensures valid foreign key
    ];
}
}