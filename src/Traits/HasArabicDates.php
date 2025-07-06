<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Traits;

use AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon;
use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use Carbon\Carbon;

trait HasArabicDates
{
    /**
     * Boot the trait and add the date casting functionality.
     */
    protected static function bootHasArabicDates(): void
    {
        // No need to modify on retrieval, we'll use accessors instead
    }

    // /**
    //  * Get the Arabic formatted date for a specific field.
    //  */
    // public function getArabicDate(string $field): ?string
    // {
    //     if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
    //         return null;
    //     }

    //     if (app()->getLocale() !== 'ar') {
    //         return $this->attributes[$field];
    //     }

    //     $arabicDateService = app(ArabicDateService::class);
    //     $date = Carbon::parse($this->attributes[$field]);

    //     dd($date);

    //     // Check if a specific format is defined for this field
    //     $format = $this->arabicDate[$field] ?? 'Y-m-d H:i:s';

    //     return $arabicDateService->formatDate($date, $format);
    // }

    /**
     * Get the original date value before Arabic conversion.
     */
    public function getOriginalDate(string $field): ?Carbon
    {
        if (!isset($this->attributes[$field])) {
            return null;
        }

        return Carbon::parse($this->attributes[$field]);
    }

    /**
     * Get the Arabic Carbon instance for a specific field.
     */
    public function getArabicCarbon(string $field): ?ArabicCarbon
    {
        if (!isset($this->attributes[$field])) {
            return null;
        }

        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);
        if (!in_array(app()->getLocale(), $supportedLanguages)) {
            return null;
        }

        $carbon = Carbon::parse($this->attributes[$field]);
        return new ArabicCarbon($carbon, true);
    }

        /**
     * Magic method to handle Arabic date conversion for specified fields.
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        // Check if this field should be converted to Arabic
        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);

        if (isset($this->arabicDate) &&
            is_array($this->arabicDate) &&
            in_array($key, $this->arabicDate) &&
            in_array(app()->getLocale(), $supportedLanguages) &&
            $value instanceof Carbon &&
            $value !== null) {

            // if the value is a carbon instance, return the original carbon
            // if ($value instanceof Carbon) {
            //     $arabicDateService = app(ArabicDateService::class);
            //     return $arabicDateService->formatDate($value, $this->arabicDate[$key] ?? config('arabic-date.default_format', 'Y-m-d H:i:s'));
            // }

            // if the value is a string, parse it to a carbon instance


            return new ArabicCarbon($value, true);
        }

        return $value;
    }

    /**
     * Check if Arabic date conversion is enabled for the current locale.
     */
    public function isArabicConversionEnabled(): bool
    {
        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);
        return in_array(app()->getLocale(), $supportedLanguages);
    }

    /**
     * Get the Arabic formatted date for a specific field.
     */
    public function getArabicDate(string $field, string $format = null): ?string
    {
        // dd($this->attributes[$field]);
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        // if (!$this->isArabicConversionEnabled()) {
        //     return $this->attributes[$field];
        // }

        $arabicDateService = app(ArabicDateService::class);
        $date = Carbon::parse($this->attributes[$field]);

        return $arabicDateService->formatDate($date, $format);
    }

    /**
     * Get the formatted date string for a specific field (uses default format from config).
     */
    public function getFormattedDate(string $field, string $format = null): ?string
    {
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        $carbon = Carbon::parse($this->attributes[$field]);

        if ($this->isArabicConversionEnabled()) {
            $arabicDateService = app(ArabicDateService::class);
            $defaultFormat = config('arabic-date.default_format', 'Y-m-d H:i:s');
            return $arabicDateService->formatDate($carbon, $format ?? $defaultFormat);
        }

        $defaultFormat = config('arabic-date.default_format', 'Y-m-d H:i:s');
        return $carbon->format($format ?? $defaultFormat);
    }
}
