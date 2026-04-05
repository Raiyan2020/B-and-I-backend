<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function jsonResponse(string $msg = null, int $code = 200, $data = [], bool $error = false, array $errors = [], $key = null): JsonResponse
    {
        return response()->json([
            'key'             => $key ?? ($error ? 'fail' : 'success'),
            'msg'             => $msg ?? __('apis.data_retrieved_successfully'),
            'code'            => $code,
            'response_status' => [
                'error'             => $error,
                'validation_errors' => $errors
            ],
            'data'            => $this->checkIfEmpty($data) ? null : $data,
        ], $code);
    }

    protected function checkIfEmpty($data): bool
    {

        return $data instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection ? $data->collection->isEmpty() : empty($data);
    }
}
