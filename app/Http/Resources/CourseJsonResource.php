<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class CourseJsonResource extends JsonResource
{

    public static $wrap = 'course';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'banner'       => $this->whenLoaded('banner', fn() => $this->banner),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'published_at' => $this->published_at,

            $this->mergeWhen($this->relationLoaded('articles'), [
                'articles' => ArticleJsonResource::collection($this->articles),
                'articles_count' => $this->whenCounted('articles', fn() => $this->articles_count),
            ]),
        ];
    }
}
