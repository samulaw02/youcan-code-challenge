<?php

namespace App\Services;

class JSONResponseService
{
    //return a successful response
    public function success($message, $data, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data ?? null
        ], $status);
    }

    //return a failed response
    public function failure($message, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $status);
    }

    //return a server related error response
    public function error($error, $status = 503)
    {
        return response()->json([
            'status' => false,
            'message' => "An error has occured",
            'error' => $error
        ], $status);
    }
}
