<?php

namespace App\Exceptions;

use Exception;

class WrongExportingFormat extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        $responseData = [
            'exception' => 'Wrong exporting format',
            'message' => $this->message
        ];
        return response($responseData, 406);
    }
}
