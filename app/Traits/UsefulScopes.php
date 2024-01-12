<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * @mixin Model
 */
trait UsefulScopes
{
    /**
     * Scope a query to only exclude specific Columns.
     */
    public function scopeSelectAllBut(Builder $query, array $excludeColumns): Builder
    {
        $existingColumns = $this->getTableColumns();

        if ($excludeColumns === $existingColumns) {
            throw new InvalidArgumentException('You can not exclude all columns!');
        }

        $diff = array_diff($existingColumns, $excludeColumns);
        //prefix items with tablename.column

        $table = $this->getTable();
        $diff = array_map(function ($item) use ($table) {
            return $table.'.'.$item;
        }, $diff);


        return $query->select($diff);
    }

    /**
     * Shows All the columns of the Corresponding Table of Model
     *
     * If You need to get all the Columns of the Model Table.
     * Useful while including the columns in search.
     * NOTE: column names of table will be cached until contents of migrations directory is added or deleted.
     * modifying the contents of files inside the migrations directory will not re-cache the columns
     * Whenever you make a new deployment/migration you can clear the cache.
     *
     **/
    public function getTableColumns(): array
    {
        $cacheKey = 'MigrMod:'.filemtime(database_path('migrations')).':'.$this->getTable();

        return Cache::rememberForever($cacheKey, function () {
            return $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable());
        });
    }

    /**
     * Custom scope (query builder method) to easy return all items from today
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFromToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', now()->today());
    }

    /**
     * Custom scope (query builder method) to easy return all items from yesterday
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFromYesterday(Builder $query): Builder
    {
        return $query->whereDate('created_at', now()->yesterday());
    }
}
