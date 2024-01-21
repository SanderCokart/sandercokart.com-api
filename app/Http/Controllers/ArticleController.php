<?php

namespace App\Http\Controllers;

use App\Enums\ArticleTypeEnum;
use App\Enums\WithEnum;
use App\Http\Resources\ArticleJsonCollection;
use App\Http\Resources\ArticleJsonResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_TAKE = 10;

    public function index(Request $request, ?ArticleTypeEnum $type = null): ArticleJsonCollection
    {
        $perPage = $request->get('per_page', self::DEFAULT_PER_PAGE);
        $take = $request->get('take', self::DEFAULT_TAKE);

        $unionQuery = $this->createArticleUnionSubQuery($take);

        $articles = QueryBuilder::for(
            Article::query()
                ->fromSub($unionQuery, 'articles')
                ->published()
                ->with([WithEnum::banner(), 'type'])
        )
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->allowedFields('body')
            ->addSelect('id', 'title', 'excerpt', 'slug', 'published_at', 'article_type_id')
            ->when($type, fn(Builder $query) => $query->where('article_type_id', $type?->getId()))
            ->when(
                $request->has('paginate'),
                fn(Builder $query) => $query->when(
                    $request->has('cursor'),
                    fn(Builder $query) => $query->cursorPaginate($perPage)
                        ->withQueryString(),
                    fn(Builder $query) => $query->paginate($perPage)
                        ->withQueryString()
                ),
                fn(Builder $query) => $query->get()
            );

        return new ArticleJsonCollection($articles);
    }

    private function createArticleUnionSubQuery(int $take): Builder
    {
        $unionQuery = null;

        foreach (ArticleTypeEnum::all() as $articleType) {
            $query = Article::query()
                ->published()
                ->where('article_type_id', $articleType->getId())
                ->orderBy('published_at', 'desc')
                ->limit($take);

            $unionQuery = $unionQuery
                ? $unionQuery->union($query)
                : $query;
        }

        return $unionQuery;
    }

    public function show(string $type, string $slug): ArticleJsonResource
    {
        return new ArticleJsonResource(
            Article::with([WithEnum::banner(), 'type'])
                ->where('slug', $slug)
                ->firstOrFail()
        );
    }

    public function paths(): array
    {
        return Article::with('type')
            ->get()
            ->map(fn(Article $article) => [
                'params' => [
                    'type' => $article->type?->name,
                    'slug' => $article->slug,
                ],
            ]
            )
            ->toArray();
    }
}
