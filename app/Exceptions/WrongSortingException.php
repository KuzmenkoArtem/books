<?php

namespace App\Exceptions;

use Exception;

class WrongSortingException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        $responseData = [
            'error' => 'Wrong sorting',
            'message' => $this->message
        ];
        return response($responseData, 422);
    }
}
