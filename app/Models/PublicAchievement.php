<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicAchievement extends Model
{
    /** @use HasFactory<\Database\Factories\PublicAchievementFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ar_title',
        'fr_title',
        'en_title',

        'ar_type',
        'fr_type',
        'en_type',

        'ar_short_description',
        'fr_short_description',
        'en_short_description',

        'ar_description',
        'fr_description',
        'en_description',

        'image',
        'project_url',
        'is_published',
    ];
}
