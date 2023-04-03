<?php

namespace Database\Seeders;

use App\Enums\ArticleTypeEnum;
use App\Models\ArticleType;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ArticleTypeEnum::names() as $name) {
            ArticleType::insertOrIgnore([
                'name' => $name,
            ]);
        }
    }
}
