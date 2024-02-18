<?php

namespace Database\Seeders;

use App\Enums\ArticleTypeEnum;
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

        User::factory()
            ->create([
                'name'     => 'Test User',
                'email'    => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

        if (app()->isLocal()) {
            $courses = Course::factory()
                ->hasArticles(5, [
                    'article_type_id' => ArticleTypeEnum::courses->getId(),
                ])
                ->createMany([
                    [
                        'title'       => 'Laravel 6 From Scratch',
                        'description' => 'Master Laravel 6 with this comprehensive course. Get hands-on experience as you develop a sophisticated web application from the ground up.',
                    ],
                    [
                        'title'       => 'Vue 2 From Scratch',
                        'description' => 'Delve into Vue 2 with our in-depth course. Equip yourself with key skills and practical knowledge required to build functional Vue 2 web applications.',
                    ],
                    [
                        'title'       => 'Tailwind CSS From Scratch',
                        'description' => 'Discover Tailwind CSS in our extensive course. Learn to design sleek, responsive web applications using Tailwind CSS starting from scratch.',
                    ],
                ]);

            // 3 types * 10
//            Article::factory()->count(15)->roundRobinArticleTypes()->draft()->create();
//            $published = Article::factory()->count(15)->roundRobinArticleTypes()->published()->create();
//
//            $publishedCourseArticles = $published->where('article_type_id', ArticleTypeEnum::courses->getId());
//
//            //add 10 more articles to the publishedCourseArticles collection
//            $publishedCourseArticles->push(Article::factory(10)->type(ArticleTypeEnum::courses)->published()->create());
//
//            // loop over publishedCourseArticles and assign them to a course, there are 3 courses, do a while loop on the length of the colleciton and pull when assigning
//            while ($publishedCourseArticles->count() > 0) {
//                foreach ($courses as $course) {
//                    $course->articles()->attach($publishedCourseArticles->pop());
//                }
//            }

            //create 5 articles of tips and general types for each month from this month to the past 5 months
            $months = 5;
            $date = now();
            for ($i = 0; $i < $months; $i++) {
                Article::factory()->count(rand(3,5))->type(ArticleTypeEnum::tips)->published()->create([
                    'published_at' => $date->subMonth(),
                ]);
                Article::factory()->count(rand(3,5))->type(ArticleTypeEnum::general)->published()->create([
                    'published_at' => $date->subMonth(),
                ]);
            }




//            Article::factory()->count(5)->type(ArticleTypeEnum::general)->hasCourses()->create();
//            Article::factory()->count(5)->type(ArticleTypeEnum::tips)->hasCourses()->create();
//            Article::factory()->count(5)->type(ArticleTypeEnum::courses)->hasCourses()->create();
        }
    }
}
