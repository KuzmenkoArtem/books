<?php

namespace App\Models;

use App\Interfaces\SortableModel;
use App\Traits\EloquentSorting;
use Illuminate\Database\Eloquent\Model;

class Book extends Model implements SortableModel
{
    use EloquentSorting;

    const SORTABLE_FIELDS = [
        'title',
        'author'
    ];

    protected $fillable = [
        'author'
    ];
}
