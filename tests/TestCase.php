<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Forms filtering query params from array
     *
     * @param array $params
     * @return string
     */
    protected function formFilteringQueryParams(array $params)
    {
        $queryString = '';

        foreach ($params as $param) {
            $queryString .= 'filter_groups[]=' . json_encode($param) . '&';
        }

        return $queryString;
    }
}
