# Laravel Arabic Date

A Laravel package that automatically converts date fields to Arabic format when the application language is set to Arabic, with civil Hijri (Islamic) calendar conversion.

## Features

- 🗓️ Automatic Arabic date conversion for model attributes (works for any date field, not just `created_at`/`updated_at` — no manual `$casts` needed)
- 🔢 Arabic numerals support (٠١٢٣٤٥٦٧٨٩), including abbreviated month/day formats (`M`, `D`)
- 📅 Arabic month and day names
- 🌙 Hijri (Islamic) calendar conversion
- 🎯 Easy-to-use trait for models, or an Eloquent cast if you prefer
- 🖋️ Blade directives: `@arabicDate($date)` and `@hijriDate($date)`
- ⚙️ Configurable settings
- 🎨 Facade for direct usage

## Installation

1. Install the package via Composer:

```bash
composer require ahmad-chebbo/laravel-arabic-date
```

2. The service provider will be automatically registered. If you're using Laravel 5.4 or lower, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    AhmadChebbo\LaravelArabicDate\ArabicDateServiceProvider::class,
],
```

3. (Optional) Publish the configuration file:

```bash
php artisan vendor:publish --tag=arabic-date-config
```

## Usage

### Basic Model Usage

Add the `HasArabicDates` trait to your model and define which fields should be converted to Arabic format:

```php
<?php

namespace App\Models;

use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasArabicDates;

    protected $arabicDate = ['created_at', 'updated_at', 'published_at'];
}
```

Now, when your application language is set to Arabic (`app()->setLocale('ar')`), the specified date fields will automatically be displayed in Arabic format.

> Fields listed in `$arabicDate` don't need to be declared in `$casts` — the trait automatically casts them to `datetime` for you (see [`auto_convert_on_retrieval`](#configuration-options)). If you'd rather cast a field explicitly, or prefer not to use the trait at all, see [Alternative: Eloquent Cast](#alternative-eloquent-cast) below.

### Alternative: `#[ArabicDate]` Attribute

If you're on a Laravel version with [attribute-based model configuration](https://laravel.com/docs/eloquent#defining-models-using-attributes) (the same mechanism behind `#[Fillable]`/`#[Hidden]`), you can declare the fields as a class attribute instead of — or alongside — the `$arabicDate` property:

```php
use AhmadChebbo\LaravelArabicDate\Attributes\ArabicDate;
use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
#[ArabicDate(['created_at'])]
class User extends Authenticatable
{
    use HasArabicDates;
}
```

If both are present, the property and the attribute are merged. On Laravel versions that don't support attribute-based configuration, `#[ArabicDate]` is simply ignored — use the `$arabicDate` property there instead.

### Alternative: Eloquent Cast

If you'd rather not add the trait to your model, cast a single field directly instead:

```php
use AhmadChebbo\LaravelArabicDate\Casts\ArabicDate;

class Post extends Model
{
    protected $casts = [
        'published_at' => ArabicDate::class,
    ];
}
```

This behaves the same way as the trait for that field: it returns an `ArabicCarbon` instance that renders in Arabic when the locale is supported, and falls back to normal Carbon formatting otherwise.

### Manual Usage with Facade

You can also use the facade directly for manual date conversion:

```php
use AhmadChebbo\LaravelArabicDate\Facades\ArabicDate;
use Carbon\Carbon;

// Basic formatting
$date = Carbon::now();
$arabicDate = ArabicDate::formatDate($date); // ٢٠٢٤-٠١-١٥ ١٤:٣٠:٠٠

// Custom formatting
$customDate = ArabicDate::formatDateCustom($date); // ١٥ يناير ٢٠٢٤

// With day name
$dateWithDay = ArabicDate::formatDateWithDay($date); // الاثنين ١٥ يناير ٢٠٢٤

// With time
$dateTime = ArabicDate::formatDateTime($date); // ١٥ يناير ٢٠٢٤ ١٤:٣٠:٠٠

// Hijri (Islamic calendar) formatting
$hijriDate = ArabicDate::formatHijri($date); // ٤ رجب ١٤٤٥هـ
$hijri = ArabicDate::toHijri($date); // ['year' => 1445, 'month' => 7, 'day' => 4]
```

> Hijri conversion uses the civil (tabular) Islamic calendar — a fixed arithmetic approximation. It may differ by a day from observation-based calendars (e.g. Umm al-Qura) near month boundaries.

### Blade Directives

