<?php

declare(strict_types=1);

namespace AhmadChebbo\LaravelArabicDate\Tests\Feature;

use AhmadChebbo\LaravelArabicDate\Traits\HasArabicDates;
use Illuminate\Database\Eloquent\Model;

class ExampleModel extends Model
{
    use HasArabicDates;

    protected $table = 'example_models';

    protected $fillable = ['title', 'content', 'published_at'];

    protected $arabicDate = ['created_at', 'updated_at', 'published_at'];
}
