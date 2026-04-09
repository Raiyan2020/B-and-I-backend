<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;

if (! function_exists('responseJson')) {
    /**
     * Unified JSON API response aligned with the application's standard schema.
     *
     * @param  array<string, mixed>|null  $errors
     */
    function responseJson(
        string $msg,
        int $code = 200,
        bool $error = false,
        ?array $errors = null,
        ?string $key = null,
    ): JsonResponse
    {
        return response()->json([
            'key' => $key ?? ($error ? 'fail' : 'success'),
            'msg' => $msg,
            'code' => $code,
            'response_status' => [
                'error' => $error,
                'validation_errors' => $errors ?? [],
            ],
            'data' => null,
        ], $code);
    }
}
