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
        'image', 
        'type', 
        'category',
    ];

    public function requests() {
        return $this->hasMany(StoreRequest::class);
    }
}
