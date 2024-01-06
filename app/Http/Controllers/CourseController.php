<?php

namespace App\Http\Controllers;

use App\Enums\WithEnum;
use App\Http\Resources\CourseJsonCollection;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CourseController extends Controller
{
    public function index(Request $request): CourseJsonCollection
    {
        $perPage = $request->get('per_page', 10);

        $courses = QueryBuilder::for(Course::class)
            ->allowedFields('body')
            ->with([WithEnum::banner()])
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->addSelect('id', 'title', 'slug', 'published_at')
            ->when($request->has('paginate'),
                fn(Builder $query) => $query->when($request->has('cursor'),
                    fn(Builder $query) => $query->cursorPaginate($perPage)->withQueryString(),
                    fn(Builder $query) => $query->paginate($perPage)->withQueryString()
                ),
                fn(Builder $query) => $query->get(),
            );

        return new CourseJsonCollection($courses);
    }
}
