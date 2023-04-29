<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseJsonCollection;
use App\Models\Article;
use App\Models\Course;
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

    public function show($id)
    {
    }
}
