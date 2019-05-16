<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface SortableModel
{
    /**
     * Applies sorting
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $sortingRules
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, ?array $sortingRules);
}
