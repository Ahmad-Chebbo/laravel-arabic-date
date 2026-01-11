# Laravel Arabic Date

A Laravel package that automatically converts date fields to Arabic format when the application language is set to Arabic.

## Features

- 🗓️ Automatic Arabic date conversion for model attributes
- 🔢 Arabic numerals support (٠١٢٣٤٥٦٧٨٩)
- 📅 Arabic month and day names
- 🎯 Easy-to-use trait for models
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
- **`auto_convert_on_retrieval`**: Enable automatic conversion when models are retrieved (default: `true`)

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

### Configuration Options

- `default_format`: The default format for date conversion
- `custom_format`: Format for custom date formatting
- `enable_arabic_numerals`: Enable/disable Arabic numerals conversion
- `enable_arabic_months`: Enable/disable Arabic month names
- `enable_arabic_days`: Enable/disable Arabic day names
- `supported_languages`: Array of language codes that trigger Arabic conversion
- `auto_convert_on_retrieval`: Enable automatic conversion when models are retrieved

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

### `getArabicCarbon(string $field)`

Get an ArabicCarbon instance for a specific field:

```php
$post = Post::first();
$arabicCarbon = $post->getArabicCarbon('created_at'); // Returns ArabicCarbon instance
echo $arabicCarbon->toArabicWithDay(); // الاثنين ١٥ يناير ٢٠٢٤
```

### `isArabicConversionEnabled()`

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


## Roadmap & Planned Features

We are committed to continuously improving **Laravel Arabic Date**. Here’s our current roadmap and planned enhancements:

### Roadmap

- **v1.x**
    - [x] Automatic Arabic date conversion for Eloquent model attributes
    - [x] Arabic numerals and month/day names support
    - [x] Configurable date formats
    - [x] Facade and helper methods
    - [x] Full compatibility with Laravel 9–12

- **v1.1+ (Planned)**
    - [x] **AM/PM Conversion:** Convert `AM`/`PM` to Arabic equivalents (`ص` for صباحًا, `م` for مساءً) in formatted dates and times
    - [x] Customizable translation for time periods (AM/PM)
    - [ ] Improved support for API Resource responses
    - [ ] Blade directive for Arabic date formatting Eg. `@arabicDate()`
    - [ ] Support for additional calendar systems (e.g., Hijri)
    - [ ] Enhanced localization and multi-language support

### Upcoming: Improved API Resource Support


When using API resources (such as Laravel's `JsonResource`), you can automatically ensure all date and time fields are formatted in Arabic, including correct AM/PM conversion. This provides seamless API support for Arabic date formatting.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 
