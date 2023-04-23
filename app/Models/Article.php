<?php

namespace App\Models;

use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Storage;

class Article extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable, Publishable;

    public static string $essentialBannerColumnsForWith = 'banner:id,model_type,model_id,disk,file_name';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::forceDeleted(function (Article $article) {
            $article->deleteAllMarkdownAttachments();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollectionEnum::ArticleBanners())
            ->singleFile();
    }

    //<editor-fold desc="scout">
    public function getScoutKey(): string
    {
        return $this->slug;
    }

    public function getScoutKeyName(): string
    {
        return 'slug';
    }
    //</editor-fold>

    //<editor-fold desc="relationships">
    public function type(): BelongsTo
    {
        return $this->belongsTo(ArticleType::class, 'article_type_id', 'id');
    }

    public function banner(): MorphOne
    {
        return $this->media()->where('collection_name', MediaCollectionEnum::ArticleBanners())->one();
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('order_column')
            ->using(ArticleCourse::class);
    }
    //</editor-fold>

    //<editor-fold desc="markdown manipulations">
    public function extractFilesFromMarkdownBody(): array
    {
        $regex = '/\((https?:\/\/)?' . preg_quote(config('app.url'), '/') . '\/storage\/markdown-attachments\/.*\)/';

        preg_match($regex, $this->body, $matches);

        $files = array_map(function ($match) {
            $match = substr($match, 4, -1);
            $url = parse_url($match, PHP_URL_PATH);
            return substr($url, strlen('/storage'));
        }, $matches);

        return $files;
    }

    public function deleteAllMarkdownAttachments(): void
    {
        Storage::disk(DiskEnum::public())->delete($this->extractFilesFromMarkdownBody());
    }
    //</editor-fold>


}
