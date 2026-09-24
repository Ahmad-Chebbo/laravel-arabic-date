<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Traits;

use AhmadChebbo\LaravelArabicDate\Attributes\ArabicDate as ArabicDateAttribute;
use AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon;
use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use Carbon\Carbon;

trait HasArabicDates
{
    /**
     * Initialize the trait for each model instance: every field returned by
     * `getArabicDateFields()` (the `$arabicDate` property and/or the
     * #[ArabicDate] attribute) is auto-cast to `datetime` unless already
     * cast, so the magic `__get` accessor below always receives a Carbon
     * instance to convert, without requiring the consumer to separately
     * declare `$casts` for those fields.
     */
    protected function initializeHasArabicDates(): void
    {
        if (!config('arabic-date.auto_convert_on_retrieval', true)) {
            return;
        }

        $casts = [];

        foreach ($this->getArabicDateFields() as $field) {
            if (!array_key_exists($field, $this->getCasts())) {
                $casts[$field] = 'datetime';
            }
        }

        if ($casts !== []) {
            $this->mergeCasts($casts);
        }
    }

    /**
     * Get the effective list of Arabic date fields: the `$arabicDate`
     * property (if declared) merged with fields declared via the
     * #[ArabicDate(['field'])] class attribute — an alternative syntax
     * mirroring Eloquent's own #[Fillable]/#[Hidden] attributes.
     *
     * The attribute requires Laravel's attribute-based model configuration
     * support (Illuminate\Database\Eloquent\Model::resolveClassAttribute());
     * on older Laravel versions it's simply ignored.
     *
     * This never writes back to `$this->arabicDate`, since assigning to a
     * property that isn't declared anywhere in the model's hierarchy would
     * route through Eloquent's `__set()` and be treated as a database
     * attribute instead of package configuration.
     *
     * @return array<int, string>
     */
    protected function getArabicDateFields(): array
    {
        $fromProperty = isset($this->arabicDate) && is_array($this->arabicDate) ? $this->arabicDate : [];

        $fromAttribute = method_exists($this, 'resolveClassAttribute')
            ? (static::resolveClassAttribute(ArabicDateAttribute::class, 'fields') ?? [])
            : [];

        if ($fromAttribute === []) {
            return $fromProperty;
        }

        return array_values(array_unique(array_merge($fromProperty, $fromAttribute)));
    }

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
    public function getArabicCarbon(string $field, ?string $locale = null): ?ArabicCarbon
    {
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        $carbon = Carbon::parse($this->attributes[$field]);

        return new ArabicCarbon($carbon, $this->isArabicConversionEnabled($locale));
    }

    /**
     * Magic method to handle Arabic date conversion for specified fields.
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);

        if (in_array($key, $this->getArabicDateFields()) &&
            in_array(app()->getLocale(), $supportedLanguages) &&
            $value instanceof Carbon) {
            return new ArabicCarbon($value, true);
        }

        return $value;
    }

    /**
     * Get the configured `$arabicDate` attributes on this model as
     * formatted strings, using the Arabic format when conversion is
     * enabled for the current (or given) locale, and a readable
     * fallback format otherwise.
     *
     * This does not mutate the model's underlying attributes (writing a
     * non-parseable Arabic string into a `datetime`-cast attribute would
     * break every subsequent read of it); it returns a plain
     * `field => formatted string` array instead, e.g. for building an
     * API resource response.
     *
     * @return array<string, string>
     */
    public function convertDatesToArabic(?string $locale = null): array
    {
        $arabicDateService = app(ArabicDateService::class);
        $format = config('arabic-date.custom_format', 'd F Y');
        $arabicEnabled = $this->isArabicConversionEnabled($locale);
        $converted = [];

        foreach ($this->getArabicDateFields() as $field) {
            if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
                continue;
            }

            $date = $this->attributes[$field] instanceof Carbon
                ? $this->attributes[$field]
                : Carbon::parse($this->attributes[$field]);

            $converted[$field] = $arabicEnabled
                ? $arabicDateService->formatDateCustom($date, $format)
                : $date->format($format);
        }

        return $converted;
    }

    /**
     * Check if Arabic date conversion is enabled for the current (or given) locale.
     */
    public function isArabicConversionEnabled(?string $locale = null): bool
    {
        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);

        return in_array($locale ?? app()->getLocale(), $supportedLanguages);
    }

    /**
     * Get the Arabic formatted date for a specific field.
     */
    public function getArabicDate(string $field, ?string $format = null): ?string
    {
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        $arabicDateService = app(ArabicDateService::class);
        $date = Carbon::parse($this->attributes[$field]);

        return $arabicDateService->formatDate($date, $format);
    }

    /**
     * Get the formatted date string for a specific field (uses default format from config).
     */
    public function getFormattedDate(string $field, ?string $format = null, ?string $locale = null): ?string
    {
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        $carbon = Carbon::parse($this->attributes[$field]);
        $defaultFormat = config('arabic-date.default_format', 'Y-m-d H:i:s');

        if ($this->isArabicConversionEnabled($locale)) {
            $arabicDateService = app(ArabicDateService::class);

            return $arabicDateService->formatDate($carbon, $format ?? $defaultFormat);
        }

        return $carbon->format($format ?? $defaultFormat);
    }

    /**
     * Get the Hijri (Islamic calendar) formatted date for a specific field.
     */
    public function getHijriDate(string $field): ?string
    {
        if (!isset($this->attributes[$field]) || !$this->attributes[$field]) {
            return null;
        }

        $date = $this->attributes[$field] instanceof Carbon
            ? $this->attributes[$field]
            : Carbon::parse($this->attributes[$field]);

        return app(ArabicDateService::class)->formatHijri($date);
    }
}
