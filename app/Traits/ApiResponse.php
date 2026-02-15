<?php

namespace App\Traits;

trait ApiResponse
{
    public function success(mixed $data = null, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public function error(string $code, string $message, mixed $details = null, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
