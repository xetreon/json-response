<?php

namespace Xetreon\JsonResponse\Traits;

use Illuminate\Support\Facades\Log;

trait LoggerTrait
{
    /**
     * Log an info message
     */
    public function createLog(string $message, array $data = [], int $trace = 1): string
    {
        $formatted = $this->formatMessage($message, $trace);
        Log::channel($this->getLogChannel())->info($formatted, $this->normalizeContext($data));
    }

    /**
     * Log an error message
     */
    public function createErrorLog(string $message, array $data = [], int $trace = 1): string
    {
        $formatted = $this->formatMessage($message, $trace);
        Log::channel($this->getLogChannel())->error($formatted, $this->normalizeContext($data));
    }

    /**
     * Log a warning message
     */
    public function createWarningLog(string $message, array $data = [], int $trace = 1): string
    {
        $formatted = $this->formatMessage($message, $trace);
        Log::channel($this->getLogChannel())->warning($formatted, $this->normalizeContext($data));
    }

    /**
     * Determine which log channel to use
     */
    private function getLogChannel(): string
    {
        return config('xetreon-jsonresponse.log_channel', config('logging.default'));
    }

    /**
     * Format the log message with file/line code
     */
    private function formatMessage(string $message, int $trace): string
    {
        $file = $this->getFile($trace);
        $line = $this->getLine($trace);

        $appName = config('xetreon-jsonresponse.app_name', config('app.name', 'APP'));
        $code = $appName . '_COMMON';

        $classCode = config('xetreon-jsonresponse.classcode', []);
        if (!empty($classCode[$file])) {
            $code = $classCode[$file] . ($line ? "-{$line}" : '');
        }

        return "{$code} - {$message}";
    }

    /**
     * Normalize log context to avoid issues with non-serializable data
     */
    private function normalizeContext(array $data): array
    {
        return json_decode(json_encode($data), true) ?? [];
    }

    /**
     * Get file from debug_backtrace
     */
    private function getFile(int $traceTrack = 1): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $traceTrack + 1);
        $file = $trace[$traceTrack]['file'] ?? '';
        return $file ? str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file) : '';
    }

    /**
     * Get line from debug_backtrace
     */
    private function getLine(int $traceTrack = 1): int
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $traceTrack + 1);
        return $trace[$traceTrack]['line'] ?? 0;
    }
}
