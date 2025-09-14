<?php

namespace Xetreon\JsonResponse\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Build a standard success JSON response.
     */
    public function success(bool $status, mixed $data, string $displayMessage, int $code = 200): JsonResponse
    {
        return response()->json([
            'result' => $status,
            'error' => [],
            'data' => $data,
            'message' => $displayMessage,
            'status_code' => $code,
        ], $code);
    }

    /**
     * Build a standard error JSON response.
     * Automatically logs the error if LoggerTrait is used.
     */
    public function error(bool $status, mixed $data, string $displayMessage, int $code, ?string $error = null): JsonResponse
    {
        if (method_exists($this, 'createErrorLog')) {
            $this->createErrorLog($displayMessage, ['data' => $data, 'error' => $error], 2);
        }

        return response()->json([
            'result' => $status,
            'error' => $error ? [$error] : [],
            'data' => $data,
            'message' => $displayMessage,
            'status_code' => $code,
        ], $code);
    }

    /**
     * Build a validation failure response.
     * Automatically logs the error if LoggerTrait is used.
     */
    public function validationError(array $data): JsonResponse
    {
        if (method_exists($this, 'createErrorLog')) {
            $this->createErrorLog('Validation Failed', $data, 2);
        }
        return response()->json($data, $data['status_code'] ?? 422);
    }
}
