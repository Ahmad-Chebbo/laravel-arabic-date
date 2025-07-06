<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Arabic Date Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Arabic Date package.
    | You can customize the date formatting and behavior here.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Date Format
    |--------------------------------------------------------------------------
    |
    | The default format used when converting dates to Arabic format.
    |
    */
    'default_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Custom Date Format
    |--------------------------------------------------------------------------
    |
    | The format used for custom date formatting (e.g., "d F Y").
    |
    */
    'custom_format' => 'd F Y',

    /*
    |--------------------------------------------------------------------------
    | Enable Arabic Numerals
    |--------------------------------------------------------------------------
    |
    | Whether to convert numbers to Arabic numerals (٠١٢٣٤٥٦٧٨٩).
    |
    */
    'enable_arabic_numerals' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Arabic Month Names
    |--------------------------------------------------------------------------
    |
    | Whether to convert month names to Arabic.
    |
    */
    'enable_arabic_months' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Arabic Day Names
    |--------------------------------------------------------------------------
    |
    | Whether to convert day names to Arabic.
    |
    */
    'enable_arabic_days' => true,

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | The languages that will trigger Arabic date conversion.
    | Default is ['ar'] for Arabic.
    |
    */
    'supported_languages' => ['ar'],

    /*
    |--------------------------------------------------------------------------
    | Auto-convert on Model Retrieval
    |--------------------------------------------------------------------------
    |
    | Whether to automatically convert dates when models are retrieved from database.
    | This is enabled by default when using the HasArabicDates trait.
    |
    */
    'auto_convert_on_retrieval' => true,
];
