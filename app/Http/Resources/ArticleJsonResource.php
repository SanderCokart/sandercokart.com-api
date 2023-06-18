<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleJsonResource extends JsonResource
{
    public static string $dateFormat = 'jS \of F Y \a\t g:i A';

    public static $wrap = 'article';

    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'banner'       => $this->whenLoaded('banner', fn() => $this->banner),
            'type'         => $this->whenLoaded('type', fn() => $this->type),
            'body'         => $this->whenHas('body'),
            'created_at'   => $this->whenHas('created_at'),
            'updated_at'   => $this->whenHas('updated_at'),
            'published_at' => $this->whenHas('published_at'),
            'estimated_reading_time' => $this->whenAppended('estimated_reading_time'),
        ];
    }
}
