<?php

use App\Enums\ArticleTypeEnum;
use App\Http\Resources\ArticleJsonResource;
use App\Models\Article;
use Database\Seeders\ArticleTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(ArticleTypeSeeder::class);
});

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

it('can index articles', function () {
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

});

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
});

it('can toggle on the inclusion of the article body', function () {
    $article = Article::factory()->type(ArticleTypeEnum::general)->create();

    $response = getJson(route('api.articles.index', parameters: [
        'per_page' => 2,
        'paginate' => 1,
        'cursor' => 1,
        'fields' => 'body'
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
        ],
        'articles' => [
            '*' => [
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

it('can sort by title and published_at', function () {
    Article::factory(10)->roundRobinArticleTypes()->create();

    $articleWithZTitle = Article::factory()->type(ArticleTypeEnum::general)->create([
        'title' => 'Z'
    ]);

    $response = getJson(route('api.articles.index', parameters: [
        'sort' => '-title',
    ]));

    $response->assertStatus(200);

    // make sure the first article is the one with the Z title
    $response->assertJsonPath('articles.general.0.title', $articleWithZTitle->title);


    $dateInTheFuture = now()->addDays(10);

    $articleWithZTitle->update([
        //set published_at to a specific date, so we can test sorting by it
        'published_at' => $dateInTheFuture
    ]);

    $response = getJson(route('api.articles.index', parameters: [
        'sort' => '-published_at',
    ]));

    $response->assertStatus(200);

    // make sure the first article is the one with the latest published_at date
    $response->assertJsonPath('articles.general.0.published_at', $dateInTheFuture->format(ArticleJsonResource::$dateFormat));
});
