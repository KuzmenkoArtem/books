<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait EloquentSorting
{
    /**
     * Applies sorting
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $sortingRules
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, ?array $sortingRules)
    {
        if (!$sortingRules) {
            return $query;
        }

        $sortingRules = $this->parseSortingRules($sortingRules);

        foreach ($sortingRules as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    /**
     * Parses sorting rules.
     * Decodes json if needed and leaves only needed fields
     * Gets rid of broken data
     *
     * @param $unparsedSortingRules
     * @return array
     */
    protected function parseSortingRules($unparsedSortingRules)
    {
        $sortingRules = [];

        foreach ($unparsedSortingRules as $sortingRule) {
            if (!is_array($sortingRule)) {
                $sortingRule = json_decode($sortingRule, true);
            }

            $field = $sortingRule['field'] ?? null;
            $direction = $sortingRule['direction'] ?? null;

            if (!$field || !$direction) {
                continue;
            }

            if ($direction !== 'asc' && $direction !== 'desc') {
                continue;
            }

            if (!defined('self::SORTABLE_FIELDS') || !in_array($field, self::SORTABLE_FIELDS)) {
                continue;
            }

            $sortingRules[$field] = $direction;
        }

        return $sortingRules;
    }
}
