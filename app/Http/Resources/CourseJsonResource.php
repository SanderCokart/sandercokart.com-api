<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class CourseJsonResource extends JsonResource
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
            'banner'       => $this->whenLoaded('banner', fn() => $this->banner),
            'created_at'   => $this->created_at?->format(self::$dateFormat),
            'updated_at'   => $this->updated_at?->format(self::$dateFormat),
            'published_at' => $this->published_at->format(self::$dateFormat),
        ];
    }
}
