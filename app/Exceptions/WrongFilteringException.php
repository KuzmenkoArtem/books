<?php

namespace App\Exceptions;

use Exception;

class WrongFilteringException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        $responseData = [
            'exception' => 'Wrong filtering',
            'message' => $this->message
        ];
        return response($responseData, 422);
    }
}
