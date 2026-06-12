<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerSubscriptionFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 
        'name', 
        'price', 
        'renewal_date', 
        'status',
    ];

    protected $casts = [
        'renewal_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
