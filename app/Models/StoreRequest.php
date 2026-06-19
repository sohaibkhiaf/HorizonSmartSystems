<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRequest extends Model
{
    /** @use HasFactory<\Database\Factories\StoreRequestFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'store_subscription_id',

        'customer_full_name',
        'customer_contact',
        'customer_email',
        'customer_address',

        'quantity', 
        'unit_price',

        'status',
        'admin_note',
    ];

    public function storeSubscription() {
        return $this->belongsTo(StoreSubscription::class);
    }
    
}
