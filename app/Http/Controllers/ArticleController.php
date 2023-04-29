<?php

namespace App\Http\Controllers;

use App\Enums\ArticleTypeEnum;
use App\Http\Resources\ArticleJsonCollection;
use App\Http\Resources\ArticleJsonResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request, ArticleTypeEnum $type): ArticleJsonCollection
    {
        $articles = QueryBuilder::for(Article::class)
            ->whereArticleTypeId($type->getId())
            ->published()
            ->allowedFields('body')
            ->with(['banner', 'type'])
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->addSelect('id', 'title', 'excerpt', 'slug', 'published_at', 'article_type_id')
            ->queryablePagination();

        return new ArticleJsonCollection($articles);
    }

    public function show(Request $request, string $type, string $slug): ArticleJsonResource
    {
        return new ArticleJsonResource(
            Article::with([
                'banner:' . Article::$essentialBannerAttributes,
                'type',
            ])
                ->when(! $request->hasValidRelativeSignature(), fn($query) => $query->published())
                ->where('slug', $slug)->firstOrFail()
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
