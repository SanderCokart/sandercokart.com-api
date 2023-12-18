<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class CourseJsonCollection extends ResourceCollection
{
    public static $wrap = 'courses';

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {

        return $this->collection;
    }
}
