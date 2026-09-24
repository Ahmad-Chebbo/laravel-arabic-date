# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.1] - 2026-09-24

### Fixed

- `composer update` in a consuming app failed to resolve because `illuminate/database`'s version constraint didn't include `^13.0`, even though Laravel 13 is out and other common dependencies (e.g. `pestphp/pest-plugin-laravel ^5.0`) already require it. Widened `illuminate/contracts`, `illuminate/database`, and `illuminate/support` to `^9.0|^10.0|^11.0|^12.0|^13.0`.
- Restored `illuminate/support` and `illuminate/contracts` as explicit `require` entries — the package directly uses classes from both (`ServiceProvider`, `Facade`, `Blade`, `CastsAttributes`), so depending on them only transitively through `illuminate/database` was fragile.
- `phpunit.xml`'s `<coverage><report>` block (a structure removed since PHPUnit 10) silently made the entire suite report "No tests executed!" under PHPUnit 12 — removed it; `<source>` alone still supports CLI coverage flags and works across PHPUnit 9–12.
- Bumped `require-dev` (`orchestra/testbench`, `phpunit/phpunit`) to allow versions compatible with Laravel 13 / PHPUnit 12.

## [1.2.0] - 2026-09-24

### Added

- `convertDatesToArabic()` on `HasArabicDates` — returns all `$arabicDate` fields as formatted strings without mutating the model, for building API responses
- `Casts\ArabicDate` — an Eloquent attribute cast alternative to the `HasArabicDates` trait
- Hijri (Islamic) calendar conversion: `ArabicDateService::toHijri()`, `formatHijri()`, `getHijriMonth()`, `ArabicCarbon::toHijri()`, `HasArabicDates::getHijriDate()`
- Blade directives `@arabicDate($date)` and `@hijriDate($date)`
- Abbreviated month/day name conversion (Carbon's `M`/`D` format characters), not just full names (`F`/`l`)
- Optional `?string $locale` parameter on `isArabicConversionEnabled()`, `getArabicCarbon()`, `getFormattedDate()`, and `convertDatesToArabic()` to check/format against a locale other than the current app locale
- `illuminate/*` and `nesbot/carbon` declared as real `require` dependencies, and `orchestra/testbench`/`phpunit/phpunit` as `require-dev` (previously undeclared, despite being used throughout) — `composer test` now works out of the box
- GitHub Actions CI workflow running the test suite across PHP 8.1–8.4 and Laravel 9–12

### Fixed

- `HasArabicDates::convertDatesToArabic()` — the test suite called this method, but it didn't exist anywhere in the package (fatal error on use)
- Model attributes listed in `$arabicDate` but not declared in `$casts` (e.g. a custom `published_at` field) were silently never converted, because the magic accessor only converts actual `Carbon` values; the trait now auto-merges a `datetime` cast for them
- `getArabicCarbon()` returned `null` whenever the locale wasn't Arabic, which crashed any code (including the README's own examples) that chained a method off it; it now only returns `null` when the field itself has no value
- `ArabicCarbon::toArabic()` used `default_format` (numeric-only, e.g. `Y-m-d H:i:s`) instead of a format with a month name, so it could never actually contain a month name as its sibling methods (`toArabicWithDay()`, `toArabicWithTime()`) do
- Implicit nullable parameters (`string $format = null`) replaced with explicit `?string $format = null` (deprecated since PHP 8.4, a hard error in future PHP versions)
- `phpunit.xml` referenced a non-existent `tests/Unit` suite, which made the entire suite fail to run on PHPUnit 11
- `auto_convert_on_retrieval` config option was documented but never read anywhere in the code; it now actually controls the auto-casting behavior above
- README linked to `LICENSE.md`, but the file is `LICENSE`
- Removed a duplicated "Configuration Options" section in the README

### Changed

- `formatDateCustom()` no longer special-cases the `'d F Y'` format with duplicate logic — it produced identical output to the generic path, just less obviously
- Replaced a per-call loop of 12 `Carbon::copy()->setMonth()` instantiations with a static month-name lookup, for the same output at lower cost
- `composer.json` description no longer claims Islamic calendar support without providing it — Hijri conversion is now implemented

### Removed

- Dead code: `ArabicCarbon::boot()` (unused, fully commented out) and `HasArabicDates::bootHasArabicDates()` (empty no-op), plus stray commented-out debug code

## [1.1.0] - 2026-01-11

### Added

- AM/PM conversion support for Arabic date formatting

## [1.0.0] - 2025-07-07

### Added
- Initial release of Laravel Arabic Date package
- `HasArabicDates` trait for automatic Arabic date conversion in models
- `ArabicDateService` for manual Arabic date formatting
- `ArabicCarbon` wrapper class for Carbon-like functionality with Arabic support
- `ArabicDate` facade for easy access to service methods
- Configuration file with customizable settings
- Support for Arabic numerals (٠١٢٣٤٥٦٧٨٩)
- Support for Arabic month names (يناير, فبراير, etc.)
- Support for Arabic day names (الأحد, الاثنين, etc.)
- Multiple date formatting methods:
  - `formatDate()` - Basic Arabic formatting
  - `formatDateCustom()` - Custom format with Arabic conversion
  - `formatDateWithDay()` - Date with Arabic day name
  - `formatDateTime()` - Date and time in Arabic
- Model methods:
  - `getFormattedDate()` - Get formatted date using config default
  - `getArabicDate()` - Get Arabic formatted date
  - `getArabicCarbon()` - Get ArabicCarbon instance
  - `getOriginalDate()` - Get original Carbon instance
  - `isArabicConversionEnabled()` - Check if Arabic conversion is active
- Configuration options:
  - `default_format` - Default date format
  - `custom_format` - Custom date format
  - `enable_arabic_numerals` - Enable/disable Arabic numerals
  - `enable_arabic_months` - Enable/disable Arabic month names
  - `enable_arabic_days` - Enable/disable Arabic day names
  - `supported_languages` - Array of supported language codes
  - `auto_convert_on_retrieval` - Auto-convert on model retrieval
- Comprehensive test suite
- Full documentation with examples
- Laravel auto-discovery support

