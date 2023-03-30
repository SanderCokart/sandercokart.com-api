<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function paths(): array
    {
        return Article::pluck('slug')->map(function ($slug) {
            return ['params' => ['slug' => $slug]];
        })->toArray();
    }
}
