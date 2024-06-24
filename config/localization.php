<?php

return [

    // Notice that any format key must  be present in the default '*' section
    // i.e. keys that are available in a specific locale like 'en', 'ja'...
    // but are not available in '*' shall be considered INVALID

    // Get a localization setting by using the "get_localization_config()" helper
    // For example:
    //
    //     get_localization_config('get_localization_config', 'en')

    // BTW, use the following methods to work with datetime
    //
    //     Customer::formatDateTime(Carbon $datetime, $name)
    //
    // or, for pages that are not associated to a customer/user (login, registration, etc.):
    //
    //     format_datetime(Carbon $datetime, $name, $locale) || locale can be 'en', 'ja', etc.
    //

    // Defaut '*'
    '*' => [
        'date_full' => 'Y-m-d',
        'date_short' => 'Y-m-d',
        'date_without_year' => 'M-j', // Apr-11
        'datetime_full' => 'Y-m-d H:i',
        'datetime_full_with_timezone' => 'Y-m-d H:i T',
        'datetime_short' => 'Y-m-d H:i',
        'time_only' => 'H:i',
        'number_precision' => '2',
        'number_decimal_separator' => '.',
        'number_thousands_separator' => ',',
        'show_last_name_first' => false,
    ],

    // Add localization settings here for a given locale
    //
    // Note:
    // Only add settings that are different than the default ones
    // as the application will use default values (above) for settings
    // that are not available in a specific locale.
    'ja' => [
        'date_full' => 'Y年m月d日',
        'date_short' => 'Y/m/d',
        'datetime_full' => 'Y年m月d日 H:i',
        'datetime_short' => 'Y/m/d H:i',
        'time_only' => 'H:i',
        'show_last_name_first' => true,
    ],
];
