<?php

namespace Database\Seeders;

use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Models\Article;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            ArticleTypeSeeder::class,
        ]);

        if (app()->isLocal()) {
            User::factory()->create([
                'name'     => 'Test User',
                'email'    => 'test@example.com',
                'password' => bcrypt('password'),
                'timezone' => 'Europe/Amsterdam',
            ]);

            // 3 types * 10
            Article::factory()->count(15)->sequentialArticleType()->draft()->create();
            Article::factory()->count(15)->sequentialArticleType()->published()->create();

            $courseDescription = 'In this course you\'ll learn how to build a %s application from scratch. We\'ll start with the basics and work our way up to building a real-world application. This course is for beginners and advanced developers alike.';
            Course::factory()
                ->sequencePublishedRedacted()
                ->afterCreating(function (Course $course) {
                    $course->addMedia($this->getCourseBanner($course))
                        ->preservingOriginal()
                        ->toMediaCollection(
                            MediaCollectionEnum::CourseBanners(),
                            DiskEnum::public()
                        );
                })
                ->createMany([
                    [
                        'title'       => 'Learn Laravel',
                        'slug'        => 'learn-laravel',
                        'description' => sprintf($courseDescription, 'laravel'),
                    ],
                    [
                        'title'       => 'Learn Vue',
                        'slug'        => 'learn-vue',
                        'description' => sprintf($courseDescription, 'vue'),
                    ],
                    [
                        'title'       => 'Learn React',
                        'slug'        => 'learn-react',
                        'description' => sprintf($courseDescription, 'react'),
                    ],
                    [
                        'title'       => 'Learn Tailwind',
                        'slug'        => 'learn-tailwind',
                        'description' => sprintf($courseDescription, 'tailwind'),
                    ],
                ]);

            $articles = Article::inRandomOrder()->published()->get();
            Course::all()->each(function (Course $course) use ($articles) {
                $course->articles()->sync($articles->pullRandom(3));
            });
        }
    }

    public function getCourseBanner(Course $course): string
    {
        return match ($course->title) {
            'Learn Laravel'  => storage_path('app/testing/laravel.jpg'),
            'Learn Vue'      => storage_path('app/testing/vuejs.jpg'),
            'Learn React'    => storage_path('app/testing/react.jpg'),
            'Learn Tailwind' => storage_path('app/testing/tailwind.jpg'),
        };
    }
}
