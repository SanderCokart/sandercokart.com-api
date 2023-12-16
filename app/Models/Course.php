<?php

namespace App\Models;

use App\Enums\MediaCollectionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::CourseBanners());
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withPivot('order_column')
            ->using(ArticleCourse::class);
    }

    //<editor-fold desc="scopes">
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeUnpublished($query)
    {
        return $query->whereNull('published_at');
    }
    //</editor-fold>
}
