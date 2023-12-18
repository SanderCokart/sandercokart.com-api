<?php

namespace App\Http\Controllers;

use App\Enums\ArticleTypeEnum;
use App\Http\Resources\ArticleJsonCollection;
use App\Http\Resources\ArticleJsonResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request, ?ArticleTypeEnum $type = null): ArticleJsonCollection
    {

        $perPage = $request->get('per_page', 10);

        $articles = QueryBuilder::for(Article::class)
            ->published()
            ->allowedFields('body')
            ->with(['banner', 'type'])
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->addSelect('id', 'title', 'excerpt', 'slug', 'published_at', 'article_type_id')
            ->when($type, fn(Builder $query) => $query->where('article_type_id', $type?->getId()))
            ->when($request->has('paginate'),
                fn(Builder $query) => $query->when($request->has('cursor'),
                    fn(Builder $query) => $query->cursorPaginate($perPage)->withQueryString(),
                    fn(Builder $query) => $query->paginate($perPage)->withQueryString()
                ),
                fn(Builder $query) => $query->get(),
            );

        return new ArticleJsonCollection($articles);
    }

    public function show(string $type, string $slug): ArticleJsonResource
    {
        return new ArticleJsonResource(
            Article::with([Article::$essentialBannerColumnsForWith, 'type'])->where('slug', $slug)->firstOrFail()
        );
    }

    public function paths(): array
    {
        return Article::with('type')->get()->map(fn(Article $article) => [
            'params' => [
                'type' => $article->type?->name,
                'slug' => $article->slug,
            ],
        ]
        )->toArray();
    }
}
