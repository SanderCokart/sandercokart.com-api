<?php

namespace App\Models;

use App\Enums\MediaCollectionEnum;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, Sluggable;

    protected $casts = [
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::CourseBanners());
    }

    protected static function booted()
    {
        parent::booted();
        static::updated(function (Course $course) {
            match ($course->published_at) {
                null => $course->articles->each->unpublish(),
                default => $course->articles->each->publish(),
            };
        });
    }

    //<editor-fold desc="relationships">
    public function banner(): MorphOne
    {
        return $this->media()
            ->where('collection_name', MediaCollectionEnum::CourseBanners())
            ->one();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withPivot('order_column')
            ->using(ArticleCourse::class);
    }
    //</editor-fold>

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


    //<editor-fold desc="manipulations">
    public function publish(): void
    {
        $this->update(['published_at' => now()]);
    }

    public function unpublish(): void
    {
        $this->update(['published_at' => null]);
    }
    //</editor-fold>

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'title',
                'onUpdate' => true,
            ],
        ];
    }
}
