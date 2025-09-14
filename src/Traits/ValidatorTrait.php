<?php

namespace Xetreon\JsonResponse\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

trait ValidatorTrait
{
    /**
     * Check validation result and return a structured array
     */
    public function validatorResult($validator, string $displayMessage = '', ?int $httpCode = null): array
    {
        if ($validator->fails()) {
            $messages = $validator->errors()->all();

            $displayMessage = $displayMessage ?: __('Unable to validate data. Please check your input(s).');
            $httpCode = $httpCode ?? config('xetreon-jsonresponse.default_http_code', 422);

            // Auto-log if LoggerTrait exists
            if (method_exists($this, 'createErrorLog')) {
                $this->createErrorLog('Validation Failed', ['errors' => $messages], 2);
            }

            return [
                'result' => false,
                'error' => [
                    'type' => 'validation_error',
                    'details' => $messages
                ],
                'data' => [],
                'message' => $displayMessage,
                'code' => $httpCode,
                'status_code' => $httpCode
            ];
        }

        return [
            'result' => true,
            'data' => [],
            'message' => __('Validation passed'),
            'code' => 200,
            'status_code' => 200,
            'error' => []
        ];
    }

    /**
     * Run validation and return structured result
     */
    public function validate(array $input, array $rules, string $displayMessage = '', ?int $httpCode = null, array $customMessages = []): array
    {
        $validator = Validator::make($input, $rules, $customMessages);
        return $this->validatorResult($validator, $displayMessage, $httpCode);
    }

    /**
     * Validate and immediately return a JsonResponse
     */
    public function validateAndRespond(array $input, array $rules, string $displayMessage = '', ?int $httpCode = null, array $customMessages = []): JsonResponse
    {
        $result = $this->validate($input, $rules, $displayMessage, $httpCode, $customMessages);
        return response()->json($result, $result['status_code']);
    }
}
