<?php

namespace Database\Seeders;

use App\Models\Article;
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
            ]);


            // 3 types * 10
            Article::factory()->count(15)->sequential()->draft()->create();
            Article::factory()->count(15)->sequential()->published()->create();
        }
    }
}
