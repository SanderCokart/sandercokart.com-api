<?php

use App\Enums\ArticleTypeEnum;
use App\Http\Resources\ArticleJsonResource;
use App\Models\Article;
use Database\Seeders\ArticleTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

test('generates paths for static page generation', function () {
    Article::factory()->roundRobinArticleTypes()->create();

    $response = getJson(route('api.articles.paths'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        '*' => [
            'params' => [
                'type',
                'slug',
            ],
        ],
    ]);
});

test('when indexing, show all types but sorted', function () {
    Article::factory(3)->roundRobinArticleTypes()->create();


    $response = getJson(route('api.articles.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'articles' => [ArticleTypeEnum::general() => [
            '*' => [
                'id',
                'banner'
            ]
        ], ArticleTypeEnum::courses() => [
            '*' => [
                'id',
                'banner'
            ]

        ], ArticleTypeEnum::tips() => [
            '*' => [
                'id',
                'banner'
            ]
        ]],
    ]);
});

it('can show an individual article', function () {
    $article = Article::factory()->create();

    $response = getJson(route('api.articles.show', parameters: [
        'article' => $article->slug,
        'type' => $article->type->name
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'article' => [
            'id',
            'banner'
        ]
    ]);
});


it('can paginate', function () {
    Article::factory(10)->roundRobinArticleTypes()->create();

    $response = getJson(route('api.articles.index', parameters: [
        'per_page' => 2,
        'paginate' => 1,
    ]));

    $response->assertStatus(200);

})->group('pagination');


it('can cursor paginate', function () {
    Article::factory(10)->roundRobinArticleTypes()->create();

    $response = getJson(route('api.articles.index', parameters: [
        'per_page' => 2,
        'paginate' => 1,
        'cursor' => 1
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'links' => [
            'next',
            'prev'
        ],
        'meta' => [
            'per_page',
            'next_cursor',
            'prev_cursor'
        ]
    ]);
})->group('pagination');

it('can toggle on the inclusion of the article body', function () {
    $article = Article::factory()->type(ArticleTypeEnum::general)->create();

    $response = getJson(route('api.articles.index', parameters: [
        'fields' => 'body'
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'articles' => [
            'general' => [
                '*' => [
                    'body'
                ]
            ]
        ]
    ]);
    $response->assertJsonFragment([
        'body' => $article->body
    ]);
});

it('can sort by title', function () {
    Article::factory(3)->type(ArticleTypeEnum::general)->create();

    $articleWithZTitle = Article::factory()->type(ArticleTypeEnum::general)->create([
        'title' => 'Z'
    ]);

    $response = getJson(route('api.articles.index', parameters: [
        'sort' => '-title',
    ]));

    $response->assertStatus(200);

    $response->assertJsonPath('articles.general.0.title', $articleWithZTitle->title);
})->group('sorting');

it('can sort by published at', function () {
    Article::factory(3)->type(ArticleTypeEnum::general)->create();
    //check if we can sort by published_at
    $oldestArticle = Article::oldest('published_at')->first();

    $response = getJson(route('api.articles.index', parameters: [
        'sort' => 'published_at',
    ]));

    $response->assertStatus(200);
    $response
        ->assertJsonPath('articles.general.0.title', $oldestArticle->title)
        ->assertJsonPath('articles.general.0.published_at', $oldestArticle->published_at->toISOString());
})->group('sorting');
