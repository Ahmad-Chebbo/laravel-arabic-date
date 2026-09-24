<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Services;

use Carbon\Carbon;

class ArabicDateService
{
    /**
     * Arabic numerals mapping.
     */
    private const ARABIC_NUMERALS = [
        '0' => '٠',
        '1' => '١',
        '2' => '٢',
        '3' => '٣',
        '4' => '٤',
        '5' => '٥',
        '6' => '٦',
        '7' => '٧',
        '8' => '٨',
        '9' => '٩',
    ];

    /**
     * Arabic month names, keyed by month number.
     */
    private const ARABIC_MONTHS = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر',
    ];

    /**
     * English month names, keyed by month number (mirrors Carbon's `F` format).
     */
    private const ENGLISH_MONTHS = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    /**
     * English abbreviated month names (Carbon's `M` format) mapped to their Arabic month.
     */
    private const ENGLISH_MONTHS_SHORT = [
        'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
        'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12,
    ];

    /**
     * Arabic day names, keyed by English day name.
     */
    private const ARABIC_DAYS = [
        'Sunday' => 'الأحد',
        'Monday' => 'الاثنين',
        'Tuesday' => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday' => 'الخميس',
        'Friday' => 'الجمعة',
        'Saturday' => 'السبت',
    ];

    /**
     * English abbreviated day names (Carbon's `D` format) mapped to their full English day name.
     */
    private const ENGLISH_DAYS_SHORT = [
        'Sun' => 'Sunday',
        'Mon' => 'Monday',
        'Tue' => 'Tuesday',
        'Wed' => 'Wednesday',
        'Thu' => 'Thursday',
        'Fri' => 'Friday',
        'Sat' => 'Saturday',
    ];

    /**
     * English AM/PM to Arabic mapping.
     */
    private const ARABIC_AM_PM = [
        'AM' => 'ص',
        'PM' => 'م',
        'am' => 'ص',
        'pm' => 'م',
    ];

    /**
     * Hijri month names, keyed by month number.
     */
    private const HIJRI_MONTHS = [
        1 => 'محرم',
        2 => 'صفر',
        3 => 'ربيع الأول',
        4 => 'ربيع الآخر',
        5 => 'جمادى الأولى',
        6 => 'جمادى الآخرة',
        7 => 'رجب',
        8 => 'شعبان',
        9 => 'رمضان',
        10 => 'شوال',
        11 => 'ذو القعدة',
        12 => 'ذو الحجة',
    ];

    /**
     * Format a date to Arabic format.
     */
    public function formatDate(Carbon $date, ?string $format = null): string
    {
        $format ??= config('arabic-date.default_format', 'Y-m-d H:i:s');
        $formattedDate = $date->format($format);

        return $this->convertToArabic($formattedDate, $date);
    }

    /**
     * Format a date with custom Arabic format.
     */
    public function formatDateCustom(Carbon $date, ?string $format = null): string
    {
        $format ??= config('arabic-date.custom_format', 'd F Y');
        $formattedDate = $date->format($format);

        return $this->convertToArabic($formattedDate, $date);
    }

    /**
     * Format a date with day name.
     */
    public function formatDateWithDay(Carbon $date): string
    {
        $dayName = config('arabic-date.enable_arabic_days', true)
            ? self::ARABIC_DAYS[$date->format('l')]
            : $date->format('l');

        $day = config('arabic-date.enable_arabic_numerals', true)
            ? $this->convertToArabicNumerals((string) $date->day)
            : (string) $date->day;

        $month = config('arabic-date.enable_arabic_months', true)
            ? self::ARABIC_MONTHS[$date->month]
            : $date->format('F');

        $year = config('arabic-date.enable_arabic_numerals', true)
            ? $this->convertToArabicNumerals((string) $date->year)
            : (string) $date->year;

        return "{$dayName} {$day} {$month} {$year}";
    }

    /**
     * Format a date with time.
     */
    public function formatDateTime(Carbon $date): string
    {
        $datePart = $this->formatDateCustom($date);
        $time = config('arabic-date.enable_arabic_numerals', true)
            ? $this->convertToArabicNumerals($date->format('H:i:s'))
            : $date->format('H:i:s');

        return "{$datePart} {$time}";
    }

    /**
     * Convert a formatted date string to Arabic, including AM/PM support.
     */
    private function convertToArabic(string $formattedDate, Carbon $date): string
    {
        $arabicDate = $formattedDate;

        // Convert AM/PM to Arabic if present
        foreach (self::ARABIC_AM_PM as $en => $ar) {
            $arabicDate = str_replace($en, $ar, $arabicDate);
        }

        // Convert numbers to Arabic numerals if enabled
        if (config('arabic-date.enable_arabic_numerals', true)) {
            $arabicDate = $this->convertToArabicNumerals($arabicDate);
        }

        // Replace English month names (full, then abbreviated) with Arabic if enabled
        if (config('arabic-date.enable_arabic_months', true)) {
            foreach (self::ENGLISH_MONTHS as $monthNumber => $englishMonth) {
                $arabicDate = str_replace($englishMonth, self::ARABIC_MONTHS[$monthNumber], $arabicDate);
            }

            foreach (self::ENGLISH_MONTHS_SHORT as $englishMonth => $monthNumber) {
                $arabicDate = str_replace($englishMonth, self::ARABIC_MONTHS[$monthNumber], $arabicDate);
            }
        }

        // Replace English day names (full, then abbreviated) with Arabic if enabled
        if (config('arabic-date.enable_arabic_days', true)) {
            foreach (self::ARABIC_DAYS as $englishDay => $arabicDay) {
                $arabicDate = str_replace($englishDay, $arabicDay, $arabicDate);
            }

            foreach (self::ENGLISH_DAYS_SHORT as $shortDay => $fullDay) {
                $arabicDate = str_replace($shortDay, self::ARABIC_DAYS[$fullDay], $arabicDate);
            }
        }

        return $arabicDate;
    }

    /**
     * Convert numbers to Arabic numerals.
     */
    public function convertToArabicNumerals(string $text): string
    {
        return strtr($text, self::ARABIC_NUMERALS);
    }

    /**
     * Get Arabic month name by number.
     */
    public function getArabicMonth(int $month): string
    {
        return self::ARABIC_MONTHS[$month] ?? '';
    }

    /**
     * Get Arabic day name.
     */
    public function getArabicDay(string $dayName): string
    {
        return self::ARABIC_DAYS[$dayName] ?? $dayName;
    }

    /**
     * Convert Arabic numerals back to English.
     */
    public function convertFromArabicNumerals(string $text): string
    {
        $englishNumerals = array_flip(self::ARABIC_NUMERALS);

        return strtr($text, $englishNumerals);
    }

    /**
     * Convert a Gregorian date to its civil (tabular) Hijri equivalent.
     *
     * Uses the tabular Islamic calendar algorithm, which is a fixed
     * arithmetic approximation. It may differ by a day from
     * observation-based calendars (e.g. Umm al-Qura) near month boundaries.
     *
     * @return array{year: int, month: int, day: int}
     */
    public function toHijri(Carbon $date): array
    {
        $julianDay = $this->gregorianToJulianDay((int) $date->year, (int) $date->month, (int) $date->day);

        $julianDay = $julianDay - 1948440 + 10632;
        $cycles = intdiv($julianDay - 1, 10631);
        $julianDay = $julianDay - 10631 * $cycles + 354;
        $j = intdiv(10985 - $julianDay, 5316) * intdiv(50 * $julianDay, 17719)
            + intdiv($julianDay, 5670) * intdiv(43 * $julianDay, 15238);
        $julianDay = $julianDay - intdiv(30 - $j, 15) * intdiv(17719 * $j, 50)
            - intdiv($j, 16) * intdiv(15238 * $j, 43) + 29;

        $month = intdiv(24 * $julianDay, 709);
        $day = $julianDay - intdiv(709 * $month, 24);
        $year = 30 * $cycles + $j - 30;

        return ['year' => $year, 'month' => $month, 'day' => $day];
    }

    /**
     * Format a date as a Hijri (Islamic calendar) Arabic string, e.g. "٤ رجب ١٤٤٥هـ".
     */
    public function formatHijri(Carbon $date, bool $useArabicNumerals = true): string
    {
        $hijri = $this->toHijri($date);

        $day = $useArabicNumerals ? $this->convertToArabicNumerals((string) $hijri['day']) : (string) $hijri['day'];
        $year = $useArabicNumerals ? $this->convertToArabicNumerals((string) $hijri['year']) : (string) $hijri['year'];
        $month = self::HIJRI_MONTHS[$hijri['month']] ?? '';

        return "{$day} {$month} {$year}هـ";
    }

    /**
     * Get the Hijri month name by number.
     */
    public function getHijriMonth(int $month): string
    {
        return self::HIJRI_MONTHS[$month] ?? '';
    }

    /**
     * Convert a proleptic Gregorian calendar date to a Julian Day Number.
     *
     * Pure-PHP implementation (Fliegel & Van Flandern algorithm) so this
     * does not depend on the optional ext-calendar extension.
     */
    private function gregorianToJulianDay(int $year, int $month, int $day): int
    {
        $a = intdiv(14 - $month, 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;

        return $day + intdiv(153 * $m + 2, 5) + 365 * $y + intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400) - 32045;
    }
}
