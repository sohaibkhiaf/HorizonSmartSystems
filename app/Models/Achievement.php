<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Achievement extends Model
{
    /** @use HasFactory<\Database\Factories\AchievementFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'short_description',
        'description',
        'image',
        'project_url',
        'is_published',
    ];

}
