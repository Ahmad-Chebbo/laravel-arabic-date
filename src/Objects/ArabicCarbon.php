<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Objects;

use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use Carbon\Carbon;

class ArabicCarbon
{
    /**
     * The original Carbon instance.
     */
    protected Carbon $originalCarbon;

    /**
     * Whether Arabic conversion is enabled.
     */
    protected bool $arabicEnabled;

    /**
     * Create a new ArabicCarbon instance.
     */
    public function __construct(Carbon $carbon, bool $arabicEnabled = true)
    {
        $this->originalCarbon = $carbon;
        $this->arabicEnabled = $arabicEnabled;
    }

    /**
     * Boot the ArabicCarbon class and set up any necessary configurations.
     */
    public static function boot(): void
    {
        // Initialize any required configurations or dependencies
        // This method can be called during the application bootstrap process
        // by default call the format method
        // $this->format();
    }

    /**
     * Create a new ArabicCarbon instance from a date string.
     */
    public static function parse(string $date): self
    {
        $carbon = Carbon::parse($date);
        return new self($carbon, true);
    }

    /**
     * Create a new ArabicCarbon instance for the current date and time.
     */
    public static function now(): self
    {
        $carbon = Carbon::now();
        return new self($carbon, true);
    }

    /**
     * Create a new ArabicCarbon instance for today's date.
     */
    public static function today(): self
    {
        $carbon = Carbon::today();
        return new self($carbon, true);
    }

    /**
     * Create a new ArabicCarbon instance for yesterday's date.
     */
    public static function yesterday(): self
    {
        $carbon = Carbon::yesterday();
        return new self($carbon);
    }

    /**
     * Create a new ArabicCarbon instance for tomorrow's date.
     */
    public static function tomorrow(): self
    {
        $carbon = Carbon::tomorrow();
        return new self($carbon);
    }

    /**
     * Format the date with Arabic conversion if enabled.
     */
    public function format($format = null): string
    {
        if (!$this->arabicEnabled || !$this->isArabicConversionEnabled()) {
            return $this->originalCarbon->format($format);
        }

        $arabicDateService = app(ArabicDateService::class);
        return $arabicDateService->formatDate($this->originalCarbon, $format);
    }

    /**
     * Get the original Carbon instance.
     */
    public function getOriginalCarbon(): Carbon
    {
        return $this->originalCarbon;
    }

    /**
     * Check if Arabic conversion is enabled for the current locale.
     */
    protected function isArabicConversionEnabled(): bool
    {
        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);
        return in_array(app()->getLocale(), $supportedLanguages);
    }

    /**
     * Convert to string representation.
     */
    public function __toString(): string
    {
        $defaultFormat = config('arabic-date.default_format', 'Y-m-d H:i:s');
        return $this->format($defaultFormat);
    }

    /**
     * Magic method to delegate all Carbon methods to the original Carbon instance.
     */
    public function __call($method, $arguments)
    {
        return $this->originalCarbon->$method(...$arguments);
    }

    /**
     * Magic method to get Carbon properties.
     */
    public function __get($property)
    {
        return $this->originalCarbon->$property;
    }

    /**
     * Magic method to set Carbon properties.
     */
    public function __set($property, $value)
    {
        $this->originalCarbon->$property = $value;
    }

    /**
     * Magic method to check if a method exists.
     */
    public function __isset($property)
    {
        return isset($this->originalCarbon->$property);
    }

    /**
     * Get the Arabic formatted date.
     */
    public function toArabic(): string
    {
        $arabicDateService = app(ArabicDateService::class);
        return $arabicDateService->formatDate($this->originalCarbon);
    }

    /**
     * Get the Arabic formatted date with custom format.
     */
    public function toArabicFormat(string $format): string
    {
        $arabicDateService = app(ArabicDateService::class);
        return $arabicDateService->formatDate($this->originalCarbon, $format);
    }

    /**
     * Get the Arabic formatted date with day name.
     */
    public function toArabicWithDay(): string
    {
        $arabicDateService = app(ArabicDateService::class);
        return $arabicDateService->formatDateWithDay($this->originalCarbon);
    }

    /**
     * Get the Arabic formatted date with time.
     */
    public function toArabicWithTime(): string
    {
        $arabicDateService = app(ArabicDateService::class);
        return $arabicDateService->formatDateTime($this->originalCarbon);
    }
}
