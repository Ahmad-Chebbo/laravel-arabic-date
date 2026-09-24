<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Attributes;

use Attribute;

/**
 * Declare a model's Arabic-convertible date fields via a class attribute,
 * as an alternative to the `$arabicDate` property — mirrors Laravel's own
 * #[Fillable] / #[Hidden] attributes.
 *
 *   use AhmadChebbo\LaravelArabicDate\Attributes\ArabicDate;
 *
 *   #[ArabicDate(['created_at', 'published_at'])]
 *   class Post extends Model
 *   {
 *       use HasArabicDates;
 *   }
 *
 * Requires Laravel's attribute-based model configuration support
 * (Illuminate\Database\Eloquent\Model::resolveClassAttribute()). On older
 * Laravel versions that don't have it, this attribute is ignored — use the
 * `$arabicDate` property instead.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ArabicDate
{
    /**
     * @var array<int, string>
     */
    public array $fields;

    /**
     * @param  array<int, string>|string  ...$fields
     */
    public function __construct(array|string ...$fields)
    {
        $this->fields = is_array($fields[0] ?? null) ? $fields[0] : $fields;
    }
}
