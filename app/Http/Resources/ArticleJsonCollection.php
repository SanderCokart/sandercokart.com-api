<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ArticleJsonCollection extends ResourceCollection
{
    public static $wrap = false;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     *
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        if ($request->has('paginate')) {
            $this::$wrap = 'articles';
        }

        return $this->collection
            // Group by type name if no type is specified
            /* ->when(
                 !$request->route('type'),
                 fn($query) => $query->groupBy('type.name')
             )*/ ;
    }
}