```blade
<p>{{-- Uses config('arabic-date.custom_format') --}}</p>
<p>@arabicDate($post->created_at)</p>
<p>@arabicDate($post->created_at, 'l, d F Y')</p>
<p>@hijriDate($post->created_at)</p>
```

### Service Injection

You can also inject the service directly:

```php
use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;

class MyController extends Controller
{
    public function index(ArabicDateService $arabicDateService)
    {
        $date = Carbon::now();
        $arabicDate = $arabicDateService->formatDateCustom($date);
        
        return view('welcome', compact('arabicDate'));
    }
}
```

## Configuration

The package comes with a configuration file that you can customize:

```php
// config/arabic-date.php

return [
    'default_format' => 'Y-m-d H:i:s',
    'custom_format' => 'd F Y',
    'enable_arabic_numerals' => true,
    'enable_arabic_months' => true,
    'enable_arabic_days' => true,
    'supported_languages' => ['ar'],
    'auto_convert_on_retrieval' => true,
];
```

### Configuration Options

- **`default_format`**: The default format for date conversion (default: `'Y-m-d H:i:s'`)
- **`custom_format`**: Format for custom date formatting (default: `'d F Y'`)
- **`enable_arabic_numerals`**: Enable/disable Arabic numerals conversion (default: `true`)
- **`enable_arabic_months`**: Enable/disable Arabic month names (default: `true`)
- **`enable_arabic_days`**: Enable/disable Arabic day names (default: `true`)
- **`supported_languages`**: Array of language codes that trigger Arabic conversion (default: `['ar']`)
- **`auto_convert_on_retrieval`**: Whether `$arabicDate` fields are auto-cast to `datetime` so they don't need `$casts` declared manually (default: `true`)

### Configuration Examples

```php
// Disable Arabic numerals but keep Arabic month/day names
'enable_arabic_numerals' => false,
'enable_arabic_months' => true,
'enable_arabic_days' => true,

// Support multiple Arabic locales
'supported_languages' => ['ar', 'ar-SA', 'ar-EG'],

// Use a different default format
'default_format' => 'd/m/Y H:i',
```

## Model Methods

When using the `HasArabicDates` trait, your model will have access to these additional methods:

### `getOriginalDate(string $field)`

Get the original date value before Arabic conversion:

```php
$post = Post::first();
$originalDate = $post->getOriginalDate('created_at'); // Returns Carbon instance
```

### `getArabicDate(string $field)`

Get the Arabic formatted date for a specific field:

```php
$post = Post::first();
$arabicDate = $post->getArabicDate('created_at'); // Returns Arabic formatted string
```

### `getArabicCarbon(string $field, ?string $locale = null)`

Get an ArabicCarbon instance for a specific field. Returns `null` only when the field itself has no value — it does **not** return `null` just because the locale isn't Arabic, so it's always safe to chain:

```php
$post = Post::first();
$arabicCarbon = $post->getArabicCarbon('created_at'); // Returns ArabicCarbon instance (or null if the field is empty)
echo $arabicCarbon?->toArabicWithDay(); // الاثنين ١٥ يناير ٢٠٢٤ — forces Arabic regardless of locale
echo $arabicCarbon?->format('Y-m-d'); // Arabic when locale is Arabic, plain Carbon format otherwise
echo $arabicCarbon?->toHijri(); // ٤ رجب ١٤٤٥هـ
```

### `getHijriDate(string $field)`

Get the Hijri (Islamic calendar) formatted date for a specific field:

```php
$post = Post::first();
$hijriDate = $post->getHijriDate('created_at'); // ٤ رجب ١٤٤٥هـ
```

### `convertDatesToArabic(?string $locale = null)`

Returns all `$arabicDate` fields as an array of formatted strings, without mutating the model — handy for building an API response:

```php
$post = Post::first();
$post->convertDatesToArabic();
// ['created_at' => '١٥ يناير ٢٠٢٤', 'updated_at' => '١٦ يناير ٢٠٢٤']
```

### `isArabicConversionEnabled(?string $locale = null)`

Check if Arabic conversion is enabled for the current locale:

```php
$post = Post::first();
if ($post->isArabicConversionEnabled()) {
    // Arabic conversion is active
}
```

### `getFormattedDate(string $field)`

Get the formatted date string using the default format from config:

```php
$post = Post::first();
$formattedDate = $post->getFormattedDate('created_at'); // Uses config('arabic-date.default_format')
```

## Examples

### Example 1: Blog Post Model

