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

    public function test_convert_dates_to_arabic_returns_arabic_strings_for_arabic_locale(): void
    {
        // Set locale to Arabic
        app()->setLocale('ar');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);
        $model->updated_at = Carbon::create(2024, 1, 16, 10, 15, 0);

        // Get the Arabic-formatted values without mutating the model
        $converted = $model->convertDatesToArabic();

        $this->assertStringContainsString('٢٠٢٤', $converted['created_at']);
        $this->assertStringContainsString('يناير', $converted['created_at']);

        // The underlying attribute must remain untouched (still a Carbon instance)
        $this->assertInstanceOf(Carbon::class, $model->getOriginalDate('created_at'));
    }

    public function test_convert_dates_to_arabic_returns_readable_strings_when_locale_is_not_arabic(): void
    {
        // Set locale to English
        app()->setLocale('en');

        // Create a model instance
        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        $converted = $model->convertDatesToArabic();

        // Not converted to Arabic — should remain readable English
        $this->assertStringContainsString('2024', $converted['created_at']);
        $this->assertStringContainsString('Jan', $converted['created_at']);
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

    public function test_non_cast_field_is_auto_cast_and_converted(): void
    {
        app()->setLocale('ar');

        // published_at is listed in $arabicDate but is NOT declared in
        // ExampleModel's $casts — initializeHasArabicDates() must merge
        // a datetime cast for it automatically.
        $model = new ExampleModel();
        $model->published_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        $this->assertInstanceOf(\AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::class, $model->published_at);
        $this->assertStringContainsString('يناير', $model->published_at->format('d F Y'));
    }

    public function test_get_arabic_carbon_is_not_null_for_non_arabic_locale(): void
    {
        app()->setLocale('en');

        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        $arabicCarbon = $model->getArabicCarbon('created_at');

        $this->assertNotNull($arabicCarbon);
        $this->assertStringContainsString('2024-01-15', $arabicCarbon->format('Y-m-d'));
    }

    public function test_abbreviated_month_and_day_names_are_converted(): void
    {
        $service = app(ArabicDateService::class);
        $date = Carbon::create(2024, 1, 15, 14, 30, 0); // Monday

        $formatted = $service->formatDate($date, 'D, d M Y');

        $this->assertStringContainsString('الاثنين', $formatted);
        $this->assertStringContainsString('يناير', $formatted);
    }

    public function test_arabic_carbon_to_arabic_includes_month_name(): void
    {
        app()->setLocale('ar');

        $model = new ExampleModel();
        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);
        $arabicCarbon = $model->getArabicCarbon('created_at');

        $this->assertStringContainsString('يناير', $arabicCarbon->toArabic());
    }

    public function test_hijri_conversion_matches_known_reference_dates(): void
    {
        $service = app(ArabicDateService::class);

        // Verified against widely reported reference dates.
        $this->assertEquals(
            ['year' => 1420, 'month' => 9, 'day' => 24],
            $service->toHijri(Carbon::create(2000, 1, 1))
        );

        $this->assertEquals(
            ['year' => 1441, 'month' => 1, 'day' => 1],
            $service->toHijri(Carbon::create(2019, 9, 1))
        );
    }

    public function test_format_hijri(): void
    {
        $service = app(ArabicDateService::class);
        $date = Carbon::create(2024, 1, 15);

        $formatted = $service->formatHijri($date);

        $this->assertStringContainsString('رجب', $formatted);
        $this->assertStringContainsString('١٤٤٥', $formatted);
        $this->assertStringContainsString('هـ', $formatted);
    }

    public function test_arabic_carbon_to_hijri(): void
    {
        $arabicCarbon = \AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::parse('2024-01-15');

        $this->assertStringContainsString('رجب', $arabicCarbon->toHijri());
    }

    public function test_arabic_date_and_hijri_date_blade_directives_are_registered(): void
    {
        $directives = \Illuminate\Support\Facades\Blade::getCustomDirectives();

        $this->assertArrayHasKey('arabicDate', $directives);
        $this->assertArrayHasKey('hijriDate', $directives);
    }

    public function test_custom_arabic_date_cast(): void
    {
        app()->setLocale('ar');

        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $table = 'example_models';
            protected $fillable = ['title', 'published_at'];
            protected $casts = [
                'published_at' => \AhmadChebbo\LaravelArabicDate\Casts\ArabicDate::class,
            ];
        };

        $model->published_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        $this->assertInstanceOf(\AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::class, $model->published_at);
        $this->assertStringContainsString('يناير', $model->published_at->format('d F Y'));
    }

    public function test_arabic_date_attribute_works_without_the_property(): void
    {
        app()->setLocale('ar');

        $model = new #[\AhmadChebbo\LaravelArabicDate\Attributes\ArabicDate(['published_at'])] class extends \Illuminate\Database\Eloquent\Model {
            use \AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;

            protected $table = 'example_models';
            protected $fillable = ['title', 'published_at'];
        };

        $model->published_at = Carbon::create(2024, 1, 15, 14, 30, 0);

        $this->assertInstanceOf(\AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::class, $model->published_at);
        $this->assertStringContainsString('يناير', $model->published_at->format('d F Y'));
    }

    public function test_arabic_date_attribute_merges_with_the_property(): void
    {
        app()->setLocale('ar');

        $model = new #[\AhmadChebbo\LaravelArabicDate\Attributes\ArabicDate(['published_at'])] class extends \Illuminate\Database\Eloquent\Model {
            use \AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;

            protected $table = 'example_models';
            protected $fillable = ['title', 'created_at', 'published_at'];
            protected $arabicDate = ['created_at'];
        };

        $model->created_at = Carbon::create(2024, 1, 15, 14, 30, 0);
        $model->published_at = Carbon::create(2024, 2, 20, 9, 0, 0);

        // Both the property-declared field and the attribute-declared field convert
        $this->assertInstanceOf(\AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::class, $model->created_at);
        $this->assertInstanceOf(\AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::class, $model->published_at);
    }

    public function test_arabic_carbon_is_json_serializable(): void
    {
        app()->setLocale('ar');

        $arabicCarbon = \AhmadChebbo\LaravelArabicDate\Objects\ArabicCarbon::parse('2024-01-15 14:30:00');

        // Without JsonSerializable, json_encode() on a plain object only
        // serializes its public properties, and this class has none —
        // producing an empty "{}" instead of the formatted date.
        $json = json_encode($arabicCarbon);

        $this->assertNotSame('{}', $json);
        $this->assertStringContainsString('٢٠٢٤', json_decode($json));

        $response = response()->json(['created_at' => $arabicCarbon]);
        $this->assertStringContainsString('٢٠٢٤', json_decode($response->getContent(), true)['created_at']);
    }
}
