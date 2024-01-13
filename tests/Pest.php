<?php

use Database\Seeders\ArticleTypeSeeder;

use function Pest\Laravel\seed;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->beforeEach(function() {
    seed(ArticleTypeSeeder::class);
})->in('Feature', 'Unit');
