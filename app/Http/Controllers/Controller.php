<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function success(mixed $data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    protected function error(mixed $errors, string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
            'message' => $message,
        ], $status);
    }
}
