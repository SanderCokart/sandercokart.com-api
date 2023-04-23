<?php

namespace App\Models;

use App\Enums\MediaCollectionEnum;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Publishable, SoftDeletes;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::CourseBanners())
            ->singleFile();
    }

    //<editor-fold desc="relationships">
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withPivot('order_column')
            ->using(ArticleCourse::class);
    }

    public function banner(): MorphOne
    {
        return $this->media()
            ->where('collection_name', MediaCollectionEnum::CourseBanners())
            ->one();
    }
    //</editor-fold>
}
