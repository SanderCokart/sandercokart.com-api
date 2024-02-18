<?php

namespace App\Http\Controllers;

use App\Enums\WithEnum;
use App\Http\Resources\CourseJsonCollection;
use App\Http\Resources\CourseJsonResource;
use App\Models\Course;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends Controller
{
    public function index(Request $request): CourseJsonCollection
    {
        $courses = QueryBuilder::for($this->getCoursesQuery())
            ->allowedFields('body')
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->addSelect('id', 'title', 'slug', 'published_at', 'created_at', 'updated_at')
            ->when(
                $request->has('paginate'),
                $this->handlePagination($request),
                fn(Builder $query) => $query->get(),
            );

        return new CourseJsonCollection($courses);
    }

    public function show(string $slug): CourseJsonResource
    {
        $courses = Course::with([
            WithEnum::banner(),
            'articles' => function ($query) {
                $query
                    ->selectAllBut(['body'])
                    ->with('type', WithEnum::banner())
                    ->published()
                    ->withPivot('order_column')
                    ->orderBy('article_course.order_column');
            },
        ])
            ->withCount('articles')
            ->where('slug', $slug)
            ->firstOrFail();

        return new CourseJsonResource($courses);
    }

    private function handlePagination(Request $request): Closure
    {
        $perPage = $request->get('per_page', 10);

        return fn(Builder $query) => $query->when(
            $request->has('cursor'),
            fn(Builder $query) => $query->cursorPaginate($perPage)
                ->withQueryString(),
            fn(Builder $query) => $query->paginate($perPage)
                ->withQueryString()
        );
    }

    public function getCoursesQuery(): Builder
    {
        return Course::query()
            ->published()
            ->withCount('articles')
            ->with(WithEnum::banner());
    }
}
