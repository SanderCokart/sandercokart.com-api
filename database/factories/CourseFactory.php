<?php

namespace Database\Factories;

use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(255),
            'created_at' => $created_at = now()->subDays(random_int(1, 30)),
            'updated_at' => $updated_at = $this->faker->dateTimeBetween($created_at, 'now'),
            'published_at' => $this->faker->dateTimeBetween($updated_at, 'now'),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Course $course) {
            $course->addMedia(UploadedFile::fake()->image('thumbnail.jpg', 300, 200))
                ->toMediaCollection(
                    MediaCollectionEnum::CourseBanners(),
                    DiskEnum::public()
                );

        });
    }


}
