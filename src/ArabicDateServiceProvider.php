<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate;

use AhmadChebbo\LaravelArabicDate\Services\ArabicDateService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ArabicDateServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('arabic-date', function ($app) {
            return new ArabicDateService();
        });

        $this->app->alias('arabic-date', ArabicDateService::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration file if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/arabic-date.php' => config_path('arabic-date.php'),
            ], 'arabic-date-config');
        }

        // Load configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/arabic-date.php', 'arabic-date'
        );

        $this->registerBladeDirectives();
    }

    /**
     * Register Blade directives for Arabic date formatting.
     */
    protected function registerBladeDirectives(): void
    {
        // @arabicDate($date) or @arabicDate($date, 'd F Y')
        Blade::directive('arabicDate', function (string $expression) {
            return "<?php echo app(\AhmadChebbo\LaravelArabicDate\Services\ArabicDateService::class)->formatDateCustom({$expression}); ?>";
        });

        // @hijriDate($date)
        Blade::directive('hijriDate', function (string $expression) {
            return "<?php echo app(\AhmadChebbo\LaravelArabicDate\Services\ArabicDateService::class)->formatHijri({$expression}); ?>";
        });
    }
}
