<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'section',
        'title',
        'content',
        'image',
        'data',
        'is_active',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getOrCreate($page, $section, $defaults = [])
    {
        return self::firstOrCreate(
            ['page' => $page, 'section' => $section],
            array_merge([
                'title' => ucwords(str_replace('_', ' ', $section)),
                'content' => '',
                'is_active' => true,
            ], $defaults)
        );
    }
}