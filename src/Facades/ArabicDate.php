<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string formatDate(\Carbon\Carbon $date, string $format = 'Y-m-d H:i:s')
 * @method static string formatDateCustom(\Carbon\Carbon $date, string $format = 'd F Y')
 * @method static string formatDateWithDay(\Carbon\Carbon $date)
 * @method static string formatDateTime(\Carbon\Carbon $date)
 * @method static string getArabicMonth(int $month)
 * @method static string getArabicDay(string $dayName)
 * @method static string convertFromArabicNumerals(string $text)
 * @method static string convertToArabicNumerals(string $text)
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
