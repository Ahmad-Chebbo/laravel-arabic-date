<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Casts;

use AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * An Eloquent attribute cast that returns an ArabicCarbon instance for a
 * date column, as an alternative to the HasArabicDates trait:
 *
 *   protected $casts = ['published_at' => \AhmadChebbo\LaravelArabicDate\Casts\ArabicDate::class];
 */
class ArabicDate implements CastsAttributes
{
    /**
     * Disable Eloquent's class-cast object caching: `get()` and `set()`
     * use different types here (Carbon in, ArabicCarbon out), so the value
     * just assigned via `set()` must never be handed back as-is on read.
     */
    public bool $withoutObjectCaching = true;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ArabicCarbon
    {
        if ($value === null) {
            return null;
        }

        $carbon = $value instanceof Carbon ? $value : Carbon::parse($value);
        $supportedLanguages = config('arabic-date.supported_languages', ['ar']);
        $arabicEnabled = in_array(app()->getLocale(), $supportedLanguages, true);

        return new ArabicCarbon($carbon, $arabicEnabled);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $carbon = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $carbon->format('Y-m-d H:i:s');
    }
}
