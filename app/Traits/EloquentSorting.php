<?php

namespace App\Traits;

use App\Exceptions\WrongSortingException;
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
     * @throws WrongSortingException
     */
    protected function parseSortingRules($unparsedSortingRules)
    {
        $sortingRules = [];

        foreach ($unparsedSortingRules as $sortingRule) {
            if (!is_array($sortingRule)) {
                $sortingRule = json_decode($sortingRule, true);
            }

            if (!$sortingRule) {
                continue;
            }

            $field = $sortingRule['field'] ?? null;
            $direction = $sortingRule['direction'] ?? null;

            if (!$field || !$direction) {
                throw new WrongSortingException("Field and Direction are required");
            }

            if ($direction !== 'asc' && $direction !== 'desc') {
                throw new WrongSortingException("Wrong direction");
            }

            if (!defined('self::SORTABLE_FIELDS') || !in_array($field, self::SORTABLE_FIELDS)) {
                throw new WrongSortingException("{$field} is not available for sorting");
            }

            $sortingRules[$field] = $direction;
        }

        return $sortingRules;
    }
}
