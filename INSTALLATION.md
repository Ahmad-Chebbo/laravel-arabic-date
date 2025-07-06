# Installation Guide

## Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- Composer

## Quick Installation

### 1. Install via Composer

```bash
composer require ahmad-chebbo/laravel-arabic-date
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=arabic-date-config
```

This will publish the configuration file to `config/arabic-date.php`.

### 3. Configure Your Application

The package will work out of the box with default settings. If you want to customize the behavior, edit the published configuration file:

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

## Usage

### Basic Model Usage

Add the trait to your model:

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

### Set Arabic Locale

```php
// In your controller or middleware
App::setLocale('ar');
```

### Access Arabic Dates

```php
$post = Post::first();

// Automatic Arabic formatting when locale is 'ar'
echo $post->created_at; // ٢٠٢٤-٠١-١٥ ١٤:٣٠:٠٠

// Custom format
echo $post->created_at->format('Y-m-d'); // ٢٠٢٤-٠١-١٥

// Get formatted string
echo $post->getFormattedDate('created_at'); // Uses default format
```

## Testing

Run the test suite to ensure everything is working:

```bash
composer test
```

## Troubleshooting

### Common Issues

1. **Dates not converting to Arabic**
   - Ensure your application locale is set to 'ar': `App::setLocale('ar')`
   - Check that the field is included in the `$arabicDate` array

2. **Configuration not loading**
   - Publish the configuration file: `php artisan vendor:publish --tag=arabic-date-config`
   - Clear config cache: `php artisan config:clear`

3. **Service provider not registered**
   - For Laravel 5.4 and below, manually register the service provider in `config/app.php`
   - For Laravel 5.5+, the package uses auto-discovery

### Getting Help

- Check the [README](README.md) for detailed documentation
- Review the [examples](examples/) directory
- Open an issue on GitHub for bugs
- Contact the maintainer for support

## Next Steps

- Read the [README](README.md) for complete documentation
- Check out the [examples](examples/) for usage patterns
- Explore the [configuration options](README.md#configuration)
- Learn about [advanced usage](README.md#advanced-usage) 
