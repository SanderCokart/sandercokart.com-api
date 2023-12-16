<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'created_at' => $created_at = now()->subDays(random_int(1, 30)),
            'updated_at' => $updated_at = $this->faker->dateTimeBetween($created_at, 'now'),
            'published_at' => $this->faker->dateTimeBetween($updated_at, 'now'),
        ];
    }
}
