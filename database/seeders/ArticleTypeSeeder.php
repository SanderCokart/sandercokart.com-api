<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    public function run(): void
    {
        ArticleType::create(['name' => 'General']);
        ArticleType::create(['name' => 'Course']);
        ArticleType::create(['name' => 'Tip']);
    }
}
