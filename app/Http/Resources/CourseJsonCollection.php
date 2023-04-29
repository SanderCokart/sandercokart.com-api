<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/** @see \App\Models\Course */
class CourseJsonCollection extends ResourceCollection
{

    public static $wrap = 'courses';

    public function toArray(Request $request): Collection
    {
        return $this->collection;
    }
}
