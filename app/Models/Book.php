<?php

namespace App\Models;

use App\Interfaces\FilterableModel;
use App\Interfaces\SortableModel;
use App\Traits\EloquentFiltering;
use App\Traits\EloquentSorting;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Book
 *
 * @property string title
 * @property string author
 * @package App\Models
 */
class Book extends Model implements SortableModel, FilterableModel
{
    use EloquentSorting, EloquentFiltering;

    const SORTABLE_FIELDS = [
        'title',
        'author'
    ];

    const FILTERABLE_FIELDS = [
        'title',
        'author'
    ];

    protected $fillable = [
        'author'
    ];
}
