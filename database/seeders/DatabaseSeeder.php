<?php

namespace Database\Seeders;

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
        }
    }
}
