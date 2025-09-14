<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Validation Error Message
    |--------------------------------------------------------------------------
    |
    | This message will be returned when validation fails and no custom
    | message is provided. You can override this by passing a message
    | to the validate() or validatorResult() methods.
    |
    */
    'validation_error_message' => 'Unable to validate data. Please check your input(s).',

    /*
    |--------------------------------------------------------------------------
    | Default HTTP Code
    |--------------------------------------------------------------------------
    |
    | The default HTTP status code to use when validation fails or when
    | an error is thrown without a specified status code.
    |
    */
    'default_http_code' => 422,

    /*
    |--------------------------------------------------------------------------
    | Error Logging
    |--------------------------------------------------------------------------
    |
    | Whether to automatically log errors when using buildUnsuccessful()
    | or validationError(). You may also specify a custom log channel.
    |
    */
    'log_errors' => true,
    'log_channel' => env('XETREON_JSON_LOG_CHANNEL', config('logging.default')),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | Used for generating error codes and log prefixes. Defaults to the
    | app.name config value if not explicitly set here.
    |
    */
    'app_name' => env('APP_NAME', config('app.name', 'APP')),

    /*
    |--------------------------------------------------------------------------
    | Hide File & Line Info in Production
    |--------------------------------------------------------------------------
    |
    | When set to true, file and line details will be hidden in the
    | rendered JSON response for security reasons in production.
    |
    */
    'hide_file_line_in_production' => true,

    /*
    |--------------------------------------------------------------------------
    | Class Code Map
    |--------------------------------------------------------------------------
    |
    | Map specific files to unique codes to make error codes more readable.
    | Example:
    | 'app/Http/Controllers/UserController.php' => 'USR'
    |
    */
    'classcode' => [

    ],
];
