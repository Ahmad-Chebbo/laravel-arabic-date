# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
