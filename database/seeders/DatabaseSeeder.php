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

        User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        if (app()->isLocal()) {
            $courses = Course::factory()->createMany([
                [
                    'title' => 'Laravel 6 From Scratch',
                ],
                [
                    'title' => 'Vue 2 From Scratch',
                ],
                [
                    'title' => 'Tailwind CSS From Scratch',
                ],
            ]);

            // 3 types * 10
            Article::factory()->count(15)->roundRobinArticleTypes()->draft()->create();
            $published = Article::factory()->count(150)->roundRobinArticleTypes()->published()->create();

            $publishedCourseArticles = $published->where('article_type_id', ArticleTypeEnum::courses->getId());

            //add 10 more articles to the publishedCourseArticles collection
            $publishedCourseArticles->push(Article::factory(10)->type(ArticleTypeEnum::courses)->published()->create());

            // loop over publishedCourseArticles and assign them to a course, there are 3 courses, do a while loop on the length of the colleciton and pull when assigning
            while ($publishedCourseArticles->count() > 0) {
                foreach ($courses as $course) {
                    $course->articles()->attach($publishedCourseArticles->pop());
                }
            }
        }
    }
}
