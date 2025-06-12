<?php

namespace App\Utility;

class ApiResponse
{
    public static function send($code = 200, $msg = "no message provided", $error = null, $data = null)
    {
        return response()->json([
            "message" => $msg,
            "error" => $error,
            "data" => $data
        ], $code);
    }
}
