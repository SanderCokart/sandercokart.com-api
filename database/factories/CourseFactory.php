<?php

namespace Database\Factories;

use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'        => $title = $this->faker->word(),
            //'slug'         => Str::slug($title), //auto generated
            'description'  => $this->faker->paragraph(),

            //dates between now and 30 days ago
            'created_at'   => $created_at = now()->subDays(random_int(1, 30)),
            'updated_at'   => $this->faker->dateTimeBetween($created_at, 'now'),
            'published_at' => null,
        ];
    }

    public function published(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => $this->faker->dateTimeBetween($attributes['updated_at'], 'now'),
            ];
        });
    }

    public function draft(): self
    {
        return $this->state(function () {
            return [
                'published_at' => null,
            ];
        });
    }

    //alternating
    public function sequencePublishedRedacted(): self
    {
        return $this->sequence(
            ['published_at' => $this->faker->dateTimeBetween('-1 month', 'now')],
            ['published_at' => null]
        );
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Course $course) {
            $course->addMedia(storage_path('app/testing/course.png'))
                ->preservingOriginal()
                ->toMediaCollection(
                    MediaCollectionEnum::CourseBanners(),
                    DiskEnum::public()
                );
        });
    }
}
