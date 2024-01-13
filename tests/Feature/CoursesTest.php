<?php

use App\Models\Course;

use function Pest\Laravel\getJson;

test('can list all courses', function () {
    $response = getJson(route('api.courses.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'courses' => [
            '*' => [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
            ],
        ],
    ]);
});

test('can show a course with all of its articles', function () {
    Course::factory()
        ->hasArticles(3)
        ->create();

    $response = getJson(route('api.courses.show', 1));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'course' => [
            'id',
            'name',
            'description',
            'created_at',
            'updated_at',
            'articles' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ],
        ],
    ]);
});

test('can paginate courses', function () {
    Course::factory()
        ->count(15)
        ->create();

    $response = getJson(route('api.courses.index', ['paginate' => true]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'courses' => [
            '*' => [
                'id',
                'title',
                'slug',
                'created_at',
                'updated_at',
                'banner'
            ],
        ],
        'links'   => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta'    => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
    ]);
});

test('can cursor paginate', function () {
    Course::factory()
        ->count(15)
        ->create();

    $response = getJson(route('api.courses.index', ['paginate' => 1, 'cursor' => 1]));

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'courses' => [
            '*' => [
                'id',
                'title',
                'slug',
                'banner',
                'created_at',
                'updated_at',
                'published_at',
            ],
        ],
        'links'   => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta'    => [
            'path',
            'per_page',
            'next_cursor',
            'prev_cursor',
        ],
    ]);
});
