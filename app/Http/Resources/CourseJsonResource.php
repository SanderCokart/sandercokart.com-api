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
            'id'    => $this->id,
            'title' => $this->title,
            'description'    => $this->whenHas('description'),

            'published_at'   => $this->whenHas('published_at'),
            'created_at'     => $this->whenHas('created_at'),
            'updated_at'     => $this->whenHas('updated_at'),
            'articles_count' => $this->whenHas('articles_count'),
            'slug'           => $this->whenHas('slug'),

            'banner'   => $this->whenLoaded('banner'),
            'articles' => ArticleJsonResource::collection($this->whenLoaded('articles')),
        ];
    }
}
