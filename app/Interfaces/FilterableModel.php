<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface FilterableModel
{
    const OPERATOR_LIKE = 'like';

    const AVAILABLE_OPERATORS = [
        self::OPERATOR_LIKE
    ];

    /**
     * Applies sorting
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|null $filteringRules
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, ?array $filteringRules);
}
