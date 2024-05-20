<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function successResponse($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function errorResponse($message, $statusCode)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}