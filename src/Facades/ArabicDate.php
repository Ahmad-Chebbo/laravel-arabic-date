<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string formatDate(\Carbon\Carbon $date, ?string $format = null)
 * @method static string formatDateCustom(\Carbon\Carbon $date, ?string $format = null)
 * @method static string formatDateWithDay(\Carbon\Carbon $date)
 * @method static string formatDateTime(\Carbon\Carbon $date)
 * @method static string getArabicMonth(int $month)
 * @method static string getArabicDay(string $dayName)
 * @method static string convertFromArabicNumerals(string $text)
 * @method static string convertToArabicNumerals(string $text)
 * @method static array{year: int, month: int, day: int} toHijri(\Carbon\Carbon $date)
 * @method static string formatHijri(\Carbon\Carbon $date, bool $useArabicNumerals = true)
 * @method static string getHijriMonth(int $month)
 *
 * @see \AhmadChebbo\LaravelArabicDate\Services\ArabicDateService
 */
class ArabicDate extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'arabic-date';
    }
}
