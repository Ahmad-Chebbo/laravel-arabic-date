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
     * Arabic month names.
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
     * Arabic day names.
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
     * English AM/PM to Arabic mapping.
     */
    private const ARABIC_AM_PM = [
        'AM' => 'ص',
        'PM' => 'م',
        'am' => 'ص',
        'pm' => 'م',
    ];

    /**
     * Format a date to Arabic format.
     */
    public function formatDate(Carbon $date, string $format = null): string
    {
        $format = $format ?? config('arabic-date.default_format', 'Y-m-d H:i:s');
        $formattedDate = $date->format($format);

        return $this->convertToArabic($formattedDate, $date);
    }

    /**
     * Format a date with custom Arabic format.
     */
    public function formatDateCustom(Carbon $date, string $format = null): string
    {
        $format = $format ?? config('arabic-date.custom_format', 'd F Y');

        if ($format === 'd F Y') {
            $day = config('arabic-date.enable_arabic_numerals', true)
                ? $this->convertToArabicNumerals((string) $date->day)
                : (string) $date->day;

            $month = config('arabic-date.enable_arabic_months', true)
                ? self::ARABIC_MONTHS[$date->month]
                : $date->format('F');

            $year = config('arabic-date.enable_arabic_numerals', true)
                ? $this->convertToArabicNumerals((string) $date->year)
                : (string) $date->year;

            return "{$day} {$month} {$year}";
        }

        // For other formats, use the standard approach
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

        // Replace English month names with Arabic if enabled
        if (config('arabic-date.enable_arabic_months', true)) {
            foreach (self::ARABIC_MONTHS as $monthNumber => $arabicMonth) {
                $englishMonth = $date->copy()->setMonth($monthNumber)->format('F');
                $arabicDate = str_replace($englishMonth, $arabicMonth, $arabicDate);
            }
        }

        // Replace English day names with Arabic if enabled
        if (config('arabic-date.enable_arabic_days', true)) {
            foreach (self::ARABIC_DAYS as $englishDay => $arabicDay) {
                $arabicDate = str_replace($englishDay, $arabicDay, $arabicDate);
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
}
