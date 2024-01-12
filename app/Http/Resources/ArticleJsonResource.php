<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleJsonResource extends JsonResource
{

    public static $wrap = 'article';

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

            $this->mergeWhen($this->relationLoaded('banner'), [
                'banner' => $this->banner
            ]),

            $this->mergeWhen($this->relationLoaded('type'), [
                'type' => $this->type
            ]),

            $this->mergeWhen($this->body,[
                'body' => $this->body
            ]),

            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'published_at' => $this->published_at,
        ];
    }
}
