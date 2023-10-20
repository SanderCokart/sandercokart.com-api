<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseJsonCollection;
use App\Http\Resources\CourseJsonResource;
use App\Models\Article;
use App\Models\Course;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends Controller
{
    public function index(): CourseJsonCollection
    {
        $courses = QueryBuilder::for(Course::class)
            ->published()
            ->limitable()
            ->withCount('articles')
            ->with(['banner:' . Article::$essentialBannerAttributes])
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->addSelect('id', 'title', 'published_at', 'slug')
            ->queryablePagination();

        return new CourseJsonCollection($courses);
    }

    public function show(Request $request, string $slug): CourseJsonResource
    {
        return new CourseJsonResource(
            Course::with([
                'banner:' . Article::$essentialBannerAttributes,
                'articles' => fn($query) => $query->with(['banner:' . Article::$essentialBannerAttributes]),
            ])
                ->when(! $request->hasValidRelativeSignature(), fn($query) => $query->published())
                ->where('slug', $slug)->firstOrFail()
        );
    }
}
