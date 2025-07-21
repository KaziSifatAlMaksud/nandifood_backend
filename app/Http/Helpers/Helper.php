<?php

namespace App\Http\Helpers;
use Illuminate\Http\Exceptions\HttpResponseException;
class Helper{
    public static function formatResponse($message = '', $errors = [], $code = 401)
    {
        return response()->json([
            'data' => $data,
            'message' => false,
            'message' => $message
        ], $code);


        throw new HttpResponseException(
            response()->json($response, $code)
        );
    }
}
