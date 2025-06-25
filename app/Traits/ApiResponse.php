<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data ?? (object) [],
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode, $headers);
    }

    public function error(
        string $message = 'Error',
        int $statusCode = 400,
        array|string $errors = [],
        array $meta = [],
        array $headers = [],
        string $errorCode = 'GENERAL_ERROR'
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => is_array($errors) ? $errors : ['error' => $errors],
            'error_code' => $errorCode,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode, $headers);
    }
}
