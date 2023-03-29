<?php

namespace App\Models;

use App\Enums\Disk;
use App\Enums\MediaCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Storage;

class Article extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::forceDeleted(function (Article $article) {
            $article->deleteAllMarkdownAttachments();
        });
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
    public function articleType(): BelongsTo
    {
        return $this->belongsTo(ArticleType::class);
    }
    //</editor-fold>

    //<editor-fold desc="markdown manipulations">
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::ArticleBanners->name);
    }

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
        Storage::disk(Disk::public->name)->delete($this->extractFilesFromMarkdownBody());
    }
    //</editor-fold>

    //<editor-fold desc="scopes">
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->whereNull('published_at');
    }
    //</editor-fold>
}
