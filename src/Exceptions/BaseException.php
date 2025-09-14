<?php

namespace Xetreon\JsonResponse\Exceptions;

use Exception;
use Illuminate\Support\Str;
use Xetreon\JsonResponse\Traits\LoggerTrait;

class BaseException extends Exception
{
    use LoggerTrait;

    protected array $extraData = [];

    public function __construct(string $message, int $code = 400, array $extraData = [])
    {
        parent::__construct($message, $code);
        $this->extraData = $extraData;
    }

    public function render($request)
    {
        $statusCode = ($this->getCode() >= 100 && $this->getCode() <= 599)
            ? $this->getCode()
            : config('xetreon-jsonresponse.default_http_code', 500);

        $appName = config('xetreon-jsonresponse.app_name', config('app.name', 'APP'));

        $error = [
            'result' => false,
            'data' => [],
            'message' => __($this->getMessage()),
            'status_code' => $statusCode,
            'error' => [
                'type' => class_basename($this),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace_id' => (string) Str::uuid(),
                'time' => now()->toDateTimeString()
            ]
        ];

        $codeIndex = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $this->getFile());
        $classCodes = config('xetreon-jsonresponse.classcode', []);
        $error['error']['code'] = !empty($classCodes[$codeIndex])
            ? $classCodes[$codeIndex] . ($this->getLine() ? '-' . $this->getLine() : '')
            : $appName . '_COMMON';

        if (!empty($this->extraData)) {
            $error['data'] = array_merge($error['data'], $this->extraData);
        }

        $this->createErrorLog($this->getMessage(), json_decode(json_encode($error), true), 1);

        if (config('xetreon-jsonresponse.hide_file_line_in_production', true)
            && config('app.env') === 'production') {
            unset($error['error']['file'], $error['error']['line']);
        }

        return response()->json($error, $statusCode);
    }
}
