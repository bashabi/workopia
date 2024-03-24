<?php

namespace App\Controllers;

class ErrorController
{

    /**
     * 404 not found 
     * 
     * @return void
     * 
     */

    public static function notFound($message = 'Resource not found')
    {
        http_response_code(404);

        loadView('error', [
            'status' => '404',
            'message' => $message
        ]);
    }

    /**
     * 403 not found 
     * 
     * @return void
     * 
     */

    public static function unAuthorizedd($message = 'You are not authorized to view this resource')
    {
        http_response_code(404);

        loadView('error', [
            'status' => '403',
            'message' => $message
        ]);
    }
}
