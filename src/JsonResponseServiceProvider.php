<?php

namespace Xetreon\JsonResponse;

use Illuminate\Support\ServiceProvider;

class JsonResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Publish config so users can override default values
        $this->publishes([
            __DIR__ . '/../config/xetreon-jsonresponse.php' => config_path('xetreon-jsonresponse.php'),
        ], 'config');

        // Optional: Allow users to publish traits (for customization/overriding)
        $this->publishes([
            __DIR__ . '/Traits/LoggerTrait.php' => app_path('Traits/LoggerTrait.php'),
            __DIR__ . '/Traits/ResponseTrait.php' => app_path('Traits/ResponseTrait.php'),
            __DIR__ . '/Traits/ValidatorTrait.php' => app_path('Traits/ValidatorTrait.php'),
        ], 'traits');

        // Optional: Publish Controller
        $this->publishes([
            __DIR__ . '/Controllers/Publish/BaseController.php' => app_path('Http/Controllers/BaseController.php')
        ], 'controller');
        // Optional: Publish Exception (for controller or exception boilerplate)
        $this->publishes([
            __DIR__ . '/Exceptions/Publish/BaseException.php'  => app_path('Exceptions/BaseException.php'),
        ], 'exception');
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        // Merge default config so users can override only what they need
        $this->mergeConfigFrom(
            __DIR__ . '/../config/xetreon-jsonresponse.php',
            'xetreon-jsonresponse'
        );
    }
}
