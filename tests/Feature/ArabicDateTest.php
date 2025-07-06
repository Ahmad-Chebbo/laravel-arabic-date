<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Tests\Feature;

use AhmadChebbo\LaravelArabicDate\Facades\ArabicDate;
use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use AhmadChebbo\LaravelArabicDate\Tests\TestCase;
use AhmadChebbo\LaravelArabicDate\Tests\Feature\ExampleModel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArabicDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_arabic_date_service_can_format_dates(): void
    {
        $service = app(ArabicDateService::class);
        $date = Carbon::create(2024, 1, 15, 14, 30, 0);

        $formatted = $service->formatDate($date);

        $this->assertStringContainsString('٢٠٢٤', $formatted);
        $this->assertStringContainsString('٠١', $formatted);
        $this->assertStringContainsString('١٥', $formatted);
    }

    public function test_arabic_date_facade_works(): void
    {
        $date = Carbon::create(2024, 1, 15, 14, 30, 0);

        $formatted = ArabicDate::formatDateCustom($date);

        $this->assertStringContainsString('يناير', $formatted);
        $this->assertStringContainsString('١٥', $formatted);
        $this->assertStringContainsString('٢٠٢٤', $formatted);
    }

    public function test_arabic_date_with_day_name(): void
    {
        $date = Carbon::create(2024, 1, 15, 14, 30, 0); // Monday

        $formatted = ArabicDate::formatDateWithDay($date);

        $this->assertStringContainsString('الاثنين', $formatted);
        $this->assertStringContainsString('يناير', $formatted);
    }

    public function test_arabic_date_with_time(): void
    {
        $date = Carbon::create(2024, 1, 15, 14, 30, 0);

        $formatted = ArabicDate::formatDateTime($date);

        $this->assertStringContainsString('يناير', $formatted);
        $this->assertStringContainsString('١٤:٣٠:٠٠', $formatted);
    }

    public function test_arabic_numerals_conversion(): void
    {
        $service = app(ArabicDateService::class);

        $this->assertEquals('٠١٢٣٤٥٦٧٨٩', $service->convertToArabicNumerals('0123456789'));
    }

    public function test_arabic_month_names(): void
    {
        $service = app(ArabicDateService::class);

        $this->assertEquals('يناير', $service->getArabicMonth(1));
        $this->assertEquals('ديسمبر', $service->getArabicMonth(12));
    }

    public function test_arabic_day_names(): void
    {
        $service = app(ArabicDateService::class);

        $this->assertEquals('الاثنين', $service->getArabicDay('Monday'));
        $this->assertEquals('الجمعة', $service->getArabicDay('Friday'));
    }

        public function test_convert_from_arabic_numerals(): void
    {
        $service = app(ArabicDateService::class);

        $this->assertEquals('0123456789', $service->convertFromArabicNumerals('٠١٢٣٤٥٦٧٨٩'));
    }

    public function test_model_trait_works_with_arabic_locale(): void
    {
        // Set locale to Arabic
        app()->setLocale('ar');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);
        $model->updated_at = Carbon::create(2024, 1, 16, 10, 15, 0);

        // Trigger the conversion
        $model->convertDatesToArabic();

        // Check if dates are converted to Arabic
        $this->assertStringContainsString('٢٠٢٤', $model->created_at);
        $this->assertStringContainsString('يناير', $model->created_at);
    }

        public function test_model_trait_does_not_convert_when_locale_is_not_arabic(): void
    {
        // Set locale to English
        app()->setLocale('en');

        // Create a model instance
        $model = new ExampleModel();
        $originalDate = Carbon::create(2024, 1, 15, 14, 30, 0);
        $model->created_at = $originalDate;

        // Trigger the conversion
        $model->convertDatesToArabic();

        // Check if dates are NOT converted (should remain in English format)
        $this->assertStringContainsString('2024', $model->created_at);
        $this->assertStringContainsString('Jan', $model->created_at);
    }

    public function test_arabic_carbon_format_method_works(): void
    {
        // Set locale to Arabic
        app()->setLocale('ar');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        // Get the ArabicCarbon instance
        $arabicCarbon = $model->getArabicCarbon('created_at');

        // Test format method
        $formatted = $arabicCarbon->format('Y-m-d');
        $this->assertStringContainsString('٢٠٢٤', $formatted);
        $this->assertStringContainsString('٠١', $formatted);
        $this->assertStringContainsString('١٥', $formatted);
    }

        public function test_arabic_carbon_special_methods(): void
    {
        // Set locale to Arabic
        app()->setLocale('ar');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        // Get the ArabicCarbon instance
        $arabicCarbon = $model->getArabicCarbon('created_at');

        // Test special methods
        $this->assertStringContainsString('يناير', $arabicCarbon->toArabic());
        $this->assertStringContainsString('الاثنين', $arabicCarbon->toArabicWithDay());
        $this->assertStringContainsString('١٤:٣٠:٠٠', $arabicCarbon->toArabicWithTime());
    }

    public function test_arabic_carbon_delegates_to_carbon(): void
    {
        // Set locale to Arabic
        app()->setLocale('ar');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        // Get the ArabicCarbon instance
        $arabicCarbon = $model->getArabicCarbon('created_at');

        // Test that Carbon methods are delegated
        $this->assertEquals(2024, $arabicCarbon->year);
        $this->assertEquals(1, $arabicCarbon->month);
        $this->assertEquals(15, $arabicCarbon->day);

        // Test that Carbon methods work
        $nextDay = $arabicCarbon->addDay();
        $this->assertEquals(16, $nextDay->day);

        // Test property access
        $this->assertTrue(isset($arabicCarbon->year));
    }
}
