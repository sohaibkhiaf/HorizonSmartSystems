<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\StoreSubscriptionFactory> */
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'discount',
        'image', 
        'type', 
        'priority',
        'is_published', 
        'category', 
        'ar_slug',
        'fr_slug',
        'en_slug',
    ];

    public function requests() {
        return $this->hasMany(StoreRequest::class);
    }
}
