<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ArticleCourse extends Pivot implements Sortable
{
    use SortableTrait;

    public $timestamps = false;
    protected $table = 'article_course';
}
