<?php

namespace App\Providers\Macros;

use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * @mixin EloquentBuilder
 * @method when($value, $callback, $default = null)
 */
class QueryBuilderMacrosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setupQueryablePaginationMacro();
        $this->setupLimitableMacro();
    }

    private function setupLimitableMacro(): void
    {
        $limitable = function () {
            $limit = request()?->input('limit');
            return $this->when($limit, fn($query) => $query->limit($limit));
        };

        EloquentBuilder::macro('limitable', $limitable);
        BelongsToMany::macro('limitable', $limitable);
        HasManyThrough::macro('limitable', $limitable);
        BaseBuilder::macro('limitable', $limitable);
    }

    private function setupQueryablePaginationMacro(): void
    {
        $queryablePagination = function (int $maxResults = 30, int $defaultSize = 30) {
            $request = request();

            if (
                $request->has('cursor') &&
                $request->has('simple')
            ) {
                throw new Exception('Cannot use cursor and simple pagination together');
            }

            $paginationMethod = match (true) {
                $request->has('cursor') => 'cursorPaginate',
                $request->has('simple') => 'simplePaginate',
                $request->has('page')   => 'paginate',
                default                 => null,
            };

            if ($paginationMethod === null) {
                return $this->get();
            }

            $per_page = $request->integer('per_page', $defaultSize);

            if ($per_page > $maxResults) {
                $per_page = $maxResults;
            }

            return match ($paginationMethod) {
                'cursorPaginate' => $this->cursorPaginate($per_page)
                    ->withQueryString(),
                'simplePaginate' => $this->simplePaginate($per_page)
                    ->withQueryString(),
                'paginate'       => $this->paginate($per_page)
                    ->withQueryString(),
                default          => $this->get(),
            };

        };

        EloquentBuilder::macro('queryablePagination', $queryablePagination);
        BelongsToMany::macro('queryablePagination', $queryablePagination);
        HasManyThrough::macro('queryablePagination', $queryablePagination);
        BaseBuilder::macro('queryablePagination', $queryablePagination);
    }
}
