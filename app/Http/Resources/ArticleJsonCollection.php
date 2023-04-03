<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ArticleJsonCollection extends ResourceCollection
{
    public static $wrap = 'articles';

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {

        return $this->collection
            ->groupBy('type.name')
            ->when($request->has('take'),
                fn($query) => $query->map(fn($group) => $group->take($request->get('take')))
            );
    }
}
