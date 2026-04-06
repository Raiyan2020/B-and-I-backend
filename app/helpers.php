<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;

if (! function_exists('responseJson')) {
    /**
     * Unified JSON API response (message, error flag, optional errors array and key).
     *
     * @param  array<string, mixed>|null  $errors
     */
    function responseJson(
        string $msg,
        int $code = 200,
        bool $error = false,
        ?array $errors = null,
        ?string $key = null,
    ): JsonResponse {
        $data = [
            'message' => $msg,
            'error' => $error,
        ];
        if ($errors !== null) {
            $data['errors'] = $errors;
        }
        if ($key !== null) {
            $data['key'] = $key;
        }

        return response()->json($data, $code);
    }
}
