<?php

namespace App\Traits;

use App\Exceptions\WrongFilteringException;
use Illuminate\Database\Eloquent\Builder;

trait EloquentFiltering
{

    /**
     * Applies filtering
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|null $filterGroups
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws WrongFilteringException
     */
    public function scopeFilter(Builder $query, ?array $filterGroups)
    {
        if (!$filterGroups) {
            return $query;
        }

        $filterGroups = $this->parseFilteringRules($filterGroups);
        $this->validateFilteringRules($filterGroups);

        foreach ($filterGroups as $filterGroup) {
            $or = $filterGroup['or'] ?? false;
            $query = $this->applyGroup($query, $filterGroup['filters'], $or);
        }

        return $query;
    }

    /**
     * Applies a filter group to the query builder
     *
     * @param Builder $query
     * @param array $filterGroup
     * @param bool $or
     * @return Builder
     */
    protected function applyGroup(Builder $query, array $filterGroup, bool $or = false)
    {
        $whereType = $or ? 'orWhere' : 'where';

        $query->$whereType(function (Builder $query) use ($filterGroup) {
            foreach ($filterGroup as $filters) {
                $whereType = isset($filters['or']) && $filters['or'] ? 'orWhere' : 'where';

                switch ($filters['operator']) {
                    case self::OPERATOR_LIKE:
                        $query->$whereType($filters['field'], 'LIKE', "%{$filters['value']}%");
                        break;
                }
            }
        });

        return $query;
    }

    /**
     * Parses and validates filtering rules.
     * Decodes json if needed
     *
     * @param $unparsedFilteringRules
     * @return array
     */
    protected function parseFilteringRules($unparsedFilteringRules)
    {
        $filteringRules = [];

        foreach ($unparsedFilteringRules as $filteringGroup) {
            if (!is_array($filteringGroup)) {
                $filteringGroup = json_decode($filteringGroup, true);
            }

            $filteringGroup['or'] = (bool)($filteringGroup['or'] ?? false);

            array_push($filteringRules, $filteringGroup);
        }

        return $filteringRules;
    }

    /**
     * Validates filtering rules. Throw exceptions if there are something wrong
     *
     * @param array $filteringRules
     * @throws WrongFilteringException
     */
    protected function validateFilteringRules(array $filteringRules)
    {
        foreach ($filteringRules as $filteringGroup) {
            if (!isset($filteringGroup['filters']) || !is_array($filteringGroup['filters'])) {
                throw new WrongFilteringException("Broken structure");
            }

            foreach ($filteringGroup['filters'] as $filterSet) {
                $field = $filterSet['field'] ?? null;
                $value = $filterSet['value'] ?? null;
                $operator = $filterSet['operator'] ?? null;
                if (!$field || !$value || !$operator) {
                    throw new WrongFilteringException("Broken structure");
                }

                if (!is_string($field) || !is_string($operator)) {
                    throw new WrongFilteringException("Broken structure");
                }

                if (!is_string($value) && !is_int($value)) {
                    throw new WrongFilteringException("Broken structure");
                }

                if (!defined('self::FILTERABLE_FIELDS') || !in_array($field, self::FILTERABLE_FIELDS)) {
                    throw new WrongFilteringException("'{$field}' is not available for filtering ");
                }

                if (!defined('self::AVAILABLE_OPERATORS') || !in_array($operator, self::AVAILABLE_OPERATORS)) {
                    throw new WrongFilteringException("Operator '{$operator}' is not available for filtering ");
                }
            }
        }
    }
}
