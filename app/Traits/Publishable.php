<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->whereNull('published_at');
    }

    public function initializePublishable(): void
    {
        $this->casts = array_merge($this->casts, [
            'published_at' => 'datetime',
        ]);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function isDraft(): bool
    {
        return $this->published_at === null;
    }

    public function publish(): void
    {
        $this->update(['published_at' => now()]);
    }

    public function redact(): void
    {
        $this->update(['published_at' => null]);
    }
}
