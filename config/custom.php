<?php

return [
    // The standard format for storing date/time string
    // Used by Carbon and MySQL
    // Important: both `date_format` (for PHP) and `date_format_sql` (for MySQL) must produce the SAME output string
    'date_format' => 'Y-m-d',           // 2020-12-25
    'date_format_sql' => '%Y-%m-%d',    // 2020-12-25

    // Timeformat for storing
    'time_format' => 'H:i',

    // Minimum recommended PHP version
    // which is required by Laravel 8
    'php_recommended' => '7.3.0',

    // Minimum supported PHP version
    'php' => '7.3.0',

    // Branding
    'default_logo_light' => env('APP_DEFAULT_LOGO_LIGHT', 'images/logo_light.svg'),
    'default_logo_dark' => env('APP_DEFAULT_LOGO_DARK', 'images/logo_dark.svg'),

    // Beta features
    'woo' => false,

    // Special settings
    'japan' => env('APP_JAPAN', false),

    // If "APP_PROFILE=" (empty), then make it return null too
    'app_profile' => env('APP_PROFILE', null) ?: null,

    // Dry run mode, do not actually send anything
    'dryrun' => env('APP_DRYRUN', false),

    // License verification endpoint
    'license_verification_endpoint' => env('LICENSE_VALIDATION_ENDPOINT', 'http://verify.acellemail.com')
];
