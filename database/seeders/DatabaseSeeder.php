<?php

namespace Database\Seeders;

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

            Course::factory()->sequencePublishedRedacted()->createMany([
                ['title' => 'Learn Laravel', 'slug' => 'learn-laravel'],
                ['title' => 'Learn Vue', 'slug' => 'learn-vue'],
                ['title' => 'Learn React', 'slug' => 'learn-react'],
                ['title' => 'Learn Tailwind', 'slug' => 'learn-tailwind'],
            ]);

            $articles = Article::inRandomOrder()->published()->get();
            Course::all()->each(function (Course $course) use ($articles) {
                $course->articles()->sync($articles->pullRandom(3));
            });
        }
    }
}
