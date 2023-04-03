<?php

namespace Database\Seeders;

use App\Enums\ArticleTypeEnum;
use App\Models\ArticleType;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    public function run(): void
    {
        ArticleType::factory()->createMany(
            array_map(fn(string $articleType) => ['name' => $articleType], ArticleTypeEnum::names()),
        );
    }
}
