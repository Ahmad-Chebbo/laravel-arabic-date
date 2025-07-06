<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Examples;

use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Database\Eloquent\Model;

class ExamplePost extends Model
{
    use HasArabicDates;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content',
        'published_at',
        'expires_at',
    ];

    /**
     * Fields that should be converted to Arabic format when locale is Arabic.
     */
    protected $arabicDate = [
        'created_at',
        'updated_at',
        'published_at',
        'expires_at',
    ];

    /**
     * Get the formatted title with creation date.
     */
    public function getFormattedTitleAttribute(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return "{$this->title} - {$this->created_at}";
        }

        return "{$this->title} - {$this->getOriginalDate('created_at')->format('d F Y')}";
    }

    /**
     * Get the publication status with date.
     */
    public function getPublicationStatusAttribute(): string
    {
        if (!$this->published_at) {
            return app()->getLocale() === 'ar' ? 'غير منشور' : 'Not Published';
        }

        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return "منشور في {$this->published_at}";
        }

        return "Published on {$this->getOriginalDate('published_at')->format('d F Y')}";
    }

    /**
     * Scope to get published posts.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to get expired posts.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
    }
}
