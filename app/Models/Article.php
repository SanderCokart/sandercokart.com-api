<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    public function articleType(): BelongsTo
    {
        return $this->belongsTo(ArticleType::class);
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function isPublished(): bool
    {
        return isset($this->published_at);
    }

    public function isDraft(): bool
    {
        return is_null($this->published_at);
    }
}
