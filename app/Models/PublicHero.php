<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicHero extends Model
{
    /** @use HasFactory<\Database\Factories\PublicHeroFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'ar_label', 
        'fr_label', 
        'en_label', 
        'ar_title', 
        'fr_title', 
        'en_title', 
        'ar_description', 
        'fr_description', 
        'en_description', 
        'image', 
        'facebook_url', 
        'instagram_url', 
        'whatsapp_url', 
        'linked_in_url', 
    ];
}
