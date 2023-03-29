<?php

namespace App\Models;

use App\Enums\MediaCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function articleType(): BelongsTo
    {
        return $this->belongsTo(ArticleType::class);
    }

    public function isPublished(): bool
    {
        return isset($this->published_at);
    }

    public function isDraft(): bool
    {
        return is_null($this->published_at);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::ArticleBanners->name);
    }
}
