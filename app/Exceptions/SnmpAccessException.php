<?php

namespace App\Exceptions;

use Exception;

class SnmpAccessException extends Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return view('errors.generic', [
            'error' => trans('view.error.snmpAccessException'),
            'message' => $this->getMessage(),
        ]);
    }
}
