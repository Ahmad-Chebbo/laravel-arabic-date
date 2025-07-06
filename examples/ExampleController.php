<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Examples;

use AhmadChebbo\LaravelArabicDate\Facades\ArabicDate;
use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
    public function __construct(
        private ArabicDateService $arabicDateService
    ) {}

    /**
     * Example of using the facade directly.
     */
    public function facadeExample(): array
    {
        $date = Carbon::now();

        return [
            'basic_format' => ArabicDate::formatDate($date),
            'custom_format' => ArabicDate::formatDateCustom($date),
            'with_day' => ArabicDate::formatDateWithDay($date),
            'with_time' => ArabicDate::formatDateTime($date),
        ];
    }

    /**
     * Example of using dependency injection.
     */
    public function serviceExample(): array
    {
        $date = Carbon::create(2024, 1, 15, 14, 30, 0);

        return [
            'basic_format' => $this->arabicDateService->formatDate($date),
            'custom_format' => $this->arabicDateService->formatDateCustom($date),
            'with_day' => $this->arabicDateService->formatDateWithDay($date),
            'with_time' => $this->arabicDateService->formatDateTime($date),
        ];
    }

    /**
     * Example of conditional formatting based on locale.
     */
    public function conditionalExample(Request $request): array
    {
        $date = Carbon::now();
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            $formattedDate = ArabicDate::formatDateCustom($date);
        } else {
            $formattedDate = $date->format('d F Y');
        }

        return [
            'locale' => $locale,
            'formatted_date' => $formattedDate,
            'is_arabic' => $locale === 'ar',
        ];
    }

    /**
     * Example of switching locale and formatting.
     */
    public function localeSwitchExample(Request $request): array
    {
        $date = Carbon::now();
        $results = [];

        // Test with English locale
        app()->setLocale('en');
        $results['english'] = [
            'locale' => app()->getLocale(),
            'date' => $date->format('d F Y'),
            'arabic_date' => ArabicDate::formatDateCustom($date),
        ];

        // Test with Arabic locale
        app()->setLocale('ar');
        $results['arabic'] = [
            'locale' => app()->getLocale(),
            'date' => $date->format('d F Y'),
            'arabic_date' => ArabicDate::formatDateCustom($date),
        ];

        return $results;
    }
}
