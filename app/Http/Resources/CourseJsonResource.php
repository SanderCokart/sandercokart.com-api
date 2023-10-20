<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Course */
class CourseJsonResource extends JsonResource
{
    public static $wrap = 'course';

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->whenHas('id'),
            'title'       => $this->whenHas('title'),
            'description' => $this->whenHas('description'),
            'slug'        => $this->whenHas('slug'),

            'published_at'   => $this->whenHas('published_at'),
            'created_at'     => $this->whenHas('created_at'),
            'updated_at'     => $this->whenHas('updated_at'),
            'articles_count' => $this->whenHas('articles_count'),

            'banner'   => $this->whenLoaded('banner', fn() => $this->banner),
            'articles' => ArticleJsonResource::collection($this->whenLoaded('articles', fn() => $this->articles)),
        ];
    }
}