```php
<?php

namespace App\Models;

use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasArabicDates;

    protected $arabicDate = ['created_at', 'updated_at', 'published_at', 'expires_at'];

    protected $fillable = [
        'title',
        'content',
        'published_at',
        'expires_at',
    ];
}
```

### Example 2: User Model

```php
<?php

namespace App\Models;

use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasArabicDates;

    protected $arabicDate = ['created_at', 'updated_at', 'last_login_at', 'email_verified_at'];

    // ... rest of your model
}
```

### Example 3: Blade Template Usage

```php
{{-- In your Blade template --}}
@if(app()->getLocale() === 'ar')
    <p>تاريخ الإنشاء: {{ $post->created_at }}</p>
    <p>تاريخ التحديث: {{ $post->updated_at }}</p>
@else
    <p>Created: {{ $post->getOriginalDate('created_at')->format('Y-m-d H:i:s') }}</p>
    <p>Updated: {{ $post->getOriginalDate('updated_at')->format('Y-m-d H:i:s') }}</p>
@endif
```

### Example 4: Format Arabic Dates

```php
// The created_at field now returns an ArabicCarbon instance when locale is Arabic
$post = Post::first();

// Format with custom format (works in both Arabic and English)
echo $post->created_at->format('Y-m-d'); // ٢٠٢٤-٠١-١٥ (Arabic) or 2024-01-15 (English)

// All Carbon methods work normally
echo $post->created_at->diffForHumans(); // Works with Arabic
echo $post->created_at->addDays(5); // Works normally
echo $post->created_at->year; // ٢٠٢٤ (Arabic) or 2024 (English)

// Get formatted string with default format from config
echo $post->getFormattedDate('created_at'); // Uses config('arabic-date.default_format')

// Get Arabic-specific formatting
$arabicCarbon = $post->getArabicCarbon('created_at');
echo $arabicCarbon->toArabicWithDay(); // الاثنين ١٥ يناير ٢٠٢٤
echo $arabicCarbon->toArabicWithTime(); // ١٥ يناير ٢٠٢٤ ١٤:٣٠:٠٠

// Get original Carbon instance
$originalCarbon = $post->getOriginalDate('created_at');
echo $originalCarbon->format('Y-m-d'); // Always in English: 2024-01-15
```

### Example 5: JSON Response with Arabic Dates

```php
Route::get('/posts', function () {
    App::setLocale('ar');
    $posts = Post::all();
    
    return $posts->map(function ($post) {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'created_at' => $post->getFormattedDate('created_at'), // Uses default format
            'updated_at' => $post->getFormattedDate('updated_at'), // Uses default format
            // Or use custom format:
            'published_at' => $post->created_at->format('Y-m-d'),
        ];
    });
});
```

Or convert every configured field at once with `convertDatesToArabic()`:

```php
Route::get('/posts', function () {
    App::setLocale('ar');

    return Post::all()->map(fn ($post) => array_merge(
        ['id' => $post->id, 'title' => $post->title],
        $post->convertDatesToArabic()
    ));
});
```


## Roadmap & Planned Features

We are committed to continuously improving **Laravel Arabic Date**. Here’s our current roadmap and planned enhancements:

### Roadmap

- **v1.x**
    - [x] Automatic Arabic date conversion for Eloquent model attributes
    - [x] Arabic numerals and month/day names support
    - [x] Configurable date formats
    - [x] Facade and helper methods
    - [x] Full compatibility with Laravel 9–12

- **v1.1**
    - [x] **AM/PM Conversion:** Convert `AM`/`PM` to Arabic equivalents (`ص` for صباحًا, `م` for مساءً) in formatted dates and times
    - [x] Customizable translation for time periods (AM/PM)

- **v1.2**
    - [x] `convertDatesToArabic()` for building API responses without mutating the model
    - [x] Automatic field conversion for any date field (no `$casts` boilerplate required)
    - [x] `Casts\ArabicDate` — an Eloquent cast alternative to the trait
    - [x] Blade directives: `@arabicDate()` and `@hijriDate()`
    - [x] Hijri (Islamic) calendar conversion — `toHijri()`, `formatHijri()`, `getHijriDate()`
    - [x] Abbreviated month/day name conversion (`M`, `D` format characters)
    - [ ] Enhanced localization and multi-language support (e.g. distinct numeral systems per Arabic locale variant)
    - [ ] `Illuminate\Contracts\Support\Arrayable`/JSON Resource integration that doesn't require calling `convertDatesToArabic()` manually

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
