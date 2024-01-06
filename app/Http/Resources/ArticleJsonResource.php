<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleJsonResource extends JsonResource
{

    public static $wrap = 'article';
    //date format: 25th of april 2021 8:00 AM
    public static string $dateFormat = 'jS \of F Y \a\t g:i A';

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
            'excerpt'      => $this->excerpt,
            'banner'       => $this->whenLoaded('banner', fn() => $this->banner),
            'type'         => $this->whenLoaded('type', fn() => $this->type),
            'body'         => $this->when($this->body, $this->body),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'published_at' => $this->published_at,
        ];
    }
}
