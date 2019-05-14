<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Forms json response
     * Accepts content, status and headers
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($data = [], int $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }
}
